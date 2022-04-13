<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting; 
use App\Models\GeneralSetting; 

class ServiceController extends Controller{

    public function details($id){  
        $service = Hosting::with('hostingConfigs.select', 'hostingConfigs.option')->findOrFail($id);
        $pageTitle = 'Service Details';
        return view('admin.service.details', compact('pageTitle', 'service'));
    } 
  
    public function update(Request $request){

        $request->validate([
            'id'=>'required' , 
            'domain_status'=>'required|between:1,3'
        ]);

        $oldDomainStatus = 0;

        $service = Hosting::findOrFail($request->id);
        $service->domain = $request->domain;
        $service->dedicated_ip = $request->dedicated_ip; 
        $service->username = $request->username;
        $service->password = $request->password;

        $oldDomainStatus = $service->domain_status;

        $service->domain_status = $request->domain_status;
        $service->save();
   
        $general = GeneralSetting::first();
        $user = $service->user;

        if($oldDomainStatus != 1 && $service->domain_status == 1){ 
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

        $notify[] = ['success', 'Service details updated successfully'];
        return back()->withNotify($notify);
    }

}
