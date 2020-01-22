@extends('master')

@section('pageTitle', 'Responsibilities')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Responsibilities<small>This is the responsibilities page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('groups') }}"><i class="fa fa-user-plus"></i> Responsibilities</a>
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

						<a href="{{ route('group.create') }}" class="btn btn-primary">
							<i class="fa fa-plus s-margin-r"></i>Add Responsibility
						</a>
						
					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#groups" data-toggle="tab">Responsibilities</a>
					</li>
					<li>
						<a @click="onShow('grouparchives')"
						href="#groups-archived" data-toggle="tab">Archived</a>
					</li>								
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="groups">

						<groups
						:autofetch="true"
						:fetchurl="'{{ route('group.fetch') }}'"
						></groups>

					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="groups-archived">

						<groups ref="grouparchives"
						:fetchurl="'{{ route('group.fetcharchive') }}'"
						></groups>

					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>

@endsection
