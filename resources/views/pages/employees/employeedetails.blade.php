@isset ($employee)
    
	@include('pages.employees.uploadavatar')

@endisset

<form id="employeeDetailsForm" method="post" class="ajax"
	@if(isset($employee))
	action="{{ route('employee.edit', $employee->id) }}"
	@else
	action="{{ route('employee.store') }}" data-redirect="true"
	@endif
>

    {{ csrf_field() }}

	<div class="box-body">

		<div class="row">

			<div class="col-sm-12">
				<h5 class="upcase">Personal Information</h5>
			</div>

			<div class="form-group col-sm-3">
				<label>First Name</label>
				<input value="{{ isset($employee) ? $employee->first_name : '' }}" required
				type="text" name="first_name" placeholder="First Name" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>
			{{-- <div class="form-group col-sm-3">
				<label>Middle Name</label>
				<input value="{{ isset($employee) ? $employee->middle_name : '' }}"
				type="text" name="middle_name" placeholder="Middle Name" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>	 --}}				
			<div class="form-group col-sm-3">
				<label>Last Name</label>
				<input value="{{ isset($employee) ? $employee->last_name : '' }}" required
				type="text" name="last_name" placeholder="Last Name" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<h5 class="upcase">Contact Information</h5>
			</div>
			<div class="form-group col-sm-6">
				<label>Email</label>
				<input type="email" name="email" placeholder="Email" class="form-control" required 
				@if(isset($employee))
				value="{{ isset($employee) ? $employee->email : '' }}" disabled
				@endif
				>
			</div>				
			{{-- <div class="form-group col-sm-3">
				<label>Contact No.</label>
				<input value="{{ isset($employee) ? $employee->contact_no : '' }}"
				type="text" name="contact_no" placeholder="Contact No." class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>	
			<div class="form-group col-sm-3">
				<label>Company Contact No.</label>
				<input value="{{ isset($employee) ? $employee->company_no : '' }}"
				type="text" name="company_no" placeholder="Company No." class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>	 --}}											
		</div>

		{{-- <div class="row">
			<div class="form-group col-sm-8">
				<label>Address line 1</label>
				<input value="{{ isset($employee) ? $employee->address_line1 : '' }}"
				type="text" name="address_line1" placeholder="Address Line 1" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>
			<div class="form-group col-sm-8">
				<label>Address line 2</label>
				<input value="{{ isset($employee) ? $employee->address_line2 : '' }}"
				type="text" name="address_line2" placeholder="Address Line 2" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>									
		</div> --}}

		<div class="row">
			<div class="col-sm-12">
				<h5 class="upcase">Professional Information</h5>
			</div>
			<div class="form-group col-sm-3">
				<label>Assignment No.</label>
				<input value="{{ isset($employee) ? $employee->id : '' }}" required 
				type="number" name="id" placeholder="Assignment No." class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>		
			<div class="form-group col-sm-3">
				<label>Assignment Category</label>
				<select required
				name="employee_category_id" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>

					@foreach($categories as $category)
				
						<option value="{{ $category->id }}" 
							@if(isset($employee))
							{{ $employee->employee_category_id == $category->id ? 'selected' : '' }}
							@endif
						>
						{{ $category->title }}
						</option>

					@endforeach

				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>Location</label>
				<select required
				name="location_id" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>

					<option value="0" selected disabled>Select location...</option>

					@foreach($locations as $location)
				
						<option value="{{ $location->id }}" 
							@if(isset($employee))
							{{ $employee->location_id == $location->id ? 'selected' : '' }}
							@endif
						>
						{{ $location->name }}
						</option>

					@endforeach

				</select>
			</div>			
		</div>

		<div class="row">

			@if(isset($canEdit) && $canEdit)
			<div class="form-group col-sm-3">
				<label>Job Level</label>

				<select value="{{ isset($employee) ? $employee->job_level : 0 }}"
				name="job_level" placeholder="Job Level" class="form-control">
					
					@foreach(App\User::getJobLevel() as $jobLevel)

					<option value="{{ $jobLevel['value'] }}"
						@if(isset($employee))
						{{ $employee->job_level == $jobLevel['value'] ? 'selected' : '' }}
						@endif
					>{{ $jobLevel['label'] }}</option>

					@endforeach

				</select>

			</div>
			@endif

			<div class="form-group col-sm-3">
				<label>Cost Center</label>
				<input value="{{ isset($employee) ? $employee->cost_center : '' }}" required
				type="text" name="cost_center" placeholder="Cost Center" class="form-control"
				@if(isset($employee) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>
			</div>
		</div>	

		<departmentselector
		:employee="{{ isset($employee) ? $employee  : '{}' }}"
		:supervisors="{{ $employees }}"
		:companies="{{ json_encode($companies) }}"
		></departmentselector>

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		<div class="pull-right">
			
			@if(isset($canEdit) && $canEdit)
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($employee))
				Update
				@else
				Register
				@endif
			</button>
			@endif
			
		</div>
	</div>
	<!-- /.box-footer -->
	
</form>