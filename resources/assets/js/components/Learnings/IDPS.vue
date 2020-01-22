<template>

	<datatable ref="datatable"
	:categories="categories"
	:paginationlimit="paginationlimit"
	:autofetch="autofetch"	
	:daterange="daterange"
	:fetchurl="fetchurl"
	>

		<thead slot="thead">
			<tr>
				<th v-if="settings.showcheckbox"></th>				
				<th>Ref. No.</th>
				<th v-if="!settings.noemployee">Name</th>
				<th v-if="!settings.noemployee">Team</th>
				<th v-if="!settings.noemployee">Position</th>
				<th v-if="!settings.noemployee">Leader</th>
				<th>Learning Act. Type</th>
				<th>Competency Type</th>
				<th>Specific Competency</th>
				<th v-if="!settings.nodetails">Details</th>
				<th><center>Completion Year</center></th>
				<th v-if="!settings.noapprover">Approver</th>
				<th>Approval Status</th>
				<th v-if="!settings.nostatus"><center>Status</center></th>
				<th><center>Action</center></th>
			</tr>
		</thead>

		<template slot="data" slot-scope="{list}">
	
			<tr>
				<td v-if="settings.showcheckbox"
				class="checkbox">
					<input :value="list.id"
					type="checkbox" name="idps[]">
				</td>
				<td>{{ list.id }}</td>		
				<td v-if="!settings.noemployee">{{ list.employee.extra.fullname }}</td>
				<td v-if="!settings.noemployee">
					
					<template v-if="list.employee.department && list.employee.department.team">

						<a :href="list.employee.department.team.extra.view"
						>{{ list.employee.department.team.name }}</a>
						
					</template>

				</td>
				<td v-if="!settings.noemployee">

					<template v-if="list.employee.department && list.employee.department.position">

						{{ list.employee.department.position.title }}
						
					</template>

				</td>
				<td v-if="!settings.noemployee">

					<template v-if="list.employee.supervisor">

						<a :href="list.employee.supervisor.extra.view"
						>{{ list.employee.supervisor.extra.fullname }}</a>
						
					</template>

				</td>				
				<td>{{ list.extra.learning }}</td>
				<td>{{ list.extra.competency }}</td>
				<td>{{ list.competency.name }}</td>
				<td v-if="!settings.nodetails">{{ truncate(list.details, 75) }}</td>
				<td><center>{{ list.completion_year }}</center></td>
				<td v-if="!settings.noapprover">
					<template v-for="approver in list.approvers">
						<p>{{ approver.extra.fullname }}</p>
					</template>
				</td>
				<td><span class="badge" :class="list.extra.approval_class">{{ list.extra.approval_status }}</span></td>
				<td v-if="!settings.nostatus">
					<center>
						<span :class="{
							'bg-yellow': isStatus('pending', list) || isStatus('pending update', list) || isStatus('ongoing', list),
							'bg-orange': isStatus('for completion', list) || isStatus('cancelled', list),
							'bg-green': isStatus('completed', list) || isStatus('approved', list),
							'bg-red': isStatus('disapproved', list),
						}"
						class="badge">{{ list.extra.status }}</span>
					</center>
				</td>
				<td v-if="!settings.noaction">
					<center>
						<a :href="list.extra.view" class="btn btn-primary btn-xs">
							<span class="fa fa-eye"></span>
						</a>
					</center>
				</td>
				<td v-if="settings.showapprovalaction">
					<center>
						<a :href="list.extra.approvalview" class="btn btn-primary btn-xs">
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
			'status',
			'approvalstatus',
			'categories',

			'showcheckbox',
			'showapprovalaction',
			'noemployee',
			'noapprover',
			'nostatus',
			'nodetails',
			'noaction',

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
				settings: {
					showcheckbox: false,
					showapprovalaction: false,
					noemployee: false,
					noapprover: false,
					nostatus: false,
					nodetails: false,
					noaction: false,
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
				this.settings.showcheckbox = this.showcheckbox;
				this.settings.showapprovalaction = this.showapprovalaction;
				this.settings.noemployee = this.noemployee;
				this.settings.noapprover = this.noapprover;
				this.settings.nostatus = this.nostatus;
				this.settings.nodetails = this.nodetails;
				this.settings.noaction = this.noaction;
			},


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/
			isStatus: function(string, idp) {

				if(string == undefined)
					return false;

				return this.isConstant(this.status, string, idp.status);
			},

			isConstant: function(array, string, value) {
				for(let type of array) { 

					console.log(type.label);
					
					if(type.value == value && type.label.toLowerCase() == string.toLowerCase()) {
						return true;
					}
				}

				return false;
			},			
		},
	}
</script>