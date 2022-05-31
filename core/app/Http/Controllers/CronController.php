<?php

namespace App\Http\Controllers;

use App\Models\Hosting;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\BillingSetting;
use App\Models\InvoiceItem; 
use Carbon\Carbon;

class CronController extends Controller{
    
    protected $selectInvoiceColumns;
    protected $billingSetting;
    protected $limit;
    protected $now;

    public function __construct(){

        $this->selectInvoiceColumns = 'id, reminder, user_id, amount, status, due_date, created, last_cron'; 
        $this->billingSetting = BillingSetting::first();
        $this->now = Carbon::now();
        $this->limit = 100;

    }

    public function index(){

        set_time_limit(0);
        ini_set('max_execution_time', 0);
       
        $billingSetting = $this->billingSetting;
        
        $this->invoiceGenerate($billingSetting); 

        if($billingSetting->invoice_send_reminder == 1){

            if($billingSetting->invoice_send_reminder_days != 0){
                $this->unpaidInvoiceReminder($billingSetting);
            }
            
            if($billingSetting->invoice_first_over_due_reminder != 0){
                $this->firstOverdueReminder($billingSetting);
            }

            if($billingSetting->invoice_second_over_due_reminder != 0){
                $this->secondOverdueReminder($billingSetting);
            }

            if($billingSetting->invoice_third_over_due_reminder != 0){
                $this->thirdOverdueReminder($billingSetting);
            }

        }

        if($billingSetting->late_fee_days != 0){
            $this->addLateFee($billingSetting);
        }
        
    }

    protected function invoiceGenerate($billingSetting){

        $enableForHosting = false;
        $enableForDomain = false;

        if($billingSetting->create_default_invoice_days != 0){
            $enableForHosting = true;
            $enableForDomain = true;
        }else{

            $array = (array) $billingSetting->create_invoice;

            if(array_filter($array)){
                $enableForHosting = true;
            }

            if($billingSetting->create_domain_invoice_days != 0){
                $enableForDomain = true;
            }

        }

        if($enableForHosting){
       
            $hostings = Hosting::active()->where('invoice', 0)->where('next_invoice_date', '<=', Carbon::now())->orderBy('last_cron')->limit($this->limit)
                               ->with([
                                    'hostingConfigs'=>function($config){
                                        $config->select('id', 'hosting_id', 'configurable_group_option_id', 'configurable_group_sub_option_id');
                                    },
                                    'hostingConfigs.select'=>function($configName){
                                        $configName->select('id', 'name', 'option_type', 'configurable_group_id');
                                    },
                                    'hostingConfigs.option'=>function($configValue){
                                        $configValue->select('id', 'name', 'configurable_group_id', 'configurable_group_option_id');
                                    },
                                    'product'=>function($product){
                                        $product->select('id', 'category_id', 'name')->with('serviceCategory', function($category){
                                            $category->select('id', 'name');
                                        });
                                    }
                                ])
                               ->get(['id', 'invoice', 'user_id', 'product_id', 'amount', 'billing_cycle', 'domain_status', 'config_options', 
                                'next_invoice_date', 'next_due_date', 'last_cron']);
              
            $this->generateHostingInvoice($hostings);
        }

        if($enableForDomain){
            
            $domains = Domain::active()->where('invoice', 0)->where('next_invoice_date', '<=', Carbon::now())->orderBy('last_cron')->limit($this->limit)
                             ->get(['id', 'invoice', 'user_id', 'domain', 'id_protection', 'recurring_amount', 'next_due_date', 'reg_period', 'last_cron']);
          
            $this->generateDomainInvoice($domains);
        } 

    }

    protected function generateHostingInvoice($hostings){
        
        foreach($hostings as $hosting){
            
            $billingCycle = billingCycle($hosting->billing_cycle, true);

            $invoice = new Invoice();
            $invoice->hosting_id = $hosting->id;
            $invoice->user_id = $hosting->user_id;
            $invoice->reminder = $invoice->updateReminder();
            $invoice->amount = $hosting->amount;
            $invoice->due_date = $hosting->next_due_date;
            $invoice->status = 2;
            $invoice->created = now();
            $invoice->next_due_date = $billingCycle['carbon'];
            $invoice->save(); 

            $hosting->invoice = 1;
            $hosting->last_cron = $this->now;
            $hosting->save();

            $this->makeInvoiceItems($hosting, $invoice);
        } 
    }

    protected function generateDomainInvoice($domains){
        foreach($domains as $domain){
         
            $invoice = new Invoice();
            $invoice->domain_id = $domain->id;
            $invoice->user_id = $domain->user_id;
            $invoice->reminder = $invoice->updateReminder();
            $invoice->amount = $domain->recurring_amount;
            $invoice->due_date = $domain->next_due_date;
            $invoice->status = 2;
            $invoice->created = now();
            $invoice->next_due_date = Carbon::now()->addYear($domain->reg_period);
            $invoice->save(); 

            $domain->invoice = 1;
            $domain->last_cron = $this->now;
            $domain->save();

            $this->makeInvoiceItems($domain, $invoice, $domain);

        }  
    }

