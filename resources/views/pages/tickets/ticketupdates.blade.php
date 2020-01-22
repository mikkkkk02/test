
<div class="row">
	<div class="col-sm-12">
		<div class="box box-widget">

			@if ($ticket->isTravelOrder() && ($ticket->isTechnician() || $ticket->isOwner()))
				<ticketupdates ref="ticketupdates"
				:ticketstatus="'{{ $ticket->status }}'"
				:statuses="{{ json_encode($statuses) }}"
				:disabled="'{{ !$ticket->canUpdate() }}'" 
				:addurl="'{{ route('ticket.addupdate', $ticket->id) }}'"
				:fetchurl="'{{ route('ticket.fetchupdates', $ticket->id) }}'"
				></ticketupdates>

				<div class="box-header">
					<i class="fa fa-plane"></i>
					<h3 class="box-title">Travel Details</h3>
				</div>

				<div class="box-body">

					<div class="row">

						<div class="col-sm-6 table-responsive">

							<formdetails
							@if(isset($formDetailsId))
							:id="'{{ $formDetailsId }}'"
							@endif
							:disabled="1"
							:answers="{{ isset($form) ? $answers : '[]' }}"
							:fields="{{ json_encode($formTemplate->fetchAvailableTablefields(true, true)) }}"
							:noadding="1"
							></formdetails>

						</div>

						<div class="col-sm-6 table-responsive">
							<ticket-travel-order-details
							:disabled="'{{ !$ticket->canUpdate() }}'" 
							:fetchurl="'{{ route('ticket.fetchtravelorderdetails', $ticket->id) }}'"
							:submiturl="'{{ route('ticket.addtravelorderdetails', $ticket->id) }}'"
							:autofetch="true">
							</ticket-travel-order-details>
						</div>
<!-- 
						@if ($ticket->canUpdate())
						<div class="col-sm-12">
							<ticket-travel-order-form
							:submiturl="'{{ route('ticket.addtravelorderdetails', $ticket->id) }}'">
							</ticket-travel-order-form>
						</div>
						@endif -->

					</div>
					
				</div>

			@elseif ($ticket->isMeetingRoom() && $ticket->isTechnician())

				<div class="box-header">
					<i class="fa fa-plane"></i>
					<h3 class="box-title">Meeting Room Reservation</h3>
				</div>

				<!-- <form class="ajax" action="{{ route('ticket.updateroomdetails', $ticket->form->mr_reservation->id) }}" data-redirect="true" method="POST">
					{{ csrf_field() }} -->

					<div class="box-body">
						<div class="row">
							<div class="col-sm-12">

								<ticket-room-update ref="ticketupdates"
								:ticketstatus="'{{ $ticket->status }}'"
								:statuses="{{ json_encode($statuses) }}"
								:disabled="'{{ !$ticket->canUpdate() }}'" 
								:loadreservation="{{ isset($ticket->form->mr_reservation) ? 1 : 0 }}"
								:fetchmrr="'{{ route('location.fetchnopagination') }}'"
								:fetchurl="'{{ route('ticket.fetchupdates', $ticket->id) }}'"
								:addurl="'{{ route('ticket.updateroomdetails', $ticket->form->mr_reservation->id) }}'"
								:mrreservation="{{ isset($ticket->form->mr_reservation) ? $ticket->form->mr_reservation : 0 }}"
								:cansetroom="true">
								</ticket-room-update>

							</div>
						</div>
					</div>


				<!-- </form> -->
			@else
			<ticketupdates ref="ticketupdates"
			:ticketstatus="'{{ $ticket->status }}'"
			:statuses="{{ json_encode($statuses) }}"
			:disabled="'{{ !$ticket->canUpdate() }}'" 
			:addurl="'{{ route('ticket.addupdate', $ticket->id) }}'"
			:fetchurl="'{{ route('ticket.fetchupdates', $ticket->id) }}'"
			></ticketupdates>
			@endif


		</div>
	</div>
</div>
