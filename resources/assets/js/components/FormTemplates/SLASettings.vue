<template>

	<div>

		<div class="row">
			<div class="form-group col-sm-4">
				<label>SLA</label>
				<input required v-model="sla"
				type="number" name="sla" placeholder="SLA Days" class="form-control">			
			</div>
			<div class="form-group col-sm-4">
				<label>SLA Option</label>
				<select required v-model="sla_option" @change="check()"
				name="sla_option" class="form-control">
					<option value="0">Start upon approval of the form</option>
					<option value="1" :disabled="!fields.length">Base on any date field on the form</option>
				</select>
			</div>
			<div class="form-group col-sm-4">
				<label>Date Field (If SLA Option is #2)</label>
				<select v-model="sla_date_id" :required="sla_option == 1" :disabled="sla_option == 0 || !fields.length"
				name="sla_date_id" class="form-control">
					
					<option value="0" selected disabled>{{ fields.length == 0 ? 'No available date fields' : 'Select datefield...' }}</option>
						
					<template v-for="(field, index) in fields">

						<option :value="field.id" :selected="index == 1">{{ field.label }}</option>
						
					</template>

				</select>
			</div>
		</div>
		
		<template v-if="field && field.options.length">

			<div class="row">
				<div class="col-sm-4"></div>
				<div class="form-group col-sm-4">
					<label>Select Table Column</label>
					<select v-model="sla_col_id" :required="sla_type == 1" :disabled="sla_type == 0 && !field"
					name="sla_col_id" class="form-control">
						
						<option value="0" selected disabled>{{ fields.length == 0 ? 'No available column fields' : 'Select column...' }}</option>
						
						<template v-for="(option, index) in field.options">

							<option :value="option.id" :selected="index == 1">{{ option.label }}</option>
							
						</template>

					</select>
				</div>
				<div class="form-group col-sm-4">
					<label>Select Table Row</label>
					<select v-model="sla_row_id" :required="sla_type == 1" :disabled="sla_type == 0 && !field"
					name="sla_row_id" class="form-control">
						
						<option value="0" selected>First row</option>
						<option value="1">Last row</option>

					</select>
				</div>

			</div>
		</template>



		<input type="hidden" :value="sla_type" name="sla_type">

	</div>	

</template>
<script>
	export default {

		props: [
			'sladay',
			'slaoption',
			'slatype',
			'sladate',
			'slacol',
			'slarow',
			'fetchurl',
		],

		data: function() {
			return {
				loading: false,

				fields: [],
				field: null,

				tables: [],

				sla: 3,
				sla_option: 0,
				sla_type: 1,
				sla_date_id: 0,
				sla_col_id: 0,
				sla_row_id: 0,
			};
		},

		computed: {},

		watch: {
			sla_date_id: function(newSLA, oldSLA) {
				let field = this.getFieldByID(newSLA);

				/* Update current field */
				if(field) {
					this.field = field;

					this.sla_type = this.field.type;
				}
			},
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				this.fetch();
			},

			setVars: function() {
				/* Set default variables */
				this.sla = this.sladay ? this.sladay : 3;
				this.sla_option = this.slaoption ? this.slaoption : 0;
				this.sla_type = this.slatype ? this.slatype : 0;
				this.sla_date_id = this.sladate ? this.sladate : 0;
				this.sla_col_id = this.slacol ? this.slacol : 0;
				this.sla_row_id = this.slarow ? this.slarow : 0;
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

							/* Set needed vars */
							this.setVars();

							this.loading = false;
						}
					});
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/				
			check: function() {

				if(this.sla_option && !this.fields.length)
					this.sla_option = 0;
			},

			getFieldByID: function(id) {
				return this.fields.filter(function(obj) {
					return obj.id == id;
				})[0];
			},			
		},
	}
</script>