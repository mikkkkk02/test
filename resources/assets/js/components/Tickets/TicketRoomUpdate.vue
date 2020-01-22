<template>
	<div>


		<div class="row margin">

				<transition-group name="fade">
						
					<div v-for="(list, index) in lists" :key="index"
					class="post">

						<template v-if="list.employee">

							<div class="user-block">

									<img class="img-circle img-bordered-sm" :src="list.employee.extra.photo" alt="user image">
									<span class="username">
										<a>{{ list.employee.extra.fullname }}</a>
										<button v-if="!disabled"
										@click="destroy(list.extra.deleteurl, list.id)" 
										class="pull-right btn btn-xs" style="background: #fff"><i class="fa fa-times"></i></button>
									</span>
									<span class="description">{{ list.created_at }}</span>
							</div>
							<p>{{ list.description }}</p>

						</template>
						
					</div>

				</transition-group>

				<template v-if="lists.length < 1">
					<p>No updates found</p>
				</template>


				<pagination ref="pagination"
				:url="fetchurl"
				:limit="paginationlimit"
				@page-was-clicked="urlfetch"></pagination>

			</div>

		
		<div class="box-body">
			<div class="row col-sm-4">
				<div class="form-group">
					<label>Status</label>
					<select v-model="field.status"
					class="form-control" name="status">

						<template v-for="status in statuses">
							
							<option :value="status.value">{{ status.label }}</option>

						</template>

					</select>
				</div>
			</div>					
			<div class="row col-sm-12">
				<div class="form-group">
					<label>Description</label>
					<textarea v-model="field.description"
					class="form-control" name="description"></textarea>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-6">
			<label>Location</label>
			<select v-model="location_id" 
			class="form-control" name="location_id">

				<option value="" selected hidden>Please select a location</option>

				<template v-for="location in locations">
				
					<option :value="location.id">{{ location.name }}</option>
				
				</template>

			</select>
		</div>

		<div class="form-group col-sm-6">
			<label>Room</label>
			<select v-model="room_id"
			class="form-control" name="room_id">

				<option value="" selected hidden>Please select a room</option>

				<template v-for="room in rooms">
				
					<option :value="room.id">{{ room.name }}</option>
				
				</template>

			</select>
		</div>

		<!-- /.box-body -->

		<div class="box-footer">
			<div class="pull-right">
				<button @click="updatemrr()"
				class="btn btn-primary s-margin-r">Update</button>
			</div>
		</div>
		<!-- /.box-footer -->

		<transition name="fade">

	        <div v-show="loading"
	        class="overlay">
				<i class="fa fa-refresh fa-spin"></i>
	        </div>

	    </transition>

	</div>
</template>


<script>

import pagination from '../Pagination.vue';

