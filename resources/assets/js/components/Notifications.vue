<template>

	<div>

		<div class="row">
			<div class="col-sm-12 l-margin-b">

				<button v-if="lists.length > 0" :disabled="loading" @click="readAll()"
				class="btn btn-primary">
					<i class="fa fa-check-square s-margin-r"></i>Mark All as Read
				</button>

			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<ul class="timeline">

					<template v-if="lists.length > 0" v-for="list in lists">

						<li>
							<i class="fa" :class="[ list.data.classes ]"></i>

							<div class="timeline-item">
								<span class="time">

									<transition name="fade">

										<small v-if="!list.read_at"
										class="label bg-red s-margin-r">New</small>

									</transition>

									<a v-if="list.data.link" :href="list.data.link"
									class="btn btn-primary btn-xs s-margin-r">{{ list.data.linkLabel ? list.data.linkLabel : 'View' }}</a>

									<i class="fa fa-clock-o"></i> {{ moment(list.created_at) }}
								</span>

								<h3 class="timeline-header" v-html="list.data.text"></h3>

								<div v-if="list.data.body"
								class="timeline-body" v-html="list.data.body"></div>

							</div>
						</li>

					</template>
					<template v-if="lists.length == 0">

						<li>
							<i class="fa fa-bell bg-gray"></i>

							<div class="timeline-item">

								<h3 class="timeline-header"><center>You have no notifications...</center></h3>

							</div>						
						</li>

					</template>
	
					<transition name="fade">

						<li v-if="url">
							<i class="fa fa-plus-circle bg-aqua"></i>

							<div class="transparent timeline-item center-align">

								<button @click="fetch()" :disabled="loading"
								class="btn btn-primary">Load More</button>

							</div>						
						</li>

					</transition>

					<li>
						<i class="fa fa-clock-o bg-gray"></i>
					</li>					

				</ul>
			</div>
		</div>	

	</div>			

</template>
<script>
	export default {

		props: [
			'readallurl',
			'fetchurl',
		],

		data: function() {
			return {
				loading: false,
				url: null,

				lists: [],
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				this.fetch();
			},

			fetch: function() {
				var $this = this;

				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch the notifications */
				axios.post(this.getURL(), {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update pagination & data */
							$this.updateData(response.data.lists);

							$this.show();
						}
					});
			},

			readAll: function() {
				var $this = this;

				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch the notifications */
				axios.post(this.readallurl, {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update all as read */
							$this.markAsRead();

							$this.show();
						}
					});
			},


			/*
			|-----------------------------------------------
			| @Getter/Setter
			|-----------------------------------------------
			*/
			getURL: function() {
				return this.url || this.fetchurl;
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/
			markAsRead: function () {
				for(var list in this.lists) { console.log(list);

					if(!this.lists[list].read_at)
						this.lists[list].read_at = 'Today';
				}
			},

			show: function() {
				this.loading = false;
			},


            /*
            |-----------------------------------------------
            | @Helper
            |-----------------------------------------------
            */
           	updateData: function(lists) { 

				/* Update the notification lists */
				this.lists = this.lists.concat(lists.data);

				/* Update page no. */
				this.url = lists.next_page_url || null;
           	},			
		},
	}
</script>