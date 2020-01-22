@extends('master')

@section('pageTitle', 'Companies')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Companies<small>This is the company's page</small></h1> --}}
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('companies') }}"><i class="fa fa-building-o"></i> Companies</a>
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
						
						<a href="{{ route('company.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Add Company
						</a>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#companies" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li>
						<a @click="onShow('companyarchives')"
						href="#companies-archived" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archived
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="companies">

						<companies
						:autofetch="true"
						:fetchurl="'{{ route('company.fetch') }}'"
						></companies>
						
					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="companies-archived">

						<companies ref="companyarchives"
						:fetchurl="'{{ route('company.fetcharchive') }}'"
						></companies>
						
					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection