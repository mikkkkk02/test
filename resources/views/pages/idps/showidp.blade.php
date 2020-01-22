@extends('master')

@section('pageTitle', 'IDP #' . $idp->id)

@section('breadcrumb')

	<div class="content-header">
		<h1>IDP # {{ $idp->id }}<small>{{ $idp->employee->renderFullname() }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('idps') }}"><i class="fa fa-book"></i> IDP</a>
	        </li>
	        <li>
	        	<a href="{{ route('idp.show', $idp->id) }}">IDP # {{ $idp->id }}</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					{{-- <form id="deleteIDPForm" method="post" action="{{ route('idp.delete', $idp->id) }}" data-redirect="true" class="ajax inline">

						{{ csrf_field() }}
						{{ method_field('DELETE') }}
	
						<button type="submit" class="btn btn-danger s-margin-r">
							<i class="fa fa-times s-margin-r"></i>Delete IDP
						</button>

					</form> --}}

					@if($self->isIdpApprover($idp->id))
					<form id="approveTempIDPForm" method="post" action="{{ route('idp.approve', $idp->id) }}" data-redirect="true" class="ajax inline">

						{{ csrf_field() }}
	
						<button type="submit" class="btn btn-success s-margin-r">
							<i class="fa fa-check s-margin-r"></i>Approve
						</button>

					</form>

					<form id="disapproveTempIDPForm" method="post" action="{{ route('idp.disapprove', $idp->id) }}" data-redirect="true" class="ajax inline">

						{{ csrf_field() }}
	
						<button type="submit" class="btn btn-danger s-margin-r">
							<i class="fa fa-times s-margin-r"></i>Disapprove
						</button>

					</form>

					{{-- <form id="transferTempIDPForm" method="post" action="{{ route('idptmp.transfer', $idp->id) }}" class="ajax inline">

						{{ csrf_field() }}
	
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-exchange s-margin-r"></i>Transfer Approval to Supervisor
						</button>

					</form> --}}

					@else
					{{-- <span class="label {{ $idp->renderStateClass() }}">{{ $idp->renderState() }} by {{ $idp->approver->renderFullname() }}</span> --}}
					@endif				

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showidp-details" data-toggle="tab">Details</a>
					</li>
					@if(!$idp->isApproved())
					<li>
						<a href="#showidp-approvers" data-toggle="tab">Approvers</a>
					</li>
					@endif
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showidp-details">

						@include('pages.idps.idpdetails')					

					</div>

					@if(!$idp->isApproved())
					<div class="tab-pane" id="showidp-approvers">

						@include('pages.idps.idpapprovers')					

					</div>
					@endif
					
				</div>

			</div>

		</div>
	</div>			

@endsection
