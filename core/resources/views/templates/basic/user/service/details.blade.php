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
                                        @php 
                                            if($product->product_type == 3){
                                                $icon = 'server'; 
                                            }elseif($product->product_type == 4){
                                                $icon = 'archive';
                                            }else{
                                                $icon = 'hdd'; 
                                            }
                                        @endphp
                                        <i class="fas fa-{{ $icon }} fa-stack-1x fa-inverse"></i>
                                    </span>
                                    <h3 class="text-center">{{ __(@$service->product->name) }}</h3>
                                    <h4 class="text-center">{{ __(@$service->product->serviceCategory->name) }}</h4>
                                    <span class="text-center d-block">
                                        @php echo $service->showDomainStatus; @endphp
                                    </span>
                                </div>

                                @if($service->domain_status == 1)
                                    <button class="btn btn-danger btn-sm w-100 mt-2 {{ $service->cancelRequest ? 'disabled' : 'cancenRequest' }}">  
                                        @lang('Request Cancellation') 
                                    </button> 
                                @endif

                                @if($service->cancelRequest && $service->cancelRequest->status == 2)
                                    <small class="text-center w-100 d-block mt-2 text-danger">
                                        @lang('There is an outstanding cancellation request for this product/service')
                                    </small>
                                @endif
                            </div> 
                        @else 

                        @php
                            $status = $service->domain_status;
                        @endphp

                            @if($status == 1)
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
                                        <h3 class="text-center mb-3">@lang('Disk Usage')</h3>
                                        <div class="row"> 
                                            <div class="col-lg-12 form-group">
                                                <div class="progress custom--progress progress-bg">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ (int) $diskUsed / (int) $diskLimit * 100 }}%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    <div class="progress-text text-white">
                                                        {{ getAmount((int) $diskUsed / (int) $diskLimit * 100) }}%
                                                    </div>
                                                    </div>
                                                <small>{{ $diskUsed }} / {{ $diskLimit }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($status == 0)
                                <div class="col-md-5">
                                    <div class="new-card bg-warning">
                                        <h3 class="mb-3">@lang('Pending')</h3>
                                        <small class="d-block">@lang('This hosting package is currently Pending')</small>
                                        <small>@lang('You cannot begin using this hosting account until it is activated')</small>
                                    </div>
                                </div>
                            @elseif($status == 2)
                                <div class="col-md-5">
                                    <div class="new-card bg-warning">
                                        <h3 class="mb-3">@lang('Suspended')</h3>
                                        <small class="d-block">@lang('This hosting package is currently Suspended')</small>
                                        <small>@lang('You cannot continue to use or manage this package until it is reactivated')</small>
                                    </div>
                                </div>
                            @elseif($status == 3)
                                <div class="col-md-5">
                                    <div class="new-card bg-warning">
                                        <h3 class="mb-3">@lang('Terminated')</h3>
                                        <small>@lang('This hosting package is currently Terminated')</small>
                                    </div>
                                </div>
                            @elseif($status == 4)
                                <div class="col-md-5">
                                    <div class="new-card bg-warning">
                                        <h3 class="mb-3">@lang('Cancelled')</h3>
                                        <small>@lang('This hosting package is currently Cancelled')</small>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="col-md-7 text-center">
                            <h4>@lang('Registration Date')</h4>
                            {{ @$service->reg_time ? showDateTime(@$service->reg_time, 'd/m/Y') : 'N/A' }}

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
                                {{ billingCycle(@$service->billing_cycle, true)['showText'] }}
                            @endif

                            <h4>@lang('Next Due Date')</h4>
                            @if($service->billing == 1)
                                @lang('N/A')
                            @else 
                                {{ @$service->next_due_date ? showDateTime(@$service->next_due_date, 'd/m/Y') : 'N/A' }}
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

<div id="cancenRequest" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Briefly Describe your reason for Cancellation')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.service.cancel.request') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $service->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="cancellation_type">@lang('Cancellation Type')</label>
                            <select name="cancellation_type" class="form-control" required>
                                <option value="">@lang('Select One')</option>
                                @foreach(App\Models\CancelRequest::type() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="reason">@lang('Reason')</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-success">@lang('Submit')</button>
                </div>
            </form>
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
    .custom--progress {
        position: relative;
    }
    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        line-height: 0;
        font-size: .75rem;
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

            $('.cancenRequest').on('click', function(){
                var modal = $('#cancenRequest');
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush 