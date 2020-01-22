<form id="meetingRoomDetailsForm" method="post" class="ajax" data-redirect="true"
	@if(isset($mrReservation))
	action="{{ route('mrreservation.edit', $mrReservation->id) }}"
	@else
	action="{{ route('mrreservation.store') }}"
	@endif
>

	{{ csrf_field() }}
	<div class="col-sm-12">

		<input type="hidden" name="employee_id" value="{{ \Auth::id() }}">
		
		@include('pages.forms.formfields', [
			'noBoxInfo' => false,
		])

	</div>



	<div class="box-footer">
		
		<div class="pull-right">
			
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($mrReservation))
				Update
				@else
				Create
				@endif			
			</button>
		
		</div>
		
	</div>
	<!-- /.box-footer -->

</form>