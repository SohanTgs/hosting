@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12"> 
        <div class="card">
            <form class="form-horizontal" method="post" action="{{ route('admin.product.update') }}">
                @csrf 
                <div class="modal-body">   
                    <div class="row"> 

                        <input type="hidden" name="id" value="{{ $product->id }}" required>
 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Product Type')</label>
                                <select name="product_type" class="form-control" required>
                                    <option value="" hidden>@lang('Select One')</option>
                                    @foreach (productType() as $index => $type)
                                        <option value="{{ $index }}">{{ __($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Service Category')</label>
                                <select name="service_category" class="form-control" required>
                                    <option value="" hidden>@lang('Select One')</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" data-slug="{{ $category->slug }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 mt-2">
                            <div class="card border--primary">
                                <h5 class="card-header bg--primary">@lang('Product Name & Slug')</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Name')</label>
                                                <input type="text" name="name" class="form-control readonly" required value="{{ $product->name }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="input-group has_append">
                                                <div class="justify-content-between d-flex w-100">
                                                    <label>@lang('Slug')</label>
                                                    <div class="slugValidatation d-none">
                                                       <i>
                                                            <span class="slugIcon"></span>
                                                            <small class="ajaxResponse">@lang('Validating')...</small>
                                                       </i>
                                                    </div>
                                                </div>
                                                <input type="text" name="slug" class="form-control readonly" required value="{{ $product->slug }}" oninput="this.value = this.value.replace(/[^a-z0-9\s -]/gi, '')">
                                                <small class="slugUrl w-100 mt-2">{{ route('home') }}/<span class="categorySlug">{{ $product->serviceCategory->slug }}</span>/<span class="productSlug">{{ $product->slug }}</span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
 
                        <div class="col-lg-6 mt-2">
                            <div class="card border--primary h-100">
                                <h5 class="card-header bg--primary">@lang('Assigned Configurable Groups')</h5>
                                <div class="card-body">
                                    <div class="input-group has_append mb-3">
                                        <select name="assigned_config_group[]" class="form-control configs_groups select-h-full" multiple="multiple">
                                            @foreach($configGroups as $configGroup)
                                                <option value="{{ $configGroup->id }}">{{ __($configGroup->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mt-3">
                            <div class="card border--primary h-100">
                                <h5 class="card-header bg--primary">@lang('Module Settings')</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Module Name')</label>
                                                <select name="module_type" class="form-control">
                                                @foreach (productModule() as $index => $module)
                                                    <option value="{{ $index }}">{{ __($module) }}</option>
                                                @endforeach
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Server Group')</label>
                                                <select name="server_group" class="form-control">
                                                    <option value="">@lang('None')</option>
                                                    @foreach ($serverGroups as $serverGroup)
                                                        <option value="{{ $serverGroup->id }}">{{ __($serverGroup->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="hidden" name="server_id" class="server_id">
                                            <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('WHM Package Name') 
                                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                </label>
                                                <select name="package_name" class="form-control">
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach($packages as $id => $package)
                                                        @foreach($package as $packageName)
                                                            <option value="{{ $packageName }}" {{ $packageName == $product->package_name ? 'selected' : null }} 
                                                                data-server_id="{{ $id }}">
                                                                {{ $packageName }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 

                        <div class="col-lg-6 mt-3">
                            <div class="card border--primary h-100">
                                <h5 class="card-header bg--primary">@lang('Select One for Module Setting')</h5>
                                <div class="card-body d-flex align-items-center">
                                    <div class="input-group has_append"> 
                                        @foreach(productModuleOptions() as $index => $data)
                                            <div>
                                                <input type="radio" name="module_option" id="{{ $index }}" value="{{ $index }}"> <label for="{{ $index }}" class="defaultLabel">{{ __($data) }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div> 

                        <div class="col-lg-3 col-md-6 mt-3">
                            <div class="card border--primary">
                                <h5 class="card-header bg--primary">@lang('Stock System')</h5>
                                <div class="card-body">
                                    <div class="input-group has_append mb-3">
                                        <label class="w-100">@lang('Stock Control')</label>
                                        <select name="stock_control" class="form-control">
                                            <option value="0" {{ $product->stock_control == 0 ? 'selected' : null }}>@lang('No')</option>
                                            <option value="1" {{ $product->stock_control == 1 ? 'selected' : null }}>@lang('Yes')</option>
                                        </select>
                                    </div>
                                    <div class="input-group has_append mb-3">
                                        <label class="w-100">@lang('Quantity in Stock')</label>
                                        <input type="number" name="stock_quantity" class="form-control" required value="{{ $product->stock_quantity }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mt-3">
                            <div class="card border--primary">
                                <h5 class="card-header bg--primary">@lang('Others')</h5>
                                <div class="card-body">
                                    <div class="row"> 
                                        <div class="col-lg-12">
                                            <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Welcome Email')</label>
                                                <select name="welcome_email" class="form-control" required>
                                                    <option value="0">@lang('Select One')</option>
                                                    @foreach (welcomeEmail() as $index => $mail) 
                                                        <option value="{{ $index }}">{{ __($mail['name']) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12">
                                            <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Domain Registration')</label>
                                                <select name="domain_registration" class="form-control" required>
                                                    <option value="0" {{ $product->domain_registration == 0 ? 'selected' : null }}>@lang('No')</option>
                                                    <option value="1" {{ $product->domain_registration == 1 ? 'selected' : null }}>@lang('Yes')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12">
                                            <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Status')</label>
                                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="status" @if($product->status) checked @endif>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  

                        <div class="col-lg-6 mt-3">
                            <div class="card border--primary h-100">
                                <h5 class="card-header bg--primary">@lang('Description')</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-group has_append">
                                                <textarea name="description" class="form-control" rows="6">{{ $product->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 

                        <div class="col-lg-12 mt-3">
                            <div class="card border--primary my-2">
                                <h5 class="card-header bg--primary">@lang('Payment Type') </h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="one_time"><input type="radio" name="payment_type" id="one_time" value="1"
                                            {{ $product->payment_type == '1' ? 'checked' : null }}> @lang('One Time')</label>
                                        / 
                                        <label for="recurring"><input type="radio" name="payment_type" id="recurring" value="2" 
                                            {{ $product->payment_type == '2' ? 'checked' : null }}> @lang('Recurring')</label>
                                        <div class="pricing mt-2">
                                            <div class="row">

                                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                                                    <div class="card border--success">
                                                        <h5 class="card-header bg--success">@lang('One Time/Monthly')</h5>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="monthly {{ $product->price->monthly < 0 ? 'd-none' : null }}">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Setup Fee')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span> 
                                                                                <input type="number" class="form-control" placeholder="0" name="monthly_setup_fee" value="{{ getAmount($product->price->monthly_setup_fee, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Price')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="monthly" value="{{ getAmount($product->price->monthly, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="input-group has_append mb-3">
                                                                        <label class="w-100">@lang('Status')</label>
                                                                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="monthly_status" {{ $product->price->monthly < 0 ? '' : 'checked' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3 recurring">
                                                    <div class="card border--success">
                                                        <h5 class="card-header bg--success">@lang('Quarterly')</h5>
                                                        <div class="card-body">
                                                            <div class="row ">
                                                                <div class="quarterly {{ $product->price->quarterly < 0 ? 'd-none' : null }}">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Setup Fee')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="quarterly_setup_fee" value="{{ getAmount($product->price->quarterly_setup_fee, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Price')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="quarterly" value="{{ getAmount($product->price->quarterly, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="input-group has_append mb-3">
                                                                        <label class="w-100">@lang('Status')</label>
                                                                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="quarterly_status" {{ $product->price->quarterly < 0 ? '' : 'checked' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3 recurring">
                                                    <div class="card border--success">
                                                        <h5 class="card-header bg--success">@lang('Semi-Annually')</h5>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="semi_annually {{ $product->price->semi_annually < 0 ? 'd-none' : null }}">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Setup Fee')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="semi_annually_setup_fee" value="{{ getAmount($product->price->semi_annually_setup_fee, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Price')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="semi_annually" value="{{ getAmount($product->price->semi_annually, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="input-group has_append mb-3">
                                                                        <label class="w-100">@lang('Status')</label>
                                                                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="semi_annually_status" {{ $product->price->semi_annually < 0 ? '' : 'checked' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>  

                                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3 recurring">
                                                    <div class="card border--success">
                                                        <h5 class="card-header bg--success">@lang('Annually')</h5>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="annually {{ $product->price->annually < 0 ? 'd-none' : null }}">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Setup Fee')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="annually_setup_fee" value="{{ getAmount($product->price->annually_setup_fee, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Price')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="annually" value="{{ getAmount($product->price->annually, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="input-group has_append mb-3">
                                                                        <label class="w-100">@lang('Status')</label>
                                                                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="annually_status" {{ $product->price->annually < 0 ? '' : 'checked' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3 recurring">
                                                    <div class="card border--success">
                                                        <h5 class="card-header bg--success">@lang('Biennially')</h5>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="biennially {{ $product->price->biennially < 0 ? 'd-none' : null }}">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Setup Fee')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="biennially_setup_fee" value="{{ getAmount($product->price->biennially_setup_fee, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Price')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="biennially" value="{{ getAmount($product->price->biennially, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="input-group has_append mb-3">
                                                                        <label class="w-100">@lang('Status')</label>
                                                                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="biennially_status" {{ $product->price->biennially < 0 ? '' : 'checked' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3 recurring">
                                                    <div class="card border--success">
                                                        <h5 class="card-header bg--success">@lang('Triennially')</h5>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="triennially {{ $product->price->triennially < 0 ? 'd-none' : null }}">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Setup Fee')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="triennially_setup_fee" value="{{ getAmount($product->price->triennially_setup_fee, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="input-group has_append mb-3">
                                                                            <label class="w-100">@lang('Price')</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-prepend">
                                                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                                                </span>
                                                                                <input type="number" class="form-control" placeholder="0" name="triennially" value="{{ getAmount($product->price->triennially, 2) }}" step="any" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                                <div class="col-lg-12">
                                                                    <div class="input-group has_append mb-3">
                                                                        <label class="w-100">@lang('Status')</label>
                                                                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="triennially_status" {{ $product->price->triennially < 0 ? '' : 'checked' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-block" id="btn-save" value="add">@lang('Submit')</button>
                </div>
            </form> 
        </div>
    </div>
</div>   
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" href="{{ route('admin.product.all') }}">
        <i class="la la-fw fa-backward"></i>@lang('Go Back')
    </a> 
@endpush 
   
@push('script')
<script>
    (function($){
        "use strict"; 

        $('select[name=server_group]').on('change', function(){
            var id = $(this).val();
            
            if(!id){
                return false;
            }

            $.ajax({
                type: 'post',
                url: '{{ route("admin.get.whm.package") }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'server_group_id': id
                },
                beforeSend: function(){
                    $('.spinner-border').removeClass('d-none');
                },
                complete: function(){
                    $('.spinner-border').addClass('d-none');
                },
                success: function (response){
           
                    $('select[name=package_name] option:not(:first)').remove();
         
                    if(response.success){ 
                        Object.entries(response.data).forEach(function(data, index){
                            data[1].forEach(function(value){
                                var name = value.split('_').pop();
                                $('select[name=package_name]').append($('<option>', {value: value, text: value}).attr('data-server_id', data[0]));
                            });
                        });
                    }else{
                        notify('error', response.message);
                    }
                },

            });

        });

        $('select[name=package_name]').on('change', function(){
            var serverId = $(this).children('option:selected').data('server_id');
            $('.server_id').val(serverId);
        }).change();

    })(jQuery);    
    </script> 
@endpush

@push('script') 
    <script>
        (function($){
            "use strict"; 

            var form = $('.form-horizontal');
            var product = @json($product);
            var paymentType = product.payment_type;
 
            if(paymentType == 1){
                $('.recurring').addClass('d-none'); 
            }else{
                $('.recurring').removeClass('d-none');
            }
 
            form.find('select[name=product_type]').val(product.product_type);
            form.find('select[name=service_category]').val(product.category_id);
            form.find('select[name=welcome_email]').val(product.welcome_email ?? 0);
            form.find('select[name=domain_registration]').val(product.domain_register);

            form.find('select[name=module_type]').val(product.module_type);
            form.find(`input[name=module_option][value=${product.module_option}]`).prop('checked', true);

            if(product.server_group_id){
                form.find('select[name=server_group]').val(product.server_group_id);
            }else{
                form.find('select[name=server_group] option:first').prop('selected', true);
            }
   
            if(product.get_configs){
                var configs = []; 
                for(var i = 0; i < product.get_configs.length; i++){
                    configs[i] = product.get_configs[i].configurable_group_id; 
                }
 
                form.find('.configs_groups').val(configs);
            } 

            form.find('input[name=payment_type]').on('change', function(){
       
                var value = $(this).val();

                if(value != 2){
                    return $('.recurring').addClass('d-none');
                }

                return $('.recurring').removeClass('d-none');
            });

            form.find('select[name=stock_control]').on('change',  function(){
                var value = $(this).val();

                if(value == 0){
                    return form.find('input[name=stock_quantity]').prop('readonly', true);
                }

                return form.find('input[name=stock_quantity]').prop('readonly', false);
            }).change();

            form.find('select[name=module_type]').on('change',  function(){
                var value = $(this).val();
                
                if(value == 0){
                    return form.find('input[name=module_option]').prop('disabled', true);
                }
                
                return form.find('input[name=module_option]').prop('disabled', false);
            }).change();

            var slugRule = /^[0-9a-zA-Z -]+$/; 

            form.find('input[name=name]').on('input',  function(){
                var input = $(this).val();
                var slug = makeSlug(input);
                var category = $('select[name=service_category]').val();

                if(!category){
                    return notify('info', 'Please select service category');
                }

                if(input.match(slugRule)){
                    form.find('input[name=slug]').val(slug);
                    return checkSlug(input, slug, category);
                }
            });

            form.find('input[name=slug]').on('input',  function(){
                var input = $(this).val();
                var slug = makeSlug(input);
                var category = $('select[name=service_category]').val();

                if(!category){
                    return notify('info', 'Please select service category');
                }

                if(input.match(slugRule)){
                    $(this).val(slug);
                    return checkSlug(input, slug, category);
                }
            });

            form.find('select[name=service_category]').on('change',  function(){
                var value = $(this).val();
                
                if(value){

                    form.find('.readonly').prop('readonly', true);
                    var name = form.find('input[name=name]').val();
                    var slug = form.find('input[name=slug]').val();

                    if(slug){
                        checkSlug(slug, makeSlug(slug), value);
                    }else if(name){
                        checkSlug(name, makeSlug(name), value);
                    }
                  
                    $('.categorySlug').text($(this).find(':selected').data('slug'));
                    return form.find('.readonly').prop('readonly', false);
                }

                form.find('.readonly').prop('readonly', true);

            });

            var triennially_status = form.find('input[name=triennially_status]');
            var biennially_status  = form.find('input[name=biennially_status]');
            var annually_status  = form.find('input[name=annually_status]');
            var semi_annually_status  = form.find('input[name=semi_annually_status]');
            var quarterly_status  = form.find('input[name=quarterly_status]');
            var monthly_status  = form.find('input[name=monthly_status]');

            triennially_status.on('change', function(){
                if(triennially_status.is(':checked')){
                    $('.triennially').removeClass('d-none');
                    $('.triennially').find('input[name=triennially]').val(0);
                }else{
                    $('.triennially').addClass('d-none');
                }
            });

            biennially_status.on('change', function(){
                if(biennially_status.is(':checked')){
                    $('.biennially').removeClass('d-none');
                    $('.biennially').find('input[name=biennially]').val(0);
                }else{
                    $('.biennially').addClass('d-none');
                }
            });

            annually_status.on('change', function(){
                if(annually_status.is(':checked')){
                    $('.annually').removeClass('d-none');
                    $('.annually').find('input[name=annually]').val(0);
                }else{
                    $('.annually').addClass('d-none');
                }
            });

            semi_annually_status.on('change', function(){
                if(semi_annually_status.is(':checked')){
                    $('.semi_annually').removeClass('d-none');
                    $('.semi_annually').find('input[name=semi_annually]').val(0);
                }else{
                    $('.semi_annually').addClass('d-none');
                }
            });

            quarterly_status.on('change', function(){ 
                if(quarterly_status.is(':checked')){
                    $('.quarterly').removeClass('d-none');
                    $('.quarterly').find('input[name=quarterly]').val(0);
                }else{
                    $('.quarterly').addClass('d-none');
                }
            });
 
            monthly_status.on('change', function(){
                if(monthly_status.is(':checked')){
                    $('.monthly').removeClass('d-none');
                    $('.monthly').find('input[name=monthly]').val(0);
                }else{
                    $('.monthly').addClass('d-none');
                }
            });

            function makeSlug(input){
                return input.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
            } 

            function checkSlug(input, slug, category){ 
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.check.slug") }}',
                    data: {
                        'input': input,
                        'model_type': 'product',
                        'category_id': category,
                        'product_id': product.id,
                        '_token': '{{ csrf_token() }}',
                    },

                    beforeSend: function() {
                        $('.slugValidatation').removeClass('d-none');
                        $('.slugValidatation').addClass('d-inline');
                        $('.slugIcon').html('<i class="fas fa-spinner fa-spin"></i>');
                        $('.ajaxResponse').text('Validating...');
                    },

                    success:function(response){ 
                        setTimeout(function() {
                            if(response.error){
                                $.each(response.error, function(key, value) {
                                    notify('error', value);
                                });
                                $('.slugValidatation').addClass('d-none');
                                $('.slugValidatation').removeClass('d-inline');
                            }
                            else if(!response.success){
                                $('.productSlug').text(slug);
                                $('.slugIcon').html('<i class="fas fa-times"></i>');
                                return $('.ajaxResponse').text(response.message);
                            }
                            else if(response.success){
                                $('.ajaxResponse').text(response.message);
                                $('.slugIcon').html('<i class="fas fa-check"></i>');
                                return $('.productSlug').text(slug);
                            }
                        }, 300);
                    }
                });
            }

            $('select[name=module_type]').on('change', function(){
                if( $(this).val() == 0 ){
                    $('select[name=package_name]').find('option:eq(0)').prop('selected', true);
                }else{
                    $('select[name=package_name] option[value=@json($product->package_name)]').prop('selected', true);
                }
            });

        })(jQuery);    
    </script> 
@endpush 

@push('style')
<style>
    .slugUrl span{ 
        border-bottom: 1px dashed;
    }
    .slugUrl{ 
        font-size: 15px;
    }
    .defaultLabel {
        font-size: initial;
    }  
    .select-h-full{
        height: 158px !important;
    }
    .toggle.btn-lg {
        min-height: 38px !important;
    }
</style>
@endpush

 