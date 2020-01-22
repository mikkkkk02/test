<template>

	<datatable ref="datatable"
	:categories="categories"
	:paginationlimit="paginationlimit"
	:autofetch="autofetch"
	:fetchurl="fetchurl"
	>

		<thead slot="thead">
			<tr>

				<dataheader
				:name="'name'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Name</center>

				</dataheader>

				<th>Company</th>
				<th>Description</th>
				<th><center>No. of Users</center></th>
				<th><center>Actions</center></th>						
			</tr>
		</thead>

		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>{{ list.name }}</td>
				<td>
					<template v-if="list.company">
						For {{ list.company.extra.name }}
					</template>
					<template v-else>
						For All
					</template>
				</td>
				<td>{{ list.description }}</td>
				<td>
					<center>{{ list.extra.employees }}</center>
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

			init: function() {},


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