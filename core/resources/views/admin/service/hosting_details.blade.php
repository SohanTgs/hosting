@extends('admin.layouts.app')
@section('panel') 
<div class="row mb-none-30 mb-2">
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Registration Date')
                        <span class="font-weight-bold">{{ showDateTime(@$hosting->created_at, 'd/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Service')
                        <span class="font-weight-bold">
                            <a href="{{ route('admin.product.update.page', @$hosting->product_id) }}">{{ __(@$hosting->product->name) }}</a>
                        </span> 
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Service Category')
                        <span class="font-weight-bold">
                            {{ __(@$hosting->product->serviceCategory->name) }}
                        </span> 
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Server')
                        <span class="font-weight-bold">{{ __(@$hosting->server->name) ?? __('N/A') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Domain')  
                        <span class="font-weight-bold">{{ __(@$hosting->domain) ?? __('N/A') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Status')
                        <span class="font-weight-bold">@php echo @$hosting->showDomainStatus; @endphp</span>
                    </li>
                </ul> 
            </div>
        </div> 
    </div>  
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('First Payment Amount')
                        <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(@$hosting->first_payment_amount) }}</span>
                    </li> 
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Discount')
                        <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(@$hosting->discount) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Recurring Amount') 
                        @if(@$hosting->billing == 1)
                            <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(0) }}</span>
                        @else 
                            <span class="font-weight-bold">{{ $general->cur_sym }}{{ showAmount(@$hosting->amount) }}</span>
                        @endif  
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Next Due Date') 
                        <span class="font-weight-bold">
                            @if($hosting->next_due_date)
                            {{ showDateTime(@$hosting->next_due_date, 'd/m/Y') }}
                            @else 
                                @lang('N/A')
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Billing Cycle')  
                        @if(@$hosting->billing == 1)
                            <span class="font-weight-bold">@lang('One Time')</span>
                        @else 
                            <span class="font-weight-bold">{{ billing(@$hosting->billing_cycle, true)['showText'] }}</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        @lang('Payment Method') 
                        <span class="font-weight-bold">
                            @if(@$hosting->deposit_id) 
                                <a href="{{ route('admin.deposit.details', @$hosting->deposit_id) }}">{{ __(@$hosting->deposit->gateway->name) }}</a>
                            @elseif($hosting->status != 0)
                                @lang('Wallet Balance')
                            @else
                                @lang('N/A')
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if(count(@$hosting->hostingConfigs))
        @foreach(@$hosting->hostingConfigs as $config)
            <div class="col-xl-3 col-md-6 mb-30">
                <div class="card b-radius--10 overflow-hidden">
                    <div class="card-body">
                        <span class="font-weight-bold">{{ __($config->select->name) }}</span>
                        <p>{{ __($config->option->name) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="row mb-none-30 mb-3">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold d-block">@lang('Module Commands')</label>
                            <button class="btn btn--primary moduleMoal" data-module="1" data-type="1">@lang('Create')</button>

                            <button class="btn btn--primary moduleMoal" data-module="2" data-type="2">@lang('Suspend')</button>

                            <button class="btn btn--primary moduleMoal" data-module="3" data-type="3">@lang('Unsuspend')</button>
                    
                            <button class="btn btn--primary moduleMoal" data-module="4" data-type="4">@lang('Terminate')</button>
                        
                            <button class="btn btn--primary moduleMoal" data-module="5" data-type="5">@lang('Change Package')</button>

                            <button class="btn btn--primary moduleMoal" data-module="6" data-type="6">@lang('Change Password')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-none-30">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.order.hosting.update') }}" method="POST">
                    <input type="hidden" name="id" value="{{ @$hosting->id }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Domain') </label>
                                <input class="form-control form-control-lg" type="text" name="domain" value="{{@$hosting->domain}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Dedicated IP')</label>
                                <input class="form-control form-control-lg" type="text" name="dedicated_ip" value="{{@$hosting->dedicated_ip}}">
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label class="form-control-label font-weight-bold">@lang('Username')</label>
                                <input class="form-control form-control-lg" type="text" name="username" value="{{@$hosting->username}}">
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <label class="form-control-label font-weight-bold">@lang('Password')</label>
                                    <a href="javascript:void(0)" class="generatePassword">@lang('Generate Strong Password')</a>
                                </div>
                                <input class="form-control form-control-lg" type="text" name="password" value="{{@$hosting->password}}" id="password">
                            </div>
                        </div>
                        <div class="col-md-4">   
                            <div class="form-group"> 
                                <label class="form-control-label font-weight-bold">@lang('Status')</label>
                                <select name="domain_status" class="form-control form-control-lg"> 
                                    @foreach(@$hosting::domainStatus() as $index => $data) 
                                        <option value="{{ $index }}" {{ @$hosting->domain_status == $index ? 'selected' : null}}>{{ $data }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Accept Modal --}}
<div class="modal fade" id="moduleMoal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
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
                        @lang('Are you sure you want to run the') <span class="moduleName font-weight-bold"></span> @lang('function')?

                        <div class="form-group mt-4 passwordArea d-none">
                            <div class="justify-content-between d-flex flex-wrap">
                                <label class="form-control-label font-weight-bold">@lang('Password')</label>
                                <a href="javascript:void(0)" class="newGeneratePassword">@lang('Generate Strong Password')</a>
                            </div>
                            <input type="text" class="form-control newPassword" name="password" autocomplete="off">
                        </div>

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
@endpush 

@push('style')
    <style>
        
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
           
            $('.moduleMoal').on('click', function () {
                var modal = $('#moduleMoal');

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

                if(moduleType == 6){
                   $('.passwordArea').removeClass('d-none'); 
                }else{
                    $('.passwordArea').addClass('d-none'); 
                }

                modal.find('.moduleName').text(moduleName);
                modal.find('input[name=module_type]').val(moduleType);

                modal.modal('show');
            });

            $('.generatePassword').on('click', function(){
                var password = generatePassword(10);
                $('#password').val(password);
            });

            $('.newGeneratePassword').on('click', function(){
                var password = generatePassword(10);
                $('.newPassword').val(password);
            });

            function generatePassword(passwordLength) {
                var numberChars = "0123456789";
                var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                var lowerChars = "abcdefghijklmnopqrstuvwxyz";
                var specialChars = "!@#$%^&*()_+-*/?><{}:|.";
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

        })(jQuery);
    </script>
@endpush 

