@extends('master')

@section('pageTitle', 'Tickets')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Tickets<small>This is the Tickets page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('tickets') }}"><i class="fa fa-ticket"></i> Tickets</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		
		<div class="col-sm-12">

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#ticket-all" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>					
					<li>
						<a @click="onShow('ticketongoing')"
						href="#ticket-ongoing" data-toggle="tab">
							<i class="fa fa-clock-o s-margin-r"></i>
							On-going
		                    @if($ticketCount)
		                    <small class="label notif bg-red">{{ $ticketCount }}</small>
		                    @endif
						</a>
					</li>
					<li>
						<a @click="onShow('ticketcompleted')"
						href="#tickets-complete" data-toggle="tab">
							<i class="fa fa-check s-margin-r"></i>
							Completed
						</a>
					</li>
					<li>
						<a @click="onShow('ticketcancelled')"
						href="#tickets-cancelled" data-toggle="tab">
							<i class="fa fa-close s-margin-r"></i>
							Cancelled
						</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="ticket-all">

						<tickets
						:status="{{ json_encode($status) }}"
						:states="{{ json_encode($states) }}"
						:priorities="{{ json_encode($priorities) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"
						:fetchurl="'{{ route('ticket.fetchall') }}'"
						:autofetch="true"
						:daterange="'ticketsforapproval'"							
						></tickets>

					</div>
					<!-- /.tab-pane -->					
					<div class="tab-pane" id="ticket-ongoing">

						<tickets ref="ticketongoing"
						:status="{{ json_encode($status) }}"
						:states="{{ json_encode($states) }}"
						:priorities="{{ json_encode($priorities) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"	
						:fetchurl="'{{ route('ticket.fetchongoing') }}'"
						:daterange="'ticketsongoing'"							
						></tickets>

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="tickets-complete">

						<tickets ref="ticketcompleted"
						:status="{{ json_encode($status) }}"
						:states="{{ json_encode($states) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"						
						:priorities="{{ json_encode($priorities) }}"						
						:fetchurl="'{{ route('ticket.fetchcompleted') }}'"
						:daterange="'ticketscompleted'"							
						></tickets>

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="tickets-cancelled">

						<tickets ref="ticketcancelled"
						:status="{{ json_encode($status) }}"
						:states="{{ json_encode($states) }}"
						:priorities="{{ json_encode($priorities) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"				
						:nostate="true"
						:fetchurl="'{{ route('ticket.fetchcancelled') }}'"
						:daterange="'ticketscancelled'"							
						></tickets>

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