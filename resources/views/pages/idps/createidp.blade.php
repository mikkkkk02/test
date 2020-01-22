@extends('master')

@section('pageTitle', 'Create IDP')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create IDP<small>{{-- This is the create IDP page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('idps') }}"><i class="fa fa-book"></i> IDP</a>
	        </li>
	        <li>
	        	<a href="{{ route('idp.create') }}">New IDP</a>
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
						<a href="#showidp-details" data-toggle="tab">Details</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showidp-details">

						@include('pages.idps.idpdetails')					

					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->

			</div>

		</div>
	</div>			

@endsection
