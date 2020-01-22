@extends('master')

@section('pageTitle', 'Teams')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Teams<small>This is the Teams page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('teams') }}"><i class="fa fa-users"></i> Teams</a>
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
						
						<a href="{{ route('team.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Add Team
						</a>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#teams" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="teams">

						<teams ref="teams"
						:categories="{{ json_encode(App\Team::renderFilterArray()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('team.fetch') }}'"
						></teams>
						
					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection