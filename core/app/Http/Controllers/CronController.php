<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hosting;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\BillingSetting;
use App\Models\InvoiceItem;
use App\Models\HostingConfig;
use Carbon\Carbon;

class CronController extends Controller{
    
    protected $billingSetting;
    protected $limit;
    protected $now;
    protected $selectInvoiceColumns;

    public function __construct(){

        $this->billingSetting = BillingSetting::first();
        $this->limit = 100;
        $this->now = Carbon::now()->toDateString();
        $this->selectInvoiceColumns = 'id, reminder, user_id, amount, status, due_date, created, last_cron'; 

    }

    public function index(){

        set_time_limit(0);

        $billingSetting = $this->billingSetting;

        return $this->invoiceGenerate(); 

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

    protected function invoiceGenerate(){

        $hostings = Hosting::active()
                           ->where('invoice', 0)
                           ->where('next_invoice_date', '<=', Carbon::now())
                           ->orderBy('last_cron')
                           ->limit($this->limit)
                           ->get(['id', 'invoice', 'user_id', 'product_id', 'amount', 'domain_status', 'config_options', 'next_due_date', 'last_cron']);

        $domains = Domain::active()
                         ->where('invoice', 0)
                         ->where('next_invoice_date', '<=', Carbon::now())
                         ->orderBy('last_cron')
                         ->limit($this->limit)
                         ->get();
  
        $this->generateHostingInvoice($hostings);
        $this->generateDomainInvoice($domains);

    }

    protected function generateHostingInvoice($hostings){
        foreach($hostings as $hosting){
          
            $invoice = new Invoice();
            $invoice->user_id = $hosting->user_id;
            $invoice->amount = $hosting->amount;
            $invoice->due_date = $hosting->next_due_date;
            $invoice->status = 2;
            $invoice->created = now();
            $invoice->save(); 

            $this->makeHostingConfigs($hosting);
            $this->makeInvoiceItems($hosting, $invoice);
        } 
    }

    protected function makeHostingConfigs($hosting){

        if($hosting->config_options){ 
            $data =  (array) $hosting->config_options;
     
            foreach($data as $optionId => $subOptionId){
                HostingConfig::firstOrCreate([
                    'hosting_id' => $hosting->id,
                    'configurable_group_option_id' => $optionId,                            
                    'configurable_group_sub_option_id' => $subOptionId,                            
                ]);
            }

        }  
    }

    protected function makeInvoiceItems($hosting, $invoice){
        // dd($hosting);
        // foreach($hostings as $hosting){
            $product = $hosting->product;
        
            if($hosting->setup_fee != 0){
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->user_id = $invoice->user_id;
                $item->relation_id = $hosting->id;
                $item->type = 1;
                $item->description = $product->name.' '.'Setup Fee'."\n".$product->serviceCategory->name;
                $item->amount = $hosting->setup_fee;
                $item->save();
            }
          
            $domainText = $hosting->domain ? ' - ' .$hosting->domain : null; 
            $date = $hosting->billing_cycle != 0 ? ' ('.showDateTime($hosting->created_at, 'd/m/Y').' - '.showDateTime($hosting->next_due_date, 'd/m/Y') .')' : ' (One Time)';
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
           
            if($hosting->discount != 0){
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->user_id = $invoice->user_id;
                $item->relation_id = $hosting->id;
                $item->type = 3;
                $item->description = 'Coupon Code: '.@$order->coupon->code.' '.$product->serviceCategory->name;
                $item->amount = $hosting->discount;
                $item->save();
            }

    }

    protected function generateDomainInvoice($domains){
        foreach($domains as $domain){
    
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
            $invoice->last_cron = Carbon::now();
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
            $invoice->last_cron = Carbon::now();
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
            $invoice->last_cron = Carbon::now();
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
            $invoice->last_cron = Carbon::now();
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
            $invoice->last_cron = Carbon::now();
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





