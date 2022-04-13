<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $general->sitename(__($pageTitle)) }}</title>
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ getImage(imagePath()['logoIcon']['path'] .'/favicon.png') }}" type="image/x-icon">
  </head>
  <style>
    @page {
        size: 8.27in 11.7in;
        margin: .5in;
    }
    
    body {
      font-family: "Arial", sans-serif;
      font-size: 14px;
      line-height: 1.5;
      color: #023047; 
    }

    /* Typography */
    .strong {
      font-weight: 700;
    }
    .fw-md {
      font-weight: 500;
    }

    .primary-text {
      color: #219ebc;
    }

    h1,
    .h1 {
      font-family: "Arial", sans-serif;
      margin-top: 8px;
      margin-bottom: 8px;
      font-size: 67px;
      line-height: 1.2;
      font-weight: 500;
    }
    h2,
    .h2 {
      font-family: "Arial", sans-serif;
      margin-top: 8px;
      margin-bottom: 8px;
      font-size: 50px;
      line-height: 1.2;
      font-weight: 500;
    }
    h3,
    .h3 {
      font-family: "Arial", sans-serif;
      margin-top: 8px;
      margin-bottom: 8px;
      font-size: 38px;
      line-height: 1.2;
      font-weight: 500;
    }
    h4,
    .h4 {
      font-family: "Arial", sans-serif;
      margin-top: 8px;
      margin-bottom: 8px;
      font-size: 28px;
      line-height: 1.2;
      font-weight: 500;
    }
    h5,
    .h5 {
      font-family: "Arial", sans-serif;
      margin-top: 8px;
      margin-bottom: 8px;
      font-size: 20px;
      line-height: 1.2;
      font-weight: 500;
    }
    h6,
    .h6 {
      font-family: "Arial", sans-serif;
      margin-top: 8px;
      margin-bottom: 8px;
      font-size: 16px;
      line-height: 1.2;
      font-weight: 500;
    }
    .text-uppercase {
      text-transform: uppercase;
    }
    .text-end {
      text-align: right;
    }
    .text-center {
      text-align: center;
    }
    /* List Style */
    ul {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    /* Utilities */
    .d-block {
      display: block;
    }
    .mt-0 {
      margin-top: 0;
    }
    .m-0 {
      margin: 0;
    }
    .mt-3 {
        margin-top: 16px;
    }
    .mt-4 {
        margin-top: 24px;
    }
    .mb-3 {
      margin-bottom: 16px;
    }
    /* Title */
    .title {
      display: inline-block;
      letter-spacing: 0.05em;
    }
    /* Table Style */
    table {
      width: 7.27in;
      caption-side: bottom;
      border-collapse: collapse;
      border: 1px solid #eafbff;
      color: #023047;
      vertical-align: top;
    }
   table td {
    padding: 5px 15px;
   }
   table th {
    padding: 5px 15px;
   }
   table th:last-child {
    text-align: right !important;
   }
    .table > :not(caption) > * > * {
      padding: 12px 24px;
      background-color: #023047;
      border-bottom-width: 1px;
      box-shadow: inset 0 0 0 9999px #023047;
    }
    .table > tbody {
      vertical-align: inherit;
      border: 1px solid #eafbff;
    }
    .table > thead {
      vertical-align: bottom;
      background: #219ebc;
      color: white;
    }
    .table > thead th {
      font-family: "Arial", sans-serif;
      text-align: left;
      font-size: 16px;
      letter-spacing: 0.03em;
      font-weight: 500;
    }
    .table td:last-child {
        text-align: right;
    }
    .table th:last-child {
        text-align: right;
    }
    .table > :not(:first-child) {
      border-top: 0;
    }

    .table-sm > :not(caption) > * > * {
      padding: 5px;
    }

    .table-bordered > :not(caption) > * {
      border-width: 1px 0;
    }
    .table-bordered > :not(caption) > * > * {
      border-width: 0 1px;
    }

    .table-borderless > :not(caption) > * > * {
      border-bottom-width: 0;
    }
    .table-borderless > :not(:first-child) {
      border-top-width: 0;
    }

    .table-striped > tbody > tr:nth-of-type(even) > * {
      background: #eafbff;
    }

    .mt-30{
        margin-top: 30px;
    }
    .text-danger{
        color: red;
    }
    .text-success{
        color:green;
    }

    /* Logo */
    .logo {
      display: flex;
      align-items: center;
      width: 100%;
      max-width: 200px;
      height: 50px;
      font-size: 24px;
      text-transform: capitalize;
    }
    .logo-img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
    .info {
      display: flex;
      justify-content: space-between;
      padding-top: 15px;
      padding-bottom: 15px;
      border-top: 1px solid #023047;
      border-bottom: 1px solid #023047;
    }
    .address {
      padding-top: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #023047;
    }
    header {
      padding-top: 15px;
      padding-bottom: 15px;
    }
    .body {
      padding-top: 30px;
      padding-bottom: 30px;
    }
    footer {
      padding-bottom: 15px;
    }
    .badge {
        display: inline-block;
        padding: 2px 15px;
        font-size: 10px;
        line-height: 1;
        border-radius: 15px;
    }
    .badge--success {
        color: white;
        background: #02c39a;
    }
    .badge--warning {
        color: white;
        background: #ffb703;
    }
    .align-items-center {
        align-items: center;
    }
    .footer-link {
        text-decoration: none;
        color: #219ebc;
    }
    .footer-link:hover {
        text-decoration: none;
        color: #219ebc;
    }
    .list--row {
      overflow: auto
    }
    .list--row::after {
      content: '';
      display: block;
      clear: both;
    }
    .float-left {
      float: left;
    }
    .float-right {
      float: right;
    }
    .d-block {
      display: block;
    }
    .d-inline-block {
      display: inline-block;
    }
  </style>
  <body>

    <header>
      <div class="container">
        <div class="row">
          <div class="col-12">
              <div class="list--row">
                  <div class="logo float-left">
                      <img src="{{ getImage(imagePath()['logoIcon']['path'] .'/logo.png') }}" alt="image" class="logo-img"/>
                  </div>
                  <h4 class="m-0 float-right">
                    @if($invoice->status == 0)
                        <span class="text-danger">@lang('Unpaid')</span>
                    @elseif($invoice->status == 1)
                        <span class="text-success">@lang('Paid')</span>
                    @endif
                  </h4>
              </div>
          </div>
        </div>
      </div>
    </header>
    <main>
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="info list--row">
              <div class="info-left float-left">
                <div class="list list--row">
                  <span class="strong">@lang('Date') :</span>
                  <span> {{ showDateTime($invoice->created_at, 'd/m/Y') }} </span>
                </div>
              </div>
              <div class="info-right float-right">
                <div class="list list--row">
                  <span class="strong">@lang('Invoice') :</span>
                  <span> #{{ $invoice->id }} </span>
                </div>
              </div>
            </div>
            <div class="address list--row">
              <div class="address-to float-left">
                <span class="primary-text d-block fw-md">@lang('Invoiced To')</span>
                <h5 class="text-uppercase">{{ __(@$user->fullname) }}</h5>
                <ul class="list" style="--gap: 0.3rem">
                  <li>
                    <div class="list list--row" style="--gap: 0.5rem">
                      <span class="strong">@lang('Country') :</span>
                      <span>{{ __(@$user->address->country) }}</span>
                    </div>
                  </li>
                  <li>
                    <div class="list list--row" style="--gap: 0.5rem">
                      <span class="strong">@lang('State') :</span>
                      <span>{{ __(@$user->address->state) }}</span>
                    </div>
                  </li>
                  <li>
                    <div class="list list--row" style="--gap: 0.5rem">
                      <span class="strong">@lang('City') :</span>
                      <span>{{ __(@$user->address->city) }}</span>
                    </div>
                  </li>
                  <li>
                    <div class="list list--row" style="--gap: 0.5rem">
                      <span class="strong">@lang('Zip') :</span>
                      <span>{{ __(@$user->address->zip) }}</span>
                    </div>
                  </li>
                  <li>
                    <div class="list list--row" style="--gap: 0.5rem">
                      <span class="strong">@lang('Mobile') :</span>
                      <span>{{ __(@$user->mobile) }}</span>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="address-form float-right">
                <ul class="text-end">
                  <li>
                    <h5 class="primary-text d-block fw-md">@lang('Pay To')</h5>
                  </li>
                  <li>
                    <span>{{ __($address->data_values->address) }}</span>
                  </li>
                  {{-- <li>
                      <span class="d-inline-block strong">Will Pay :</span>
                      <span class="d-inline-block">1 Times</span>
                  </li> --}}
                </ul>
              </div>
            </div>
            <div class="body">
              <div class="text-center mt-4 mb-3">
                <div class="title-inset">
                  <h5 class="title m-0 text-uppercase">@lang('Items')</h5>
                </div>
              </div>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>@lang('Description')</th>
                    <th>@lang('Amount')</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item) 
                        @if($item->type == 1)
                            <tr>
                                <td>@php echo nl2br($item->description); @endphp</td>
                                <td class="text-center">{{ $general->cur_sym }}{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                            </tr>
                        @endif
                        @if($item->type == 2)
                            <tr>
                                <td>@php echo nl2br($item->description); @endphp</td>
                                <td class="text-center">{{ $general->cur_sym }}{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                            </tr>
                        @endif
                        @if($item->type == 3)
                            <tr>
                                <td>@php echo nl2br($item->description); @endphp</td>
                                <td class="text-center">{{ $general->cur_sym }}-{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                            </tr>
                        @endif
                    @endforeach
                  <tr>
                    <td colspan="1" class="text-end">@lang('Total')</td>
                    <td><span class="h5">{{ $general->cur_sym }}{{ showAmount($invoice->amount) }} {{ __($general->cur_text) }}</span></td>
                  </tr>
                </tbody>
              </table>

              <table class="table table-striped mt-30">
                <thead>
                    <tr>
                        <td>@lang('Transaction Date')</td>
                        <td>@lang('Gateway')</td>
                        <td>@lang('Transaction ID')</td>
                        <td>@lang('Charge')</td>
                        <td>@lang('Amount')</td>
                    </tr>
                </thead>
                <tbody> 
                    @if(@$invoice->payment)
                        <tr> 
                            <td class="text-center">{{ showDateTime(@$invoice->payment->created_at, 'd/m/Y') }}</td>
                            <td class="text-center">{{ __(@$invoice->payment->gateway->name) }}</td>
                            <td class="text-center">{{ @$invoice->payment->trx }}</td>
                            <td class="text-center">{{ showAmount(@$invoice->payment->charge) }} {{ __($general->cur_text) }}</td>
                            <td class="text-center">{{ showAmount(@$invoice->payment->amount + @$invoice->payment->charge) }} {{ __($general->cur_text) }}</td>
                        </tr>
                    @elseif(@$invoice->status == 1 && !$invoice->payment) 
                        <tr>
                            <td class="text-center">{{ showDateTime(@$invoice->trx->created_at, 'd/m/Y') }}</td>
                            <td class="text-center">@lang('Wallet Balance')</td>
                            <td class="text-center">{{ @$invoice->trx->trx }}</td>
                            <td class="text-center">{{ showAmount(0) }} {{ __($general->cur_text) }}</td>
                            <td class="text-center">{{ showAmount($invoice->amount) }} {{ __($general->cur_text) }}</td>
                        </tr>
                    @else 
                        <tr>
                            <td class="text-center" colspan="3">@lang('No Related Transactions Found')</td>
                        </tr>
                    @endif
                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>
    </main>
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-12">
            <span class="d-block text-center">
              @lang('Copyright') &copy; {{ date('Y') }} @lang('All Right Reserved By')
              <a href="#" class="footer-link">{{ __($general->sitename) }}</a>
            </span>
          </div>
        </div>
      </div>
    </footer>
  </body>
</html>
