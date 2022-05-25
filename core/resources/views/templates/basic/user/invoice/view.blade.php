@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-12">
                <div class="container-fluid invoice-container" id="invoice">

                    <div class="row invoice-header">
                        <div class="col-12 col-sm-6 justify-content-sm-between text-center text-sm-left invoice-col">
                            <h4>{{ __($general->sitename) }}</h4> 
                            <h5>@lang('Invoice') #{{ $invoice->id }}</h5> 
                        </div>
                        <div class="col-12 col-sm-6 invoice-col text-center">
                            <div class="invoice-status">
                                @php echo @$invoice->statusText; @endphp
                            </div>

                            @if($invoice->status == 2)
                                <div class="small-text">
                                    @lang('Due Date'): {{ showDateTime($invoice->created_at, 'd/m/Y') }}
                                    <div class="payment-btn-container d-print-none" align="center">
                                        <button type="button" class="btn btn-success btn-sm payBtn hide" disabled>@lang('Pay Now')</button>
                                    </div>
                                </div>
                            @endif
                        </div> 
                    </div>
                    <hr>
            
                    @if( Session::has('notify') && Session::get('notify')[0][0] ==  'success' )
                        <div class="card w-100 mb-3">
                            <div class="card-title py-1 px-2 text-white font-weight-bold bg-success">
                                @lang('Success')
                            </div>
                            <div class="card-text text-center mx-2 mb-3">
                                {{ Session::get('notify')[0][1] }}
                            </div>
                        </div>
                    @endif 

                    <div class="row justify-content-sm-between">
                        <div class="col-12 col-sm-6 order-sm-last text-sm-right invoice-col right">
                            <strong>@lang('Pay To')</strong>
                            <address class="small-text">
                                {{ __($address->data_values->address) }}
                            </address>
                        </div>
                        <div class="col-12 col-sm-6 invoice-col">
                            <strong>@lang('Invoiced To')</strong>
                            <address class="small-text">
                                {{ __($user->fullname) }}<br>
                                {{ __(@$user->address->address) }}, <br>
                                {{ __(@$user->address->city) }}, {{ __(@$user->address->state) }}, {{ __(@$user->address->zip) }}<br>
                                {{ __(@$user->address->country) }}
                            </address>
                        </div>
                    </div>
        
                    <div class="row">
                        @if($invoice->status != 1)
                            <div class="col-12 col-sm-6 order-sm-last text-sm-right invoice-col right hide">
                                <strong>@lang('Payment Method')</strong><br>
                                <span class="small-text float-sm-right" data-role="paymethod-info">
                                    <form method="post" action="{{ route('user.payment') }}" class="form-inline paymentForm">
                                        @csrf

                                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                        <input type="hidden" name="method_code">
                                        <input type="hidden" name="currency">
    
                                        <select name="payment" class="custom-select gateway">
                                            <option value="">@lang('Select One')</option>
                                            <option value="wallet">@lang('Wallet Balance') {{ $general->cur_sym }}{{ showAmount($user->balance) }}</option>
                                            @foreach($gatewayCurrency as $data)    
                                                <option value="{{$data->method_code}}" data-gateway="{{ $data }}">{{$data->name}}</option> 
                                            @endforeach
                                        </select>
                                    </form>
                                </span>
                                <br><br>
                            </div>
                        @endif

                        <div class="col-12 col-sm-6 invoice-col">
                            <strong>@lang('Invoice Date')</strong>
                            <br>
                            <span class="small-text">
                                {{ showDateTime($invoice->created_at, 'd/m/Y') }}<br><br>
                            </span>
                        </div>
                    </div>
                    <br>        
                    <div class="card bg-default">
                        <div class="card-header">
                            <h3 class="card-title mb-0 font-size-24"><strong>@lang('Invoice Items')</strong></h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <td><strong>@lang('Description')</strong></td>
                                    <td width="20%" class="text-center"><strong>@lang('Amount')</strong></td>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $item) 
                                        @if($item->type == 1)
                                            <tr>
                                                <td>@php echo nl2br($item->description); @endphp</td>
                                                <td class="text-center">{{ $general->cur_sym }}{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                                            </tr>
                                        @endif
                                        @if($item->type == 2)
                                            <tr>
                                                <td>@php echo nl2br($item->description); @endphp</td>
                                                <td class="text-center">{{ $general->cur_sym }}{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                                            </tr>
                                        @endif
                                        @if($item->type == 4)
                                            <tr>
                                                <td>@php echo nl2br($item->description); @endphp</td>
                                                <td class="text-center">{{ $general->cur_sym }}{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                                            </tr>
                                        @endif
                                        @if($item->type == 3)
                                            <tr>
                                                <td>@php echo nl2br($item->description); @endphp</td>
                                                <td class="text-center">{{ $general->cur_sym }}-{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td class="total-row text-right"><strong>@lang('Total')</strong></td>
                                        <td class="total-row text-center">{{ $general->cur_sym }}{{ showAmount($invoice->amount) }} {{ __($general->cur_text) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
        
                    <div class="transactions-container small-text mt-4"> 
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>  
                                    <tr>
                                        <td class="text-center"><strong>@lang('Transaction Date')</strong></td>
                                        <td class="text-center"><strong>@lang('Gateway')</strong></td>
                                        <td class="text-center"><strong>@lang('Transaction ID')</strong></td>
                                        <td class="text-center"><strong>@lang('Charge')</strong></td>
                                        <td class="text-center"><strong>@lang('Amount')</strong></td>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @if(@$invoice->payment)
                                        <tr>
                                            <td class="text-center">{{ showDateTime(@$invoice->payment->created_at, 'd/m/Y') }}</td>
                                            <td class="text-center">{{ __(@$invoice->payment->gateway->name) }}</td>
                                            <td class="text-center">{{ @$invoice->payment->trx }}</td>
                                            <td class="text-center">{{ showAmount(@$invoice->payment->charge) }} {{ __($general->cur_text) }}</td>
                                            <td class="text-center">{{ showAmount(@$invoice->payment->amount + @$invoice->payment->charge) }} {{ __($general->cur_text) }}</td>
                                        </tr>
                                    @elseif(@$invoice->status == 1 && !$invoice->payment)
                                        <tr>
                                            <td class="text-center">{{ showDateTime(@$invoice->trx->created_at, 'd/m/Y') }}</td>
                                            <td class="text-center">@lang('Wallet Balance')</td>
                                            <td class="text-center">{{ @$invoice->trx->trx }}</td>
                                            <td class="text-center">{{ showAmount(0) }} {{ __($general->cur_text) }}</td>
                                            <td class="text-center">{{ showAmount($invoice->amount) }} {{ __($general->cur_text) }}</td>
                                        </tr>
                                    @else 
                                        <tr>
                                            <td class="text-center" colspan="4">@lang('No Related Transactions Found')</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table> 
                        </div> 
                    </div>
        
                    <div class="float-right btn-group btn-group-sm d-print-none">
                        <a href="{{ route('user.invoice.download', ['id'=>$invoice]) }}" class="btn btn-primary">
                            <i class="fas fa-download"></i> @lang('Download')
                        </a> 
                        &nbsp;
                        <a href="{{ route('user.invoice.download', ['id'=>$invoice, 'view'=>'preview']) }}" target="_blank" class="btn btn-success">
                            <i class="fas fa-eye"></i> @lang('Preview')
                        </a>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .invoice-container {
        margin: 15px auto;
        padding: 70px;
        max-width: 850px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
    .invoice-container td.total-row {
        background-color: #f8f8f8;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    .invoice-container .invoice-status {
        margin: 20px 0 0 0;
        text-transform: uppercase;
        font-size: 24px;
        font-weight: 700;
    }
    .unpaid {
        color: #c00;
    }
</style>
@endpush

@push('script')
    <script>
        "use strict";

        (function ($) {

            $('.gateway').on('change', function(){
                var gateway = $(this).val();

                var resource = $('select[name=payment] option:selected').data('gateway');

                if(gateway == 'wallet'){
                    $('.payBtn').prop('disabled', false);
                }else if(gateway && gateway != 'wallet'){
                    $('input[name=currency]').val(resource.currency);
                    $('input[name=method_code]').val(resource.method_code);
                    $('.payBtn').prop('disabled', false);
                }else{
                    $('.payBtn').prop('disabled', true);
                }

            });

            $('.payBtn').on('click', function(){
                $('.paymentForm').submit();
            });

        })(jQuery);

    </script>
@endpush
