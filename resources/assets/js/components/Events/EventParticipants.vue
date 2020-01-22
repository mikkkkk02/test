<template>

	<div>
		
		<datatable ref="datatable"
		:categories="categories"
		:paginationlimit="paginationlimit"
		:daterange="daterange"
		:showdaterange="showdaterange"
		:autofetch="autofetch"
		:fetchurl="fetchurl"
		>

			<thead slot="thead">
				<tr>
					<th v-if="settings.showcheckbox"></th>
					<th v-if="!settings.noevent">Event</th>
					<th v-if="!settings.noevent">Start Date</th>
					<th v-if="!settings.noevent">End Date</th>				
					<th v-if="!settings.noevent">Time</th>
					<th v-if="!settings.noeventdetails">Title</th>
					<th v-if="!settings.noeventdetails">Facilitator</th>
					<th v-if="!settings.noeventdetails">Venue</th>					
					<th v-if="!settings.noemployee">Email</th>
					<th v-if="!settings.noemployee">Name</th>
					<th v-if="!settings.nostatus"><center>Status</center></th>
					<th v-if="!settings.noteam">Team</th>
					<th v-if="!settings.noemployee">Position</th>
					<th v-if="!settings.noapprover">Approver</th>
					<th v-if="!settings.noattendance">Approved At</th>
					<th v-if="!settings.noattendance"><center>Attendance</center></th>
					<th><center>Actions</center></th>
				</tr>
			</thead>

			<template slot="data" slot-scope="{list}">

				<tr>
					<td v-if="settings.showcheckbox"
					class="checkbox">
						<input :value="list.id"
						type="checkbox" name="participants[]">
					</td>					
					<td v-if="!settings.noevent">{{ list.event.title }}
					</td>
					<td v-if="!settings.noevent">

						{{ moment(list.event.start_date) }}

					</td>
					<td v-if="!settings.noevent">

						{{ moment(list.event.end_date) }}

					</td>
					<td v-if="!settings.noevent">

						<pre>{{ list.event.extra.time }}</pre>

					</td>
					<td v-if="!settings.noeventdetails">{{ list.event.title }}</td>
					<td v-if="!settings.noeventdetails">{{ list.event.facilitator }}</td>
					<td v-if="!settings.noeventdetails">{{ list.event.venue }}</td>						
					<td v-if="!settings.noemployee">{{ list.participant.email }}</td>
					<td v-if="!settings.noemployee">{{ list.participant.extra.fullname }}</td>
					<td v-if="!settings.nostatus">
						<center>
							<span data-toggle="tooltip"
							:class="{
								'bg-yellow': isStatus('pending', list.status),
								'bg-green': isStatus('approved', list.status),
								'bg-red': isStatus('disapproved', list.status),
								'bg-orange': isStatus('cancelled', list.status),
							}"
							class="badge">{{ list.extra.status }}</span>
						</center>
					</td>
					<td v-if="!settings.noteam">
						
						<template v-if="list.participant.department && list.participant.department.team">

							<a :href="list.participant.department.team.extra.view"
							>{{ list.participant.department.team.name }}</a>
							
						</template>

					</td>
					<td v-if="!settings.noemployee">

						<template v-if="list.participant.department && list.participant.department.position">

							{{ list.participant.department.position.title }}
							
						</template>

					</td>
					<td v-if="!settings.noapprover">
						
						<template v-if="list.approver">
						
							{{ list.approver.extra.fullname }}	

						</template>

					</td>
					<td v-if="!settings.noattendance">
					
						{{ moment(list.approved_at, 'MMM. DD, YYYY (hh:mm a)') }}

					</td>					
					<td v-if="!settings.noattendance">
						<center>
							<span :class="{
								'bg-yellow': isAttendance('pending', list.hasAttended),
								'bg-green': isAttendance('attended', list.hasAttended),
								'bg-red': isAttendance('no show', list.hasAttended),
							}"
							class="badge">{{ list.extra.attendance }}</span>
						</center>						
					</td>
					<td>
						<center>
							<a :href="list.event.extra.view" class="btn btn-primary btn-xs">
								<span class="fa fa-eye"></span>
							</a>

							<template v-if="!settings.nostatusaction">

								<button v-if="isStatus('pending', list.status) || isStatus('disapproved', list.status)"
								@click="approve(list)"
								class="btn btn-success btn-xs">
									<span class="fa fa-check"></span>
								</button>

								<button v-if="isStatus('pending', list.status) || isStatus('approved', list.status)"
								@click="showDisapproveModal(list)"
								class="btn btn-danger btn-xs" data-toggle="modal" data-target="#disapprove-attendance">
									<span class="fa fa-times"></span>
								</button>

							</template>
							<template v-if="!settings.noattendanceaction">							

								<button v-if="isAttendance('pending', list.hasAttended) || isAttendance('no show', list.hasAttended)"
								@click="attended(list)"
								class="btn btn-success btn-xs">
									<span class="fa fa-check"></span>
								</button>

								<button v-if="isAttendance('pending', list.hasAttended) || isAttendance('attended', list.hasAttended)"
								@click="noshow(list)"
								class="btn btn-danger btn-xs">
									<span class="fa fa-times"></span>
								</button>								

							</template>

						</center>
					</td>
				</tr>

			</template>

		</datatable>

		<!-- Disapprove Modal -->
		<div id="disapprove-attendance" class="modal fade" tabindex="-1">
		    <div class="modal-dialog">
		        <div class="box modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                	<span aria-hidden="true">&times;</span>
		                </button>
		                <h4 class="modal-title">Disapprove Event Attendance</h4>
		            </div>

					<transition name="fade">

				        <div v-if="loading"
						class="overlay">
							<i class="fa fa-refresh fa-spin"></i>
				        </div>

				    </transition>

		            <div class="modal-body">

						<div class="row">
							<div class="col-sm-12">

								<div class="form-group">
									<label>Reason</label>
									<textarea v-model="field.reason" :disabled="loading"
									type="text" name="reason" placeholder="Reason" class="form-control"></textarea>
								</div>

			                </div>
		                </div>

					</div>
					<div class="modal-footer">
						<button @click="disapprove()" :disabled="loading"
						class="btn btn-primary pull-left">Disapprove</button>
						<a class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancel</a>
					</div>
		        </div>
		        <!-- /.modal-content -->
		    </div>
		    <!-- /.modal-dialog -->
		</div>	

	</div>

