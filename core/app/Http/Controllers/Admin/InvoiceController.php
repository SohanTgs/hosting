<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Frontend;
use PDF;

class InvoiceController extends Controller{ 
    
    protected function with($with = []){
        $array = ['order', 'user', 'payment.gateway'];
        return array_merge($array, $with);  
    }  
 
    public function all(){  
        $pageTitle = 'Manage Invoice'; 
        $invoices = Invoice::latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.invoice.all', compact('pageTitle', 'invoices', 'emptyMessage'));
    }

    public function paid(){  
        $pageTitle = 'Paid Invoice'; 
        $invoices = Invoice::paid()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.invoice.all', compact('pageTitle', 'invoices', 'emptyMessage'));
    }

    public function unpaid(){
        $pageTitle = 'Paid Invoice'; 
        $invoices = Invoice::unpaid()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.invoice.all', compact('pageTitle', 'invoices', 'emptyMessage'));
    }

    public function details($id){   
        $pageTitle = 'Invoice Details';
        $invoice = Invoice::findOrFail($id);
        return view('admin.invoice.details', compact('pageTitle', 'invoice'));
    }

    public function download($id, $view = null){
        $invoice = Invoice::findOrFail($id);
        $address = Frontend::where('data_keys','invoice_address.content')->first();
        $user = $invoice->user;
        $pageTitle = 'Invoice';

        $pdf = PDF::loadView('invoice', compact('pageTitle', 'invoice', 'user', 'address'));

        if($view){
            return $pdf->stream('invoice.pdf');
        }

        return $pdf->download('invoice.pdf');
    }

}
