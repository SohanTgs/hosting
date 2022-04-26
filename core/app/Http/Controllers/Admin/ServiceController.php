<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting; 
use App\Models\HostingConfig; 
use App\Models\Domain; 
use App\Models\GeneralSetting; 
use App\Models\ServiceCategory; 
use App\Models\Product; 
use Illuminate\Support\Facades\Http;


class ServiceController extends Controller{

    public function hostingDetails($id){ 
        $hosting = Hosting::with('hostingConfigs.select', 'hostingConfigs.option', 'product.getConfigs.group.options.subOptions.getOnlyPrice')->findOrFail($id);
        $pageTitle = 'Hosting Details';
        $productDropdown = $this->productDropdown();
        return view('admin.service.hosting_details', compact('pageTitle', 'hosting', 'productDropdown'));
    }  
  
    public function hostingUpdate(Request $request){

        $request->validate([
            'id'=>'required' , 
            'domain_status'=>'required|between:1,3',
            'server_id'=>'nullable|exists:servers,id',
        ]);

        $oldStatus = 0;

        $service = Hosting::findOrFail($request->id);
        $product = $service->product;

        $service->domain = $request->domain;
        $service->first_payment_amount = $request->first_payment_amount;
        $service->amount = $request->amount;
        $service->next_due_date = $request->next_due_date;
        $service->billing_cycle = $request->billing_cycle;

        if($request->server_id){
            $service->server_id = $request->server_id; 
        }

        $service->termination_date = $request->termination_date ?? null; 
        $service->admin_notes = $request->admin_notes ?? null; 

        $service->dedicated_ip = $request->dedicated_ip; 
        $service->username = $request->username;
        $service->password = $request->password;

        $oldStatus = $service->domain_status;

        $service->domain_status = $request->domain_status;
        $service->reg_time = $request->reg_time;

        if($request->config_options){
            foreach($request->config_options as $option => $select){
                
                if($option){
                    $optionResponse = $this->getOptionAndSelect($product, 'option', $option);
                    
                    if(!@$optionResponse['success']){
                        $notify[] = ['error', @$optionResponse['message']];
                        return back()->withNotify($notify);
                    } 
                } 
            
                if($select){
                    $selectResponse = $this->getOptionAndSelect($product, 'select', $select); 
                   
                    if(!@$selectResponse['success']){
                        $notify[] = ['error', @$selectResponse['message']];
                        return back()->withNotify($notify);
                    }
                }

                if($select){
                    $exists = HostingConfig::where('hosting_id', $service->id)->where('configurable_group_option_id', $option)->first();

                    if($exists){
                        $exists->update(['configurable_group_sub_option_id'=>$select]);
                    }else{
                        $new = new HostingConfig();
                        $new->hosting_id = $service->id;
                        $new->configurable_group_option_id = $option;
                        $new->configurable_group_sub_option_id = $select;
                        $new->save();
                    }
                }
            }
        }

        if($product->product_type == 3){
            $service->assigned_ips = $request->assigned_ips;
            $service->ns1 = $request->ns1;
            $service->ns2 = $request->ns2;
        }

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

    public function changeHostingProduct($hostingId, $productId){

        $product = Product::findOrFail($productId);
        $hosting = Hosting::findOrFail($hostingId);

        $hosting->product_id = $productId;
        $hosting->server_id = $product->server_id;
        $hosting->save();

        $notify[] = ['success', 'Your changes saved successfully'];
        return back()->withNotify($notify);
    }

    protected function productDropdown(){
       
        $option = null;
        $allCategory = ServiceCategory::with(['products'=>function($product){
            $product->select('id', 'category_id', 'name');
        }])->get(['id', 'name']);
    
        foreach($allCategory as $category){
            $option .= "<option value='' class='font-weight-bold'>".trans($category->name)."</option>";

            if(count($category->products)){
                foreach($category->products as $product){
                    $option .= "<option value='$product->id'>&nbsp;&nbsp;&nbsp;".trans($product->name)."</option>";
                }
            }else{
                $option .= "<option value=''>&nbsp;&nbsp;&nbsp;".trans('N/A')."</option>";
            }
        }
        
        return $option;
    }

    protected function getOptionAndSelect($product, $type, $value){
        
        foreach($product->getConfigs as $config){
            $options = $config->group->options;

            foreach($options as $option){
                $subOptions = $option->subOptions;
              
                if($type == 'option'){
                
                    if(!$option->find($value)){
                        return ['success'=>false, 'message'=>'The selected option is invalid'];
                    }
         
                }
 
                if($type != 'option'){
                    foreach($subOptions as $subOption){
                        
                        if(!$subOption->find($value)){
                            return ['success'=>false, 'message'=>'The selected value is invalid'];
                        }
                        
                    }
                }

                return ['success'=>true];
            }

        }

    } 


}
