<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting; 
use App\Models\Domain; 
use App\Models\GeneralSetting; 
use Illuminate\Support\Facades\Http;

class ServiceController extends Controller{

    public function hostingDetails($id){  
        $hosting = Hosting::with('hostingConfigs.select', 'hostingConfigs.option')->findOrFail($id);
        $pageTitle = 'Hosting Details';

        // try{    

        //     $general = GeneralSetting::first(); 
            
        //     $response = Http::withHeaders([
        //         'Authorization' => 'WHM '.$general->whm_username.':'.$general->whm_api_token,
        //     ])->get($general->whm_server.'/cpsess'.$general->whm_security_token.'/json-api/listpkgs?api.version=1');
 
        //     $response = json_decode($response);
 
        //     if($response->metadata->result == 0){

        //         if(str_contains($response->metadata->reason, '. at') !== false){
        //             $message = explode('. at', $response->metadata->reason)[0];
        //         }else{
        //             $message = $response->metadata->reason;
        //         }

        //         $notify[] = ['error', $message];
        //         return back()->withNotify($notify);
        //     } 

        //     $notify[] = ['success', explode('\n', $response->metadata->reason)[0]];
        //     return back()->withNotify($notify);

        // }catch(\Exception  $error){
        //     $notify[] = ['error', $error->getMessage()];
        //     return back()->withNotify($notify);
        // }

        return view('admin.service.hosting_details', compact('pageTitle', 'hosting'));
    }  
  
    public function hostingUpdate(Request $request){

        $request->validate([
            'id'=>'required' , 
            'domain_status'=>'required|between:1,3'
        ]);

        $oldStatus = 0;

        $service = Hosting::findOrFail($request->id);
        $service->domain = $request->domain;
        $service->dedicated_ip = $request->dedicated_ip; 
        $service->username = $request->username;
        $service->password = $request->password;

        $oldStatus = $service->domain_status;

        $service->domain_status = $request->domain_status;
        $service->save();
   
        $general = GeneralSetting::first();
        $user = $service->user;

        if($oldStatus != 1 && $service->domain_status == 1){ 
            $product = $service->product;
            $act = welcomeEmail()[$product->welcome_email]['act'] ?? null; 
         
            if($act == 'HOSTING_ACCOUNT'){ 
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_domain' => $service->domain,
                    'service_first_payment_amount' => showAmount($service->first_payment_amount),
                    'service_recurring_amount' => showAmount($service->amount),
                    'service_billing_cycle' => billing(@$service->billing_cycle, true)['showText'],
                    'service_next_due_date' => showDateTime($service->next_due_date, 'd/m/Y'),
                    'currency' => $general->cur_text,
                ]);
            }
            elseif($act == 'RESELLER_ACCOUNT'){
                notify($user, $act, [
                    'service_domain' => $service->domain,
                    'service_username' => $service->username,
                    'service_password' => $service->password, 
                    'service_product_name' => $product->name,
                    'currency' => $general->cur_text,
                ]);
            }
            elseif($act == 'VPS_SERVER'){
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_dedicated_ip' => '',
                    'service_password' => $service->password, 
                    'service_assigned_ips' => '',
                    'service_domain' => $service->domain,
                    'currency' => $general->cur_text,
                ]);
            }
            elseif($act == 'OTHER_PRODUCT'){
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_payment_method' => 'Site Balance',
                    'service_recurring_amount' => showAmount($service->amount),
                    'service_billing_cycle' => billing(@$service->billing_cycle, true)['showText'],
                    'service_next_due_date' => showDateTime($service->next_due_date, 'd/m/Y'),
                    'currency' => $general->cur_text,
                ]);
            }
        }

        $notify[] = ['success', 'Hosting details updated successfully'];
        return back()->withNotify($notify);
    }

    public function domainDetails($id){  
        $domain = Domain::findOrFail($id);
        $pageTitle = 'Domain Details';
        return view('admin.service.domain_details', compact('pageTitle', 'domain'));
    } 
  
    public function domainUpdate(Request $request){

        $request->validate([
            'id'=>'required' , 
            'status'=>'required|in:1,2'
        ]); 

        $domain = Domain::findOrFail($request->id);
        $domain->subscription_id = $request->subscription_id;
        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->status = $request->status;
        $domain->save();

        $notify[] = ['success', 'Domain details updated successfully'];
        return back()->withNotify($notify);
    }

}
