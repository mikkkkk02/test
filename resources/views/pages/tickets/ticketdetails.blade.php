<section class="invoice">

	<div class="row">
		<div class="col-sm-12">
			<h2 class="page-header">
				<i class="fa fa-tag s-margin-r"></i><b>REQUEST #{{ $ticket->form->id }}</b>
				<small class="pull-right">Date: {{ $ticket->created_at->format('M. d, Y') }}</small>
			</h2>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<h4><b>DETAILS:</b></h4>

			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td><b>Form Type</b></td>
							<td colspan="2"><b>Technician</b></td>
							<td><b>Date Closed</b></td>
						</tr>
						<tr>
							<td>{{ $ticket->form->template->name }}</td>
							<td colspan="2">{{ $ticket->technician ? $ticket->technician->renderFullname() : 'None yet' }}</td>
							<td>{{ $ticket->date_closed }}</td>
						</tr>	
						<tr>
							<td><b>Priority</b></td>
							<td><b>Status</b></td>
							<td><b>State</b></td>
							<td><b>Submit Status</b></td>
						</tr>	
						<tr>
							<td>
								<span class="badge {{ $ticket->renderPriorityClass() }}">{{ $ticket->renderPriority() }}</span>
							</td>
							<td>
								<span class="badge {{ $ticket->renderStatusClass() }}">{{ $ticket->renderStatus() }}</span>
							</td>
							<td>
								<span class="badge {{ $ticket->renderStateClass() }}">{{ $ticket->renderComputedState() }}</span>
							</td>
							<td>
								<span class="badge">{{ $ticket->renderSubmitStatus() }}</span>
							</td>
						</tr>														
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-sm-12">

			<attachments
			:id="'ticket'"
			:header="'Ticket Attachments'"
			:fetchurl="'{{ route('ticket.fetchattachments', $ticket->id) }}'"
			:addattachmenturl="'{{ route('ticket.addattachment', $ticket->id) }}'"
			:disabled="'{{ !$ticket->canAttach() }}'"
			></attachments>

			
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showform-details" data-toggle="tab">Form Details</a>
					</li>	
					<li>
						<a href="#showform-approvers" data-toggle="tab">Form Approvers</a>
					</li>
					<li>
						<a @click="onShow('requesthistory')"
						href="#showform-history" data-toggle="tab">Form History</a>
					</li>								
				</ul>
			</div>
			<div class="tab-content">
				<div class="tab-pane active" id="showform-details">

					@include('pages.forms.formdetails')

				</div>
				<div class="tab-pane" id="showform-approvers">

					@include('pages.forms.formapprovers')

				</div>
				<div class="tab-pane" id="showform-history">

					<requesthistory ref="requesthistory"
					:fetchurl="'{{ route('requesthistory.fetch', $ticket->form_id) }}'"
					></requesthistory>

				</div>				
			</div>

		</div>
	</div>

{{-- 	<div class="row invoice-info">
		<div class="col-sm-12">
			<h4><b>EMPLOYEE DETAILS:</b></h4>

			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td><b>Requested By</b></td>
						<td><b>Immediate Leader</b></td>
						<td><b>Cost Center</b></td>
					</tr>
					<tr>
						<td>{{ $ticket->owner->renderFullname() }}</td>
						<td>{{ $ticket->owner->supervisor->renderFullname() }}</td>
						<td>{{ $ticket->owner->cost_center }}</td>
					</tr>
					<tr>
						<td><b>Department</b></td>
						<td><b>Team</b></td>
						<td><b>Position</b></td>
					</tr>
					<tr>
						<td>
							@if($ticket->owner->getDepartment())
							{{ $ticket->owner->getDepartment()->name }}
							@endif
						</td>
						<td>
							@if($ticket->owner->getTeam())
							{{ $ticket->owner->getTeam()->name }}
							@endif
						</td>
						<td>
							@if($ticket->owner->getDepartment() && $ticket->owner->getDepartment()->position)
							{{ $ticket->owner->getDepartment()->position->title }}
							@endif
						</td>
					</tr>											
				</table>
			</div>

		</div>													
	</div> --}}

</section>