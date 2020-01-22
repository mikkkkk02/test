@extends('master')

@section('pageTitle', $group->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $group->name }}<small>{{ $group->desciption }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('groups') }}"><i class="fa fa-user-plus"></i> Groups</a>
	        </li>
	        <li>
	        	<a href="{{ route('group.show', $group->id) }}">{{ $group->name }}</a>
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
						
						@if($group->trashed())
						<form id="groupRestoreForm" method="post" action="{{ route('group.restore', $group->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-success s-margin-r">
								<i class="fa fa-plus-circle s-margin-r"></i>Restore
							</button>

						</form>
						@else
						<form id="groupArchiveForm" method="post" action="{{ route('group.archive', $group->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}
							{{ method_field('DELETE') }}

							<button type="submit" class="btn btn-danger s-margin-r">
								<i class="fa fa-minus-circle s-margin-r"></i>Archive
							</button>

						</form>
						@endif

						<button @click="onShow('groupaddemployees')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-adduser">
							<i class="fa fa-user-plus s-margin-r"></i>Add Users
						</button>
						<button @click="onShow('groupremoveemployees')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removeuser">
							<i class="fa fa-user-times s-margin-r"></i>Remove Users
						</button>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#group-details" data-toggle="tab">Details</a>
					</li>
					<li>
						<a href="#group-roles" data-toggle="tab">Roles</a>
					</li>
					<li>
						<a @click="onShow('groupemployees')"
						href="#group-users" data-toggle="tab">Users</a>
					</li>													
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="group-details">
						
						@include('pages.groups.groupdetails')		

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="group-roles">
						
						@include('pages.groups.roles')

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="group-users">

						<employees ref="groupemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:fetchurl="'{{ route('employee.fetchgroupusers', $group->id) }}'"
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
	<div id="view-adduser" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Add Users</h4>
	            </div>         

				<form id="groupAddUserForm" method="post" action="{{ route('group.addusers', $group->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<employees ref="groupaddemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:showcheckbox="true"
						:noemail="true"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchnotgroupusers', $group->id) }}'"
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
	<div id="view-removeuser" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Remove Users</h4>
	            </div>         

				<form id="groupRemoveUserForm" method="post" action="{{ route('group.removeusers', $group->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<employees ref="groupremoveemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:showcheckbox="true"
						:noemail="true"
						:nocompany="true"
						:nodivision="true"
						:nodepartment="true"
						:fetchurl="'{{ route('employee.fetchgroupusers', $group->id) }}'"
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