</template>
<script>

	import datatable from '../DataTable.vue';

	export default {

		props: [
			'categories',

			'showdaterange',
			'showcheckbox',
			'noevent',
			'noeventdetails',
			'nostatus',
			'noemployee',
			'noteam',
			'noapprover',
			'noattendance',
			'nostatusaction',
			'noattendanceaction',

			'status',
			'attendance',

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

				active: null,
				field: {
					reason: null,
				},

				settings: {
					showcheckbox: false,
					noevent: false,
					noeventdetails: false,
					nostatus: false,
					noemployee: false,
					noteam: false,
					noapprover: false,
					noattendance: false,
					nostatusaction: false,
					noattendanceaction: false,
				},
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
				this.settings.noevent = this.noevent;
				this.settings.noeventdetails = this.noeventdetails;
				this.settings.nostatus = this.nostatus;
				this.settings.noemployee = this.noemployee;
				this.settings.noteam = this.noteam;
				this.settings.noapprover = this.noapprover;
				this.settings.noattendance = this.noattendance;
				this.settings.nostatusaction = this.nostatusaction;
				this.settings.noattendanceaction = this.noattendanceaction;
			},

			run: function(url, post = {}, type) {
				var $this = this;

				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch the groups */
				axios.post(url, post)
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;

							/* Update event status */
							switch(type) {
								case 1:

									$this.active.status = data.status;
									$this.active.extra.status = data.message;

								break;
								case 2:

									$this.active.hasAttended = data.attendance;
									$this.active.extra.attendance = data.message;

								break;
							}

							$this.reset();
						}
					});
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/
			approve: function(list) {
				this.active = list;

				this.run(list.extra.approveurl, {}, 1);
			},

			disapprove: function() {
				this.run(this.active.extra.disapproveurl, { reason: this.field.reason }, 1);
			},

			showDisapproveModal: function(list) {
				this.active = list;
			},

			attended: function(list) {
				this.active = list;

				this.run(this.active.extra.attendedurl, null, 2);
			},

			noshow: function(list) {
				this.active = list;

				this.run(this.active.extra.noshowurl, null, 2);
			},

			reset: function() {
				/* Hide modal */
				$('#disapprove-attendance').modal('hide');

				this.active = null;
				this.field.reason = '';

				this.loading = false;
			},


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/
			isStatus: function(string, value) {

				if(string == undefined)
					return false;

				return this.isConstant(this.status, string, value);
			},

			isAttendance: function(string, value) {

				if(string == undefined)
					return false;

				return this.isConstant(this.attendance, string, value);
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