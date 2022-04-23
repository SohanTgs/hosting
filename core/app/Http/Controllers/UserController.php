<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Product;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\DomainSetup;
use App\Models\Domain;
use App\Models\Frontend;
use App\Models\Hosting;
use App\Models\Invoice;
use App\Models\HostingConfig;
use App\Models\Withdrawal;
use App\Models\GatewayCurrency;
use App\Models\InvoiceItem;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }
    
    public function home()
    {
        $pageTitle = 'Dashboard';
        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle'));
    }

    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = Auth::user();
        return view($this->activeTemplate. 'user.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([ 
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required'=>'Last name field is required'
        ]);
        
        $user = Auth::user();

        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }
        $user->fill($in)->save();
        $notify[] = ['success', 'Profile updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$password_validation]
        ]);
        

        try {
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();
                $notify[] = ['success', 'Password changes successfully.'];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'The password doesn\'t match!'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /*
     * Deposit History
     */
    public function depositHistory()
    {
        $pageTitle = 'Deposit History';
        $emptyMessage = 'No history found.';
        $logs = auth()->user()->deposits()->with(['gateway'])->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'user.deposit_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    /*
     * Withdraw Operation
     */

    public function withdrawMoney()
    {
        $withdrawMethod = WithdrawMethod::where('status',1)->get();
        $pageTitle = 'Withdraw Money';
        return view($this->activeTemplate.'user.withdraw.methods', compact('pageTitle','withdrawMethod'));
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $user = auth()->user();
        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }


        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id; // wallet method ID
        $withdraw->user_id = $user->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();
        session()->put('wtrx', $withdraw->trx);
        return redirect()->route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $withdraw = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id','desc')->firstOrFail();
        $pageTitle = 'Withdraw Preview';
        return view($this->activeTemplate . 'user.withdraw.preview', compact('pageTitle','withdraw'));
    }


    public function withdrawSubmit(Request $request)
    {
        $general = GeneralSetting::first();
        $withdraw = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id','desc')->firstOrFail();

        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $this->validate($request, $rules);
        
        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user,$request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }   
        }


        if ($withdraw->amount > $user->balance) {
            $notify[] = ['error', 'Your request amount is larger then your current balance.'];
            return back()->withNotify($notify);
        }

        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['withdraw']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
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
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
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
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }


        $withdraw->status = 2;
        $withdraw->save();
        $user->balance  -=  $withdraw->amount;
        $user->save();



        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = showAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx =  $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from '.$user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.details',$withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($user->balance),
            'delay' => $withdraw->method->delay
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return redirect()->route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
    {
        $pageTitle = "Withdraw Log";
        $withdraws = Withdrawal::where('user_id', Auth::id())->where('status', '!=', 0)->with('method')->orderBy('id','desc')->paginate(getPaginate());
        $data['emptyMessage'] = "No Data Found!";
        return view($this->activeTemplate.'user.withdraw.log', compact('pageTitle','withdraws'));
    }



    public function show2faForm()
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->sitename, $secret);
        $pageTitle = 'Two Factor';
        return view($this->activeTemplate.'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Google authenticator enabled successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]); 

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Two factor authenticator disable successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify); 
    }  

    private function paymentByWallet($invoice, $order, $user){
  
        $general = GeneralSetting::first();
        $amount = $invoice->amount;
        
        if($amount > $user->balance){
            $notify[] = ['error', 'Your account '.getAmount($user->balance).' '.$general->cur_text.' balance not enough! please deposit money'];
            return back()->withNotify($notify);
        }

        $user->balance -= $amount;
        $user->save();

        $invoice->status = 1;
        $invoice->paid_date = Carbon::now(); 
        $invoice->save();

        $order->status = 2;
        $order->save();
        
        foreach($order->hostings as $hosting){
            $hosting->status = 1;
            $hosting->save();
        } 

        foreach($order->domains as $domain){
            $domain->status = 2;
            $domain->expiry_date = Carbon::now()->addYear($domain->reg_period);
            $domain->save();
        }

        $transaction = new Transaction();
        $transaction->invoice_id = $invoice->id;
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '-';
        $transaction->trx = getTrx();
        $transaction->details = "Payment";
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New order payment from '.$user->username;
        $adminNotification->click_url = urlPath('home'); 
        $adminNotification->save(); 

        $notify[] = ['success', 'Your payment was successful'];
        return back()->withNotify($notify);
    }

    private function paymentByCheckout($gate, $amount, $orderId, $user){
    
        Deposit::where('order_id', $orderId)->where('status', 0)->delete();

        $charge = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable = $amount + $charge;
        $final_amo = $payable * $gate->rate;

        $data = new Deposit();
        $data->order_id = $orderId;
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();

        $invoice = $data->order->invoice;
        $invoice->deposit_id = $data->id;
        $invoice->save();

        session()->put('Track', $data->trx);
        return redirect()->route('user.deposit.preview');
    }  
 
    private function getOptionAndSelect($product, $type, $value, $billing_type = null){
   
        foreach($product->getConfigs as $config){
            $options = $config->activeGroup->activeOptions;

            foreach($options as $option){
                $subOptions = $option->activeSubOptions;
              
                if($type == 'option'){
      
                    if(!$option->where('status', 1)->find($value)){
                        return ['success'=>false, 'message'=>'The selected option is invalid'];
                    }
         
                    return ['success'=>true, 'data'=>$option->find($value)];
                }
 
                if($type != 'option'){
                    foreach($subOptions as $subOption){
                        
                        if(!$subOption->where('status', 1)->find($value)){
                            return ['success'=>false, 'message'=>'The selected value is invalid'];
                        }
                        
                        $data = $subOption->with('getOnlyPrice')->find($value); 
                        $getPrice = pricing($product->payment_type, $data->getOnlyPrice, 'price', false, $billing_type);
                        $getSetupFee = pricing($product->payment_type, $data->getOnlyPrice, 'setupFee', false, $billing_type);
  
                        return ['success'=>true, 'data'=>$data, 'price'=>$getPrice, 'setupFee'=>$getSetupFee];
                    }
                }
            }

        }

    }  
 
    public function addCart(Request $request){ 
       
        $request->validate([
            'product_id' => 'required',
            'billing_type' => 'required|in:'.pricing(),
        ]);
  
        $product = Product::where('status', 1)->whereHas('price', function($price){
                $price->filter($price);
            }) 
            ->whereHas('serviceCategory', function($category){
                $category->where('status', 1);
            })
            ->with('getConfigs.activeGroup.activeOptions.activeSubOptions', 'price')
        ->findOrFail($request->product_id); 

        if($product->product_type == 3){
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'ns1' => 'required',
                'ns2' => 'required',
            ]);
        }

        if($request->domain_id){

            $general = GeneralSetting::first();
            $domain = DomainSetup::where('status', 1)->findOrFail($request->domain_id); 

            try{
                $url = 'https://domain-availability.whoisxmlapi.com/api/v1?'. "apiKey={$general->api_key}&domainName={$request->domain}";
                $response = Http::get($url);

                if(json_decode($response)->DomainInfo->domainAvailability != 'AVAILABLE'){
                    $notify[] = ['error', $request->domain.' is unavailable'];
                    return back()->withNotify($notify);
                } 
            }catch(\Exception $error){
                $notify[] = ['error', $error->getMessage()];
                return back()->withNotify($notify);
            }

        }
        
        if($product->stock_control && !$product->stock_quantity){
            $notify[] = ['error', 'Sorry, Out of stock'];
            return back()->withNotify($notify);
        }
                    
        $productPrice = pricing($product->payment_type, $product->price, 'price', false, $request->billing_type);
        $productSetup = pricing($product->payment_type, $product->price, 'setupFee', false, $request->billing_type);

        if($request->config_options){
            foreach($request->config_options as $option => $select){
                
                if($option){
                    $optionResponse = $this->getOptionAndSelect($product, 'option', $option);
            
                    if(!$optionResponse['success']){
                        $notify[] = ['error', $optionResponse['message']];
                        return back()->withNotify($notify);
                    } 
                } 
            
               if($select){
                    $selectResponse = $this->getOptionAndSelect($product, 'select', $select, $request->billing_type); 

                    $productPrice += @$selectResponse['price'];
                    $productSetup += @$selectResponse['setupFee'];
                   
                    if(!$selectResponse['success']){
                        $notify[] = ['error', $selectResponse['message']];
                        return back()->withNotify($notify);
                    }
               }
            }
        } 
 
        shoppingCart($product, $request, null, null, ['price'=>$productPrice, 'setupFee'=>$productSetup], $domain ?? null);

        if($request->domain_id && $request->domain){  
            return redirect()->route('user.config.domain', [$request->domain_id, $request->domain, @$domain->pricing->firstPrice['year'] ?? 0]);
        }

        return redirect()->route('user.shopping.cart');
    } 
 
    public function cart(){  

        $carts = shoppingCart('get');
        $pageTitle = 'Review & Checkout';
        $coupon = null;
 
        if(!$carts){
            $notify[] = ['info', 'Your shopping cart is empty'];
            return redirect()->route('home')->withNotify($notify);
        }

        if(session()->has('coupon')){ 
            $coupon = Coupon::find(session()->get('coupon'));
        }
        
        session()->forget('payment');
        
        return view($this->activeTemplate.'cart', compact('pageTitle', 'carts', 'coupon'));  
    }

    public function deleteCart($id = null, $type = null){  

        if($id && $type){
            shoppingCart(null, null, $id, $type);
            
            if(count(shoppingCart('get')) == 0){
                session()->forget('shoppingCart');
                session()->forget('coupon');
            }

            $notify[] = ['success', 'Removed item successfully'];
            return back()->withNotify($notify);
        }

        session()->forget('shoppingCart');
        session()->forget('coupon');
        
        $notify[] = ['success', 'Removed all item successfully'];
        return redirect()->route('home')->withNotify($notify);
        
    }  
 
    public function coupon(Request $request){
      
        $shoppingCart = shoppingCart('get');
        $array = [];

        if(!$shoppingCart){
            $notify[] = ['info', 'Your shopping cart is empty'];
            return redirect()->route('home')->withNotify($notify);
        }

        if(session()->has('coupon')){ 
            session()->forget('coupon');

            foreach($shoppingCart as $cart){
                @$cart['discount'] = 0;
                @$cart['afterDiscount'] = @$cart['total'];
                $array[] = $cart;
            }

            session()->put('shoppingCart', $array);

            $notify[] = ['success', 'Your order total has been updated'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'coupon_code' => 'required|exists:coupons,code'
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)->where('status', 1)->first();

        foreach($shoppingCart as $cart){
            
            $discount = 0;
       
            if($coupon && @$cart['total'] >= $coupon->min_order_amount){ 
                $discount = $coupon->discount(@$cart['total']);
            }

            @$cart['discount'] = $discount;
            @$cart['afterDiscount'] = (@$cart['afterDiscount'] - $discount);
            $array[] = $cart;
        }

        session()->put('coupon', $coupon->id);
        session()->put('shoppingCart', $array);
        
        $notify[] = ['success', 'Coupon code accepted, Your order total has been updated'];
        return back()->withNotify($notify);
    }

    public function payment(Request $request){
        
        $request->validate([
            'payment' => 'required',
        ]);
       
        $user = auth()->user();
        $invoice = Invoice::where('user_id', $user->id)->where('id', $request->invoice_id)->where('status', 0)->firstOrFail();
        $order = $invoice->order;
        $amount = $invoice->amount;

        if($request->payment == 'wallet'){
            return $this->paymentByWallet($invoice, $order, $user);
        }
        
        $user = auth()->user();
        $invoice = Invoice::where('user_id', $user->id)->where('id', $request->invoice_id)->firstOrFail();
        $order = $invoice->order;
        $amount = $invoice->amount;

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        return $this->paymentByCheckout($gate, $amount, $order->id, $user);
    }

    public function myServices(){

        $pageTitle = 'My Services';
        $user = auth()->user();

        $emptyMessage = 'No data found'; 
        $services = Hosting::whereBelongsTo($user)
                    ->where('status', 1)
                    ->latest()
                    ->with('product.serviceCategory')
                    ->paginate(getPaginate());
  
        return view($this->activeTemplate . 'user.service.list', compact('pageTitle', 'services', 'emptyMessage'));
    }

    public function serviceDetails($id){

        $pageTitle = 'Service Details';
        $user = auth()->user();
        $service = Hosting::whereBelongsTo($user)->with('hostingConfigs.select', 'hostingConfigs.option')->findOrFail($id);

        return view($this->activeTemplate . 'user.service.details', compact('pageTitle', 'service'));
    }

    public function createInvoice(Request $request){
    
        if(!shoppingCart()){
            $notify[] = ['info', 'Your shopping cart is empty'];
            return redirect()->route('home')->withNotify($notify);
        }
    
        $shoppingCart = shoppingCart('get');
        $user = Auth::user();
 
        $productsId = array_unique(array_column($shoppingCart, 'product_id'));
        $products = Product::whereIn('id', $productsId)
                            ->where('status', 1)
                            ->get();

        $domainsId = array_unique(array_column($shoppingCart, 'domain_id'));
        $domainSetups = DomainSetup::whereIn('id', $domainsId)
                            ->where('status', 1)
                            ->get();
        $totalPrice = 0;
        $allDiscount = 0;
        $coupon = null;

        $data = [];

        foreach($shoppingCart as $cart){
            
            if($cart['product_id']){

                $product = $products->find($cart['product_id']);

                if(!$product){
                    session()->forget('shoppingCart');
                    session()->forget('coupon');

                    $notify[] = ['error', 'Sorry, Something went wrong. Please try again'];
                    return redirect()->route('home')->withNotify($notify); 
                }

                if($product->stock_control && !$product->stock_quantity){
                    session()->forget('shoppingCart');
                    session()->forget('coupon');

                    $notify[] = ['error', 'Sorry, Out of stock'];
                    return redirect()->route('home')->withNotify($notify);
                }

                $productPrice = pricing($product->payment_type, $product->price, 'price', false, $cart['billing_type']);
                $productSetup =  pricing($product->payment_type, $product->price, 'setupFee', false, $cart['billing_type']);  
                $productTotal = ($productPrice + $productSetup);
    
                $totalPrice += $productTotal;       
                
                $append = [ 
                    'product_id'=>$product->id, 
                    'domain'=>@$cart['domain'], 
                    'username'=>@$cart['username'], 
                    'password'=>@$cart['password'], 
                    'ns1'=>@$cart['ns1'], 
                    'ns2'=>@$cart['ns2'], 
                    'server_id'=>$product->server_id, 
                    'first_payment_amount'=>$productTotal,
                    'amount'=>$productPrice,
                    'setup_fee'=>@$cart['setupFee'],
                    'discount'=>@$cart['discount'],
                    'billing_cycle'=>billing($cart['billing_type']),
                    'next_due_date'=>$product->payment_type == 1 ? null : @billing(@$cart['billing_type'], true)['carbon'], 
                    'next_invoice_date'=>$product->payment_type == 1 ? null : @billing(@$cart['billing_type'], true)['carbon'],
                    'stock_control'=>$product->stock_control,
                    'billing'=> $product->payment_type == 1 ? 1 : 2,
                    'config_options'=> null
                ];
                
                if($cart['config_options']){
     
                    foreach($cart['config_options'] as $option => $select){   
                        
                        if($option){
                            $optionResponse = $this->getOptionAndSelect($product, 'option', $option);
                    
                            if(!@$optionResponse['success']){
                                session()->forget('shoppingCart');
                                session()->forget('coupon');
    
                                $notify[] = ['error', 'Sorry, Something went wrong. Please try again'];
                                return redirect()->route('home')->withNotify($notify); 
                            } 
                        } 
                        
                        if($select){
                            $selectResponse = $this->getOptionAndSelect($product, 'select', $select, $cart['billing_type']); 
    
                            if(@$selectResponse['success']){ 
                                $sum = (@$selectResponse['price'] + @$selectResponse['setupFee']);
                                $totalPrice += $sum;
                                $productTotal += $sum;
        
                                $append['first_payment_amount'] = $productTotal;
                                $append['amount'] += @$selectResponse['price'];
    
                                $append['config_options'] = $cart['config_options'];
                            }else{
                                session()->forget('shoppingCart');
                                session()->forget('coupon');
    
                                $notify[] = ['error', 'Sorry, Something went wrong. Please try again'];
                                return redirect()->route('home')->withNotify($notify); 
                            }
                        }
                    }
    
                }

            }else{
                $domain = $domainSetups->find($cart['domain_id']);
         
                if(!$domain){
                    session()->forget('shoppingCart');
                    session()->forget('coupon');

                    $notify[] = ['error', 'Sorry, Something went wrong. Please try again'];
                    return redirect()->route('home')->withNotify($notify); 
                }
                
                $domainPrice = @$domain->pricing->singlePrice(@$cart['reg_period']) ?? 0;
                $domainIdProtection = $cart['id_protection'] != 0 ? $domain->pricing->singlePrice(@$cart['reg_period'], true) : 0;  
                $productTotal = ($domainPrice + $domainIdProtection);

                $totalPrice += $productTotal;       
              
                $append = [  
                    'product_id'=>0, 
                    'domain'=>@$cart['domain'], 
                    'first_payment_amount'=>$productTotal,
                    'amount'=>$productTotal,
                    'setup_fee'=>@$cart['setupFee'],
                    'discount'=>@$cart['discount'],
                    'reg_period'=>@$cart['reg_period'],
                    'id_protection'=>@$cart['id_protection'] ? 1 : 0,
                    'next_due_date'=>Carbon::now()->addYear(@$cart['reg_period']),
                    'next_invoice_date'=>Carbon::now()->addYear(@$cart['reg_period'])
                ];
            }  

            if(session()->has('coupon')){  
                $discount = 0;

                $coupon = Coupon::where('id', session()->get('coupon'))->where('status', 1)->first();  
    
                if($coupon && $productTotal >= $coupon->min_order_amount){
                    $discount = $coupon->discount($productTotal);
                }
    
                $productTotal = (($productTotal) - $discount);
                $append['first_payment_amount'] = $productTotal;
                $allDiscount += $discount;
            }

            $data[] = $append;
        }

        $totalPrice -= $allDiscount;     

        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->amount = $totalPrice;
        $invoice->status = 0;
        $invoice->save(); 

        $order = new Order();
        $order->user_id = $user->id;
        $order->invoice_id = $invoice->id;
        $order->coupon_id = $coupon ? $coupon->id : 0;
        $order->amount = $totalPrice;
        $order->discount = $allDiscount; 
        $order->ip_address = $_SERVER["REMOTE_ADDR"];
        $order->status = 0; 
        $order->save();
       
        $data = array_map(function($data) use ($user, $order){
            return $data + [
                'user_id' => $user->id,
                'order_id' => $order->id
            ];
        }, $data);
     
        foreach($data as $singleData){  
         
            if($singleData['product_id'] != 0){
                $hosting = new Hosting();
                $hosting->product_id = $singleData['product_id'];
                $hosting->domain = $singleData['domain'];
                $hosting->server_id = $singleData['server_id'];
                $hosting->first_payment_amount = $singleData['first_payment_amount'];
                $hosting->amount = $singleData['amount'];
                $hosting->discount = $singleData['discount'];
                $hosting->setup_fee = $singleData['setup_fee'];
                $hosting->billing_cycle = $singleData['billing_cycle'];
                $hosting->next_due_date = $singleData['next_due_date'];
                $hosting->next_invoice_date = $singleData['next_invoice_date'];
                $hosting->stock_control = $singleData['stock_control'];
                $hosting->billing = $singleData['billing'];
                $hosting->config_options = $singleData['config_options'];
                $hosting->user_id = $singleData['user_id'];
                $hosting->order_id = $singleData['order_id'];
                $hosting->username = $singleData['username'];
                $hosting->password = $singleData['password'];
                $hosting->ns1 = $singleData['ns1'];
                $hosting->ns2 = $singleData['ns2'];
                $hosting->status = 0;
                $hosting->save();

                $this->makeHostingConfigs($hosting);
            }
            else{
                $domain = new Domain();
                $domain->user_id = $singleData['user_id'];
                $domain->order_id = $singleData['order_id'];
                $domain->coupon_id = $coupon ? $coupon->id : 0;
                $domain->domain = $singleData['domain'];
                $domain->id_protection = $singleData['id_protection'];
                $domain->first_payment_amount = $singleData['first_payment_amount'];
                $domain->recurring_amount = $singleData['amount'];
                $domain->discount = $singleData['discount'];
                $domain->reg_period = $singleData['reg_period'];
                $domain->next_due_date = $singleData['next_due_date'];
                $domain->next_invoice_date = $singleData['next_invoice_date'];
                $domain->save();
            }


        }

        $this->makeInvoiceItems($invoice);

        session()->forget('shoppingCart');
        session()->forget('coupon');

        return redirect()->route('user.view.invoice', $invoice->id);
    }

    public function viewInvoice($id){

        $user = auth()->user();
        $pageTitle = 'Invoice';

        $invoice = Invoice::where('id', $id)->whereBelongsTo($user)
                          ->with('order.hostings.product.serviceCategory', 'order.hostings.hostingConfigs.select', 'order.hostings.hostingConfigs.option')
                          ->firstOrFail();

        $order = $invoice->order;
        $hostings = $order->hostings; 

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate){
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        
        $address = Frontend::where('data_keys','invoice_address.content')->first();

        return view($this->activeTemplate.'user.invoice.view', compact('pageTitle', 'user', 'invoice', 'gatewayCurrency', 'address', 'hostings', 'order'));
    } 

    protected function makeHostingConfigs($hosting){
        $array = [];  
       
        if($hosting->config_options){ 
            $data =  (array) $hosting->config_options;
     
            foreach($data as $optionId => $subOptionId){
                $array[] = [
                    'hosting_id' => $hosting->id,
                    'configurable_group_option_id' => $optionId,                            
                    'configurable_group_sub_option_id' => $subOptionId,                            
                ];
            }
            
            HostingConfig::insert($array);
        }  
    }

    protected function makeInvoiceItems($invoice){
     
        $order = $invoice->order;
        $hostings = $order->hostings;
        $domains = $order->domains;

        foreach($hostings as $hosting){
            $product = $hosting->product;
            
            if($hosting->setup_fee != 0){
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->user_id = $invoice->user_id;
                $item->relation_id = $hosting->id;
                $item->type = 1;
                $item->description = $product->name.' '.'Setup Fee'."\n".$product->serviceCategory->name;
                $item->amount = $hosting->setup_fee;
                $item->save();
            }
          
            $domainText = $hosting->domain ? ' - ' .$hosting->domain : null;
            $date = $hosting->billing == 2 ? ' ('.showDateTime($hosting->created_at, 'd/m/Y').' - '.showDateTime($hosting->next_due_date, 'd/m/Y') .')' : ' (One Time)';
            $text = $product->name . $domainText. $date ."\n".$product->serviceCategory->name;
       
            foreach($hosting->hostingConfigs as $config){
                $text = $text ."\n". $config->select->name.': '.$config->option->name;
            }

            $item = new InvoiceItem(); 
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->relation_id = $hosting->id;
            $item->type = 2; 
            $item->description = $text;
            $item->amount = $hosting->amount;
            $item->save();
           
            if($hosting->discount != 0){
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->user_id = $invoice->user_id;
                $item->relation_id = $hosting->id;
                $item->type = 3;
                $item->description = 'Coupon Code: '.@$order->coupon->code.' '.$product->serviceCategory->name;
                $item->amount = $hosting->discount;
                $item->save();
            }
        }
   
        foreach($domains as $domain){
            
            $domainText = ' - '. $domain->domain .' - '. $domain->reg_period . ' Year/s';
            $protection = $domain->id_protection ? '+ ID Protection' : null;
            $text = 'Domain Registration' . $domainText. ' ('.showDateTime($domain->created_at, 'd/m/Y').' - '.showDateTime($domain->next_due_date, 'd/m/Y') .')'."\n".$protection;
     
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->relation_id = $domain->id;
            $item->type = 4;
            $item->description = $text;
            $item->amount = $domain->recurring_amount;
            $item->save();

            if($domain->discount != 0){
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->user_id = $invoice->user_id;
                $item->relation_id = $domain->id;
                $item->type = 3;
                $item->description = 'Coupon Code: '.@$order->coupon->code.' for domain Registration';
                $item->amount = $domain->discount;
                $item->save();
            }
        }

    }
    
    public function myInvoices(){
        $pageTitle = 'My Invoices';
        $user = auth()->user();
        $invoices = Invoice::whereBelongsTo($user)->latest()->paginate(getPaginate());
        $emptyMessage = 'No Data Found';
        return view($this->activeTemplate.'user.invoice.list', compact('pageTitle', 'invoices', 'emptyMessage'));
    }

    public function invoiceDownload($id, $view = null){
        $invoice = Invoice::where('user_id', auth()->user()->id)->findOrFail($id);
        $address = Frontend::where('data_keys','invoice_address.content')->first();
        $user = $invoice->user;
        $pageTitle = 'Invoice';

        $pdf = PDF::loadView('invoice', compact('pageTitle', 'invoice', 'user', 'address'));

        if($view){
            return $pdf->stream('invoice.pdf');
        }

        return $pdf->download('invoice.pdf');
    } 
 
    public function deleteDomainCart($id, $domain){
        $cart = shoppingCart('get');

        foreach($cart as $arrayIndex => $singleCart){
            if($singleCart['domain_id'] == $id && $singleCart['domain'] == $domain && $singleCart['product_id'] == 0){
                unset($cart[$arrayIndex]);
            }

            if($singleCart['domain_id'] == $id && $singleCart['domain'] == $domain && $singleCart['product_id'] != 0){
                $cart[$arrayIndex]['domain_id'] = 0;
            }
        }

        $cart = array_reverse($cart); 
        session()->put('shoppingCart', $cart);

        session()->forget('coupon');

        $notify[] = ['success', 'Removed item successfully'];
        return back()->withNotify($notify);
    }

    public function configDomain($id, $domain, $regPeriod){

        $domainSetup = DomainSetup::findOrFail($id);
   
        if(empty($domainSetup->pricing->firstPrice)){
            return redirect()->route('user.shopping.cart');
        }

        $pageTitle = 'Domains Configuration';
        return view($this->activeTemplate.'domain_config', compact('pageTitle', 'regPeriod', 'id', 'domain', 'domainSetup'));
    }

    public function configDomainUpdate(Request $request){
        
        $request->validate([
            'id'=>'required',
            'reg_period'=>'required|between:1,6',
            'id_protection'=>'nullable|between:1,6',
        ]);

        $domainSetup = DomainSetup::findOrFail($request->id);
        $domainSetup->pricing->singlePrice($request->reg_period);

        $domainPrice = @$domainSetup->pricing->singlePrice($request->reg_period) ?? 0;
        $idProtectionPrice = @$domainSetup->pricing->singlePrice($request->id_protection, true) ?? 0;

        $shoppingCart = shoppingCart('get');
        $array = [];

        foreach($shoppingCart as $cart){ 
      
            if($cart['domain_id'] == $request->id && $cart['product_id'] == 0 && $cart['domain'] == $request->domain){

                @$cart['reg_period'] = getAmount($request->reg_period);
                @$cart['id_protection'] = getAmount($request->id_protection ?? 0);

                @$cart['price'] = getAmount($domainPrice);
                @$cart['setupFee'] = getAmount($idProtectionPrice);
                @$cart['total'] = getAmount($domainPrice + $idProtectionPrice);
                
                @$cart['discount'] = 0;
                @$cart['afterDiscount'] = getAmount(@$cart['total']);
               
                $array[] = @$cart;
            }else{
                $array[] = @$cart; 
            }

        }

        session()->forget('coupon');
        session()->put('shoppingCart', $array);

        $notify[] = ['success', 'Domains configuration updated successfully'];
        return redirect()->route('user.shopping.cart')->withNotify($notify);
    }

    public function myDomains(){
        $pageTitle = 'My Domains';
        $user = auth()->user();
        $domains = Domain::whereBelongsTo($user)->where('status', '!=', 0)->latest()->paginate(getPaginate());
        $emptyMessage = 'No Data Found';
        return view($this->activeTemplate.'user.domain.list', compact('pageTitle', 'domains', 'emptyMessage'));
    }

    public function domainDetails($id){
        $pageTitle = 'Domain Details';
        $user = auth()->user();
        $domain = Domain::whereBelongsTo($user)->where('status', '!=', 0)->where('id', $id)->firstOrFail();
        return view($this->activeTemplate.'user.domain.details', compact('pageTitle', 'domain'));
    }

    public function domainNameserverUpdate(Request $request){
        return $request;
    }

}
