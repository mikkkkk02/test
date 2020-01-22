<form id="departmentDetailsForm" method="post" class="ajax" 
	@if(isset($company))
	action="{{ route('company.edit', $company->id) }}"
	@else
	action="{{ route('company.store') }}" data-redirect="true"
	@endif
>

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-4">
				<label>Name</label>
				<input value="{{ isset($company) ? $company->name : '' }}" required
				type="text" name="name" placeholder="Name" class="form-control">
			</div>
			<div class="form-group col-sm-2">
				<label>Abbreviation</label>
				<input value="{{ isset($company) ? $company->abbreviation : '' }}" required
				type="text" name="abbreviation" placeholder="Abbreviation" class="form-control">
			</div>
		</div>

		<div class="row">
			<div class="form-group col-sm-9">
				<label>Description</label>
				<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($company) ? $company->description : '' }}</textarea>
			</div>
		</div>					

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		<div class="pull-right">
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($company))
				Update
				@else
				Create
				@endif
			</button>
		</div>
	</div>
	<!-- /.box-footer -->					
	
</form>	