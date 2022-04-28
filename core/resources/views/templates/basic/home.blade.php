@extends($activeTemplate.'layouts.frontend')
@section('content')



<div class="container mt-5">
    <div class="row">

        @include($activeTemplate.'partials.sidenav')
    
        <div class="col-md-9 mt-3 mt-md-0">  
            @if(request()->category)
                @php 
                    $cat = $active_service_categories->where('slug', request()->category)->first();
                    $products = $cat->products($filter = true)->get();
                @endphp
    
               <div class="row"> 
 
                    <div class="col-md-12"> 
                        <h1>{{ __($cat->name) }}</h1> 
                        <p>{{ __($cat->short_description) }}</p>
                    </div>

                    @forelse($products as $product)
                        <div class="col-md-6 form-group">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">  
                                    {{ __($product->name) }}
                                    <div> 
                                        @if($product->stock_control)
                                            <i class="fz-12">{{ $product->stock_quantity }} @lang('Available')</i>
                                        @endif
                                    </div> 
                                </div> 
                                <div class="card-body">
                                    <div class="row"> 

                                        <div class="col-md-7 fz-12">
                                            @php echo nl2br($product->description); @endphp
                                        </div>
                                        
                                        @php
                                            $price = $product->price;
                                        @endphp
              
                                        <div class="col-md-5 mt-3 mt-md-0"> 
                                            <p class="text-left text-md-center"> 
                                                {{ $general->cur_sym }}{{ pricing($product->payment_type, $price, $type = 'price') }} {{ __($general->cur_text) }}

                                                <span class="d-block">
                                                    {{ pricing($product->payment_type, $price, $type = 'price', $showText = true) }}
                                                </span>

                                                @php
                                                    $setup = pricing($product->payment_type, $price, $type = 'setupFee');
                                                @endphp
                                         
                                                @if($setup) 
                                                    <span class="fz-12">
                                                        {{ $general->cur_sym }}{{ $setup }}
                                                        {{ pricing($product->payment_type, $price, $type = 'setupFee', $showText = true) }}
                                                    </span> 
                                                @endif

                                            </p>
                                            <a href="{{ route('product.configure', $product->id) }}" class="btn btn-success"><i class="fa fa-shopping-bag"></i> @lang('Order Now')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-12 card-header mb-3">
                            @lang('Category does not contain any visible products')
                        </div>
                    @endforelse
               </div>

            @endif
        </div> 

    </div> 
</div>

    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif
@endsection
