<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;


class ModuleController extends Controller{
    
    public function moduleCommand(Request $request){

        $request->validate([
            'hosting_id'=> 'required',
            'module_type'=> 'required|between:1,6',
            'suspend_reason'=> 'required_if:module_type,==,2',
            'password'=> 'required_if:module_type,==,6',
        ]);

        $hosting = Hosting::where('status', 1)->findOrFail($request->hosting_id);
 
        if($request->module_type == 1){ 
            return $this->create($hosting);
        }
        elseif($request->module_type == 2){
            return $this->suspend($hosting, $request);
        }
        elseif($request->module_type == 3){
            return $this->unSuspend($hosting, $request);
        }
        elseif($request->module_type == 4){
            return $this->terminate($hosting);
        }
        elseif($request->module_type == 5){
            return $this->changePackage($hosting);
        }
        elseif($request->module_type == 6){
            return $this->changePassword($hosting, $request);
        }

    } 

    protected function create($hosting){

        $general = GeneralSetting::first();
        $user = $hosting->user;
        $product = $hosting->product; 

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$general->whm_username.':'.$general->whm_api_token,
            ])->get($general->whm_server.'/cpsess'.$general->whm_security_token.'/json-api/createacct?api.version=1&username='.$hosting->username.'&domain='.$hosting->domain.'&contactemail='.$user->email.'&ip='.$hosting->dedicated_ip.'&password='.$hosting->password);
    
            $response = json_decode($response);
        
            if($response->metadata->result == 0){

                if(str_contains($response->metadata->reason, '. at') !== false){
                    $message = explode('. at', $response->metadata->reason)[0];
                }else{
                    $message = $response->metadata->reason;
                }

                $notify[] = ['error', $message];
                return back()->withNotify($notify);
            } 
        
            $hosting->ns1 = $response->data->nameserver;
            $hosting->ns2 = $response->data->nameserver2;
            $hosting->ns3 = $response->data->nameserver3;
            $hosting->ns4 = $response->data->nameserver4;
            $hosting->dedicated_ip = $response->data->ip;
            $hosting->save(); 

            $act = welcomeEmail()[$product->welcome_email]['act'] ?? null; 
           
            if($act == 'HOSTING_ACCOUNT'){
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_domain' => $hosting->domain,
                    'service_first_payment_amount' => showAmount($hosting->first_payment_amount),
                    'service_recurring_amount' => showAmount($hosting->amount),
                    'service_billing_cycle' => billing(@$hosting->billing_cycle, true)['showText'],
                    'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                    'currency' => $general->cur_text,

                    'service_username' => $hosting->username,
                    'service_password' => $hosting->password,
                    'service_server_ip' => $response->data->ip,

                    'ns1' => $response->data->nameserver,
                    'ns2' => $response->data->nameserver2,
                    'ns3' => $response->data->nameserver3 != null ? $response->data->nameserver3 : 'N/A',
                    'ns4' => $response->data->nameserver4 != null ? $response->data->nameserver4 : 'N/A',
                ]);
            }
            // elseif($act == 'RESELLER_ACCOUNT'){
            //     notify($user, $act, [
            //         'service_domain' => $hosting->domain,
            //         'service_username' => $hosting->username,
            //         'service_password' => $hosting->password, 
            //         'service_product_name' => $product->name,
            //         'currency' => $general->cur_text,
            //     ]);
            // }
            // elseif($act == 'VPS_SERVER'){
            //     notify($user, $act, [
            //         'service_product_name' => $product->name,
            //         'service_dedicated_ip' => '',
            //         'service_password' => $hosting->password, 
            //         'service_assigned_ips' => '',
            //         'service_domain' => $hosting->domain,
            //         'currency' => $general->cur_text,
            //     ]);
            // }
            // elseif($act == 'OTHER_PRODUCT'){
            //     notify($user, $act, [
            //         'service_product_name' => $product->name,
            //         'service_payment_method' => 'Site Balance',
            //         'service_recurring_amount' => showAmount($hosting->amount),
            //         'service_billing_cycle' => billing(@$hosting->billing_cycle, true)['showText'],
            //         'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
            //         'currency' => $general->cur_text,
            //     ]);
            // }

            $notify[] = ['success', 'Create module command run successfully'];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }

    }

    protected function suspend($hosting, $request){
        $general = GeneralSetting::first(); 
        $user = $hosting->user;
        $product = $hosting->product;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$general->whm_username.':'.$general->whm_api_token,
            ])->get($general->whm_server.'/cpsess'.$general->whm_security_token.'/json-api/suspendacct?api.version=1&user='.$hosting->username.'&reason='.$request->suspend_reason);
 
            $response = json_decode($response);
     
            if($response->metadata->result == 0){

                if(str_contains($response->metadata->reason, '. at') !== false){
                    $message = explode('. at', $response->metadata->reason)[0];
                }else{
                    $message = $response->metadata->reason;
                }

                $notify[] = ['error', $message];
                return back()->withNotify($notify);
            } 

            $hosting->suspend_reason = $request->suspend_reason;
            $hosting->save();

            if($request->suspend_email){
                notify($user, 'SERVICE_SUSPEND', [
                    'service_name' => $product->name,
                    'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                    'service_suspension_reason' => $request->suspend_reason,
                ]);
            }

            $notify[] = ['success', 'Suspension of '.$hosting->username.' user'];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function unSuspend($hosting, $request){
        $general = GeneralSetting::first(); 
        $user = $hosting->user;
        $product = $hosting->product;
        
        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$general->whm_username.':'.$general->whm_api_token,
            ])->get($general->whm_server.'/cpsess'.$general->whm_security_token.'/json-api/unsuspendacct?api.version=1&user='.$hosting->username);
 
            $response = json_decode($response);
     
            if($response->metadata->result == 0){

                if(str_contains($response->metadata->reason, '. at') !== false){
                    $message = explode('. at', $response->metadata->reason)[0];
                }else{
                    $message = $response->metadata->reason;
                }

                $notify[] = ['error', $message];
                return back()->withNotify($notify);
            } 

            $hosting->suspend_reason = null;
            $hosting->save();

            if($request->unSuspend_email){
                notify($user, 'SERVICE_UNSUSPEND', [
                    'service_name' => $product->name,
                    'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y')
                ]);
            }

            $notify[] = ['success', 'Unsuspension account of '.$hosting->username.' user'];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function changePackage($hosting){
        $general = GeneralSetting::first(); 

        try{
            
            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$general->whm_username.':'.$general->whm_api_token,
            ])->get($general->whm_server.'/cpsess'.$general->whm_security_token.'/json-api/listpkgs?api.version=1');
 
            $response = json_decode($response);
 
            if($response->metadata->result == 0){

                if(str_contains($response->metadata->reason, '. at') !== false){
                    $message = explode('. at', $response->metadata->reason)[0];
                }else{
                    $message = $response->metadata->reason;
                }

                $notify[] = ['error', $message];
                return back()->withNotify($notify);
            } 

            $notify[] = ['success', explode('\n', $response->metadata->reason)[0]];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function terminate($hosting){
        $general = GeneralSetting::first(); 
 
        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$general->whm_username.':'.$general->whm_api_token,
            ])->get($general->whm_server.'/cpsess'.$general->whm_security_token.'/json-api/removeacct?api.version=1&username='.$hosting->username);
 
            $response = json_decode($response);
     
            if($response->metadata->result == 0){

                if(str_contains($response->metadata->reason, '. at') !== false){
                    $message = explode('. at', $response->metadata->reason)[0];
                }else{
                    $message = $response->metadata->reason;
                }

                $notify[] = ['error', $message];
                return back()->withNotify($notify);
            } 

            $notify[] = ['success', explode('\n', $response->metadata->reason)[0]];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function changePassword($hosting, $request){
        $general = GeneralSetting::first(); 
 
        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$general->whm_username.':'.$general->whm_api_token,
            ])->get($general->whm_server.'/cpsess'.$general->whm_security_token.'/json-api/passwd?api.version=1&user='.$hosting->username.'&password='.$request->password);
 
            $response = json_decode($response);
     
            if($response->metadata->result == 0){

                if(str_contains($response->metadata->reason, '. at') !== false){
                    $message = explode('. at', $response->metadata->reason)[0];
                }else{
                    $message = $response->metadata->reason;
                }

                $notify[] = ['error', $message];
                return back()->withNotify($notify);
            } 
        
            $hosting->password = @$request->password;
            $hosting->save(); 

            $notify[] = ['success', explode('\n', $response->metadata->reason)[0]];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }



}

