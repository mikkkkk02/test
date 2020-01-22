<form id="settingsForm" method="action" action="{{ route('settings.delagate') }}" class="ajax">

	{{ csrf_field() }}

	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Assignee</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>
				<select multiple="multiple" style="width: 100%;"
				class="form-control select2" name="employees[]" data-placeholder="Select responsibilities...">

					@foreach($employees as $assignee)
						@if($assignee->id != $self->id)
						
							<option value="{{ $assignee->id }}" {{ in_array($assignee->id, $self->getAssigneesID()) ? 'selected' : '' }}>{{ $assignee->renderFullname() }}</option>
	
						@endif
					@endforeach

                </select>
			</div>
		</div>
	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		<button type="submit" class="btn btn-primary s-margin-r">Update</button>
	</div>
	<!-- /.box-footer -->

</form>