<template>

	<div class="box no-border">
		<div class="box-header">
			<h3 class="box-title">Tickets Overview</h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">

			<transition name="fade">

		        <div v-if="loading"
				class="overlay">
					<i class="fa fa-refresh fa-spin"></i>
		        </div>

		    </transition>

			<div class="chart">
				<canvas id="barChart" style="height: 215px"></canvas>
			</div>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->		

</template>
<script>
	export default {

		props: [
			'generateurl',
			'startdate',
			'enddate',
		],

		data: function() {
			return {
				initialLoad: true,
				loading: false,
				options: {},

				data: {
					labels: [],
					datasets: [],
				},
				canvas: null,
				bar: null,
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				this.setOptions();
				this.setVariables();
			},

			initBar: function() {

				/* Clear chart */
				if(!this.initialLoad) {

					/* Re-initialized canvas */
					this.bar.destroy();
					this.setVariables();
				}

				/* Initialize bar */
				this.bar = this.bar.Line(this.data, this.options);
			},


            /*
            |-----------------------------------------------
            | @Controllers
            |-----------------------------------------------
            */
            show: function() {
            	this.loading = false;
            	this.initialLoad = false;
            },


            /*
            |-----------------------------------------------
            | @Methods
            |-----------------------------------------------
            */
			fetch: function() {
				var $this = this;

				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch the groups */
				axios.post(this.generateurl, { startDate: this.startdate, endDate: this.enddate })
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;

							/* Update pagination & data */
							$this.data.labels = data.labels;
							$this.data.datasets[0]['data'] = data.total;
							$this.data.datasets[1]['data'] = data.within;

							$this.initBar();
							$this.show();


							/* Broadcast to parent that the fetching is done */
			            	$this.$emit('has-loaded', { element: 'barchart' });
						}
					});
			},


            /*
            |-----------------------------------------------
            | @Methods
            |-----------------------------------------------
            */
			setVariables: function() {
				this.canvas = $("#barChart").get(0).getContext("2d");
				this.bar = new Chart(this.canvas);
			},

			setOptions: function() {
				this.options = {
					//Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
					scaleBeginAtZero: true,
					//Boolean - Whether grid lines are shown across the chart
					scaleShowGridLines: true,
					//String - Colour of the grid lines
					scaleGridLineColor: "rgba(0,0,0,.05)",
					//Number - Width of the grid lines
					scaleGridLineWidth: 1,
					//Boolean - Whether to show horizontal lines (except X axis)
					scaleShowHorizontalLines: true,
					//Boolean - Whether to show vertical lines (except Y axis)
					scaleShowVerticalLines: true,
					//Boolean - If there is a stroke on each bar
					datasetFill: true,
					//String - A legend template
					legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
					//Boolean - whether to make the chart responsive
					responsive: true,
					maintainAspectRatio: true
			    };

			    this.options.datasetFill = false;


	            /*
	            | @Set "Total Requests" options
	            |-----------------------------------------------*/
	            this.data.datasets[0] = {
					label: "Total Requests",
					fillColor: "rgba(210, 214, 222, 1)",
					strokeColor: "rgba(210, 214, 222, 1)",
					pointColor: "rgba(210, 214, 222, 1)",
					pointStrokeColor: "#c1c7d1",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(220,220,220,1)",
					data: null,
	            };

	            /*
	            | @Set "Within SLA" options
	            |-----------------------------------------------*/
	            this.data.datasets[1] = {
					label: "Within SLA",
					fillColor: "rgba(60,141,188,0.9)",
					strokeColor: "rgba(60,141,188,0.8)",
					pointColor: "#3b8bba",
					pointStrokeColor: "rgba(60,141,188,1)",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(60,141,188,1)",
					data: null,
	            };	            
			},
		},
	}
</script>