    protected function makeInvoiceItems($hosting, $invoice, $domain = null){
        
        if($domain){
          
            $domainText = ' - '. $domain->domain .' - '. $domain->reg_period . ' Year/s';
            $protection = $domain->id_protection ? '+ ID Protection' : null;
            $text = 'Domain Renewal' . $domainText. ' ('.showDateTime($invoice->created_at, 'd/m/Y').' - '.showDateTime($invoice->next_due_date, 'd/m/Y') .')'."\n".$protection;

            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->relation_id = $domain->id;
            $item->type = 4;
            $item->description = $text;
            $item->amount = $domain->recurring_amount;
            $item->save();
        
        }
        else{
            $product = $hosting->product;
         
            // if($hosting->setup_fee != 0){
            //     $item = new InvoiceItem();
            //     $item->invoice_id = $invoice->id;
            //     $item->user_id = $invoice->user_id;
            //     $item->relation_id = $hosting->id;
            //     $item->type = 1;
            //     $item->description = $product->name.' '.'Setup Fee'."\n".$product->serviceCategory->name;
            //     $item->amount = $hosting->setup_fee;
            //     $item->save();
            // }
      
            $domainText = $hosting->domain ? ' - ' .$hosting->domain : null; 
    
            if($hosting->billing_cycle == 0){
                $date = '(One Time)';
            }else{
                $date = ' ('.showDateTime($invoice->created_at, 'd/m/Y').' - '.showDateTime($invoice->next_due_date, 'd/m/Y') .')';
            }
    
            $text = $product->name . $domainText. $date ."\n".$product->serviceCategory->name;
      
            foreach($hosting->hostingConfigs as $config){
                $text = $text ."\n". $config->select->name.': '.$config->option->name;
            }
    
            $item = new InvoiceItem(); 
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->relation_id = $hosting->id;
            $item->type = 2; 
            $item->description = $text;
            $item->amount = $hosting->amount;
            $item->save();
        }

    }

    protected function unpaidInvoiceReminder($billingSetting){

        $days = $billingSetting->invoice_send_reminder_days;

        $invoices = $this->invoices('unpaid_reminder', $days, '-');
     
        foreach($invoices as $invoice){
          
            $user = $invoice->user;
        
            notify($user, 'INVOICE_PAYMENT_REMINDER', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.view.invoice', $invoice->id),
            ]); 
        
            $invoice->reminder = $invoice->updateReminder('unpaid_reminder');
            $invoice->last_cron = $this->now;
            $invoice->save();

        }   

    }
    
    protected function firstOverdueReminder($billingSetting){

        $days = $billingSetting->invoice_first_over_due_reminder;

        $invoices = $this->invoices('first_over_due_reminder', $days, '+');
    
        foreach($invoices as $invoice){

            $user = $invoice->user;
            
            notify($user, 'FIRST_INVOICE_OVERDUE_NOTICE', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.view.invoice', $invoice->id),
            ]); 

            $invoice->reminder = $invoice->updateReminder('first_over_due_reminder');
            $invoice->last_cron = $this->now;
            $invoice->save();

        } 

    }

    protected function secondOverdueReminder($billingSetting){           
        
        $days = $billingSetting->invoice_second_over_due_reminder;

        $invoices = $this->invoices('second_over_due_reminder', $days, '+');
      
        foreach($invoices as $invoice){

            $user = $invoice->user;
            
            notify($user, 'SECOND_INVOICE_OVERDUE_NOTICE', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.view.invoice', $invoice->id),
            ]); 

            $invoice->reminder = $invoice->updateReminder('second_over_due_reminder');
            $invoice->last_cron = $this->now;
            $invoice->save();

        } 

    }

    protected function thirdOverdueReminder($billingSetting){             
        
        $days = $billingSetting->invoice_third_over_due_reminder;

        $invoices = $this->invoices('third_over_due_reminder', $days, '+');
       
        foreach($invoices as $invoice){

            $user = $invoice->user;
            
            notify($user, 'THIRD_INVOICE_OVERDUE_NOTICE', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.view.invoice', $invoice->id),
            ]); 

            $invoice->reminder = $invoice->updateReminder('third_over_due_reminder');
            $invoice->last_cron = $this->now;
            $invoice->save();
            
        } 

    }

    protected function addLateFee($billingSetting){       

        $days = $billingSetting->late_fee_days;

        $invoices = $this->invoices('add_late_fee', $days, '+');

        foreach($invoices as $invoice){

            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->relation_id = 0;
            $item->type = 6;
            $item->description = 'Late Fee';
            $item->amount = $billingSetting->getLateFee($invoice->amount);
            $item->save();

            $invoice->reminder = $invoice->updateReminder('add_late_fee');
            $invoice->amount = $billingSetting->getLateFee($invoice->amount, true);
            $invoice->last_cron = $this->now;
            $invoice->save();
            
        }  
    }

    protected function invoices($column, $days, $addOrLess){
       
        $append = 'Append_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', $column))).'_Date';
        $select = $this->selectInvoiceColumns;

        $invoices = Invoice::unpaid()
                    ->whereJsonContains("reminder->$column", 0)
                    ->selectRaw("$select, DATE_FORMAT(due_date $addOrLess interval $days day, '%Y-%m-%d') AS $append")
                    ->whereRaw("DATE_FORMAT(due_date $addOrLess interval $days day, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d')")
                    ->orderBy('last_cron')
                    ->limit($this->limit)
                    ->with('user', function($user){
                        $user->select('id', 'firstname', 'lastname', 'username', 'email', 'mobile', 'country_code');
                    })
                    ->get();

        return $invoices;
    }

}





