@extends('master')

@section('pageTitle', $mrReservation->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $mrReservation->name }}<small>{{ $mrReservation->description }}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('mrreservations') }}"><i class="fa fa-users"></i> Meeting Room Reservations</a>
	        </li>
	        <li>
	        	<a href="{{ route('mrreservation.show', $mrReservation->id) }}">{{ $mrReservation->name }}</a>
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
						
						@if($checker->hasModuleRoles(['Adding/Editing of Meeting Room Reservations']) && $mrReservation->trashed())
						<form id="roomRestoreForm" method="post" action="{{ route('mrreservation.restore', $mrReservation->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-success s-margin-r">
								<i class="fa fa-plus-circle s-margin-r"></i>Restore
							</button>

						</form>
						@elseif($checker->hasModuleRoles(['Adding/Editing of Meeting Room Reservations']) && !$mrReservation->trashed())
						<form id="roomArchiveForm" method="post" action="{{ route('mrreservation.archive', $mrReservation->id) }}" data-redirect="true" class="ajax inline">

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
						<a href="#showmrreservation-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showmrreservation-details">
						
						@include('pages.mrreservations.mrreservationdetails')					

					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection
