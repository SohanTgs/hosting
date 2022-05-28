@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Order Date')
                            </span>
                            {{ showDateTime(@$order->created_at, 'd/m/Y') }} 
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Order')
                            </span>
                            #{{ @$order->id }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('User')
                            </span>
                            <a href="{{ route('admin.users.detail', @$order->user->id) }}">{{ @$order->user->fullname }}</a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Order Status')
                            </span>
                            @php echo @$order->showStatus; @endphp
                        </div>
                    </li>
                </ul> 
            </div>
        </div> 
    </div>  
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Amount')
                            </span>
                            <span class="font-weight-bold">{{ showAmount(@$order->amount) }} {{ $general->cur_text }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Invoice')
                            </span>
                            <a href="{{ route('admin.invoice.details', @$order->invoice->id) }}">#{{ @$order->invoice->id }}</a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('IP Address')
                            </span>
                            <a href="https://extreme-ip-lookup.com/{{ @$order->ip_address }}" target="_blank">{{ @$order->ip_address }}</a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Coupon Code')
                            </span>
                           <span class="font-weight-bold">{{ @$order->coupon->code ?? __('N/A') }}</span>
                        </div>
                    </li>
                </ul> 
            </div>
        </div> 
    </div>  
    @php
        $hostings = $order->hostings;
        $domains = $order->domains;
    @endphp

<form action="{{ route('admin.order.accept') }}" method="post" class="form w-100">
@csrf
<input type="hidden" name="order_id" value="{{ $order->id }}">
    <div class="col-md-12 text-right mb-3">
        <button class="btn btn-sm btn--primary box--shadow1 text-white text--small noteBtn" type="button">
            <i class="far fa-sticky-note"></i>@lang('Admin Notes')
        </button>
    </div>
    <div class="col-xl-12 col-md-12 mb-30">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('Service')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Billing Cycle')</th> 
                            <th>@lang('Amount')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Payment Status')</th>
                        </tr> 
                        </thead> 
                        <tbody>
                        @foreach($hostings as $hosting)
                            @php $product = $hosting->product; @endphp
                            <tr>
                                <td data-label="@lang('Service')">
                                    <span class="font-weight-bold">
                                        <a href="{{ route('admin.order.hosting.details', $hosting->id) }}">{{ __(@$product->item) }}</a>
                                    </span>
                                </td>
                                <td data-label="@lang('Description')">
                                    {{ __(@$product->serviceCategory->name) }} - 
                                    <a href="{{ route('admin.product.update.page', $product->id) }}" target="_blank">{{ __(@$product->name) }}</a>
                                </td>
                                <td data-label="@lang('Billing Cycle')">
                                    @if($hosting->billing == 1) 
                                        @lang('One Time')
                                    @else  
                                        {{ @billingCycle(@$hosting->billing_cycle, true)['showText'] }}
                                    @endif
                                </td>
                                <td data-label="@lang('Amount')">
                                    {{ @$general->cur_sym }}{{ showAmount(@$hosting->amount) }}
                                </td>
                                <td data-label="@lang('Status')">
                                    @php echo $hosting->showDomainStatus; @endphp
                                </td>
                                <td data-label="@lang('Payment Status')">
                                    @php echo $order->invoice->showStatus; @endphp
                                </td>
                            </tr>
                            @if(@$hosting->domain_status == 2 && $order->status == 2)
                                <tr class="bg-light">
                                    <td class="text-muted fullwidth-td" colspan="100%">
                                        <div class="row align-items-center">
                                            @if(@$product->module_option == 2 && $product->module_type == 1)
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group">
                                                        <span class="font-weight-bold">@lang('Username')</span>
                                                        <input type="text" class="form-control" name="hostings[{{ $hosting->id }}][username]" value="{{ $hosting->username }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group">
                                                        <span class="font-weight-bold">@lang('Password')</span>
                                                        <input type="text" class="form-control" name="hostings[{{ $hosting->id }}][password]" value="{{ $hosting->password }}">
                                                    </div>
                                                </div> 
                                                <div class="col-sm-12 col-md-4">
                                                    <div class="form-group">
                                                        <span class="font-weight-bold">@lang('Server')</span>
                                                        <select name="hostings[{{ $hosting->id }}][server_id]" class="form-control">
                                                            <option value="">@lang('None')</option>
                                                            @foreach(@$product->serverGroup->servers ?? [] as $index => $server) 
                                                                <option value="{{ $server->id }}" {{ $server->id == $hosting->server_id ? 'selected' : null }}>
                                                                    {{ $server->hostname }} - {{ $server->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> 
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" name="hostings[{{ $hosting->id }}][run_create_module]" checked id="run_create_module{{ $hosting->id }}"> 
                                                        <label for="run_create_module{{ $hosting->id }}"><span class="font-weight-bold">@lang('Run Module Create')</span></label>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($product->welcome_email != 0 )
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" name="hostings[{{ $hosting->id }}][send_email]" checked id="send_email{{ $hosting->id }}">
                                                        <label for="send_email{{ $hosting->id }}"><span class="font-weight-bold">@lang('Send Welcome Email')</span></label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @foreach($domains as $domain)
                            <tr> 
                                <td data-label="@lang('Service')">
                                    <span class="font-weight-bold">
                                        <a href="{{ route('admin.order.domain.details', $domain->id) }}">@lang('Domain')</a>
                                    </span>
                                </td>
                                <td data-label="@lang('Description')"> 
                                    @lang('Registration') - {{ $domain->domain }}
                                    @if($domain->id_protection) 
                                    <br>
                                       + @lang('ID Protection')
                                    @endif
                                </td>
                                <td data-label="@lang('Billing Cycle')">
                                    {{ __($domain->reg_period) }} @lang('Year/s')
                                </td>
                                <td data-label="@lang('Amount')">
                                    {{ @$general->cur_sym }}{{ showAmount(@$domain->recurring_amount) }}
                                </td>
                                <td data-label="@lang('Status')">
                                    @php echo $domain->showStatus; @endphp
                                </td>
                                <td data-label="@lang('Payment Status')">
                                    @php echo $order->invoice->showStatus; @endphp
                                </td>
                            </tr>
                            @if(@$domain->status == 2 && $order->status == 2)
                                <tr class="bg-light">
                                    <td class="text-muted fullwidth-td" colspan="100%">
                                        <div class="row align-items-center">
                                            <div class="col-sm-12 col-md-4">
                                                <div class="form-group">
                                                    <span class="font-weight-bold">@lang('Register')</span>
                                                    <select name="domains[{{ $domain->id }}][register]" class="form-control">
                                                        <option value="">@lang('None')</option>
                                                        @foreach($domainRegisters as $register) 
                                                            <option value="{{ $register->id }}" {{ $domain->domain_register_id == $register->id ? 'selected' : null}}>
                                                                {{ $register->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6 col-md-2">
                                                <div class="form-group">
                                                    <input type="checkbox" name="domains[{{ $domain->id }}][domain_register]" checked id="domain_register{{ $domain->id }}">
                                                    <label for="domain_register{{ $domain->id }}"><span class="font-weight-bold">@lang('Send to Register')</span></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-2">
                                                <div class="form-group">
                                                    <input type="checkbox" name="domains[{{ $domain->id }}][send_email]" checked id="send_domain_email{{ $domain->id }}">
                                                    <label for="send_domain_email{{ $domain->id }}"><span class="font-weight-bold">@lang('Send Email')</span></label>
                                                </div>
                                            </div> 
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12 note">
    <h4 class="mb-2">@lang('Notes / Additional Information')</h4>
    <div class="card">
        <form action="{{ route('admin.order.notes') }}" method="post">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <div class="card-body">
                <textarea name="admin_notes" rows="6" class="form-control" required>@php echo $order->admin_notes; @endphp</textarea>
                <button type="submit" class="btn btn--primary btn-block mt-3">@lang('Submit')</button>
            </div>
        </form>
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
            <div class="modal-body">
                <div class="form-group">
                    @lang('Are you sure to want to accept this order')?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                <button type="button" class="btn btn--primary submitBtn">@lang('Yes')</button>
            </div> 
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

{{-- Mark as Modal --}}
<div class="modal fade" id="padingModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Confirmation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.order.mark.pending') }}">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        @lang('Are you sure to want to set this order back to Pending')?
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


@push('breadcrumb-plugins') 
    @if($order->status == 2)
        <button class="btn btn-sm btn--danger box--shadow1 text-white text--small cancelBtn">
            <i class="fa fa-fw fa-times"></i>@lang('Cancel Order')
        </button>
        <button class="btn btn-sm btn--primary box--shadow1 text-white text--small acceptBtn">
            <i class="fa fa-fw fa-check"></i>@lang('Accept Order')
        </button>
    @else
        <button class="btn btn-sm btn--warning box--shadow1 text-white text--small pendingBtn">
            <i class="fa fa-fw fa-spinner"></i>@lang('Mark as Pending')
        </button>
    @endif
@endpush

@push('style')
<style>
    @media (max-width: 991px) {
        .table-responsive--md tr .fullwidth-td {
            display: block;
            padding-left: 15px !important;
            text-align: left !important;
        }
    }
</style> 
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            
            $('.note').toggle();

            $('.acceptBtn').on('click', function () {
                var modal = $('#acceptModal');
                modal.modal('show');
            });
            
            $('.cancelBtn').on('click', function () {
                var modal = $('#cancelModal');
                modal.modal('show');
            });
            
            $('.pendingBtn').on('click', function () {
                var modal = $('#padingModal');
                modal.modal('show');
            });
            
            $('.submitBtn').on('click', function () {
                $('.form').submit();
            });
            
            $('.noteBtn').on('click', function () {
                $('.note').toggle(300);
            });

        })(jQuery);
    </script>
@endpush
