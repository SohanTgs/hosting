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
                            <th>@lang('Extension')</th>
                            <th>@lang('ID Protection')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($domains as $domain)
                                <tr>
                                    <td data-label="@lang('Extension')">
                                        <span class="font-weight-bold">{{ $domain->extension }}</span>
                                    </td>
                                    <td data-label="@lang('ID Protection')">
                                        <input type="checkbox" {{ $domain->id_protection ? 'checked' : null }}>
                                    </td>
                                    <td data-label="@lang('Status')">
                                        @if($domain->status == 1)
                                            <span class="badge badge--success">@lang('Enable')</span>
                                        @else 
                                            <span class="badge badge--danger">@lang('Disable')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <button class="icon-btn btn--success priceModal" data-toggle="tooltip" data-original-title="@lang('Pricing')" data-pricing="{{ $domain->pricing }}" data-id="{{ $domain->pricing->id }}">
                                            <i class="las la-money-bill-wave text--shadow"></i>
                                        </button>
                                        <button class="icon-btn editBtn" data-toggle="tooltip" data-original-title="@lang('Edit')" data-data="{{ $domain }}">
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
                {{ paginateLinks($domains) }}
            </div> 
        </div>
    </div>
</div>

{{-- NEW MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Add New Domain Extension')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.domain.add') }}">
                @csrf 
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Extension')</label>
                                <input type="text" class="form-control" name="extension" placeholder="@lang('.com')" value="{{old('extension')}}" required autocomplete="off">
                            </div>
                        </div> 
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('ID Protection')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="id_protection">
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

{{-- EDIT MODAL --}}  
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Update Domain Extension')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.domain.update') }}">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Extension')</label>
                                <input type="text" class="form-control" name="extension" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('ID Protection')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="id_protection">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Status')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="status" checked>
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

{{-- PRICE MODAL --}}  
<div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Domain Pricing')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.domain.update.pricing') }}" method="POST">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('One Year')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="one_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="one_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4  mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Two Year')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="two_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="two_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Three Year')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="three_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="three_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Four Year')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="four_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="four_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Five Year')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="five_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="five_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Six Year')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="six_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="six_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-block" id="btn-save">@lang('Submit')</button>
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

@push('style')
<style>
    .custom-pricing {
        background: #fafafa;
        padding: 30px 15px;
        border-radius: 5px;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
    }
    .custom-pricing .border-line-title {
        margin-top: 0;
    }
    .custom-pricing .form-control {
        background: white;
    }
</style>
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
                
                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=extension]').val(record.extension);

                if(record.id_protection == 1){
                    modal.find('input[name=id_protection]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=id_protection]').bootstrapToggle('off');
                }

                if(record.status == 1){
                    modal.find('input[name=status]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                modal.modal('show');
            });

            $('input[type=checkbox]').on('click', function(){
                return false;
            });

            $('.priceModal').on('click', function () {
                var modal = $('#priceModal');
                var pricing = $(this).data('pricing');

                modal.find('input[name=id]').val($(this).data('id'));

                modal.find('input[name=one_year_price]').val(parseFloat(pricing.one_year_price).toFixed(2));
                modal.find('input[name=one_year_id_protection]').val(parseFloat(pricing.one_year_id_protection).toFixed(2));
                
                modal.find('input[name=two_year_price]').val(parseFloat(pricing.two_year_price).toFixed(2));
                modal.find('input[name=two_year_id_protection]').val(parseFloat(pricing.two_year_id_protection).toFixed(2));

                modal.find('input[name=three_year_price]').val(parseFloat(pricing.three_year_price).toFixed(2));
                modal.find('input[name=three_year_id_protection]').val(parseFloat(pricing.three_year_id_protection).toFixed(2));

                modal.find('input[name=four_year_price]').val(parseFloat(pricing.four_year_price).toFixed(2));
                modal.find('input[name=four_year_id_protection]').val(parseFloat(pricing.four_year_id_protection).toFixed(2));

                modal.find('input[name=five_year_price]').val(parseFloat(pricing.five_year_price).toFixed(2));
                modal.find('input[name=five_year_id_protection]').val(parseFloat(pricing.five_year_id_protection).toFixed(2));

                modal.find('input[name=six_year_price]').val(parseFloat(pricing.six_year_price).toFixed(2));
                modal.find('input[name=six_year_id_protection]').val(parseFloat(pricing.six_year_id_protection).toFixed(2));

                modal.modal('show');
            });
 
        })(jQuery);
    </script>
@endpush

  