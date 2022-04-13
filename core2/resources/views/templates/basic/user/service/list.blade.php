@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __($pageTitle) }}</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table ">
                                <thead class="thead-dark">
                                <tr>
                                    <th>@lang('Service/Product')</th>
                                    <th>@lang('Pricing')</th>
                                    <th>@lang('Next Due Date')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('View')</th>
                                </tr>
                                </thead>
                                <tbody> 
                                @forelse($services as $service)
                                    <tr>
                                        <td data-label="@lang('Service/Product')">
                                            <b>{{ __(@$service->product->name) }}</b>
                                            <small class="d-block">{{ __(@$service->product->serviceCategory->name) }}</small>
                                        </td>  
                                        <td data-label="@lang('Pricing')"> 
                                            <span>{{ $general->cur_sym }}{{ getAmount($service->amount) }} {{ __($general->text) }}</span>
                                            @if($service->billing == 1)
                                                @lang('One Time') 
                                            @else 
                                                {{ billing(@$service->billing_cycle, true)['showText'] }}
                                            @endif
                                        </td>
                                        <td data-label="@lang('Next Due Date')">
                                            <strong>{{ showDateTime($service->next_due_date, 'd/m/Y') }}</strong>
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @php echo $service->showDomainStatus; @endphp
                                        </td> 
                                        <td data-label="@lang('View')">
                                            <a href="{{ route('user.service.details', $service->id) }}" class="btn btn-primary btn-sm approveBtn">
                                                <i class="fa fa-desktop"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty 
                                    <tr>
                                        <td colspan="100% text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{$services->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


