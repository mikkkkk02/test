@extends('master')

@section('pageTitle', 'BBLS Monitoring')

@section('breadcrumb')

	<div class="content-header">
		<h1>BBLS Monitoring<small>{{-- This is the BBLS monitoring page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('eventparticipant.report') }}"><i class="fa fa-building-o"></i> BBLS Monitoring</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<eventparticipantreport
			:companies="{{ json_encode($companies) }}"
			:status="{{ json_encode(App\EventParticipant::getStatus()) }}"
			:autofetch="true"
			:exporturl="'{{ route('eventparticipant.export') }}'"
			:fetchurl="'{{ route('eventparticipant.fetchreport') }}'"
			></eventparticipantreport>

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