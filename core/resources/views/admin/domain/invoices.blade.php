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
                                <th>@lang('Invoice')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Invoice Date')</th>
                                <th>@lang('Payment Method')</th>
                                <th>@lang('Total') | @lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(@$invoices as $invoice)
                                <tr>
                                    <td data-label="@lang('Invoice')">
                                        <span class="font-weight-bold">#{{@$invoice->id}}</span>
                                    </td> 

                                    <td data-label="@lang('User')">
                                         <span class="font-weight-bold">{{@$invoice->user->fullname}}</span>
                                        <br>
                                        <span class="small">
                                        <a href="{{ route('admin.users.detail', @$invoice->user->id) }}"><span>@</span>{{ @$invoice->user->username }}</a>
                                        </span>
                                    </td>
                                    <td data-label="@lang('Invoice Date')">
                                        {{ showDateTime(@$invoice->created_at, 'd/m/Y') }} <br> {{ diffForHumans(@$invoice->created_at) }}
                                    </td> 

                                    @php
                                        $deposit = @$invoice->payment;
                                    @endphp

                                    <td data-label="@lang('Payment Method')">        
                                        @if(@$deposit)
                                            <span class="font-weight-bold">
                                                <a href="{{ route('admin.deposit.details', $deposit->id) }}">{{ __(@$deposit->gateway->name) }}</a>
                                            </span>
                                            <br>  
                                            @if(@$deposit->status == 2)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif(@$deposit->status == 1)
                                                <span class="badge badge--success">@lang('Complete')</span>
                                            @elseif(@$deposit->status == 3)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @endif
                                        @elseif($invoice->status == 1)
                                            @lang('Wallet Balance')
                                        @else 
                                            @lang('N/A')
                                        @endif
                                    </td>

                                    <td data-label="@lang('Total') | @lang('Status')">
                                        <span class="font-weight-bold"> 
                                            {{ $general->cur_sym }}{{ showAmount(@$invoice->amount) }}</a>
                                        </span>
                                        <br>
                                        @php echo $invoice->showStatus; @endphp
                                    </td>
                                
                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.invoice.details', @$invoice->id) }}" class="icon-btn" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                            <i class="las la-desktop text--shadow"></i>
                                        </a>
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
                    {{ paginateLinks($invoices) }}
                </div>
            </div>
        </div>


    </div>
@endsection
