@extends('splash')

@section('pageTitle', 'Successfully logged out')

@section('section')

    <div class="lockscreen-wrapper">
        <div class="lockscreen-border">
            <div class="lockscreen-logo">
                <span class="logo-lg">@include('includes.const.title')</span>
            </div>
            <div class="help-block text-center">

                <p>You have successfully logged out!</p>

                <p>If you want to login again try clicking <a href="{{ route('google.auth') }}">here</a>.</p>

            </div>
            <div class="lockscreen-footer text-center">
                @include('includes.const.copyright')
            </div>
        </div>
    </div>	

@endsection