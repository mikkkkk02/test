@extends('master')

@section('pageTitle', 'Create Responsibility')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Responsibility<small>{{-- This is the create responsibility page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('employees') }}"><i class="fa fa-users"></i> Groups</a>
	        </li>
	        <li>
	        	<a href="{{ route('group.create') }}">New Responsibility</a>
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
						<a href="#group-details" data-toggle="tab">Details</a>
					</li>													
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="group-details">
						
						@include('pages.groups.groupdetails')		

					</div>
					<!-- /.tab-pane -->									
				</div>
				<!-- /.tab-content -->
				
			</div>

		</div>
	</div>		

@endsection
