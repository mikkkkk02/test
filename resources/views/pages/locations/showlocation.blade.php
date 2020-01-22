@extends('master')

@section('pageTitle', $location->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $location->name }}<small>{{ $location->description }}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('locations') }}"><i class="fa fa-map-marker"></i> Locations</a>
	        </li>
	        <li>
	        	<a href="{{ route('location.show', $location->id) }}">{{ $location->name }}</a>
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

						@if($checker->hasModuleRoles(['Adding/Editing of Locations']) && $location->trashed())
						<form id="locationRestoreForm" method="post" action="{{ route('location.restore', $location->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-success s-margin-r">
								<i class="fa fa-plus-circle s-margin-r"></i>Restore
							</button>

						</form>
						@else
						<form id="locationArchiveForm" method="post" action="{{ route('location.archive', $location->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}
							{{ method_field('DELETE') }}

							<button type="submit" class="btn btn-danger s-margin-r">
								<i class="fa fa-minus-circle s-margin-r"></i>Archive
							</button>

						</form>
						@endif

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showlocation-details" data-toggle="tab">Details</a>
					</li>
					@isset ($location)
						<li class="">
							<a href="#showlocation-rooms" data-toggle="tab" @click="onShow('rooms')">Rooms</a>
						</li>
					@endisset
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showlocation-details">
						
						@include('pages.locations.locationdetails')					

					</div>
					<!-- /.tab-pane -->
					@isset ($location)
						<div class="tab-pane" id="showlocation-rooms">

							<rooms ref="rooms"
							:fetchurl="'{{ route('location.fetchrooms', $location->id) }}'">
							</rooms>						

						</div>
					@endisset

					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection

@section('styles')
	
	<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/select2.min.css') }}">

@endsection

@section('js')

	<!-- AdminLTE: Select2 -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/select2/select2.min.js') }}"></script>  	

@endsection