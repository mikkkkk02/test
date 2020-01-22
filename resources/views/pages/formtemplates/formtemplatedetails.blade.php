<form id="formTemplateDetailsForm" method="post" class="ajax"
	@if(isset($formTemplate))
	action="{{ route('formtemplate.edit', $formTemplate->id) }}"
	@else
	action="{{ route('formtemplate.store') }}" data-redirect="true"
	@endif
>

	{{ csrf_field() }}
	
	<div class="box-body">
		<div class="col-sm-12">

			<div class="row">
				<div class="form-group col-sm-12">
					<label for="assigneeList" class="control-label">Form Template</label>
					<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-sm-4">
					<label>Name <span class="has-error">*</span></label>
					<input value="{{ isset($formTemplate) ? $formTemplate->name : '' }}" required 
					type="text" name="name" placeholder="Name" class="form-control">					
				</div>
				<div class="form-group col-sm-4">
					<label>Category</label>
					<select required
					name="form_template_category_id" class="form-control">

						@foreach($formCategories as $category)
					
							<option value="{{ $category->id }}" 
								@if(isset($formTemplate))
								{{ $formTemplate->form_template_category_id == $category->id ? 'selected' : '' }}
								@endif
							>
							{{ $category->name }}
							</option>

						@endforeach

					</select>
				</div>
				<div class="form-group col-sm-4">
					<label>Request Type</label>
					<select required
					name="request_type" class="form-control">

						@foreach($requestTypes as $type)
					
							<option value="{{ $type['value'] }}" 
								@if(isset($formTemplate))
								{{ $formTemplate->request_type == $type['value'] ? 'selected' : '' }}
								@endif
							>
							{{ $type['label'] }}
							</option>

						@endforeach

					</select>
				</div>				
			</div>
			
			<div class="row">
				<div class="form-group col-sm-6">
					<label>Type</label>
					<select required
					name="type" class="form-control">

						@foreach($formTemplateTypes as $type)
					
							<option value="{{ $type['value'] }}" 
								@if(isset($formTemplate))
								{{ $formTemplate->type == $type['value'] ? 'selected' : '' }}
								@endif
							>
							{{ $type['label'] }}
							</option>

						@endforeach

					</select>
				</div>				
				<div class="form-group col-sm-6">
					<label>Priority <span class="has-error">*</span></label>
					<select required 
					name="priority" class="form-control">

						@foreach($priorities as $priority)

							<option value="{{ $priority['value'] }}"
								@if(isset($formTemplate))
								{{ $formTemplate->priority == $priority['value'] ? 'selected' : '' }}
								@endif
							>{{ $priority['label'] }}</option>

						@endforeach 

					</select>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-sm-12">
					<label>Description</label>
					<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($formTemplate) ? $formTemplate->description : '' }}</textarea>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-sm-12">
					<label>Service Level Agreement</label>
					<textarea type="text" name="sla_text" placeholder="Service Level Agreement" class="form-control trumbowyg">{{ isset($formTemplate) ? $formTemplate->sla_text : '' }}</textarea>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-sm-12">
					<label>Policy & Procedures</label>
					<textarea type="text" name="policy" placeholder="Policy & Procedures" class="form-control trumbowyg">{{ isset($formTemplate) ? $formTemplate->policy : '' }}</textarea>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-xs-12 no-margin-b">
					<label for="assigneeList" class="control-label">Managerial Settings</label>
					<p>Will check each approver and when its on a manager level (Job Level on the Employee details) don’t add in the next approvers</p>
				</div>
			</div>
			<div class="row">
	            <div class="form-group col-xs-3">
					<div class="checkbox no-margin-t no-margin-b">
						<label>
							<input {{ isset($formTemplate) && $formTemplate->enableManagerial ? 'checked' : '' }}
							type="checkbox" name="enableManagerial" value="1">Enable Managerial Settings
						</label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-xs-12 no-margin-b">
					<label for="assigneeList" class="control-label">Attachment</label>
					<p>Toggle the checkbox below to allow employees to attach files to the form</p>
				</div>
			</div>
			<div class="row">
	            <div class="form-group col-xs-3">
					<div class="checkbox no-margin-t no-margin-b">
						<label>
							<input {{ isset($formTemplate) && $formTemplate->enableAttachment ? 'checked' : '' }}
							type="checkbox" name="enableAttachment" value="1">Enable Attachment
						</label>
					</div>
				</div>
			</div>

			@if(isset($formTemplate))

				<slasettings
				:sladay="'{{ $formTemplate->sla }}'"
				:slaoption="'{{ $formTemplate->sla_option }}'"
				:slatype="'{{ $formTemplate->sla_type }}'"
				:sladate="'{{ $formTemplate->sla_date_id }}'"
				:slacol="'{{ $formTemplate->sla_col_id }}'"
				:slarow="'{{ $formTemplate->sla_row_id }}'"
				:fetchurl="'{{ route('formtemplate.availabledatefields', $formTemplate->id) }}'">
				</slasettings>

				<travelordersettings
				:travelordertableid="'{{ $formTemplate->travel_order_table_id }}'"
				:fetchurl="'{{ route('formtemplate.availabletablefields', $formTemplate->id) }}'">
				</travelordersettings>
			
				<workflow
				:id="{{ $formTemplate->id }}"
				:employees="{{ $employees }}"
				:types="{{ json_encode(App\FormTemplateApprover::getTypes()) }}"
				:companies="{{ json_encode(App\FormTemplateApprover::getCompanies()) }}"
				:levels="{{ json_encode(App\FormTemplateApprover::getLevels()) }}"				
				:autofetch="true"
				:fetchurl="'{{ route('formtemplate.fetchcontacts', $formTemplate->id) }}'"
				:addurl="'{{ route('formtemplate.addcontact', $formTemplate->id) }}'"
				:removeurl="'{{ route('formtemplate.removecontact', $formTemplate->id) }}'">
					
					<template slot="header">Notification</template>
					<p slot="body">Notify the ff. employees when the form has been approved</p>

				</workflow>

			@endif

		</div>
	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		<button type="submit" class="btn btn-primary s-margin-r">Update</button>
	</div>
	<!-- /.box-footer -->

</form>

@section('styles')

<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.5.1/ui/trumbowyg.min.css">

@endsection

@section('js')

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.5.1/trumbowyg.min.js"></script>

@endsection