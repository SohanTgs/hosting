<?php

namespace App\DomainRegisters;

use App\Models\Admin;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Resell{

    public $url;
    public $domain; 
    public $request;
    public $resellAcc;

    public function __construct($domain){	
		$this->domain = $domain;
        $register = $domain->register;
        $this->url = $register->test_mode ? 'https://test.httpapi.com' : 'https://httpapi.com';
        $this->resellAcc = $register->params;
	} 

    protected function makeNameservers($request, $domain, $noChange = false){
  
        $nameservers = null;
        $server = $domain->hosting->server;

        if($request){
            $nameservers = $request->ns1.','.$request->ns2;
            
            if($request->ns3){
                $nameservers .= ','.$request->ns3;
            }
    
            if($request->ns4){
                $nameservers .= ','.$request->ns4;
            }

            return $nameservers;
        }

        if($noChange){
            $nameservers = $domain->ns1.','.$domain->ns2;

            if($domain->ns3){
                $nameservers .= ','.$domain->ns3;
            }
    
            if($domain->ns4){
                $nameservers .= ','.$domain->ns4;
            }

            return $nameservers;
        }

        if(@$server){
            $nameservers = $server->ns1.','.$server->ns2;
   
            if($server->ns3){
                $nameservers .= ','.$server->ns3;
            }
    
            if($server->ns4){
                $nameservers .= ','.$server->ns4;
            }

            return $nameservers;
        }
        
        $general = GeneralSetting::first();
        $nameservers = $general->ns1.','.$general->ns2;

        if($general->ns3){
            $nameservers .= ','.$general->ns3;
        }

        if($general->ns4){
            $nameservers .= ','.$general->ns4;
        }

        return $nameservers;
    }

    public function register(){

        $domain = $this->domain;
        $user = $domain->user;
        $request = $this->request;

        $nameservers = $this->makeNameservers($request, $domain);

        $array = explode(',', $nameservers);
        $ns1 = @$array[0];
        $ns2 = @$array[1];
        $ns3 = @$array[2];
        $ns4 = @$array[3];
 
        $nameservers = implode('&ns=', $array);

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $dialCode = $countryData[@$user->country_code]->dial_code;
        $countCode = strlen($dialCode);
        $phoneWithoutCode = substr($user->mobile, $countCode);

        try{   
 
            $getUser = Http::get($this->url.'/api/customers/details.json', [
                'auth-userid'=>$this->resellAcc->auth_user_id->value,
                'api-key'=>$this->resellAcc->api_key->value,
                'username'=>$user->email,
            ]);
           
            if(!@json_decode($getUser)->username){

                $createUser = Http::post($this->url.'/api/customers/v2/signup.xml?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&username='.$user->email.'&passwd=Passw@rd123&name='.$user->fullname.'&company=CompanyName&address-line-1='.@$user->address->address.'&city='.@$user->address->city.'&state='.@$user->address->state.'&country='.@$user->country_code.'&zipcode='.@$user->address->zip.'&phone-cc='.$dialCode.'&phone='.$phoneWithoutCode.'&lang-pref=en');
          
                $domain->customer_id = @xmlToArray(@$createUser)[0]; 
                $domain->save(); 
            }

            $getContact = Http::get($this->url.'/api/contacts/details.json', [
                'auth-userid'=>$this->resellAcc->auth_user_id->value,
                'api-key'=>$this->resellAcc->api_key->value,
                'contact-id'=>$domain->contact_id,
            ]);
      
            if(!@json_decode($getContact)->contactid){

                $createContact = Http::post($this->url.'/api/contacts/add.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&name='.$user->fullname.'&company=CompanyName&email='.$user->email.'&address-line-1='.@$user->address->address.'&address-line-2='.@$user->address->address.'&city='.@$user->address->city.'&country='.@$user->country_code.'&zipcode='.@$user->address->zip.'&phone-cc='.$dialCode.'&phone='.$phoneWithoutCode.'&customer-id='.$domain->customer_id.'&type=Contact');

                if(@json_decode(@$createContact)->status != 'ERROR'){
                    $domain->contact_id = @json_decode($createContact) ?? 0; 
                    $domain->save(); 
                }
            }

            $protection = $domain->id_protection ? 'true' : 'false';
    
            $response = Http::post($this->url.'/api/domains/register.xml?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&domain-name='.$domain->domain.'&years='.$domain->reg_period.'&ns='.$nameservers.'&customer-id='.$domain->customer_id.'&reg-contact-id='.$domain->contact_id.'&admin-contact-id='.$domain->contact_id.'&tech-contact-id='.$domain->contact_id.'&billing-contact-id='.$domain->contact_id.'&invoice-option=KeepInvoice&purchase-privacy='.$protection);

            $response = xmlToArray(@$response);  
            if(@$response['entry'][0]['string'][1] == 'error'){
                return ['success'=>false, 'message'=>@$response['entry'][1]['string'][1]];
            }
         
            $domain->ns1 = @$ns1; 
            $domain->ns2 = @$ns2; 
            $domain->ns3 = @$ns3; 
            $domain->ns4 = @$ns4; 
            $domain->status = 1; 
            $domain->resell_order_id = @$response['hashtable']['string'][7]; 
            $domain->save(); 

            if($domain->id_protection){
                Http::post($this->url.'/api/domains/purchase-privacy.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&invoice-option=NoInvoice');
            }

            return ['success'=>true];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
	}

    public function renew(){

        $domain = $this->domain;
        $request = $this->request;

        try{

            if(!$domain->resell_order_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'OrderDetails'
                ]);

                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }

            $response = Http::post($this->url.'/api/domains/renew.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&years='.$domain->reg_period.'&exp-date=1279012036&invoice-option=NoInvoice');

            $response = json_decode(@$response); 
            if(@$response->status == 'ERROR'){
                return ['success'=>false, 'message'=>@$response->message];
            }

            if($request){
                $domain->reg_period = $request->renew_year;
            }

            $domain->expiry_date = Carbon::parse($domain->expiry_date)->addYear($request ? $request->renew_year : $domain->reg_period);
            $domain->save(); 
    
            return ['success'=>true];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function getContact(){
      
        $domain = $this->domain;

        try{

            if(!$domain->contact_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'ContactIds'
                ]);

                $domain->contact_id = @json_decode($details)->registrantcontactid;
                $domain->save();
            }

            $response = Http::get($this->url.'/api/contacts/details.json', [
                'auth-userid'=>$this->resellAcc->auth_user_id->value,
                'api-key'=>$this->resellAcc->api_key->value,
                'contact-id'=>$domain->contact_id
            ]);

            $response = @json_decode(@$response);
            if(@$response->status == 'ERROR'){
                return ['success'=>false, 'message'=>@$response->message];
            }
            
        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }

        return ['success'=>true, 'response'=>$response];
    }

    public function setContact(){

        $domain = $this->domain;
        $request = $this->request;

        try{   

            $response = Http::post($this->url.'/api/contacts/modify.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&contact-id='.$domain->contact_id.'&name='.$request->name.'&company=CompanyName&email='.$request->email.'&address-line-1='.$request->address1.'&address-line-2='.$request->address2.'&city='.$request->city.'&country='.$request->country.'&zipcode='.$request->zip.'&phone-cc='.$request->telephonecc.'&phone='.$request->telephone);

            $response = json_decode(@$response); 
            if(@$response->status == 'ERROR'){
                return ['success'=>false, 'message'=>@$response->message];
            }

            return ['success'=>true];


        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function changeNameservers(){
     
        $domain = $this->domain;
        $request = $this->request;
        $nameservers = $this->makeNameservers($request, $domain, true);

        $array = explode(',', $nameservers);
        $ns1 = @$array[0];
        $ns2 = @$array[1];
        $ns3 = @$array[2];
        $ns4 = @$array[3];

        $nameservers = implode('&ns=', $array);

        try{
            
            if(!$domain->resell_order_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'OrderDetails'
                ]);

                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }
            
            $response = Http::post($this->url.'/api/domains/modify-ns.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&ns='.$nameservers);

            $response = json_decode(@$response); 
            if(@$response->status == 'ERROR'){
                return ['success'=>false, 'message'=>@$response->message];
            }

            $domain->ns1 = @$ns1; 
            $domain->ns2 = @$ns2; 
            $domain->ns3 = @$ns3; 
            $domain->ns4 = @$ns4; 
            $domain->save(); 
    
            return ['success'=>true];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function enableIdProtection(){

        $domain = $this->domain;

        try{

            if(!$domain->resell_order_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'OrderDetails'
                ]);

                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }

            $response = Http::post($this->url.'/api/domains/modify-privacy-protection.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&protect-privacy=true&reason=PrivacyProtect');

            $response = json_decode(@$response);
            if(@$response->status == 'ERROR'){
                return ['success'=>false, 'message'=>@$response->message];
            }

            $domain->id_protection = 1; 
            $domain->save(); 
    
            return ['success'=>true];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }

    }

    public function disableIdProtection(){
       
        $domain = $this->domain;

        try{

            if(!$domain->resell_order_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'OrderDetails'
                ]);

                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }

            $response = Http::post($this->url.'/api/domains/modify-privacy-protection.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&protect-privacy=false&reason=PrivacyProtect');

            $response = json_decode(@$response);
            if(@$response->status == 'ERROR'){
                return ['success'=>false, 'message'=>@$response->message];
            }

            $domain->id_protection = 0; 
            $domain->save(); 
    
            return ['success'=>true];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }


}



