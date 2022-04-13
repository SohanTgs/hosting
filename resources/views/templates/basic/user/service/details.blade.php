@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">

            <div class="col-md-10">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="row">
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
    
                @if(count($service->hostingConfigs))
                    <h3 class="mt-4 text-center">@lang('Configurable Options')</h3>

                    <div class="card w-100 mt-4">
                        <div class="card-body">  
                            <ol>
                                @foreach($service->hostingConfigs as $config)
                                    <li class="mt-1"><b>{{ __($config->select->name) }}</b> - {{ __($config->option->name) }}</li>
                                @endforeach
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
</style>
@endpush