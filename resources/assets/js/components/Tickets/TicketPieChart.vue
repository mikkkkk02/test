<template>

	<div class="box no-border">

		<transition name="fade">

	        <div v-if="loading"
			class="overlay">
				<i class="fa fa-refresh fa-spin"></i>
	        </div>

	    </transition>

		<div class="box-header">
			<h3 class="box-title">Tickets Summary</h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="row">
				<div class="col-md-8">

					<div class="chart">
					    <canvas id="pieChart" height="211"></canvas>
						<p class="full-center center-align">Tickets <br> {{ sum() }}</p>
					</div>
					<!-- ./chart -->
				</div>
				<!-- /.col -->
				<div class="col-md-4">
					<ul class="chart-legend clearfix">

						<template v-for="d in data">

							<li class="m-margin-b">
								<i :style="{ 
									'color': d.color 
								}"
								class="fa fa-circle-o"></i> {{ d.label }}
								<span class="pull-right">
									{{ d.value }}
								</span>
							</li>

						</template>

					</ul>
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->
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

				data: [],
				canvas: null,
				chart: null,
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

			initChart: function() {

				/* Clear chart */
				if(!this.initialLoad) {

					/* Re-initialized canvas */
					this.chart.destroy();
					this.setVariables();
				}

				/* Initialize chart */
				this.chart = this.chart.Doughnut(this.data, this.options);
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
							$this.data = data.data;

							$this.initChart();
							$this.show();


							/* Broadcast to parent that the fetching is done */
			            	$this.$emit('has-loaded', { element: 'piechart' });					
						}
					});
			},           

			setVariables: function() {
				this.canvas = $("#pieChart").get(0).getContext("2d");
				this.chart = new Chart(this.canvas);
			},

			setOptions: function() {
				this.options = {
					//Boolean - Whether we should show a stroke on each segment
					segmentShowStroke: true,
					//String - The colour of each segment stroke
					segmentStrokeColor: "#fff",
					//Number - The width of each segment stroke
					segmentStrokeWidth: 1,
					//Number - The percentage of the chart that we cut out of the middle
					percentageInnerCutout: 50, // This is 0 for Pie charts
					//Number - Amount of animation steps
					animationSteps: 100,
					//String - Animation easing effect
					animationEasing: "easeOutBounce",
					//Boolean - Whether we animate the rotation of the Doughnut
					animateRotate: true,
					//Boolean - Whether we animate scaling the Doughnut from the centre
					animateScale: false,
					//Boolean - whether to make the chart responsive to window resizing
					responsive: true,
					// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
					maintainAspectRatio: false,
					//String - A legend template
					legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
					//String - A tooltip template
					tooltipTemplate: "<%=value %> <%=label%> Ticket(s)"
				};
			},

			sum() {
				let sum = 0;
				this.data.forEach(function(obj) {
					sum += obj.value;
				});

				return sum;
			}
		},
	}
</script>