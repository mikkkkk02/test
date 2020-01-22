<div class="col-sm-4">
	<a href="{{ route('employee.show', $self->id) }}">
		<div class="box-widget widget-user-2">
			<div class="widget-user-header">
				<div class="widget-user-image">
					<img class="img-circle border" src="{{ $self->getProfilePhoto() }}" alt="User Avatar">
				</div>
				<!-- /.widget-user-image -->
				<h3 class="widget-user-username">{{ $self->renderFullname2() }}</h3>
				<h5 class="widget-user-desc">ID: {{ $self->id }}</h5>
			</div>
		</div>
	</a>
</div>
