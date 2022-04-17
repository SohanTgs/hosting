@extends(@$activeTemplate.'layouts.frontend')
@section('content')

<div class="container mt-5">
    <div class="row">

        @include(@$activeTemplate.'partials.sidenav')

        <div class="col-md-5 mt- mt-md-0"> 
            <div class="row"> 
                <div class="col-md-12"><h1>{{ __($pageTitle) }}</h1></div>
       
                @foreach($carts as $cart)  
                    <div class="col-md-12 cart_child"> 
                        <div class="card fz-12"> 
                            <div class="card-body">  
                                @if(@$cart['product_id'])
                                    <div class="row">
                                        <div class="col-md-11 d-flex justify-content-between">
                                            <div>
                                                <h6 class="d-inline">{{ @$cart['name'] }}</h6> 
                                                <a href="{{ route('product.configure', @$cart['product_id']) }}?id={{ @$cart['product_id'] }}&billing_type={{ @$cart['billing_type'] }}">
                                                    <i class="fa fa-pencil"></i> @lang('Edit')
                                                </a>
                                                <span class="d-block">{{ @$cart['category'] }}</span>
                                                <span class="d-block font-weight-bold">{{ @$cart['domain'] }}</span>
                                                <i>@lang('Total') {{ @$general->cur_sym }}{{ @$cart['total'] }} {{ __($general->cur_text) }}</i>
                                            </div>  
                                            <div>   
                                                <h6 class="d-inline">{{ $general->cur_sym }}{{ @$cart['price'] }} {{ __($general->cur_text) }}</h6> 
                                                <span class="d-block">
                                                    @if(@$cart['billing'] == 1) 
                                                        @lang('One Time')
                                                    @else 
                                                        @php
                                                            $replace = str_replace('_', ' ', @$cart['billing_type']);
                                                            echo ucwords($replace);
                                                        @endphp
                                                    @endif
                                                </span> 
                                                <span class="d-block">{{ $general->cur_sym }}{{ @$cart['setupFee'] }} @lang('Setup Fee')</span>
                                            </div>
                                        </div> 
                                        <div class="col-md-1 form-group">
                                            @php
                                                $product_id = @$cart['product_id'];
                                            @endphp
                                            <a class="remove_cart d-none" href="{{ route('user.shopping.cart.delete', [@$product_id, $cart['billing_type']]) }}">
                                                <i class="fa fa-trash">&nbsp;@lang('Remove')</i>
                                            </a>
                                            <a href="{{ route('user.shopping.cart.delete', [@$product_id, $cart['billing_type']]) }}" class="remove_icon">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                @else  
                                    <div class="row">
                                        <div class="col-md-11 d-flex justify-content-between">
                                            <div>  
                                                <h6 class="d-inline">{{ @$cart['name'] }}</h6> 
                                                <a href="{{ route('user.config.domain', [@$cart['domain_id'], @$cart['domain'], @$cart['reg_period']]) }}?protection={{ @$cart['id_protection'] }}">
                                                    <i class="fa fa-pencil"></i> @lang('Edit')
                                                </a>
                                                <span class="d-block font-weight-bold">
                                                    {{ @$cart['domain'] }} - {{ @$cart['reg_period'] }} @lang('Year')
                                                    {{ @$cart['id_protection'] ? __('with ID Protection') : null }}
                                                </span>
                                                <i>@lang('Total') {{ @$general->cur_sym }}{{ @$cart['total'] }} {{ __($general->cur_text) }}</i>
                                            </div>  
                                            <div>    
                                                <h6 class="d-inline">{{ $general->cur_sym }}{{ @$cart['price'] }} {{ __($general->cur_text) }}</h6> 
                                                @if(@$cart['id_protection'])
                                                    <span class="d-block">{{ $general->cur_sym }}{{ @$cart['setupFee'] }} @lang('ID Protection')</span>
                                                @endif
                                            </div>
                                        </div> 
                                        <div class="col-md-1 form-group">
                                            <a class="remove_cart d-none" href="{{ route('user.shopping.cart.delete.domain', [@$cart['domain_id'], @$cart['domain']]) }}">
                                                <i class="fa fa-trash">&nbsp;@lang('Remove')</i>
                                            </a>
                                            <a href="{{ route('user.shopping.cart.delete.domain', [@$cart['domain_id'], @$cart['domain']]) }}" class="remove_icon">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($carts)
                    <div class="col-md-12 justify-content-end w-100 d-flex">
                        <span class="bg-danger text-white p-1 rounded fz-12">
                            <a href="{{ route('user.shopping.cart.delete') }}" class="text-white"><i class="fa fa-trash"></i> @lang('Empty Cart')</a>
                        </span>
                    </div>
                @endif

                <div class="col-md-12 mt-3">
                    <form action="{{ route('user.coupon') }}" method="post">
                        @csrf 
                        <div class="form-group">
                            @if(session()->has('coupon'))
                                <p class="border p-2 text-center sessionMessage"></p>
                            @else 
                                <input type="text" class="form-control" name="coupon_code" placeholder="@lang('Enter coupon code if you have one')" required>
                            @endif
                        </div>
                        @if(session()->has('coupon'))
                            <div class="form-group">
                                <button type="submit" class="btn btn-warning w-100 text-white">@lang('Remove Coupon Code')</button>
                            </div>
                        @else 
                            <div class="form-group">
                                <button type="submit" class="btn btn-success w-100">@lang('Validate Code')</button>
                            </div>
                        @endif
                    </form>
                </div>

            </div>
        </div>  

        <div class="col-md-4">
            <div class="card">
                <div class="card-header order--bg text-center">@lang('Order Summary')</div>
                <div class="card-body pb-2">
                    <div>
                        <div class="d-flex justify-content-between mt-3">
                            <span>@lang('Subtotal')</span>
                            <span>{{ @$general->cur_sym }}<span class="basicPrice"></span> {{ __(@$general->cur_text) }}</span>
                        </div>

                    </div>
                    <div class="border-top mt-3 couponArea d-none">
                        <div class="d-flex justify-content-between">
                            <span class="discount"></span>
                            <span>{{ @$general->cur_sym }}<span class="discountAmount"></span> {{ __(@$general->cur_text) }}</span>
                        </div>
                    </div>
                    <h4 class="justify-content-end d-flex mt-2 border-top">
                        {{ @$general->cur_sym }}<span class="finalAmount"></span>
                        {{ __(@$general->cur_text) }}
                    </h4>
                </div>  
            </div> 
            <div class="text-center mt-3">  
                <form action="{{ route('user.create.invoice') }}" method="post">
                    @csrf
                    <button type="submit" class="btn bg-info btn-lg text-white" data-toggle="modal" data-target="#paymentModal">
                        @lang('Checkout') <i class="fa fa-arrow-circle-right"></i>
                    </button>
                </form>
            </div>
        </div> 

    </div>
