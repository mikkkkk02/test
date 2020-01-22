@extends('master')

@section('pageTitle', 'Create Request')

@section('breadcrumb')

	<div class="content-header">
		<h1>Attend {{ $event->title }} event</h1>
		<small>{{ $event->description }}</small>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('event.show', $event->id) }}"><i class="fa fa-file"></i> {{ $event->title }}</a>
	        </li>
	        <li>
	        	<a href="{{ route('event.createrequest', ['event' => $event->id, 'form' => $formTemplate->id]) }}"> Attend</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')
	
	<div class="row m-margin-t">
		<div class="col-sm-12">

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showform-details" data-toggle="tab">Request</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showform-details">

						@include('pages.forms.formdetails')

					</div>
					<!-- /.tab-pane -->
				</div>
			</div>

		</div>
	</div>	

@endsection

