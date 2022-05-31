<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Frontend;
use App\Models\InvoiceItem;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Transaction;
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

    public function cancelled(){
        $pageTitle = 'Cancelled Invoice'; 
        $invoices = Invoice::cancelled()->latest()->with($this->with())->paginate(getPaginate());
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
        $pageTitle = 'Unpaid Invoice'; 
        $invoices = Invoice::unpaid()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.invoice.all', compact('pageTitle', 'invoices', 'emptyMessage'));
    }

    public function paymentPending(){  
        $pageTitle = 'Payment Pending Invoice'; 
        $invoices = Invoice::paymentPending()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.invoice.all', compact('pageTitle', 'invoices', 'emptyMessage'));
    }

    public function refunded(){  
        $pageTitle = 'Refunded Invoice'; 
        $invoices = Invoice::refunded()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.invoice.all', compact('pageTitle', 'invoices', 'emptyMessage'));
    }

    public function details($id){   
        $pageTitle = 'Invoice Details';
        $invoice = Invoice::findOrFail($id);
        return view('admin.invoice.details', compact('pageTitle', 'invoice'));
    }
    
    public function updateInvoice(Request $request){

        $request->validate([
            'invoice_id'=>'required',
            'created'=>'nullable|date_format:d-m-Y',
            'due_date'=>'nullable|date_format:d-m-Y',
            'paid_date'=>'nullable|date_format:d-m-Y',
            'status'=>'required|integer|in:'.Invoice::status(true),
        ]);
        
        $invoice = Invoice::where('status', '!=', 5)->findOrFail($request->invoice_id);
        $totalAmount = 0;

        foreach($request->items as $id => $value){
            $item = InvoiceItem::where('id', $id)->where('invoice_id', $invoice->id)->first();

            if($item){
                $item->update(['description'=>$value['description'], 'amount'=>$value['amount']]);
            }else{
                $newItem = new InvoiceItem();
                $newItem->invoice_id = $invoice->id;
                $newItem->user_id = $invoice->user_id;
                $newItem->type = 5;
                $newItem->description = $value['description'];
                $newItem->amount = $value['amount'];
                $newItem->save();
            }

            $totalAmount += $value['amount'];
        }

        $invoice->amount = $totalAmount;

        $invoice->status = $request->status;
        $invoice->created = $request->created;
        $invoice->due_date = $request->due_date;
        $invoice->paid_date = $request->paid_date;
        $invoice->admin_notes = $request->admin_notes;

        $invoice->save();

        $notify[] = ['success', 'Invoice items updated successfully'];
        return back()->withNotify($notify);
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

    public function deleteInvoiceItem(Request $request){

        $request->validate([
            'invoice_id'=>'required',
            'id'=>'required',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        InvoiceItem::where('id', $request->id)->where('invoice_id', $invoice->id)->firstOrFail()->delete();
        $totalAmount = InvoiceItem::where('invoice_id', $invoice->id)->sum('amount');

        $invoice->amount = $totalAmount;        
        $invoice->save();        

        $notify[] = ['success', 'Invoice item deleted successfully'];
        return back()->withNotify($notify);
    }

    public function refundInvoice(Request $request){

        $request->validate([
            'invoice_id'=>'required',
            'amount'=>'nullable|numeric|gt:0',
        ]);

        $invoice = Invoice::paid()->findOrFail($request->invoice_id);
        $amount = $request->amount;
        $user = $invoice->user;

        if($amount > $invoice->amount){
            $notify[] = ['error', 'Sorry, Refund amount must be less than invoice amount'];
            return back()->withNotify($notify);
        }

        $refundAmount = $amount ?? $invoice->amount;

        $invoice->status = 5;
        $invoice->save();

        $user->balance += $refundAmount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $refundAmount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Invoice refund';
        $transaction->trx =  getTrx();
        $transaction->save();

        $notify[] = ['success', 'Invoice refunded successfully'];
        return back()->withNotify($notify);
    }

    public function domainInvoices($id){
        $domain = Domain::findOrFail($id);
        $pageTitle = 'All Invoices - '.$domain->domain;
        $invoices = Invoice::where('domain_id', $domain->id)->latest()->with('user', 'payment.gateway')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.domain.invoices', compact('pageTitle', 'invoices', 'domain', 'emptyMessage'));
    }

    public function hostingInvoices($id){
        $hosting = Hosting::findOrFail($id);
        $pageTitle = 'All Invoices';
        $invoices = Invoice::where('hosting_id', $hosting->id)->latest()->with('user', 'payment.gateway')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.domain.invoices', compact('pageTitle', 'invoices', 'hosting', 'emptyMessage'));
    }

}
