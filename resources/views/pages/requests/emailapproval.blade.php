@extends('splash')

@section('pageTitle', 'Request Approval')

@section('section')

    <div class="lockscreen-wrapper">
        <div class="lockscreen-border">
            <div class="lockscreen-logo">
                <span class="logo-lg"><b>CSG</b> e-Workflow</span>
            </div>
            <div class="help-block text-center">

                <p><b>{{ $message }}</b></p>

                @if(!$response)
                <p>If you are having authorization error. The request may already be approved or you can try signing in again <a href="{{ route('google.auth') }}">here</a>.</p>
                @endif

                <p>If you want to view the request try clicking <a href="{{ $redirectURL }}">here</a>.</p>

            </div>
            <div class="lockscreen-footer text-center">
                Copyright &copy; 2017 <b><span class="text-black">SN Aboitiz Power Group</span></b>
                <br>
                All rights reserved
            </div>
        </div>
    </div>	

@endsection