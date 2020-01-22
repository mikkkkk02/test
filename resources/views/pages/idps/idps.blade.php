@extends('master')

@section('pageTitle', 'IDP')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>IDP<small>This is the IDP page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('idps') }}"><i class="fa fa-book"></i> IDP</a>
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

						<a href="{{ route('idp.create') }}" class="btn btn-primary s-margin-r">
							{{-- <i class="fa fa-plus s-margin-r"></i> --}}
							<img src="{{ asset('image/icons/edit-document-note-writing-review%402x.png') }}" class="tab--icon">
							Add IDP
						</a>

					</div>

				</div>
			</div>


			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a @click="onShow('idpmyown')"
						href="#idp-myown" data-toggle="tab">
							<img src="/image/tabs/individual.png" class="tab--icon">
							Individual
						</a>
					</li>					
				</ul>

				<div class="tab-content">				
					<div class="tab-pane active" id="idp-myown">

						<idps ref="idpmyown"
						:categories="{{ json_encode(App\Idp::renderStatusFilter()) }}"
						:status="{{ json_encode(App\Idp::getStatus()) }}"
						:approvalstatus="{{ json_encode(App\Idp::getApprovalStatus()) }}"
						:noemployee="true"
						:noapprover="true"
						:autofetch="true"
						:fetchurl="'{{ route('idp.fetchuseridps', $self->id) }}'"
						:daterange="'idpsmyown'"
						></idps>

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
