<form id="formDetailsForm" method="post" data-redirect="true" class="ajax" 
	@if(!isset($resubmit) && isset($form))
	action="{{ route('request.edit', [$formTemplate->id, $form->id]) }}"
	@else
	action="{{ route('request.store', $formTemplate->id) }}"
	@endif
>

	{{ csrf_field() }}
	
	@include('pages.forms.formempdetails')

	@include('pages.forms.formfields')

	@include('pages.forms.formbuttons')

</form>