<form id="requestMultiPartForm2" action="#">

	@if($formTemplate->category->forLearning())
	<div class="row">
		<div class="form-group col-sm-12 no-margin-b">
	    	<label>Training Venue <span class="has-error">*</span></label>
		</div>
	</div>		
	<div class="row">
	    <div class="form-group col-sm-3">
			<div class="radio no-margin-t no-margin-b">
				<label>
					<input
					type="radio" name="isLocal" value="1" {{ isset($form) && $form->isLocal ? 'checked' : '' }}
					@if(!isset($form))
					checked
					@endif
					@if(isset($form) ? !$form->isEditable() : 0)
					disabled
					@endif
					>
					Local
				</label>
			</div>
		</div>
		<div class="form-group col-sm-3">
			<div class="radio no-margin-t no-margin-b">
				<label>
					<input type="radio" name="isLocal" value="0" {{ isset($form) && !$form->isLocal ? 'checked' : '' }}
					@if(isset($form) ? !$form->isEditable() : 0)
					disabled
					@endif
					>
					International
				</label>
			</div>
		</div>
	</div>		

	<ldcost
	:form="{{ isset($form) ? $form : 0 }}"
	:iseditable="{{ isset($form) ? $form->isEditable() + 0 : 1 }}"
	></ldcost>

	@else

	<div class="row">
		<div class="form-group col-sm-12">
			<label>Purpose/Details</label>
			<textarea class="form-control" name="purpose" placeholder="Optional"
			@if(isset($resubmit) ? 0 : isset($form) && !$form->isEditable())
			disabled
			@endif
			>{{ isset($form) ? $form->purpose : '' }}</textarea>
		</div>
	</div>
	@endif

</form>


@section('styles')

    <!-- AdminLTE: Datepicker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datepicker/datepicker3.css') }}">
    <!-- AdminLTE: Timepicker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">

@endsection

@section('js')

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js"></script>
    <!-- AdminLTE: Datepicker -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>	
    <!-- AdminLTE: Timepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

@endsection