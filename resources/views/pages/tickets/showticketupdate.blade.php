@extends('master')

@section('pageTitle', 'Ticket Update for Request #' . $ticketUpdate->id)

@section('breadcrumb')

	<div class="content-header">
		<h1>Ticket Update # {{ $ticketUpdate->id }}<small>by {{ $ticketUpdate->employee->renderFullname() }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('tempticketupdates') }}"><i class="fa fa-ticket"></i> Ticket Update Approvals</a>
	        </li>
	        <li>
	        	<a href="{{ route('tempticketupdate.show', $ticketUpdate->id) }}"># {{ $ticketUpdate->id }}</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					<form id="tempTicketUpdateForm" method="post" action="{{ route('tempticketupdate.approve', $ticketUpdate->id) }}" data-redirect="true" 
					class="ajax inline-block">

						{{ csrf_field() }}

						<button type="submit" class="btn btn-success s-margin-r">
							<i class="fa fa-plus-circle s-margin-r"></i>Approve
						</button>

					</form>

					<button class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#disapprove-request">
						<i class="fa fa-minus-circle s-margin-r"></i>Disapprove
					</button>					

				</div>
			</div>			

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs no-print">
					<li class="active">
						<a href="#showticket-details" data-toggle="tab">Details</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showticket-details">

						@include('pages.tickets.ticketupdatedetails')					

					</div>				
				</div>	
			</div>

		</div>
	</div>

@endsection

@section('modal')

<!-- Disapprove Modal -->
<div id="disapprove-request" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <form id="tempTicketUpdateDisapproveForm" method="post" action="{{ route('tempticketupdate.disapprove', $ticketUpdate->id) }}" data-redirect="true" class="ajax modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Disapprove</h4>
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

@endsection