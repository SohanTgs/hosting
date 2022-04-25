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
                            <th>@lang('Type')</th>
                            <th>@lang('Pay Type')</th>
                            <th>@lang('Stock')</th>
                            <th>@lang('Domain Registration')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead> 
                        <tbody>
                            @forelse($groupByCategories as $groupCategory)
                                <tr>
                                    <td class="table--bg ct-td" colspan="100%">
                                        <div class="d-flex flex-wrap">
                                            <p class="font-weight-bold mr-1"><i class="las la-clipboard-list"></i> @lang('Service Category'):</p> 
                                            <p>{{ __($groupCategory->name) }}</p>
                                        </div>
                                    </td>
                                </tr> 

                                @php
                                    $products = $groupCategory->products; 
                                @endphp

                                @forelse($products as $product)
                                    <tr>
                                        <td data-label="@lang('Name')">
                                            <span class="font-weight-bold">{{ __($product->name) }}</span>
                                        </td>

                                        <td data-label="@lang('Type')">
                                            <span class="font-weight-bold"> 
                                                {{ productType()[$product->product_type] }}
                                            </span>
                                            {{ $product->module_type == 1 ? '(cPanel)' : null }}
                                        </td>

                                        <td data-label="@lang('Payment Type')">
                                            @if($product->payment_type == 1)
                                                @lang('One Time')
                                            @else 
                                                @lang('Recurring')
                                            @endif
                                        </td>
                                    
                                        <td data-label="@lang('Stock')">
                                            @if($product->stock_control == 1)
                                                {{ $product->stock_quantity }}
                                            @else 
                                                @lang('N/A') 
                                            @endif
                                        </td> 
                                    
                                        <td data-label="@lang('Domain Registration')">
                                            @if($product->domain_register == 1)
                                                <span class="badge badge--success">@lang('Yes')</span>
                                            @else 
                                                <span class="badge badge--danger">@lang('No')</span>
                                            @endif
                                        </td>
 
                                        <td data-label="@lang('Status')">
                                            @if($product->status == 1)
                                                <span class="badge badge--success">@lang('Enable')</span>
                                            @else 
                                                <span class="badge badge--danger">@lang('Disable')</span>
                                            @endif
                                        </td>  
                                        
                                        <td data-label="@lang('Action')">
                                            <div class="btn--group justify-content-end">
                                                <a class="icon-btn editBtn" data-toggle="tooltip" data-original-title="@lang('Edit')" href="{{ route('admin.product.update.page', $product->id) }}">
                                                    <i class="las la-edit text--shadow"></i>
                                                </a>
                                            </div>
                                        </td> 
                                    </tr>
                                @empty 
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
          
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" href="{{ route('admin.product.add.page') }}">
        <i class="fa fa-fw fa-plus"></i>@lang('Add New')
    </a>
@endpush

@push('style')
    <style>
        @media (max-width: 991px) {
            .table-responsive--md tr td.ct-td {
                padding-left: 25px !important;
                text-align: left !important;
            }
        }
    </style>
@endpush