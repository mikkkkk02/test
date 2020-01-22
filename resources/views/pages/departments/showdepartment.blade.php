@extends('master')

@section('pageTitle', $department->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $department->name }}<small>{{ $department->description }}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('departments') }}"><i class="fa fa-industry"></i> Departments</a>
	        </li>
	        <li>
	        	<a href="{{ route('department.show', $department->id) }}">{{ $department->name }}</a>
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
						
						<button @click="onShow('departmentaddpositions')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-addpositions">
							<i class="fa fa-map-marker s-margin-r"></i>Add Positions
						</button>
						<button @click="onShow('departmentremovepositions')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removepositions">
							<i class="fa fa-map-marker s-margin-r"></i>Remove Positions
						</button>
						<button @click="onShow('departmentaddteams')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-addteams">
							<i class="fa fa-user-plus s-margin-r"></i>Add Teams
						</button>
						<button @click="onShow('departmentremoveteams')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removeteams">
							<i class="fa fa-user-times s-margin-r"></i>Remove Teams
						</button>
	{{-- 					<button @click="onShow('departmentaddusers')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-addusers">
							<i class="fa fa-user-plus s-margin-r"></i>Add users
						</button>
						<button @click="onShow('departmentremoveusers')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removeusers">
							<i class="fa fa-user-times s-margin-r"></i>Remove users
						</button> --}}
						
					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showdepartment-details" data-toggle="tab">Details</a>
					</li>
					<li>
						<a @click="onShow('departmentpositions')"
						href="#showdepartment-positions" data-toggle="tab">Positions</a>
					</li>					
					<li>
						<a @click="onShow('departmentteams')"
						href="#showdepartment-teams" data-toggle="tab">Teams</a>
					</li>					
					<li>
						<a @click="onShow('departmentemployees')"
						href="#showdepartment-employees" data-toggle="tab">Employees</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showdepartment-details">
						
						@include('pages.departments.departmentdetails')							

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showdepartment-positions">
						
						<positions ref="departmentpositions"
						:nodepartment="true"
						:fetchurl="'{{ route('position.fetchdepartmentpositions', $department->id) }}'"
						></positions>

					</div>
					<!-- /.tab-pane -->						
					<div class="tab-pane" id="showdepartment-teams">				

						<teams ref="departmentteams"
						:nodepartment="true"
						:fetchurl="'{{ route('team.fetchdepartmentteams', $department->id) }}'"
						></teams>

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showdepartment-employees">				

						<employees ref="departmentemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchdepartmentemployees', $department->id) }}'"
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

	<!-- Add Position Modal -->
	<div id="view-addpositions" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Add Positions</h4>
	            </div>         

				<form id="departmentAddPositionForm" method="post" action="{{ route('department.addpositions', $department->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<positions ref="departmentaddpositions"
						:showcheckbox="true"
						:fetchurl="'{{ route('position.fetchnotdepartmentpositions', $department->id) }}'"
						></positions>

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

	<!-- Remove Position Modal -->
	<div id="view-removepositions" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Remove Positions</h4>
	            </div>         

				<form id="departmentRemovePositionForm" method="post" action="{{ route('department.removepositions', $department->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<positions ref="departmentremovepositions"
						:showcheckbox="true"
						:fetchurl="'{{ route('position.fetchdepartmentpositions', $department->id) }}'"
						></positions>

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

	<!-- Add Team Modal -->
	<div id="view-addteams" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Add Teams</h4>
	            </div>         

				<form id="departmentAddTeamForm" method="post" action="{{ route('department.addteams', $department->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<teams ref="departmentaddteams"
						:showcheckbox="true"
						:fetchurl="'{{ route('team.fetchnotdepartmentteams', $department->id) }}'"
						></teams>

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

	<!-- Remove Team Modal -->
	<div id="view-removeteams" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Remove Teams</h4>
	            </div>         

				<form id="departmentRemoveTeamForm" method="post" action="{{ route('department.removeteams', $department->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<teams ref="departmentremoveteams"
						:showcheckbox="true"
						:fetchurl="'{{ route('team.fetchdepartmentteams', $department->id) }}'"
						></teams>

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