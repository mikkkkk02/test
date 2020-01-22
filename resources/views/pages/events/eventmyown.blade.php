@extends('master')

@section('pageTitle', 'My Events')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>My Calendar<small>This is the event's page</small></h1> --}}
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('events.myown') }}"><i class="fa fa-calendar"></i> My Events</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12 m-margin-b">

			<div class="pull-right">

				<button id="eventSyncBtn" class="btn btn-success s-margin-r">
					<i class="fa fa-refresh s-margin-r"></i>Sync
				</button>

			</div>
			
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12">
			<div class="box no-border">
				<div class="box-body no-padding">

					<calendar
					:name="'events-calendar'"
					:autoload="true"
					:events="{{ json_encode($events) }}"
					></calendar>	

				</div>
				<!-- /.box-body -->
			</div>
		<!-- /. box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->

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
							<img src="/image/tabs/individual.png" class="tab--icon">
							Individual Events
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="events">

						<eventparticipants
						:categories="{{ json_encode(App\EventParticipant::renderStatusFilter()) }}"
						:status="{{ json_encode(App\EventParticipant::getStatus()) }}"
						:attendance="{{ json_encode(App\EventParticipant::getAttendance()) }}"
						:noemployee="true"
						:noteam="true"
						:nostatusaction="true"
						:noattendanceaction="true"
						:fetchurl="'{{ route('eventparticipant.fetchuserevents', $self->id) }}'"
						:autofetch="true"
						:showdaterange="true"
						:daterange="'eventparticipants'"
						></eventparticipants>
						
					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection

@section('data')
cal_client = '{{ env('GOOGLE_CAL_CLIENT_ID') }}',
cal_api = '{{ env('GOOGLE_CAL_KEY') }}',
eventsJSON = '{!! json_encode($events) !!}',
@endsection

@section('styles')

    <!-- FullCalendar-->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.print.css') }}" media="print">	

	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">

@endsection

@section('js')

	<script async defer src="https://apis.google.com/js/api.js"
	onload="this.onload=function(){};app.calendar.init()"
	onreadystatechange="if(this.readyState === 'complete') this.onload()">
	</script>

    <!-- FullCalendar -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.js') }}"></script>

	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>    

@endsection