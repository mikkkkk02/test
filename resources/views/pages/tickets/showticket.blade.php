@extends('master')

@section('pageTitle', 'Ticket #' . $ticket->id)

@section('breadcrumb')

	<div class="content-header">
		<h1>Ticket # {{ $ticket->id }}<small>{{ $ticket->owner->renderFullname() }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('tickets') }}"><i class="fa fa-ticket"></i> Tickets</a>
	        </li>
	        <li>
	        	<a href="{{ route('ticket.show', $ticket->id) }}"># {{ $ticket->id }}</a>
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

						@if($checker->hasModuleRoles(['Assigning of Tickets']))
						<button class="btn btn-primary s-margin-r" data-toggle="modal" data-target="#view-assigntechnician">
							<i class="fa fa-user-plus s-margin-r"></i>Assign Technician
						</button>
						@endif

						@if($ticket->isWithdrawable())
						<form id="requestWithdrawFormBtn" method="post" action="{{ route('request.withdraw', $ticket->form->id) }}" data-redirect="true" 
						class="ajax inline-block">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-danger s-margin-r">
								<i class="fa fa-close s-margin-r"></i>Withdraw
							</button>

						</form>
						@endif

						@if($ticket->canResubmit())
						<a href="{{ route('request.resubmit', $ticket->form->id) }}" class="btn btn-info s-margin-r">
							<i class="fa fa-refresh s-margin-r"></i>Re-submit
						</a>
						@endif

					</div>
					
				</div>
			</div>			

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs no-print">
					<li class="active">
						<a href="#showticket-details" data-toggle="tab">Details</a>
					</li>
					<li>
						<a @click="onShow('ticketupdates')"
						href="#showticket-updates" data-toggle="tab">Updates</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showticket-details">

						@include('pages.tickets.ticketdetails')					

					</div>

					<div class="tab-pane" id="showticket-updates">

						@include('pages.tickets.ticketupdates')

					</div>				
				</div>	
			</div>

		</div>
	</div>

@endsection

@section('modal')

    <div id="view-assigntechnician" class="modal fade" tabindex="-1">
    	<div class="modal-dialog">
    		<div class="modal-content">
		        <div class="modal-header">
		            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		            	<span aria-hidden="true">&times;</span>
		            </button>
		            <h4 class="modal-title">Assign Technician</h4>
		        </div>

				<form id="assignTechnicianForm" method="post" action="{{ route('ticket.updatetechnician', $ticket->id) }}" data-redirect="true" class="ajax">

					{{ csrf_field() }}

			        <div class="modal-body">
						<div class="row margin">
							<div class="col-sm-12">

								<div class="row">
									<div class="form-group" style="float: left;">
										<label for="assigneeList" class="control-label">Assign Technician</label>
									</div>
									<div class="form-group" style="float: left; margin-left: 25%">
										<label for="assigneeList" class="control-label">Notes</label>
									</div>
								</div>

								<div class="row">
									<div class="form-group col-sm-5 no-padding" style="float: left;">
										<select name="technician_id" required class="form-control">

											<option value="0" selected disabled="">Select technician...</option>

											@foreach($technicians as $technician)

												<option value="{{ $technician->id }}"
													@if($ticket->technician)
													{{ $ticket->technician->id == $technician->id ? 'selected' : '' }}
													@endif
												>
												{{ $technician->renderFullname() }}
												</option>

											@endforeach

										</select>
									</div>
									<div class="form-group col-sm-5 no-padding" style="float: left; margin-left: 5%">
										<textarea class="form-control" placeholder="Add Notes" name="" required></textarea>
									</div>
								</div>

			                </div>
			            </div>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-primary pull-left">Assign</button>
					</div>	

				</form>			

			</div>
	        <!-- /.modal-content -->			
		</div>
	    <!-- /.modal-dialog -->
    </div>

@endsection