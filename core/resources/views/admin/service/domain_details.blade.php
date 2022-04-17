@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30 mb-2">
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Registration Date')
                        <span class="font-weight-bold">{{ showDateTime(@$domain->created_at, 'd/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Domain')  
                        <span class="font-weight-bold"><a href="http://{{ @$domain->domain }}" target="_blank">{{ @$domain->domain }}</a></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Expiry Date')
                        @if($domain->expiry_date) 
                            <span class="font-weight-bold">{{ showDateTime(@$domain->expiry_date, 'd/m/Y') }}</span>
                        @else 
                            <span class="font-weight-bold">@lang('N/A')</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Next Due Date')
                        <span class="font-weight-bold">{{ showDateTime(@$domain->next_due_date, 'd/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Status')
                        <span class="font-weight-bold">@php echo @$domain->showStatus; @endphp</span>
                    </li>
                </ul> 
            </div>
        </div> 
    </div>  
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('First Payment Amount') 
                        <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(@$domain->first_payment_amount) }}</span>
                    </li> 
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Discount')
                        <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(@$domain->discount) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Recurring Amount')
                        @if($domain->billing == 1)
                            <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(0) }}</span>
                        @else 
                            <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(@$domain->recurring_amount ) }}</span>
                        @endif 
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Billing Cycle')  
                        <span class="font-weight-bold">
                            {{ $domain->reg_period }} @lang('Year\s') 
                            @if($domain->id_protection)
                                + @lang('ID Protection')
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Payment Method') 
                        <span class="font-weight-bold">
                            @if(@$domain->deposit_id) 
                                <a href="{{ route('admin.deposit.details', $domain->deposit_id) }}">{{ __(@$domain->deposit->gateway->name) }}</a>
                            @elseif($domain->status != 0)
                                @lang('Wallet Balance')
                            @else 
                                @lang('N/A')
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
<div class="row mb-none-30">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.order.domain.update') }}" method="POST">
                    <input type="hidden" name="id" value="{{ $domain->id }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Subscription ID') </label>
                                <input class="form-control form-control-lg" type="text" name="subscription_id" value="{{$domain->subscription_id}}">
                            </div>
                        </div>
                        <div class="col-md-4">   
                            <div class="form-group"> 
                                <label class="form-control-label font-weight-bold">@lang('Status')</label>
                                <select name="status" class="form-control form-control-lg"> 
                                    @foreach($domain::status() as $index => $data) 
                                        <option value="{{ $index }}" {{ $domain->status == $index ? 'selected' : null}}>{{ $data }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('ID Protection')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="id_protection" @if($domain->id_protection) checked @endif>
                            </div> 
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins') 
<a href="{{ route('admin.order.details', $domain->order_id) }}" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-backward"></i>@lang('Go Back')
</a>
@endpush 

@push('style')
    <style>
        
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
           
        })(jQuery);
        
    </script>
@endpush

