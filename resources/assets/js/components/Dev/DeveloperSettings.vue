<template>

	<section>

		<div id="secret" class="modal fade" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
				    <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				        	<span aria-hidden="true">&times;</span>
				        </button>
				        <h4 class="no-margin">Developer Settings</h4>
				        <h4 class="modal-title"></h4>
				    </div>

				    <div class="modal-body">

						<div class="row">
							<div class="form-group col-sm-12">
								<label class="control-label">Switch Account </label>
								<select v-model="employee" required 
								class="form-control" name="user_id" data-placeholder="Select here...">

									<option value="0" selected disabled>Choose here...</option>

									<template v-for="employee in employees">

										<option :value="employee.id">{{ employee.extra.fullname }}</option>

									</template>

					            </select>
					        </div>					
						</div>

						<div class="row">
							<div class="form-group col-sm-12">
								<a @click="switchAccount()"
								class="btn btn-primary pull-left">Proceed</a>
							</div>
						</div>				

					</div>
				</div>
			</div>
		</div>

		<div id="secret-alert">
			<div class="secret-content">

				<template v-if="settings.toggle >= settings.toggleCount">

					<p>You have enabled Developer mode!</p>

				</template>
				<template v-else>

					<p>Click the version text {{ settings.toggleCount - settings.toggle }} more time(s) to enable Developer mode.</p>

				</template>

			</div>
		</div>

	</section>

</template>
<script>
	export default {

		props: [
			'triggerName',
			'toggleCount',
			'toggle',


			'switchaccounturl',
			'fetchemployeeurl',
		],

		data: function() {
			return {
				loading: false,

				modal: null,
				trigger: null,
				alert: null,

				settings: {
					triggerName: 'version',
					toggleCount: 10,
					toggle: 0,
				},

				employees: [],
				employee: null,
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Set default variables */
				this.settings.triggerName = this.triggerName ? this.triggerName : this.settings.triggerName;
				this.settings.toggle = this.toggle ? this.toggle : this.settings.toggle;
				this.settings.toggleCount = this.toggleCount ? this.toggleCount : this.settings.toggleCount;

				/* Fetch elements */
				this.modal = $('#secret');
				this.trigger = $('#' + this.settings.triggerName);
				this.alert = $('#secret-alert');


				this.bindEvents();
				this.fetchEmployees();
			},

			bindEvents: function() {
				let $this = this;


				/* Bind click event for the trigger */
				$(this.trigger).on('click', function() {
					$this.settings.toggle++; // console.log($this.settings.toggle)

					/* Check toggle count */
					$this.check();
				});
			},

			check: function() {
				const $this = this;
				let count = this.settings.toggleCount - this.settings.toggle;

				if(count <= 3 && count > 0) {
					$(this.alert).addClass('show');
				} else if(count == 0) {
					setTimeout(function() {
						$($this.alert).removeClass('show');
					}, 1000);
				}

				if(this.settings.toggle >= this.settings.toggleCount)
					$(this.modal).modal();
			},


            /*
            |-----------------------------------------------
            | @Methods
            |-----------------------------------------------
            */
           	switchAccount: function() {
				var $this = this

				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch the groups */
				axios.post(this.switchaccounturl, { 'user_id': this.employee })
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update pagination & data */
							window.location.replace(response.data.redirectURL);

							$this.loading = false;
						}
					});
           	},


            /*
            |-----------------------------------------------
            | @Fetch
            |-----------------------------------------------
            */		
           	fetchEmployees: function() {
				var $this = this

				/* Check if component is still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch the groups */
				axios.post(this.fetchemployeeurl, {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update pagination & data */
							$this.employees = response.data.lists;

							$this.loading = false;
						}
					});           		
           	},
		},
	}
</script>