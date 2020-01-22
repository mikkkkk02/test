<form id="securityForm" method="post" action="{{ route('user.edit', $employee->id) }}" 
class="ajax">

	{{ csrf_field() }}

	<div>
		<div class="box-header col-sm-6">
			<i class="fa fa-lock"></i>
			<h3 class="box-title">Security</h3>
		</div>
		<div class="box-header col-sm-6">
			<i class="fa fa-key"></i>
			<h3 class="box-title">Permissions</h3>
		</div>		
	</div>
	<!-- /.box-header -->

	<div class="box-body">

		<div class="col-sm-6 no-padding-l">
			<div class="form-group">
				<label>Email</label>
				<input type="text" name="email" placeholder="Email Address" class="form-control">
			</div>
			<div class="form-group">
				<label>Repeat Email Address</label>
				<input type="text" name="email_validation" placeholder="Repeat Email Address" class="form-control">
			</div>

{{-- 			<div class="form-group m-margin-t">
				<label>New Password</label>
				<input type="text" name="password" placeholder="Password" class="form-control">
			</div>
			<div class="form-group">
				<label>Repeat Password</label>
				<input type="text" name="newpassword" placeholder="Password" class="form-control">
			</div>

			<div class="form-group m-margin-t">
				<label>Old Password</label>
				<input type="text" name="password" placeholder="Password" class="form-control">
			</div>
			<div class="form-group">
				<label>New Password</label>
				<input type="text" name="newpassword" placeholder="Password" class="form-control">
			</div>
			<div class="form-group">
				<label>Repeat New Password</label>
				<input type="text" name="newpassword_confirmation" placeholder="Repeat Password" class="form-control">
			</div> --}}

		</div>
		<div class="col-sm-6">
			<div class="form-group">
				<label for="assigneeList" class="control-label">Responsibilities</label>
				<p>{{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --}}</p>
				<select multiple style="width: 100%;"
				class="form-control select2" name="groups[]" data-placeholder="Select responsibilities...">

					@foreach($groups as $group)

						<option value="{{ $group['id'] }}" {{ in_array($group['id'], $employee->getGroupsID()) ? 'selected' : '' }}>{{ $group['name'] }}</option>

					@endforeach

                </select>
			</div>
		</div>

	</div>
	<!-- /.box-body -->

	<div class="box-footer">
		<div class="pull-right">
			
			<button type="submit" id="securityFormBtn" class="btn btn-primary s-margin-r">Update</button>
			
		</div>
	</div>
	<!-- /.box-footer -->					

</form>
