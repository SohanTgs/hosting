@extends($activeTemplate.'layouts.frontend')
@section('content')

<div class="container mt-5">
 
        <div class="row">

            @include($activeTemplate.'partials.sidenav')

            @if($product->stock_control && !$product->stock_quantity)
                <div class="col-md-9 mt-3 mt-md-0">
                    <div class="col-md-12 card-header mb-3">
                        @lang('Out of Stock')
                    </div>
                </div>
            @else
                @if($product->domain_register)
                    <div class="col-md-9 domainArea">
                        <ul class="option-list">
                            <li class="option-list-item option-selected">
                                <button type="button" class="option-list-btn">
                                    <input type="radio" name="gridRadios" id="gridRadios2" checked>
                                    <label for="gridRadios2">@lang('Register a new domain')</label>
                                </button>
                                <form action="" class="register_domain_form">
                                    <div class="option-list-body">
                                        <div class="custom--chek">
                                            <div class="custom-input-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">
                                                            @lang('WWW').
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control domain_name" name="domain_name" required>
                                                </div>
                                                <div class="custom--select">
                                                    <select class="form-control extension" name="extension" required>
                                                        @foreach($domains as $singleDomain)
                                                            <option value="{{ $singleDomain->extension }}">{{ $singleDomain->extension }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-info">@lang('Check')</button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <li class="option-list-item">
                                <button type="button" class="option-list-btn">
                                    <input type="radio" name="gridRadios" id="gridRadios3">
                                    <label for="gridRadios3">@lang('I will use my existing domain and update my nameservers')</label>
                                </button>
                                <form action="" class="domain_form">
                                    <div class="option-list-body">
                                        <div class="custom--chek">
                                            <div class="custom-input-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">
                                                            @lang('WWW.')
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control domain_name" placeholder="@lang('example')" name="domain_name" required>
                                                </div>
                                                <div class="custom--select">
                                                    <input type="text" class="form-control extension" placeholder="@lang('com')" required name="extension">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-info">@lang('Use')</button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        </ul>

                        <div class="text-center mt-4 availability"></div>

                        <div class="row mt-4 showAvailability"></div>
                    </div>
                @endif
                 
                <div class="col-md-9 {{ $product->domain_register ? 'd-none hideElement' : null }}">
                    <form action="{{ route('user.shopping.cart.add') }}">
                        <input type="hidden" name="product_id" value="{{ $product->id }}" required>

                        <input type="hidden" name="domain_id" value="0" required class="domain_id">

                        <input type="hidden" name="domain" class="domain">
                        <div class="row">
                            <div class="col-md-7 mt-3 mt-md-0"> 
                                <div class="row"> 
                                    <div class="col-md-12"> 
                                        <h1>@lang('Product Configure')</h1> 
                                        <p>@lang('Configure your desired options and continue to checkout')</p>
                                    </div>
            
                                    @php
                                        $price = $product->price;
                                    @endphp
                                    
                                    <div class="col-md-12 form-group">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="col-md-12"> 
                                                    <h4>{{ __($product->name) }}</h4> 
                                                    <div class="fz-12">@php echo nl2br($product->description); @endphp</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 {{ $product->payment_type == 1 ? 'd-none' : '' }}">
                                        <div class="form-group"> 
                                            <label>@lang('Choose Billing Type')</label>
                                            <select name="billing_type" class="form-control"> 
                                                @php
                                                    echo pricing($product->payment_type, $price);
                                                @endphp 
                                            </select>
                                        </div>
                                    </div> 
            
                                    @php
                                        $configs = $product->getConfigs;
                                    @endphp
                    
                                    @if(count($configs))
                                    
                                    @endif
            
                                    @foreach($configs as $config)
            
                                        @php
                                            $group = $config->activeGroup; 
                                            $options = $group->activeOptions;
                                        @endphp
            
                                            @foreach($options->sortBy('order') as $option)
                                                @php
                                                    $subOptions = $option->activeSubOptions;
                                                @endphp
            
                                                @if(count($subOptions))
                                                    <div class="col-md-6">
                                                        <div class="form-group"> 
                                                            <label>{{ __($option->name) }}</label> 
                                                            <select name="config_options[{{ $option->id }}]" class="form-control options" data-type='' data-name="{{ __($option->name) }}"> 
                                                                <option hidden value="0">@lang('Select One')</option>
                                                                @foreach($subOptions->sortBy('order') as $subOption)
                                                                    <option value="{{ $subOption->id }}" data-price='{{ $subOption->getOnlyPrice }}' data-text='{{ __($subOption->name) }}'>
                                                                        {{ __($subOption->name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div> 
                                                    </div> 
                                                @endif
            
                                            @endforeach
                                            
                                    @endforeach
                                    
                                </div>
                            </div> 
             
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header order--bg text-center">@lang('Order Summary')</div>
                                    <div class="card-body pb-2">
                                        <div>
                                            <b>{{ __($product->name) }}</b>
                                            <span class="d-block"><i>{{ $product->serviceCategory->name }}</i></span>
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-between mt-3">
                                                <span>{{ __($product->name) }}</span>
                                                <span>{{ $general->cur_sym }}<span class="basicPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span> {{ __($general->cur_text) }}</span>
                                            </div>
            
                                            <div class="configurablePrice"></div>
            
                                        </div>
                                        <div class="calculatePrice border-top border-bottom mt-3">
                                            <div class="d-flex justify-content-between">
                                                <span>@lang('Setup Fees'):</span>
                                                <span>{{ $general->cur_sym }}<span class="setupFee">{{ pricing($product->payment_type, $price, $type = 'setupFee') }}</span> {{ __($general->cur_text) }}</span>
                                            </div> 
                                            <div class="d-flex justify-content-between">
                                                <span class="billingType">{{ pricing($product->payment_type, $price, $type = 'price', $showText = true) }}:</span>
                                                <span>{{ $general->cur_sym }}<span class="billingPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span> {{ __($general->cur_text) }}</span>
                                            </div>
                                        </div>
                                        <h4 class="justify-content-end d-flex mt-2">
                                            {{ $general->cur_sym }}<span class="finalAmount">{{ pricing($product->payment_type, $price, $type = 'price') + pricing($product->payment_type, $price, $type = 'setupFee') }} </span>
                                            {{ __($general->cur_text) }}
                                        </h4>
                                    </div> 
                                </div> 
                                <div class="text-center mt-3">  
                                    <button type="submit" class="btn bg-info btn-lg text-white">
                                        @lang('Continue') <i class="fa fa-arrow-circle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                <div>
            @endif 
        </div>
    
</div>

@endsection
  
@push('style')
<style> 
    .form-control:focus {
        box-shadow: none;
    }
    .option-list-body {
        display: none;
        padding: 15px;
        background: #00000003;
    }
    .option-list-item.option-selected .option-list-body {
        display: block;
    }
    .custom--select {
        min-width: 120px;
        flex-shrink: 0;
    }
    .custom--chek {
        display: flex;
        flex-wrap: wrap;
        align-content: center;
        gap: 1.5rem;
    }
    .custom--chek button {
        flex-shrink: 0;
    }
    .custom-input-group {
        display: flex;
        align-content: center;
        gap: .5rem;
        flex-grow: 1;
    }
    .custom-input-group .input-group {
        flex-grow: 1;
    }
    .option-list {
        margin: 0;
        padding: 0;
        list-style-type: none;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .option-list-btn {
        display: block;
        border: none;
        width: 100%;
        padding: 10px 15px;
        background: #00000008;
        text-align: start;
    }
    .option-list-btn:focus {
        outline: none;
    }
    .option-list-btn label {
        display: block !important;
        margin-bottom: 0;
    }

    [type="radio"]:checked,
    [type="radio"]:not(:checked) {
    position: absolute;
    left: -9999px;
    }
    [type="radio"]:checked + label,
    [type="radio"]:not(:checked) + label {
    position: relative;
    padding-left: 30px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: #666;
    }
    [type="radio"]:checked + label:before,
    [type="radio"]:not(:checked) + label:before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 20px;
    height: 20px;
    border: 1px solid #ddd;
    border-radius: 100%;
    background: #fff;
    }
    [type="radio"]:checked + label:after,
    [type="radio"]:not(:checked) + label:after {
    content: "";
    width: 10px;
    height: 10px;
    background: #2489C5;
    position: absolute;
    top: 5px;
    left: 5px;
    border-radius: 100%;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
    }
    [type="radio"]:not(:checked) + label:after {
    opacity: 0;
    -webkit-transform: scale(0);
    transform: scale(0);
    }
    [type="radio"]:checked + label:after {
    opacity: 1;
    -webkit-transform: scale(1);
    transform: scale(1);
    }

    .order--bg{
        background: #666;
        color: #fff;
    }
    .allOption{
        height: 0;
        border-top: 1px solid #ddd;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 30px;
    } 
    .allOption div{
        display: inline-block;
        position: relative;
        padding: 0 17px;
        top: -13px;
        font-size: 16px;
        color: #058;
        background: white;
    }
    .capitalize{
        text-transform: capitalize;
    }
</style>
@endpush 

@push('script')
<script>
    (function ($) {
        "use strict";
        
        var optionList = $('.option-list-item');
        optionList.on('click', function(){
            $(this).addClass('option-selected');
            $(this).siblings().removeClass('option-selected');
        })

        var product = @json($product);

        if(product.domain_register){

            var domains = @json($domains); 
            var general = @json($general); 
            var hideElement = $('.hideElement');
            var domainArea = $('.domainArea');

            $('.register_domain_form').on('submit', function(e){
                e.preventDefault();

                var domainName = $(this).find('.domain_name').val();
                var extension = $(this).find('.extension :selected').val();
            
                $('.showAvailability').empty();
                $('.availability').empty();

                $(domains).each(function(index, value){
                    var domain = domainName+value.extension;
                    checkDomain(domain, value, extension); 
                });

            });

            $('.domain_form').on('submit', function(e){
                e.preventDefault(); 

                var domainName = $(this).find('.domain_name').val();
                var extension = $(this).find('.extension').val();
                var domain = domainName+'.'+extension;
                
                var regexDomain = /^\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                if(domain.match(regexDomain)){
                    $('.domain').val(domain);
                    $('.domain_id').val(0);
                    hideElement.removeClass('d-none');
                    domainArea.addClass('d-none');
                }

            });

            function checkDomain(domain, data = null, extension = null){
     
                var apiKey = @json($general->api_key);

                $.ajax({
                    url: "https://domain-availability.whoisxmlapi.com/api/v1",
                    data: {apiKey: apiKey, domainName: domain},
                    success: function(success){

                        var respnoe = success.DomainInfo.domainAvailability;
                        var value = data;
                        var button = '';
                       
                        if(extension == value.extension){
                            var text = '';
                            if(respnoe == 'AVAILABLE'){
                                text = `<h3>@lang('Congratulations')! <span class='text-success'>${domain}</span> is @lang('available')!<h3>`;
                            }else{
                                text = `<h3><span class='text-danger'>${domain}</span> is @lang('unavailable')<h3>`;
                            }
                 
                            $('.availability').html(text); 
                        }

                        if(respnoe == 'AVAILABLE'){
                            button = `<button class="btn btn-info w-100 btn-sm registerDomainBtn" data-domain="${domain}" data-id="${value.id}">@lang('Add')</button>`;
                        }else{
                            button = `<button class="btn btn-dark w-100 btn-sm disabled">@lang('Unavailable')</button>`;
                        }

                        var html = `<div class="col-md-4 mt-3">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <b>${value.extension}</b>
                                                <div>${general.cur_sym}${parseFloat(value.pricing.firstPrice['price']).toFixed(2)} ${general.cur_text}</div>
                                                ${button}
                                            </div>
                                        </div>
                                    </div>`;

                        $('.showAvailability').append(html);
                    },
                    error: function(error){
                        $('.availability').html(`<h4 class='text-danger'>${error.responseJSON.messages}<h4>`);
                    }
                });

            }

            $(document).on('click', '.registerDomainBtn', function(){
                $('.domain').val($(this).data('domain'));
                $('.domain_id').val($(this).data('id'));
                hideElement.removeClass('d-none');
                domainArea.addClass('d-none');
            });

        }

    })(jQuery);
</script>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict"; 

        var product = @json($product);
        var productPrice = @json($product->price);
        var allOptions = $('.options');

        var globalSetup = "{{ pricing($product->payment_type, @$price, $type = 'setupFee') }}";
        var addingSetupFee = 0;

        var globalPrice = "{{ pricing($product->payment_type, @$price, $type = 'price') }}";
        var addingPrice = 0;

        var basicPrice = $('.basicPrice');

        var billingType = $('.billingType');
        var setupFee = $('.setupFee');
        var billingPrice = $('.billingPrice');

        var finalAmount = $('.finalAmount');

        var info = '';
        
        $('select[name=billing_type]').on('change', function() {
            var value = $(this).val();

            var price =  pricing(productPrice, 'price', value);
            var setup =  pricing(productPrice, 'setupFee', value);
            var type = pricing(0, null, value);
        
            var totalPriceForSelectedItem = pricing(productPrice, null, value);

            billingType.text(type);
            basicPrice.text(price);
            billingPrice.text(price);
            setupFee.text(setup);

            finalAmount.text(totalPriceForSelectedItem);
            allOptions.attr('data-type', value);
           
            globalSetup = setup;
            globalPrice = price;

            showSelect(value);

        }).change(); 

        allOptions.on('change', function(){

            var column = $(this).attr('data-type');
            var getPrice = $(this).find(":selected").data('price');
            var setup = getPrice[column+'_setup_fee'];

            showSelect(column, false);
        });

        function pricing(price, type, column){ 
            try{ 

                if(!price){
                    column = column.replaceAll('_', ' ');

                    if(product.payment_type == 1){
                        column = 'One Time:';
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

        function showSelect(value, showDropdown = true){
            
            try{

                addingSetupFee = 0;
                addingPrice = 0;

                var getColumn = value;
                var getFeeColumn = value+'_setup_fee';

                allOptions.each(function(index, data) {
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
                            var display = product.payment_type == 1 ? 'One Time' : pricing(0, null, getColumn);

                            if(product.payment_type == 1){
                                getColumn = 'monthly'
                            }

                            if(priceForThisItem[getFeeColumn] > 0){
                                optionSetupFee = ` + ${general.cur_sym}${getAmount(priceForThisItem[getFeeColumn])} ${general.cur_text} Setup Fee`
                            }
        
                            dropdownOptions = `${general.cur_sym}${getAmount(priceForThisItem[getColumn])} ${general.cur_text} ${display} ${optionSetupFee}`;

                            finalText = mainText+' '+dropdownOptions;
                            
                            if(showDropdown){
                                dropdown.text(finalText);
                            }
                            
                        }
 
                        if(dropdown.filter(':selected').attr('data-price')){
                           
                            var configurableOption =  $('.configurablePrice')
                            configurableOption.empty();
                            
                            info += `<div class='d-flex justify-content-between fz-12 mt-2 flex-wrap'>
                                        <span><i class='fa fa-angle-double-right'></i> ${$(data).data('name')}:</span> 
                                        <span>${finalText}</span>
                                    </div>`

                            configurableOption.append(info);
                            
                            addingSetupFee = sum(addingSetupFee, priceForThisItem[getFeeColumn]);
                            addingPrice = sum(addingPrice, priceForThisItem[getColumn]);

                            setupFee.text(sum(addingSetupFee, globalSetup));
                            billingPrice.text(sum(addingPrice, globalPrice));

                            finalAmount.text( sum(sum(addingSetupFee, globalSetup), sum(addingPrice, globalPrice)) );
                        }

                    });

                });

                info = '';

            }catch(message){
                console.log(message);
            }

        }

        function sum(param1, param2){
            var amount = parseFloat(param1) + parseFloat(param2);
            return getAmount(amount);
        }

        @if(shoppingCart())
            var editId = '{{ request()->id }}';
            var editBilling = '{{ request()->billing_type }}';
            var oldCart = null;

            if(editId && editBilling){

                var productId = @json($product->id);
                var getSession = @json(shoppingCart('get'));
                
                $(getSession).each(function(index, value){

                    if(value['billing_type'] == editBilling && value['product_id'] == editId){
                        oldCart = value;
                    }

                });

                var length = Object.keys(oldCart.config_options).length;

                for(var i = 0; i <= length; i++){
                    var selectName = Object.keys(oldCart.config_options)[i];
                    var selectOption = Object.values(oldCart.config_options)[i];
                    
                    $(`select[name='config_options[${selectName}]'] option[value=${selectOption}]`).prop('selected', true);
            
                    var column = oldCart['billing_type'];

                    $(`select[name=billing_type] option[value=${column}]`).prop('selected', true);
                    var price =  pricing(productPrice, 'price', column);
                    var setup =  pricing(productPrice, 'setupFee', column);
                    var type = pricing(0, null, column);

                    var totalPriceForSelectedItem = pricing(productPrice, null, column);

                    billingType.text(type);
                    basicPrice.text(price);
                    billingPrice.text(price);
                    setupFee.text(setup);

                    finalAmount.text(totalPriceForSelectedItem);
                    allOptions.attr('data-type', column);
                
                    globalSetup = setup;
                    globalPrice = price;

                    showSelect(column, false);
                }
                    

            }
        @endif

    })(jQuery);
</script>
@endpush

