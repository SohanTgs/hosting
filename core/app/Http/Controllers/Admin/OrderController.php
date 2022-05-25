<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        return view('admin.order.details', compact('pageTitle', 'order'));
    }

    public function accept(Request $request){
       
        $request->validate([
            'order_id'=> 'required'
        ]);

        $order = Order::where('status', 2)->where('id', $request->order_id)->firstOrFail();
        $order->status = 1; 
        $order->save();

        // foreach($order->hostings as $hosting){
        //     $hosting->status = 1;
        //     $hosting->domain_status = 1;
        //     $hosting->save();
            // $product = $hosting->product;

            // if($product->module_type == 1 && $product->module_option == 1){ 
            //     $this->createCpanelAccount($hosting, $product);
            // }
        // }

        // foreach($order->domains as $domain){
        //     $domain->status = 2;
        //     $domain->save();
        // }

        $notify[] = ['success', 'Order accepted successfully'];
        return back()->withNotify($notify);

    }

    public function cancel(Request $request){
     
        $request->validate([
            'order_id'=> 'required'
        ]);

        $order = Order::where('status', 2)->findOrFail($request->order_id);
        $order->status = 1;
        $order->save();

        $notify[] = ['success', 'Order accepted successfully'];
        return back()->withNotify($notify);

    }

    // protected function createCpanelAccount($hosting, $product){
        
    //     $general = GeneralSetting::first('cur_text');
    //     $user = $hosting->user;
    //     $server = $hosting->server;

    //     try{

    //         $response = Http::withHeaders([
    //             'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
    //         ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/createacct?api.version=1&username='.$hosting->username.'&domain='.$hosting->domain.'&contactemail='.$user->email.'&password='.$hosting->password.'&pkgname='.$product->package_name);
    
    //         $response = json_decode($response);
 
    //         if($response->metadata->result == 0){

    //             $message = null;
    
    //             if(str_contains($response->metadata->reason, '. at') !== false){
    //                 $message = explode('. at', $response->metadata->reason)[0];
    //             }else{
    //                 $message = $response->metadata->reason;
    //             }

    //             Log::error($message);
    //         }

    //         $hosting->ns1 = $response->data->nameserver;
    //         $hosting->ns2 = $response->data->nameserver2;
    //         $hosting->ns3 = $response->data->nameserver3;
    //         $hosting->ns4 = $response->data->nameserver4;
    //         $hosting->package_name = $product->package_name;
    //         $hosting->save(); 

    //         $act = welcomeEmail()[$product->welcome_email]['act'] ?? null; 
           
    //         if($act == 'HOSTING_ACCOUNT'){
    //             notify($user, $act, [
    //                 'service_product_name' => $product->name,
    //                 'service_domain' => $hosting->domain,
    //                 'service_first_payment_amount' => showAmount($hosting->first_payment_amount),
    //                 'service_recurring_amount' => showAmount($hosting->amount),
    //                 'service_billing_cycle' => billing(@$hosting->billing_cycle, true)['showText'],
    //                 'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
    //                 'currency' => $general->cur_text,

    //                 'service_username' => $hosting->username,
    //                 'service_password' => $hosting->password,
    //                 'service_server_ip' => $response->data->ip,

    //                 'ns1' => $hosting->ns1,
    //                 'ns2' => $hosting->ns2,
    //                 'ns3' => $hosting->ns3,
    //                 'ns4' => $hosting->ns4,

    //                 'ns1_ip' => $hosting->ns1_ip,
    //                 'ns2_ip' => $hosting->ns2_ip,
    //                 'ns3_ip' => $hosting->ns3_ip,
    //                 'ns4_ip' => $hosting->ns4_ip,
    //             ]);
    //         }

    //     }catch(\Exception  $error){
    //         Log::error($error->getMessage());
    //     }

    // }

}
