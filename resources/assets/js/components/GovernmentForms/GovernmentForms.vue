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

				<th>Description</th>
				<th>Edited By</th>

				<dataheader
				:name="'updated_at'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Last Updated</center>

				</dataheader>

				<th><center>Actions</center></th>						
			</tr>
		</thead>

		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>{{ list.name }}</td>
				<td>{{ list.description }}</td>
				<td>
					
					<template v-if="list.updater">
							
						{{ list.updater.extra.fullname }}

					</template>					

				</td>				
				<td>{{ moment(list.updated_at) }}</td>
				<td>
					<center>

						<template v-if="list.attachment">

							<a :href="list.attachment.extra.view" target="_blank" class="btn btn-success btn-xs">
								<span class="fa fa-download"></span>
							</a>	

						</template>
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