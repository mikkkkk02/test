@extends('master')

@section('pageTitle', 'L&D Monitoring')

@section('breadcrumb')

	<div class="content-header">
		<h1>L&D Monitoring<small>{{-- This is the L&D monitoring page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('request.l&dreport') }}"><i class="fa fa-building-o"></i> L&D Monitoring</a>
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
			:notype="true"
			:showcost="true"
			:exporturl="'{{ route('request.exportld') }}'"			
			:fetchurl="'{{ route('request.fetchldrequest') }}'"
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