</div>
 
@endsection
 
@push('style')
<style>
    .order--bg{
        background: #666;
        color: #fff;
    }
    .allOption{
        height: 0;
        border-top: 1px solid #ddd;
        text-align: center;  
        margin-top: 20px;
        margin-bottom: 30px;
    } 
    .allOption div{
        display: inline-block;
        position: relative;
        padding: 0 17px;
        top: -13px;
        font-size: 16px;
        color: #058;
        background: white;
    }
    .capitalize{
        text-transform: capitalize;
    }
    .cart_child:nth-child(odd) .card-body{
        background-color: #00000008;
    }
    @media only screen and (max-width: 1199px) {
        .remove_cart{
            display:inline-block !important;
        }
        .remove_icon {
            display: none;
        }
    }
</style>
@endpush 


@push('script')
<script>
    (function ($) {
        "use strict";

        var price =  @json( array_sum(array_column($carts, 'total')) ); 
        var allDiscount =  @json( array_sum(array_column($carts, 'discount')) ); 

        var couponArea = $('.couponArea');
        var sessionMessageElement = $('.sessionMessage');
        var discountElement = $('.discount');
        var discountAmountElement = $('.discountAmount');
        var basicPrice = $('.basicPrice');
        var discountAmount = $('.discountAmount');
        var finalAmount = $('.finalAmount');
        var coupon = @json($coupon ? true : false);
       
        basicPrice.text(price.toFixed(2));
        
        if(coupon){
            var general = @json($general);
            var data = @json($coupon);
            var type = data.type; 
            var discountAmount = data.discount;
            var message = '';
            var discount = 0;
            var sessionMessage = '';

            if(type == 0){ 
                message = `Get ${parseFloat(allDiscount).toFixed(2)}% Discount`;
                sessionMessage = `${data.code} - ${parseFloat(allDiscount).toFixed(2)}% Discount`;
            }else{
                message = `Get ${parseFloat(allDiscount).toFixed(2)} ${general.cur_text} Discount`;
                sessionMessage = `${data.code} - ${parseFloat(allDiscount).toFixed(2)} ${general.cur_text} Discount`;
            }

            discount = parseFloat(discount).toFixed(2);
            price -= allDiscount;
            price = price.toFixed(2);

            couponArea.removeClass('d-none');
            discountElement.text(message);
            discountAmountElement.text(allDiscount);
            sessionMessageElement.text(sessionMessage);
        }

        finalAmount.text(parseFloat(price).toFixed(2));

    })(jQuery);
</script>
@endpush

