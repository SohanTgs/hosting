@extends('admin.layouts.app')
@section('panel')
<form action="{{ route('admin.invoice.update') }}" method="post">
    @csrf

    <input type="hidden" name="invoice_id" value="{{$invoice->id }}">

    <div class="row mb-none-30">
        <div class="col-xl-6 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-20 text-muted">@php echo @$invoice->showStatus; @endphp</h5>
                    </div>  
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('User') 
                                </span>
                                <a href="{{ route('admin.users.detail', @$invoice->user_id) }}">{{ @$invoice->user->fullname }}</a>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Order')
                                </span>
                                @php echo $invoice->viewDetails('domain'); @endphp
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Total Amount')
                                </span>
                                <span class="font-weight-bold">
                                    {{ $general->cur_sym }}{{ showAmount(@$invoice->amount) }}
                                </span>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Stauts')
                                </span> 
                                <select name="status" class="server_id form-control"> 
                                    @foreach($invoice::status() as $index => $status)
                                        <option value="{{ $index }}" {{ $invoice->status == $index ? 'selected' : null }} {{ $index == 5 ? 'disabled' : null }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
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
                                    @lang('Invoice Date')
                                </span>
                                <input type="text" class="timePicker form-control created flex-grow-1" data-language='en' data-position='bottom left' value="{{ showDateTime($invoice->created, 'd-m-Y') }}" name="created" autocomplete="off">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Due Date')
                                </span>
                                <input type="text" class="timePicker form-control" data-language='en' data-position='bottom left' 
                                value="{{ @$invoice->due_date ? showDateTime(@$invoice->due_date, 'd-m-Y') : null }}" name="due_date" autocomplete="off">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Paid Date')
                                </span>
                                <input type="text" class="timePicker form-control" data-language='en' data-position='bottom left' 
                                value="{{ @$invoice->paid_date ? showDateTime(@$invoice->paid_date, 'd-m-Y') : null }}" name="paid_date" autocomplete="off">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Admin Notes')  
                                </span>
                                <textarea name="admin_notes" class="form-control" rows="3">@php echo nl22br($invoice->admin_notes); @endphp</textarea>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @php
        $items = @$invoice->items;
    @endphp


<div class="row mt-4">
 
    @if($invoice->status != 5)
        <div class="col-md-12 text-right mb-3">
            <button class="btn btn-sm btn--primary box--shadow1 text-white text--small addNewItem" type="button">
                <i class="fa fa-fw fa-plus"></i>@lang('Add Item')
            </button>
        </div>
    @endif

    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Description')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="invoiceTable">  
                            @forelse($items as $item)
                                <tr> 
                                    <td data-label="@lang('Description')">
                                        <textarea name="items[{{ $item->id }}][description]" cols="80" class="form-control" required>@php echo nl22br($item->description); @endphp</textarea>
                                    </td>
                                    <td data-label="@lang('Amount')">
                                       <div class="row justify-content-center">
                                           <div class="col-xl-6">
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control" name="items[{{ $item->id }}][amount]" 
                                                    value="{{ getAmount(@$item->amount) }}" required>
                                                    <span class="input-group-append">
                                                        <span class="input-group-text">{{ @$general->cur_text }}</span>
                                                    </span>
                                                </div>
                                           </div>
                                       </div>
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <button class="icon-btn btn--danger delete" type="button" data-toggle="tooltip" title="" data-original-title="@lang('Delete')"
                                        data-id="{{ $item->id }}"
                                        >
                                            <i class="las la-trash text--shadow"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty  
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No data found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-12 mt-3"> 
        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn--primary btn-block" {{ $invoice->status == 5 ? 'disabled' : null }}>@lang('Submit')</button>
            </div>
        </div>
    </div>
</div>
</form>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Confirmation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.invoice.item.delete') }}">
                @csrf
                <input type="hidden" name="id" required> 
                <input type="hidden" name="invoice_id" value="{{$invoice->id }}">
                <div class="modal-body">
                    <p>@lang('Are you sure to delete this item')?</p>
                </div> 
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-save" value="add">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Are you sure to refund')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.invoice.refund') }}">
                @csrf
                <input type="hidden" name="invoice_id" value="{{$invoice->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="amount">@lang('Amount')</label>
                            <label class="text--primary">@lang('Leave blank for full refund')</label>
                            <input type="number" step="any" class="form-control" name="amount"  id="amount" placeholder="0.00">
                        </div>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-save" value="add">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')

@if($invoice->status == 1)
    <button class="btn btn-sm btn--warning box--shadow1 text-white text--small refundModal" type="button">
        <i class="fa fa-fw fa-hand-holding-usd"></i>@lang('Refund')
    </button>
@endif

<a href="{{ route('admin.invoice.download', $invoice->id) }}" class="btn btn-sm btn--success box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-download"></i>@lang('Download')
</a>
<a href="{{ route('admin.invoice.download', ['id'=>$invoice->id, 'view'=>'preview']) }}" target="_blank" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-eye"></i>@lang('Preview')
</a>
@endpush

@push('style')
<style>
    table textarea {
        min-height: auto !important;
    }
</style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
           
            $('.timePicker').datepicker({
                dateFormat: 'dd-mm-yyyy'
            });

            $('table textarea').each(function () {
                this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
            }).on('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            $('.addNewItem').on('click', function(){

                var getFakeId = fakeId(4);
                var html = `
                <tr> 
                    <td data-label="@lang('Description')">
                        <textarea name="items[${getFakeId}][description]" cols="80" class="form-control" required></textarea>
                    </td>
                    <td data-label="@lang('Amount')">
                        <div class="row justify-content-center">
                            <div class="col-xl-6">
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="items[${getFakeId}][amount]" required>
                                    <span class="input-group-append">
                                        <span class="input-group-text">{{ @$general->cur_text }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td data-label="@lang('Action')">
                        <button class="icon-btn btn--danger removeItem" type="button" data-toggle="tooltip" title="" data-original-title="@lang('Delete')">
                            <i class="las la-trash text--shadow"></i>
                        </button>
                    </td>
                </tr>`;

                $('.invoiceTable').append(html);
            });

            $(document).on('click', '.removeItem', function(){
                $(this).closest('tr').remove();
            })

            $('.delete').on('click', function () {
                var modal = $('#deleteModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.refundModal').on('click', function () {
                var modal = $('#refundModal');
                modal.modal('show');
            });

            function fakeId(length) {
                var result = '';
                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;
                for ( var i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * 
                    charactersLength));
                }

                var date = new Date();   
                var seconds = date.getSeconds();
                return result+seconds;
            }

        })(jQuery);
    </script>
@endpush 
