<form id="formTemplateApproversForm" method="post" action="{{ route('formtemplate.editapprover', $formTemplate->id) }}" class="ajax">

	{{ csrf_field() }}

	<div class="box-body">
		<div class="col-sm-12">

			<div class="row">
				<div class="form-group col-sm-12 no-margin-b">
	            	<label>Approval Flow</label>
				</div>
			</div>		
			<div class="row">
	            <div class="form-group col-sm-3">
					<div class="radio no-margin-t no-margin-b">
						<label>
							<input {{ $formTemplate->approval_option == 0 ? 'checked' : '' }}
							type="radio" name="approval_option" value="0">
							In Order
						</label>
					</div>
				</div>
				<div class="form-group col-sm-3">
					<div class="radio no-margin-t no-margin-b">
						<label>
							<input {{ $formTemplate->approval_option == 1 ? 'checked' : '' }}
							type="radio" name="approval_option" value="1">
							Simultaneously
						</label>
					</div>
				</div>
			</div>

			<workflow ref="formtemplateapprovers"
			:id="{{ $formTemplate->id }}"
			:employees="{{ $employees }}"
			:types="{{ json_encode(App\FormTemplateApprover::getTypes()) }}"
			:companies="{{ json_encode(App\FormTemplateApprover::getCompanies()) }}"
			:levels="{{ json_encode(App\FormTemplateApprover::getLevels()) }}"
			:fetchurl="'{{ route('formtemplate.fetchapprovers', $formTemplate->id) }}'"
			:addurl="'{{ route('formtemplate.addapprover', $formTemplate->id) }}'"
			:removeurl="'{{ route('formtemplate.removeapprover', $formTemplate->id) }}'">

				<template slot="header">Form Approvers</template>
				<p slot="body">{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>

			</workflow>			

		</div>
	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		<button type="submit" class="btn btn-primary s-margin-r">Update</button>
	</div>
	<!-- /.box-footer -->

</form>