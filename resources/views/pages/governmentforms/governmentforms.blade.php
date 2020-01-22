@extends('master')

@section('pageTitle', 'Government Forms')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Government Forms<small>This is the company's page</small></h1> --}}
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('governmentforms') }}"><i class="fa fa-file-text-o"></i> Government Forms</a>
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
						
						<a href="{{ route('governmentform.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Add Company Form
						</a>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#governmentforms" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li>
						<a @click="onShow('governmentformarchives')"
						href="#governmentforms-archived" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archived
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="governmentforms">

						<governmentforms
						:autofetch="true"
						:fetchurl="'{{ route('governmentform.fetch') }}'"
						></governmentforms>
						
					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="governmentforms-archived">

						<governmentforms ref="governmentformarchives"
						:fetchurl="'{{ route('governmentform.fetcharchive') }}'"
						></governmentforms>
						
					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection