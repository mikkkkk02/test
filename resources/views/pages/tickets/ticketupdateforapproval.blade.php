@extends('master')

@section('pageTitle', 'Ticket Updates For Approval')

@section('breadcrumb')

	<div class="content-header">
		<h1>Ticket Updates For Approval<small>{{-- This is the Tickets page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('tickets') }}"><i class="fa fa-ticket"></i> Ticket Updates</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row l-margin-t">
		<div class="col-sm-12">

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#ticketupdate-all" data-toggle="tab">All</a>
					</li>						
					<li>
						<a @click="onShow('ticketupdateforapproval')"
						href="#ticketupdate-approval" data-toggle="tab">For Approval</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="ticketupdate-all">

						<tempticketupdates
						:status="{{ json_encode($status) }}"
						:categories="{{ json_encode(App\TempTicketUpdate::renderTableFilter()) }}"	
						:fetchurl="'{{ route('tempticketupdate.fetch') }}'"
						:autofetch="true"
						:daterange="'ticketsforapproval'"
						></tempticketupdates>

					</div>
					<!-- /.tab-pane -->					
					<div class="tab-pane" id="ticketupdate-approval">

						<tempticketupdates ref="ticketupdateforapproval"
						:status="{{ json_encode($status) }}"
						:categories="{{ json_encode(App\TempTicketUpdate::renderTableFilter()) }}"	
						:fetchurl="'{{ route('tempticketupdate.fetchdone') }}'"
						:daterange="'ticketsongoing'"							
						></tempticketupdates>

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