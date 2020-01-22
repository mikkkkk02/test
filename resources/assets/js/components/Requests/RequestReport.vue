<template>

	<div>

		<div class="box collapsed-box">

			<div class="row form-inline s-padding-lr s-padding-t">
				<div class="col-sm-6 left-align">

					<div class="input-group input-group-sm">
						<input @keyup.enter="fetch()" v-model="search" :disabled="loading"
						type="text" name="table_search" style="width: 250px;" class="form-control" placeholder="Search">
						<div class="input-group-btn">
							<a class="btn btn-default"><i class="fa fa-search"></i></a>
						</div>
					</div>

				</div>
				<div class="col-sm-6 right-align">

					<div class="form-group">
						<div class="input-group">
							<button :disabled="loading"
							type="button" class="btn btn-default" id="daterange-btn">
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

			<div class="row">
				<div class="col-sm-12">
					<div class="box-header">

						<button @click="fetch()" :disabled="loading"
						class="btn btn-primary s-margin-r">
							<i class="fa fa-refresh s-margin-r"></i>Search
						</button>

						<button @click="excel()"
						class="btn btn-primary s-margin-r">
							<i class="fa fa-file-excel-o s-margin-r"></i>Export
						</button>

						<button class="btn btn-primary btn-xs" data-widget="collapse">
							<i class="fa fa-plus"></i>
						</button>
						<span class="no-margin s-padding-l"><b>TOGGLE FILTER OPTIONS</b></span>

					</div>
				</div>
			</div>

			<div class="box-body no-padding">
				<div class="col-sm-12">
					
					<div class="row">
						<div class="form-group col-xs-3">
							<label>Service Request</label>
							<select :disabled="loading" v-model="filter.template"
							class="form-control" required>

								<option value="0" selected>Choose request...</option>

								<template v-for="template in templates">
							
									<option :value="template.id">{{ template.name }}</option>

								</template>

							</select>							
						</div>
					</div>
						
					<div class="row">
						<div class="form-group col-xs-3">
							<label>Company</label>
							<select :disabled="loading || companies.length == 0" v-model="filter.company" @change="fetchOrg(filter.company, 0)"
							class="form-control" required>

								<option value="0" selected>Choose company...</option>

								<template v-for="company in companies">
							
									<option :value="company.id">{{ company.name }}</option>

								</template>

							</select>
						</div>
						<div class="form-group col-xs-3">

							<label>Group</label>
							<select :disabled="loading || divisions.length == 0" v-model="filter.division" @change="fetchOrg(filter.division, 1)"
							class="form-control" required>

								<option value="0" selected>Choose group...</option>

								<template v-for="division in divisions">
								
									<option :value="division.id">{{ division.name }}</option>						

								</template>

							</select>

						</div>
						<div class="form-group col-xs-3">

							<label>Department</label>
							<select :disabled="loading || departments.length == 0" v-model="filter.department" @change="fetchOrg(filter.department, 2)"
							name="department_id" class="form-control" required>

								<option value="0" selected>Choose department...</option>

								<template v-for="department in departments">
								
									<option :value="department.id">{{ department.name }}</option>						

								</template>

							</select>

						</div>	
						<div class="form-group col-xs-3">

							<label>Team</label>
							<select :disabled="loading || teams.length == 0" v-model="filter.team"
							name="team_id" class="form-control" required>

								<option value="0" selected>Choose team...</option>

								<template v-for="team in teams">
								
									<option :value="team.id">{{ team.name }}</option>						

								</template>

							</select>

						</div>		
					</div>

				</div>
			</div>
			<!-- /.box-body -->
		</div>
		<!-- /. box -->

		<div class="box box-info">
			<div class="box-body">

				<div class="dataTables_wrapper form-inline dt-bootstrap">
					
					<div class="row">
						<div class="col-sm-12 table-responsive">

							<table id="forms-yours-table" class="table table-bordered table-striped">					

								<transition name="fade">

							        <div v-if="loading"
									class="overlay">
										<i class="fa fa-refresh fa-spin"></i>
							        </div>

							    </transition>

								<thead>
									<tr>
										<th>Request Number</th>
										<th>Date Submitted</th>
										<th>Employee Name</th>
										<th>Company</th>
										<th>Division</th>
										<th>Department</th>
										<th>Team</th>
										<th v-if="!settings.notype">Type of Service Request</th>
										<th>Specific Details</th>
										<th v-if="settings.showcost">Total Cost</th>
										<th>Approved By</th>
										<th>Date Approved</th>
										<th>SLA</th>
										<th>Assigned To</th>
										<th>Status</th>
										<th>Date Closed</th>
										<th><center>Action</center></th>
									</tr>
								</thead>

								<tbody>

									<template v-for="(list, index) in lists">
								
										<tr>
											<td>{{ list.id }}</td>
											<td>{{ moment(list.created_at) }}</td>
											<td>{{ list.employee.extra.fullname }}</td>
											<td>
												
												<template v-if="list.employee.department && list.employee.department.department && list.employee.department.department.division && list.employee.department.department.division.company">

													<a :href="list.employee.department.department.division.company.extra.view"
													>{{ list.employee.department.department.division.company.abbreviation }}</a>

												</template>												

											</td>
											<td>
												
												<template v-if="list.employee.department && list.employee.department.department && list.employee.department.department.division">

													<a :href="list.employee.department.department.division.extra.view"
													>{{ list.employee.department.department.division.name }}</a>

												</template>

											</td>
											<td>
									
												<template v-if="list.employee.department && list.employee.department.department">

													<a :href="list.employee.department.department.extra.view"
													>{{ list.employee.department.department.name }}</a>

												</template>

											</td>
											<td>
												
												<template v-if="list.employee.department && list.employee.department.team">

													<a :href="list.employee.department.team.extra.view"
													>{{ list.employee.department.team.name }}</a>
													
												</template>

											</td>
											<td v-if="!settings.notype">{{ list.template.name }}</td>
											<td class="view-more">
												<center v-if="!list.extra.answers">
													<a @click="fetchDetails(list)"
													class="btn btn-xs btn-primary">
														<span :class="{
															'fa-refresh fa-spin' : list.extra.loading,
														}"
														class="fa"></span>
														 View
													</a>
												</center>

												<template v-if="list.extra.answers">
													
													<pre>{{ list.extra.answers }}</pre>

												</template>

											</td>
											<td v-if="settings.showcost">{{ list.extra.total }}</td>
											<td>
												<pre>{{ list.extra.approvers }}</pre>
											</td>
											<td>
												
												<template v-if="list.ticket">

													{{ moment(list.ticket.created_at) }}

												</template>													

											</td>
											<td>

												<template v-if="list.template">

													{{ list.template.sla }} Day(s)

												</template>

											</td>
											<td>

												<template v-if="list.ticket && list.ticket.technician">

													{{ list.ticket.technician.extra.fullname }}

												</template>														

											</td>
											<td>
												<center>
													<span data-toggle="tooltip"
													:class="{
														'bg-yellow': isStatus('draft', list) || isStatus('open', list),
														'bg-orange': isStatus('pending', list) || isStatus('on-hold', list),
														'bg-green': isStatus('approved', list) || isStatus('closed', list),
														'bg-red': isStatus('disapproved', list) || isStatus('cancelled', list),
													}"
													class="badge">{{ list.extra.status }}</span>
												</center>
											</td>
											<td>
												
												<template v-if="list.ticket">

													{{ moment(list.ticket.date_closed) }}

												</template>

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

								</tbody>

							</table>

						</div>
					</div>

					<pagination ref="pagination"
					:lists="lists"
					:url="fetchurl"
					:limit="paginationlimit"
					@page-was-clicked="fetch"></pagination>

				</div>

			</div>
		</div>	

	</div>

