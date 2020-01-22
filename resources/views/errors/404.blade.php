@extends('splash')

@section('pageTitle', 'Page Not Found')

@section('section')

    <div class="lockscreen-wrapper">
        <div class="lockscreen-border">
            <div class="lockscreen-logo">
                <p class="logo-lg">@include('includes.const.title')</p>
            </div>
            <div class="help-block text-center">

                <h3><b class="text-blue left-align">Page not found!</b></h3>
                <p>Please make sure you've entered the correct URL address. Click <a href="{{ route('dashboard') }}">here</a> to go back to the dashboard screen</p>

            </div>
            <div class="lockscreen-footer text-center">
                @include('includes.const.copyright')
            </div>
        </div>
    </div>  

@endsection