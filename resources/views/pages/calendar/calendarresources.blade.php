@section('styles')

    <!-- FullCalendar-->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.print.css') }}" media="print">	

	<!-- AdminLTE: Daterange picker -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">

@endsection

@section('js')

    <!-- FullCalendar -->
    <script type="text/javascript" src="{{ asset('adminlte/plugins/fullcalendar/fullcalendar.min.js') }}"></script>

	<!-- AdminLTE: Datepicker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	<!-- AdminLTE: Daterange picker -->
	<script type="text/javascript" src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>    

@endsection