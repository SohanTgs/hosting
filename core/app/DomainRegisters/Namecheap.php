<?php

namespace App\DomainRegisters;

use App\Models\Admin;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Namecheap{

    public $url;
    public $domain; 
    public $request;
    public $username;
    public $requestIP;
    public $namecheapAcc;

    public function __construct($domain){		
		$this->domain = $domain;
        $this->requestIP  = $_SERVER['REMOTE_ADDR'];
        
        $register = $domain->register;
        $this->url = $register->test_mode ? 'https://api.sandbox.namecheap.com/xml.response' : 'https://api.namecheap.com/xml.response';

        $this->namecheapAcc = $register->params;
        $this->username = $register->test_mode ? $this->namecheapAcc->sandbox_username->value : $this->namecheapAcc->username->value;
	}

    public function register(){

        $domain = $this->domain;
        $request = $this->request;

        $nameservers = null;

        if($request->ns1 && $request->ns2){
            $nameservers = $request->ns1.','.$request->ns2;
            
            if($request->ns3){
                $nameservers .= ','.$request->ns3;
            }
    
            if($request->ns4){
                $nameservers .= ','.$request->ns4;
            }

        }else{
            $general = GeneralSetting::first();
            $nameservers = $general->ns1.','.$general->ns2;
        }

        $array = explode(',', $nameservers);
        $ns1 = @$array[0];
        $ns2 = @$array[1];
        $ns3 = @$array[2];
        $ns4 = @$array[3];

        $user = $domain->user;
        $admin = Admin::first();

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $dialCode = @$countryData[@$user->country_code]->dial_code;
        $countCode = strlen($dialCode);
 
        $withoutCode = substr($user->mobile, $countCode);
        $phone = '+'.@$dialCode.'.'.$withoutCode;
 
        try{   
 
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.create',
                'ClientIp'=>$this->requestIP,

                'DomainName'=>$domain->domain,
                'Years'=>$domain->reg_period,
                'Nameservers'=>$nameservers,

                'AuxBillingFirstName'=>$user->firstname,
                'AuxBillingLastName'=>$user->lastname,
                'AuxBillingAddress1'=>@$user->address->address ?? 'N/A',
                'AuxBillingStateProvince'=>@$user->address->state ?? 'N/A',
                'AuxBillingPostalCode'=>@$user->address->zip ?? 'N/A',
                'AuxBillingCountry'=>@$user->country_code ?? 'N/A',
                'AuxBillingPhone'=>$phone,
                'AuxBillingEmailAddress'=>$user->email,
                'AuxBillingCity'=>@$user->address->city ?? 'N/A',

                'TechFirstName'=>$user->firstname,
                'TechLastName'=>$user->lastname,
                'TechAddress1'=>@$user->address->address ?? 'N/A',
                'TechStateProvince'=>@$user->address->state ?? 'N/A',
                'TechPostalCode'=>@$user->address->zip ?? 'N/A',
                'TechCountry'=>@$user->country_code ?? 'N/A',
                'TechPhone'=>$phone,
                'TechEmailAddress'=>$user->email,
                'TechCity'=>@$user->address->city ?? 'N/A',

                'AdminFirstName'=>explode(' ', @$admin->name)[0] ?? 'Super',
                'AdminLastName'=>explode(' ', @$admin->name)[1] ?? 'Admin',
                'AdminAddress1'=>@$admin->address->address ?? 'N/A',
                'AdminStateProvince'=>@$admin->address->state ?? 'N/A',
                'AdminPostalCode'=>@$admin->address->zip ?? 'N/A',
                'AdminCountry'=>@$admin->address->country ?? 'N/A',
                'AdminPhone'=>$admin->mobile,
                'AdminEmailAddress'=>$admin->email,
                'AdminCity'=>@$admin->address->city ?? 'N/A', 

                'RegistrantFirstName'=>$user->firstname,
                'RegistrantLastName'=>$user->lastname,
                'RegistrantAddress1'=>@$user->address->address ?? 'N/A',
                'RegistrantStateProvince'=>@$user->address->state ?? 'N/A',
                'RegistrantPostalCode'=>@$user->address->zip ?? 'N/A',
                'RegistrantCountry'=>@$user->address->country ?? 'N/A',
                'RegistrantPhone'=>$phone,
                'RegistrantEmailAddress'=>$user->email,
                'RegistrantCity'=>@$user->country_code ?? 'N/A',
    
                'Whoisguard'=>'yes',
                'AddFreeWhoisguard'=>'yes',

                'WGEnabled'=>$domain->id_protection ? 'yes' : 'no',
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                return ['success'=>false, 'message'=>@$response['Errors']['Error']];
            }

            $getInfo = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'ClientIp'=>$this->requestIP,
                'Command'=>'namecheap.domains.getinfo',
                'DomainName'=>$domain->domain,
            ]);

            $domainId = xmlToArray(@$getInfo)['CommandResponse']['DomainGetInfoResult']['Whoisguard']['ID']; 

            $domain->ns1 = @$ns1; 
            $domain->ns2 = @$ns2; 
            $domain->ns3 = @$ns3; 
            $domain->ns4 = @$ns4; 
            $domain->whois_guard = @$domainId; 
            $domain->status = 1; 
            $domain->save(); 

            return ['success'=>true];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
	}

    public function renew(){

        $domain = $this->domain;
        $request = $this->request;

        try{
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.renew',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,
                'Years'=>$domain->reg_period,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                return ['success'=>false, 'message'=>@$response['Errors']['Error']];
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
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.getContacts',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,
            ]);
     
            if(@$response['Errors']){
                return ['success'=>false, 'message'=>@$response['Errors']['Error']];
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
 
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.setContacts',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,

                'AuxBillingFirstName'=>$request->AuxBillingFirstName,
                'AuxBillingLastName'=>$request->AuxBillingLastName,
                'AuxBillingAddress1'=>@$request->AuxBillingAddress1,
                'AuxBillingStateProvince'=>@$request->AuxBillingStateProvince,
                'AuxBillingPostalCode'=>@$request->AuxBillingPostalCode,
                'AuxBillingCountry'=>$request->AuxBillingCountry,
                'AuxBillingPhone'=>$request->AuxBillingPhone,
                'AuxBillingEmailAddress'=>$request->AuxBillingEmailAddress,
                'AuxBillingCity'=>@$request->AuxBillingCity,

                'TechFirstName'=>$request->TechFirstName,
                'TechLastName'=>$request->TechLastName,
                'TechAddress1'=>@$request->TechAddress1,
                'TechStateProvince'=>@$request->TechStateProvince,
                'TechPostalCode'=>@$request->TechPostalCode,
                'TechCountry'=>$request->TechCountry,
                'TechPhone'=>$request->TechPhone,
                'TechEmailAddress'=>$request->TechEmailAddress,
                'TechCity'=>@$request->TechCity,

                'AdminFirstName'=>$request->AdminFirstName,
                'AdminLastName'=>$request->AdminLastName,
                'AdminAddress1'=>@$request->AdminAddress1,
                'AdminStateProvince'=>@$request->AdminStateProvince,
                'AdminPostalCode'=>@$request->AdminPostalCode,
                'AdminCountry'=>$request->AdminCountry,
                'AdminPhone'=>$request->AdminPhone,
                'AdminEmailAddress'=>$request->AdminEmailAddress,
                'AdminCity'=>@$request->AdminCity, 

                'RegistrantFirstName'=>@$request->RegisterFirstName,
                'RegistrantLastName'=>@$request->RegisterLastName,
                'RegistrantAddress1'=>@$request->RegisterAddress1,
                'RegistrantStateProvince'=>@$request->RegisterStateProvince,
                'RegistrantPostalCode'=>@$request->RegisterPostalCode,
                'RegistrantCountry'=>$request->RegisterCountry,
                'RegistrantPhone'=>@$request->RegisterPhone,
                'RegistrantEmailAddress'=>@$request->RegisterEmailAddress,
                'RegistrantCity'=>@$request->RegisterCity,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                return ['success'=>false, 'message'=>@$response['Errors']['Error']];
            }

            return ['success'=>true];


        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function changeNameservers(){

        $domain = $this->domain;
        $request = $this->request;
        $nameservers = null;

        if($request){
            $nameservers = $request->ns1.','.$request->ns2;

            if($request->ns3){
                $nameservers .= ','.$request->ns3;
            }
    
            if($request->ns4){
                $nameservers .= ','.$request->ns4;
            }

        }else{
            $nameservers = $domain->ns1.','.$domain->ns2;

            if($domain->ns3){
                $nameservers .= ','.$domain->ns3;
            }
    
            if($domain->ns4){
                $nameservers .= ','.$domain->ns4;
            }
        }

        $array = explode(',', $nameservers);
        $ns1 = @$array[0];
        $ns2 = @$array[1];
        $ns3 = @$array[2];
        $ns4 = @$array[3];
   
        try{
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.dns.setCustom',
                'ClientIp'=>$this->requestIP,
                'SLD'=>explode('.', $domain->domain)[0],
                'TLD'=>explode('.', $domain->domain)[1],
                'NameServers'=>$nameservers,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                return ['success'=>false, 'message'=>@$response['Errors']['Error']];
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
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'Namecheap.Whoisguard.enable',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,
                'ForwardedToEmail'=>$domain->user->email,
                'WhoisGuardid'=>$domain->whois_guard,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                return ['success'=>false, 'message'=>@$response['Errors']['Error']];
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
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'Namecheap.Whoisguard.disable',
                'ClientIp'=>$this->requestIP,
                'WhoisGuardid'=>$domain->whois_guard,
            ]);
    
            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                return ['success'=>false, 'message'=>@$response['Errors']['Error']];
            }

            $domain->id_protection = 0; 
            $domain->save(); 
    
            return ['success'=>true];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }


}

