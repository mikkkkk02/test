@extends('master')

@section('pageTitle', $form->template->name)

@section('breadcrumb')

	<div class="content-header">
		<h1>{{ $form->template->name }}<small>Requested By: {{ $form->employee->renderFullname() }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('forms') }}"><i class="fa fa-file"></i> Forms</a>
	        </li>
	        <li>
	        	<a href="#">{{ $form->template->name }}</a>: <a href="#">{{ $form->employee->renderFullname() }}</a>
	        </li>
	    </ol>
	</div>	

@endsection

@section('content')


	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					@if(!$form->template->category->forEvent())
						@if($self->isApprover($form->id))

							@if($form->template->isInOrder())
								@if ($form->isCurrentApprover($self))
									<form id="requestApproveFormBtn" method="post" action="{{ route('request.approve', $form->id) }}" data-redirect="true" 
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
							@else
								@if ($form->isApprover($self))
									<button class="btn btn-success s-margin-r" data-toggle="modal" data-target="#approve-request">
										<i class="fa fa-plus-circle s-margin-r"></i>Approve
									</button>
									<button class="btn btn-danger s-margin-r" data-toggle="modal" data-target="#disapprove-request">
										<i class="fa fa-minus-circle s-margin-r"></i>Disapprove
									</button>
								@endif
							@endif
						@endif

						@if(!$form->isDisapproved() && !$form->isWithdrawn() && ($self->id == $form->employee_id || $self->id == $form->assignee_id))
						<form id="requestWithdrawFormBtn" method="post" action="{{ route('request.withdraw', $form->id) }}" data-redirect="true" 
						class="ajax inline-block">

							{{ csrf_field() }}

							<button type="submit" class="btn btn-danger s-margin-r">
								<i class="fa fa-close s-margin-r"></i>Withdraw
							</button>

						</form>
						@endif

						@if($form->isApproved() && ($form->ticket && $form->ticket->isClosed()) && ($self->id == $form->employee_id || $self->id == $form->assignee_id))
						<a href="{{ route('request.resubmit', $form->id) }}" class="btn btn-info s-margin-r">
							<i class="fa fa-refresh s-margin-r"></i>Re-submit
						</a>
						@endif

						@if($form->isArchivable())
							@if($self->id == $form->employee_id || $self->id == $form->assignee_id || $self->isSuperUser())
								@if($form->trashed())
								<form id="requestRestoreForm" method="post" action="{{ route('request.restore', $form->id) }}" data-redirect="true" class="ajax inline">

									{{ csrf_field() }}

									<button type="submit" class="btn btn-success s-margin-r">
										<i class="fa fa-plus-circle s-margin-r"></i>Restore
									</button>

								</form>
								@else
								<form id="requestArchiveForm" method="post" action="{{ route('request.archive', $form->id) }}" data-redirect="true" class="ajax inline">

									{{ csrf_field() }}
									{{ method_field('DELETE') }}

									<button type="submit" class="btn btn-danger s-margin-r">
										<i class="fa fa-minus-circle s-margin-r"></i>Archive
									</button>

								</form>
								@endif
							@endif
						@endif
						
					@endif

				</div>
			</div>

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showform-details" data-toggle="tab">Details</a>
					</li>

					@if(!$form->template->category->forEvent())
					<li>
						<a href="#showform-approvers" data-toggle="tab">Approvers</a>
					</li>
					<li>
						<a @click="onShow('requestupdates')"
						href="#showform-updates" data-toggle="tab">Updates</a>
					</li>
					@endif

					<li>
						<a @click="onShow('requesthistory')"
						href="#showform-history" data-toggle="tab">History</a>
					</li>

				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showform-details">

						@include('pages.forms.formdetails')

					</div>

					@if(!$form->template->category->forEvent())
					<div class="tab-pane" id="showform-approvers">

						@include('pages.forms.formapprovers')

					</div>
					<div class="tab-pane" id="showform-updates">

						<requestupdates ref="requestupdates"
						:addurl="'{{ route('request.addupdate', $form->id) }}'"
						:fetchurl="'{{ route('request.fetchupdates', $form->id) }}'"
						></requestupdates>

					</div>	
					@endif
					<div class="tab-pane" id="showform-history">

						<requesthistory ref="requesthistory"
						:fetchurl="'{{ route('requesthistory.fetch', $form->id) }}'"
						></requesthistory>

					</div>					

				</div>
			</div>

		</div>
	</div>	

@endsection


@section('modal')

@if(!$form->template->category->forEvent() && $self->isApprover($form->id))
	<!-- Disapprove Modal -->
	<div id="disapprove-request" class="modal fade" tabindex="-1">
	    <div class="modal-dialog">
	        <form id="requestDisapproveFormBtn" method="post" action="{{ route('request.disapprove', $form->id) }}" data-redirect="true" class="ajax modal-content">
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
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" class="btn btn-default">Back</button>			
					<button type="submit" class="btn btn-primary pull-left">Disapprove</button>
				</div>

	        </form>
	        <!-- /.modal-content -->
	    </div>
	    <!-- /.modal-dialog -->
	</div>

	@if(!$form->template->isInOrder())
	<!-- Approve Modal -->
	<div id="approve-request" class="modal fade" tabindex="-1">
	    <div class="modal-dialog">
	        <form id="clearanceApproveFormBtn" method="post" action="{{ route('request.approve', $form->id) }}" data-redirect="true" class="ajax modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title">Clearing request</h4>
	            </div>         

				{{ csrf_field() }}

	            <div class="modal-body">

					<div class="row">
						<div class="col-sm-12">
					
							<div class="form-group">
								<label>Remarks</label>
								<textarea type="text" name="reason" placeholder="Remarks" class="form-control"></textarea>
							</div>
			
		                </div>
	                </div>

				</div>
				<div class="modal-footer">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" class="btn btn-default">Back</button>			
					<button type="submit" class="btn btn-primary pull-left">Approve</button>
				</div>

	        </form>
	        <!-- /.modal-content -->
	    </div>
	    <!-- /.modal-dialog -->
	</div>
	@endif
@endif

@endsection
