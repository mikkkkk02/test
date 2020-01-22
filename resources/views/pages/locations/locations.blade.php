@extends('master')

@section('pageTitle', 'Locations')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Locations<small>This is the Locations page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('locations') }}"><i class="fa fa-users"></i> Locations</a>
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
						
						<a href="{{ route('location.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Add Location
						</a>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#locations" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li>
						<a @click="onShow('locationsarchive')"
						href="#locations-archive" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archive
						</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="locations">

						<locations ref="locations"
						:autofetch="true"
						:fetchurl="'{{ route('location.fetch') }}'"
						></locations>
						
					</div>
					<div class="tab-pane" id="locations-archive">

						<locations ref="locationsarchive"
						:fetchurl="'{{ route('location.fetcharchive') }}'"
						></locations>
						
					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection