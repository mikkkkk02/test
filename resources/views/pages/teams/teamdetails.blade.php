<form id="departmentDetailsForm" method="post" class="ajax"
	@if(isset($team))
	action="{{ route('team.edit', $team->id) }}"
	@else
	action="{{ route('team.store') }}" data-redirect="true"
	@endif
>

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-4">
				<label>Name</label>
				<input value="{{ isset($team) ? $team->name : '' }}" required 
				type="text" name="name" placeholder="Full Name" class="form-control">
			</div>
			<div class="form-group col-sm-4">
				<label>Department</label>
				<select required
				name="department_id" class="form-control">

					<option value="0" selected disabled>Select department...</option>

					@foreach($companies as $company)
						@foreach($company->divisions as $division)
							@foreach($division->departments as $department)
						
								<option value="{{ $department->id }}" 
									@if(isset($team))
									{{ $team->department_id == $department->id ? 'selected' : '' }}
									@endif
								>
								{{ $department->name }}
								</option>

							@endforeach
						@endforeach
					@endforeach

				</select>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-sm-8">
				<label>Description</label>
				<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($team) ? $team->description : '' }}</textarea>
			</div>
		</div>					

	</div>
	<!-- /.box-body -->

	<div class="box-footer">

		<div class="pull-right">
			
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($team))
				Update
				@else
				Create
				@endif			
			</button>
			
		</div>

	</div>
	<!-- /.box-footer -->

</form>