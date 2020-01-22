@extends('master')

@section('pageTitle', 'Request Updates')

@section('breadcrumb')

	<div class="content-header">
		<h1>Request Updates<small>{{-- This is the request updates page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="#"><i class="fa fa-file"></i> Request Updates</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">
	
			<div class="row">
				<div class="col-sm-12">

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					@if($checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms']))
					<li class="active">
						<a href="#forms-all" data-toggle="tab">All</a>
					</li>
					@endif					
					<li class="
					@if(!$checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms']))
					active
					@endif
					">
						<a href="#request-pending" data-toggle="tab">Pending</a>
					</li>
					<li class="">
						<a @click="onShow('requestdone')"
						href="#request-done" data-toggle="tab">Done</a>
					</li>
				</ul>

				<div class="tab-content">
					@if($checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms']))
					<div class="tab-pane active" id="forms-all">
						
						<formupdates
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\TempForm::getStatus()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('temprequest.fetch') }}'"
						:daterange="'requestsall'"
						></formupdates>						

					</div>
					@endif					
					<div class="tab-pane
					@if(!$checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms']))
					active
					@endif					
					" id="request-pending">

						<formupdates
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\TempForm::getStatus()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('temprequest.fetchapproval', $self->id) }}'"
						:daterange="'requestsall'"
						></formupdates>

					</div>
					<div class="tab-pane" id="request-done">

						<formupdates ref="requestdone"
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\TempForm::getStatus()) }}"
						:fetchurl="'{{ route('temprequest.fetchdone') }}'"
						:daterange="'requestsall'"						
						></formupdates>

					</div>																
				</div>

			</div>			

		</div>
	</div>	

@endsection

@section('styles')

	<!-- jQuery Chosen -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.min.css">

	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">	
	
@endsection

@section('js')

	<!-- jQuery Chosen -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.jquery.min.js"></script>

	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>	

@endsection