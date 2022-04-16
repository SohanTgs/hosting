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
                                    <th>@lang('Domain')</th>
                                    <th>@lang('Registration Date')</th>
                                    <th>@lang('Next Due Date')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('View')</th>
                                </tr>
                                </thead>
                                <tbody> 
                                @forelse($domains as $domain)
                                    <tr>
                                        <td data-label="@lang('Domain')">
                                            <b><a href="http://{{ @$domain->domain }}" target="_blank">{{ @$domain->domain }}</a></b>
                                        </td>  
                                        <td data-label="@lang('Registration Date')"> 
                                            {{ showDateTime(@$domain->created_at, 'd/m/Y') }}
                                        </td>
                                        <td data-label="@lang('Next Due Date')">
                                            <strong>{{ showDateTime(@$domain->next_due_date, 'd/m/Y') }}</strong>
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @php echo @$domain->showStatus; @endphp
                                        </td> 
                                        <td data-label="@lang('View')">
                                            <a href="{{ route('user.domain.details', $domain->id) }}" class="btn btn-primary btn-sm approveBtn">
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
                        {{$domains->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


