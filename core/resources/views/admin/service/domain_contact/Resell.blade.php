@extends('admin.layouts.app')
@section('panel')

<form action="{{ route('admin.domain.module.command') }}" method="POST">
    <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
    <input type="hidden" name="module_type" required value="4">
    @csrf
    <div class="row mb-none-30 mb-2 justify-content-center"> 
        <div class="col-xl-8 col-md-12 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-header">
                    @lang('Details')
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Name')  
                                </span> 
                                <input class="form-control" type="text" name="name" value="{{@$contactInfo->name}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Address 1')  
                                </span> 
                                <input class="form-control" type="text" name="address1" value="{{@$contactInfo->address1}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Address 2')  
                                </span> 
                                <input class="form-control" type="text" name="address2" value="{{@$contactInfo->address2}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('City')  
                                </span> 
                                <input class="form-control" type="text" name="city" value="{{@$contactInfo->city}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Zip')  
                                </span> 
                                <input class="form-control" type="text" name="zip" value="{{@$contactInfo->zip}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Country')  
                                </span> 
                                <select name="country" class="form-control">
                                    @foreach($countries as $key => $country)
                                        <option value="{{ $key }}" @if($key == @$contactInfo->country ) selected @endif>{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Telephone CC')  
                                </span> 
                                <div class="w-100">
                                    <input class="form-control" type="text" name="telephonecc" value="{{@$contactInfo->telnocc}}">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Telephone')  
                                </span> 
                                <div class="w-100">
                                    <input class="form-control" type="text" name="telephone" value="{{@$contactInfo->telno}}">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Email Address')  
                                </span> 
                                <input class="form-control" type="text" name="email" value="{{@$contactInfo->emailaddr}}">
                            </div>
                        </li>
                    </ul> 
                </div>
            </div> 
        </div>  
    </div>

    <div class="row mb-none-30 justify-content-center">
        <div class="col-xl-8 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Submit')</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('breadcrumb-plugins') 
<a href="{{ route('admin.order.domain.details', $domain->id) }}" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
    <i class="fa fa-fw fa-backward"></i>@lang('Go Back')
</a>
@endpush 
  