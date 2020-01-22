@extends('master')

@section('pageTitle', 'Create Government Form')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Government Form<small>{{-- This is the create group page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('governmentforms') }}"><i class="fa fa-industry"></i> Government Forms</a>
	        </li>
	        <li>
	        	<a href="{{ route('governmentform.create') }}">New Government Form</a>
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
						<a href="#showgovernmentform-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showgovernmentform-details">
						
						@include('pages.governmentforms.governmentformdetails')							

					</div>
					<!-- /.tab-pane -->				
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection
