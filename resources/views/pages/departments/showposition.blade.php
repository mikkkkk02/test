@extends('master')

@section('pageTitle', $position->title)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $position->title }}<small>{{ $position->description }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('departments') }}"><i class="fa fa-building"></i> Departments</a>
	        </li>
	        @if($position->department)
	        <li>
	        	<a href="{{ route('department.show', $position->department_id) }}">{{ $position->department->name }}</a>
	        </li>
	        @endif
	        <li>
	        	<a href="{{ route('position.show', $position->id) }}">{{ $position->title }}</a>
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
						
						<button @click="onShow('positionaddusers')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-addusers">
							<i class="fa fa-user-plus s-margin-r"></i>Add Employees
						</button>
						<button @click="onShow('positionremoveusers')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removeusers">
							<i class="fa fa-user-times s-margin-r"></i>Remove Employees
						</button>
						
					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showposition-details" data-toggle="tab">Details</a>
					</li>					
					<li>
						<a @click="onShow('positionemployees')"
						href="#showposition-employees" data-toggle="tab">Employees</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showposition-details">
						
						@include('pages.departments.positiondetails')						

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showposition-employees">	

						<employees ref="positionemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchpositionemployees', $position->id) }}'"
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

	<!-- Add User Modal -->
	<div id="view-addusers" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Add employees</h4>
	            </div>         

				<form id="companyAddGroupForm" method="post" action="{{ route('position.addemployees', $position->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<employees ref="positionaddusers"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:showcheckbox="true"
						:noemail="true"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchnotpositionemployees', $position->id) }}'"
						></employees>

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

	<!-- Remove User Modal -->
	<div id="view-removeusers" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Remove employees</h4>
	            </div>         

				<form id="companyRemoveGroupForm" method="post" action="{{ route('position.removeemployees', $position->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<employees ref="positionremoveusers"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:showcheckbox="true"
						:noemail="true"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchpositionemployees', $position->id) }}'"
						></employees>

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