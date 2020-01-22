<div id="view-selectformtemplate" class="modal fade" tabindex="-1">
    <div class="modal-dialog width--80">

		<selectrequest
		:id="'view-selectformtemplate'"
		:createurl="'{{ route('request.create') }}'"
		:createurl2="'{{ route('request.create.step2') }}'"
		:submiturl1="'{{ route('request.validate.step1') }}'"
		:submiturl2="'{{ route('request.store') }}'"
		:fetchurl="'{{ route('formtemplate.fetchforms') }}'"
		:templateid="'{{ isset($templateId) ? $templateId : '' }}'"
		></selectrequest>

    </div>
    <!-- /.modal-dialog -->
</div>