export default {

	props: [
		'ticketstatus',
		'statuses',
		'fetchmrr',
		'fetchurl',
		'mrreservation',
		'disabled',
		'autofetch',
		'paginationlimit',		
		'addurl',
	],
	components: {
			pagination,
		},


	data() {
		return {
			loading: false,
			message: {
				show: 0,
				state: 0,
				label: null,
			},	
			locations: [],

			location_id: '',
			room_id: '',

			lists: [],
			field: {
				status: 0,
				description: '',
			},

			settings: {
				autofetch: false,
				initialfetch: false,
			},
		}
	},

	computed: {
		rooms() {
			let lists = [];

			if (this.locations.length > 0 && this.location_id) {
				lists = this.locations.filter(obj => { return obj.id === this.location_id})[0].rooms;
			}

			return lists;
		},
	},

	mounted() {
		this.init();
		this.inits();
	},

	methods: {
		init() {
		/* Create default filter variables */
			this.settings.autofetch = this.autofetch ? true : false;
			this.field.status = this.ticketstatus ? this.ticketstatus : 0;
			/* Check if auto fetch is enable */
			// if(this.settings.autofetch)
			this.urlfetch();

		},
		inits() {
			this.fetch();
			this.setup();
		},

		/**
		 * @Methods
		 */


		/**
		 * @Getters
		 */
		fetch() {
			this.loading = true;

			axios.post(this.fetchmrr)
			.then(response => {
				this.locations = response.data.lists;
				this.loading = false;
			}).catch(error => {
				console.log(error);
				this.loading = false;
			});

			
		},
		urlfetch(url) {

			this.run(url);
		},

		run: function(link = null, post = {}) {
				var $this = this,
					url = link || this.fetchurl;


				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch/Add/Delete the ticket update */
				axios.post(url, post)
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;

							switch(data.response) {
								case 1:

									/* Update pagination & data */
									$this.updateData(data.lists);

								break;
								case 2:

									$this.show();

									/* Redirect user to tickets */
									if(data.redirectURL) {
										$this.redirectTo(data.redirectURL);
									} else {
										$this.setMessage(data.message, 1);
									}

								break;
								case 3:

									$this.lists.splice($this.findUpdateByID(data.id), 1);

								break;
							}

							$this.reset();
							$this.show();
						}
					})
					.catch(function(error) {
						let message = 'Please input the value'; 

						switch(error.response.status) { 
							case 422: 

							let errorJSON = error.response.data; 

							for(let field in errorJSON) { 

								/* Check if there are multiple error */ 
								if($this.isArray(errorJSON[field])) { 
									message = errorJSON[field][0]; 
								} else { 
									message = errorJSON[field]; 
								} 
							} 

							break; 
							case 403: 
								message = 'Unauthorized access'; 
							break; 
						} 
						 
			            $this.setMessage(message, 0); 
						$this.reset();
						$this.show();
				    });	
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/	
			update: function() {
				this.run(this.addurl, { 
					status: this.field.status,
					description: this.field.description 
				});

			},

			updatemrr: function() {
				axios.post(this.addurl, { 
					status: this.field.status,
					description: this.field.description,
					location_id: this.location_id,
					room_id: this.room_id,

				}).then(function(response){
					/* Check AJAX result */
					if(response.status == 200) {
						var data = response.data;
						window.location.href = data.redirectURL;
					}

			
				})
				.catch(function(error) {
						
				});	
			},

			destroy: function(url, id) {
				this.run(url, { ticket_update_id: id });
			},

			show: function() {
				this.loading = false;

				if(!this.settings.initialfetch)
					this.settings.initialfetch = true;
			},

			reset: function() {

				/* Reset new field inputs */
				this.field.description = '';
			},			


			/*
			|-----------------------------------------------
			| @Getter/Setter
			|-----------------------------------------------
			*/
			//


            /*
            |-----------------------------------------------
            | @Helper
            |-----------------------------------------------
            */
			setMessage: function(message, state) {
				var $this = this;

		        this.message.label = message;
		        this.message.state = state;
				this.message.show = true;

				/* Hide message after */
				setTimeout(function() {
			        $this.message.show = false;
				}, 1500);
			},           

           	updateData: function(lists) {

				/* Update the lists */
				this.lists = lists.data;

				/* Update the pagination data */
				this.$refs.pagination.updatePagination(lists);
           	},

           	redirectTo: function(url) { console.log('asd');
				window.location.href = url;
           	},

           	isInitialFetch: function() {
           		return this.settings.initialfetch;
           	},

			isArray: function(obj) { 
				return Object.prototype.toString.call(obj) === '[object Array]'; 
			},           	

           	findUpdateByID: function(id) {
           		return this.lists.map(function(obj) {
           			return obj.id;
           		}).indexOf(id);
           	},         
		/**
		 * @Setters
		 */
		setup() {
			if (this.mrreservation) {
				this.location_id = this.mrreservation.location_id ? this.mrreservation.location_id : '';
				this.room_id = this.mrreservation.room_id ? this.mrreservation.room_id : '';
			}
		}
	},

}
</script>
