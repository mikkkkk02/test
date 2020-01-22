<!-- Header -->
<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="logo">
		<!-- mini logo for sidebar mini 50x50 pixels -->
		<span class="logo-mini">@include('includes.const.short-title')</span>
		<!-- logo for regular state and mobile devices -->    
        <span class="logo-lg">@include('includes.const.title')</span>
    </a>

	<nav class="navbar navbar-static-top bg__gradiant" role="navigation">
		<!-- Sidebar toggle button-->
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
			<span class="sr-only">Toggle navigation</span>
		</a>
		<!-- Navbar Right Menu -->
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">

				<li class="dropdown user user-menu">
					<a href="{{ route('employee.show', $self->id) }}">
						<img class="user-image" src="{{ $self->getProfilePhoto() }}">
					</a>
				</li>

				<li>
					<div class="line"></div>
				</li>

				<li>
					<a id="gapiLogoutBtn" href="{{ route('logout') }}">Logout</a>
				</li>
				
			</ul>
		</div>
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">

				<li class="dropdown messages-menu">
					<a href="{{ route('notifications') }}">
						<i class="fa fa-bell-o"></i>
						
						@if($notifCount)
						<span class="label label-danger">{{ $notifCount }}</span>
						@endif
					</a>
				</li>

			</ul>
		</div>
	</nav>

</header>