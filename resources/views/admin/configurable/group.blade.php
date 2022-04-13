@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead> 
                        <tr>
                            <th>@lang('Group Name')</th>
                            <th>@lang('Configurable Options')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>  
                        <tbody>
                            @forelse($groups as $group)  
                                <tr>
                                    <td data-label="@lang('Group Name')">
                                        <span class="font-weight-bold">{{ __($group->name) }}</span>
                                    </td>
                                
                                    <td data-label="@lang('Configurable Options')">
                                        <span class="font-weight-bold">{{ @$group->options->count() }}</span>
                                    </td>

                                    <td data-label="@lang('Status')">
                                        @if($group->status == 1)
                                            <span class="badge badge--success">@lang('Enable')</span>
                                        @else 
                                            <span class="badge badge--danger">@lang('Disable')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <div class="btn--group justify-content-end">
                                            <a href="{{ route('admin.configurable.group.all.option', $group->id) }}" class="icon-btn btn--success" data-toggle="tooltip" data-original-title="@lang('Configurable Options')">
                                                <i class="las la-clipboard-list text--shadow"></i>
                                            </a>
                                            <button class="icon-btn editBtn" data-toggle="tooltip" data-original-title="@lang('Edit')" data-data="{{ $group }}">
                                                <i class="las la-edit text--shadow"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            <div class="card-footer py-4">
                {{ paginateLinks($groups) }}
            </div>
        </div>
    </div>
</div> 

{{-- NEW MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Add New Configurable Group')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.add') }}">
                @csrf 
                <div class="modal-body"> 
                    <div class="form-group">
                        <label>@lang('Name')</label> 
                        <input type="text" class="form-control add_name" name="name" placeholder="@lang('Name')" required value="{{old('name')}}" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Assigned Products')</label>
                        <select name="assigned_product[]" class="form-control select-h-custom productsId" multiple="multiple">
                            <option value="0" hidden>@lang('Select One')</option>
                             @foreach($products as $product) 
                                <option value="{{ $product->id }}">{{ __($product->name) }} - {{ __(@$product->serviceCategory->name) }}</option>
                             @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="description" class="form-control" required>{{old('description')}}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-block" id="btn-save" value="add">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Update Configurable Group')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.update') }}">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body"> 
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control edit_name" name="name" placeholder="@lang('Name')" required required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Assigned Products')</label>
                        <select name="assigned_product[]" class="form-control select-h-custom productsId" multiple="multiple">
                            <option value="0" hidden>@lang('Select One')</option>
                             @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ __($product->name) }} - {{ __(@$product->serviceCategory->name) }}</option>
                             @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>@lang('Status')</label>
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="status" checked>
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
    <button class="btn btn-sm btn--primary box--shadow1 text-white text--small addBtn">
        <i class="fa fa-fw fa-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";

            $('.addBtn').on('click', function () {
                var modal = $('#createModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var record = $(this).data('data');
                
                if(record.get_products){
                    var productsId = []; 
                    for(var i = 0; i < record.get_products.length; i++){
                        productsId[i] = record.get_products[i].product_id; 
                    }
 
                    modal.find('.productsId').val(productsId);
                } 

                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=name]').val(record.name);
                modal.find('textarea[name=description]').val(record.description);
                modal.find('select[name=service_category_id]').val(record.service_category_id);

                if(record.status == 1){
                    modal.find('input[name=status]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .select-h-custom{
            height: 110px !important;
        }
    </style>
@endpush