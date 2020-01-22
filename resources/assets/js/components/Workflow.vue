<template>

	<div>
		<div class="row">
			<div class="form-group col-sm-12">
				<label for="assigneeList" class="control-label">

					<slot name="header"></slot>

				</label>

				<slot name="body"></slot>
				
			</div>
		</div>

		<div class="row">
			<div class="form-group col-sm-3">
				<select v-model="option" :disabled="loading" @change="refresh()"
				class="form-control">

					<template v-for="type in types">

						<option :value="type['value']">{{ type['label'] }}</option>

					</template>

				</select>
			</div>
			<div class="form-group col-sm-3">
				<select v-model="type" :disabled="loading"
				class="form-control">

					<template v-if="option == 1">
						
						<template v-for="level in levels">

							<option :value="level['value']">{{ level['label'] }}</option>

						</template>

					</template>
					<template v-else-if="option == 2">
						
						<template v-for="(employee, index) in employees">

							<option :value="employee.id" :selected="index == 1">{{ employee.extra.fullname }}</option>
							
						</template>

					</template>
					<template v-else-if="option == 3">
						
						<template v-for="company in companies">

							<option :value="company['value']">{{ company['label'] }}</option>

						</template>

					</template>					

				</select>
			</div>
			<div class="form-group col-sm-3">
				<a @click="add()" :disabled="loading"
				class="btn btn-primary">

					<i :class="{
						'fa-refresh fa-spin' : loading,
						'fa-plus' : !loading,
					}"
					class="fa xs-margin-r"> </i> Add
						
				</a>
			</div>
		</div>

		<template v-for="approver in approvers">

			<div class="row">
				<div class="form-group col-sm-12">
					<div class="col-xs-6 no-padding" style="max-width: 300px">
						<div class="input-group">
							<span class="input-group-addon">@</span>
							<input :value="getLabel(approver)" :disabled="loading"
							type="text" class="form-control" disabled>
						</div>
					</div>
					<div class="col-sm-1">
						<a @click="remove(approver.id)" :disabled="loading"
						class="btn btn-danger btn-xs">
							<span class="fa fa-times"></span>
						</a>
					</div>
				</div>
			</div>

		</template>

	</div>

</template>
<script>
	export default {

		props: [
			'id',
			'employees',
			'levels',
			'companies',
			'types',

			'autofetch',

			'fetchurl',
			'addurl',
			'removeurl',
		],

		data: function() {
			return {
				loading: false,
				option: 1,
				type: 1,

				approvers: [],

				settings: {
					autofetch: false,
					initialfetch: false,
				},				
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				this.settings.autofetch = this.autofetch ? true : false;


				/* Check if auto fetch is enable */
				if(this.settings.autofetch)
					this.fetch();
			},

			fetch: function() {
				var $this = this;

				this.loading = true;

				/* Fetch approvers */
				axios.post(this.fetchurl, {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;

							/* Update the approver lists */
							$this.approvers = data.lists;

							$this.show();
						}
					});				
			},

			updateApprover: function(url, post) {
				var $this = this;

				/* Check for loading state */
				if(this.loading)
					return false;

				this.loading = true;


				/* Add/Remove approver */
				axios.post(url, post)
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;

							/* Update the approver */
							$this.push(data.response, data.data); 

							$this.show();
						}
					});
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/	
			refresh: function() {

				/* Return to first option if no datefield is available */
				if(this.type == undefined || this.type > 1)
					this.type = 1;
			},

			add: function() {
				this.updateApprover(this.addurl, this.getPost());
			},

			remove: function(index) {
				this.updateApprover(this.removeurl, { id: index });
			},

			push: function(response, data = null) {

				/* Check if remove or add */
				switch(response) {
					case 1: this.approvers.push(data); break;
					case 2: this.approvers.splice(this.getApproverByID(data), 1); break;
				}
			},

			show: function() {
				this.loading = false;

				if(!this.settings.initialfetch)
					this.settings.initialfetch = true;
			},			


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/
			getLabel: function(data) {

				switch(data.type) {
					case 1: return data.type_value ? 'Next Level Leader' : 'Immediate Leader';
					case 2:

						if(data.employee) {
							return data.employee.extra.fullname;
                        } else if (data.employee_id) {
                            let emp = this.getEmployeeByID(data.employee_id);

                            if(emp && emp.extra)
                                return emp.extra.fullname;
                        }

                        return 'Employee not found!';

					case 3: return data.type_value ? 'OD' : 'HR';
					default: return '';
				}
			},

			getPost: function() {
				return {
					form_template_id: this.id,
					type: this.option,
					type_value: this.type,
					employee_id: this.option == 2 ? this.type : null,
				}
			},

			getApproverByID: function(id) {
				return this.approvers.map(function(obj) {
					return obj.id; 
				}).indexOf(id);
			},	

			getEmployeeByID: function(id) {
				return this.employees.filter(function(obj) {
					return obj.id == id;
				})[0];
			},

           	isInitialFetch: function() {
           		return this.settings.initialfetch;
           	},							
		},
	}
</script>