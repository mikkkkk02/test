<template>

	<div class="modal-dialog">
        <div class="box modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{{ header }}</h4>
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

						<transition name="fade">			

							<label v-show="message.show" :class="{
								'has-success': message.state,
								'has-warning': !message.state,
							}"
							class="form-group">
			                	<label class="control-label" for="inputSuccess">
			              			<i :class="{
			              				'fa-check': message.state,
			              				'fa-times': !message.state,
			              			}"
			              			class="fa"></i> {{ message.label }}
			              		</label>
			                </label>

						</transition>

		                <div class="form-group">
							<label for="importFile">File input</label>

							<input :disabled="loading"
							type="file" id="importFile">

		                </div>
			            <div v-if="!settings.nooverwrite"
			            class="form-group">
							<div class="checkbox no-margin-t no-margin-b">
								<label>
									<input v-model="overwrite"
									type="checkbox" name="overwrite" value="1">Overwrite/Update any existing data
								</label>
							</div>
						</div>
						<div class="form-group">
							<p class="help-block">{{ text }}</p>
						</div>		                

						<div v-if="response.success.length > 0 || response.errors.length > 0" 
						class="box box-widget nav-tabs-custom no-margin-b">
							<ul class="nav nav-tabs">
								<li class="active">
									<a @click="tab == 0"
									href="#import-success" data-toggle="tab">Log</a>
								</li>					
								<li>
									<a @click="tab == 1"
									href="#import-errors" data-toggle="tab">Error ({{ response.errors.length }})</a>
								</li>					
							</ul>
							<div class="tab-content">
								<div :class="{ active: !tab }"
								class="tab-pane" id="import-success">
	
									<div class="form-group log-container">
										<div class="log-container-b">

										<template v-for="text in response.success">
												
											<p v-html="text"></p>

										</template>

										<template v-if="response.success.length > 0">
											
											<br>
											<div class="form-group has-success">
												<p>
													<b>
														<label>Successfully updated {{ response.success.length }} record(s)</label>
													</b>
												</p>
											</div>

										</template>

										</div>
									</div>

								</div>
								<div :class="{ active: tab }"
								class="tab-pane" id="import-errors">
	
									<div class="log-container">
										<div class="log-container-b">
										
										<template v-for="text in response.errors">
												
											<p v-html="text"></p>

										</template>

										<template v-if="response.errors.length > 0">

											<br>
											<div class="form-group has-error">
												<p>
													<b>
														<label>Fail to update {{ response.errors.length }} record(s)</label>
													</b>
												</p>
											</div>

										</template>

										</div>
									</div>

								</div>								
							</div>
						</div>		

	                </div>
                </div>

			</div>

			<div class="modal-footer">
				<button @click="importFile()" :disabled="loading"
				class="btn btn-primary pull-left">{{ buttontext ? buttontext : 'Import' }}</button>
				<a class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancel</a>
			</div>
			
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->		

</template>
<script>
	export default {

		props: [
			'header',
			'text',
			'buttontext',

			'nooverwrite',

			'importurl',
		],

		data: function() {
			return {
				loading: false,
				tab: 0,

				form: null,
				overwrite: 0,

				settings: {
					nooverwrite: false,
				},

				message: {
					show: 0,
					state: 0,
					label: null,
				},
				response: {
					success: [],
					errors: [],
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
				this.form = $('#importForm');
				this.settings.nooverwrite = this.nooverwrite;
			},


			/*
			|-----------------------------------------------
			| @Methods
			|-----------------------------------------------
			*/
			importFile: function() {
				var $this = this;

				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Set post vars */
				var formData = new FormData();
				var imagefile = document.querySelector('#importFile');

				formData.append("file", imagefile.files[0]);
				formData.append("overwrite", $this.overwrite);


				/* Import the file */
				axios.post(this.importurl, formData, {
					    headers: {
					      'Content-Type': 'multipart/form-data'
					    }
					}).then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;

							$this.response = data.logs;
							$this.setMessage(data.message, data.response);

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
				this.loading = false;

				this.reset();
			},

			reset: function() {

				/* Clear file input */
				this.clearInput($('#importFile'))
			},	


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
				if(state)
					setTimeout(function() {
				        $this.message.show = false;
					}, 1500);
			},		

			clearInput: function(input) {
				input.wrap('<form>').closest('form').get(0).reset();
				input.unwrap();
			},
		},
	}
</script>