@extends('master')

@section('pageTitle', 'Employees')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Employees<small>This is the employees page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('employees') }}"><i class="fa fa-users"></i> Employees</a>
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
						
			            @if($checker->hasModuleRoles(['Adding/Editing of Employee Profile']))
						<a href="{{ route('employee.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-user-plus s-margin-r"></i>Add Employee
						</a>
						@endif

			            @if($checker->hasModuleRoles(['Batch updating of Employee Database']))		
						<button class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#import-employee">
							<i class="fa fa-upload s-margin-r"></i>Import
						</button>
						@endif

					</div>

					
				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#employees" data-toggle="tab">
							{{-- <i class="fa fa-archive s-margin-r"></i> --}}
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>	
					<li class="">
						<a @click="onShow('employeearchive')"
						href="#employees-archive" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archive
						</a>
					</li>														
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="employees">

						<employees
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('employee.fetch') }}'"
						></employees>

					</div>
					<div class="tab-pane" id="employees-archive">

						<employees ref="employeearchive"
						:categories="{{ json_encode(App\User::renderTableFilter()) }}"
						:fetchurl="'{{ route('employee.fetcharchive') }}'"
						></employees>

					</div>					
				</div>

			</div>			

		</div>
	</div>

@endsection

@if($checker->hasModuleRoles(['Batch updating of Employee Database']))
@section('modal')

	<!-- Import User Modal -->
	<div id="import-employee" class="modal fade" tabindex="-1">
	    
		<import
		:header="'Import Employee'"
		:text="'Upload the file containing the Employee data'"
		:importurl="'{{ route('user.import') }}'"
		></import>

	</div>

@endsection
@endif