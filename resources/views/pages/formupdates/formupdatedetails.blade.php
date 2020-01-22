<form id="formDetailsForm" method="post" data-redirect="true" class="ajax" 
	@if(!isset($resubmit) && isset($form))
	action="{{ route('request.edit', [$formTemplate->id, $form->id]) }}"
	@else
	action="{{ route('request.store', $formTemplate->id) }}"
	@endif
>

	{{ csrf_field() }}
	
	@include('pages.forms.formempdetails')

	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#showformupdatedetails-new" data-toggle="tab">Updated Details</a>
			</li>
			{{-- <li>
				<a href="#showformupdatedetails-old" data-toggle="tab">Old</a>
			</li> --}}
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="showformupdatedetails-new">

				@include('pages.forms.formfields', [
							'formDetailsId' => 'oldForm-details', 
							'form' => $tempForm,
							'answers' => $newAnswers
						])

			</div>
			{{-- <div class="tab-pane" id="showformupdatedetails-old">	

				@include('pages.forms.formfields', [
							'formDetailsId' => 'newForm-details', 
							'form' => $form,
							'answers' => $oldAnswers
						])

			</div> --}}			
		</div>

	</div>

</form>