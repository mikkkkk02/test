@extends('master')

@section('pageTitle', 'Rooms')

@section('breadcrumb')

	<div class="content-header">
		<div class="h1"></div>
		{{-- <h1>Rooms<small>This is the Locations page</small></h1> --}}
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('rooms') }}"><i class="fa fa-cube"></i> Rooms</a>
	        </li>
	    </ol>
	</div>

@endsection

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-12 l-margin-b">

					<div class="pull-right">
						
						<a href="{{ route('room.create') }}" class="btn btn-primary s-margin-r">
							<i class="fa fa-plus s-margin-r"></i>Add Room
						</a>

					</div>

				</div>
			</div>

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#rooms" data-toggle="tab">
							<img src="/image/tabs/stacks.png" class="tab--icon">
							All
						</a>
					</li>
					<li>
						<a @click="onShow('roomsarchive')"
						href="#rooms-archive" data-toggle="tab">
							<i class="fa fa-archive s-margin-r"></i>
							Archive
						</a>
					</li>					
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="rooms">
						<rooms ref="rooms"
						:fetchurl="'{{ route('room.fetch') }}'"
						:autofetch="true">
						</rooms>
					</div>
					<div class="tab-pane" id="rooms-archive">
						<rooms ref="roomsarchive"
						:fetchurl="'{{ route('room.fetcharchive') }}'">
						</rooms>
					</div>
					<!-- /.tab-pane -->					
				</div>
				<!-- /.tab-content -->
			</div>			

		</div>
	</div>	

@endsection