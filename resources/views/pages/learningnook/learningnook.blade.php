@extends('master')

@section('pageTitle', 'Learning Nook')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Learning Nook<small>This is the Learning and Development page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('learningnook') }}"><i class="fa fa-book"></i> Learning Nook</a>
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
						
						<a href="#" class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#learningrequest">
							<img src="{{ asset('image/icons/edit-document-note-writing-review%402x.png') }}" class="tab--icon">
							Submit L&D?
						</a>

					</div>

				</div>
			</div>			

		</div>
	</div>

@endsection

@section('modal')

<div id="learningrequest" class="modal fade" tabindex="-1">
    <div class="modal-dialog width--80">

		<selectrequest
		:id="'learningrequest'"
		:header="'Add L&D'"
		:createurl="'{{ route('request.create') }}'"
		:createurl2="'{{ route('request.create.step2') }}'"
		:submiturl1="'{{ route('request.validate.step1') }}'"
		:submiturl2="'{{ route('request.store') }}'"
		:fetchurl="'{{ route('formtemplate.fetchforms') }}'"
		:templateid="'{{ isset($templateId) ? $templateId : '' }}'"
		></selectrequest>

    </div>
    <!-- /.modal-dialog -->
</div>

@endsection