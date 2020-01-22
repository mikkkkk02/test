@extends('master')

@section('pageTitle', 'Create Room')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Room<small>{{-- This is the create location page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('rooms') }}"><i class="fa fa-cube"></i> Rooms</a>
	        </li>
	        <li>
	        	<a href="{{ route('room.create') }}">New Room</a>
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
						
						@include('pages.rooms.roomdetails')					

					</div>
					<!-- /.tab-pane -->				
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection

@section('js')

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js"></script>

@endsection