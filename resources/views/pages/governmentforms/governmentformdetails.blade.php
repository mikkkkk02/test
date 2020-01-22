<form id="departmentDetailsForm" method="post" class="ajax"
	@if(isset($governmentForm))
	action="{{ route('governmentform.edit', $governmentForm->id) }}"
	@else
	action="{{ route('governmentform.store') }}" data-redirect="true"
	@endif
>
	
	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-8">
				<label>Name <span class="has-error">*</span></label>
				<input value="{{ isset($governmentForm) ? $governmentForm->name : '' }}" required
				type="text" name="name" placeholder="Name" class="form-control"
				@if(isset($governmentForm) && isset($canEdit) && !$canEdit)
				disabled
				@endif 
				>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-8">
				<label>Description</label>
				<textarea type="text" name="description" placeholder="Description" class="form-control"
				@if(isset($governmentForm) && isset($canEdit) && !$canEdit)
				disabled
				@endif
				>{{ isset($governmentForm) ? $governmentForm->description : '' }}</textarea>
			</div>
		</div>

	</div>
	<!-- /.box-body -->

	
	<div class="box-footer">
		
		<div class="pull-right">
			
			@if(isset($canEdit) && $canEdit)
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($governmentForm))
				Update
				@else
				Create
				@endif			
			</button>
			@endif

		</div>

	</div>
	<!-- /.box-footer -->

</form>