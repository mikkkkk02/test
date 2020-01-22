<form id="employeeDetailsForm" method="post" class="ajax"
	@if(isset($event))
	action="{{ route('event.edit', $event->id) }}"
	@else
	action="{{ route('event.store') }}" data-redirect="true"
	@endif
>

	{{ csrf_field() }}

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-3">
				<label>Title <span class="has-error">*</span></label>
				<input value="{{ isset($event) ? $event->title : '' }}" required
				type="text" name="title" placeholder="Title" class="form-control">
			</div>
			<div class="form-group col-sm-3">
				<label>Facilitator</label>
				<input value="{{ isset($event) ? $event->facilitator : '' }}" required
				type="text" name="facilitator" placeholder="Facilitator" class="form-control">
			</div>
			<div class="form-group col-sm-3">
				<label>Venue</label>
				<input value="{{ isset($event) ? $event->venue : '' }}" required 
				type="text" name="venue" placeholder="Venue" class="form-control">
			</div>			
		</div>

		<div class="row">
			<div class="form-group col-sm-3">
                <label>Color:</label>
                <input value="{{ isset($event) ? $event->color : '' }}" required 
                type="text" name="color" class="form-control jscolor">
			</div>
			<div class="form-group col-sm-3">
				<label>Form <span class="has-error">*</span></label>
				<select required
				name="form_template_id" class="form-control">

					<option value="0" selected disabled>Select form...</option>

					@foreach($formTemplates as $template)

						<option value="{{ $template->id }}"
							@if(isset($event))
							{{ $event->form_template_id == $template->id ? 'selected' : '' }}
							@endif
						>
						{{ $template->name }}
						</option>

					@endforeach

				</select>
			</div>
		</div>

		<div class="row">
			{{-- <div class="form-group col-sm-3">
				<label>Category</label>
				<select required
				name="event_category_id" class="form-control">

					<option value="0" selected disabled>Select category...</option>

					@foreach($categories as $category)

						<option value="{{ $category->id }}"
							@if(isset($event))
							{{ $event->event_category_id == $category->id ? 'selected' : '' }}
							@endif
						>
						{{ $category->title }}
						</option>

					@endforeach

				</select>
			</div> --}}
			<div class="form-group col-sm-3">
				<label>Participant Limit</label>
				<input value="{{ isset($event) ? $event->limit : '' }}" required 
				type="number" name="limit" placeholder="Limit" class="form-control">
			</div>
			<div class="form-group col-sm-3">
				<label>No. of Hours</label>
				<input value="{{ isset($event) ? $event->hours : '' }}" required
				type="number" name="hours" placeholder="No. of Hours" class="form-control">
			</div>			
		</div>	

		<datesettings
		:event="{{ isset($event) ? $event : 'null' }}"
		></datesettings>

		<div class="row">
			<div class="form-group col-sm-9">
				<label>Description <span class="has-error">*</span></label>
				<textarea type="text" name="description" placeholder="Description" class="form-control">{{ isset($event) ? $event->description : '' }}</textarea>
			</div>
		</div>

	</div>
	<!-- /.box-body -->

    @if($checker->hasModuleRoles(['Adding/Editing of Events']))
	<div class="box-footer">
	
		<div class="pull-right">
			
			<button type="submit" class="btn btn-primary s-margin-r">
				@if(isset($event))
				Update
				@else
				Submit
				@endif
			</button>
	
		</div>

	</div>
	@endif
	<!-- /.box-footer -->

</form>

@section('styles')

    <!-- AdminLTE: Datepicker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datepicker/datepicker3.css') }}">
    <!-- Flatpickr -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.5.1/flatpickr.min.css"> --}}

@endsection

@section('js')

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js"></script>
    <!-- AdminLTE: Datepicker -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>	
    <!-- Flatpickr -->
	{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.5.1/flatpickr.min.js"></script> --}}

@endsection