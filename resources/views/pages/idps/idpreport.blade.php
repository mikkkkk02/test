@extends('master')

@section('pageTitle', 'IDP Monitoring')

@section('breadcrumb')

	<div class="content-header">
		<h1>IDP Monitoring<small>{{-- This is the idp monitoring page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('idp.report') }}"><i class="fa fa-building-o"></i> IDP Monitoring</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<idpreport
			:companies="{{ json_encode($companies) }}"
			:years="{{ json_encode(App\Idp::getRecentYears()) }}"
			:learningtypes="{{ json_encode(App\Idp::getLearningActivityType()) }}"
			:competencytypes="{{ json_encode(App\Idp::getCompetencyType()) }}"
			:specificcompetencies="{{ $specificcompetencies }}"
			:status="{{ json_encode(App\Idp::getStatus()) }}"
			:autofetch="true"
			:exporturl="'{{ route('idp.export') }}'"
			:fetchurl="'{{ route('idp.fetch') }}'"
			></idpreport>

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