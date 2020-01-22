<template>

	<div>

		<div class="row">
			<template v-if="fields.length > 0">
				<div class="form-group col-sm-12">
					<label>Travel Order Table</label>
					<p>Table that will be updated by the Technician of the Ticket correspanding with the Travel Order details</p>
					<select v-model="travel_order_table_id" name="travel_order_table_id" class="form-control">
						<option value="0" selected disabled>{{ fields.length == 0 ? 'No available column fields' : 'Select column...' }}</option>

						<template v-for="(field, index) in fields">

							<option :value="field.id" :selected="travel_order_field_id === field.id">{{ field.label }}</option>

						</template>
					</select>
				</div>
			</template>
		</div>

	</div>	

</template>
<script>
	export default {

		props: [
			'travelordertableid',
			'fetchurl',
		],

		data: function() {
			return {
				loading: false,

				fields: [],

				travel_order_table_id: 0,
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				this.fetch();
			},

			setVars: function() {
				/* Set default variables */
				this.travel_order_table_id = this.travelordertableid ? this.travelordertableid : 0;
			},

			fetch: function() {
				var $this = this;

				/* Fetch the available fields */
				axios.post(this.fetchurl, {})
					.then(response => {

						/* Check AJAX result */
						if(response.status == 200) {

							var data = response.data;

							/* Update the item lists */
							this.fields = data.lists;

							console.log(this.fields.length);

							/* Set needed vars */
							this.setVars();

							this.loading = false;
						}
					});
			},
		},
	}
</script>