@extends('main_layout')
@section('title', 'Create Trade Offer')
{{--@section('page', 'Create Trade Offer')--}}

@section('content')
    {{--{{ dd($errors) }}--}}
    <div class="container container-custom" >
        {{--<div class="row">--}}
            <div class="col-xs-12 col-md-8" style="padding-left: 20px; padding-right: 40px">
                <h1 class="page-header">
                    {{--@yield('page')--}}
                    Create Trade Offer
                </h1>
                <h4>Let's create an offer to Seller/Buyer</h4>

                <form method="post" action="{{ route('trade.post_offer') }}">
                    {{ csrf_field() }}
                    <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                        <label style="float: left">I am</label>
                        <div style="float: left; margin-left: 20px" id="type">
                            <label class="radio-inline"><input type="radio" name="type" value="1" {{ old('type') == 1 ? 'checked': ''}}>Seller</label>
                            <label class="radio-inline"><input type="radio" name="type" value="2" {{ old('type') == 2 ? 'checked': ''}}>Buyer</label>
                        </div>
                        @if ($errors->has('type'))
                            <br>
                            <span class="help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group {{ ($errors->has('first_name') || $errors->has('last_name')) ? ' has-error' : '' }}">
                        <label  for="item_amount">Your name is:</label>
                        <div class="col-xs-12">
                            <div class="col-xs-6">
                                <span>First name</span>
                                <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name') }}" placeholder="First name">
                                @if ($errors->has('first_name'))
                                 <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                 </span>
                                @endif
                            </div>
                            <div class="col-xs-6">
                                <span>Last name</span>
                                <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}" placeholder="Last name">
                                @if ($errors->has('last_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}" >
                        <label  for="item_amount">Your email address:</label>
                        <input class="form-control" name="email" value="{{ old('email') }}" placeholder="you@example.com">
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}" >
                        <label>By clicking this, we send 6 digit code to your email address.</label>
                        <div class="text-center">
                            <input type="submit" class="btn btn-primary" name="check_email_submit" value="> Proceed and check my email">
                            {{--{!! Form::submit( '> Proceed and check my email', ['class' => 'btn btn-primary', 'name' => 'submit', 'value' => 'save'])!!}--}}
                        </div>
                    </div>

                    <br>
                    <div id="proceed_session"></div>
                    @if (session('status') && session('status') == 'email_checked')
                        <div class="alert alert-success">
                            Email has been sent!
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ( $confirmBack || (session('status') && in_array(session('status'),['email_checked', 'trade_input'])))
                    {{--@if(true)--}}
                    <label>Trading Partner</label>
                    <div class="form-group {{ $errors->has('partner_email') ? ' has-error' : '' }}">
                        <label  for="partner_email">Trading Partner's email address:</label>
                        <input class="form-control" name="partner_email" id="partner_email" placeholder="partner@example.com" value="{{ old('partner_email') }}" required>
                        @if ($errors->has('partner_email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('partner_email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group {{ $errors->has('partner_country') ? ' has-error' : '' }}">
                        <label for="partner_country">Trading Partner is located in:</label>
                        <select class="form-control" name="partner_country" id="partner_country">
                            @foreach ($countries as $key => $value)
                                <option value="{{ $key }}"  {{ $key == old('partner_country', 'US') ?  'selected="selected"' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group{{ $errors->has('trading_currency') ? ' has-error' : '' }}">
                        <label  for="trading_currency">Trading Partner Currency</label>
                        <select class="form-control pull-right" style="width:100px" name="trading_currency" id="trading_currency">
                            @foreach ($currencies as $key => $value)
                                <option value="{{ $key }}"  {{ $key == old('trading_currency', 'USD') ?  'selected="selected"' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label  for="item_amount">Trade Item</label>
                        <div class="item-table" style="margin-left: 10px" data-prototype='
                                        <tr data-index="__index__" class="item-row">
                                            <td><input class="form-control item-name" name="trade_item[__index__][name]"></td>
                                            <td><input class="form-control item-quantity" name="trade_item[__index__][quantity]"></td>
                                            <td><input class="form-control item-price" name="trade_item[__index__][price]"></td>
                                            <td class="action_td">
                                                <a href="#" class="add-item"><i class="fa fa-lg fa-plus"></i></a>
                                                <a href="#" class="left-10 delete-item"><i class="fa fa-lg fa-minus"></i></a>
                                            </td>
                                        </tr>'>
                            <small>Please enter trading item</small>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="vertical-align: top">Item</th>
                                    <th>
                                        Quantity<br>
                                        <small style="font-weight: normal">How many do you want?</small>
                                    </th>
                                    <th>
                                        Unit Price<br>
                                        <small style="font-weight: normal">What price do you want?</small>
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if(old('trade_item') )
                                    @foreach( old('trade_item') as $i=>$item)
                                        <tr data-index="{{$i}}" class="item-row">
                                            <td><input class="form-control item-name" name="trade_item[{{$i}}][name]" value="{{ $item['name'] }}"></td>
                                            <td><input class="form-control item-quantity" name="trade_item[{{$i}}][quantity]" value="{{ $item['quantity'] }}"></td>
                                            <td><input class="form-control item-price" name="trade_item[{{$i}}][price]" value="{{ $item['price'] }}"></td>
                                            <td class="action_td">
                                                <a href="#" class="add-item"><i class="fa fa-lg fa-plus"></i></a>
                                                <a href="#" class="left-10 delete-item"><i class="fa fa-lg fa-minus"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
                                        <tr data-index="0" class="item-row">
                                            <td><input class="form-control item-name" name="trade_item[0][name]"></td>
                                            <td><input class="form-control item-quantity" name="trade_item[0][quantity]"></td>
                                            <td><input class="form-control item-price" name="trade_item[0][price]"></td>
                                            <td class="action_td">
                                                <a href="#" class="add-item" ><i class="fa fa-lg fa-plus"></i></a>
                                                <a href="#" class="left-10 delete-item"><i class="fa fa-lg fa-minus"></i></a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="summary">
                                <label>Money Transfer Fee</label>
                                <input type="hidden" name="trading_fee" id="trading_fee" value="{{ old('trading_fee') }}" >
                                <span class="pull-right" id="trading_fee_span">{{ old('trading_fee', 'calculating') }}</span>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total_amount">Total</label>
                        <span class="pull-right" id="total_amount_span"> {{ old('total_amount', '0.00 + calculating') }}</span>
                        <input type="hidden" name="total_amount" id="total_amount" value="{{ old('total_amount') }}">
                        <div class="pull-right error" id="calculate_error_message"></div>
                    </div>

                    <div class="form-group{{ $errors->has('item_currency') ? ' has-error' : '' }}">
                        <label  for="item_currency">Currency
                            <small style="font-weight: normal">I want to {{ session('type') == 1 ? 'receive' : 'buy' }} in</small>
                        </label>
                        <select class="form-control pull-right" style="width:100px" name="item_currency" id="item_currency">
                            <option value=""></option>
                            @foreach ($currencies as $key => $value)
                                <option value="{{ $key }}"  {{ $key == old('item_currency') ?  'selected="selected"' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label>Offer mail</label>
                    <div style="margin-left: 10px">
                        <small>Please edit the message</small>
                        <div class="form-group{{ $errors->has('mail_subject') ? ' has-error' : '' }}">
                            <label> Subject</label>
                            <input class="form-control" name="mail_subject" id="mail_subject" value="{{ old('mail_subject') }}">
                            @if ($errors->has('mail_subject'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mail_subject') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('mail_content') ? ' has-error' : '' }}">
                            <label>Message</label>
                            <select class="form-control pull-right" name="mail_template" id="mail_template" style="width:200px">
                                @foreach ($emailTemplates as $key => $template )
                                    <option value="{{ $key }}"  {{ $key == old('mail_template', 'default') ?  'selected="selected"' : '' }} data-content="{{ $template['content'] }}">
                                        {{ $template['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <textarea class="form-control" name="mail_content" id="mail_content" style="height: 200px">{{ old('mail_content') }}</textarea>
                            @if ($errors->has('mail_content'))
                                <span class="help-block">
                                <strong>{{ $errors->first('mail_content') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('passcode') ? ' has-error' : '' }}">
                        <label  for="passcode">Passcode</label>
                        <div style="margin-left: 10px">
                            <small>Please enter passcode.  We sent the passcode to your email address.  If you have not received yet, please retype your email address.</small>
                            <input class="form-control" name="passcode" id="passcode" value="{{ old('passcode') }}" required>
                        </div>
                        @if ($errors->has('passcode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('passcode') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group text-center">
                        <input type="submit" class="btn btn-primary" name="confirm_submit" value=">> Proceed to confirm">
                        {{--<button class="btn btn-primary"> >> Proceed to confirm</button>--}}
                    </div>
                    <br>
                    @endif
                </form>
            </div>

            <div class="col-md-4 hidden-xs hidden-sm right-child">
                <h1>　</h1>
                <h4>　</h4>
                <div style="margin-top: 40px">[Help]</div>
                <div style="margin-top: 120px">[Help]</div>
                <div style="margin-top: 40px">[Help]</div>
                @if (session('status') && in_array(session('status'),['email_checked', 'trade_input']))
                <div style="margin-top: 520px">[Help]</div>
                @endif
            </div>
        {{--</div>--}}
    </div>
    <div class="modal"><!-- Place at bottom of page --></div>
@stop
@section('script')
    <script>
        $(function() {
            // Initial jquery validate
            $('form').validate();

            // Calculate button on click event
            $('.calculate').on('click', function () {
                calculate();
            });

            // Delete an item in table
            $('.item-table').on('click', '.delete-item', function (e) {
                $(this).closest('tr').remove();
                calculate();
                e.preventDefault();
            // Add an item int table
            }).on('click', '.add-item', function (e) {
                var prototype = $('.item-table').data('prototype');

                // Get max index
                var maxIndex = $('tbody').find('tr').last().data('index');
                var html = prototype.replace(/__index__/g, maxIndex !=null ? Number(maxIndex) + 1 : 0);
                $('tbody').append(html);
                e.preventDefault();
            // Change an item
            }).on('change', 'input', function () {
                updateMailContent();
            });

            // Update mail
            updateMailContent();

            // Change mail content
            $('#mail_content').on('change', function () {
                $('#mail_template option').each(function () {
                    var $this = $(this);
                    if($this.is(':selected') ){
                        $this.data('changed', true);
                    }
                });
            });

            // Change mail template event
            $('#mail_template').on('change', function () {
                var itemQuantityPrice = '';
                $('.item-row').each(function () {
                    var $row = $(this);
                    if($row.find('.item-price').val() && $row.find('.item-quantity').val()){
                        itemQuantityPrice += $row.find('.item-name').val() +  '  ' + $row.find('.item-quantity').val() + '@' +
                                $row.find('.item-price').val() + $('#trading_currency').val() +  "\n";
                    }
                });

                updateMailContent();
            });

            $('#item_amount, #item_currency, #trading_fee, #trading_currency').on('change', function () {
                calculate();
            });

            // Calculate temporary quotation
            function calculate() {
                // Validate
                if($('form').valid()){
                    var itemCurrency = $('#item_currency').val();
                    var tradingCurrency = $('#trading_currency').val();
                    var amount = 0;

                    // Calculate total amount (without transfer fee)
                    $('.item-row').each(function () {
                        var $row = $(this);
                        if($row.find('.item-price').val() && $row.find('.item-quantity').val()){
                            amount += $row.find('.item-price').val() * $row.find('.item-quantity').val();
                        }
                    });

                    // Call temporary quotes API using Ajax
                    $.ajax({
                        method: "GET",
                        url: "{{ route('trade.get_transferwise_quote') }}",
                        data: {
                            source: itemCurrency,
                            target: tradingCurrency,
                            amount:  parseFloat(amount)
                        },
                        beforeSend: function( xhr ) {
                            $("body").addClass("loading");
                            $('#total_amount_span').text('');
                            $('#total_amount').val('');
                            $('#trading_fee_span').text('');
                            $('#trading_fee').val('');
                            $('#calculate_error_message').text('');
                        }
                    })
                            .done(function( response ) {
                                if(response.status.code == 0){
                                    // Set result value
                                    $('#total_amount_span').text(response.data.sourceAmount + ' ' + itemCurrency);
                                    $('#total_amount').val(response.data.sourceAmount);

                                    $('#trading_fee_span').text(response.data.fee + ' ' + itemCurrency);
                                    $('#trading_fee').val(response.data.fee);
                                    $('#calculate_error_message').text('');

                                }else{
                                    // Alert error message
                                    $('#calculate_error_message').text(response.status.message);
                                }
                            })
                            .fail(function (response) {
                                $('#calculate_error_message').text('Fail to call server api');
                            })
                            .always(function() {
                                // Update mail content
                                updateMailContent();
                                $("body").removeClass("loading");
                            });

                }
            }

            // Update mail content
            function updateMailContent() {
                var $selectedOption = $('#mail_template').find('option:selected');
                var mailPrototype = $selectedOption.data('content');
                var userChanged = $('#mail_template').data('changed');

                //var template = $(this).val();
                var itemQuantityPrice = '';
                $('.item-row').each(function () {
                    var $row = $(this);
                    if($row.find('.item-price').val() && $row.find('.item-quantity').val()){
                        itemQuantityPrice += $row.find('.item-name').val() +  '  ' + $row.find('.item-quantity').val() + '@' +
                                $row.find('.item-price').val() + $('#trading_currency').val() +  "\n";
                    }
                });

                if(mailPrototype && !userChanged){
                    var  mailContent = mailPrototype.replace('__WHO_I_AM__', $('#type').val() == 1 ? 'sell' : 'purchase')
                                    .replace('__ITEM_QUANTITY_PRICE__', itemQuantityPrice)
                                    .replace('__FEE__', $('#trading_fee').val())
                                    .replace('__TOTAL__', $('#total_amount').val())
                                    .replace('__FIRST_NAME__', $('#first_name').val())
                                    .replace('__LAST_NAME__', $('#last_name').val())
                                    .replace(/\\n/g, '\\\n')
                            ;
                    $('#mail_content').text(mailContent);
                    var subject = "Offering of XXX through My Transporter";
                    subject = subject.replace('XXX', $('.item-name').val());
                    $('#mail_subject').val(subject);
                }
            }
        });
    </script>
@stop
