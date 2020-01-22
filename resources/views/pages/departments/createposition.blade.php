@extends('master')

@section('pageTitle', 'Create Position')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Position<small>{{-- This is the create position page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('departments') }}"><i class="fa fa-building"></i> Departments</a>
	        </li>f
	        <li>
	        	<a href="{{ route('position.create') }}">New Position</a>
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
						<a href="#showposition-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showposition-details">
						
						@include('pages.departments.positiondetails')						

					</div>
					<!-- /.tab-pane -->				
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection
