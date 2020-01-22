<template>

	<div>
		<div class="row">
			<div class="form-group col-sm-12 no-margin-b">
				<label>Training Investments</label>
			</div>
		</div>
		<div class="row">

			<currencyinput
			:name="'course_cost'"
			:label="'Course Cost'"
			:editable="iseditable"
			v-model="costs.course"
			></currencyinput>

		</div>
		<div class="row">

			<currencyinput
			:name="'accommodation_cost'"
			:label="'Accommodation Cost'"
			:editable="iseditable"
			v-model="costs.accommodation"
			></currencyinput>

		</div>
		<div class="row">

			<currencyinput
			:name="'meal_cost'"
			:label="'Meal Cost'"
			:editable="iseditable"
			v-model="costs.meal"
			></currencyinput>

		</div>
		<div class="row">

			<currencyinput
			:name="'transport_cost'"
			:label="'Transport Cost'"
			:editable="iseditable"
			v-model="costs.transport"
			></currencyinput>

		</div>
		<div class="row">

			<currencyinput
			:name="'others_cost'"
			:label="'Other Cost'"
			:editable="iseditable"
			v-model="costs.others"
			></currencyinput>

		</div>

		<div class="row">
			<div class="form-group col-sm-6">
				<label>Total Cost</label>
				<input placeholder="Total Cost" :value="totalCost" 
				type="text" class="form-control" disabled>
			</div>
		</div>
	</div>	

</template>
<script>

	import currencyinput from '../CurrencyInput.vue';	

	export default {

		props: [
			'form',
			'iseditable',
		],

		data: function() {
			return {
				costs: {
					course: 0.00,
					accommodation: 0.00,
					meal: 0.00,
					transport: 0.00,
					others: 0.00,
				},
			};
		},

		components: {
			currencyinput,	
		},		

		computed: {
			totalCost: function() {
				let total = this.costs.course + this.costs.accommodation + this.costs.meal + this.costs.transport + this.costs.others;

				/* Format total cost to PH currency */
                return 'P ' + parseFloat(total).toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,");
			},
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Set default variables */
				this.costs.course = this.form ? parseFloat(this.form.course_cost) : 0.00;
				this.costs.accommodation = this.form ? parseFloat(this.form.accommodation_cost) : 0.00;
				this.costs.meal = this.form ? parseFloat(this.form.meal_cost) : 0.00;
				this.costs.transport = this.form ? parseFloat(this.form.transport_cost) : 0.00;
				this.costs.others = this.form ? parseFloat(this.form.others_cost) : 0.00;
			},
		},
	}
</script>