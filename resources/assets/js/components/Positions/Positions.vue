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
				<th>Name</th>
				<th>Description</th>
				<th v-if="!settings.nodepartment">Department</th>
				<th><center>No. of Employees</center></th>
				<th><center>Actions</center></th>
			</tr>			
		</thead>

		<template slot="data" slot-scope="{list}">
	
			<tr>
				<td v-if="settings.showcheckbox"
				class="checkbox">
					<input :value="list.id"
					type="checkbox" name="positions[]">
				</td>				
				<td>{{ list.title }}</td>
				<td>{{ list.description }}</td>
				<td v-if="!settings.nodepartment">
					<template v-if="list.department">

						<a :href="list.department.extra.view">{{ list.department.name }}</a>

					</template>
				</td>
				<td><center>{{ list.extra.employees }}</center></td>
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
			'nodepartment',

			'paginationlimit',
			'autofetch',

			'fetchurl',
		],

		components: {
			datatable,
		},

		data: function() {
			return {	
				settings: {
					showcheckbox: false,
					nodepartment: false,
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
				this.settings.nodepartment = this.nodepartment || false;
			},
		},
	}
</script>
