<div>
	<div class="box-header col-sm-12">
		<i class="fa fa-cog"></i>
		<h3 class="box-title">Settings</h3>
	</div>		
	<!-- /.box-header -->
</div>

<form id="settingsForm" method="post" action="{{ route('employee.updatesettings', $employee->id) }}"
class="ajax">

	{{ csrf_field() }}

	@if($self->isSuperUser())
	<input type="hidden" name="hasAssignees" value="1">
	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Assignee</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>
				<select multiple="multiple" style="width: 100%;"
				class="form-control select2" name="employees[]" data-placeholder="Select assignee...">

					@foreach($employees as $assignee)
						@if($assignee->id != $self->id)
						
							<option value="{{ $assignee->id }}" {{ in_array($assignee->id, $self->getAssigneesID()) ? 'selected' : '' }}>{{ $assignee->renderFullname() }}</option>
	
						@endif
					@endforeach

                </select>
			</div>
		</div>
	</div>
	@endif
	<!-- /.box-body -->

	<onvacationsettings
	:employee="{{ $employee }}"
	:proxies="{{ $employees }}"
	></onvacationsettings>	

	<div class="box-footer">
		<div class="pull-right">
			
			<button type="submit" id="settingsFormBtn" class="btn btn-primary s-margin-r">Update</button>
	
		</div>

	</div>
	<!-- /.box-footer -->

</form>	