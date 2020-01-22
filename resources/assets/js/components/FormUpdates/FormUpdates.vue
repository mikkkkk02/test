<template>

	<datatable ref="datatable"
	:headericon="headericon"
	:header="header"	
	:categories="categories"
	:paginationlimit="paginationlimit"
	:autofetch="autofetch"
	:daterange="daterange"
	:fetchurl="fetchurl"
	@is-loading="setLoading(true)"
	@not-loading="setLoading(false)"
	>

		<thead slot="thead">
			<tr>

				<dataheader
				:name="'id'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Request #</center>

				</dataheader>

				<th>Owner</th>
				<th>Requests</th>
				<!-- <th>Type</th> -->
				<!-- <th>Category</th> -->
				<th><center>Status</center></th>
				<th>Requested By</th>

				<dataheader
				:name="'created_at'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Date Created</center>

				</dataheader>

				<th><center>Actions</center></th>
				
			</tr>
		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>
					<center>
						<a :href="list.extra.view">
							{{ list.form_id }}
						</a>
					</center>
				</td>
				<td>{{ list.employee.extra.fullname }}</td>
				<td>{{ list.template.name }}</td>
				<!-- <td>{{ list.template.extra.type }}</td> -->
				<!-- <td>{{ list.template.category.name }}</td> -->
				<td>
					<center>
						<span data-toggle="tooltip" 
						:class="{
							'bg-orange': isStatus('pending', list),
							'bg-green': isStatus('approved', list),
							'bg-red': isStatus('disapproved', list) || isStatus('cancelled', list),
						}"
						class="badge">{{ list.extra.status }}</span>
					</center>							
				</td>
				<td>
					<template v-if="list.requester">
						{{ list.requester.extra.fullname }}
					</template>
				</td>
				<td>
					<center>
						{{ moment(list.created_at) }}
					</center>
				</td>
				<td>	
					<center>	
						<a :href="list.extra.view" class="btn btn-primary btn-xs">
							<span class="fa fa-eye"></span>
						</a>
					</center>
				</td>
			</tr>

		</template>

	</datatable>

</template>
<script>

	import datatable from '../DataTable.vue';

	export default {

		props: [
			'categories',
			'formstatus',

			'headericon',
			'header',
			'paginationlimit',
			'autofetch',
			'daterange',

			'fetchurl',
		],

		components: {
			datatable,
		},

		data: function() {
			return {	
				loading: false,

				settings: {
					sort: '',
					direction: null,
				}
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Set default variables */
				//
			},


			/*
			|-----------------------------------------------
			| @Methods
			|-----------------------------------------------
			*/
			sort: function (name) { //console.log(name);
				let direction = this.toggleDirection();
				this.settings.sort = name;

				this.$refs.datatable.fetch(null, name, direction);
			},

			toggleDirection: function() {
				this.settings.direction = this.settings.direction ? false : true;

				return this.settings.direction ? 'ASC' : 'DESC';
			},

			setLoading: function(bool) {
				this.loading = bool;
			},


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/
			isFormStatus: function(string, list) {

				if(string == undefined)
					return false;

				return this.isConstant(this.formstatus, string, list.status);				
			},

			isStatus: function(string, list) {

				if(string == undefined)
					return false;

				if(this.isConstant(this.formstatus, 'approved', list.status) && list.ticket)
					return this.isConstant(this.ticketstatus, string, list.ticket.status);

				return this.isConstant(this.formstatus, string, list.status);
			},

			isConstant: function(array, string, value) {
				for(let type of array) {
					
					if(type.value == value && type.label.toLowerCase() == string.toLowerCase())
						return true;
				}

				return false;
			},			
		},
	}
</script>