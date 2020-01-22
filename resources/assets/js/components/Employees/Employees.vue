<template>

	<datatable ref="datatable"
	:categories="categories"
	:paginationlimit="paginationlimit"
	:autofetch="autofetch"	
	:fetchurl="fetchurl"
	>

		<thead slot="thead">
			<tr>
				<th v-if="settings.showcheckbox"></th>

				<dataheader
				:name="'id'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">#</center>

				</dataheader>

				<dataheader v-if="!settings.noemail"
				:name="'email'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Email Address</center>

				</dataheader>

				<dataheader
				:name="'last_name'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Name</center>

				</dataheader>

				<th v-if="!settings.nocompany">Company</th>
				<th v-if="!settings.nodivision">Group</th>
				<th v-if="!settings.nodepartment">Department</th>
				<th v-if="!settings.noteam">Team</th>
				<th>Leader</th>
				<th>Position</th>
				<th v-if="!settings.nolocation">Location</th>
				<th>Category</th>
				<th><center>Actions</center></th>
			</tr>
		</thead>

		<template slot="data" slot-scope="{list}">

			<tr :key="list.id">
				<td v-if="settings.showcheckbox"
				class="checkbox">
					<input :value="list.id"
					type="checkbox" name="users[]">
				</td>
				<td>{{ list.id }}</td>
				<td v-if="!settings.noemail">{{ list.email }}</td>
				<td>{{ list.extra.fullname }}</td>
				<td v-if="!settings.nocompany">
	
					<template v-if="list.department && list.department.department && list.department.department.division && list.department.department.division.company">

						<a :href="list.department.department.division.company.extra.view"
						>{{ list.department.department.division.company.abbreviation }}</a>

					</template>

				</td>
				<td v-if="!settings.nodivision">
	
					<template v-if="list.department && list.department.department && list.department.department.division">

						<a :href="list.department.department.division.extra.view"
						>{{ list.department.department.division.name }}</a>

					</template>

				</td>
				<td v-if="!settings.nodepartment">
		
					<template v-if="list.department && list.department.department">

						<a :href="list.department.department.extra.view"
						>{{ list.department.department.name }}</a>

					</template>

				</td>
				<td v-if="!settings.noteam">
					
					<template v-if="list.department && list.department.team">

						<a :href="list.department.team.extra.view"
						>{{ list.department.team.name }}</a>
						
					</template>

				</td>
				<td>

					<template v-if="list.supervisor">

						<a :href="list.supervisor.extra.view"
						>{{ list.supervisor.extra.fullname }}</a>
						
					</template>

				</td>
				<td>

					<template v-if="list.department && list.department.position">

						{{ list.department.position.title }}
						
					</template>

				</td>
				<td v-if="!settings.nolocation">
			
					<template v-if="list.location">
						
						{{ list.location.name }}

					</template>
					
				</td>
				<td>{{ list.category.title }}</td>
				<td>
					<center>
						<a :href="list.extra.view" 
						class="btn btn-primary btn-xs">
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

			'showcheckbox',
			'noemail',
			'nocompany',
			'nodivision',
			'nodepartment',
			'noteam',
			'nolocation',

			'paginationlimit',
			'autofetch',

			'fetchurl',
		],

		components: {
			datatable,
		},

		data: function() {
			return {	
				loading: false,
				
				settings: {
					showcheckbox: false,
					noemail: false,
					nocompany: false,
					nodivision: false,
					nodepartment: false,
					noteam: false,
					nolocation: false,

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
				this.settings.showcheckbox = this.showcheckbox;
				this.settings.noemail = this.noemail;
				this.settings.nocompany = this.nocompany;
				this.settings.nodivision = this.nodivision;
				this.settings.nodepartment = this.nodepartment;
				this.settings.noteam = this.noteam;
				this.settings.nolocation = this.nolocation;			
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

		},
	}
</script>