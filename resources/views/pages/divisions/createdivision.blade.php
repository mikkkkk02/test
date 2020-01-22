@extends('master')

@section('pageTitle', 'Create Group')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Group<small>{{-- This is the create group page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('divisions') }}"><i class="fa fa-industry"></i> Groups</a>
	        </li>
	        <li>
	        	<a href="{{ route('division.create') }}">New Group</a>
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
						<a href="#showdivision-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showdivision-details">
						
						@include('pages.divisions.divisiondetails')							

					</div>
					<!-- /.tab-pane -->				
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection
