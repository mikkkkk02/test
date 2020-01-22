<template>

	<div class="row">
		<div id="request-track" class="col-sm-12">
		
			<div class="request-track-header row s-margin-b">
				<div class="col-sm-2 left-align">

					<div class="box-header">
						<i class="fa fa-files-o"></i>
						<h3 class="box-title">Track Requests</h3>
					</div>

				</div>								
				<div class="col-sm-10 right-align">
					<input @keyup.enter="fetch()" v-model="searchfield" :disabled="loading"
					type="number" name="table_search" class="form-control" placeholder="Search the request # of the request you want to track">
				</div>
			</div>

			<datatable ref="datatable"
			:categories="categories"
			:emptymessage="'No request logs found'"
			:nosearch="true"
			:paginationlimit="paginationlimit"
			:autofetch="false"	
			:fetchurl="fetchurl"
			>

				<thead slot="thead">
					<tr>
						<th v-if="settings.showcheckbox"></th>
						<th class="sm">Employee</th>
						<th>Details</th>
						<th class="sm"><center>Update Date</center></th>
					</tr>
				</thead>

				<template slot="data" slot-scope="{list}">

					<tr :key="list.id">
						<td v-if="settings.showcheckbox"
						class="checkbox">
							<input :value="list.id"
							type="checkbox" name="reports[]">
						</td>
						<td>{{ list.updater.extra.fullname }}</td>
						<td v-html="list.text"></td>
						<td>
							<center>
								{{ moment(list.created_at) }}
							</center>
						</td>
					</tr>

				</template>

			</datatable>	

		</div>
	</div>

</template>
<script>

	import datatable from '../DataTable.vue';

	export default {

		props: [
			'categories',

			'showcheckbox',

			'paginationlimit',

			'fetchurl',
		],

		components: {
			datatable,
		},

		data: function() {
			return {	
				loading: false,

				form: {},
				searchfield: '',

				settings: {
					fetchurl: '',
					showcheckbox: false,
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
				this.settings.fetchurl = this.fetchurl;	
				this.settings.showcheckbox = this.showcheckbox;	
			},


			/*
			|-----------------------------------------------
			| @Methods
			|-----------------------------------------------
			*/
			fetch: function() {
				var $this = this;


				/* Check if component is still loading */
				if(!this.searchfield)
					return false;


				this.form.id = this.searchfield;
				this.show();
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/	
			show: function() {
				this.loading = false;

				if(this.form.id) {
					this.settings.fetchurl = this.getURL(this.settings.fetchurl);

					this.$refs.datatable.fetch(this.settings.fetchurl);
				}
			},


			/*
			|-----------------------------------------------
			| @Getter/Setter
			|-----------------------------------------------
			*/
			getURL: function(url) {
				if(this.searchfield)
					url = this.addURLParam('request', this.searchfield, url);

				return url;
			},			
		},
	}
</script>