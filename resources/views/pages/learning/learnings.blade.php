@extends('master')

@section('pageTitle', 'Learning and Development')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Learning & Development<small>This is the Learning and Development page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('learnings') }}"><i class="fa fa-book"></i> Learning & Development</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					<div class="pull-right">
						
						<a href="#" class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#learningrequest">
							<img src="{{ asset('image/icons/edit-document-note-writing-review%402x.png') }}" class="tab--icon">
							Submit L&D?
						</a>
						
					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">

		            @if($checker->hasModuleRoles(['Adding/Editing of Learning Activities']))
					<li class="active">
						<a href="#learning-activities" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>					
					@endif

					<li class="{{ $checker->hasModuleRoles(['Adding/Editing of Learning Activities']) ? : 'active' }}">
						<a @click="onShow('learningmyown')"
						href="#learning-myown" data-toggle="tab">
							<img src="/image/tabs/individual.png" class="tab--icon">
							Individual
						</a>
					</li>
					
					<li>
						<a @click="onShow('learning')"
						href="#learning-myteam" data-toggle="tab">
							<img src="/image/tabs/team.png" class="tab--icon">
							Team
						</a>
					</li>
					<li>
						<a @click="onShow('learningforapproval')"
						href="#learning-forapproval" data-toggle="tab">
							<img src="/image/tabs/approval.png" class="tab--icon">
							For Approval
						</a>
					</li>					

				</ul>

				<div class="tab-content">

		            @if($checker->hasModuleRoles(['Adding/Editing of Learning Activities']))
					<div class="tab-pane active" id="learning-activities">

						<requests
						:categories="{{ json_encode(App\User::renderEmployeeFilter()) }}"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"						
						:notype="true"
						:noticket="true"
						:nocategory="true"
						:noassignedto="true"
						:nodetails="true"
						:autofetch="true"
						:fetchurl="'{{ route('request.fetchldrequest') }}'"
						:daterange="'learningsall'"
						></requests>

					</div>
					<!-- /.tab-pane -->	
					@endif

					<div class="tab-pane {{ $checker->hasModuleRoles(['Adding/Editing of Learning Activities']) ? : 'active' }}" id="learning-myown">

						<requests ref="learningmyown"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"						
						:notype="true"
						:noticket="true"
						:nocategory="true"
						:noassignedto="true"
						:nodetails="true"
						:norequestedby="true"
						:autofetch="{{ $checker->hasModuleRoles(['Adding/Editing of Learning Activities']) ? 0 : 1 }}"
						:fetchurl="'{{ route('request.fetchuserldrequest', $self->id) }}'"
						:daterange="'learningsmyown'"
						></requests>

					</div>
					<!-- /.tab-pane -->		

					<div class="tab-pane" id="learning-myteam">

						<requests ref="learning"
						:categories="{{ json_encode($self->renderSubordinateFilter()) }}"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"						
						:notype="true"
						:noticket="true"
						:nocategory="true"
						:noassignedto="true"
						:nodetails="true"
						:fetchurl="'{{ route('request.fetchteamldrequest', $self->id) }}'"
						:daterange="'learningsmyteam'"
						></requests>						

					</div>
					<!-- /.tab-pane -->						
					<div class="tab-pane" id="learning-forapproval">

						<requests ref="learningforapproval"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"						
						:notype="true"
						:noticket="true"
						:nocategory="true"
						:noassignedto="true"
						:nodetails="true"
						:fetchurl="'{{ route('request.fetchuserldapprovalrequest', $self->id) }}'"
						:daterange="'learningsforapproval'"
						></requests>						

					</div>
					<!-- /.tab-pane -->										
									
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>

@endsection

@section('modal')

<div id="learningrequest" class="modal fade" tabindex="-1">
    <div class="modal-dialog width--80">

		<selectrequest
		:id="'learningrequest'"
		:header="'Add L&D'"
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

@section('styles')

	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">

@endsection

@section('js')

	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

@endsection
