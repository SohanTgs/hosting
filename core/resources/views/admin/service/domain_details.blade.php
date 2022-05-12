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

    {{-- @if($hosting->product->module_type == 1) --}}
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
                                    <div class="col-lg-4 col-md-4 form-group">
                                        <button class="btn btn--primary moduleModal w-100" data-module="1" data-type="1" type="button">
                                            <i class="las la-registered"></i>@lang('Register')
                                        </button>
                                    </div>
                                    <div class="col-lg-4 col-md-4 form-group">
                                        <button class="btn btn--primary moduleModal w-100" data-module="2" data-type="2" type="button">
                                            <i class="las la-shopping-cart"></i>@lang('Renew')
                                        </button>
                                    </div> 
                                    <div class="col-lg-4 col-md-4 form-group">
                                        <button class="btn btn--primary moduleModal w-100" data-module="3" data-type="3" type="button">
                                            <i class="las la-undo-alt"></i>@lang('Modify Contact Details')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    {{-- @endif --}}

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

                        <div class="form-group mt-4 suspendArea">
                            <label class="form-control-label font-weight-bold">@lang('Reason')</label>
                            <input type="text" class="form-control" name="suspend_reason" autocomplete="off">
                        </div> 
                        <div class="form-group suspendArea">
                            <input type="checkbox" name="suspend_email" id="suspend"> <label for="suspend">@lang('Send Suspension Email')</label>
                        </div>

                        <div class="form-group mt-4 unSuspendArea">
                            <input type="checkbox" name="unSuspend_email" id="unSuspend"> <label for="unSuspend">@lang('Send Unsuspension Email')</label>
                        </div> 

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
                var moduleType =  $(this).data('type');

                if(moduleType == 2){
                   $('.suspendArea').removeClass('d-none'); 
                }else{
                    $('.suspendArea').addClass('d-none'); 
                }

                if(moduleType == 3){
                   $('.unSuspendArea').removeClass('d-none'); 
                }else{
                    $('.unSuspendArea').addClass('d-none'); 
                }

                modal.find('.registerName').text('SERVICE');

                modal.find('.moduleName').text(moduleName);
                modal.find('input[name=module_type]').val(moduleType);

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush 
