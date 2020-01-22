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
				:name="'name'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Name</center>

				</dataheader>

				<th>Description</th>
				<th v-if="!settings.nodivision">Groups</th>
				<th><center>No. of Teams</center></th>
				<th><center>Actions</center></th>
			</tr>			
		</thead>

		<template slot="data" slot-scope="{list}">
	
			<tr>
				<td v-if="settings.showcheckbox"
				class="checkbox">
					<input :value="list.id"
					type="checkbox" name="departments[]">
				</td>				
				<td>{{ list.name }}</td>
				<td>{{ list.description }}</td>
				<td v-if="!settings.nodivision">

					<template v-if="list.division">

						<a :href="list.division.extra.view">{{ list.division.name }}</a>

					</template>

				</td>
				<td><center>{{ list.extra.teams }}</center></td>
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

			'showcheckbox',
			'nodivision',

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
					nodivision: false,

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
				this.settings.showcheckbox = this.showcheckbox || false;
				this.settings.nodivision = this.nodivision || false;
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