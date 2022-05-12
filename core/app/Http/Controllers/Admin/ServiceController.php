<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting; 
use App\Models\HostingConfig; 
use App\Models\Domain; 
use App\Models\ServiceCategory; 
use App\Models\Product;  
use App\Models\DomainRegister;  
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;  

class ServiceController extends Controller{

    public function hostingDetails($id){ 
        $hosting = Hosting::with('hostingConfigs.select', 'hostingConfigs.option', 'product.getConfigs.group.options.subOptions.getOnlyPrice')->findOrFail($id);
        $pageTitle = 'Hosting Details';
        $productDropdown = $this->productDropdown();
        $accountSummary = $this->accountSummary($hosting->server, $hosting->username) ?? null; 
        return view('admin.service.hosting_details', compact('pageTitle', 'hosting', 'productDropdown', 'accountSummary'));
    }   
    
    public function hostingUpdate(Request $request){
 
        $request->validate([
            'id'=>'required' , 
            'domain_status'=>'required|between:0,5',
            'server_id'=>'nullable|exists:servers,id',
            'next_due_date'=>'nullable|date_format:d-m-Y',
            'termination_date'=>'nullable|date_format:d-m-Y',
            'reg_time'=>'nullable|date_format:d-m-Y',
            'billing_cycle'=>'required|between:0,6',
        ]);

        $service = Hosting::findOrFail($request->id);
        $product = $service->product;

        $service->domain = $request->domain;
        $service->first_payment_amount = $request->first_payment_amount;
        $service->amount = $request->amount;
        $service->next_due_date = $request->next_due_date;
        $service->billing_cycle = $request->billing_cycle;

        $service->server_id = $request->server_id; 
        
        $service->termination_date = $request->termination_date; 
        $service->admin_notes = $request->admin_notes; 

        $service->dedicated_ip = $request->dedicated_ip; 
        $service->username = $request->username;
        $service->password = $request->password;

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
                
                $exists = HostingConfig::where('hosting_id', $service->id)->where('configurable_group_option_id', $option)->first();
              
                if($select){
                    
                    if($exists){
                        $exists->update(['configurable_group_sub_option_id'=>$select]);
                    }else{
                        $new = new HostingConfig();
                        $new->hosting_id = $service->id;
                        $new->configurable_group_option_id = $option;
                        $new->configurable_group_sub_option_id = $select;
                        $new->save();
                    }
                }elseif(!$select && $exists){
                    $exists->delete();
                }
       
            }
        }

        if($product->product_type == 3){
            $service->assigned_ips = $request->assigned_ips;
            $service->ns1 = $request->ns1;
            $service->ns2 = $request->ns2;
        }

        if($request->domain_status == 4 && @$service->cancelRequest->status == 2){
            $cancel = @$service->cancelRequest; 
            $cancel->status = 1;
            $cancel->save();
        }

        if($request->domain_status != 4 && @$service->cancelRequest->status == 1){
            $cancel = @$service->cancelRequest; 
            $cancel->status = 2;
            $cancel->save();
        }

        if(@$request->delete_cancel_request){
            @$service->cancelRequest->delete();  
        }

        $service->save();

        $notify[] = ['success', 'Hosting details updated successfully'];
        return back()->withNotify($notify);
    }

    public function domainDetails($id){   
        $domain = Domain::findOrFail($id);
        $pageTitle = 'Domain Details';
        $domainRegisters = DomainRegister::active()->latest()->get(['id', 'name']); 
        return view('admin.service.domain_details', compact('pageTitle', 'domain', 'domainRegisters'));
    } 
  
    public function domainUpdate(Request $request){ 

        $request->validate([ 
            'id'=>'required' , 
            'status'=>'required|in:1,2',
            'reg_time'=>'nullable|date_format:d-m-Y',
            'next_due_date'=>'nullable|date_format:d-m-Y',
            'expiry_date'=>'nullable|date_format:d-m-Y',
            'register_id'=>'exists:domain_registers,id|nullable',
        ]); 

        $domain = Domain::findOrFail($request->id);
        $domain->subscription_id = $request->subscription_id;
        $domain->domain_register_id = $request->register_id;
        $domain->reg_time = $request->reg_time;
        $domain->reg_period = $request->reg_period;
        $domain->next_due_date = $request->next_due_date;
        $domain->domain = $request->domain;
        $domain->expiry_date = $request->expiry_date;
        $domain->first_payment_amount = $request->first_payment_amount;
        $domain->recurring_amount = $request->recurring_amount;
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

    protected function accountSummary($server, $username){

        try{
            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/accountsummary?api.version=1&user='.$username);
            
            $response = json_decode(@$response)->data->acct[0];
            return $response;

        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }

    }


}
