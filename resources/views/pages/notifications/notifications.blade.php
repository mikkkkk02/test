@extends('master')

@section('pageTitle', 'Notifications')

@section('breadcrumb')

	<div class="content-header">
		<h1>Notifications<small>{{ $self->renderFullname() }}</small></h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('dashboard') }}"><i class="fa fa-bell"></i> Dashboard</a>
	        </li>
	        <li>
	        	<a href="{{ route('notifications') }}">Notifications</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<notifications
			:readallurl="'{{ route('notification.read') }}'"
			:fetchurl="'{{ route('notification.fetch') }}'"
			></notifications>

		</div>
	</div>			

@endsection
