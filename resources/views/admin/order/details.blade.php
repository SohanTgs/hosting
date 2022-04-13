@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-20 text-muted">@lang('Order Status')</h5>
                        <h5>@php echo @$order->statusText; @endphp</h5>
                    </div> 
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Order Date')
                            <span class="font-weight-bold">{{ showDateTime(@$order->created_at, 'd/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Order Number')
                            <span class="font-weight-bold">#{{ @$order->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('User')
                            <span class="font-weight-bold">
                                <a href="{{ route('admin.users.detail', @$order->user->id) }}">{{ @$order->user->fullname }}</a>
                            </span> 
                        </li>  
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Payment Method')
                            <span class="font-weight-bold">
                                @if(@$order->invoice->payment)
                                    <a href="{{ route('admin.deposit.details', @$order->invoice->payment->id) }}">{{ __(@$order->invoice->payment->gateway->name) }}</a>
                                @elseif(@$order->invoice->status == 1)
                                    @lang('Wallet Balance')
                                @else 
                                    @lang('N/A')
                                @endif 
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(@$order->amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Invoice')
                            <span class="font-weight-bold">
                                <a href="{{ route('admin.invoice.details', @$order->invoice->id) }}">#{{ @$order->invoice->id }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('IP Address')
                            <span class="font-weight-bold">
                                <a href="https://extreme-ip-lookup.com/{{ @$order->ip_address }}" target="_blank">{{ @$order->ip_address }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Coupon Code')
                            <span class="font-weight-bold">{{ @$order->coupon->code ?? __('N/A') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @php
            $items = $order->hostings;
        @endphp

        <div class="col-xl-8 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Order Items')</h5>
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Service')</th>
                                <th>@lang('Description')</th>
                                <th>@lang('Billing Cycle')</th>
                                <th>@lang('Amount')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td data-label="@lang('Service')">
                                        <span class="font-weight-bold">
                                            <a href="{{ route('admin.order.service.details', $item->id) }}">{{ __(@$item->product->serviceCategory->name) }}</a>
                                        </span>
                                    </td>
                                    <td data-label="@lang('Description')">
                                        {{ shortDescription(@$item->product->serviceCategory->short_description, 30) }}
                                    </td>
                                    <td data-label="@lang('Billing Cycle')">
                                        @if($item->billing == 1)
                                            @lang('One Time')
                                        @else 
                                            {{ billing(@$item->billing_cycle, true)['showText'] }}
                                        @endif
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

{{-- Accept Modal --}}
<div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Confirmation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.order.accept') }}">
                @csrf 
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        @lang('Are you sure to want to accept this order')?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div> 
            </form> 
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Confirmation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.order.cancel') }}">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        @lang('Are you sure to want to cancel this order')?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if($order->status == 2)
    @push('breadcrumb-plugins') 
        <button class="btn btn-sm btn--danger box--shadow1 text-white text--small cancelBtn">
            <i class="fa fa-fw fa-times"></i>@lang('Cancel Order')
        </button>
        <button class="btn btn-sm btn--primary box--shadow1 text-white text--small acceptBtn">
            <i class="fa fa-fw fa-check"></i>@lang('Accept Order')
        </button>
    @endpush
@endif

@push('script')
    <script>
        (function ($) {
            "use strict";
            
            $('.acceptBtn').on('click', function () {
                var modal = $('#acceptModal');
                modal.modal('show');
            });
            
            $('.cancelBtn').on('click', function () {
                var modal = $('#cancelModal');
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
