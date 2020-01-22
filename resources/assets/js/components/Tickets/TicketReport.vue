<template>

	<div>
		
		<!-- Date and time range -->
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<div class="input-group">
						<button :disabled="isLoading"
						type="button" class="btn btn-default" id="daterange-btn">
							<i class="fa fa-calendar"></i>
							<span>{{ startDisplay && endDisplay ? startDisplay + ' - ' + endDisplay : ' Select date here...' }}</span>
							<i class="fa fa-caret-down"></i>
						</button>
						<button @click="refresh()" :disabled="isLoading"
						type="button" class="btn btn-default s-margin-l">
							<i class="fa fa-refresh"></i>
						</button>			
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				
				<ticketpercent ref="percent"
				:generateurl="generatepercent"
				:startdate="startdate"
				:enddate="enddate"
				@has-loaded="hasLoaded"></ticketpercent>

			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">

				<ticketpiechart ref="piechart"
				:generateurl="generatepiechart"
				:startdate="startdate"
				:enddate="enddate"
				@has-loaded="hasLoaded"></ticketpiechart>

			</div>
			<div class="col-sm-6">

				<ticketbarchart ref="barchart"
				:generateurl="generatebarchart"
				:startdate="startdate"
				:enddate="enddate"
				@has-loaded="hasLoaded"></ticketbarchart>

			</div>		
		</div>

	</div>		

</template>
<script>

	import ticketpercent from './TicketPercent.vue';
	import ticketbarchart from './TicketBarChart.vue';
	import ticketpiechart from './TicketPieChart.vue';

	export default {

		props: [
			'generatepercent',
			'generatepiechart',
			'generatebarchart',
		],

		data: function() {
			return {
				loading: {
					percent: false,
					piechart: false,
					barchart: false,
				},

				startdate: null,
				enddate: null,

				startDisplay: null,
				endDisplay: null,				
			};
		},

		components: {
			ticketpercent,
			ticketbarchart,
			ticketpiechart,
		},

		computed: {
			isLoading: function() {
				return this.loading.percent && this.loading.piechart && this.loading.barchart;
			},
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Initialize date range */
				this.initDatePicker();
			},

			initDatePicker: function() {
				var daterange = $('#daterange-btn'),
					startDate = moment().startOf('year'),
					endDate = moment().endOf('year'),
					$this = this;

				daterange.daterangepicker({
					locale: {
				      format: 'YYYY-MM-DD'
				    },
					startDate: startDate,
					endDate: endDate,
					opens: 'right',
					ranges: {
						'This Year': [startDate, endDate],
						'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
				}, function(start, end) {

					/* Update date */
					$this.setDate(start, end);
				});

				/* Set default value */
				this.setDate(startDate, endDate);
			},


            /*
            |-----------------------------------------------
            | @Controllers
            |-----------------------------------------------
            */
           	setDate: function(startDate, endDate) {

				/* Check if still fetching */
				if(this.isLoading)
					return false;


				this.startdate = startDate.format('YYYY-MM-DD');
				this.enddate = endDate.format('YYYY-MM-DD');

				this.startDisplay = startDate.format('MMMM D, YYYY');
				this.endDisplay = endDate.format('MMMM D, YYYY');

				/* Refresh reports */
				this.$nextTick(function() {
					this.refresh();
				});
           	},

           	refresh: function() {

           		/* Toggle all loading */
           		this.toggleLoading(true);

           		this.$refs.percent.fetch();
           		this.$refs.piechart.fetch();
           		this.$refs.barchart.fetch();
           	},

           	hasLoaded: function(data) {
           		switch(data.element) {
           			case 'percent': this.loading.percent = false; break;
           			case 'piechart': this.loading.piechart = false; break;
           			case 'barchart': this.loading.barchart = false; break;
           		}

           		/* Check if all fetching is done */
           		this.checkLoading();	
           	},

           	checkLoading: function() {
           		
           		/* Check computed property for all loading */
           		if(this.isLoading)
           			return false;

           		this.toggleLoading(false);
           	},


            /*
            |-----------------------------------------------
            | @Helpers
            |-----------------------------------------------
            */        
           	toggleLoading: function(boolean) {
           		this.loading.percent = boolean;
           		this.loading.piechart = boolean;
           		this.loading.barchart = boolean;
           	},	
		},
	}
</script>