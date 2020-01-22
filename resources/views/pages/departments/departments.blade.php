@extends('master')

@section('pageTitle', 'Departments')

@section('breadcrumb')

	<div class="content-header">
		<h1>Departments<small>{{-- This is the Departments page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('departments') }}"><i class="fa fa-home"></i> Departments</a>
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

						<a href="{{ route('department.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Add Department
						</a>
						<a href="{{ route('position.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Create Position
						</a>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#departments" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="departments">
	
						<departments ref="departments"
						:categories="{{ json_encode(App\Department::renderFilterArray()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('department.fetch') }}'"
						></departments>
						
					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection