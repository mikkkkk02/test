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
						<div class="form-group">
							<p class="help-block">{{ text }}</p>
						</div>

	                </div>
                </div>

			</div>

			<div class="modal-footer">
				<button @click="importFile()" :disabled="loading"
				class="btn btn-primary pull-left">Upload</button>
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

			'importurl',
		],

		data: function() {
			return {
				loading: false,
				tab: 0,

				form: null,
				overwrite: 0,

				message: {
					show: 0,
					state: 0,
					label: null,
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

				formData.append("attachment", imagefile.files[0]);
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

							$this.setMessage(data.message, data.response);

							$this.show();
						}
					}).catch(function(error) {

						$this.setMessage('The attachment must be a pdf or excel file', 0);

						$this.reset();
						$this.show();
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
				this.clearInput($('#importFile'));
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