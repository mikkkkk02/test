@if($checker->hasModuleRoles(['Generating of Ticketing Reports']))

	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#calendar-events" data-toggle="tab">
					<i class="fa fa-object-group s-margin-r"></i>
					Reports
				</a>
			</li>
			<li>
				<a @click="renderCalendar('meetingroomcalendar')"
				href="#calendar-meetingroom" data-toggle="tab">
					<img src="{{ asset('image/tabs/team.png') }}" class="tab--icon invert">
					{{-- <i class="fa fa-users"></i> --}}
					Meeting Room
				</a>
			</li>		
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="calendar-events">			

				<ticketreport
				:generatepercent="'{{ route('ticket.generatepercent') }}'"
				:generatepiechart="'{{ route('ticket.generatepiechart') }}'"
				:generatebarchart="'{{ route('ticket.generatebarchart') }}'"
				></ticketreport>

			</div>
			<div class="tab-pane" id="calendar-meetingroom">

				<calendar ref="meetingroomcalendar"
				:name="'reservation-calendar'"
				:events="{{ json_encode($meetinRoomEvents) }}"
				></calendar>

			</div>		
		</div>
	</div>			

	@section('styles')

		<!-- AdminLTE: Daterange picker -->
	    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">

	    <!-- FullCalendar-->
	    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.css') }}">
	    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.print.css') }}" media="print">	

		<!-- AdminLTE: Daterange picker -->
	    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">	    

	@endsection

	@section('js')

	    <!-- FullCalendar -->
	    <script type="text/javascript" src="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
		<!-- AdminLTE: Datepicker -->
		<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
		<!-- AdminLTE: Daterange picker -->
		<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script> 		

	@endsection

@else

	@include('pages.calendar.calendarresources')

	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#calendar-events" data-toggle="tab">Events</a>
			</li>
			<li>
				<a @click="renderCalendar('meetingroomcalendar')"
				href="#calendar-meetingroom" data-toggle="tab">Meeting Room</a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="calendar-events">

				<calendar
				:name="'events-calendar'"
				:autoload="true"
				:events="{{ json_encode($events) }}"
				></calendar>

			</div>
			<div class="tab-pane" id="calendar-meetingroom">

				<calendar ref="meetingroomcalendar"
				:name="'meetingroom-calendar'"
				:events="{{ json_encode($meetinRoomEvents) }}"
				></calendar>

			</div>		
		</div>
	</div>

@endif

<div class="row">
	<div class="col-sm-12">
		<div class="box no-border">
			<div class="box-body">

				<requests
				:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
				:formstatus="{{ json_encode(App\Form::getStatus()) }}"
				:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"
				:headericon="'fa-file-text-o'"
				:header="'Ongoing Requests'"				
				:paginationlimit="5"				
				:autofetch="true"
				:norequestedby="true"
				:fetchurl="'{{ route('request.fetchuserongoingrequest', $self->id) }}'"
				></requests>

			</div>
			<!-- /.box-body -->
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="box no-border">
			<div class="box-body">

				<request-logs
				:categories="''"
				:headericon="'fa-files-o'"
				:header="'Track Request'"
				:fetchurl="'{{ route('requestlog.fetch') }}'"
				></request-logs>

			</div>
			<!-- /.box-body -->
		</div>
	</div>
</div>
