@extends('admin.layouts.app')

@section('panel')
<form class="form-horizontal" method="post" action="{{ route('admin.server.update') }}">
    @csrf 
    <input type="hidden" name="id" required value="{{ $server->id }}">
    <div class="row">
        <div class="col-lg-6 form-group">
            <div class="card">
                <div class="card-header w-100">
                    @lang('Name')
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Select Group')</label>
                                <select name="server_group_id" class="form-control" required>
                                    <option value="" hidden>@lang('Select One')</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" >{{ __($group->name) }}</option>
                                    @endforeach
                                </select>
                            </div> 
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Name')</label>
                                <input type="text" class="form-control" name="name" required value="{{ $server->name }}" placeholder="@lang('Name')">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <label>@lang('Hostname')</label>
                                    <small>https://hostname.example.com</small>
                                </div>
                                <input type="text" class="form-control" name="hostname" required value="{{ $server->hostname }}" placeholder="https://hostname.example.com:2087">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ip_address" value="{{ $server->ip_address }}" placeholder="@lang('IP Address')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="card">
                    <div class="card-header">
                        @lang('Server Details')
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input type="text" class="form-control" name="username" required value="{{ $server->username }}" placeholder="@lang('Username')">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input type="text" class="form-control" name="password" value="{{ $server->password }}" required placeholder="@lang('Password')">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('API Token')</label>
                                    <input type="text" class="form-control" name="api_token" value="{{ $server->api_token }}" required placeholder="@lang('API Token')">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Security Token')</label>
                                    <input type="text" class="form-control" name="security_token" value="{{ $server->security_token }}" required placeholder="@lang('Security Token')">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <input type="checkbox" data-width="100%" data-size="sm" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="status" @if($server->status) checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 form-group">
            <div class="card h-100">
                <div class="card-header">
                    @lang('Nameservers')
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Primary Nameserver')</label>
                                <input type="text" class="form-control" name="ns1" value="{{ $server->ns1 }}" placeholder="@lang('Ns1')">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns_ip1" value="{{ $server->ns_ip1 }}" placeholder="@lang('IP Address')">
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Secondary Nameserver')</label>
                                <input type="text" class="form-control" name="ns2" value="{{ $server->ns2 }}" placeholder="@lang('Ns2')">
                            </div> 
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns_ip2" value="{{ $server->ns_ip2 }}" placeholder="@lang('IP Address')">
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Third Nameserver')</label>
                                <input type="text" class="form-control" name="ns3" value="{{ $server->ns3 }}" placeholder="@lang('Ns3')">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns_ip3" value="{{ $server->ns_ip3 }}" placeholder="@lang('IP Address')">
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Fourth Nameserver')</label>
                                <input type="text" class="form-control" name="ns4" value="{{ $server->ns4 }}" placeholder="@lang('Ns4')">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns_ip4" value="{{ $server->ns_ip4 }}" placeholder="@lang('IP Address')">
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Fifth Nameserver')</label>
                                <input type="text" class="form-control" name="ns5" value="{{ $server->ns5 }}" placeholder="@lang('Ns5')">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns_ip5" value="{{ $server->ns_ip5 }}" placeholder="@lang('IP Address')">
                            </div>
                        </div>
    
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn--primary btn-block" id="btn-save" value="add">@lang('Submit')</button>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" href="{{ route('admin.server.all') }}">
        <i class="la la-fw fa-backward"></i>@lang('Go Back')
    </a>
@endpush

@push('script')
    <script>
        (function($){
            "use strict"; 

            var group = '{{ $server->server_group_id }}'; 
          
            if(group){
                $('select[name=server_group_id]').val(group);
            }

        })(jQuery);    
    </script> 
@endpush
