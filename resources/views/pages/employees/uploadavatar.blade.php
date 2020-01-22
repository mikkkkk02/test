<form action="{{ route('employee.uploadavatar', $employee->id) }}" method="POST" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="col-sm-12">
		@if ($errors->any())
		    <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif
	</div>
	<div class="col-sm-6 s-margin-b col-with-input-group">
		<label class="upcase">Avatar</label>
		<div class="input-group">
			<input type="file" name="profile_photo" class="form-control">
			<div class="input-group-btn">
				<button type="submit" class="btn btn-primary">Upload</button>
			</div>
		</div>
	</div>
</form>