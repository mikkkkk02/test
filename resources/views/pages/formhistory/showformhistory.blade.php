@extends('master')

@section('pageTitle', 'History of Request #' . $formHistory->form_id)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ 'History of Request #' . $formHistory->form_id }}<small>{{ $formHistory->created_at->format('F m, Y i:h A') }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('requests') }}"><i class="fa fa-file"></i> Request Updates</a>
	        </li>
	        <li>
	        	<a href="#">{{ 'History of Request #' . $formHistory->form_id }}</a>
	        </li>
	    </ol>
	</div>	

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

				</div>
			</div>

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showform-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showform-details">

						@include('pages.forms.formempdetails', ['form' => $formHistory->form])
						@include('pages.forms.formfields', ['form' => $formHistory])

					</div>
				</div>
			</div>

		</div>
	</div>	

@endsection