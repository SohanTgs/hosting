<?php

namespace App\Observers;

use App\Models\GeneralSetting;
use App\Models\Hosting;
use App\Models\HostingConfig;

class HostingObserver
{
    /**
     * Handle the Hosting "created" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */

    public function created(Hosting $hosting)
    {       
        $array = [];  

        $hosting = Hosting::where('id', $hosting->id)->first();

        if($hosting->config_options){
            $data =  (array) json_decode($hosting->config_options);
    
            foreach($data as $optionId => $subOptionId){
                $array[] = [
                    'hosting_id' => $hosting->id,
                    'configurable_group_option_id' => $optionId,                            
                    'configurable_group_sub_option_id' => $subOptionId,                            
                ];
            }
            
            HostingConfig::insert($array);
        }  
    }

    /**
     * Handle the Hosting "updated" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function updated(Hosting $hosting){

        $hosting = Hosting::where('id', $hosting->id)->first();
        $general = GeneralSetting::first();

        $product = $hosting->product;
        $user = $hosting->user;

        if($hosting->deposit->method_code < 1000){
            if($hosting->stock_control){
                $product->decrement('stock_quantity');
                $product->save();
            }
        } 

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
            ]);
        }
        elseif($act == 'RESELLER_ACCOUNT'){
            notify($user, $act, [
                'service_domain' => $hosting->domain,
                'service_username' => $hosting->username,
                'service_password' => $hosting->password, 
                'service_product_name' => $product->name,
                'currency' => $general->cur_text,
            ]);
        }
        elseif($act == 'VPS_SERVER'){
            notify($user, $act, [
                'service_product_name' => $product->name,
                'service_dedicated_ip' => '',
                'service_password' => $hosting->password, 
                'service_assigned_ips' => '',
                'service_domain' => $hosting->domain,
                'currency' => $general->cur_text,
            ]);
        }
        elseif($act == 'OTHER_PRODUCT'){
            notify($user, $act, [
                'service_product_name' => $product->name,
                'service_payment_method' => 'Site Balance',
                'service_recurring_amount' => showAmount($hosting->amount),
                'service_billing_cycle' => billing(@$hosting->billing_cycle, true)['showText'],
                'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                'currency' => $general->cur_text,
            ]);
        }

    }

    /**
     * Handle the Hosting "deleted" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function deleted(Hosting $hosting)
    {
        //
    }

    /**
     * Handle the Hosting "restored" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function restored(Hosting $hosting)
    {
        //
    }

    /**
     * Handle the Hosting "force deleted" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function forceDeleted(Hosting $hosting)
    {
        //
    }
}






