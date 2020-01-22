@extends('master')

@section('pageTitle', 'Create Company')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Company<small>{{-- This is the create company page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('companies') }}"><i class="fa fa-building-o"></i> Companies</a>
	        </li>
	        <li>
	        	<a href="{{ route('company.create') }}">New Company</a>
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
						<a href="#showcompany-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showcompany-details">
						
						@include('pages.companies.companydetails')

					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection
