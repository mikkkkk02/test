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
				:name="'created_at'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Date</center>

				</dataheader>

				<th><center>Actions</center></th>
				
			</tr>
		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>
					<center>
						{{ moment(list.created_at, 'MMM. DD, YYYY hh:mm A') }}
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
			//			
		},
	}
</script>