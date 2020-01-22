<form id="groupDetailsForm" method="post" class="ajax"
	@if(isset($group))
	action="{{ route('group.edit', $group->id) }}"
	@else
	action="{{ route('group.store') }}" data-redirect="true"
	@endif
>

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-4">
				<label>Name</label>
				<input value="{{ isset($group) ? $group->name : '' }}"
				type="text" name="name" placeholder="Name" class="form-control">
			</div>
			<div class="form-group col-sm-4">
				<label>Company</label>
				<select
				name="company_id" class="form-control">

					<option value="0" selected>All</option>

					@foreach($companies as $company)
				
						<option value="{{ $company->id }}" 
							@if(isset($group))
							{{ $group->company_id == $company->id ? 'selected' : '' }}
							@endif
						>
						{{ $company->name }}
						</option>

					@endforeach

				</select>
			</div>
			<div class="form-group col-sm-4">
				<label>Group</label>
				<select
				name="type" class="form-control">

					@foreach($types as $type)

						<option value="{{ $type['value'] }}" 
							@if(isset($group))
							{{ $group->type == $type['value'] ? 'selected' : '' }}
							@endif
						>
						{{ $type['label'] }}
						</option>

					@endforeach

				</select>
			</div>						
		</div>
		<div class="row">
			<div class="form-group col-sm-12">
				<label>Description</label>
				<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($group) ? $group->description : '' }}</textarea>
			</div>			
		</div>

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		<div class="pull-right">
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($group))
				Update
				@else
				Create
				@endif
			</button>
		</div>
	</div>
	<!-- /.box-footer -->					

</form>	