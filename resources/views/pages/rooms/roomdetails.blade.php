<form id="roomDetailsForm" method="post" class="ajax"
	@if(isset($room))
	action="{{ route('room.edit', $room->id) }}"
	@else
	action="{{ route('room.store') }}" data-redirect="true"
	@endif
>

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-6">
				<label>Name <span class="has-error">*</span></label>
				<input value="{{ isset($room) ? $room->name : '' }}" required 
				type="text" name="name" placeholder="Name" class="form-control">
			</div>
			<div class="form group col-sm-6">
				<label>Color</label>
				<input value="{{ isset($room) ? $room->color : '' }}" required
				type="text" name="color" class="form-control jscolor">
			</div>
			<div class="form-group col-sm-12">
				<label>Locations</label>
				<select class="form-control" name="location_id" required>
					<option value="" selected hidden>Please select a Location</option>
					@foreach ($locations as $location)
						<option value="{{ $location->id }}" {{ isset($room) ? $room->isSelected($location->id) : '' }}>{{  $location->name }}</option>
					@endforeach
				</select>
			</div>
		</div>				

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		
		<div class="pull-right">
				
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($room))
				Update
				@else
				Create
				@endif
			</button>

		</div>

	</div>
	<!-- /.box-footer -->

</form>