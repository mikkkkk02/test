@extends('master')

@section('pageTitle', $event->title)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $event->title }}<small>{{ $event->description }}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('events') }}"><i class="fa fa-building"></i> Calendar</a>
	        </li>
	        <li>
	        	<a href="{{ route('event.show', $event->id) }}">{{ $event->title }}</a>
	        </li>
	    </ol>
	</div>	

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					<div class="pull-right">
						
						@if($event->canStillRegister() && !$event->isRegistered($self->id))
						<a href="{{ $event->renderAttendURL() }}" class="btn btn-success s-margin-r">
							<i class="fa fa-plus-circle s-margin-r"></i>Attend Event
						</a>
						@endif

						@if($event->isRegistered($self->id))
	{{-- 					<button class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#cancel-attendance">
							<i class="fa fa-minus-circle s-margin-r"></i>Cancel Participation
						</button> --}}
						@endif

						@if($checker->hasModuleRoles(['Add/Remove Participants to Event']))
						<button @click="onShow('eventparticipantsmodal')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#remove-participants">
							<i class="fa fa-user-times s-margin-r"></i>Remove Participants
						</button>
						@endif

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showevent-details" data-toggle="tab">Details</a>
					</li>
					@if($checker->hasModuleRoles(['Add/Remove Participants to Event']))
					<li>
						<a @click="onShow('eventparticipants')"
						href="#showevent-participants" data-toggle="tab">Participants</a>
					</li>
					<li>
						<a @click="onShow('eventinqueue')"
						href="#showevent-inqueue" data-toggle="tab">In Queue</a>
					</li>					
					<li>
						<a @click="onShow('eventpendings')"
						href="#showevent-pending" data-toggle="tab">Pending</a>
					</li>
					@endif
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showevent-details">

						@include('pages.events.eventdetails')

					</div>
					<!-- /.tab-pane -->
					@if($checker->hasModuleRoles(['Add/Remove Participants to Event']))
					<div class="tab-pane" id="showevent-participants">

						<eventparticipants ref="eventparticipants"
						:categories="{{ json_encode(App\EventParticipant::renderAttendanceFilter()) }}"						
						:status="{{ json_encode($status) }}"
						:attendance="{{ json_encode($attendance) }}"
						:noevent="true"
						:noeventdetails="true"
						:noapprover="true"
						:nostatus="true"
						:nostatusaction="true"
						:showdaterange="false"
						{{-- :noattendanceaction="'{{ $checker->hasModuleRoles(['Confirm Attendance of Participants')] ? : 'true'  }}'" --}}
						:fetchurl="'{{ route('eventparticipant.fetcheventparticipants', $event->id) }}'"
						></eventparticipants>

					</div>
					<!-- /.tab-pane -->	
					<div class="tab-pane" id="showevent-inqueue">

						<eventparticipants ref="eventinqueue"
						:status="{{ json_encode($status) }}"
						:attendance="{{ json_encode($attendance) }}"
						:noevent="true"
						:noeventdetails="true"
						:noapprover="true"
						:nostatus="true"
						:nostatusaction="true"
						:noattendanceaction="true"
						:showdaterange="false"
						:fetchurl="'{{ route('eventparticipant.fetcheventinqueue', $event->id) }}'"
						></eventparticipants>

					</div>
					<!-- /.tab-pane -->						
					<div class="tab-pane" id="showevent-pending">				

						<eventparticipants ref="eventpendings"
						:status="{{ json_encode($status) }}"
						:attendance="{{ json_encode($attendance) }}"
						:noevent="true"
						:noeventdetails="true"
						:nostatusaction="true"
						:noattendance="true"
						:noattendanceaction="true"
						:showdaterange="false"
						:fetchurl="'{{ route('eventparticipant.fetcheventpendings', $event->id) }}'"
						></eventparticipants>

					</div>
					<!-- /.tab-pane -->										
					@endif
				</div>
				<!-- /.tab-content -->
				
			</div>

		</div>
	</div>			

@endsection

@section('modal')

	@include('includes.modals.events.cancelattendance')

	@if($checker->hasModuleRoles(['Add/Remove Participants to Event']))
	<!-- Remove Participants -->
	<div id="remove-participants" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Remove participants</h4>
	            </div>         

				<form id="eventRemoveParticipantsForm" method="post" action="{{ route('event.removeparticipants', $event->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<eventparticipants ref="eventparticipantsmodal"
						:status="{{ json_encode($status) }}"
						:attendance="{{ json_encode($attendance) }}"
						:showcheckbox="true"
						:noevent="true"
						:noeventdetails="true"
						:noapprover="true"
						:nostatus="true"
						:nostatusaction="true"
						:noattendance="true"
						:noattendanceaction="true"
						:fetchurl="'{{ route('eventparticipant.fetcheventparticipants', $event->id) }}'"
						></eventparticipants>

						<div class="modal-footer no-padding">
							<button type="submit" class="btn btn-primary pull-left">Remove</button>
							<a class="btn btn-default pull-left" data-dismiss="modal" aria-label="Close">Back</a>
						</div>

					</div>

				</form>

	        </div>
	        <!-- /.modal-content -->
	    </div>
	    <!-- /.modal-dialog -->
	</div>
	@endif

@endsection