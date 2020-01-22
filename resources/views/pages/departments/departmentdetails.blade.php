<form id="departmentDetailsForm" method="post" class="ajax"
	@if(isset($department))
	action="{{ route('department.edit', $department->id) }}"
	@else
	action="{{ route('department.store') }}" data-redirect="true"
	@endif
>
	
	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-4">
				<label>Name</label>
				<input value="{{ isset($department) ? $department->name : '' }}" required 
				type="text" name="name" placeholder="Full Name" class="form-control">
			</div>
			<div class="form-group col-sm-4">
				<label>Group</label>
				<select required
				name="division_id" class="form-control">

					<option value="0" selected disabled>Select group...</option>

					@foreach($companies as $company)
						@foreach($company->divisions as $division)
				
						<option value="{{ $division->id }}" 
							@if(isset($department))
							{{ $department->division_id == $division->id ? 'selected' : '' }}
							@endif
						>
						{{ $division->name }}
						</option>

						@endforeach
					@endforeach

				</select>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-sm-8">
				<label>Description</label>
				<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($department) ? $department->description : '' }}</textarea>
			</div>
		</div>

	</div>
	<!-- /.box-body -->

	<div class="box-footer">

		<div class="pull-right">
				
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($department))
				Update
				@else
				Create
				@endif			
			</button>
			
		</div>

	</div>
	<!-- /.box-footer -->					
	
</form>