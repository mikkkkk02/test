@extends('master')

@section('pageTitle', 'Register Employee')

@section('breadcrumb')

	<div class="content-header">
		<h1>Register Employee<small>{{-- This is the create employee's page --}}</small></h1>
	    <ol class="breadcrumb">		    
	        <li>
	            <a href="{{ route('employees') }}"><i class="fa fa-users"></i> Employees</a>
	        </li>
	        <li>
	        	<a href="{{ route('employee.create') }}">New Employee</a>
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
						<a href="#showemployee-details" data-toggle="tab">Details</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showemployee-details">

						@include('pages.employees.employeedetails')					

					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->

			</div>

		</div>
	</div>			

@endsection