<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\GeneralSetting;

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

    public function initiated(){  
        $pageTitle = 'Initiated Orders';
        $orders = Order::initiated()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.order.all', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function details($id){   
        $order = Order::with($this->with(['hostings.product.serviceCategory']))->findOrFail($id);
        $pageTitle = 'Order Details'; 
        return view('admin.order.details', compact('pageTitle', 'order'));
    }

    public function accept(Request $request){

        $request->validate([
            'order_id'=> 'required'
        ]);

        $order = Order::where('status', '2')->where('id', $request->order_id)->firstOrFail();
        $order->status = 1; 
        $order->save();

        // $general = GeneralSetting::first();
        // $user = $order->user;

        foreach($order->hostings as $hosting){
            $hosting->status = 1;
            $hosting->save();

            // $product = $hosting->product;
            // $act = welcomeEmail()[$product->welcome_email]['act'] ?? null; 

            // if($act == 'HOSTING_ACCOUNT'){
            //     notify($user, $act, [
            //         'service_product_name' => $product->name,
            //         'service_domain' => $hosting->domain,
            //         'service_first_payment_amount' => showAmount($hosting->first_payment_amount),
            //         'service_recurring_amount' => showAmount($hosting->amount),
            //         'service_billing_cycle' => billing(@$hosting->billing_cycle, true)['showText'],
            //         'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
            //         'currency' => $general->cur_text,
            //     ]);
            // }
            // elseif($act == 'RESELLER_ACCOUNT'){
            //     notify($user, $act, [
            //         'service_domain' => $hosting->domain,
            //         'service_username' => $hosting->username,
            //         'service_password' => $hosting->password, 
            //         'service_product_name' => $product->name,
            //         'currency' => $general->cur_text,
            //     ]);
            // }
            // elseif($act == 'VPS_SERVER'){
            //     notify($user, $act, [
            //         'service_product_name' => $product->name,
            //         'service_dedicated_ip' => '',
            //         'service_password' => $hosting->password, 
            //         'service_assigned_ips' => '',
            //         'service_domain' => $hosting->domain,
            //         'currency' => $general->cur_text,
            //     ]);
            // }
            // elseif($act == 'OTHER_PRODUCT'){
            //     notify($user, $act, [
            //         'service_product_name' => $product->name,
            //         'service_payment_method' => 'Site Balance',
            //         'service_recurring_amount' => showAmount($hosting->amount),
            //         'service_billing_cycle' => billing(@$hosting->billing_cycle, true)['showText'],
            //         'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
            //         'currency' => $general->cur_text,
            //     ]);
            // }
        }

        $notify[] = ['success', 'Order accepted successfully'];
        return back()->withNotify($notify);

    }

    public function cancel(Request $request){
        return 200;
        $request->validate([
            'order_id'=> 'required'
        ]);

        $notify[] = ['success', 'Order accepted successfully'];
        return back()->withNotify($notify);

    }

}
