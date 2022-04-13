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
                            <th>@lang('Configurable Sub Option')</th>
                            <th>@lang('Sort Order')</th>
                            <th>@lang('Status')</th> 
                            <th>@lang('Action')</th>
                        </tr>  
                        </thead>
                        <tbody>  
                            @forelse($subOptions as $subOption)
                                <tr>
                                    <td data-label="@lang('Configurable Sub Option')">
                                        <span class="font-weight-bold">{{ __($subOption->name) }}</span>
                                    </td>
                                
                                    <td data-label="@lang('Sort Order')">
                                       {{ $subOption->order }} 
                                    </td>

                                    <td data-label="@lang('Status')">
                                        @if($subOption->status == 1)
                                            <span class="badge badge--success">@lang('Enable')</span>
                                        @else 
                                            <span class="badge badge--danger">@lang('Disable')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <button class="icon-btn editBtn" data-toggle="tooltip" data-original-title="@lang('Edit')" data-data="{{ $subOption }}">
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
                {{ paginateLinks($subOptions) }}
            </div>
        </div>
    </div>
</div>
 
{{-- NEW MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Add New Option for ') {{ __($option->name) }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.configurable.group.add.sub.option') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $group->id }}" required name="group_id">
                <input type="hidden" value="{{ $option->id }}" required name="option_id">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Option Name')</label>
                                <input type="text" class="form-control" name="name" placeholder="@lang('Name')" required value="{{old('name')}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Sort Order')</label>
                                <input type="number" class="form-control" name="order" placeholder="@lang('Order')" required value="{{old('order') ?? 0}}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('One Time/Monthly')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="monthly_setup_fee" step="any" value="{{ old('monthly_setup_fee') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="monthly" step="any" value="{{ old('monthly') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Quarterly')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="quarterly_setup_fee" step="any" value="{{ old('quarterly_setup_fee') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="quarterly" step="any" value="{{ old('quarterly') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Semi-Annual')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" step="any" name="semi_annually_setup_fee" value="{{ old('semi_annually_setup_fee') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" step="any" name="semi_annually" value="{{ old('semi_annually') ?? 0 }}" />
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
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Annual')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="annually_setup_fee" step="any" value="{{ old('annually_setup_fee') ?? 0 }}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="annually" step="any" value="{{ old('annually') ?? 0 }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Biennial')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" step="any" name="biennially_setup_fee" value="{{ old('biennially_setup_fee') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" step="any" name="biennially" value="{{ old('biennially') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Triennial')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="triennially_setup_fee" step="any" value="{{ old('triennially_setup_fee') ?? 0 }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="triennially" step="any" value="{{ old('triennially') ?? 0 }}" />
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

{{-- EDIT MODAL --}} 
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Update Option for ') {{ __($option->name) }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.configurable.group.update.sub.option') }}" method="POST">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">

                    <div class="row">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Option Name')</label>
                                <input type="text" class="form-control" name="name" placeholder="@lang('Name')" required value="{{old('name')}}" required>
                            </div>
                       </div>
                       <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Sort Order')</label>
                                <input type="number" class="form-control" name="order" placeholder="@lang('Order')" required required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('One Time/Monthly')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="monthly_setup_fee" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="monthly" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4  mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Quarterly')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="quarterly_setup_fee" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="quarterly" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Semi-Annual')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="semi_annually_setup_fee" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="semi_annually" step="any" required/>
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
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Annual')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="annually_setup_fee" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="annually" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Biennial')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="biennially_setup_fee" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="biennially" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center font-weight-bold">@lang('Triennial')</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Setup')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="triennially_setup_fee" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Pricing')</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">{{ __($general->cur_sym) }}</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="0" name="triennially" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Status')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="status" checked>
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
    <a href="{{ route('admin.configurable.group.all.option', $group->id) }}" class="btn btn-sm btn--success box--shadow1 text--small">
        <i class="la la-fw fa-backward"></i>@lang('Go Back')
    </a>
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
                modal.find('input[name=order]').val(record.order);

                modal.find('input[name=monthly_setup_fee]').val(parseInt(record.price.monthly_setup_fee).toFixed(2));
                modal.find('input[name=monthly]').val(parseInt(record.price.monthly).toFixed(2));

                modal.find('input[name=quarterly_setup_fee]').val(parseInt(record.price.quarterly_setup_fee).toFixed(2));
                modal.find('input[name=quarterly]').val(parseInt(record.price.quarterly).toFixed(2));

                modal.find('input[name=semi_annually_setup_fee]').val(parseInt(record.price.semi_annually_setup_fee).toFixed(2));
                modal.find('input[name=semi_annually]').val(parseInt(record.price.semi_annually).toFixed(2));

                modal.find('input[name=annually_setup_fee]').val(parseInt(record.price.annually_setup_fee).toFixed(2));
                modal.find('input[name=annually]').val(parseInt(record.price.annually).toFixed(2));

                modal.find('input[name=biennially_setup_fee]').val(parseInt(record.price.biennially_setup_fee).toFixed(2));
                modal.find('input[name=biennially]').val(parseInt(record.price.biennially).toFixed(2));
                
                modal.find('input[name=triennially_setup_fee]').val(parseInt(record.price.triennially_setup_fee).toFixed(2));
                modal.find('input[name=triennially]').val(parseInt(record.price.triennially).toFixed(2));

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

 