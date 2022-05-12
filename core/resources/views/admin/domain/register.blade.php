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
                            <th>@lang('Service Provider')</th>
                            <th>@lang('Test Mode')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Default')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($domainRegisters as $data)
                                <tr>
                                    <td data-label="@lang('Service Provider')">
                                        <span class="font-weight-bold">{{ $data->name }}</span>
                                    </td>
                                    <td data-label="@lang('Test Mode')">
                                        <input type="checkbox" {{ $data->test_mode ? 'checked' : null }}>
                                    </td>
                                    <td data-label="@lang('Status')">
                                        @if($data->status == 1)
                                            <span class="badge badge--success">@lang('Enable')</span>
                                        @else 
                                            <span class="badge badge--danger">@lang('Disable')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Default')">
                                        @if($data->default == 1)
                                            <span class="badge badge--success">@lang('Default')</span>
                                        @else 
                                            <span class="badge badge--warning">@lang('Selectable')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <button class="icon-btn btn--primary configBtn" data-toggle="tooltip" data-original-title="@lang('Config')"
                                        data-data="{{ $data }}"
                                        >
                                            <i class="las la-cogs text--shadow"></i>
                                        </button>
                                        @if($data->status == 0)
                                            <button type="button"
                                                    class="icon-btn btn--success ml-1 activateBtn"
                                                    data-toggle="modal" data-target="#activateModal"
                                                    data-id="{{ $data->id }}" 
                                                    data-name="{{ __($data->name) }}"
                                                    data-original-title="@lang('Enable')">
                                                <i class="la la-eye"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="icon-btn btn--danger ml-1 deactivateBtn"
                                                    data-toggle="modal" data-target="#deactivateModal"
                                                    data-id="{{ $data->id }}"
                                                    data-name="{{ __($data->name) }}"
                                                    data-original-title="@lang('Disable')">
                                                <i class="la la-eye-slash"></i>
                                            </button>
                                        @endif
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
                {{ paginateLinks($domainRegisters) }}
            </div> 
        </div>
    </div>
</div>

{{-- CONFIG --}}
<div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Update Configuration'): <span class="provider-name"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.register.domain.update') }}">
                @csrf 
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="row">

                        <div class="configFields w-100"></div> 

                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" name="test_mode" id="test_mode">
                                <label for="test_mode">@lang('Test Mode')</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Status')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="status">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Default Domain Register')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Default')" data-off="@lang('Unset')" name="default">
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

{{-- ACTIVATE METHOD MODAL --}}
<div id="activateModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Confirmation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.register.domain.change.status') }}" method="POST">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <p>@lang('Are you sure to activate') <span class="font-weight-bold provider-name"></span> @lang('domain register')?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DEACTIVATE METHOD MODAL --}}
<div id="deactivateModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Confirmation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.register.domain.change.status') }}" method="POST">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <p>@lang('Are you sure to disable') <span class="font-weight-bold provider-name"></span> @lang('domain register')?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        (function($){
            "use strict";  

            $('.configBtn').on('click', function () {
                var modal = $('#configModal'); 
                var data = $(this).data('data');
                var appendArea = modal.find('.configFields');
                appendArea.empty();

                modal.find('.provider-name').text(data.name);
                modal.find('input[name=id]').val(data.id);

                if(data.test_mode == 1){
                    modal.find('input[name=test_mode]').prop('checked', true);
                }else{
                    modal.find('input[name=test_mode]').prop('checked', false);
                }

                if(data.status == 1){
                    modal.find('input[name=status]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                if(data.default == 1){
                    modal.find('input[name=default]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=default]').bootstrapToggle('off');
                }

                for(var [key, item] of Object.entries(data.params)){
                    appendArea.append(`
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class='capitalize'>${item.title}</label>
                                <input type='text' class='form-control' placeholder='${key}' value='${item.value}' name='${key}'>
                            </div>
                        </div>
                    `);
                }

                modal.modal('show');
            });

            $('table input[type=checkbox]').on('click', function(){
                return false;
            });

            $('.activateBtn').on('click', function () {
                var modal = $('#activateModal');
                modal.find('.provider-name').text($(this).data('name'));
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.deactivateBtn').on('click', function () {
                var modal = $('#deactivateModal');
                modal.find('.provider-name').text($(this).data('name'));
                modal.find('input[name=id]').val($(this).data('id'));
            });

        })(jQuery);
    </script>
@endpush





