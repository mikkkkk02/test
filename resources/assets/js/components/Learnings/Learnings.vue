<template>

	<datatable ref="datatable"
	:categories="categories"
	:paginationlimit="paginationlimit"
	:autofetch="autofetch"	
	:fetchurl="fetchurl"
	:daterange="daterange"
	>

		<thead slot="thead">
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Time</th>
				<th>Hours</th>
				<th>Type</th>
				<th>Title</th>
				<th>Facilitator</th>
				<th>Venue</th>
				<th>Description</th>
				<th>Provider</th>
				<th>Status</th>
				<th><center>Actions</center></th>
			</tr>						
		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>{{ list.learning.start_date }}</td>
				<td>{{ list.learning.end_date }}</td>
				<td>{{ list.learning.extra.time }}</td>
				<td>{{ list.learning.hours }}</td>
				<td>{{ list.learning.category.title }}</td>
				<td>{{ list.learning.title }}</td>
				<td>{{ list.learning.facilitator }}</td>
				<td>{{ list.learning.venue }}</td>
				<td>{{ list.learning.description }}</td>
				<td>{{ list.learning.provider }}</td>
				<td>
					<center>
						<span class="badge bg-yellow">{{ list.extra.status }}</span>
					</center>
				</td>
				<td>
					<center>
						<a :href="list.learning.extra.view" class="btn btn-primary btn-xs">
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
			'daterange',

			'fetchurl',
		],

		components: {
			datatable,
		},

		data: function() {
			return {	
				//
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {},
		},
	}
</script>