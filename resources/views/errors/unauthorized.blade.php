@extends('master')

@section('pageTitle', '401 Unauthorized Access')

@section('breadcrumb')

	<div class="content-header">
		<h1><label class="text-yellow">401</label> Unauthorized Access</h1>
	    <ol class="breadcrumb">
	        <li>
	            <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a>
	        </li>
	        <li>
	        	<a>Unauthorized Access</a>
	        </li>	        
	    </ol>		
	</div>

@endsection

@section('content')

	<div class="error-page">
		<div class="error-page-border">
			<div class="error-content">
				<h3><i class="fa fa-warning text-yellow"></i> Oops! Permission denied</h3>
				<p>It appears you don't have permission to access the page. Please make sure you're authorized	to view the content. If you think you should be able to view the page. Please use the email provided to notify support of the problem.</p>

				<p>Support email <a href="mailto:hr@snaboitiz.com">hr@snaboitiz.com</a>.</p>
			</div>
			<!-- /.error-content -->
		</div>
	</div>
	<!-- /.error-page -->	

@endsection