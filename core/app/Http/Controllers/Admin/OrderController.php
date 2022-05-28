<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\GeneralSetting;
use App\Models\Hosting;
use App\Models\Domain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DomainRegister;
use App\DomainRegisters\Register; 

class OrderController extends Controller{

    protected function with($with = []){
        $array = ['invoice.payment.gateway', 'user'];
        return array_merge($array, $with);
    } 

    public function all(){ 
        $pageTitle = 'Manage Orders';
        $orders = Order::latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.order.all', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function pending(){  
        $pageTitle = 'Pending Orders';
        $orders = Order::pending()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.order.all', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function active(){  
        $pageTitle = 'Active Orders'; 
        $orders = Order::active()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.order.all', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function cancelled(){  
        $pageTitle = 'Cancelled Orders';
        $orders = Order::cancelled()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.order.all', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function details($id){   
        $order = Order::with($this->with(['hostings.product.serviceCategory', 'domains.details', 'hostings.details']))->findOrFail($id);
        $pageTitle = 'Order Details'; 
        $domainRegisters = DomainRegister::active()->get(['id', 'name']);
        return view('admin.order.details', compact('pageTitle', 'order', 'domainRegisters'));
    }

    public function accept(Request $request){
       
        $request->validate([
            'order_id'=> 'required'
        ]);

        $order = Order::pending()->where('id', $request->order_id)->with('hostings')->firstOrFail();
        $error = false;

        foreach($request->hostings ?? [] as $id => $hosting){

            $hosting = (object) $hosting;
            $service = $order->hostings->find($id);

            if($service){
                $product = $service->product;

                $service->username = @$hosting->username;
                $service->password = @$hosting->password;
                $service->server_id = @$hosting->server_id;
                $service->save();
    
                if(@$service->domain_status == 2 && $order->status == 2){
                    if(@$hosting->run_create_module && @$product->module_option == 2 && $product->module_type == 1){
                        $service->domain_status = 1;
                    }else{
                        $service->domain_status = 1;
                    }
                    
                    if(@$hosting->send_email){
                    }
                }
    
                $service->save();
            }

        }

        foreach($request->domains ?? [] as $id => $domain){

            $domain = (object) $domain;
            $service = $order->domains->find($id);
         
            if($service && DomainRegister::where('id', $domain->register)->exists()){
                $service->domain_register_id = $domain->register;
                $service->save();
    
                if(@$domain->domain_register){
                    $register = new Register($service->register->alias);
                    $register->domain = $service;
                    $register->command = 'register';
                    $execute = $register->run();
                   
                    if(!$execute['success']){
                        $error = true;
                        $notify[] = ['error', $execute['message']];
                    }else{
                        $service->status = 1;
    
                        if(@$domain->send_email){
                        }
                    }
    
                }else{
                    $service->status = 1;
                }
    
                $service->save(); 
            }

        }

        if(!$error){
            $order->status = 1; 
            $order->save();
            $notify[] = ['success', 'Order accepted successfully'];
        }

        return back()->withNotify($notify);
    }

    public function orderNotes(Request $request){

        $request->validate([
            'order_id'=> 'required', 
            'admin_notes'=> 'required',
        ]);

        $order = Order::where('id', $request->order_id)->firstOrFail();
        $order->admin_notes = $request->admin_notes;
        $order->save();

        $notify[] = ['success', 'Order notes updated successfully'];
        return back()->withNotify($notify);
    }

    public function cancel(Request $request){
     
        $request->validate([
            'order_id'=> 'required'
        ]);

        $order = Order::where('status', 2)->findOrFail($request->order_id);
        $order->status = 3;
        $order->save();

        $hostings = $order->hostings;
        $domains = $order->domains;

        Hosting::whereIn('id', $hostings->pluck('id'))->update(['domain_status'=> 5]);
        Domain::whereIn('id', $domains->pluck('id'))->update(['status'=> 5]);

        $notify[] = ['success', 'Order accepted successfully'];
        return back()->withNotify($notify);

    }

    public function markPending(Request $request){

        $request->validate([
            'order_id'=> 'required'
        ]);

        $order = Order::where('status', '!=', 2)->findOrFail($request->order_id);
        $order->status = 2;
        $order->save();

        $hostings = $order->hostings;
        $domains = $order->domains;

        Hosting::whereIn('id', $hostings->pluck('id'))->update(['domain_status'=> 2]);
        Domain::whereIn('id', $domains->pluck('id'))->update(['status'=> 2]);

        $notify[] = ['success', 'Order set back to pending successfully'];
        return back()->withNotify($notify);
    }

}
