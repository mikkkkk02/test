<form id="settingsCEOForm" method="action" action="{{ route('settings.update') }}" class="ajax">

	{{ csrf_field() }}

	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Set-up CEO</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>
				<select style="width: 100%;"
				class="form-control" name="ceo_id">

					<option value="0" disabled selected>Select CEO...</option>

					@foreach($employees as $assignee)

						<option value="{{ $assignee->id }}" {{ $assignee->id == $settings->ceo_id ? 'selected' : '' }}>{{ $assignee->renderFullname() }}</option>

					@endforeach

                </select>
			</div>
		</div>
	</div>

	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Set-up HR</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>

				@foreach($companies as $company)

				<small><b>For {{ $company->renderAbbr() }}</b></small>
				<select style="width: 100%;"
				class="form-control s-margin-b" name="hr_id[{{ $company->id }}]">

					<option value="0" disabled selected>Select HR...</option>

					@foreach($employees as $hr)

						<option value="{{ $hr->id }}" {{ $hr->id == $company->hr_id ? 'selected' : '' }}>{{ $hr->renderFullname() }}</option>

					@endforeach

                </select>

				@endforeach

			</div>
		</div>
	</div>

	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Set-up OD</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>

				@foreach($companies as $company)

				<small><b>For {{ $company->renderAbbr() }}</b></small>
				<select style="width: 100%;"
				class="form-control s-margin-b" name="od_id[{{ $company->id }}]">

					<option value="0" disabled selected>Select HR...</option>

					@foreach($employees as $od)

						<option value="{{ $od->id }}" {{ $od->id == $company->od_id ? 'selected' : '' }}>{{ $od->renderFullname() }}</option>

					@endforeach

                </select>

				@endforeach

			</div>
		</div>
	</div>							

	<div class="box-footer">
		<button type="submit" class="btn btn-primary s-margin-r">Update</button>
	</div>
	<!-- /.box-footer -->

</form>	