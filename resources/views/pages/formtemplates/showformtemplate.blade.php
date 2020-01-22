@extends('master')

@section('pageTitle', $formTemplate->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $formTemplate->name }}</h1>
		<small>{{ $formTemplate->description }}</small>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('formtemplates') }}"><i class="fa fa-file"></i> Form Templates</a>
	        </li>
	        <li>
	        	<a href="{{ route('formtemplate.show', $formTemplate->id) }}">{{ $formTemplate->name }}</a>
	        </li>
	    </ol>
	</div>	

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">
					@if($formTemplate->trashed())
					<form id="formTemplateRestoreForm" method="post" action="{{ route('formtemplate.restore', $formTemplate->id) }}" data-redirect="true" class="ajax">

						{{ csrf_field() }}

						<button type="submit" class="btn btn-success">
							<i class="fa fa-plus-circle s-margin-r"></i>Restore
						</button>

					</form>
					@else
					<form id="formTemplateArchiveForm" method="post" action="{{ route('formtemplate.archive', $formTemplate->id) }}" data-redirect="true" class="ajax">

						{{ csrf_field() }}
						{{ method_field('DELETE') }}

						<button type="submit" class="btn btn-danger s-margin-r">
							<i class="fa fa-minus-circle s-margin-r"></i>Archive
						</button>

					</form>
					@endif
				</div>
			</div>			

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showform-details" data-toggle="tab">Configuration</a>
					</li>
					<li>
						<a @click="onShow('formtemplatefields')"
						href="#showform-fields" data-toggle="tab">Fields</a>
					</li>
					<li>
						<a @click="onShow('formtemplateapprovers')"
						href="#showform-approvers" data-toggle="tab">Approvers</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showform-details">

						@include('pages.formtemplates.formtemplatedetails')

					</div>
					<div class="tab-pane" id="showform-fields">

						<formtemplatefields ref="formtemplatefields"
						:fields="{{ $formTemplate->fields }}"						
						:types="{{ json_encode($types) }}"
						:addurl="'{{ route('formtemplate.addfield', $formTemplate->id) }}'"
						:updateurl="'{{ route('formtemplate.updatesorting', $formTemplate->id) }}'"
						:fetchurl="'{{ route('formtemplate.fetchfields', $formTemplate->id) }}'"
						></formtemplatefields>

					</div>							
					<div class="tab-pane" id="showform-approvers">

						@include('pages.formtemplates.formtemplateapprovers')

					</div>				
				</div>
			</div>

		</div>
	</div>		

@endsection
