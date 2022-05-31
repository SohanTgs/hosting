@extends('admin.layouts.app')
@section('panel') 

<form action="{{ route('admin.order.hosting.update') }}" method="POST">
    @csrf
<input type="hidden" name="id" value="{{ @$hosting->id }}">
<div class="row mb-none-30 mb-2">

    @if(session()->has('response'))
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    @php echo @session()->get('response')->metadata->output->raw; @endphp
                </div>
            </div>
        </div>
    @endif
 
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Order')
                            </span>
                            <a href="{{ route('admin.order.details', $hosting->order_id) }}">@lang('View Order')</a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Invoices')
                            </span>
                            <a href="{{ route('admin.invoice.hosting.all', $hosting->id) }}">@lang('View Invoices')</a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Registration Date')
                            </span>
                            <input type="text" class="timePicker form-control reg_time flex-grow-1" data-language='en' data-position='bottom left' value="{{ showDateTime($hosting->reg_time, 'd-m-Y') }}" name="reg_time" autocomplete="off">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Product/Service')
                            </span>
                            <select name="change_product_id" class="change_product_id form-control">
                                @php echo $productDropdown; @endphp
                            </select>
                        </div>
                    </li> 
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Server')
                            </span> 
                            <select name="server_id" class="server_id form-control"> 
                                @if(@$hosting->product->serverGroup)
                                    <option value="">@lang('Select One')</option>
                                    @foreach(@$hosting->product->serverGroup->servers as $index => $server) 
                                        <option value="{{ $server->id }}" {{ $server->id == $hosting->server_id ? 'selected' : null }}>
                                            {{ $server->hostname }} - {{ $server->name }}
                                        </option>
                                    @endforeach
                                @else 
                                    <option value="">@lang('N/A')</option>
                                @endif
                            </select>
                        </div>
                    </li>
                    <li class="list-group-item ">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @if($hosting->product->product_type == 3)
                                    @lang('Hostname')  
                                @else 
                                    @lang('Domain')  
                                @endif
                            </span>
                            <input class="form-control" type="text" name="domain" value="{{@$hosting->domain}}">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Dedicated IP')  
                            </span>
                            <input class="form-control" type="text" name="dedicated_ip" value="{{@$hosting->dedicated_ip}}">
                        </div>
                    </li>

                    @if($hosting->product->product_type == 3)
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Assigned IPs')  
                                </span>
                                <textarea name="assigned_ips" class="form-control" rows="3">{{@$hosting->assigned_ips}}</textarea>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Nameserver 1')  
                                </span>
                                <input class="form-control" type="text" name="ns1" value="{{@$hosting->ns1}}">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Nameserver 2')  
                                </span>
                                <input class="form-control" type="text" name="ns2" value="{{@$hosting->ns2}}">
                            </div>
                        </li>
                    @endif

                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Username')  
                            </span>
                            <input class="form-control" type="text" name="username" value="{{@$hosting->username}}">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Password')  
                            </span>
                            <div class="w-100">
                                <a href="javascript:void(0)" class="generatePassword">@lang('Generate Strong Password')</a>
                                <input class="form-control" type="text" name="password" value="{{@$hosting->password}}" id="password">
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Status')
                            </span>
                            <select name="domain_status" class="form-control"> 
                                @foreach(@$hosting::domainStatus() as $index => $data) 
                                    <option value="{{ $index }}" {{ @$hosting->domain_status == $index ? 'selected' : null}}>{{ $data }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>

                    @if($hosting->cancelRequest) 
                        <li class="list-group-item"> 
                            <div class="billing-form"> 
                                <span class="billing-form__label d-block flex-shrink-0 mt-1">
                                    <input type="checkbox" id="delete_cancel_request" name="delete_cancel_request">
                                    <label for="delete_cancel_request">@lang('Delete Cancellation Request')</label>
                                </span>
                                <span class="text--primary">@php echo nl22br($hosting->cancelRequest->reason); @endphp</span>
                            </div>
                        </li>
                    @endif


                </ul> 
            </div>
        </div> 
        
        @if($hosting->product->module_type == 1)
            <div class="card mt-4">
                <div class="card-header">
                    @lang('Metric Statistics')
                </div>
                <div class="card-body">
                    <div class="table-responsive--md table-responsive border">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Metric')</th>
                                <th>@lang('Info')</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Disk Limit')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->disklimit ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Disk Used')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->diskused ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Max Subdomains')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->maxsub ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Max Addons')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->maxaddons ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Max SQL Databases')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->maxsql ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Max Email Per Hour')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->max_email_per_hour ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Backup')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->backup ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Legacy Backup')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->legacy_backup ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Theme')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->theme ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Metric')">
                                        @lang('Package')
                                    </td>
                                    <td data-label="@lang('Info')">
                                        <span class="font-weight-bold">
                                            {{ @$accountSummary->plan ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
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
                                <input type="text" name="first_payment_amount" value="{{ getAmount(@$hosting->first_payment_amount) }}" class="form-control">
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
                                @if(@$hosting->billing == 1)
                                    <input type="text" value="{{ getAmount(0) }}" class="form-control">
                                @else 
                                    <input type="text" name="amount" value="{{ getAmount(@$hosting->amount) }}" class="form-control">
                                @endif 
                                <span class="input-group-append">
                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                </span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Next Due Date') 
                            </span>
                            <input type="text" class="timePicker form-control" data-language='en' data-position='bottom left' value="{{ showDateTime(@$hosting->next_due_date, 'd-m-Y') }}" name="next_due_date" autocomplete="off">
                        </div>
                    </li>
                    <li class="list-group-item"> 
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Termination Date') 
                            </span>
                            <input type="text" class="timePicker form-control" data-language='en' data-position='bottom left' 
                            value="{{ @$hosting->termination_date ? showDateTime(@$hosting->termination_date, 'd-m-Y') : null }}" name="termination_date" autocomplete="off">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Billing Cycle')  
                            </span>
                            <select name="billing_cycle" class="form-control">
                                @foreach(billingCycle() as $index => $data) 
                                    <option value="{{ $index }}" {{ $hosting->billing_cycle == $index ? 'selected' : null }} data-data='{{ $data['billing_type'] }}'>
                                        {{ __($data['showText']) }}
                                    </option>
                                @endforeach 
                            </select> 
                        </div>
                    </li> 
                   
                    @foreach($hosting->product->getConfigs as $index => $config)                 
                        @foreach($config->group->options as $option)     
                            <li class="list-group-item">
                                <div class="billing-form">
                                    <span class="billing-form__label d-block flex-shrink-0">
                                        {{ __($option->name) }}
                                    </span>
                                    <select name="config_options[{{ $option->id }}]" class="form-control options">
                                        <option value="">@lang('Select One')</option>
                                        @forelse($option->subOptions as $subOption)
                                            <option value="{{ $subOption->id }}" data-price='{{ $subOption->getOnlyPrice }}' data-text='{{ $subOption->name }}'>
                                                {{ __($subOption->name) }}
                                            </option> 
                                        @empty
                                            <option value="">@lang('N/A')</option>
                                        @endforelse
                                    </select>
                                </div>
                            </li>
                        @endforeach
                    @endforeach

                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Admin Notes')  
                            </span>
                            <textarea name="admin_notes" class="form-control" rows="3">@php echo nl22br($hosting->admin_notes); @endphp</textarea>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
 
</div> 

@if($hosting->product->module_type == 1)
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
                                    <button class="btn btn--primary moduleModal w-100" data-module="1" data-type="1" type="button">
                                        <i class="lab la-cpanel"></i>@lang('Create')
                                    </button>
                                </div>
                                <div class="col-lg-2 col-md-4 form-group">
                                    <button class="btn btn--primary moduleModal w-100" data-module="2" data-type="2" type="button">
                                        <i class="las la-ban"></i>@lang('Suspend')
                                    </button>
                                </div>
                                <div class="col-lg-2 col-md-4 form-group">
                                    <button class="btn btn--primary moduleModal w-100" data-module="3" data-type="3" type="button">
                                        <i class="las la-undo"></i>@lang('Unsuspend')
                                    </button>
                                </div>
                                <div class="col-lg-2 col-md-4 form-group">
                                    <button class="btn btn--primary moduleModal w-100" data-module="4" data-type="4" type="button">
                                        <i class="las la-trash"></i>@lang('Terminate')
                                    </button>
                                </div>
                                <div class="col-lg-2 col-md-4 form-group">
                                    <button class="btn btn--primary moduleModal w-100" data-module="5" data-type="5" type="button">
                                        <i class="las la-exchange-alt"></i>@lang('Change Package')
                                    </button>
                                </div>
                                <div class="col-lg-2 col-md-4 form-group">
                                    <button class="btn btn--primary moduleModal w-100" data-module="6" data-type="6" type="button">
                                        <i class="las la-key"></i>@lang('Change Password')
                                    </button>
                                </div>
                                @if($hosting->suspend_reason)
                                    <div class="col-md-12 mt-3">
                                        <span class="d-block font-weight-bold">@lang('Account Suspended')</span>
                                        <span class="d-block"><span class="font-weight-bold">@lang('Reason')</span> {{ $hosting->suspend_reason }}</span>
                                        <span class="d-block"><span class="font-weight-bold">@lang('Suspended')</span> {{ showDateTime($hosting->suspend_date) }}</span>
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
            <form class="form-horizontal" method="post" action="{{ route('admin.module.command') }}">
                @csrf  
                <input type="hidden" name="hosting_id" value="{{ $hosting->id }}" required>
                <input type="hidden" name="module_type" required>
                <div class="modal-body"> 
                    <div class="form-group">
                        @lang('Are you sure to want run the') <span class="moduleName font-weight-bold"></span> @lang('function')?

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
<a href="{{ route('admin.order.details', @$hosting->order_id) }}" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-backward"></i>@lang('Go Back')
</a>

@if($hosting->product->module_type == 1)
<form class="d-init" action="{{ route('admin.module.cpanel.login') }}" method="post">
    @csrf
    <input type="hidden" name="hosting_id" value="{{ $hosting->id }}" required>
    <button type="submit" class="btn btn-sm btn--success box--shadow1 text-white text--small" {{ @$accountSummary ? null : 'disabled' }}>
        <i class="fa fa-fw fa-sign-in-alt"></i>@lang('Login to cPanel')
    </button>
</form>

<a href="{{ session()->get('url') ?? '#' }}" class="cPanelLogin" target="_blank"></a>
@endif

@endpush 

@push('style')
<style>
    .d-init{
        display: initial;
    }
    @media (max-width: 991px){
    .table-responsive--md tbody tr:nth-child(odd) {
        background-color: #1208080d;
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

            $('.cancel_request_checked').on('click', function(){
                console.log(200);
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

                modal.find('.moduleName').text(moduleName);
                modal.find('input[name=module_type]').val(moduleType);

                modal.modal('show');
            });

            $('.generatePassword').on('click', function(){
                var password = generatePassword(15);
                $('#password').val(password);
            });

            function generatePassword(passwordLength) {
                var numberChars = "0123456789";
                var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                var lowerChars = "abcdefghijklmnopqrstuvwxyz";
                var specialChars = "!#$%&()*+,-./:;<=>?@[\]^_`{|}~";
                var allChars = numberChars + upperChars + lowerChars + specialChars;
                var randPasswordArray = Array(passwordLength);

                randPasswordArray[0] = numberChars;
                randPasswordArray[1] = upperChars;
                randPasswordArray[2] = lowerChars;
                randPasswordArray[3] = specialChars;
                randPasswordArray = randPasswordArray.fill(allChars, 4);

                return shuffleArray(randPasswordArray.map(function(x) { return x[Math.floor(Math.random() * x.length)] })).join('');
            }

            function shuffleArray(array) {
                for (var i = array.length - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var temp = array[i];
                    array[i] = array[j];
                    array[j] = temp;
                }
                return array;
            }
            
            $('.change_product_id').on('change', function(){
                var productId = $(this).val();
                var hostingId = @json($hosting->id);

                if(!productId){
                    return false;
                }

                window.location.href = '{{ route("admin.change.order.hosting.product", ['', '']) }}/'+hostingId+'/'+productId;
            });

            $('.change_product_id option[value=@json($hosting->product->id)]').prop('selected', true);

            var product = @json($hosting->product);
            var hosting = @json($hosting);

            $('select[name=billing_cycle]').on('change', function() {
                var value = $('select[name=billing_cycle] option:selected').data('data');
               
                if($(this).val() == 0){
                    value = 'monthly';
                }

                showSelect(value, product, $(this).val());
            }).change(); 

            function showSelect(value, product, cycle = null){
                try{
                   
                    var getColumn = value;
                    var getFeeColumn = value+'_setup_fee';

                    $('.options').each(function(index, data){
                        var options = $(data).find('option');
                        var general = @json($general);
                        var finalText = null;

                        options.each(function(iteration, dropdown) { 
                            var dropdown = $(dropdown);
                            var dropdownOptions = null; 
                            var optionSetupFee = ''; 
          
                            if( dropdown.data('price') ){ 
                                var priceForThisItem = dropdown.data('price');
                                var mainText = dropdown.data('text');
                 
                                var display = cycle == 0 ? 'One Time' : pricing(0, null, getColumn, cycle);

                                if(cycle == 0){
                                    getColumn = 'monthly'
                                }
                      
                                if(priceForThisItem[getFeeColumn] > 0){
                                    optionSetupFee = ` + ${general.cur_sym}${getAmount(priceForThisItem[getFeeColumn])} ${general.cur_text} Setup Fee`
                                }
            
                                dropdownOptions = `${general.cur_sym}${getAmount(priceForThisItem[getColumn])} ${general.cur_text} ${display} ${optionSetupFee}`;

                                finalText = mainText+' '+dropdownOptions;
                                dropdown.text(finalText);
                            }

                        });
                    });

                }catch(message){
                    console.log(message);
                }
            }

            function pricing(price, type, column, cycle = null){ 
                try{ 
                    
                    if(!price){
                        column = column.replaceAll('_', ' ');
                        
                        if(cycle == 0){
                            column = 'One Time';
                        }

                        return column.replaceAll(/(?:^|\s)\S/g, function(word){
                            return word.toUpperCase(); 
                        });
                    }

                    if(!type){
                        var price = productPrice[column];
                        var fee = productPrice[column+'_setup_fee'];
                        var sum = (parseFloat(fee) + parseFloat(price));
                        
                        return getAmount(sum);
                    }

                    var amount = 0;

                    if(type == 'price'){
                        amount = productPrice[column];
                    }else{
                        column = column+'_setup_fee';
                        amount = productPrice[column];
                    }

                    return getAmount(amount);

                }catch(message){
                    console.log(message);
                }
            }

            function getAmount(getAmount, length = 2){
                var amount = parseFloat(getAmount).toFixed(length);
                return amount;
            }

            var hostingConfigs = @json(@$hosting->hostingConfigs);
            
            for(var i = 0; i < hostingConfigs.length; i++){

                var selectName = hostingConfigs[i]['configurable_group_option_id'];
                var selectOption = hostingConfigs[i]['configurable_group_sub_option_id'];
                    
                $(`select[name='config_options[${selectName}]'] option[value=${selectOption}]`).prop('selected', true);
            }

            var cpanelLoginUrl = @json(session()->get('url'));

            if(cpanelLoginUrl){
                document.querySelector('.cPanelLogin').click();
            }

        })(jQuery);
    </script>
@endpush 

