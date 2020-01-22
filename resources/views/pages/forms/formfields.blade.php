@if(!isset($noBoxInfo))
<div class="box box-info">
	{{-- <div class="box-header">
		<h3 class="box-title">Form Details</h3>
	</div> --}}
	
	@isset ($header)
		<div class="box-header with-border">
			<h3 class="box-title">{{ $header }}</h3>
		</div>
	@endisset

	
	<div class="box-body">

@endif

		@if ($formTemplate->isMeetingRoom())

			<input type="hidden" name="form_template_id" value="{{ $formTemplate->id }}">

			<div class="relative">

				<div class="row">
					<div class="form-group col-sm-12">
						<label>Title</label>
						<input value="{{ isset($mrReservation) ? $mrReservation->name : '' }}" required 
						type="text" name="name" placeholder="Title" class="form-control">
					</div>
				</div>

				<div class="row">
					<mrdetails
						:loadreservation="{{ isset($mrReservation) ? 1 : 0 }}"
						:fetchurl="'{{ route('location.fetchnopagination') }}'"
						:mrreservation="{{ isset($mrReservation) ? $mrReservation : 0 }}"
						:cansetroom="'{{ $isUserTechnician || $self->isSuperUser() }}'"
					></mrdetails>
				</div>
				
			</div>

		@endif
		
		@if($formTemplate->isTravelOrder())	
		<div class="row">
			<div class="form-group col-sm-12 no-margin-b">
	        	<label>Travel Location <span class="has-error">*</span></label>
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
		{{-- @elseif($formTemplate->isMeetingRoom())

		<meetingroomdetails
		:disabled="{{ isset($resubmit) ? 0 : (isset($form) && !$form->isEditable()) + 0 }}"			
		:form="{{ isset($form) ? $form : 0 }}"
		:checkscheduleurl="'{{ route('meetingroom.checkavailability') }}'"
		></meetingroomdetails> --}}

		@endif

		<formdetails
		@if(isset($formDetailsId))
		:id="'{{ $formDetailsId }}'"
		@endif
		:disabled="{{ isset($resubmit) ? 0 : (isset($form) && !$form->isEditable()) + 0 }}"
		:answers="{{ isset($form) ? $answers : '[]' }}"
		:fields="{{ $formTemplate->fields }}"
		></formdetails>

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

@if(!isset($noBoxInfo))
	</div>
</div>
@endif
<!-- /.box-body -->

@if($formTemplate->enableAttachment && !isset($resubmit))
<attachments
:id="'request'"
:header="'Request Attachments'"
@if(isset($form))
:fetchurl="'{{ route('request.fetchattachments', $form->id) }}'"
:addattachmenturl="'{{ route('request.addattachment', $form->id) }}'"
@else
:addattachmenturl="'{{ route('temp.addattachment') }}'"	
@endif
></attachments>
@endif

@if(isset($event))
<input type="hidden" name="event_id" value="{{ $event->id }}" required>
@endif

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