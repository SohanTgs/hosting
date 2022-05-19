@extends($activeTemplate.'layouts.master')
@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-10">
            <div class="card w-100">
                <form action="{{ route('user.domain.nameserver.update') }}" method="post">
                    @csrf
                    <div class="card-body">

                        @if($domain->status != 1)
                            <div class="alert alert-warning text-center" role="alert">
                                @lang('This domain is not currently active. Domains cannot be managed unless active')
                            </div>
                        @endif

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

                                <h3>@lang('Status'): </h3>
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

                            @if($domain->status == 1)
                                <div class="col-md-12 mt-4">
                                    <h3>@lang('What would you like to do today')?</h3>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)" class="nameserverModal">@lang('Change the nameservers your domain points to')</a>
                                        </li>
                                        <li>
                                            <a href="{{ $domain->register ? route('user.domain.contact', $domain->id) : 'javascript:void(0)' }}">
                                                @lang('Update the WHOIS contact information for your domain')
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" class="renewModal">@lang('Renew Your Domain')</a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                        </div>
                    </div>
                </form>
            </div> 
        </div>
    </div>
</div>

<div id="nameserverModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Change Nameservers')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.domain.nameserver.update') }}" method="post">
                @csrf
                <input type="hidden" name="domain_id" required value="{{ $domain->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                @lang('You can change where your domain points to here. Please be aware changes can take up to 24 hours to propagate')
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns1">@lang('Nameserver 1')</label>
                            <input type="text" class="form-control" name="ns1" id="ns1" required placeholder="@lang('ns1.example.com')" value="{{ $domain->ns1 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns2">@lang('Nameserver 2')</label>
                            <input type="text" class="form-control" name="ns2" id="ns2" required placeholder="@lang('ns2.example.com')" value="{{ $domain->ns2 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns3">@lang('Nameserver 3')</label>
                            <input type="text" class="form-control" name="ns3" id="ns3" placeholder="@lang('ns3.example.com')" value="{{ $domain->ns3 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns4">@lang('Nameserver 4')</label>
                            <input type="text" class="form-control" name="ns4" id="ns4" placeholder="@lang('ns4.example.com')" value="{{ $domain->ns4 }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="renewModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Domain Renewal')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.domain.renew') }}" method="post">
                @csrf
                <input type="hidden" name="domain_id" required value="{{ $domain->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <h5>{{ $domain->domain }}</h5>
                            @lang('Expiry Date'): {{ showDateTime($domain->expiry_date, 'd M Y') }} ({{ diffForHumans($domain->expiry_date) }})
                        </div> 
                        <div class="col-md-12 form-group">
                            <label for="ns1">@lang('Available Renewal Periods')</label>
                            <select name="renew_year" class="form-control">
                                @foreach($renewPricing->renewPrice() as $year => $data)
                                    <option value="{{ $year }}">
                                        {{ $year }} @lang('Year/s') @ {{ $general->cur_sym }}{{ showAmount($data['renew']) }} {{ __($general->cur_text) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">@lang('Submit')</button>
                </div>
            </form>
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

@push('script')
    <script>
        (function($){
            "use strict";
            $('.nameserverModal').on('click', function() {
                var modal = $('#nameserverModal');
                modal.modal('show');
            });
            $('.renewModal').on('click', function() {
                var modal = $('#renewModal');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
