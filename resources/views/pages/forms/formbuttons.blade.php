<div class="box-footer">

	<div class="pull-right">
		@if(!isset($disableForm))
			@if(!isset($resubmit) && isset($form))
				@if($form->isEditable() || $form->isClosed())
					@if (!$form->isDraft() && !$form->status == \App\Form::APPROVED)
						<input type="submit" name="save" value="Update" class="btn btn-primary s-margin-r"></input>
					@endif
				@endif
				@if ($form->isDraft())
					<input type="submit" name="save" value="Submit" class="btn btn-primary s-margin-r"></input>
					<input type="submit" name="draft" value="Save as Draft" class="btn btn-info s-margin-r"></input>
				@endif
			@else
				<input type="submit" name="save" value="Submit" class="btn btn-primary s-margin-r"></input>
				<input type="submit" name="draft" value="Save as Draft" class="btn btn-info s-margin-r"></input>
			@endif
		@else
			@if(isset($form) && $form->canUpdateDetails())
				<input type="submit" name="save" value="Update" class="btn btn-primary s-margin-r"></input>	
			@endif
		@endif

	</div>

</div>
<!-- /.box-footer -->