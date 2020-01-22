<form id="locationDetailsForm" method="post" class="ajax"
	@if(isset($location))
	action="{{ route('location.edit', $location->id) }}"
	@else
	action="{{ route('location.store') }}" data-redirect="true"
	@endif
>

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-12">
				<label>Name <span class="has-error">*</span></label>
				<input value="{{ isset($location) ? $location->name : '' }}" required 
				type="text" name="name" placeholder="Name" class="form-control">
			</div>
			<div class="form-group col-sm-12">
				<label>Rooms</label>
				<select class="form-control select2" name="rooms[]" multiple="multiple">
					@foreach ($rooms as $room)
						<option value="{{ $room->id }}" @isset ($location)
						    {{ in_array($room->id, $location->getRoomsID()) ? 'selected' : '' }}
						@endisset>{{ $room->name }}</option>
					@endforeach
				</select>
			</div>
		</div>				

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		
		<div class="pull-right">
			
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($location))
				Update
				@else
				Create
				@endif
			</button>

		</div>

	</div>
	<!-- /.box-footer -->

</form>