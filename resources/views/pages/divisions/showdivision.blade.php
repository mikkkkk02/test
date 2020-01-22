@extends('master')

@section('pageTitle', $division->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $division->name }}<small>{{ $division->description }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('divisions') }}"><i class="fa fa-industry"></i> Groups</a>
	        </li>
	        <li>
	        	<a href="{{ route('division.show', $division->id) }}">{{ $division->name }}</a>
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
						
						@if($division->trashed())
						<form id="divisionRestoreForm" method="post" action="{{ route('division.restore', $division->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-success s-margin-r">
								<i class="fa fa-plus-circle s-margin-r"></i>Restore
							</button>

						</form>
						@else
						<form id="divisionArchiveForm" method="post" action="{{ route('division.archive', $division->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}
							{{ method_field('DELETE') }}

							<button type="submit" class="btn btn-danger s-margin-r">
								<i class="fa fa-minus-circle s-margin-r"></i>Archive
							</button>

						</form>
						@endif

						<button @click="onShow('divisionadddepartments')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-adddepartment">
							<i class="fa fa-plus s-margin-r"></i>Add Departments
						</button>
						<button @click="onShow('divisionremovedepartments')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removedepartment">
							<i class="fa fa-times s-margin-r"></i>Remove Departments
						</button>
						
					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showdivision-details" data-toggle="tab">Details</a>
					</li>
					<li>
						<a @click="onShow('divisiondepartments')"
						href="#showdivision-departments" data-toggle="tab">Departments</a>
					</li>
					<li>
						<a @click="onShow('divisionteams')"
						href="#showdivision-teams" data-toggle="tab">Teams</a>
					</li>
					<li>
						<a @click="onShow('divisionemployees')"
						href="#showdivision-employees" data-toggle="tab">Employees</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showdivision-details">
						
						@include('pages.divisions.divisiondetails')							

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showdivision-departments">
						
						<departments ref="divisiondepartments"
						:nodivision="true"
						:fetchurl="'{{ route('department.fetchdivisiondepartments', $division->id) }}'"
						></departments>

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showdivision-teams">
						
						<teams ref="divisionteams"
						:fetchurl="'{{ route('team.fetchdivisionteams', $division->id) }}'"
						></teams>

					</div>
					<!-- /.tab-pane -->					
					<div class="tab-pane" id="showdivision-employees">				

						<employees ref="divisionemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:nocompany="true"
						:nodivision="true"
						:fetchurl="'{{ route('employee.fetchdivisionemployees', $division->id) }}'"
						></employees>																

					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection

@section('modal')

	<!-- Add Department Modal -->
	<div id="view-adddepartment" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Add Departments</h4>
	            </div>         

				<form id="companyAddGroupForm" method="post" action="{{ route('division.adddepartments', $division->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<departments ref="divisionadddepartments"
						:showcheckbox="true"
						:fetchurl="'{{ route('department.fetchnotdivisiondepartments', $division->id) }}'"
						></departments>

						<div class="modal-footer no-padding">
							<button type="submit" class="btn btn-primary pull-left">Add</button>
							<a class="btn btn-default pull-left" data-dismiss="modal" aria-label="Close">Back</a>
						</div>

					</div>

				</form>

	        </div>
	        <!-- /.modal-content -->
	    </div>
	    <!-- /.modal-dialog -->
	</div>

	<!-- Remove Department Modal -->
	<div id="view-removedepartment" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Remove Departments</h4>
	            </div>         

				<form id="companyRemoveGroupForm" method="post" action="{{ route('division.removedepartments', $division->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<departments ref="divisionremovedepartments"
						:showcheckbox="true"
						:fetchurl="'{{ route('department.fetchdivisiondepartments', $division->id) }}'"
						></departments>

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

@endsection