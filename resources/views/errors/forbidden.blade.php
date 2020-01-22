@extends('splash')

@section('pageTitle', 'Forbidden')

@section('section')

    <div class="lockscreen-wrapper">
        <div class="lockscreen-border">
            <div class="lockscreen-logo">
                <span class="logo-lg">@include('includes.const.title')</span>
            </div>
            <div class="help-block text-center">

                @if(session('email'))
                <p><b class="text-primary">{{ session('email') }}</b> is not yet registered</p>
                @endif

                <p>It looks like your Google account isn't registered yet, Please use the email provided to notify support of the problem or try logging in again <a href="{{ route('google.auth') }}">here</a>.</p>
                <p>Support email <a href="mailto:hr@snaboitiz.com">hr@snaboitiz.com</a>.</p>            
            </div>
            <div class="lockscreen-footer text-center">
                @include('includes.const.copyright')
            </div>
        </div>
    </div>

@endsection