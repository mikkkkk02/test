<template>

	<div class="row">

		<template v-for="(d, index) in data">

			<div class="col-sm-3">
				<div class="small-box">
					<div class="inner">
						<p class="color-darkblue upcase bold">{{ d.label }}</p>

						<p>{{ d.percent }}</p>
					</div>
					<div class="icon">
						<img class="m-margin-b chart-image" :src="d.image">
					</div>
				</div>
			</div>
	
			<!-- <div class="col-sm-3">
				<div class="info-box" :class="[ d.color ]">
					<span class="info-box-icon">
						
						<i :class="{
							'fa-clipboard': isSame('total requests', d),
							'fa-refresh': isSame('ongoing', d),
							'fa-check-circle': isSame('on-time', d),
							'fa-exclamation-triangle': isSame('delayed', d),
						}" class="fa"></i>
					</span>

					<div class="info-box-content">
						<span class="info-box-text">{{ d.label }}</span>
						<span class="info-box-number">{{ d.percent }}</span>
						
						<div class="progress">
							<div class="progress-bar" :style="{ width: index == 0 ? '100%' : d.percent }"></div>
						</div>
						<span v-if="d.value"
						class="progress-description">{{ d.value }}</span>
					</div>
				</div>
			</div> -->
			<!-- ./col -->

		</template>

	</div>	

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
				loading: false,
				data: [],
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {},

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

							$this.show();


							/* Broadcast to parent that the fetching is done */
			            	$this.$emit('has-loaded', { element: 'percent' });
						}
					});
			},


            /*
            |-----------------------------------------------
            | @Controllers
            |-----------------------------------------------
            */
            show: function() {
            	this.loading = false;
            },


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/
			isSame: function(string, field) {

				/* Check field type */
				if(field.label && field.label.toLowerCase() == string.toLowerCase())
					return true;

				return false;
			},	
		},
	}
</script>