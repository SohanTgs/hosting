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
                                <th>@lang('Product/Service')</th>
                                <th>@lang('Reason')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Cancellation By End')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead> 
                            <tbody>
                                @forelse($cancelRequests as $data)
                                <tr>
                                    <td data-label="@lang('Product/Service')">
                                        <span class="font-weight-bold">
                                            <a href="{{ route('admin.order.hosting.details', $data->service->id) }}">
                                                {{$data->service->product->serviceCategory->name}}
                                            </a>
                                        </span>
                                        <br> 
                                        <span class="small">
                                            <a href="{{ route('admin.users.detail', $data->service->user_id) }}">
                                                <span>@</span>{{ $data->service->user->username }}
                                            </a>
                                        </span>
                                    </td>
                                    <td data-label="@lang('Reason')">
                                        {{ shortDescription($data->reason, 50) }} 
                                    </td>
                                    <td data-label="@lang('Type')">
                                        <span class="font-weight-bold">{{ $data::type()[$data->type] }}</span>
                                    </td>
                                    <td data-label="@lang('Cancellation By End')">
                                        @if($data->type == 1)
                                            {{ showDateTime($data->created_at) }} <br> {{ diffForHumans($data->created_at) }}
                                        @else 
                                            {{ showDateTime($data->service->next_due_date) }} <br> {{ diffForHumans($data->service->next_due_date) }}
                                        @endif
                                    </td>
                                    <td data-label="@lang('Status')">
                                        @php
                                            echo $data->showStatus;
                                        @endphp
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <button class="icon-btn reason" data-original-title="@lang('Reason')" data-reason="{{ $data->reason }}">
                                            <i class="las la-eye text--shadow"></i>
                                        </button>

                                        @if($data->status == 2)
                                        <button class="icon-btn btn--warning cancelRequest" data-original-title="@lang('Mark as cancellation')" data-id="{{ $data->id }}">
                                            <i class="las la-ban text--shadow"></i>
                                        </button>
                                        @endif

                                        <button class="icon-btn btn--danger delete" data-original-title="@lang('Delete')" data-id="{{ $data->id }}">
                                            <i class="las la-trash text--shadow"></i>
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
                    {{ paginateLinks($cancelRequests) }}
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="cancelRequest" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Mark as Cancellation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.cancel.request') }}">
                @csrf
                <input type="hidden" name="id" required> 
                <div class="modal-body">
                    <p>@lang('Are you sure to cancel this service/product')?</p>
                </div> 
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-save" value="add">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Confirmation')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.cancel.request.delete') }}">
                @csrf
                <input type="hidden" name="id" required> 
                <div class="modal-body">
                    <p>@lang('Are you sure to delete this cancellation request')?</p>
                </div> 
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-save" value="add">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Reason for Cancellation Request')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
                <p class="view_reason"></p>
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('script')  
    <script>
        (function($){
            "use strict";

            $('.reason').on('click', function () {
                var modal = $('#reasonModal');
                modal.find('.view_reason').text($(this).data('reason'));
                modal.modal('show');
            });

            $('.delete').on('click', function () {
                var modal = $('#deleteModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.cancelRequest').on('click', function () { 
                var modal = $('#cancelRequest');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });
 
        })(jQuery);
    </script>
@endpush
