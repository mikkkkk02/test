@extends('master')

@section('pageTitle', $company->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $company->name }}<small>{{ $company->description }}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('companies') }}"><i class="fa fa-building-o"></i> Companies</a>
	        </li>
	        <li>
	        	<a href="{{ route('company.show', $company->id) }}">{{ $company->abbreviation }}</a>
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

						@if($company->trashed())
						<form id="companyRestoreForm" method="post" action="{{ route('company.restore', $company->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-success s-margin-r">
								<i class="fa fa-plus-circle s-margin-r"></i>Restore
							</button>

						</form>
						@else
						<form id="companyArchiveForm" method="post" action="{{ route('company.archive', $company->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}
							{{ method_field('DELETE') }}

							<button type="submit" class="btn btn-danger s-margin-r">
								<i class="fa fa-minus-circle s-margin-r"></i>Archive
							</button>

						</form>
						@endif

						<button @click="onShow('companyadddivisions')"
						class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-addgroup">
							<i class="fa fa-plus s-margin-r"></i>Add Groups
						</button>
						<button @click="onShow('companyremovedivisions')"
						class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#view-removegroup">
							<i class="fa fa-times s-margin-r"></i>Remove Groups
						</button>

					</div>
					
				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showcompany-details" data-toggle="tab">Details</a>
					</li>
					<li>
						<a @click="onShow('companydivisions')"
						href="#showcompany-groups" data-toggle="tab">Groups</a>
					</li>
					<li>
						<a @click="onShow('companydepartments')"
						href="#showcompany-departments" data-toggle="tab">Departments</a>
					</li>
					<li>
						<a @click="onShow('companyteams')"
						href="#showcompany-teams" data-toggle="tab">Teams</a>
					</li>
					<li>
						<a @click="onShow('companyemployees')" 
						href="#showcompany-employees" data-toggle="tab">Employees</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showcompany-details">
						
						@include('pages.companies.companydetails')

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="showcompany-groups">
						
						<divisions ref="companydivisions"
						:nocompany="true"
						:fetchurl="'{{ route('division.fetchcompanydivisions', $company->id) }}'"
						></divisions>

					</div>
					<!-- /.tab-pane -->	
					<div class="tab-pane" id="showcompany-departments">
						
						<departments ref="companydepartments"
						:categories="{{ json_encode($company->renderDepartmentTableFilter()) }}"
						:fetchurl="'{{ route('department.fetchcompanydepartments', $company->id) }}'"
						></departments>

					</div>
					<!-- /.tab-pane -->	
					<div class="tab-pane" id="showcompany-teams">
						
						<teams ref="companyteams"
						:categories="{{ json_encode($company->renderTeamTableFilter()) }}"
						:fetchurl="'{{ route('team.fetchcompanyteams', $company->id) }}'"
						></teams>

					</div>
					<!-- /.tab-pane -->														
					<div class="tab-pane" id="showcompany-employees">
						
						<employees ref="companyemployees"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:nocompany="true"
						:fetchurl="'{{ route('employee.fetchcompanyemployees', $company->id) }}'"
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

	<!-- Add Divisions Modal -->
	<div id="view-addgroup" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Add Groups</h4>
	            </div>         

				<form id="companyAddGroupForm" method="post" action="{{ route('company.adddivisions', $company->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<divisions ref="companyadddivisions"
						:showcheckbox="true"
						:fetchurl="'{{ route('division.fetchnotcompanydivisions', $company->id) }}'"
						></divisions>

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

	<!-- Remove Divisions Modal -->
	<div id="view-removegroup" class="modal fade" tabindex="-1">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Remove Groups</h4>
	            </div>         

				<form id="companyRemoveGroupForm" method="post" action="{{ route('company.removedivisions', $company->id) }}" data-redirect="true" @keydown.enter.prevent=""
				class="ajax">

					{{ csrf_field() }}

		            <div class="box box-widget modal-body">
						
						<divisions ref="companyremovedivisions"
						:showcheckbox="true"
						:fetchurl="'{{ route('division.fetchcompanydivisions', $company->id) }}'"
						></divisions>

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