@extends('admin_layout')

@section('title', 'Dashboard')
@section('page', 'Dashboard')

@section('content')
<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div> <!-- end .flash-message -->
<div class="row">
    {{--<div class="col-md-8 col-md-offset-2">--}}
        {{--<div class="panel panel-default">--}}
            {{--<div class="panel-heading">Dashboard</div>--}}

            {{--<div class="panel-body">--}}
                {{--You are logged in!--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
</div>
@endsection
