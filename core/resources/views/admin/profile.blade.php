@extends('admin.layouts.app')

@section('panel')

    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-4 mb-30">

            <div class="card b-radius--5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex p-3 bg--primary align-items-center">
                        <div class="avatar avatar--lg">
                            <img src="{{ getImage(imagePath()['profile']['admin']['path'].'/'. $admin->image,imagePath()['profile']['admin']['size'])}}" alt="@lang('Image')">
                        </div>
                        <div class="pl-3">
                            <h4 class="text--white">{{__($admin->name)}}</h4>
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="font-weight-bold">{{__($admin->name)}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span  class="font-weight-bold">{{__($admin->username)}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span  class="font-weight-bold">{{$admin->email}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile')
                            <span  class="font-weight-bold">{{$admin->mobile}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Address')
                            <span class="font-weight-bold">{{@$admin->address->address}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('State')
                            <span class="font-weight-bold">{{@$admin->address->state}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Zip')
                            <span class="font-weight-bold">{{@$admin->address->zip}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Country')
                            <span class="font-weight-bold">{{@$admin->address->country}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('City')
                            <span class="font-weight-bold">{{@$admin->address->city}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('Profile Information')</h5>
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url({{ getImage(imagePath()['profile']['admin']['path'].'/'.auth()->guard('admin')->user()->image,imagePath()['profile']['admin']['size']) }})">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                                                <label for="profilePicUpload1" class="bg--success">@lang('Upload Image')</label>
                                                <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg').</b> @lang('Image will be resized into 400x400px') </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Name')</label>
                                    <input class="form-control" type="text" name="name" value="{{ auth()->guard('admin')->user()->name }}" >
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Email')</label>
                                    <input class="form-control" type="email" name="email" value="{{ auth()->guard('admin')->user()->email }}" >
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">
                                        @lang('Mobile') <span class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                    </label>
                                    <input class="form-control" type="text" name="mobile" value="{{ auth()->guard('admin')->user()->mobile }}" required placeholder="@lang('+NNN.NNNNNNNNNN')">
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Address')</label>
                                    <input class="form-control" type="text" name="address" value="{{ auth()->guard('admin')->user()->address->address }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('State')</label>
                                    <input class="form-control" type="text" name="state" value="{{ auth()->guard('admin')->user()->address->state }}" required>
                                </div>
 
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Zip')</label>
                                    <input class="form-control" type="text" name="zip" value="{{ auth()->guard('admin')->user()->address->zip }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{ auth()->guard('admin')->user()->address->city }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Country')</label>
                                    <select name="country" class="form-control">
                                        @foreach($countries as $key => $country)
                                            <option value="{{ $key }}" @if($key == auth()->guard('admin')->user()->address->country ) selected @endif>{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Save Changes')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{route('admin.password')}}" class="btn btn-sm btn--primary box--shadow1 text--small" ><i class="fa fa-key"></i>@lang('Password Setting')</a>
@endpush
