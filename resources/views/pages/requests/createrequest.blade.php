@extends('modal')

@section('pageTitle', 'Create Request')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Request<small>{{ $formTemplate->name }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('requests') }}"><i class="fa fa-file"></i> Requests</a>
	        </li>
	        <li>
	        	<a href="{{ route('request.create', $formTemplate->id) }}">Create {{ $formTemplate->name }}</a>
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
						<a href="#showform-details" data-toggle="tab">Request</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showform-details">

						@include('pages.forms.formdetails')

					</div>
					<!-- /.tab-pane -->
				</div>
			</div>

		</div>
	</div>	

@endsection

