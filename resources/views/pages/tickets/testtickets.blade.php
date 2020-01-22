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
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

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
						
						<alltickets
						:autofetch="true"
						:daterange="'ticketsforapproval'"
						:status="{{ json_encode($status) }}"
						:states="{{ json_encode($states) }}"
						:priorities="{{ json_encode($priorities) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"	
						:fetchurl="'{{ route('datatable') }}'"
						></alltickets>	

					</div>
					<!-- /.tab-pane -->					
					<div class="tab-pane" id="ticket-ongoing">
	
						<ongoing
						:autofetch="true"
						:daterange="'ticketsforapproval'"
						:states="{{ json_encode($states) }}"
						:priorities="{{ json_encode($priorities) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"	
						:fetchurl="'{{ route('datatable') }}'"
						:status="1"
						></ongoing>	

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="tickets-complete">
	
						<completed
						:autofetch="true"
						:daterange="'ticketsforapproval'"
						:states="{{ json_encode($states) }}"
						:priorities="{{ json_encode($priorities) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"	
						:fetchurl="'{{ route('datatable') }}'"
						:status="2"
						></completed>	

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="tickets-cancelled">

						<cancelled
						:autofetch="true"
						:daterange="'ticketsforapproval'"
						:states="{{ json_encode($states) }}"
						:priorities="{{ json_encode($priorities) }}"
						:categories="{{ json_encode(App\Ticket::renderTableFilter()) }}"	
						:fetchurl="'{{ route('datatable') }}'"
						:status="3"
						></cancelled>	

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

	<script>
		
		
	</script>
	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

@endsection