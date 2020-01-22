<form id="tmpTicketUpdateForm" method="post" class="ajax">

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-3">
				<label>Request #</label>
				<input value="{{ $ticketUpdate->ticket->form_id }}" disabled
				type="text" class="form-control">
			</div>
			<div class="form-group col-sm-3">
				<label>Form Type</label>
				<input value="{{ $ticketUpdate->ticket->form->template->name }}" disabled
				type="text" class="form-control">
			</div>
			<div class="form-group col-sm-3">
				<label>Requested By</label>
				<input value="{{ $ticketUpdate->employee->renderFullname() }}" disabled
				type="text" class="form-control">
			</div>								
		</div>

		<div class="row">
			<div class="form-group col-sm-3">
				<label>Priority</label>
				<input value="{{ $ticketUpdate->ticket->renderPriorityClass() }}" disabled
				type="text" class="form-control">
			</div>
			<div class="form-group col-sm-3">
				<label>State</label>
				<input value="{{ $ticketUpdate->ticket->renderStatus() }}" disabled
				type="text" class="form-control">
			</div>
			<div class="form-group col-sm-3">
				<label>Status</label>
				<input value="{{ $ticketUpdate->ticket->renderState() }}" disabled
				type="text" class="form-control">
			</div>								
		</div>

		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">Update Details</h3>
			</div>
		
			<div class="box-body">
				<div class="row">
					<div class="form-group col-sm-3">
						<label>Status</label>
						<input value="{{ $ticketUpdate->ticket->renderStatus() }}" disabled
						type="text" class="form-control">
					</div>	
				</div>
				<div class="row col-sm-12">
					<div class="form-group">
						<label>Description</label>
						<textarea class="form-control" name="description" disabled>{{ $ticketUpdate->description }}</textarea>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- /.box-body -->

{{--     @if($checker->hasModuleRoles(['Adding/Editing of Events']))
	<div class="box-footer">
		<button type="submit" class="btn btn-primary s-margin-r">
			@if(isset($event))
			Update
			@else
			Submit
			@endif
		</button>
	</div>
	@endif --}}
	<!-- /.box-footer -->

</form>