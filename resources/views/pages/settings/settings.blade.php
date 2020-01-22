@extends('master')

@section('pageTitle', 'Settings')

@section('breadcrumb')

	<div class="content-header">
		<h1>Settings<small>{{-- This is the Settings page --}}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('settings') }}"><i class="fa fa-cog"></i> Settings</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row l-margin-t">
		<div class="col-sm-12">

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#settings-forms" data-toggle="tab">
							<i class="fa fa-user-o s-margin-r"></i>
							Delegate
						</a>
					</li>

		            @if($checker->hasModuleRoles(['Editing of Ticket Technician']))
					<li>
						<a href="#settings-tickets" data-toggle="tab">
							<i class="fa fa-ticket s-margin-r"></i>
							Tickets
						</a>
					</li>					
					@endif

		            @if($checker->hasModuleRoles(['Editing of CEO, HR & OD']))
					<li>
						<a href="#settings-others" data-toggle="tab">
							<i class="fa fa-cubes s-margin-r"></i>
							Others
						</a>
					</li>					
					@endif					

				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="settings-forms">

						@include('pages.settings.settingsdelegate')

					</div>
					<!-- /.tab-pane -->

		            @if($checker->hasModuleRoles(['Editing of Ticket Technician']))
					<div class="tab-pane" id="settings-tickets">

						@include('pages.settings.settingsticket')

					</div>
					<!-- /.tab-pane -->
					@endif	

		            @if($checker->hasModuleRoles(['Editing of CEO, HR & OD']))
					<div class="tab-pane" id="settings-others">

						@include('pages.settings.settingsothers')

					</div>
					<!-- /.tab-pane -->
					@endif				
					
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection


@section('styles')

    <!-- AdminLTE: Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/select2.min.css') }}">

@endsection
@section('js')

    <!-- AdminLTE: Select2 -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/select2/select2.min.js') }}"></script>  

@endsection