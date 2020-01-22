<form id="rolesUserForm" method="post" action="{{ route('group.editroles', $group->id) }}" 
class="ajax">

	{{ csrf_field() }}

	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">

			@foreach($categories as $key => $category)

				<li class="{{ !$key ? 'active' : '' }}">
					<a href="#role-{{ $category->id }}" data-toggle="tab">{{ $category->name }}</a>
				</li>

			@endforeach

		</ul>
		<div class="tab-content no-padding">

			@foreach($categories as $key => $category)

				<div class="tab-pane {{ !$key ? 'active' : '' }}" id="role-{{ $category->id }}">

					@foreach($category->roles as $role)
						
						<div class="box-body">
			
							<div class="row">			
								<div class="checkbox form-group col-sm-9 no-margin-b">
									<h5 class="control-label bold">{{ $role->name }}</h5>
									<p>{{ $role->description }}</p>
					                <label>
										<input name="roles[]" value="{{ $role->id }}" {{ $group->hasRole($role->name) ? 'checked' : '' }}
										type="checkbox"> Enable
					                </labe>
								</div>
							</div>																

						</div>
						<!-- /.box-body -->

					@endforeach

				</div>

			@endforeach

			<div class="box-footer">
				<button type="submit" class="btn btn-primary s-margin-r">Update</button>
				<button type="submit" class="btn btn-default">Back</button>
			</div>
			<!-- /.box-footer -->

		</div>
	</div>

</form>