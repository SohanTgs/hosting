@extends($activeTemplate.'layouts.master')
@section('content')

@php
    $product = $service->product;
@endphp

    <div class="container">
        <div class="row justify-content-center mt-5">

            <div class="col-md-10">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="row">

                            @if($product->module_type == 0)
                                <div class="col-md-5">
                                    <div class="new-card">
                                        <span class="fa-stack fa-lg">
                                            <i class="fas fa-circle fa-stack-2x"></i>
                                            <i class="fas fa-hdd fa-stack-1x fa-inverse"></i>
                                        </span>
                                        <h3 class="text-center">{{ __(@$service->product->name) }}</h3>
                                        <h4 class="text-center">{{ __(@$service->product->serviceCategory->name) }}</h4>
                                        <span class="text-center d-block">
                                            @php echo $service->showDomainStatus; @endphp
                                        </span>
                                    </div>
                                </div> 
                            @else 
                                <div class="col-md-5">
                                    <div class="new-card text-center">
                                        <h3 class="mb-3">@lang('Package/Domain')</h3>
                                        <div>
                                            <em>{{ __($product->serviceCategory->name) }}</em>
                                            <h4>{{ __($product->name) }}</h4>
                                            <a href="http://{{ $service->domain }}" target="_blank">www.{{ $service->domain }}</a>
                                            <div class="d-block">
                                                <a class="btn btn-success btn-sm mt-3" href="http://{{ $service->domain }}" target="_blank">@lang('Visit Website')</a>
                                                <a class="btn btn-info btn-sm mt-3" href="{{ route('user.login.cpanel', $service->id) }}">@lang('Login to cPanel')</a>
                                                <a href="{{ session()->get('url') ?? '#' }}" class="cPanelLogin" target="_blank"></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="new-card mt-4">
                                        <h3 class="text-center mb-3">@lang('Usage Statistics')</h3>
                                        <div class="row">
                                            <div class="col-lg-6 form-group">
                                                <span class="d-block">@lang('Disk Usage')</span>
                                                <div class="progress mt-1 progress-bg">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                                                </div>
                                                <small>0 M / Unlimited M</small>
                                            </div>
                                            <div class="col-lg-6 form-group">
                                                <span class="d-block">@lang('Bandwidth Usage')</span>
                                                <div class="progress mt-1 progress-bg">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                                                </div>
                                                <small>0 M / Unlimited M</small>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            @endif

                            <div class="col-md-7 text-center">
                                <h4>@lang('Registration Date')</h4>
                                {{ showDateTime(@$service->created_at, 'd/m/Y') }}

                                <h4>@lang('First Payment Amount')</h4>
                                {{ $general->cur_sym }}{{ getAmount($service->first_payment_amount) }} {{ __($general->cur_text) }}

                                @if($service->billing != 1)
                                    <h4>@lang('Recurring Amount')</h4>
                                    {{ $general->cur_sym }}{{ getAmount($service->amount) }} {{ __($general->cur_text) }}
                                @endif

                                <h4>@lang('Billing Cycle')</h4>
                                @if($service->billing == 1)
                                    @lang('One Time')
                                @else 
                                    {{ billing(@$service->billing_cycle, true)['showText'] }}
                                @endif

                                <h4>@lang('Next Due Date')</h4>
                                @if($service->billing == 1)
                                    @lang('N/A')
                                @else 
                                    {{ showDateTime(@$service->next_due_date, 'd/m/Y') }}
                                @endif

                                <h4>@lang('Payment Method')</h4>
                                @if($service->deposit_id)
                                    {{ __(@$service->deposit->gateway->name) }}
                                @else 
                                    @lang('Wallet Balance') 
                                @endif
                            </div>
                        </div>
                    </div>
                </div> 

                @if(count($product->getConfigs))
                    <h3 class="mt-4 text-center">@lang('Configurable Options')</h3>
                    <div class="card w-100 mt-4">
                        <div class="card-body">   
                            <ol>
                                @foreach($product->getConfigs as $config)
                                    @foreach($config->group->options as $option)  
                                        <li class="mt-1">
                                            <b>{{ __(@$option->name) }}</b> 
                                            : {{ @$service->hostingConfigs->where('configurable_group_option_id', $option->id)->first()->option->name ?? __('N/A') }}
                                        </li>
                                    @endforeach
                                @endforeach
                            </ol>
                        </div>
                    </div>
                @endif

                @if($product->product_type == 3)
                    <h3 class="mt-4 text-center">@lang('Server Information')</h3>
                    <div class="card w-100 mt-4">
                        <div class="card-body">   
                            <ol>
                                <li class="mt-1"> 
                                    <b>@lang('Hostname')</b> : {{ $service->domain ?? 'N/A' }} 
                                </li>
                                <li class="mt-1"> 
                                    <b>@lang('Primary IP')</b> : {{ $service->dedicated_ip ?? 'N/A' }}
                                </li>
                                <li class="mt-1"> 
                                    <b>@lang('Nameservers')</b> : {{ $service->ns1 ?? 'N/A' }}, {{ $service->ns2 ?? 'N/A' }}
                                </li>
                                <li class="mt-1"> 
                                    <b>@lang('Assigned IPs')</b> : @php echo nl2br($service->assigned_ips); @endphp 
                                </li>
                            </ol>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .new-card {
        margin: 0;
        background-color: #efefef;
        border-radius: 10px;
        padding: 30px;
        line-height: 1em;
    }
    .fa-stack {
        display: inline-block;
        height: 2em;
        line-height: 2em;
        position: relative;
        vertical-align: middle;
        width: 2.5em;
        font-size: 50px;
        width: 100%;
        justify-content: center;
    }
    .progress-bg{
        background: #c5cace;
    }
</style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            var cpanelLoginUrl = @json(session()->get('url'));

            if(cpanelLoginUrl){
                document.querySelector('.cPanelLogin').click();
            }
        })(jQuery);
    </script>
@endpush 