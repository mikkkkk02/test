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
					<center slot="column">Ticket #</center>

				</dataheader>

				<dataheader
				:name="'form_id'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Request #</center>

				</dataheader>

				<dataheader
				:name="'name'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Form</center>

				</dataheader>				

				<dataheader
				:name="'priority'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Priority</center>

				</dataheader>

				<dataheader
				:name="'category'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Category</center>

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
				:name="'technician_id'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Assigned To</center>

				</dataheader>

				<dataheader
				:name="'sla'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">SLA</center>

				</dataheader>

				<dataheader
				:name="'start_date'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Date Needed</center>

				</dataheader>

				<dataheader
				:name="'date_closed'"
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

				<dataheader v-if="!settings.nostate"
				:name="'state'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">State</center>

				</dataheader>

				<th><center>Actions</center></th>
			</tr>
		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>
					<center>
						{{ list.id }}
					</center>
				</td>
				<td>
					<center>
						{{ list.form.id }}
					</center>
				</td>
				<td>{{ list.form.template.name }}</td>
				<td>
					<center>
						<span  :class="{
							'bg-yellow': isPriority('low', list.priority),
							'bg-orange': isPriority('medium', list.priority),
							'bg-red': isPriority('high', list.priority),
						}"
						class="badge">{{ list.extra.priority }}</span>
					</center>
				</td>
				<td>{{ list.form.template.category.name }}</td>
				<td>{{ list.owner.extra.fullname }}</td>
				<td v-if="!settings.noassignedto">
	
					<template v-if="list.technician">

						{{ list.technician.extra.fullname }}

					</template>

				</td>
				<td>{{ list.form.template.sla }} Day(s)</td>
				<td>{{ moment(list.start_date, 'MMM. DD, YYYY hh:mm A', list.form.template.sla) }}</td>
				<td>{{ moment(list.date_closed, 'MMM. DD, YYYY hh:mm A') }}</td>
				<td>{{ moment(list.created_at, 'MMM. DD, YYYY hh:mm A') }}</td>
				<td>
					<center>
						<span :class="{
							'bg-primary': isStatus('open', list.status),
							'bg-green': isStatus('closed', list.status),
							'bg-yellow': isStatus('on-hold', list.status),
							'bg-red': isStatus('cancelled', list.status),
						}"
						class="badge">{{ list.extra.status }}</span>
					</center>
				</td>
				<td v-if="!settings.nostate">
					<center>
						<span v-if="!isStatus('cancelled', list.status)"
						:class="{
							'bg-green': isState('on-time', list.state) || computeState(list, 'On-time'),
							'bg-red': isState('delayed', list.state) || computeState(list, 'Delayed'),
						}"
						class="badge">{{ computeState(list) }}</span>
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
			'states',
			'priorities',
			'loading',

			'noassignedto',
			'nostate',

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
					noassignedto: false,
					nostate: false,

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
				this.settings.noassignedto = this.noassignedto;
				this.settings.nostate = this.nostate;
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

			computeState(ticket, value = null) {
				let state = ticket.extra.state;

				console.log(ticket.state);

				if (ticket.state === 0) {
					let current = moment();
					let deadline = moment(ticket.start_date).add(ticket.form.template.sla, 'days').format();

					if (current.isAfter(deadline)) {
						state = 'Delayed';
					} else {
						state = 'On-time';
					}
				}

				if (value) {
					console.log(state === value);
					return state === value;
				}

				console.log(state);

				return state;
			},
		},
	}
</script>