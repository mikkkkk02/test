@extends('master')

@section('pageTitle', 'Dashboard')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Dashboard<small>This is the dashboard page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	@include('pages.dashboard.dashboard')

@endsection