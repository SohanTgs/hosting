@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12"> 
        <div class="card">
            <form class="form-horizontal" method="post" action="{{ route('admin.product.add') }}">
                @csrf 
                <div class="modal-body">   
                    <div class="row"> 

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
                                                <input type="text" name="name" class="form-control readonly" required value="{{ old('name') }}" readonly>
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
                                                <input type="text" name="slug" class="form-control readonly" required value="{{ old('slug') }}" oninput="this.value = this.value.replace(/[^a-z0-9\s -]/gi, '')" readonly>
                                                <small class="slugUrl w-100 mt-2">{{ route('home') }}/<span class="categorySlug"></span><span class="productSlug">{{ old("slug") }}</span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
 
                        <div class="col-lg-6 mt-2">
                            <div class="card border--primary h-100">
                                <h5 class="card-header bg--primary">@lang('Module Settings')</h5>
                                <div class="card-body">
                                    <div class="input-group has_append mb-3">
                                        <label class="w-100">@lang('Module Name')</label>
                                        <select name="module_type" class="form-control">
                                           @foreach (productModule() as $index => $module)
                                                <option value="{{ $index }}">{{ __($module) }}</option>
                                           @endforeach
                                        </select>
                                    </div>
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
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="card border--primary">
                                <h5 class="card-header bg--primary">@lang('Select One for Module Setting')</h5>
                                <div class="card-body">
                                    <div class="input-group has_append">
                                        @foreach(productModuleOptions() as $index => $data)
                                            <div class="d-block w-100">
                                                <input type="radio" name="module_option" id="{{ $index }}" value="{{ $index }}"> <label for="{{ $index }}" class="defaultLabel">{{ __($data) }}</label>
                                            </div>
                                        @endforeach
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

            var form = $('.form-horizontal');

            var oldProductType = '{{ old("product_type") }}';
            var oldCategory = '{{ old("service_category") }}';

            if(oldProductType){
                $('select[name=product_type]').val(oldProductType);
            }
            if(oldCategory){
                $('select[name=service_category]').val(oldCategory);
                $('.categorySlug').text($('select[name=service_category]').find(':selected').data('slug')+'/');
            }

            form.find('select[name=module_type]').on('change',  function(){
                var value = $(this).val();
                
                if(value == 0){
                    return form.find('input[name=module_option]').prop('disabled', true);
                }
                
                return form.find('input[name=module_option]').prop('disabled', false);
            }).change();

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
                  
                    $('.categorySlug').text($(this).find(':selected').data('slug')+'/');
                    return form.find('.readonly').prop('readonly', false);
                }

                form.find('.readonly').prop('readonly', true);

            });

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
</style>
@endpush

 