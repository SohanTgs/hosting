@extends($activeTemplate.'layouts.frontend')
@section('content')

<form action="{{ route('user.config.domain.update') }}" method="post">
    @csrf
    <input type="hidden" name="id" required value="{{ $id }}">
    <input type="hidden" name="domain" required value="{{ $domain }}">
    <div class="container mt-5">
        <div class="row">
            @include($activeTemplate.'partials.sidenav')

            <div class="col-md-9 mt-3 mt-md-0">  
                <div class="row"> 

                    <div class="col-md-12"> 
                        <h1>{{ __($pageTitle) }}</h1> 
                        <p>@lang('Please review your domain name selections and any addons that are available for them')</p>
                    </div>

                    @php
                        $pricing = $domainSetup->pricing;
                    @endphp

                    <div class="col-md-4 form-group {{ $pricing->one_year_price >= 0 ? '' : 'd-none' }}">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <input type="radio" name="reg_period" id="one_year_price" value="1" required>
                                    <label for="one_year_price">@lang('One year')</label>
                                    <small>({{ showAmount($pricing->one_year_price) }} {{ __($general->cur_text) }})</small>
                                </div>
                                <div>
                                    <input type="radio" value="1" id="one_year_id_protection" name="id_protection" disabled>
                                    <label for="one_year_id_protection">@lang('With ID Protection')</label>
                                    <small class="d-block">({{ showAmount($pricing->one_year_id_protection) }} {{ __($general->cur_text) }})</small>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group {{ $pricing->two_year_price >= 0 ? '' : 'd-none' }}">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <input type="radio" name="reg_period" id="two_year_price" value="2" required> 
                                    <label for="two_year_price">@lang('Two year')</label>
                                    <small>({{ showAmount($pricing->two_year_price) }} {{ __($general->cur_text) }})</small>
                                </div>
                                <div>
                                    <input type="radio" value="2" id="two_year_id_protection" name="id_protection" disabled>
                                    <label for="two_year_id_protection">@lang('With ID Protection')</label>
                                    <small class="d-block">({{ showAmount($pricing->two_year_id_protection) }} {{ __($general->cur_text) }})</small>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group {{ $pricing->three_year_price >= 0 ? '' : 'd-none' }}">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <input type="radio" name="reg_period" id="three_year_price" value="3" required> 
                                    <label for="three_year_price">@lang('Three year')</label>
                                    <small>({{ showAmount($pricing->three_year_price) }} {{ __($general->cur_text) }})</small>
                                </div>
                                <div>
                                    <input type="radio" value="3" id="three_year_id_protection" name="id_protection" disabled>
                                    <label for="three_year_id_protection">@lang('With ID Protection')</label>
                                    <small class="d-block">({{ showAmount($pricing->three_year_id_protection) }} {{ __($general->cur_text) }})</small>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group {{ $pricing->four_year_price >= 0 ? '' : 'd-none' }}">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <input type="radio" name="reg_period" id="four_year_price" value="4" required> 
                                    <label for="four_year_price">@lang('Four year')</label>
                                    <small>({{ showAmount($pricing->four_year_price) }} {{ __($general->cur_text) }})</small>
                                </div>
                                <div>
                                    <input type="radio" value="4" id="four_year_id_protection" name="id_protection" disabled>   
                                    <label for="four_year_id_protection">@lang('With ID Protection')</label>
                                    <small class="d-block">({{ showAmount($pricing->four_year_id_protection) }} {{ __($general->cur_text) }})</small>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group {{ $pricing->five_year_price >= 0 ? '' : 'd-none' }}">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <input type="radio" name="reg_period" id="five_year_price" value="5" required> 
                                    <label for="five_year_price">@lang('Five year')</label>
                                    <small>({{ showAmount($pricing->five_year_price) }} {{ __($general->cur_text) }})</small>
                                </div>
                                <div>
                                    <input type="radio" value="5" id="five_year_id_protection" name="id_protection" disabled>  
                                    <label for="five_year_id_protection">@lang('With ID Protection')</label>
                                    <small class="d-block">({{ showAmount($pricing->five_year_id_protection) }} {{ __($general->cur_text) }})</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group {{ $pricing->six_year_price >= 0 ? '' : 'd-none' }}">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <input type="radio" name="reg_period" id="six_year_price" value="6" required> 
                                    <label for="six_year_price">@lang('Six year')</label>
                                    <small>({{ showAmount($pricing->six_year_price) }} {{ __($general->cur_text) }})</small>
                                </div>
                                <div>
                                    <input type="radio" value="6" id="six_year_id_protection" name="id_protection" disabled>
                                    <label for="six_year_id_protection">@lang('With ID Protection')</label>
                                    <small class="d-block">({{ showAmount($pricing->six_year_id_protection) }} {{ __($general->cur_text) }})</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 form-group text-center mt-4"> 
                        <button type="submit" class="btn bg-info btn-lg text-white" data-toggle="modal" data-target="#paymentModal">
                            @lang('Continue') <i class="fa fa-arrow-circle-right"></i>
                        </button>
                    </div>
                </div>
            </div> 

        </div> 
    </div>
</form> 
@endsection

@push('script')
<script>
    (function ($) { 
        "use strict"; 

        $(`input[name='reg_period'][value=@json($regPeriod)]`).prop('checked', true);
        $(`input[name='id_protection'][value=@json($regPeriod)]`).prop('disabled', false);

        if( @json(request()->protection) ){
            $(`input[name='id_protection'][value=@json(request()->protection)]`).prop('checked', true);
        }

        $('input[name=reg_period]').on('click', function(){

            $("input[name=id_protection]").each(function(){ 
                $(this).prop('checked', false).prop('disabled', true);;
            });

            if($(this).prop('checked') == true){
                $(this).parents('.card-body').find('input[type=radio]').last().prop('disabled', false)
            }
        });

    })(jQuery);
</script>
@endpush
