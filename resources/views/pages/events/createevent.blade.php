@extends('master')

@section('pageTitle', 'Create Event')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Event<small>{{-- This is the create event page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('events') }}"><i class="fa fa-building"></i> Calendar</a>
	        </li>
	        <li>
	        	<a href="{{ route('event.create') }}">New Event</a>
	        </li>
	    </ol>
	</div>	

@endsection

@section('content')

	<div class="row l-margin-t">
		<div class="col-sm-12">

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showevent-details" data-toggle="tab">Details</a>
					</li>				
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showevent-details">

						@include('pages.events.eventdetails')				

					</div>
					<!-- /.tab-pane -->									
				</div>
				<!-- /.tab-content -->
				
			</div>

		</div>
	</div>			

@endsection
