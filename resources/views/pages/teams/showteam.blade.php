@extends('master')

@section('pageTitle', $team->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $team->name }}<small>{{ $team->description }}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('teams') }}"><i class="fa fa-industry"></i> Teams</a>
	        </li>
	        <li>
	        	<a href="{{ route('team.show', $team->id) }}">{{ $team->name }}</a>
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
						
						<button @click="onShow('teamaddusers')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-addusers">
							<i class="fa fa-user-plus s-margin-r"></i>Add Employees
						</button>
						<button @click="onShow('teamremoveusers')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removeusers">
							<i class="fa fa-user-times s-margin-r"></i>Remove Employees
						</button>
						
					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showteam-details" data-toggle="tab">Details</a>
					</li>				
					<li>
						<a @click="onShow('teamemployees')"
						href="#showteam-employees" data-toggle="tab">Employees</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showteam-details">
						
						@include('pages.teams.teamdetails')					

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showteam-employees">				

						<employees ref="teamemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:nocompany="true"
						:fetchurl="'{{ route('employee.fetchteamemployees', $team->id) }}'"
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

				<form id="teamAddEmployeeForm" method="post" action="{{ route('team.addemployees', $team->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<employees ref="teamaddusers"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:showcheckbox="true"
						:noemail="true"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchnotteamemployees', $team->id) }}'"
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

				<form id="teamRemoveEmployeeForm" method="post" action="{{ route('team.removeemployees', $team->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<employees ref="teamremoveusers"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:showcheckbox="true"
						:noemail="true"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchteamemployees', $team->id) }}'"
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