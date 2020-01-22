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

			@include('pages.formmodal.formdetails')

		</div>
	</div>	

@endsection

