@extends('master')

@section('pageTitle', 'For Approvals')

@section('breadcrumb')

	<div class="content-header">
		<h1>Approvals<small>{{-- This is the approvals page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('approvals') }}"><i class="fa fa-book"></i> Approvals</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row l-margin-t">
		<div class="col-sm-12">

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#approvals" data-toggle="tab">Approvals</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="approvals">

						<requests
						:categories="{{ json_encode(App\FormTemplate::renderFilterArray()) }}"
						:formstatus="{{ json_encode(App\Form::getStatus()) }}"
						:ticketstatus="{{ json_encode(App\Ticket::getStatus()) }}"						
						:autofetch="true"
						:fetchurl="'{{ route('request.fetchuserapprovalrequest', $self->id) }}'"
						:daterange="'requestsforapproval'"						
						></requests>

					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>

@endsection

@section('styles')

	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">

@endsection

@section('js')

	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

@endsection