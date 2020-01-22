@extends('master')

@section('pageTitle', 'Create Location')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Location<small>{{-- This is the create location page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('locations') }}"><i class="fa fa-industry"></i> Locations</a>
	        </li>
	        <li>
	        	<a href="{{ route('location.create') }}">New Location</a>
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
						<a href="#showlocation-details" data-toggle="tab">Details</a>
					</li>				
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showlocation-details">
						
						@include('pages.locations.locationdetails')					

					</div>
					<!-- /.tab-pane -->				
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection

@section('styles')
	
	<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/select2.min.css') }}">

@endsection

@section('js')

	<!-- AdminLTE: Select2 -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/select2/select2.min.js') }}"></script>  	

@endsection