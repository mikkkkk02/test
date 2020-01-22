<form id="idpDetailsForm" method="post" data-redirect="true" class="ajax" 
	@if(!isset($temp))
		@if(isset($idp))
		action="{{ route('idp.edit', $idp->id) }}"
		@else
		action="{{ route('idp.store') }}" data-redirect="true"
		@endif
	@endif
>

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-3">
				<label>Employee</label>
				<select required
				name="employee_id" class="form-control">
			
					@if(isset($idp))

						<option value="{{ $idp->employee->id }}" selected>{{ $idp->employee->renderFullname() }}</option>

					@else

						@if($checker->hasModuleRoles(['Adding/Editing of Learning Activities']))

							@foreach($employees as $key => $employee)

								<option value="{{ $employee->id }}"
									{{ $key == 0 ? 'selected' : '' }}
								>{{ $employee->renderFullname() }}</option>

							@endforeach

						@else

							<option value="{{ $self->id }}" selected>{{ $self->renderFullname() }}</option>

						@endif

					@endif

				</select>
			</div>				
			<div class="form-group col-sm-3">
				<label>Year</label>
				<select required 
				name="completion_year" class="form-control">
	
					@foreach(App\Idp::getRecentYears() as $key => $year)

						<option value="{{ $year }}"
							@if(isset($idp))
							{{ $idp->completion_year == $year ? 'selected' : '' }}
							@else
							{{ $key == 1 ? 'selected' : '' }}
							@endif
						>{{ $year }}</option>

					@endforeach

				</select>
			</div>
			@if(isset($idp))
			<div class="form-group col-sm-3">
				<label>Status</label>
				<select required
				name="status" class="form-control">

					@foreach(App\Idp::getStatus() as $s)

						<option value="{{ $s['value'] }}"
							{{ $idp->status == $s['value'] ? 'selected' : '' }}
						>{{ $s['label'] }}</option>

					@endforeach

				</select>
			</div>
			@endif	
		</div>

		<div class="row">
			<div class="form-group col-sm-3">
				<label>Specific Competency</label>
				<select required 
				name="competency_id" class="form-control">
				
					@foreach($competencies as $competency)

						<option value="{{ $competency->id }}"
							@if(isset($idp))
							{{ $idp->competency_id == $competency->id ? 'selected' : '' }}
							@endif
						>{{ $competency->name }}</option>

					@endforeach

				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>Learning Type</label>
				<select required 
				name="learning_type" class="form-control">
				
					@foreach(App\Idp::getLearningActivityType() as $learning)

						<option value="{{ $learning['value'] }}"
							@if(isset($idp))
							{{ $idp->learning_type == $learning['value'] ? 'selected' : '' }}
							@endif
						>{{ $learning['label'] }}</option>

					@endforeach

				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>Competency Type</label>
				<select required 
				name="competency_type" class="form-control">
				
					@foreach(App\Idp::getCompetencyType() as $competency)

						<option value="{{ $competency['value'] }}"
							@if(isset($idp))
							{{ $idp->competency_type == $competency['value'] ? 'selected' : '' }}
							@endif
						>{{ $competency['label'] }}</option>

					@endforeach

				</select>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-sm-3">
				<label>Current Proficiency Level</label>
				<select required 
				name="current_proficiency_level" class="form-control">
					
					@for($i = App\Idp::MINPROFICIENCY; $i <= App\Idp::MAXPROFICIENCY; $i++)
	
						<option value="{{ $i }}"
							@if(isset($idp))
							{{ $idp->current_proficiency_level == $i ? 'selected' : '' }}
							@endif
						>{{ $i }}</option>

					@endfor

				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>Required Proficiency Level</label>
				<select required
				name="required_proficiency_level" class="form-control">
					
					@for($i = App\Idp::MINPROFICIENCY; $i <= App\Idp::MAXPROFICIENCY; $i++)
	
						<option value="{{ $i }}"
							@if(isset($idp))
							{{ $idp->required_proficiency_level == $i ? 'selected' : '' }}
							@endif
						>{{ $i }}</option>

					@endfor

				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>Type</label>
				<select required 
				name="type" class="form-control">

					@foreach(App\Idp::getType() as $type)

						<option value="{{ $type['value'] }}"
							@if(isset($idp))
							{{ $idp->type == $type['value'] ? 'selected' : '' }}
							@endif
						>{{ $type['label'] }}</option>

					@endforeach

				</select>
			</div>			
		</div>

		@if(isset($idp))
		<div class="row">
			<div class="form-group col-sm-9">
				<label>Details</label>
				<textarea type="text" name="details" placeholder="Details" class="form-control">{{ isset($idp) ? $idp->details : '' }}</textarea>
			</div>									
		</div>		
		@endif						

	</div>
	<!-- /.box-body -->
	
	@if(!isset($idp) || (isset($idp) && $idp->isApproved()))
	<div class="box-footer">
		<button type="submit" class="btn btn-primary s-margin-r">
			@if(isset($idp))
			Update
			@else
			Submit
			@endif			
		</button>
	</div>
	@endif
	<!-- /.box-footer -->

</form>	