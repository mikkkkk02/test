<form id="departmentDetailsForm" method="post" class="ajax"
	@if(isset($division))
	action="{{ route('division.edit', $division->id) }}"
	@else
	action="{{ route('division.store') }}" data-redirect="true"
	@endif
>
	
	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-4">
				<label>Name</label>
				<input value="{{ isset($division) ? $division->name : '' }}" required="" 
				type="text" name="name" placeholder="Name" class="form-control">
			</div>
			<div class="form-group col-sm-4">
				<label>Company</label>
				<select required
				name="company_id" class="form-control">

					<option value="0" selected disabled>Select company...</option>

					@foreach($companies as $company)
				
						<option value="{{ $company->id }}" 
							@if(isset($division))
							{{ $division->company_id == $company->id ? 'selected' : '' }}
							@endif
						>
						{{ $company->name }}
						</option>

					@endforeach

				</select>
			</div>
			<div class="form-group col-sm-4">
				<label>Group Head</label>
				<select required
				name="group_head_id" class="form-control">

					<option value="0" selected disabled>Select group head...</option>

					@foreach($employees as $employee)
				
						<option value="{{ $employee->id }}" 
							@if(isset($division))
							{{ $division->group_head_id == $employee->id ? 'selected' : '' }}
							@endif
						>
						{{ $employee->renderFullname() }}
						</option>

					@endforeach

				</select>
			</div>			
		</div>
		<div class="row">
			<div class="form-group col-sm-12">
				<label>Description</label>
				<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($division) ? $division->description : '' }}</textarea>
			</div>
		</div>					

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		
		<div class="pull-right">
			
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($division))
				Update
				@else
				Create
				@endif			
			</button>

		</div>

	</div>
	<!-- /.box-footer -->

</form>