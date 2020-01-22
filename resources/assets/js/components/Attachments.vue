<template>
	<div class="row">
		<div class="col-sm-12">
			
				
			<div class="box box-warning">
				<div style="cursor: pointer;"
				class="box-header" @click="toggle()">
					<i class="fa fa-clipboard"></i>
					<h3 class="box-title">{{ header }}</h3>
					
					<form v-show="!disabled" :id="'attachmentForm-' + id" method="post" action="#" class="pull-right">
						<input :id="'formAttachment-' + id" type="file" name="attachment" style="display: none;">
						<label @click="toggle(1)"
						:for="'formAttachment-' + id" class="btn btn-xs btn-warning pull-right">
							Add Attachments
						</label>
					</form>

				</div>

				<div id="attachment-table" class="box-body collapse in">

					<transition name="fade">			

						<label v-show="message.show" :class="{
							'has-success': message.state,
							'has-warning': !message.state,
						}"
						class="form-group s-padding-l no-margin">
			            	<label class="control-label" for="inputSuccess">
			          			<i :class="{
			          				'fa-check': message.state,
			          				'fa-times': !message.state,
			          			}"
			          			class="fa"></i> {{ message.label }}
			          		</label>
			            </label>

					</transition>

					<div class="dataTables_wrapper form-inline dt-bootstrap">					

						<div class="row">
							<div class="col-sm-12 table-responsive">

								<table v-show="lists.length > 0"
								:id="'attachmentstable-' + id" class="table table-bordered table-striped">

									<transition name="fade">

								        <div v-if="loading"
										class="overlay">
											<i class="fa fa-refresh fa-spin"></i>
								        </div>

								    </transition>

									<thead>
										<tr>
											<th>Name</th>
											<th>Date Attached</th>
											<th>Attached By</th>
											<th v-show="!disabled"><center>Actions</center></th>
										</tr>								
									</thead>

									<tbody>

										<template v-for="(list, index) in lists">
											
											<tr>
												<td>{{ list.name }}</td>
												<td>{{ moment(list.created_at) }}</td>
												<td>{{ list.extra.employee }}</td>
												<td v-show="!disabled">
													<center>
														<a :href="list.extra.view" 
														target="_blank" class="btn btn-primary btn-xs">
															<span class="fa fa-eye"></span>
														</a>
														<a @click="removeAttachment(list, list.id)" 
														class="btn btn-danger btn-xs">
															<span class="fa fa-times"></span>
														</a>												
													</center>											
												</td>
											</tr>

										</template>

									</tbody>

								</table>

								<table v-show="attachments.length > 0"
								:id="'attachmentstable-' + id" class="table table-bordered table-striped">

									<transition name="fade">

								        <div v-if="loading"
										class="overlay">
											<i class="fa fa-refresh fa-spin"></i>
								        </div>

								    </transition>

									<thead>
										<tr>
											<th>Name</th>
											<th>Date Attached</th>
											<th>Attached By</th>
											<th><center>Actions</center></th>
										</tr>								
									</thead>

									<tbody>

										<template v-for="(list, index) in attachments">
											
											<tr>
												<td>{{ list.name }}</td>
												<td>{{ moment(list.created_at) }}</td>
												<td>{{ list.extra.employee }}</td>
												<td>
													<center>
														<a :href="list.extra.view" 
														target="_blank" class="btn btn-primary btn-xs">
															<span class="fa fa-eye"></span>
														</a>
														<a @click="removeAttachment(list, list.id)" 
														class="btn btn-danger btn-xs">
															<span class="fa fa-times"></span>
														</a>												
													</center>											
												</td>
											</tr>

										</template>

									</tbody>

								</table>						

								<div v-if="lists.length == 0">
									<h5><center>No attachments...</center></h5>
								</div>

							</div>
						</div>

					</div>	

				</div>

				<template v-for="attachment in attachments">
					
					<input type="hidden" name="attachments[]" :value="attachment.id">

				</template>

			</div>

		</div>
	</div>

</template>
<script>
	export default {

		props: [
			'id',
			'header',
			'paginationlimit',

			'fetchurl',
			'addattachmenturl',

			'disabled',
		],

		data: function() {
			return {
				loading: false,
				message: {
					show: 0,
					state: 0,
					label: null,
				},				

				lists: [],
				attachments: [],
				form: null,
				table: null,
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {
				const $this = this;


				this.$nextTick(function() {

					/* Set default vars */
					$this.form = $('#attachmentForm-' + $this.id);
					$this.table = $('#attachmenttable-' + $this.id);

					/* Initialize attachment */
					$this.initAttachment();


					$this.fetch();
				});
			},

			initAttachment: function() {
				var files = $('#formAttachment-' + this.id),
					$this = this;


		        files.change(function(e) {

		        	/* Add in attachment */
		        	$this.addAttachment();
		        });
			},			

			fetch: function() {
				var $this = this;

				/* Check if component is still loading */
				if(!this.fetchurl || this.loading)
					return false;

				this.loading = true;


				/* Fetch the attachments */
				axios.post(this.fetchurl, {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update pagination & data */
							$this.lists = response.data.attachments;

							$this.show();
						}
					});
			},

			run: function(url, type = 0) {
				var $this = this,
					message = '',
					post = {};

				/* Check if still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Check if is attaching */
				switch(type) {
					case 0: 

						post = new FormData($($this.form)[0]);
						message = 'There seems to be a problem uploading your file, File size must be less than 2mb';

					break;
					default: 

						post = { attachment_id: type }; 
						message = 'Unauthorized access';

					break;
				}

				
				/* Update/Delete/Sort the field */
				axios.post(url, post)
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;


							/* Refresh */
							$this.show();


							switch(type) {
								case 0: 

									/* Add in to attachments if temp */
									if(data.attachment) {

										$this.attachments.push(data.attachment);
										$this.loading = false;

									} else {
										$this.fetch();
									}

								break;
								default: 

									/* Remove from attachment if temp */
									if(data.attachment) {

										$this.destroy(data.attachment);
										$this.loading = false;										

									} else {
										$this.fetch();
									}

								break;
							}

							$this.setMessage(data.message, 1);							
						}
					})
					.catch(function(error) {

						$this.setMessage(message, 0, 1);
						$this.loading = false;						
				    });							
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/
			addAttachment: function() {
				this.run(this.addattachmenturl);
			},

			removeAttachment: function(attachment, id) { // console.log(id);
				this.run(attachment.extra.delete, id);
			},

			toggle: function(bool) {

				/* If bool param is available show it */
				if(bool)
					return $(this.table).addClass('in');

				/* Toggle */
				$(this.table).toggleClass('in');
			},

			show: function() {
				this.loading = false;
			},

			destroy: function(id) {
				this.attachments.splice(this.findAttachmentByID(id), 1);
			},			


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/
			setMessage: function(message, state, timer = true) {
				var $this = this;

		        this.message.label = message;
		        this.message.state = state;
				this.message.show = true;

				/* Hide message after */
				if(timer)
					setTimeout(function() {
				        $this.message.show = false;
					}, 10000);
			},

           	findAttachmentByID: function(id) {
           		return this.attachments.map(function(obj) {
           			return obj.id;
           		}).indexOf(id);
           	},				
		},
	}
</script>