<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hosting;
use Carbon\Carbon;

class CronController extends Controller{
    
    public function expire(){
        $hostings = Hosting::where('status', 1)->where('billing', 2)->where('next_invoice_date', '<', Carbon::now())->get();
        
        foreach($hostings as $hosting){
            return $hosting;
        }

    }



}
