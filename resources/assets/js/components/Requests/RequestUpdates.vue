<template>

	<div class="row">
		<div class="col-sm-12">

			<div class="row margin">

				<transition-group name="fade">

					<div v-for="(list, index) in lists" :key="index"
					class="post">
						<div class="user-block">
							<img class="img-circle img-bordered-sm" :src="list.employee.extra.photo" alt="user image">
							<span class="username">
								<a>{{ list.employee.extra.fullname }}</a>
								<button @click="destroy(list.extra.deleteurl, list.id)" 
								class="pull-right btn btn-xs" style="background: #fff"><i class="fa fa-times"></i></button>
							</span>
							<span class="description">{{ list.created_at }}</span>
						</div>
						<!-- /.user-block -->
						<p>{{ list.description }}</p>
					</div>
					<!-- /.post -->		

				</transition-group>

				<pagination ref="pagination"
				:url="fetchurl"
				:limit="paginationlimit"
				@page-was-clicked="fetch"></pagination>

			</div>

			<div v-if="!settings.noupdate" 
			class="box box-widget row margin">
		
				<transition name="fade">

			        <div v-show="loading"
					class="overlay">
						<i class="fa fa-refresh fa-spin"></i>
			        </div>

			    </transition>

				<div class="box-header">
					<i class="fa fa-comments"></i>
					<h3 class="box-title">Update</h3>
				</div>
				<!-- /.box-header -->
				
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

				<div class="box-body">
					<div class="row col-sm-12">
						<div class="form-group">
							<label>Description</label>
							<textarea v-model="field.description"
							class="form-control" name="description"></textarea>
						</div>
					</div>
				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<button @click="update()"
					class="btn btn-primary s-margin-r">Update</button>
				</div>
				<!-- /.box-footer -->
				
			</div>

		</div>

	</div>	

</template>
<script>

	import pagination from '../Pagination.vue';

	export default {

		props: [
			'paginationlimit',		
			'autofetch',

			'noupdate',

			'addurl',
			'fetchurl',
		],

		components: {
			pagination,
		},

		data: function() {
			return {
				loading: false,
				message: {
					show: 0,
					state: 0,
					label: null,
				},				

				lists: [],
				field: {
					description: '',
				},

				settings: {
					autofetch: false,
					initialfetch: false,
					noupdate: false,
				},
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Create default filter variables */
				this.settings.autofetch = this.autofetch ? true : false;
				this.settings.noupdate = this.noupdate;


				/* Check if auto fetch is enable */
				if(this.settings.autofetch)
					this.fetch();
			},

			fetch: function(url) {
				this.run(url);
			},

			run: function(link = null, post = {}) {
				var $this = this,
					url = link || this.fetchurl;


				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch/Add/Delete the form update */
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
									$this.fetch();

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
						
						$this.setMessage('Please input the description', 0);
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
				this.run(this.addurl, { description: this.field.description });
			},

			destroy: function(url, id) {
				this.run(url, { form_update_id: id });
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

           	isInitialFetch: function() {
           		return this.settings.initialfetch;
           	},

           	findUpdateByID: function(id) {
           		return this.lists.map(function(obj) {
           			return obj.id;
           		}).indexOf(id);
           	},           			
		},
	}
</script>