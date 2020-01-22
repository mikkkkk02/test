@extends('master')

@section('pageTitle', 'Create Team')

@section('breadcrumb')

	<div class="content-header">
		<h1>Create Team<small>{{-- This is the create team page --}}</small></h1>
	    <ol class="breadcrumb">	    
	        <li>
	            <a href="{{ route('teams') }}"><i class="fa fa-industry"></i> Teams</a>
	        </li>
	        <li>
	        	<a href="{{ route('team.create') }}">New Team</a>
	        </li>
	    </ol>
	</div>	

@endsection

@section('content')

	<div class="row l-margin-t">
		<div class="col-sm-12">

			<div class="box box-widget nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#showteam-details" data-toggle="tab">Details</a>
					</li>				
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="showteam-details">
						
						@include('pages.teams.teamdetails')					

					</div>
					<!-- /.tab-pane -->				
				</div>
				<!-- /.tab-content -->
				
			</div>			

		</div>
	</div>	

@endsection
