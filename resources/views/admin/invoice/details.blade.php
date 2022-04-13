@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-20 text-muted">@lang('Invoice Status')</h5>
                        <h5>@php echo @$invoice->statusText; @endphp</h5> 
                    </div>   
                    <ul class="list-group"> 
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Invoice Date')
                            <span class="font-weight-bold">{{ showDateTime(@$invoice->created_at, 'd/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Paid Date')
                            <span class="font-weight-bold">
                                @if(@$invoice->paid_date)
                                    {{ showDateTime(@$invoice->paid_date, 'd/m/Y') }}
                                @else 
                                    @lang('N/A')   
                                @endif
                            </span> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('User') 
                            <span class="font-weight-bold"> 
                                <a href="{{ route('admin.users.detail', @$invoice->user_id) }}">{{ @$invoice->user->fullname }}</a>
                            </span>   
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Payment Method')
                            <span class="font-weight-bold">
                                @if(@$invoice->payment)
                                    <a href="{{ route('admin.deposit.details', @$invoice->payment->id) }}">{{ __(@$invoice->payment->gateway->name) }}</a>
                                @elseif($invoice->status == 1)
                                    @lang('Wallet Balance')
                                @else 
                                    @lang('N/A')
                                @endif 
                            </span>
                        </li> 
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="font-weight-bold">
                                <a href="{{ route('admin.order.details', $invoice->order->id) }}">
                                    {{ $general->cur_sym }}{{ showAmount(@$invoice->amount) }}
                                </a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Coupon Code')
                            <span class="font-weight-bold">{{ @$invoice->order->coupon->code ?? __('N/A') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @php
            $items = @$invoice->items;
        @endphp

        <div class="col-xl-8 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--"> 
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Invoice Items')</h5>
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Description')</th>
                                <th>@lang('Amount')</th>
                            </tr>
                            </thead>
                            <tbody> 
                            @forelse($items as $item)
                                <tr>
                                    <td data-label="@lang('Description')">
                                        @php echo nl2br($item->description); @endphp
                                    </td>
                                    <td data-label="@lang('Amount')">
                                        {{ @$general->cur_sym }}{{ showAmount(@$item->amount) }}
                                    </td>
                                </tr>
                            @empty  
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No data found')</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div>  
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.invoice.download', $invoice->id) }}" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-download"></i>@lang('Download')
</a>
<a href="{{ route('admin.invoice.download', ['id'=>$invoice->id, 'view'=>'preview']) }}" target="_blank" class="btn btn-sm btn--success box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-eye"></i>@lang('Preview')
</a>
@endpush