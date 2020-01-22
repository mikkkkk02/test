<template>

	<div class="dataTables_wrapper form-inline dt-bootstrap">

		<div v-if="settings.showdaterange"
		class="row s-margin-b">
			<div class="col-sm-12">
				<div class="form-group">
					<div class="input-group">
						<button type="button" class="btn btn-default" :id="daterange + 'daterange-btn'">
							<i class="fa fa-calendar"></i>
							<span>{{ startDisplay && endDisplay ? startDisplay + ' - ' + endDisplay : ' Select date here...' }}</span>
							<i class="fa fa-caret-down"></i>
						</button>
						<button @click="fetch()" :disabled="loading"
						type="button" class="btn btn-default s-margin-l">
							<i class="fa fa-refresh"></i>
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row s-margin-b">
			<div class="col-sm-4 left-align">

				<div v-if="header"
				class="box-header">
					<i class="fa" :class="[ headericon ]"></i>
					<h3 class="box-title">{{ header }}</h3>
				</div>

				<template v-for="category in categories">
					
					<select v-if="category" :disabled="loading" v-model="filter[category.label]" @change="fetch()"
					class="form-control" style="width: 150px;">
						<option value="x" selected>Filter {{ category.label }}...</option>

						<template v-for="option in category.options">

							<option :value="option.id">{{ option.label }}</option>

						</template>

					</select>

				</template>

			</div>								
			<div class="col-sm-8 right-align">
				<div v-if="!settings.nosearch"
				class="form-group left-inner-addon width--100">
					<i class="fa fa-search"></i>
					<input @keyup.enter="fetch()" v-model="search" :disabled="loading"
					type="text" name="table_search" class="form-control width--100" placeholder="Search">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12 table-responsive">

				<table id="forms-yours-table" class="table table-bordered table-striped">

					<transition name="fade">

				        <div v-if="loading"
						class="overlay">
							<i class="fa fa-refresh fa-spin"></i>
				        </div>

				    </transition>


					<slot name="thead"></slot>

					<tbody>

						<template v-for="(list, index) in lists">

							<slot name="data" :list="list"></slot>

						</template>

					</tbody>

				</table>

			</div>
		</div>

	<!-- 	<pagination ref="pagination"
		:lists="lists"
		:url="fetchurl"
		:limit="paginationlimit"
		@page-was-clicked="fetch"></pagination> -->

	</div>

</template>
<script>

	import pagination from './Pagination.vue';

	  	$(document).ready( function () {
            $('#forms-yours-table').DataTable({
            	// 'bProcessing': true
            });
        });

	export default {

		props: [
			'categories',

			'headericon',
			'header',
			'paginationlimit',
			'autofetch',
			'nosearch',
			'showdaterange',
			'daterange',

			'fetchurl',
		],

		components: {
			pagination,
		},

		data: function() {
			return {
				loading: false,

				lists: [],
				sort: null, 
				sortOrder: null,
				search: '',	

				filter: {},

				settings: {
					autofetch: false,
					initialfetch: false,
					nosearch: false,
					showdaterange: false,					
				},

				startdate: null,
				enddate: null,

				startDisplay: null,
				endDisplay: null,				
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {
				var $this = this;


				/* Create default filter variables */
				if(this.categories) {

					for(var $i = 0; $i < this.categories.length; $i++) {
						this.filter[this.categories[$i].label] = 'x';
					}
				}

				this.settings.autofetch = this.autofetch ? true : false;
				this.settings.paginationlimit = this.paginationlimit ? this.paginationlimit : 10;
				this.settings.nosearch = this.nosearch ? this.nosearch : false;
				this.settings.showdaterange = this.daterange ? true : false;


				/* Initialize date range */
				if(this.settings.showdaterange) {
					this.$nextTick(function() {
						$this.initDatePicker();
					});
				}


				/* Check if auto fetch is enable */
				if(this.settings.autofetch)
					this.fetch();
			},

			initDatePicker: function() {
				var daterange = $('#' + this.daterange + 'daterange-btn'),
					startDate = moment().startOf('year'),
					endDate = moment().endOf('year'),
					$this = this;


				daterange.daterangepicker({
					locale: {
						format: 'YYYY-MM-DD'
				    },
					startDate: 0,
					endDate: 0,
					opens: 'right',
					ranges: {
						'This Year': [startDate, endDate],
						'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
				}, function(start, end) {

					/* Update date */
					$this.setDate(start, end);
				});
			},			

			fetch: function(link = null, sort = null, order = null) {
				var $this = this,
					url = link || this.fetchurl;


				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.setLoading(true);


				/* Set sort if exist */
				if(sort)
					this.sort = sort;

				if(order)
					this.sortOrder = order;


				/* Fetch the groups */
				axios.post(this.getURL(url), {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update pagination & data */
							$this.updateData(response.data.lists);

							$this.show();
						}
					});
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/	
			show: function() {
				this.setLoading(false);

				if(!this.settings.initialfetch)
					this.settings.initialfetch = true;
			},

			setLoading: function(bool) {
				this.loading = bool;

				if(this.loading) {
					this.$emit('is-loading');
				} else {
					this.$emit('not-loading');
				}
			},


			/*
			|-----------------------------------------------
			| @Getter/Setter
			|-----------------------------------------------
			*/
			getURL: function(url) {

				/* Add date parameters to URL */
				if(this.settings.showdaterange) {

					if(this.startdate)
						url = this.addURLParam('start', this.startdate, url);

					if(this.enddate)
						url = this.addURLParam('end', this.enddate, url);
				}

				/* Add filter parameters to URL */
				if(this.categories) {

					for(var $i = 0; $i < this.categories.length; $i++) {
						var filter = this.filter[this.categories[$i].label];

						/* Check if dynamic filter has value */
						if(filter != 'x')
							url = this.addURLParam(this.categories[$i].label, filter, url);			
					}
				}

				/* Add sort parameters to URL */
				if(this.sort)
					url = this.addURLParam('sort', this.sort, url);

				if(this.sortOrder)
					url = this.addURLParam('order', this.sortOrder, url);

				/* Add search parameters to URL */
				if(this.search)
					url = this.addURLParam('search', this.search, url);	

				if(this.settings.paginationlimit)
					url = this.addURLParam('limit', this.settings.paginationlimit, url);				


				return url;
			},

			setURL: function(url) {

				/* Check if url is empty */
				if(!url)
					return false;

				this.fetchurl = url;
			},

           	setDate: function(startDate, endDate) {

				/* Check if still fetching */
				if(this.loading)
					return false;


				this.startdate = startDate.format('YYYY-MM-DD');
				this.enddate = endDate.format('YYYY-MM-DD');

				this.startDisplay = startDate.format('MMMM D, YYYY');
				this.endDisplay = endDate.format('MMMM D, YYYY');
           	},			


            /*
            |-----------------------------------------------
            | @Helper
            |-----------------------------------------------
            */
           	updateData: function(lists) {

				/* Update the groups lists */
				this.lists = lists.data;

				/* Update the pagination data */
				this.$refs.pagination.updatePagination(lists);
           	},

           	isInitialFetch: function() {
           		return this.settings.initialfetch;
           	},
		},
	}
</script>