@extends('master')

@section('pageTitle', $governmentForm->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $governmentForm->name }}<small></small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('governmentforms') }}"><i class="fa fa-file-text-o"></i> Government Forms</a>
	        </li>
	        <li>
	        	<a href="{{ route('governmentform.show', $governmentForm->id) }}">{{ $governmentForm->name }}</a>
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
						
						@if($governmentForm->attachment)
						<div class="inline">

							<a href="{{ $governmentForm->renderDownloadURL() }}" target="_blank" class="btn btn-success s-margin-r">
								<i class="fa fa-download s-margin-r"></i>Download
							</a>

						</div>
						@endif


	                    @if($checker->hasModuleRoles(['Adding/Editing of Government Forms']))
						<div class="inline">

							<button class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#upload-governmentform">
								<i class="fa fa-upload s-margin-r"></i>Upload
							</button>

						</div>						
							@if($governmentForm->trashed())
							<form id="governmentformRestoreForm" method="post" action="{{ route('governmentform.restore', $governmentForm->id) }}" data-redirect="true" class="ajax inline">

								{{ csrf_field() }}

								<button type="submit" class="btn btn-success s-margin-r">
									<i class="fa fa-plus-circle s-margin-r"></i>Restore
								</button>

							</form>
							@else
							<form id="governmentformArchiveForm" method="post" action="{{ route('governmentform.archive', $governmentForm->id) }}" data-redirect="true" class="ajax inline">

								{{ csrf_field() }}
								{{ method_field('DELETE') }}

								<button type="submit" class="btn btn-danger s-margin-r">
									<i class="fa fa-minus-circle s-margin-r"></i>Archive
								</button>

							</form>
							@endif
						@endif

					</div>
					

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showgovernmentform-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showgovernmentform-details">
						
						@include('pages.governmentforms.governmentformdetails')							

					</div>				
				</div>
				
			</div>			

		</div>
	</div>	

@endsection

@if($checker->hasModuleRoles(['Adding/Editing of Government Forms']))
@section('modal')

	<div id="upload-governmentform" class="modal fade" tabindex="-1">
	    
		<uploadfile
		:header="'Upload Government Form'"
		:text="'Upload the government form file (Existing file will be overwritten)'"
		:importurl="'{{ route('governmentform.addattachment', $governmentForm->id) }}'"
		></uploadfile>

	</div>

@endsection
@endif
