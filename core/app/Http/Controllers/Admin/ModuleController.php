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
            'suspend_reason'=> 'required_if:module_type,==,2'
        ]);

        $hosting = Hosting::where('status', 1)->findOrFail($request->hosting_id);

        if(!$hosting->server_id){
            $notify[] = ['error', 'Select server before running the module command'];
            return back()->withNotify($notify);
        }

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
            return $this->changePackage($hosting, $request);
        }
        elseif($request->module_type == 6){
            return $this->changePassword($hosting);
        }

    } 
        
    protected function create($hosting){

        $general = GeneralSetting::first();
        $user = $hosting->user;
        $product = $hosting->product; 
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/createacct?api.version=1&username='.$hosting->username.'&domain='.$hosting->domain.'&contactemail='.$user->email.'&ip='.$hosting->dedicated_ip.'&password='.$hosting->password.'&pkgname='.$product->package_name);
    
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $notify[] = ['error', @$responseStatus['message']];
                return back()->withNotify($notify);
            }

            $hosting->ns1 = $response->data->nameserver;
            $hosting->ns2 = $response->data->nameserver2;
            $hosting->ns3 = $response->data->nameserver3;
            $hosting->ns4 = $response->data->nameserver4;
            $hosting->package_name = $product->package_name;
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

            $notify[] = ['success', 'Create module command run successfully'];
            return back()->withNotify($notify)->with('response', $response);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }

    }

    protected function suspend($hosting, $request){
      
        $user = $hosting->user;
        $product = $hosting->product;
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/suspendacct?api.version=1&user='.$hosting->username.'&reason='.$request->suspend_reason);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $notify[] = ['error', @$responseStatus['message']];
                return back()->withNotify($notify);
            }

            $hosting->suspend_reason = $request->suspend_reason;
            $hosting->suspend_date = now();
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

        $user = $hosting->user;
        $product = $hosting->product;
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/unsuspendacct?api.version=1&user='.$hosting->username);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $notify[] = ['error', @$responseStatus['message']];
                return back()->withNotify($notify);
            }
            
            $hosting->suspend_reason = null;
            $hosting->suspend_date= null;
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

    protected function changePackage($hosting, $request){
        $server = $hosting->server;
        $product = $hosting->product;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/changepackage?api.version=1&user='.$hosting->username.'&pkg='.$product->package_name);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $notify[] = ['error', @$responseStatus['message']];
                return back()->withNotify($notify);
            }

            $hosting->package_name = $request->package_name;
            $hosting->save();

            $notify[] = ['success', 'Changed package for '.$hosting->username.' user'];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function terminate($hosting){
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/removeacct?api.version=1&username='.$hosting->username);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $notify[] = ['error', @$responseStatus['message']];
                return back()->withNotify($notify);
            }

            $hosting->termination_date = now();
            $hosting->save();

            $notify[] = ['success', explode('\n', $response->metadata->reason)[0]];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function changePassword($hosting){
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/passwd?api.version=1&user='.$hosting->username.'&password='.$hosting->password);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $notify[] = ['error', @$responseStatus['message']];
                return back()->withNotify($notify);
            }

            $notify[] = ['success', explode('\n', $response->metadata->reason)[0]];
            return back()->withNotify($notify);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function whmApiResponse($response){
        $success = true;
        $message = null;

        if($response->metadata->result == 0){

            $success = false;

            if(str_contains($response->metadata->reason, '. at') !== false){
                $message = explode('. at', $response->metadata->reason)[0];
            }else{
                $message = $response->metadata->reason;
            }
        }

        return ['success'=>$success, 'message'=>$message];
    }

    public function loginCpanel(Request $request){

        $request->validate([
            'hosting_id'=> 'required'
        ]);

        $hosting = Hosting::findOrFail($request->hosting_id);
        $product = $hosting->product;
        $server = $hosting->server;

        if(!$server){
            $notify[] = ['error', 'There is no selected server to auto-login'];
            return back()->withNotify($notify); 
        }

        if($product->module_type == 0){
            $notify[] = ['error', 'Unable to auto-login'];
            return back()->withNotify($notify);
        }

        try{
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode($server->username.':'.$server->password),
            ])->get($server->hostname.'/json-api/create_user_session?api.version=1&user='.$hosting->username.'&service=cpaneld');
    
            $response = json_decode($response);
      
            if(@$response->cpanelresult->error){
                $notify[] = ['error', @$response->cpanelresult->data->reason];
                return back()->withNotify($notify);
            }

            $redirectUrl = $response->data->url;
            return back()->with('url', $redirectUrl);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }


    }


}

