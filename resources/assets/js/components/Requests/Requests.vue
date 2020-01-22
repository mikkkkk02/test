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

		<div slot="bhead">
			<div v-if="!settings.norequestedby" style="padding-bottom:10px">
					<button class="btn btn-danger s-margin-r" @click="withdrawrequest" v-if="this.isChecked">
						<i class="fa fa-close s-margin-r"></i>Withdraw
					</button>
			</div>
		</div>
		<thead slot="thead">

			<tr>
				<th v-if="!settings.norequestedby">
					<center>
						<input type="checkbox" :checked="checkedAll" @click="selectAll">
					</center>
				</th>

				<dataheader
				:name="'id'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Request #</center>

				</dataheader>

				<th v-if="!settings.norequestedby">Requested By</th>
				<th v-if="!settings.notype">Requests</th>
				<th v-if="!settings.nodetails">Purpose/Details</th>
				<th v-if="!settings.notemplatetype">Type</th>
				<th v-if="!settings.nocategory">Category</th>
				<th><center>Status</center></th>
				<th v-if="!settings.noassignedto">Assigned To</th>
				<th>Pending At</th>

				<dataheader
				:name="'created_at'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Date Created</center>

				</dataheader>

				<dataheader
				:name="'date_closed'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Date Closed</center>

				</dataheader>
				<dataheader
				:name="'updated_at'"
				:sort="settings.sort"
				:direction="settings.direction"
				:loading="loading"
				@toggle="sort"
				>
					<center slot="column">Update Date</center>

				</dataheader>
				<th><center>Actions</center></th>
			</tr>
		</thead>
		
		<template slot="data" slot-scope="{list}">
				
			<tr>
				<td v-if="!settings.norequestedby">
					<input type="checkbox" :id="'checkbox'+list.id" :value="list.id" v-model="checkedIDs" @change="select"> 
					<!-- <span>{{ checkedIDs }}</span> -->
				</td>
				<td>
					<center>
						<a :href="list.extra.view">
							{{ list.id }}
						</a>
					</center>
				</td>
				<td v-if="!settings.norequestedby">{{ list.employee.extra.fullname }}</td>
				<td v-if="!settings.notype">{{ list.template.name }}</td>
				<td v-if="!settings.nodetails">{{ truncate(list.purpose, 75) }}</td>
				<td v-if="!settings.notemplatetype">{{ list.template.extra.type }}</td>
				<td v-if="!settings.nocategory">{{ list.template.category.name }}</td>
				<td>
					<center>
						<span data-toggle="tooltip" 
						:class="{
							'bg-yellow': isStatus('draft', list) || isStatus('open', list) || isStatus('on-hold', list),
							'bg-orange': isStatus('pending', list),
							'bg-green': isStatus('approved', list) || isStatus('closed', list),
							'bg-red': isStatus('disapproved', list) || isStatus('cancelled', list),
						}"
						class="badge">{{ list.extra.status }}</span>
					</center>							
				</td>
				<td v-if="!settings.noassignedto">

					<template v-if="list.ticket && list.ticket.technician">

						{{ list.ticket.technician.extra.fullname }}

					</template>

				</td>
				<td :class="{
					'first-ch-only': list.template.approval_option == 0 
				}">
					
					<template v-if="list.approvers">
						<template v-for="approver in list.approvers">
	
							<template v-if="approver.status == 0 && !approver.approver.deleted_at">

								<p>{{ approver.approver.extra.fullname }}</p>

							</template>

						</template>
					</template>		

				</td>
				<td>{{ moment(list.created_at) }}</td>
				<td>
					<template v-if="list.ticket">
						{{ moment(list.ticket.date_closed) }}
					</template>
				</td>
				<td>{{ moment(list.ticket ? list.ticket.updated_at : list.updated_at) }}</td>
				<td>
					<center>

						<template v-if="list.ticket && isFormStatus('approved', list)">
							
							<a :href="list.ticket.extra.view" class="btn btn-primary btn-xs">
								<span class="fa fa-eye"></span>
							</a>

						</template>
						<template v-else>
							
							<a :href="list.extra.view" class="btn btn-primary btn-xs">
								<span class="fa fa-eye"></span>
							</a>

						</template>

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
			'formstatus',
			'ticketstatus',

			'notemplatetype',
			'nocategory',
			'notype',
			'noticket',
			'norequestedby',
			'noassignedto',
			'nodetails',

			'headericon',
			'header',
			'paginationlimit',
			'autofetch',
			'daterange',

			'fetchurl',
			'withdrawurl',
		],

		components: {
			datatable,
		},

		data: function() {
			return {	
				loading: false,
				checkedAll: false,
				checkedIDs: [],
				isChecked: false,

				settings: {
					norequestedby: false,
					notemplatetype: false,
					notype: false,
					nocategory: false,			
					noticket: false,
					nonoassignedto: false,
					nodetails: false,

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
				this.settings.notemplatetype = this.notemplatetype;
				this.settings.nocategory = this.nocategory;
				this.settings.notype = this.notype;
				this.settings.noticket = this.noticket;
				this.settings.norequestedby = this.norequestedby;				
				this.settings.noassignedto = this.noassignedto;				
				this.settings.nodetails = this.nodetails;
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
			isFormStatus: function(string, list) {

				if(string == undefined)
					return false;

				return this.isConstant(this.formstatus, string, list.status);				
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
			selectAll: function(){
				//select all function!!
			},
			select: function(){
				if(this.checkedIDs == 0){
					this.isChecked = false;
				}else{
					this.isChecked = true;
				}
			},
			withdrawrequest: function(){
				axios.post(this.withdrawurl, {
					id: this.checkedIDs
				})
				// .then(function (response) {
				// 	console.log(response);
				// })
				// .catch(function (error) {
				// 	console.log(error);
				// });
				// console.log(this.withdrawurl)
				location.reload();
			},
		},
	}


</script>