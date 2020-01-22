@extends('master')

@section('pageTitle', 'Benefits')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Benefits<small>This is the approvals page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('benefits') }}"><i class="fa fa-book"></i> Benefits</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="content-body">		

		<div class="row">
			<div class="col-sm-12">

				<div class="row">
					<div class="col-sm-12 l-margin-b">

						<div class="pull-right">

							<a href="#" class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-selectformtemplate">
								<i class="fa fa-file-text-o s-margin-r"></i> Add Benefit
							</a>

						</div>

					</div>
				</div>

			</div>
		</div>

	</div>

@endsection

@section('styles')
	
	<!-- jQuery Chosen -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.min.css">

@endsection

@section('js')
	<!-- jQuery Chosen -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.jquery.min.js"></script>
@endsection

@section('modal')

	<div id="view-selectformtemplate" class="modal fade" tabindex="-1">
	    <div class="modal-dialog width--80">

			<selectrequest
			:id="'view-selectformtemplate'"
			:header="'Avail a Benefit'"
			:content="'Select which benefit you want to avail:'"
			:createurl="'{{ route('request.create') }}'"
			:createurl2="'{{ route('request.create.step2') }}'"
			:submiturl1="'{{ route('request.validate.step1') }}'"
			:submiturl2="'{{ route('request.store') }}'"
			:fetchurl="'{{ route('benefit.fetchforms') }}'"
			:templateid="'{{ isset($templateId) ? $templateId : '' }}'"
			></selectrequest>

	    </div>
	    <!-- /.modal-dialog -->
	</div>

@endsection