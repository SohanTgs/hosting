<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }

    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Deposit Methods';
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
       
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);


        $user = auth()->user();

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();

        session()->put('Track', $data->trx);
        return redirect()->route('user.deposit.preview');
    }


    public function depositPreview()
    {

        $track = session()->get('Track');
        $data = Deposit::where('trx', $track)->where('status',0)->orderBy('id', 'DESC')->firstOrFail();
        $pageTitle = 'Payment Preview';
        return view($this->activeTemplate . 'user.payment.preview', compact('data', 'pageTitle'));
    }


    public function depositConfirm() 
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status',0)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();
        
        if ($deposit->method_code >= 1000) {
            $this->userDataUpdate($deposit);
            $notify[] = ['success', 'Your deposit request is queued for approval.'];
            return back()->withNotify($notify);
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if(@$data->session){
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($trx)
    {  
        $general = GeneralSetting::first();
        $data = Deposit::where('trx', $trx)->first();  

        if ($data->status == 0) {
            $data->status = 1;
            $data->save();
        
            $user = User::find($data->user_id);
            $user->balance += $data->amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $data->user_id;
            $transaction->amount = $data->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $data->charge; 
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $data->gatewayCurrency()->name;
            $transaction->trx = $data->trx;
            $transaction->save();

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'Deposit successful via '.$data->gatewayCurrency()->name;
            $adminNotification->click_url = urlPath('admin.deposit.successful');
            $adminNotification->save();
            
            notify($user, 'DEPOSIT_COMPLETE', [
                'method_name' => $data->gatewayCurrency()->name,
                'method_currency' => $data->method_currency,
                'method_amount' => showAmount($data->final_amo),
                'amount' => showAmount($data->amount),
                'charge' => showAmount($data->charge),
                'currency' => $general->cur_text,
                'rate' => showAmount($data->rate),
                'trx' => $data->trx,
                'post_balance' => showAmount($user->balance)
            ]);
     
            if($data->order_id){  
              
                $user->balance -= $data->amount;
                $user->save();
      
                $transaction = new Transaction();
                $transaction->user_id = $data->user_id;
                $transaction->amount = $data->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = $data->charge;
                $transaction->trx_type = '-';
                $transaction->trx = getTrx();
                $transaction->details = "Payment";
                $transaction->save(); 
               
                $invoice = $data->invoice; 
                $invoice->status = 1; 
                $invoice->paid_date = Carbon::now();  
                $invoice->save();
               
                $order = $data->order;
                $order->status = 2;
                $order->save(); 
      
                foreach($order->hostings as $hosting){

                    $hosting->status = 1;
                    $hosting->deposit_id = $data->id;
                    $hosting->save();

                    $product = $hosting->product;

                    if($product->module_type == 1 && $product->module_option == 1){ 
                        static::createCpanelAccount($hosting, $product);
                    } 

                    if($hosting->stock_control){
                        $product->decrement('stock_quantity');
                        $product->save();
                    }

                }
    
                foreach($order->domains as $domain){
                    $domain->status = 2;
                    $domain->deposit_id = $data->id;
                    $domain->expiry_date = Carbon::now()->addYear($domain->reg_period);
                    $domain->save();
                }

            }

        }

    }

    protected function createCpanelAccount($hosting, $product){
        
        $general = GeneralSetting::first('cur_text');
        $user = $hosting->user;
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/createacct?api.version=1&username='.$hosting->username.'&domain='.$hosting->domain.'&contactemail='.$user->email.'&password='.$hosting->password.'&pkgname='.$product->package_name);
    
            $response = json_decode($response);
 
            if($response->metadata->result == 0){

                $message = null;
    
                if(str_contains($response->metadata->reason, '. at') !== false){
                    $message = explode('. at', $response->metadata->reason)[0];
                }else{
                    $message = $response->metadata->reason;
                }

                Log::error($message);
            }

            $hosting->package_name = $product->package_name;

            $hosting->ns1 = $server->ns1;
            $hosting->ns2 = $server->ns2;
            $hosting->ns3 = $server->ns3;
            $hosting->ns4 = $server->ns4;

            $hosting->ns1_ip = $server->ns1_ip;
            $hosting->ns2_ip = $server->ns2_ip;
            $hosting->ns3_ip = $server->ns3_ip;
            $hosting->ns4_ip = $server->ns4_ip;
            $hosting->domain_status = 1;
            $hosting->ip = $response->data->ip;

            $hosting->save(); 

            $act = welcomeEmail()[$product->welcome_email]['act'] ?? null; 
           
            if($act == 'HOSTING_ACCOUNT'){
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_domain' => $hosting->domain,
                    'service_first_payment_amount' => showAmount($hosting->first_payment_amount),
                    'service_recurring_amount' => showAmount($hosting->amount),
                    'service_billing_cycle' => billingCycle(@$hosting->billing_cycle, true)['showText'],
                    'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                    'currency' => $general->cur_text,

                    'service_username' => $hosting->username,
                    'service_password' => $hosting->password,
                    'service_server_ip' => $hosting->ip,

                    'ns1' => $hosting->ns1,
                    'ns2' => $hosting->ns2,
                    'ns3' => $hosting->ns3,
                    'ns4' => $hosting->ns4,

                    'ns1_ip' => $hosting->ns1_ip,
                    'ns2_ip' => $hosting->ns2_ip,
                    'ns3_ip' => $hosting->ns3_ip,
                    'ns4_ip' => $hosting->ns4_ip, 
                ]);
            }

        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }

    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {

            $pageTitle = 'Deposit Confirm';
            $method = $data->gatewayCurrency();
            return view($this->activeTemplate . 'user.manual_payment.manual_confirm', compact('data', 'pageTitle', 'method'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }
    
        $params = json_decode($data->gatewayCurrency()->gateway_parameter);

        $rules = [];
        $inputField = [];
        $verifyImages = [];

        if ($params != null) {
            foreach ($params as $key => $custom) {
                $rules[$key] = [$custom->validation];
                if ($custom->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');

                    array_push($verifyImages, $key);
                }
                if ($custom->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($custom->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $this->validate($request, $rules);


        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['deposit']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField = [];
        if ($params != null) {
            foreach ($collection as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $inKey];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $data->detail = $reqField;
        } else {
            $data->detail = null;
        }

        $data->status = 2; // pending
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id; 
        $adminNotification->title = 'Deposit request from '.$data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details',$data->id);
        $adminNotification->save();

        $general = GeneralSetting::first();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($data->rate), 
            'trx' => $data->trx
        ]);

        if($data->order_id){
            $order = $data->order;
            $hostings = $order->hostings;

            foreach($hostings as $hosting){ 

                $hosting->deposit_id = $data->id;
                $hosting->save();

                if($hosting->stock_control){
                    $product = $hosting->product;

                    $product->decrement('stock_quantity');
                    $product->save();
                }
            }

            foreach($order->domains as $domain){
                $domain->deposit_id = $data->id;
                $domain->expiry_date = Carbon::now()->addYear($domain->reg_period);
                $domain->save();
            }

            $invoice = $data->invoice;
            $invoice->paid_date = now();
            $invoice->save();
        } 

        $notify[] = ['success', 'You have deposit request has been taken.'];
        return redirect()->route('user.deposit.history')->withNotify($notify);
    }


}