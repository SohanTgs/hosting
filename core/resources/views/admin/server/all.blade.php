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
                            <th>@lang('Group')</th>
                            <th>@lang('Hostname')</th>
                            <th>@lang('Username')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead> 
                        <tbody>
                            @forelse($servers as $server)
                                <tr>  
                                    <td data-label="@lang('Name')">
                                        <span class="font-weight-bold">{{ __($server->name) }}</span>
                                    </td>
                                    <td data-label="@lang('Group')">
                                        <span class="font-weight-bold">{{ __(@$server->group->name) }}</span>
                                    </td>
                                    <td data-label="@lang('Hostname')">
                                        {{ $server->hostname }}
                                    </td>

                                    <td data-label="@lang('Username')">
                                        {{ __($server->username) }}
                                    </td>

                                    <td data-label="@lang('Status')">
                                        @if($server->status == 1)
                                            <span class="badge badge--success">@lang('Enable')</span>
                                        @else 
                                            <span class="badge badge--danger">@lang('Disable')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <a class="icon-btn btn--success" data-toggle="tooltip" data-original-title="@lang('Login to WHM')" 
                                            href="{{ route('admin.server.login.WHM', $server->id) }}">
                                            <i class="lab la-whmcs text--shadow"></i>
                                        </a>
                                        <a class="icon-btn" data-toggle="tooltip" data-original-title="@lang('Edit')" href="{{ route('admin.server.edit.page', $server->id) }}">
                                            <i class="las la-edit text--shadow"></i>
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
                {{ paginateLinks($servers) }}
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}} 
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Update Server')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.server.update') }}">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="form-group"> 
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" placeholder="@lang('Name')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Hostname or IP Address')</label>
                        <input type="text" class="form-control" name="hostname" placeholder="@lang('Hostname')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Username')</label>
                        <input type="text" class="form-control" name="username" placeholder="@lang('Username')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Password')</label>
                        <input type="password" class="form-control" name="password" placeholder="@lang('Password')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('API Token')</label>
                        <input type="text" class="form-control" name="api_token" placeholder="@lang('API Token')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Status')</label>
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="status" checked>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-block" id="btn-update" value="add">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" href="{{ route('admin.server.add.page') }}">
        <i class="fa fa-fw fa-plus"></i>@lang('Add New')
    </a>
    <a href="{{ session()->get('url') ?? '#' }}" class="whmLogin" target="_blank"></a>
@endpush 

@push('script')
    <script>
        (function ($) {
            "use strict";

            var whmLoginUrl = @json(session()->get('url'));

            if(whmLoginUrl){
                document.querySelector('.whmLogin').click();
            }

        })(jQuery);
    </script>
@endpush 
