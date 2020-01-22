<div id="calendar"></div>

<script type="text/javascript">

    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();

    $(@yield('calendarname')).fullCalendar({
    	height: 500,
		header: {
			left: 'prev, next, today',
			center: 'title',
			right: 'month, agendaWeek, agendaDay'
		},
		buttonText: {
			today: 'today',
			month: 'month',
			week: 'week',
			day: 'day'
		},
		events: [
			@yield('calendarevents')
		],
	});

</script>