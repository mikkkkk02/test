<template>
	
	<div :id="name"></div>

</template>
<script>
	export default {

		props: [
			'name',
			'events',
		],

		data: function() {
			return {
				data: {
					events: [],
				},
			};
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {
				const $this = this;

				/* Set default vars */
				this.data.events = this.events ? this.events : [];


				/* Run init if on autoload */
				this.$nextTick(function() {

					setTimeout(function() {
						$this.initCalendar();
					}, 1000);
					/* Init calendar */
				});
			},

			initCalendar: function() {
			    $('#' + this.name).fullCalendar({
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
					events: this.data.events,
					timeFormat: 'h:mm A'
				});
			},

			render: function() {
				const $this = this;

				setTimeout(function() {
					$('#' + $this.name).fullCalendar('changeView', 'month');
				}, 500);
			},
		},
	}
</script>