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
    
        })(jQuery);
    </script>
@endpush 
