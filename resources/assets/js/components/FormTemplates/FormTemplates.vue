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
				<th>#</th>

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
				<th>Type</th>
				<th>Category</th>
				<th v-if="!settings.noedited">Edited By</th>

				<dataheader v-if="!settings.noupdated"
				:name="'updated_at'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Last Updated</center>

				</dataheader>

				<th v-if="settings.showarchive">Archived At</th>
				<th><center>Actions</center></th>				
			</tr>			
		</thead>

		<template slot="data" slot-scope="{list}">
	
			<tr>
				<td v-if="settings.showcheckbox"
				class="checkbox">
					<input :value="list.id"
					type="checkbox" name="formtemplates[]">
				</td>
				<td>{{ list.id }}</td>
				<td>{{ list.name }}</td>
				<td>{{ truncate(list.description, 50) }}</td>
				<td>{{ list.extra.type }}</td>				
				<td>{{ list.category.name }}</td>
				<td v-if="!settings.noedited">

					<template v-if="list.updater">

						{{ list.updater.extra.fullname }}

					</template>
				</td>
				<td v-if="!settings.noupdated">{{ moment(list.updated_at) }}</td>
				<td v-if="settings.showarchive">{{ moment(list.deleted_at) }}</td>
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
			'showarchive',
			'noedited',
			'noupdated',
			
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
					showarchive: false,
					noedited: false,
					noupdated: false,

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
				this.settings.showarchive = this.showarchive || false;
				this.settings.noedited = this.noedited || false;
				this.settings.noupdated = this.noupdated || false;
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
