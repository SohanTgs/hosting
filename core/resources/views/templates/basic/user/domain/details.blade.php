@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <div class="card w-100">
                    <form action="{{ route('user.domain.nameserver.update') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>@lang('Domain'): </h3> 
                                    <h5>
                                        <a href="http://{{ $domain->domain }}" target="_blank">{{ $domain->domain }}</a>
                                    </h5>
    
                                    <h3>@lang('Registration Date'): </h3>
                                    <h5>{{ $domain->reg_time ? showDateTime($domain->reg_time, 'd/m/Y') : 'N/A' }}</h5>

                                    <h3>@lang('Next Due Date'): </h3>
                                    <h5>{{ $domain->next_due_date ? showDateTime($domain->next_due_date, 'd/m/Y') : 'N/A' }}</h5>

                                    <div>@lang('Status'): </div>
                                    <h5>@php echo $domain->showStatus; @endphp</h5>
                                </div>
                                <div class="col-md-6">
                                    <h3>@lang('First Payment Amount'): </h3>
                                    <h5>{{ $general->cur_sym }}{{ showAmount($domain->first_payment_amount) }} {{ __($general->cur_text) }}</h5>

                                    <h3>@lang('Recurring Amount'): </h3>
                                    <h5>
                                        {{ $general->cur_sym }}{{ showAmount($domain->recurring_amount) }} {{ __($general->cur_text) }} {{ $domain->reg_period }} @lang('Year/s') 
                                        @if($domain->id_protection)
                                            @lang('with ID Protection')
                                        @endif
                                    </h5>
                                </div>

                                <div class="col-md-6 form-group mt-4">
                                    <label for="nameserver_1">@lang('Nameserver 1')</label>
                                    <input type="text" name="nameserver_1" id="nameserver_1" class="form-control">
                                </div>
                                <div class="col-md-6 form-group mt-4">
                                    <label for="nameserver_2">@lang('Nameserver 2')</label>
                                    <input type="text" name="nameserver_2" id="nameserver_2" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="nameserver_3">@lang('Nameserver 3')</label>
                                    <input type="text" name="nameserver_3" id="nameserver_3" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="nameserver_4">@lang('Nameserver 4')</label>
                                    <input type="text" name="nameserver_4" id="nameserver_4" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="nameserver_5">@lang('Nameserver 5')</label>
                                    <input type="text" name="nameserver_5" id="nameserver_5" class="form-control">
                                </div>
                                <div class="col-md-12 w-100  form-group">
                                    <button class="btn btn-info w-100">@lang('Submit')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .new-card {
        margin: 0;
        background-color: #efefef;
        border-radius: 10px;
        padding: 30px;
        line-height: 1em;
    }
    .fa-stack {
        display: inline-block;
        height: 2em;
        line-height: 2em;
        position: relative;
        vertical-align: middle;
        width: 2.5em;
        font-size: 50px;
        width: 100%;
        justify-content: center;
    }
</style>
@endpush