@extends('master')

@section('pageTitle', 'Requests')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Requests<small>This is the requests page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('requests') }}"><i class="fa fa-file"></i> Requests</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">
			<div class="row">
			
				@include('includes.profile')
	
			    @if($checker->hasModuleRoles(['Submission of Forms']))
					<div class="pull-right">
						<a href="#" class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-selectformtemplate">
							{{-- <i class="fa fa-file-text-o s-margin-r"></i>  --}}
							<img src="{{ asset('image/icons/edit-document-note-writing-review%402x.png') }}" class="tab--icon">
							Add Request
						</a>
						{{-- <searching
						:header="'What requests do you want to make?'"
						:buttontext="'Do you want to create this request?'"
						:fetchurl="'{{ route('formtemplate.fetchforms') }}'"
						></searching> --}}
					</div>
				@endif

			</div>


			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					{{-- @if($checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms'])) --}}
					<li class="active">
						<a href="#forms-all" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All Pending
						</a>
					</li>
					{{-- @endif --}}
					<li class="
					{{-- @if(!$checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms'])) --}}
					{{-- active --}}
					{{-- @endif --}}
					">
						<a href="#forms-templates" data-toggle="tab">
							<img src="{{ asset('image/icons/edit-document-note-writing-review%402x.png') }}" class="tab--icon invert">
							Requests
						</a>
					</li>
					<li class="">
						<a @click="onShow('requestarchive')"
						href="#forms-templates-archive" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archives
						</a>
					</li>								
				</ul>

				<div class="tab-content">
					{{-- @if($checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms'])) --}}
					<div class="tab-pane active" id="forms-all">

						<requests
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"
						:autofetch="true"
						:norequestedby="true"
						:fetchurl="'{{ route('request.fetchpendingrequest', $self->id) }}'"
						:daterange="'requestsall'"						
						></requests>

					</div>
					<!-- /.tab-pane -->
					{{-- @endif --}}
					<div class="tab-pane
					{{-- @if(!$checker->hasModuleRoles(['Creating/Designing/Editing/Removing of Forms'])) --}}
					{{-- active --}}
					{{-- @endif --}}
					" id="forms-templates">

						<requests
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('request.fetchuserrequest', $self->id) }}'"
						:withdrawurl="'{{ route('request.withdrawAll', $self->id) }}'"
						:daterange="'requestsmyown'"							
						></requests>

					</div>
					<div class="tab-pane" id="forms-templates-archive">

						<requests ref="requestarchive"
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"
						:norequestedby="true"
						:fetchurl="'{{ route('request.fetchuserrequestarchive', $self->id) }}'"
						:daterange="'requestsarchive'"							
						></requests>

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

	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>	

@endsection

@section('modal')

	@if($checker->hasModuleRoles(['Submission of Forms']))
	<!-- Select Form field Modal -->
	@include('includes.modals.forms.selectformtemplate')
	@endif

@endsection