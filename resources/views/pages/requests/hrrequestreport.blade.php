@extends('master')

@section('pageTitle', 'HR Monitoring')

@section('breadcrumb')

	<div class="content-header">
		<h1>HR Monitoring<small>{{-- This is the HR monitoring page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('request.hrreport') }}"><i class="fa fa-building-o"></i> HR Monitoring</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<requestreport
			:companies="{{ json_encode($companies) }}"
			:templates="{{ json_encode($templates) }}"
			:formstatus="{{ json_encode(App\Form::getStatus()) }}"
			:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"			
			:autofetch="true"
			:exporturl="'{{ route('request.exporthr') }}'"
			:fetchurl="'{{ route('request.fetchhrrequest') }}'"
			></requestreport>		

		</div>
	</div>

@endsection

@section('styles')

	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">

@endsection

@section('js')

	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

@endsection