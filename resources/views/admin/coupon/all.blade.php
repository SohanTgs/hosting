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
                                <th>@lang('Name')</th>
                                <th>@lang('Coupon Code')</th>
                                <th>@lang('Discount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Used')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody> 
                            @forelse($coupons as $data)
                            <tr>

                                <td data-label="@lang('Name')">
                                    <span class="font-weight-bold">{{ __($data->name) }}</span>
                                </td>

                                <td data-label="@lang('Coupon Code')">
                                    <span class="font-weight-bold">{{ __($data->code) }}</span>
                                </td>

                                <td data-label="@lang('Discount')">
                                    @if($data->type == 0)
                                        {{ showAmount($data->discount, 2) }}%
                                    @else
                                        {{ showAmount($data->discount, 2) }} {{ __($general->cur_text) }}
                                    @endif
                                </td>

                                <td data-label="@lang('Status')">
                                    @if($data->status == 0)
                                        <span class="badge badge--warning">@lang('Disable')</span>
                                    @else
                                        <span class="badge badge--success">@lang('Enable')</span>
                                    @endif
                                </td>

                                <td data-label="@lang('Used')">
                                    <span class="font-weight-bold">{{ $data->used }}</span> @lang('Times')
                                </td>

                                <td data-label="@lang('Action')">
                                    <a href="#" class="icon-btn editBtn" data-toggle="tooltip" title="" data-original-title="@lang('Edit')"
                                    data-row="{{ $data }}"
                                    >
                                        <i class="las la-edit text--shadow"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}!</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($coupons) }}
                </div>
            </div>
        </div>
    </div> 

    {{-- ADD METHOD MODAL --}}
    <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Add New Coupon')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.coupon.add') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 form-group">
                                <label for="name">@lang('Name')</label>
                                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}" placeholder="@lang('Name')">
                            </div>
                            <div class="col-lg-12 form-group">
                                <label for="code">@lang('Coupon Code')</label>
                                <input type="text" name="code" id="code" class="form-control" required value="{{ old('code') }}" placeholder="@lang('Code')">
                            </div>
                            <div class="col-lg-12 form-group">
                                <label>@lang('Discount Type')</label>
                                <select name="type" class="form-control addType" required>
                                    <option value="0" {{ old('type') == 0 ? 'selected' : null }}>@lang('Percentage')</option>
                                    <option value="1" {{ old('type') == 1 ? 'selected' : null }}>@lang('Fixed')</option>
                                </select>
                            </div>
                            <div class="col-lg-12 form-group">
                                <label for="discount">@lang('Discount Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="discount" id="discount" class="form-control" required value="{{ old('discount') }}" placeholder="@lang('Discount Amount')">
                                    <div class="input-group-append">
                                        <span class="input-group-text addText">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group"> 
                                <label for="min_order_amount">@lang('Minmum Order Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="min_order_amount" id="min_order_amount" class="form-control" required value="{{ old('min_order_amount') }}" placeholder="@lang('Minmum Order Amount')">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT METHOD MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Update Coupon')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.coupon.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 form-group">
                                <label for="name">@lang('Name')</label>
                                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="col-lg-12 form-group">
                                <label for="code">@lang('Coupon Code')</label>
                                <input type="text" name="code" id="code" class="form-control" required value="{{ old('code') }}">
                            </div>
                            <div class="col-lg-12 form-group">
                                <label>@lang('Amount Type')</label>
                                <select name="type" class="form-control updateType" required>
                                    <option value="0">@lang('Percentage')</option>
                                    <option value="1">@lang('Fixed')</option>
                                </select>
                            </div>
                            <div class="col-lg-12 form-group">
                                <label for="discount">@lang('Discount Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="discount" id="discount" class="form-control" required value="{{ old('discount') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text updateText">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group">
                                <label for="min_order_amount">@lang('Minmum Order Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="min_order_amount" id="min_order_amount" class="form-control" required value="{{ old('min_order_amount') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="status">@lang('Status')</label>
                                    <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" id="status" data-on="@lang('Enable')" data-off="@lang('Disable')" name="status">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn--primary box--shadow1 text-white text--small addNew">
        <i class="fa fa-fw fa-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.addNew').on('click', function () {
                var modal = $('#addModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var row = $(this).data('row'); 

                modal.find('input[name=name]').val(row.name);
                modal.find('input[name=id]').val(row.id);
                modal.find('input[name=code]').val(row.code);
                modal.find('input[name=discount]').val(parseFloat(row.discount).toFixed(2));
                modal.find('input[name=min_order_amount]').val(parseFloat(row.min_order_amount).toFixed(2));
                modal.find('select[name=type]').val(row.type);

                if(row.type == 0){
                    modal.find('.updateText').text('%');
                }else{
                    modal.find('.updateText').text(@json(__($general->cur_text)));
                }

                if(row.status == 1){
                    modal.find('input[name=status]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                modal.modal('show');
            });

            $('.addType').on('change', function(){
                var value = $(this).val();
                var discount = $('.addText');

                if(value == 0){
                    discount.text('%');
                }else{
                    discount.text(@json(__($general->cur_text)));
                }
            });

            $('.updateType').on('change', function(){
                var value = $(this).val();
                var discount = $('.updateText');

                if(value == 0){
                    discount.text('%');
                }else{
                    discount.text(@json(__($general->cur_text)));
                }
            });

        })(jQuery);
    </script>
@endpush
