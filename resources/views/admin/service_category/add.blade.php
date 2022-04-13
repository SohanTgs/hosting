@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.hosting.plan.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body"> 
                        <div class="payment-method-item">
                            <div class="">
                                <div class="row"> 
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="w-100">@lang('Name') <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="name" class="form-control border-radius-5" placeholder="@lang('Plan Name')"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="w-100">@lang('Category') <span class="text-danger">*</span></label>
                                            <select name="category" class="form-control">
                                                <option value="" hidden>@lang('Select One')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="w-100">@lang('Pricing')</label>

                                            <div class="input-group has_append">
                                                <input type="number" class="form-control" name="price" min="0" value="0">
                                                <div class="input-group-append">
                                                    <select name="category" class="form-control">
                                                        <option value="day">@lang('Day')</option>
                                                        <option value="month">@lang('Month')</option>
                                                        <option value="year">@lang('Year')</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-method-body"> 
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card border--primary">
                                            <h5 class="card-header bg--primary">@lang('Automation')</h5>
                                            <div class="card-body">
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Server/Hosting Provider') <span class="text-danger">*</span></label>
                                                    <select name="hosting_provider_id" class="form-control">
                                                        <option value="" hidden>@lang('Select One')</option>
                                                    </select>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Plan API Name') <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="@lang('API Name')" name="api_name" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mt-3 mt-lg-0">
                                        <div class="card border--primary mb-2">
                                            <h5 class="card-header bg--primary">@lang('Others')</h5>
                                            <div class="card-body">
                                                <div class="input-group has_append mb-4">
                                                    <label class="w-100">@lang('Require Domain Name')</label>
                                                    <input type="checkbox" data-width="100%" data-size="sm" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="en" checked>
                                                </div>
                                               
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Featured')</label>
                                                    <input type="checkbox" data-width="100%" data-size="sm" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="en" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="w-100">@lang('Features') <span class="text-danger">*</span></label>
                                            <select name="features[]" class="form-control select2-auto-tokenize"  multiple="multiple" required>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="card border--dark my-2">
                                            <h5 class="card-header bg--dark">@lang('Description') </h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <textarea rows="5" class="form-control border-radius-5 nicEdit" name="description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div><!-- card end -->
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
 
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('input[name=currency]').on('input', function () {
                $('.currency_symbol').text($(this).val());
            });
            $('.currency_symbol').text($('input[name=currency]').val());

            // $('.addUserData').on('click', function () {
            //     var html = `
            //     <div class="col-md-12 user-data">
            //         <div class="form-group">
            //             <div class="input-group mb-md-0 mb-4">
            //                 <div class="col-md-4">
            //                     <input name="field_name[]" class="form-control" type="text" required placeholder="@lang('Field Name')">
            //                 </div>
            //                 <div class="col-md-3 mt-md-0 mt-2">
            //                     <select name="type[]" class="form-control">
            //                         <option value="text" > @lang('Input Text') </option>
            //                         <option value="textarea" > @lang('Textarea') </option>
            //                         <option value="file"> @lang('File') </option>
            //                     </select>
            //                 </div>
            //                 <div class="col-md-3 mt-md-0 mt-2">
            //                     <select name="validation[]"
            //                             class="form-control">
            //                         <option value="required"> @lang('Required') </option>
            //                         <option value="nullable">  @lang('Optional') </option>
            //                     </select>
            //                 </div>
            //                 <div class="col-md-2 mt-md-0 mt-2 text-right">
            //                     <span class="input-group-btn">
            //                         <button class="btn btn--danger btn-lg removeBtn w-100" type="button">
            //                             <i class="fa fa-times"></i>
            //                         </button>
            //                     </span>
            //                 </div>
            //             </div>
            //         </div>
            //     </div>`;

            //     $('.addedField').append(html);
            // });


            $(document).on('click', '.removeBtn', function () {
                $(this).closest('.user-data').remove();
            });

        })(jQuery);


    </script>
@endpush
