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
        $order = Order::with($this->with(['hostings.product.serviceCategory', 'domains.details', 'hostings.details']))->findOrFail($id);
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

        foreach($order->hostings as $hosting){
            $hosting->status = 1;
            $hosting->save();
        }

        foreach($order->domains as $domain){
            $domain->status = 2;
            $domain->save();
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
