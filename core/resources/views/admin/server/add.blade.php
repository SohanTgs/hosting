@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12"> 
        <div class="card">
            <form class="form-horizontal" method="post" action="{{ route('admin.server.add') }}">
                @csrf 
                <div class="modal-body">   
                    <div class="row">

                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Name')</label>
                                <input type="text" class="form-control" name="name" placeholder="@lang('Name')" required value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="justify-content-between d-flex">
                                    <label>@lang('Hostname')</label>
                                    <small>https://hostname.example.com</small>
                                </div>
                                <input type="url" class="form-control" name="hostname" placeholder="https://hostname.example.com:2087" required value="{{old('hostname')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Username')</label>
                                <input type="text" class="form-control" name="username" placeholder="@lang('Username')" required value="{{old('username')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('Password')</label>
                                <input type="text" class="form-control" name="password" placeholder="@lang('Password')" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('API Token')</label>
                                <input type="text" class="form-control" name="api_token" value="{{ old('api_token') }}" placeholder="@lang('API Token')" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('Security Token')</label>
                                <input type="text" class="form-control" name="security_token" value="{{ old('security_token') }}" placeholder="@lang('Security Token')" required>
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

            var oldGroup = '{{ old("server_group_id") }}'; 
          
            if(oldGroup){
                $('select[name=server_group_id]').val(oldGroup);
            }

        })(jQuery);    
    </script> 
@endpush
