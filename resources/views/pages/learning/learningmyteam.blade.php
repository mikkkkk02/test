@extends('master')

@section('pageTitle', "My Team's Development")

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>My Team's Development<small>This is the your team's development page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('learning.myteam') }}"><i class="fa fa-book"></i> My Team's Development</a>
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

			            @if($checker->hasModuleRoles(['Adding/Editing of Learning Activities']))
						<button @click="onShow('idpdeleteactivity')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#idp-delete">
							<i class="fa fa-times s-margin-r"></i>Delete IDPs
						</button>
						<a href="{{ route('idp.create') }}" class="btn btn-primary s-margin-r">
							{{-- <i class="fa fa-plus s-margin-r"></i> --}}
							<img src="{{ asset('image/icons/edit-document-note-writing-review%402x.png') }}" class="tab--icon">
							Add IDP
						</a>
						@endif

			            @if($checker->hasModuleRoles(['Batch Uploading of Learning Activities']))
						<a class="btn btn-primary" data-toggle="modal" data-target="#import-idp">
							<i class="fa fa-upload s-margin-r"></i>Import
						</a>
						@endif

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					
		            @if($checker->hasModuleRoles(['Adding/Editing of Learning Activities']))
					<li class="active">
						<a href="#idp-allidps" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					@endif

					<li class="{{ $checker->hasModuleRoles(['Adding/Editing of Learning Activities']) ? : 'active' }}">
						<a @click="onShow('idpmyteam')"
						href="#idp-idps" data-toggle="tab">
							<img src="/image/tabs/team.png" class="tab--icon">
							Team
						</a>
					</li>					
					<li>
						<a @click="onShow('idpforapproval')"
						href="#idp-forapproval" data-toggle="tab">
							<img src="/image/tabs/approval.png" class="tab--icon">
							For Approval
		                    @if($idpCount)
		                    <small class="label notif bg-red">{{ $idpCount }}</small>
		                    @endif
		                </a>
					</li>					

				</ul>

				<div class="tab-content">

		            @if($checker->hasModuleRoles(['Adding/Editing of Learning Activities']))
					<div class="tab-pane active" id="idp-allidps">

						<idps
						:categories="{{ json_encode(App\User::renderEmployeeFilter()) }}"
						:status="{{ json_encode(App\Idp::getStatus()) }}"
						:approvalstatus="{{ json_encode(App\Idp::getApprovalStatus()) }}"
						:noapprover="true"
						:fetchurl="'{{ route('idp.fetch') }}'"
						:autofetch="true"
						:daterange="'idpsall'"						
						></idps>

					</div>
					@endif

					<!-- /.tab-pane -->					
					<div class="tab-pane {{ $checker->hasModuleRoles(['Adding/Editing of Learning Activities']) ? : 'active' }}" id="idp-idps">

						<idps ref="idpmyteam"
						:categories="{{ json_encode(App\User::renderEmployeeFilter()) }}"
						:status="{{ json_encode(App\Idp::getStatus()) }}"
						:approvalstatus="{{ json_encode(App\Idp::getApprovalStatus()) }}"
						:noapprover="true"
						:fetchurl="'{{ route('idp.fetchteamidps', $self->id) }}'"
						:autofetch="{{ $checker->hasModuleRoles(['Adding/Editing of Learning Activities']) ? 0 : 1 }}"
						:daterange="'idpsmyteam'"
						></idps>

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="idp-forapproval">

						<idps ref="idpforapproval"
						:categories="{{ json_encode(App\Idp::renderStatusFilter()) }}"
						:status="{{ json_encode(App\Idp::getStatus()) }}"
						:approvalstatus="{{ json_encode(App\Idp::getApprovalStatus()) }}"
						:noaction="'false'"
						:showapprovalaction="'true'"
						:fetchurl="'{{ route('idp.fetchuserapprovalidps', $self->id) }}'"
						:daterange="'idpsforapproval'"
						></idps>

					</div>
					<!-- /.tab-pane -->						
												
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>

@endsection

@section('modal')

    @if($checker->hasModuleRoles(['Adding/Editing of Learning Activities']))
	<!-- Delete IDP Modal -->
	<div id="idp-delete" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Delete IDPs</h4>
	            </div>         

				<form id="idpDeleteAllForm" method="post" action="{{ route('idp.deleteall') }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}
					{{ method_field('DELETE') }}

		            <div class="box box-widget modal-body">
						
						<idps ref="idpdeleteactivity"
						:categories="{{ json_encode(App\User::renderEmployeeFilter()) }}"
						:status="{{ json_encode(App\Idp::getStatus()) }}"
						:showcheckbox="true"
						:nodetails="true"
						:fetchurl="'{{ route('idp.fetch') }}'"
						></idps>

						<div class="modal-footer no-padding">
							<button type="submit" class="btn btn-primary pull-left">Remove</button>
							<a class="btn btn-default pull-left" data-dismiss="modal" aria-label="Close">Back</a>
						</div>

					</div>

				</form>

	        </div>
	        <!-- /.modal-content -->
	    </div>
	    <!-- /.modal-dialog -->
	</div>
	@endif

    @if($checker->hasModuleRoles(['Batch Uploading of Learning Activities']))
	<!-- Add User Modal -->
	<div id="import-idp" class="modal fade" tabindex="-1">
	    
		<import
		:header="'Import IDPs '"
		:text="'Upload the file containing the IDP data'"
		:importurl="'{{ route('idp.import') }}'"
		></import>

	</div>
	@endif

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