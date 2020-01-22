@extends('master')

@section('pageTitle', 'Meeting Room Reservations')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Meeting Room Reservations<small>This is the Locations page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('mrreservations') }}"><i class="fa fa-users"></i> Meeting Room Reservations</a>
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
							
						<form action="{{ route('mrreservation.create') }}" method="GET" class="right-align">
							<div class="form-group">
								<label>Meeting Room Form</label>
								<select name="formtemplate" class="form-control">
									@foreach ($formTemplates as $formTemplate)
										<option value="{{ $formTemplate->id }}">{{ $formTemplate->name }}</option>
									@endforeach
								</select>
							</div>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-plus s-margin-r"></i>Add Reservation
							</button>
						</form>

					</div>

				</div>
			</div>

			

			<div class="row">
				<div class="col-sm-12">
					<div class="box no-border">
						<div class="box-body no-padding">

							<calendar
							:name="'reservation-calendar'"
							:autoload="true"
							:events="{{ json_encode($mrTimeCalendarObjects) }}"
							></calendar>
							
						</div>
						<!-- /.box-body -->
					</div>
				<!-- /. box -->
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#mrreservation" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li>
						<a @click="onShow('mrreservationarchive')"
						href="#mrreservation-archive" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archive
						</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="mrreservation">
						<mrreservations ref="mrreservation"
						:fetchurl="'{{ route('mrreservation.fetch') }}'"
						:autofetch="true">
						</mrreservations>
					</div>
					<div class="tab-pane" id="mrreservation-archive">
						<mrreservations ref="mrreservationarchive"
						:fetchurl="'{{ route('mrreservation.fetcharchive') }}'">
						</mrreservations>
					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection

@section('styles')

    <!-- FullCalendar-->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.print.css') }}" media="print">	

@endsection

@section('js')

	<!-- FullCalendar -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.js') }}"></script>

@endsection