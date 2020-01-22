@extends('master')

@section('pageTitle', $room->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $room->name }}<small>{{ $room->description }}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('rooms') }}"><i class="fa fa-cube"></i> Rooms</a>
	        </li>
	        <li>
	        	<a href="{{ route('room.show', $room->id) }}">{{ $room->name }}</a>
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

						@if($checker->hasModuleRoles(['Adding/Editing of Locations']) && $room->trashed())
						<form id="roomRestoreForm" method="post" action="{{ route('room.restore', $room->id) }}" data-redirect="true" class="ajax inline">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-success s-margin-r">
								<i class="fa fa-plus-circle s-margin-r"></i>Restore
							</button>

						</form>
						@else
						<form id="roomArchiveForm" method="post" action="{{ route('room.archive', $room->id) }}" data-redirect="true" class="ajax inline">

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
						<a href="#showroom-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showroom-details">
						
						@include('pages.rooms.roomdetails')					

					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection


@section('js')

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js"></script>

@endsection