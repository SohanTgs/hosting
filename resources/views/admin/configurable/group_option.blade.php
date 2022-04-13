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
                            <th>@lang('Configurable Option')</th>
                            <th>@lang('Configurable Sub Option')</th>
                            <th>@lang('Sort Order')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead>
                        <tbody>
                            @forelse($options as $option)
                                <tr>
                                    <td data-label="@lang('Configurable Option')">
                                        <span class="font-weight-bold">{{ __($option->name) }}</span>
                                    </td>

                                    <td data-label="@lang('Configurable Sub Option')">
                                        <span class="font-weight-bold">{{ @$option->subOptions->count() }}</span>
                                    </td>

                                    <td data-label="@lang('Sort Order')">
                                       {{ $option->order }}
                                    </td>
                                
                                    <td data-label="@lang('Status')">
                                        @if($option->status == 1)
                                            <span class="badge badge--success">@lang('Enable')</span>
                                        @else 
                                            <span class="badge badge--danger">@lang('Disable')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <a  href="{{ route('admin.configurable.group.all.sub.option', [$group->id, $option->id]) }}" class="icon-btn btn--success" data-toggle="tooltip" data-original-title="@lang('Configurable Sub options')">
                                            <i class="las la-list text--shadow"></i>
                                        </a>
                                        <button class="icon-btn editBtn" data-toggle="tooltip" data-original-title="@lang('Edit')" data-data="{{ $option }}">
                                            <i class="las la-edit text--shadow"></i>
                                        </button>
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
                {{ paginateLinks($options) }}
            </div>
        </div>
    </div>
</div>

{{-- NEW MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Add New Option')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.add.option') }}">
                @csrf
                <input type="hidden" value="{{ $group->id }}" required name="group_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" placeholder="@lang('Name')" required value="{{old('name')}}" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Sort Order')</label>
                        <input type="number" class="form-control" name="order" placeholder="@lang('Order')" required value="{{old('order') ?? 0}}" required>
                    </div>
                    <div class="form-group d-none">
                        <label>@lang('Option Type')</label>
                        <select name="option_type" class="form-control">
                            <option value="1">@lang('Dropdown')</option>
                        </select>
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
                <h4 class="modal-title" id="createModalLabel">@lang('Update Option')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.update.option') }}">
                @csrf
                <input type="hidden" value="{{ $group->id }}" required name="group_id">
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control edit_name" name="name" placeholder="@lang('Name')" required required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Sort Order')</label>
                        <input type="number" class="form-control" name="order" placeholder="@lang('Order')" required required>
                    </div>
                    <div class="form-group d-none">
                        <label>@lang('Option Type')</label>
                        <select name="option_type" class="form-control">
                            <option value="1">@lang('Dropdown')</option>
                        </select>
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

            var pageTitle = '{{ $pageTitle }}';
            var groupName = pageTitle.substring(pageTitle.indexOf('for') + 4);
            var withOutGroup = pageTitle.split(groupName)[0];
            pageTitle = `${withOutGroup}<span class='text--primary'>${groupName}</span>`; 
            $('.page-title').html(pageTitle);

            $('.addBtn').on('click', function () {
                var modal = $('#createModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var record = $(this).data('data');
             
                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=name]').val(record.name);
                modal.find('select[name=option_type]').val(record.option_type);
                modal.find('input[name=order]').val(record.order);

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

  