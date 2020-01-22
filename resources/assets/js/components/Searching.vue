<template>
	
	<div class="row">
		<div class="col-sm-12">
			<div id="benefits-searching" class="box box-widget">
				<div class="benefits-content box-body">
	
					<div class="benefits-header">
						<h1>{{ header }}</h1>
						<div class="benefits-search">
							<input v-model="searchfield" @keyup="checkSearch()" @keyup.enter="forceSearch()"
							type="text" class="form-control" placeholder="Search here">

							<transition name="up">
								
								<div v-if="loading"
								class="overlay">
									<i class="fa fa-refresh fa-spin"></i>
						        </div>

							</transition>

						</div>
					</div>

					<transition name="up">

						<div v-if="onShow" 
						class="benefits-result">
							<div class="box-group">

								<template v-for="(value, key, index) in forms">

									<div class="box-header with-border">
										<h4 class="box-title">
											<a data-toggle="collapse" :href="'#' + key + '-forms'"><b>{{ capitalizeFirstLetter(key) }}</b></a>
										</h4>
									</div>
									<div :id="key + '-forms'" class="panel-collapse collapse in">
										<div class="box-body no-padding-t">
											
											<template v-if="value.length">
												<template v-for="form in value">
													
													<div class="box-group">
														
														<div class="box-header with-border">
															<h5 class="no-margin">
																<a data-toggle="collapse" :href="'#form-' + form.id">{{ form.name }}</a>
															</h5>
															<div :id="'form-' + form.id" class="panel-collapse collapse">
															
																<div class="box-body no-padding">
																	<h5 v-if="form.description"
																	class="control-label bold no-margin-b">Description</h5>
																	<p class="no-margin-b">{{ form.description }}</p>
																	<h5 v-if="form.policy"
																	class="control-label bold no-margin-b">Policy & Procedures</h5>
																	<p class="no-margin-b" v-html="form.policy"></p>
																	<h5 v-if="form.sla_text"
																	class="control-label bold no-margin-b">SLA</h5>
																	<p class="no-margin-b" v-html="form.sla_text"></p>
																</div>

																<a :href="form.extra.create" class="btn btn-xs btn-primary s-margin-t">{{ buttontext }}</a>

															</div>
														</div>

													</div>

												</template>
											</template>
											<template v-else>
												
												<div class="box-header with-border">
													<h6 class="no-margin">No {{ key }} forms found...</h6>
												</div>

											</template>

										</div>
									</div>

								</template>

							</div>
						</div>

					</transition>

				</div>
			</div>
		</div>
	</div>

</template>
<script>
	export default {

		props: [
			'header',
			'buttontext',

			'fetchurl',
		],

		components: {},

		data: function() {
			return {
				loading: false,

				timer: null,
				searchfield: '',
				oldsearchfield: '',

				forms: {},
			};
		},

		mounted: function() {
			this.init();
		},

		computed: {
			onShow: function() {
				return this.forms;
			},
		},

		methods: {

			init: function() {},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/	
			forceSearch: function() {
				this.checkSearch(1);
			},

			checkSearch: function(fire = false) {
				const $this = this;

				/* Check if value is the same */
				if(this.searchfield && this.searchfield == this.oldsearchfield)
					return false;

				/* Clear old timer */
				if(this.timer)
					clearTimeout(this.timer);


				/* Check if fire immediately */
				if(!fire) {

					/* Set timer on a 1 sec delay */
					this.timer = setTimeout(function() {
						$this.oldsearchfield = $this.searchfield;

						$this.loading = true;

						/* Run search */
						$this.search();					

					}, 700);

				} else {
					this.search();
				}
			},

			search: function() {
				const $this = this;


				axios.post($this.fetchurl, { search: $this.searchfield })
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update pagination & data */
							$this.updateData(response.data);

							$this.loading = false;
						}
					});				
			},

			updateData: function(data) {
				this.forms = data.forms;
			},

			show: function() {},


            /*
            |-----------------------------------------------
            | @Helper
            |-----------------------------------------------
            */
			capitalizeFirstLetter: function(string) {
			    return string[0].toUpperCase() + string.slice(1);
			},
		},
	}
</script>