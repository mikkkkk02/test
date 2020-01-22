@extends('master')

@section('pageTitle', 'Create Form Template')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Form Template<small>{{-- This is the create form template page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('formtemplates') }}"><i class="fa fa-file"></i> Form Template</a>
	        </li>
	        <li>
	        	<a href="{{ route('formtemplate.create') }}">New Form Template</a>
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
						<a href="#showform-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showform-details">

						@include('pages.formtemplates.formtemplatedetails')

					</div>			
				</div>
			</div>

		</div>
	</div>		

@endsection
