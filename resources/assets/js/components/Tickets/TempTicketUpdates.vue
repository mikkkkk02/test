<template>

	<datatable ref="datatable"
	:categories="categories"
	:paginationlimit="paginationlimit"
	:autofetch="autofetch"	
	:daterange="daterange"
	:fetchurl="fetchurl"
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

				<dataheader
				:name="'employee_id'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Requested By</center>

				</dataheader>

				<dataheader
				:name="'description'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Description</center>

				</dataheader>

				<th><center>Approver</center></th>

				<th><center>Reason</center></th>

				<dataheader
				:name="'approved_date'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Date Closed</center>

				</dataheader>

				<dataheader
				:name="'created_at'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Date Created</center>

				</dataheader>

				<dataheader
				:name="'status'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Status</center>

				</dataheader>

				<th><center>Actions</center></th>
			</tr>
		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>
					<center>
						{{ list.ticket.form_id }}
					</center>
				</td>
				<td>
	
					<template v-if="list.employee">

						<center>{{ list.employee.extra.fullname }}</center>

					</template>

				</td>
				<td>{{ truncate(list.description, 80) }}</td>
				<td>

					<template v-if="list.approver">

						<center>{{ list.approver.extra.fullname }}</center>

					</template>					

				</td>
				<td>{{ truncate(list.reason, 80) }}</td>
				<td>
					<center>
						{{ moment(list.approved_date, 'MMM. DD, YYYY') }}
					</center>
				</td>
				<td>
					<center>
						{{ moment(list.created_at, 'MMM. DD, YYYY') }}
					</center>
				</td>
				<td>
					<center>
						<span :class="{
							'bg-yellow': isStatus('pending', list.status),
							'bg-green': isStatus('approved', list.status),
							'bg-red': isStatus('disapproved', list.status),
						}"
						class="badge">{{ list.extra.status }}</span>
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
			'status',
			'loading',

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
			isStatus: function(string, value) {
				return this.isConstant(this.status, string, value);
			},

			isState: function(string, value) {
				return this.isConstant(this.states, string, value);
			},

			isPriority: function(string, value) {
				return this.isConstant(this.priorities, string, value);
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