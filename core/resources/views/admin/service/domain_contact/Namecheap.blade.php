@extends('admin.layouts.app')
@section('panel')

@php
    $register = @$contactInfo['CommandResponse']['DomainContactsResult']['Registrant'];
    $tech = @$contactInfo['CommandResponse']['DomainContactsResult']['Tech'];
    $admin = @$contactInfo['CommandResponse']['DomainContactsResult']['Admin'];
    $auxBilling = @$contactInfo['CommandResponse']['DomainContactsResult']['AuxBilling'];
@endphp

<form action="{{ route('admin.domain.module.command') }}" method="POST">
    <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
    <input type="hidden" name="module_type" required value="4">
    @csrf
    <div class="row mb-none-30 mb-2">
        <div class="col-xl-6 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-header">
                    @lang('Register')
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('First Name')  
                                </span> 
                                <input class="form-control" type="text" name="RegisterFirstName" value="{{@$register['FirstName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Last Name')  
                                </span> 
                                <input class="form-control" type="text" name="RegisterLastName" value="{{@$register['LastName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Address')  
                                </span> 
                                <input class="form-control" type="text" name="RegisterAddress1" value="{{@$register['Address1']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('City')  
                                </span> 
                                <input class="form-control" type="text" name="RegisterCity" value="{{@$register['City']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('State')  
                                </span> 
                                <input class="form-control" type="text" name="RegisterStateProvince" value="{{@$register['StateProvince']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Postal Code')  
                                </span> 
                                <input class="form-control" type="text" name="RegisterPostalCode" value="{{@$register['PostalCode']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Country')  
                                </span>  
                                <select name="RegisterCountry" class="form-control">
                                    @foreach($countries as $key => $country)
                                        <option value="{{ $key }}" @if($key == @$register['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Phone')  
                                </span> 
                                <div class="w-100">
                                    <span class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                    <input class="form-control" type="text" name="RegisterPhone" value="{{@$register['Phone']}}">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Email Address')  
                                </span> 
                                <input class="form-control" type="text" name="RegisterEmailAddress" value="{{@$register['EmailAddress']}}">
                            </div>
                        </li>
                    </ul> 
                </div>
            </div> 
        </div>  
        <div class="col-xl-6 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-header">
                    @lang('Tech')
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('First Name')  
                                </span> 
                                <input class="form-control" type="text" name="TechFirstName" value="{{@$tech['FirstName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Last Name')  
                                </span> 
                                <input class="form-control" type="text" name="TechLastName" value="{{@$tech['LastName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Address')  
                                </span> 
                                <input class="form-control" type="text" name="TechAddress1" value="{{@$tech['Address1']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('City')  
                                </span> 
                                <input class="form-control" type="text" name="TechCity" value="{{@$tech['City']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('State')  
                                </span> 
                                <input class="form-control" type="text" name="TechStateProvince" value="{{@$tech['StateProvince']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Postal Code')  
                                </span> 
                                <input class="form-control" type="text" name="TechPostalCode" value="{{@$tech['PostalCode']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Country')  
                                </span> 
                                <select name="TechCountry" class="form-control">
                                    @foreach($countries as $key => $country)
                                        <option value="{{ $key }}" @if($key == @$tech['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Phone')  
                                </span> 
                                <div class="w-100">
                                    <span class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                    <input class="form-control" type="text" name="TechPhone" value="{{@$tech['Phone']}}">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Email Address')  
                                </span> 
                                <input class="form-control" type="text" name="TechEmailAddress" value="{{@$tech['EmailAddress']}}">
                            </div>
                        </li>
                    </ul> 
                </div>
            </div> 
        </div>  
    </div>

    <div class="row mb-none-30 mb-2">
        <div class="col-xl-6 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-header">
                    @lang('Admin')
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('First Name')  
                                </span> 
                                <input class="form-control" type="text" name="AdminFirstName" value="{{@$admin['FirstName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Last Name')  
                                </span> 
                                <input class="form-control" type="text" name="AdminLastName" value="{{@$admin['LastName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Address')  
                                </span> 
                                <input class="form-control" type="text" name="AdminAddress1" value="{{@$admin['Address1']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('City')  
                                </span> 
                                <input class="form-control" type="text" name="AdminCity" value="{{@$admin['City']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('State')  
                                </span> 
                                <input class="form-control" type="text" name="AdminStateProvince" value="{{@$admin['StateProvince']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Postal Code')  
                                </span> 
                                <input class="form-control" type="text" name="AdminPostalCode" value="{{@$admin['PostalCode']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Country')  
                                </span> 
                                <select name="AdminCountry" class="form-control">
                                    @foreach($countries as $key => $country)
                                        <option value="{{ $key }}" @if($key == @$admin['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Phone')  
                                </span> 
                                <div class="w-100">
                                    <span class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                    <input class="form-control" type="text" name="AdminPhone" value="{{@$admin['Phone']}}">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Email Address')  
                                </span> 
                                <input class="form-control" type="text" name="AdminEmailAddress" value="{{@$admin['EmailAddress']}}">
                            </div>
                        </li>
                    </ul> 
                </div>
            </div> 
        </div>  
        <div class="col-xl-6 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-header">
                    @lang('AuxBilling')
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('First Name')  
                                </span> 
                                <input class="form-control" type="text" name="AuxBillingFirstName" value="{{@$auxBilling['FirstName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Last Name')  
                                </span> 
                                <input class="form-control" type="text" name="AuxBillingLastName" value="{{@$auxBilling['LastName']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Address')  
                                </span> 
                                <input class="form-control" type="text" name="AuxBillingAddress1" value="{{@$auxBilling['Address1']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('City')  
                                </span> 
                                <input class="form-control" type="text" name="AuxBillingCity" value="{{@$auxBilling['City']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('State')  
                                </span> 
                                <input class="form-control" type="text" name="AuxBillingStateProvince" value="{{@$auxBilling['StateProvince']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Postal Code')  
                                </span> 
                                <input class="form-control" type="text" name="AuxBillingPostalCode" value="{{@$auxBilling['PostalCode']}}">
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Country')  
                                </span> 
                                <select name="AuxBillingCountry" class="form-control">
                                    @foreach($countries as $key => $country)
                                        <option value="{{ $key }}" @if($key == @$auxBilling['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Phone')  
                                </span> 
                                <div class="w-100">
                                    <span class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                    <input class="form-control" type="text" name="AuxBillingPhone" value="{{@$auxBilling['Phone']}}">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item"> 
                            <div class="billing-form">
                                <span class="billing-form__label d-block flex-shrink-0">
                                    @lang('Email Address')  
                                </span> 
                                <input class="form-control" type="text" name="AuxBillingEmailAddress" value="{{@$auxBilling['EmailAddress']}}">
                            </div>
                        </li>
                    </ul> 
                </div>
            </div> 
        </div>  
    </div>

    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
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
 