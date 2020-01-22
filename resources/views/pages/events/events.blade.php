@extends('master')

@section('pageTitle', 'Events')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Calendar<small>This is the Calendar page</small></h1> --}}
	    <ol class="breadcrumb">    
	        <li>
	            <a href="{{ route('events') }}"><i class="fa fa-calendar"></i> Calendar</a>
	        </li>
	    </ol>


	</div>

@endsection

@section('content')

	@include('pages.calendar.calendarresources')

    @if($checker->hasModuleRoles(['Adding/Editing of Events']))
	<div class="row">
		<div class="col-sm-12 m-margin-b">

			<div class="pull-right">
			
				<a href="{{ route('event.create') }}" class="btn btn-primary s-margin-r">
					<i class="fa fa-plus s-margin-r"></i>Add Event
				</a>

			</div>
			
		</div>
	</div>
	@endif

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
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li>
						<a @click="onShow('forapprovalevents')"
						href="#events-forapproval" data-toggle="tab">
							<img src="/image/tabs/approval.png" class="tab--icon">
							For Approval
		                    @if($eventCount)
		                    <small class="label notif bg-red">{{ $eventCount }}</small>
		                    @endif
						</a>
					</li>				
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="events">

						<events
						:autofetch="true"
						:daterange="'events'"
						:fetchurl="'{{ route('event.fetch') }}'"
						></events>

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="events-forapproval">

						<eventparticipants ref="forapprovalevents"
						:categories="{{ json_encode(App\EventParticipant::renderStatusFilter()) }}"
						:status="{{ json_encode(App\EventParticipant::getStatus()) }}"
						:attendance="{{ json_encode(App\EventParticipant::getAttendance()) }}"
						:noattendance="true"
						:noattendanceaction="true"
						:showdaterange="true"
						:daterange="'eventparticipants'"
						:fetchurl="'{{ route('eventparticipant.fetchuserapprovalevents', $self->id) }}'"
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
