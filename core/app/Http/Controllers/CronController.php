<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hosting;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\BillingSetting;
use Carbon\Carbon;

class CronController extends Controller{
    
    protected $billingSetting;
    protected $limit;
    protected $now;

    public function __construct(){

        $this->billingSetting = BillingSetting::first();
        $this->limit = 100;
        $this->now = Carbon::now()->toDateString(); 

    }

    public function index(){

        set_time_limit(0);

        $billingSetting = $this->billingSetting;

        $this->invoiceGenerate(); 

        if($billingSetting->invoice_send_reminder == 1){

            if($billingSetting->invoice_send_reminder_days != 0){
                $this->unpaidInvoiceReminder();
            }
            
            if($billingSetting->invoice_first_over_due_reminder != 0){
                $this->firstOverdueReminder();
            }

            if($billingSetting->invoice_second_over_due_reminder != 0){
                $this->secondOverdueReminder();
            }

            if($billingSetting->invoice_third_over_due_reminder != 0){
                $this->thirdOverdueReminder();
            }

        }

        if($billingSetting->late_fee_days != 0){
            $this->addLateFee($billingSetting);
        }

    }

    protected function invoiceGenerate(){

        $hostings = Hosting::active()->where('next_invoice_date', '<=', Carbon::now())->limit($this->limit)->get();
        $domains = Domain::active()->where('next_invoice_date', '<=', Carbon::now())->limit($this->limit)->get();

        foreach($hostings as $hosting){

        } 

        foreach($domains as $domain){
    
        } 

    }

    protected function unpaidInvoiceReminder(){

        $invoices = Invoice::unpaid()->whereJsonContains('reminder->invoice_send_reminder_days', $this->now)->limit($this->limit)->get();
        
        dump($invoices);

        foreach($invoices as $invoice){

        }   

    }

    protected function firstOverdueReminder(){

        $invoices = Invoice::unpaid()->whereJsonContains('reminder->invoice_first_over_due_reminder', $this->now)->limit($this->limit)->get();

        dump($invoices);

        foreach($invoices as $invoice){

        } 

    }

    protected function secondOverdueReminder(){           
        
        $invoices = Invoice::unpaid()->whereJsonContains('reminder->invoice_second_over_due_reminder', $this->now)->limit($this->limit)->get();
        
        dump($invoices);

        foreach($invoices as $invoice){

        }

    }

    protected function thirdOverdueReminder(){             
        
        $invoices = Invoice::unpaid()->whereJsonContains('reminder->invoice_third_over_due_reminder', $this->now)->limit($this->limit)->get();

        dump($invoices);

        foreach($invoices as $invoice){

        }

    }

    protected function addLateFee($billingSetting){       
        
        $invoices = Invoice::unpaid()->where('due_date', '<', Carbon::now()->subDay($billingSetting->late_fee_days)->toDateString())->limit($this->limit)->get();

        dump($invoices);

        foreach($invoices as $invoice){
 
        }  
    }

}