</template>
<script>

	import pagination from '../Pagination.vue';

	export default {

		props: [
			'templates',
			'companies',
			'formstatus',
			'ticketstatus',

			'paginationlimit',
			'notype',
			'showcost',
			'autofetch',

			'exporturl',
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
				search: '',	

				filter: {
					template: 0,
					company: 0,
					division: 0,
					department: 0,
					team: 0,
				},

				settings: {
					initialfetch: false,
					notype: false,
					showcost: false,
				},

				startdate: null,
				enddate: null,

				startDisplay: null,
				endDisplay: null,

				divisions: [],
				departments: [],
				teams: [],									
			};
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Set default variables */
				this.settings.notype = this.notype;
				this.settings.showcost = this.showcost;


				/* Initialize date range */
				this.initDatePicker();	


				/* Check if auto fetch is enable */
				if(this.autofetch) {

					/* Check if handling only 1 company */
					if(this.companies.length == 1) {

						this.filter.company = this.companies[0].id;

						/* Fetch org filter and reload list */
						this.fetchOrg(this.filter.company, 0, 1);

					} else {

						this.fetch();
					}
				}
			},

			fetch: function(link = null, param = null) {
				var $this = this,
					url = link || this.fetchurl;


				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


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

			fetchOrg: function($id, $level, $reload = false) {
				var $this = this;


				/* Check if component is still loading */
				if(this.loading || $id == 0)
					return false;

				this.loading = true;
				this.resetList($level);


				/* Fetch the divisions */
				axios.post(this.getOrgURL($id, $level), {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update divisions list */
							switch($level) {
								case 0: 

									$this.filter.company = $id;
									$this.divisions = response.data.lists; 


									/* Reset */
									$this.resetSelected($level);

								break;
								case 1: 

									$this.filter.division = $id;
									$this.departments = response.data.lists; 


									/* Reset */
									$this.resetSelected($level);

								break;
								case 2:

									$this.filter.department = $id;

									$this.filter.positions = response.data.positions; 
									$this.teams = response.data.teams; 

									
									/* Reset */
									$this.resetSelected($level);

								break;								
							}

							$this.show();


							/* Check if list needs to be reloaded */
							if($reload)
								$this.fetch();
						}
					});
			},		

			fetchDetails: function(request) {
				var $this = this;


				/* Check if component is still loading */
				if(request.extra.loading)
					return false;

				request.extra.loading = true;


				/* Fetch the groups */
				axios.post(request.extra.details, {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Insert answer data to the list */
							request.extra.answers = response.data.answers;
						}

						request.extra.loading = false;
					});
			},

			initDatePicker: function() {
				var daterange = $('#daterange-btn'),
					startDate = moment().startOf('year'),
					endDate = moment().endOf('year'),
					$this = this;

				daterange.daterangepicker({
					locale: {
						format: 'YYYY-MM-DD'
				    },
					startDate: startDate,
					endDate: endDate,
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

				/* Set default value */
				this.setDate(startDate, endDate);
			},


            /*
            |-----------------------------------------------
            | @Controllers
            |-----------------------------------------------
            */
			resetList: function(level) {

				this.filter.teams = [];
				this.filter.positions = [];

				if(level == 2)
					return null;

				this.filter.departments = [];

				if(level == 1)
					return null;

				this.filter.divisions = [];
			},		

			resetSelected: function(level) {

				this.filter.team = 0;
				this.filter.position = 0;

				if(level == 2)
					return null;

				this.filter.department = 0;

				if(level == 1)
					return null;

				this.filter.division = 0;
			},           

			show: function() {
				this.loading = false;

				if(!this.initialfetch)
					this.initialfetch = true;
			},

			excel: function() {
				window.open(this.getURL(this.exporturl), '_blank');
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
			| @Getter/Setter
			|-----------------------------------------------
			*/
			getOrgURL: function(id, level) {

				switch(level) {
					case 0: return this.getByID(this.companies, id).extra.fetchdivisions;
					case 1: return this.getByID(this.divisions, id).extra.fetchdepartments;
					case 2: return this.getByID(this.departments, id).extra.fetchpositionsteams;
				}
			},		

			getURL: function(url) {

				/* Add date parameters to URL */
				if(this.startdate)
					url = this.addURLParam('start', this.startdate, url);

				if(this.enddate)
					url = this.addURLParam('end', this.enddate, url);


				/* Add data filter parameters to URL */
				if(this.filter.template)
					url = this.addURLParam('template', this.filter.template, url);


				/* Add organization filter parameters to URL */
				if(this.filter.company && this.filter.company != 0)
					url = this.addURLParam('company', this.filter.company, url);

				if(this.filter.division && this.filter.division != 0)
					url = this.addURLParam('division', this.filter.division, url);

				if(this.filter.department && this.filter.department != 0)
					url = this.addURLParam('department', this.filter.department, url);

				if(this.filter.team && this.filter.team != 0)
					url = this.addURLParam('team', this.filter.team, url);				


				/* Add sort parameters to URL */
				if(this.sort)
					url = this.addURLParam('sort', this.sort, url);


				/* Add search parameters to URL */
				if(this.search)
					url = this.addURLParam('search', this.search, url);	

				if(this.paginationlimit)
					url = this.addURLParam('limit', this.paginationlimit, url);


				return url;
			},

			getByID: function(array, id) {
				return array.filter(function(obj) {
					return obj.id == id;
				})[0];
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

			isStatus: function(string, list) {

				if(string == undefined)
					return false;

				if(this.isConstant(this.formstatus, 'approved', list.status) && list.ticket)
					return this.isConstant(this.ticketstatus, string, list.ticket.status);

				return this.isConstant(this.formstatus, string, list.status);
			},           		

			isConstant: function(array, string, value) {
				for(let type of array) { 
					
					if(type.value == value && type.label.toLowerCase() == string.toLowerCase())
						return true;
				}

				return false;
			},			
		},
	}
</script>