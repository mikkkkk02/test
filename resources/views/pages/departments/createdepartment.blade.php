@extends('master')

@section('pageTitle', 'Create Department')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Department<small>{{-- This is the create department page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('departments') }}"><i class="fa fa-industry"></i> Departments</a>
	        </li>
	        <li>
	        	<a href="{{ route('department.create') }}">New Department</a>
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
						<a href="#showdepartment-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showdepartment-details">
						
						@include('pages.departments.departmentdetails')							

					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection