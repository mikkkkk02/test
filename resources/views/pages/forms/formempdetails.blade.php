<div class="box-body">

	<div class="row">
		<div class="form-group col-sm-3">
			<label>Employee</label>
			<select class="form-control" name="employee_id"
			@if(isset($resubmit) ? 0 : isset($form) && !$form->isEditable())
			disabled
			@endif
			>
		
				@if(isset($form))

					<option value="{{ $form->employee->id }}">{{ $form->employee->renderFullname() }} (ID: {{ $form->employee->id }})</option>

				@else

					<option value="{{ $self->id }}">{{ $self->renderFullname() }} (ID: {{ $self->id }})</option>

					@if(isset($assignees))
					@foreach($assignees as $assignee)

						<option value="{{ $assignee->assigner->id }}">{{ $assignee->assigner->renderFullname() }} (ID: {{ $assignee->assigner->id }})</option>

					@endforeach
					@endif

				@endif

			</select>
		</div>
		@if(isset($form))
		<div class="form-group col-sm-3">
			<label>Create Date</label>
			<div class="input-group date">
				<div class="input-group-addon">
					<i class="fa fa-calendar"></i>
				</div>
				<input value="{{ $form->created_at }}" data-date-format='yyyy-mm-dd'
				type="text" placeholder="Created At" class="form-control datepicker"
				@if(isset($form) && !$form->isDraft())
				disabled
				@endif
				>
			</div>
		</div>
		<div class="form-group col-sm-3">
			<label>Last Update</label>
			<div class="input-group date">
				<div class="input-group-addon">
					<i class="fa fa-calendar"></i>
				</div>
				<input value="{{ $form->updated_at }}" data-date-format='yyyy-mm-dd'
				type="text" placeholder="Updated At" class="form-control datepicker"
				@if(isset($form) && !$form->isDraft())
				disabled
				@endif
				>
			</div>
		</div>
		@endif			
	</div>

	@if(isset($form))
	<div class="row">
		<div class="form-group col-sm-3">
			<label>Job Level</label>
			<input value="{{ $form->employee->renderJobLevel() }}" required
			type="text" placeholder="Job Level" class="form-control"
			@if(isset($form) && !$form->isDraft())
			disabled
			@endif
			>
		</div>
		<div class="form-group col-sm-3">
			<label>Cost Center</label>
			<input value="{{ $form->employee->cost_center }}" required
			type="text" placeholder="Cost Center" class="form-control"
			@if(isset($form) && !$form->isDraft())
			disabled
			@endif
			>
		</div>						
	</div>

	<div class="row">
		<div class="form-group col-sm-3">
			<label>Department</label>
			<input value="{{ isset($form->employee->department->department) ? $form->employee->department->department->name : '' }}" required
			type="text" placeholder="Department" class="form-control"
			@if(isset($form) && !$form->isDraft())
			disabled
			@endif
			>
		</div>
		<div class="form-group col-sm-3">
			<label>Team</label>
			<input value="{{ isset($form->employee->department->team) ? $form->employee->department->team->name : '' }}" required
			type="text" placeholder="Team" class="form-control"
			@if(isset($form) && !$form->isDraft())
			disabled
			@endif
			>
		</div>
		<div class="form-group col-sm-3">
			<label>Position</label>
			<input value="{{ isset($form->employee->department->position) ? $form->employee->department->position->title : '' }}" required
			type="text" placeholder="Position" class="form-control"
			@if(isset($form) && !$form->isDraft())
			disabled
			@endif
			>
		</div>						
	</div>
	@endif

</div>