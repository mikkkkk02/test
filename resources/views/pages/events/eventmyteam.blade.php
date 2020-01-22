@extends('master')

@section('pageTitle', "My Team's events")

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>My Team's Events<small>This is the event's page</small></h1> --}}
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('events') }}"><i class="fa fa-calendar"></i> My Team's Events</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">			
				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#events" data-toggle="tab">
							<img src="/image/tabs/team.png" class="tab--icon">
							Team
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="events">

						<eventparticipants
						:categories="{{ json_encode($categories) }}"
						:status="{{ json_encode(App\EventParticipant::getStatus()) }}"
						:attendance="{{ json_encode(App\EventParticipant::getAttendance()) }}"
						:nostatusaction="true"
						:noattendanceaction="true"
						:showdaterange="true"
						:daterange="'eventparticipants'"
						:fetchurl="'{{ route('eventparticipant.fetchteamevents', $self->id) }}'"
						:autofetch="true"
						></eventparticipants>												
						
					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection

@section('styles')

	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">

@endsection

@section('js')

	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

@endsection