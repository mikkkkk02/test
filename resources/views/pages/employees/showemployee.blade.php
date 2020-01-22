@extends('master')

@section('pageTitle', $employee->renderFullname())

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>{{ $employee->renderFullname() }}<small>This is the employee's bio</small></h1> --}}
	    <ol class="breadcrumb">		    
	        <li>
	            <a href="{{ route('employees') }}"><i class="fa fa-users"></i> Employees</a>
	        </li>
	        <li>
	        	<a href="{{ route('employee.show', $employee->id) }}">{{ $employee->renderFullname() }}</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					<div class="col-sm-4">
						<a href="{{ route('employee.show', $employee->id) }}">
							<div class="box-widget widget-user-2">
								<div class="widget-user-header">
									<div class="widget-user-image">
										<img class="img-circle border" src="{{ $employee->getProfilePhoto() }}" alt="User Avatar">
									</div>
									<!-- /.widget-user-image -->
									<h3 class="widget-user-username">{{ $employee->renderFullname2() }}</h3>
									<h5 class="widget-user-desc">ID: {{ $employee->id }}</h5>
								</div>
							</div>
						</a>
					</div>


					<div class="pull-right">

						@if($employee->id == $self->id)
						<a href="#" class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-selectformtemplate">
							<i class="fa fa-file-text-o s-margin-r"></i> Add Request
						</a>
						@endif

						@if($employee->id == $self->id)
						<a href="{{ route('settings') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-gear s-margin-r"></i> Settings
						</a>
						@endif

						@if($checker->hasModuleRoles(['Adding/Editing of Employee Profile']))
                            @if($employee->trashed())
    						<form id="employeeRestoreForm" method="post" action="{{ route('employee.restore', $employee->id) }}" data-redirect="true" class="ajax inline">

    							{{ csrf_field() }}

    							<button type="submit" class="btn btn-success">
    								<i class="fa fa-plus-circle s-margin-r"></i>Restore
    							</button>

    						</form>
    						@else
    						<form id="employeeArchiveForm" method="post" action="{{ route('employee.archive', $employee->id) }}" data-redirect="true" class="ajax inline">

    							{{ csrf_field() }}
    							{{ method_field('DELETE') }}

    							<button type="submit" class="btn btn-danger">
    								<i class="fa fa-minus-circle s-margin-r"></i>Archive
    							</button>

    						</form>
                            @endif
						@endif

					</div>
					
				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showemployee-details" data-toggle="tab">
							<i class="fa fa-user-o s-margin-r"></i>
							Profile
						</a>
					</li>
					<li>
						<a @click="onShow('employeerequests')" 
						href="#showemployee-forms" data-toggle="tab">
							<i class="fa fa-file-text-o s-margin-r"></i>
							Requests
						</a>
					</li>
					<li>
						<a @click="onShow('employeeevents')" 
						href="#showemployee-events" data-toggle="tab">
							<i class="fa fa-star-o s-margin-r"></i>
							Events
						</a>
					</li>
					<li>
						<a @click="onShow('employeedevelopments')"
						href="#showemployee-developments" data-toggle="tab">
							<i class="fa fa-book s-margin-r"></i>
							Developments
						</a>
					</li>
					{{-- <li>
						<a href="#showemployee-settings" data-toggle="tab">
							<i class="fa fa-gear s-margin-r"></i>
							Settings
						</a>
					</li> --}}

		            @if($checker->hasModuleRoles(['Adding/Editing of Employee User Responsibilities/Group']))
					<li>
						<a href="#showemployee-security" data-toggle="tab">
							<i class="fa fa-lock s-margin-r"></i>
							Security & Permissions
						</a>
					</li>
					@endif

				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showemployee-details">

						@include('pages.employees.employeedetails')					

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showemployee-forms">				

						<requests ref="employeerequests"
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"						
						:categories="{{ json_encode(App\Form::renderTableFilter()) }}"
						:fetchurl="'{{ route('request.fetchuserrequest', $employee->id) }}'"
						:daterange="'requestprofile'"
						></requests>

					</div>
					<!-- /.tab-pane -->	
					<div class="tab-pane" id="showemployee-events">				

						<eventparticipants ref="employeeevents"
						:categories="{{ json_encode(App\EventParticipant::renderStatusFilter()) }}"
						:status="{{ json_encode(App\EventParticipant::getStatus()) }}"
						:attendance="{{ json_encode(App\EventParticipant::getAttendance()) }}"
						:noemployee="true"
						:noteam="true"
						:nostatusaction="true"
						:noattendanceaction="true"
						:fetchurl="'{{ route('eventparticipant.fetchuserevents', $employee->id) }}'"						
						></eventparticipants>																

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showemployee-developments">				

						<idps ref="employeedevelopments"
						:categories="{{ json_encode(App\Idp::renderStatusFilter()) }}"
						:status="{{ json_encode(App\Idp::getStatus()) }}"
						:approvalstatus="{{ json_encode(App\Idp::getApprovalStatus()) }}"
						:noemployee="true"
						:noapprover="true"						
						:fetchurl="'{{ route('idp.fetchuseridps', $self->id) }}'"
						:daterange="'idpprofile'"						
						></idps>		


					</div>
					<!-- /.tab-pane -->		
					{{-- <div class="tab-pane" id="showemployee-settings">				

						@include('pages.employees.employeesettings')

					</div> --}}
					<!-- /.tab-pane -->	

		            @if($checker->hasModuleRoles(['Adding/Editing of Employee User Responsibilities/Group']))
					<div class="tab-pane" id="showemployee-security">
	
						@include('pages.employees.employeesecurity')					

					</div>
					@endif
					<!-- /.tab-pane -->
					
				</div>
				<!-- /.tab-content -->

			</div>

		</div>
	</div>			

@endsection

@section('styles')

    <!-- AdminLTE: Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/select2.min.css') }}">

    <!-- AdminLTE: Datepicker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datepicker/datepicker3.css') }}">
    <!-- AdminLTE: Timepicker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">
	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">    

	<!-- jQuery Chosen -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.min.css">

@endsection

@section('js')

	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js"></script>
    <!-- AdminLTE: Datepicker -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>	
	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>    
    <!-- AdminLTE: Timepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <!-- AdminLTE: Select2 -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/select2/select2.min.js') }}"></script>  	

	<!-- jQuery Chosen -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.jquery.min.js"></script>

@endsection

@section('modal')

	<div id="view-selectformtemplate" class="modal fade" tabindex="-1">
	    <div class="modal-dialog width--80">

			<selectrequest
			:id="'view-selectformtemplate'"
			:createurl="'{{ route('request.create') }}'"
			:createurl2="'{{ route('request.create.step2') }}'"
			:submiturl1="'{{ route('request.validate.step1') }}'"
			:submiturl2="'{{ route('request.store') }}'"
			:fetchurl="'{{ route('formtemplate.fetchforms') }}'"
			:templateid="'{{ isset($templateId) ? $templateId : '' }}'"
			></selectrequest>

	    </div>
	    <!-- /.modal-dialog -->
	</div>

@endsection