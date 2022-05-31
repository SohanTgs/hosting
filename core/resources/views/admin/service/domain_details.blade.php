@extends('admin.layouts.app')
@section('panel')
<form action="{{ route('admin.order.domain.update') }}" method="POST">
<input type="hidden" name="id" value="{{ $domain->id }}">
@csrf
    <div class="row mb-none-30 mb-2">
        <div class="col-xl-6 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Order')
                                </span>
                                <a href="{{ route('admin.order.details', $domain->order_id) }}">@lang('View Order')</a>
                            </div>
                        </li> 
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Invoices')
                                </span>
                                <a href="{{ route('admin.invoice.domain.all', $domain->id) }}">@lang('View Invoices')</a>
                            </div> 
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Registration Date')
                                </span> 
                                <input type="text" class="timePicker form-control flex-grow-1" data-language='en' data-position='bottom left' 
                                value="{{ showDateTime($domain->reg_time, 'd-m-Y') }}" name="reg_time" autocomplete="off">
                            </div>  
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Domain')  
                                </span> 
                                <input class="form-control" type="text" name="domain" value="{{@$domain->domain}}">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Expiry Date')
                                </span>
                                <input type="text" class="timePicker form-control flex-grow-1" data-language='en' data-position='bottom left' 
                                value="{{ showDateTime($domain->expiry_date, 'd-m-Y') }}" name="expiry_date" autocomplete="off">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Next Due Date')
                                </span>
                                <input type="text" class="timePicker form-control flex-grow-1" data-language='en' data-position='bottom left' 
                                value="{{ showDateTime($domain->next_due_date, 'd-m-Y') }}" name="next_due_date" autocomplete="off">
                            </div>
                        </li>
 
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Register')
                                </span> 
                                <select name="register_id" class="form-control"> 
                                    <option value="">@lang('Select One')</option>
                                    @foreach($domainRegisters as $register) 
                                        <option value="{{ $register->id }}" {{ $domain->domain_register_id == $register->id ? 'selected' : null}}>{{ $register->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Status')
                                </span>
                                <select name="status" class="form-control"> 
                                    @foreach($domain::status() as $index => $data) 
                                        <option value="{{ $index }}" {{ $domain->status == $index ? 'selected' : null}}>{{ $data }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('First Payment Amount')
                                </span>
                                <div class="input-group">
                                    <input type="text" name="first_payment_amount" value="{{ getAmount(@$domain->first_payment_amount) }}" class="form-control">
                                    <span class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </span>
                                </div>
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
                                    @lang('Recurring Amount') 
                                </span>
                                <div class="input-group">
                                    <input type="text" name="recurring_amount" value="{{ getAmount(@$domain->recurring_amount) }}" class="form-control">
                                    <span class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Registration Period')  
                                </span>
                                <div class="input-group">
                                    <input type="text" name="reg_period" value="{{ @$domain->reg_period }}" class="form-control">
                                    <span class="input-group-append">
                                        <span class="input-group-text">@lang('Years')</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Subscription ID')  
                                </span>
                                <input type="text" name="subscription_id" value="{{ @$domain->subscription_id }}" class="form-control">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Nameserver 1')  
                                </span>
                                <input type="text" name="ns1" value="{{ @$domain->ns1 }}" class="form-control" placeholder="@lang('ns1.example.com')">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Nameserver 2')  
                                </span>
                                <input type="text" name="ns2" value="{{ @$domain->ns2 }}" class="form-control" placeholder="@lang('ns2.example.com')">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Nameserver 3')  
                                </span>
                                <input type="text" name="ns3" value="{{ @$domain->ns3 }}" class="form-control" placeholder="@lang('ns3.example.com')">
                            </div>
                        </li> 
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Nameserver 4')  
                                </span>
                                <input type="text" name="ns4" value="{{ @$domain->ns4 }}" class="form-control" placeholder="@lang('ns4.example.com')">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Admin Notes')  
                                </span>
                                <textarea name="admin_notes" class="form-control" rows="3">@php echo nl22br($domain->admin_notes); @endphp</textarea>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    <input type="checkbox" name="id_protection" value="1" {{ $domain->id_protection ? 'checked' : null  }} id="id_protection">
                                    <label for="id_protection">@lang('ID Protection') </label> 
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div> 

    @if($register) 
        <div class="row mb-none-30 mb-3">
            <div class="col-lg-12 col-md-12 mb-30">
                <div class="card">
                    <div class="card-header">
                        @lang('Module Commands')
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-lg-2 col-md-4 form-group">
                                        <button class="btn btn--primary w-100 registerModal" type="button">
                                            <i class="las la-registered"></i>@lang('Register')
                                        </button>
                                    </div>
                                    <div class="col-lg-2 col-md-4 form-group">
                                        <button class="btn btn--primary w-100 moduleModal" data-module="2" type="button">
                                            <i class="las la-server"></i>@lang('Change Nameservers')
                                        </button>
                                    </div>
                                    <div class="col-lg-2 col-md-4 form-group">
                                        <button class="btn btn--primary moduleModal w-100" type="button" data-module="3">
                                            <i class="las la-shopping-cart"></i>@lang('Renew')
                                        </button>
                                    </div> 
                                    <div class="col-lg-2 col-md-4 form-group">
                                        @php
                                            $contactDetails = $domain->register ? route('admin.order.domain.contact', $domain->id) : '#';
                                        @endphp
                                        <a href="{{ $contactDetails }}" class="btn btn--primary w-100">
                                            <i class="las la-undo-alt"></i>@lang('Modify Contact Details')
                                        </a>
                                    </div>

                                    @if(!$domain->id_protection)
                                        <div class="col-lg-2 col-md-4 form-group">
                                            <button class="btn btn--primary moduleModal w-100" type="button" data-module="5">
                                                <i class="las la-shopping-cart"></i>@lang('Enable ID Protection')
                                            </button>
                                        </div> 
                                    @else
                                        <div class="col-lg-2 col-md-4 form-group">
                                            <button class="btn btn--primary moduleModal w-100" type="button" data-module="6">
                                                <i class="las la-shopping-cart"></i>@lang('Disable ID Protection')
                                            </button>
                                        </div> 
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    @endif

    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Submit')</button>
                </div>
            </div>
        </div>
    </div>
</form> 

{{-- Register Modal --}}
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Register Domain')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.domain.module.command') }}">
                @csrf  
                <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
                <input type="hidden" name="module_type" required value="1">
                <div class="modal-body"> 
                    <div class="form-group">
                        <label>@lang('Domain Register')</label>
                        <input type="text" class="form-control" disabled value="{{ @$domain->register->name ?? 'N/A' }}">
                    </div>
                    <div class="form-group">
                        <label>@lang('Domain')</label>
                        <input type="text" class="form-control" disabled value="{{ $domain->domain }}">
                    </div>
                    <div class="form-group">
                        <label>@lang('Registration Period')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" disabled value="{{ $domain->reg_period }}">
                            <span class="input-group-append">
                                <span class="input-group-text">@lang('Years')</span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ns1">@lang('Nameserver 1')</label>
                        <input type="text" class="form-control" name="ns1" id="ns1" placeholder="ns1.example.com" required value="{{ $general->ns1 }}">
                    </div>
                    <div class="form-group">
                        <label for="ns2">@lang('Nameserver 2')</label>
                        <input type="text" class="form-control" name="ns2" id="ns2" placeholder="ns2.example.com" required value="{{ $general->ns2 }}">
                    </div>
                    <div class="form-group">
                        <label for="ns3">@lang('Nameserver 3')</label>
                        <input type="text" class="form-control" name="ns3" id="ns3" placeholder="ns3.example.com">
                    </div>
                    <div class="form-group">
                        <label for="ns4">@lang('Nameserver 4')</label>
                        <input type="text" class="form-control" name="ns4" id="ns4" placeholder="ns4.example.com">
                    </div>
                    <div>
                        <input type="checkbox" name="send_email" id="send_email"> 
                        <label for="send_email">@lang('Send Confirmation Email')</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                </div> 
            </form> 
        </div>
    </div>
</div> 

{{-- Module Modal --}}
<div class="modal fade" id="moduleModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Confirm Module Command')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.domain.module.command') }}">
                @csrf  
                <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
                <input type="hidden" name="module_type" required>
                <div class="modal-body"> 
                    <div class="form-group">
                        @lang('Are you sure to want run the ') <span class="moduleName font-weight-bold"></span> @lang(' function request to the ')
                        <span class="registerName font-weight-bold"></span>?
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
<a href="{{ route('admin.order.details', $domain->order_id) }}" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-backward"></i>@lang('Go Back')
</a>
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

            $('.moduleModal').on('click', function () {
                var modal = $('#moduleModal');

                var moduleName = $(this).text();
                modal.find('.registerName').text(getRegisterName());
                modal.find('.moduleName').text(moduleName);
                modal.find('input[name=module_type]').val($(this).data('module'));

                modal.modal('show');
            });

            $('.registerModal').on('click', function () {
                var modal = $('#registerModal');
                modal.modal('show');
            });

            function getRegisterName(){
                return $('select[name=register_id]').find(":selected").text();
            }

        })(jQuery);
    </script>
@endpush 
