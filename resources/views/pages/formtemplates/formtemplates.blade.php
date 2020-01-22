@extends('master')

@section('pageTitle', 'Form Templates')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Form Templates<small>This is the form templates page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('formtemplates') }}"><i class="fa fa-file"></i> Form Templates</a>
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
						
						<a href="{{ route('formtemplate.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-file-o s-margin-r"></i>Add Template
						</a>
					
					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#forms-templates" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li class="">
						<a @click="onShow('formarchives')"
						href="#forms-archived" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archived
						</a>
					</li>												
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="forms-templates">

						<formtemplates ref="formtemplates"
						:categories="{{ json_encode(App\FormTemplateCategory::renderFilterArray()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('formtemplate.fetch') }}'"
						></formtemplates>

					</div>
					<!-- /.tab-pane -->	
					<div class="tab-pane" id="forms-archived">

						<formtemplates ref="formarchives"
						:showarchive="true"
						:noedited="true"
						:noupdated="true"		
						:fetchurl="'{{ route('formtemplate.fetcharchive') }}'"	
						></formtemplates>

					</div>
					<!-- /.tab-pane -->														
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection