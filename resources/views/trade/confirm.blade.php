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
                Confirm Trade Offer
            </h1>
            <h5>The following message will be sent to the trading partner</h5>

            <form method="post" action="{{ route('trade.post_confirm') }}">
                {{ csrf_field() }}

                <div class="form-group {{ $errors->has('partner_email')  ? ' has-error' : '' }}">
                    <label  for="partner_email">To:</label>
                    <input type="text" class="form-control" name="partner_email" id="partner_email" value="{{ $data['partner_email'] }}" placeholder="To" required>
                    @if ($errors->has('partner_email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('partner_email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('mail_subject') ? ' has-error' : '' }}">
                    <label> Subject:</label>
                    <input class="form-control" name="mail_subject" id="mail_subject" value="{{ $data['mail_subject'] }}" required>
                    @if ($errors->has('mail_subject'))
                        <span class="help-block">
                             <strong>{{ $errors->first('mail_subject') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('mail_content') ? ' has-error' : '' }}">
                    <label> Message:</label>
                    <textarea class="form-control" name="mail_content" id="mail_content" style="height: 200px" required>{{  $data['mail_content'] }}</textarea>
                    @if ($errors->has('mail_content'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mail_content') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary width-100" name="confirm_submit" value="Send">
                    <a class="btn btn-danger width-100" href="{{ route('trade.offer') }}">Back</a>
                </div>
                <br>
            </form>
        </div>

        <div class="col-md-4 hidden-xs hidden-sm right-child">
            <h1>　</h1>
            <h4>　</h4>
            <div style="margin-top: 40px">[Help]</div>
            <div style="margin-top: 120px">[Help]</div>
            <div style="margin-top: 40px">[Help]</div>
            <div style="margin-top: 520px">[Help]</div>
        </div>
        {{--</div>--}}
    </div>
    <div class="modal"><!-- Place at bottom of page --></div>
@stop

