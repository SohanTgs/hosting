@extends('admin.layouts.app')
@section('panel') 
<form action="{{ route('admin.order.hosting.update') }}" method="POST">
    @csrf
<input type="hidden" name="id" value="{{ @$hosting->id }}">
<div class="row mb-none-30 mb-2">
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Payment Method') 
                            </span>
                            <span class="font-weight-bold">
                                @if(@$hosting->deposit_id) 
                                    <a href="{{ route('admin.deposit.details', @$hosting->deposit_id) }}">{{ __(@$hosting->deposit->gateway->name) }}</a>
                                @elseif($hosting->status != 0)
                                    @lang('Wallet Balance')
                                @else
                                    @lang('N/A')
                                @endif
                            </span>
                        </div>
                    </li>
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
                                @lang('Invoice')
                            </span>
                            <a href="{{ route('admin.invoice.download', ['id'=>$hosting->order->invoice->id, 'view'=>'preview']) }}" target="_blank">
                                @lang('View Invoice')
                            </a>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Registration Date')
                            </span>
                            <input type="text" class="datepicker-here form-control reg_time flex-grow-1" data-language='en' data-position='bottom left' value="{{ showDateTime($hosting->reg_time, 'd-m-Y') }}" name="reg_time" autocomplete="off">
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
                                @lang('Domain')  
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
                            <input type="text" class="datepicker-here form-control" data-language='en' data-position='bottom left' value="{{ showDateTime(@$hosting->next_due_date, 'd-m-Y') }}" name="next_due_date" autocomplete="off">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Termination Date') 
                            </span>
                            <input type="text" class="datepicker-here form-control" data-language='en' data-position='bottom left' 
                            value="{{ @$hosting->termination_date ? showDateTime(@$hosting->termination_date, 'd-m-Y') : null }}" name="termination_date" autocomplete="off">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Billing Cycle')  
                            </span>
                            <select name="billing_cycle" class="form-control">
                                @foreach(billing_cycle() as $index => $data)
                                    <option value="{{ $index }}" {{ $hosting->billing_cycle == $index ? 'selected' : null }}>{{ __($data) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>
                   
                    @foreach($hosting->product->getConfigs as $index => $config)                      
                        <li class="list-group-item">
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    {{ __($config->group->name) }}
                                </span>
                                <select name="" class="form-control">
                                    @forelse($config->group->options as $option)
                                        <option value="{{ $option->id }}">{{ __($option->name) }}</option>
                                    @empty
                                        <option value="">@lang('N/A')</option>
                                    @endforelse
                                </select>
                            </div>
                        </li>
                    @endforeach

                    <li class="list-group-item">
                        <div class="billing-form">
                            <span class="billing-form__label d-block flex-shrink-0">
                                @lang('Admin Notes')  
                            </span>
                            <textarea name="admin_notes" class="form-control" rows="3">@php echo nl2br($hosting->admin_notes); @endphp</textarea>
                        </div>
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
                            <div class="button-group">
                                <button class="btn btn--primary moduleMoal" data-module="1" data-type="1" type="button">
                                    <i class="las la-plus"></i>@lang('Create')
                                </button>
                                <button class="btn btn--primary moduleMoal" data-module="2" data-type="2" type="button">
                                    <i class="las la-ban"></i>@lang('Suspend')
                                </button>
                                <button class="btn btn--primary moduleMoal" data-module="3" data-type="3" type="button">
                                    <i class="las la-undo"></i>@lang('Unsuspend')
                                </button>
                                <button class="btn btn--primary moduleMoal" data-module="4" data-type="4" type="button">
                                    <i class="las la-trash"></i>@lang('Terminate')
                                </button>
                                <button class="btn btn--primary moduleMoal" data-module="5" data-type="5" type="button">
                                    <i class="las la-exchange-alt"></i>@lang('Change Package')
                                </button>
                                <button class="btn btn--primary moduleMoal" data-module="6" data-type="6" type="button">
                                    <i class="las la-key"></i>@lang('Change Password')
                                </button>
                            </div>
                            @if($hosting->suspend_reason)
                                <div class="mt-2">
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

<div class="row mb-none-30">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {{-- <label class="form-control-label font-weight-bold">@lang('Password')</label>
                            <input class="form-control form-control-lg" type="text" name="password" value="{{@$hosting->password}}" id="password"> --}}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Submit')</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

{{-- Module Modal --}}
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

                        <div class="form-group mt-4 passwordArea">
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

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
           
            if(!$('.datepicker-here').val()){
                $('.datepicker-here').datepicker({
                    dateFormat: 'dd-mm-yyyy'
                });
            }

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
            
            $('.change_product_id').on('change', function(){
                var productId = $(this).val();
                var hostingId = @json($hosting->id);

                if(!productId){
                    return false;
                }

                window.location.href = '{{ route("admin.change.order.hosting.product", ['', '']) }}/'+hostingId+'/'+productId;
            });

            $('.change_product_id option[value=@json($hosting->product->id)]').prop('selected', true);

        })(jQuery);
    </script>
@endpush 

