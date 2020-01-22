@extends('master')

@section('pageTitle', 'Update for Request #' . $tempForm->form_id)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ 'Update for Request #' . $tempForm->form_id }}<small>Requested By: {{ $tempForm->requester->renderFullname() }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('temprequests') }}"><i class="fa fa-file"></i> Request Updates</a>
	        </li>
	        <li>
	        	<a href="#">{{ 'Update for Request #' . $tempForm->form_id }}</a>
	        </li>
	    </ol>
	</div>	

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					@if($self->isUpdateApprover($tempForm->id))

						<form id="formUpdateApproveFormBtn" method="post" action="{{ route('temprequest.approve', $tempForm->id) }}" data-redirect="true" 
						class="ajax inline-block">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-success s-margin-r">
								<i class="fa fa-plus-circle s-margin-r"></i>Approve
							</button>

						</form>

						<button class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#disapprove-request">
							<i class="fa fa-minus-circle s-margin-r"></i>Disapprove
						</button>

					@endif

				</div>
			</div>

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showform-details" data-toggle="tab">Details</a>
					</li>

					@if(!$tempForm->template->category->forEvent())
					<li>
						<a href="#showform-approvers" data-toggle="tab">Approvers</a>
					</li>
					@endif

				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showform-details">

						@include('pages.formupdates.formupdatedetails')

					</div>

					@if(!$tempForm->template->category->forEvent())
					<div class="tab-pane" id="showform-approvers">

						@include('pages.forms.formapprovers')

					</div>
					@endif

				</div>
			</div>

		</div>
	</div>	

@endsection


@section('modal')

@if(!$tempForm->template->category->forEvent() && $self->isUpdateApprover($tempForm->id))
<!-- Disapprove Modal -->
<div id="disapprove-request" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <form id="tempRequestDisapproveFormBtn" method="post" action="{{ route('temprequest.disapprove', $tempForm->id) }}" data-redirect="true" class="ajax modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Not Clearing request</h4>
            </div>         

			{{ csrf_field() }}

            <div class="modal-body">

				<div class="row">
					<div class="col-sm-12">
				
						<div class="form-group">
							<label>Reason</label>
							<textarea type="text" name="reason" placeholder="Reason" class="form-control"></textarea>
						</div>
		
	                </div>
                </div>

			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default">Back</button>			
				<button type="submit" class="btn btn-primary pull-left">Disapprove</button>
			</div>

        </form>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endif

@endsection
