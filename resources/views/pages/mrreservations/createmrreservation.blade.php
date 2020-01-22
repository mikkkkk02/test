@extends('master')

@section('pageTitle', 'Create Meeting Room Reservation')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Meeting Room Reservation<small>{{-- This is the create location page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('mrreservations') }}"><i class="fa fa-users"></i> Meeting Room Reservations</a>
	        </li>
	        <li>
	        	<a href="{{ route('mrreservation.create') }}">New Meeting Room Reservation</a>
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
						<a href="#showroom-details" data-toggle="tab">Details</a>
					</li>				
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showroom-details">
						
						@include('pages.mrreservations.mrreservationdetails')					

					</div>
					<!-- /.tab-pane -->				
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection
