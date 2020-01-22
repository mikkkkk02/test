<form id="departmentDetailsForm" method="post" class="ajax"
	@if(isset($position))
	action="{{ route('position.edit', $position->id) }}"
	@else
	action="{{ route('position.store') }}" data-redirect="true"
	@endif
>
	
	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-4">
				<label>Title</label>
				<input value="{{ isset($position) ? $position->title : '' }}" required="" 
				type="text" name="title" placeholder="Title" class="form-control">
			</div>
			<div class="form-group col-sm-4">
				<label>Department</label>
				<select required
				name="department_id" class="form-control">

					@foreach($companies as $company)
						@foreach($company->divisions as $division)
							@foreach($division->departments as $department)
				
								<option value="{{ $department->id }}" 
									@if(isset($position))
									{{ $position->department_id == $department->id ? 'selected' : '' }}
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
				<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($position) ? $position->description : '' }}</textarea>
			</div>
		</div>					

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		
		<div class="pull-right">
			
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($position))
				Update
				@else
				Create
				@endif
			</button>

		</div>

	</div>
	<!-- /.box-footer -->	

</form>	