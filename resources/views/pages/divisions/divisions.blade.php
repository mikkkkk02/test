@extends('master')

@section('pageTitle', 'Groups')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Groups<small>This is the groups page</small></h1> --}}
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('divisions') }}"><i class="fa fa-industry"></i> Groups</a>
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
						
						<a href="{{ route('division.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Add Group
						</a>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#divisions" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li>
						<a @click="onShow('divisionarchives')"
						href="#divisions-archived" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archived
						</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="divisions">

						<divisions ref="divisions"
						:categories="{{ json_encode(App\Company::renderFilterArray()) }}"
						:autofetch="true"
						:fetchurl="'{{ route('division.fetch') }}'"
						></divisions>
						
					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="divisions-archived">

						<divisions ref="divisionarchives"
						:categories="{{ json_encode(App\Company::renderFilterArray()) }}"
						:fetchurl="'{{ route('division.fetcharchive') }}'"
						></divisions>
						
					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection