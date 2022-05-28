<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hosting;
use App\Models\Domain;
use Carbon\Carbon;

class CronController extends Controller{
    
    public function index(){
       $hostings = Hosting::get();
       $domains = Domain::get();
    }

    protected function addLateFee(){
        return 'addLateFee';
    }

    protected function invoiceGenerate(){
        return 'invoiceGenerate';
    }

    protected function unpaidInvoiceReminder(){
        return 'unpaidInvoiceReminder';
    }

    protected function firstOverdueReminder(){
        return 'firstOverdueReminder';
    }

    protected function secondOverdueReminder(){
        return 'secondOverdueReminder';
    }

    protected function thirdOverdueReminder(){
        return 'thirdOverdueReminder';
    }


}



