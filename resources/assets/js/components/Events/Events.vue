<template>

	<datatable ref="datatable"
	:headericon="headericon"
	:header="header"
	:categories="categories"
	:paginationlimit="paginationlimit"
	:autofetch="autofetch"
	:daterange="daterange"
	:showdaterange="true"
	:fetchurl="fetchurl"
	>

		<thead slot="thead">
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Time</th>
				<th>Hours</th>
				<th>Title</th>
				<th>Facilitator</th>
				<th>Venue</th>
				<th>Description</th>
				<th>Participants</th>
				<th><center>Actions</center></th>							
			</tr>						
		</thead>

		<template slot="data" slot-scope="{list}">

			<tr>
				<td>{{ moment(list.start_date) }}</td>
				<td>{{ moment(list.end_date) }}</td>
				<td>
					<pre>{{ list.extra.time }}</pre>
				</td>
				<td>{{ list.hours }}</td>
				<td>{{ list.title }}</td>
				<td>{{ list.facilitator }}</td>
				<td>{{ list.venue }}</td>
				<td>{{ truncate(list.description, 75) }}</td>
				<td>
					<center>
						<span :class="{
							'bg-green': !list.limit,
							'bg-blue': list.limit && list.limit > list.attending,
							'bg-red': list.limit && list.limit >= list.attending,
						}"
						class="badge">{{ list.extra.participants }}</span>
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