<form id="settingsTicketForm" method="action" action="{{ route('settings.ticket') }}" class="ajax">

	{{ csrf_field() }}

	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Set-up Admin Technician</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>

				@foreach($companies as $company)

				<small><b>For {{ $company->renderAbbr() }}</b></small>
				<select multiple style="width: 100%;"
				class="form-control select2" name="admin_technician_id[{{ $company->id }}][]" data-placeholder="Select technicians...">

					@foreach($employees as $technician)

						<option value="{{ $technician->id }}" {{ in_array($technician->id, $company->getAdminTechnicianID()) ? 'selected' : '' }}>{{ $technician->renderFullname() }}</option>

					@endforeach

                </select>

				@endforeach

			</div>
		</div>
	</div>

	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Set-up HR Technician</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>

				@foreach($companies as $company)

				<small><b>For {{ $company->renderAbbr() }}</b></small>
				<select multiple style="width: 100%;"
				class="form-control select2" name="hr_technician_id[{{ $company->id }}][]" data-placeholder="Select technicians...">

					@foreach($employees as $technician)

						<option value="{{ $technician->id }}" {{ in_array($technician->id, $company->getHRTechnicianID()) ? 'selected' : '' }}>{{ $technician->renderFullname() }}</option>

					@endforeach

                </select>

				@endforeach

			</div>
		</div>
	</div>

	<div class="box-body">
		<div class="form-horizontal col-sm-12">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Set-up OD Technician</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>

				@foreach($companies as $company)

				<small><b>For {{ $company->renderAbbr() }}</b></small>
				<select multiple style="width: 100%;"
				class="form-control select2" name="od_technician_id[{{ $company->id }}][]" data-placeholder="Select technicians...">

					@foreach($employees as $technician)

						<option value="{{ $technician->id }}" {{ in_array($technician->id, $company->getODTechnicianID()) ? 'selected' : '' }}>{{ $technician->renderFullname() }}</option>

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