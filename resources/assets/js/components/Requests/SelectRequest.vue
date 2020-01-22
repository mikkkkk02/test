<template>

    <div class="modal-content">

        <div class="modal-header center-align">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title upcase">{{ header ? header : 'Add Request' }}</h4>
			<h6 v-show="loading">Please wait...</h6>
        </div>

        <div class="modal-body">
        	<div class="row">
        		<div class="col-sm-12">
				  <ul class="list-unstyled multi-steps">
				    <li :class="step > 0 ? 'is-active' : ''" @click.prevent="stepBtn(1, true)">
				    	<span>1</span>
				    </li>
				    <li :class="step > 1 ? 'is-active' : ''" @click.prevent="stepBtn(2, true)">
				    	<span>2</span>
					</li>
				    <li :class="step > 2 ? 'is-active' : ''" @click.prevent="stepBtn(3, true)">
				    	<span>3</span>
				    </li>
				  </ul>
        		</div>
        	</div>
			<div class="row margin">
				<div class="col-sm-12">

					<div class="row">

						<div v-show="step === 1">
							<div class="form-group center-align">
								<label for="assigneeList" class="control-label">
									{{ content ? content : 'Select which request you would like to make:' }}
								</label>
								<!-- <p></p> -->
							</div>
							<div class="col-sm-12 flex-center">
								
								<div class="form-group col-sm-4 no-padding center-align">
									<select id="template" required v-model="template_id"
									class="form-control chosen-select" data-placeholder="-">

										<template v-for="type in types">

											<optgroup :label="type.label">

												<template v-for="template in type.templates">

													<option :value="template.id">{{ template.name }}</option>

												</template>

											</optgroup>

										</template>

									</select>
								</div>

							</div>

							<div>
								<template v-for="type in types">
									<template v-for="template in type.templates">

										<template v-if="template.id == template_id">
											<div class="left-align" v-show="template.policy">
												<h4>Policy/Procedure</h4>
												<p v-html="template.policy"></p>
											</div>
											<div class="left-align" v-show="template.sla_text">
												<h4>SLA</h4>
												<p v-html="template.sla_text"></p>
											</div>
										</template>

									</template>
								</template>
							</div>

						</div>

						<div v-show="step === 2">

							<div class="col-sm-12">

								<iframe id="requestIframe1" v-if="requesturl" :src="requesturl" style="width: 100%;"></iframe>

							</div>

						</div>

						<div v-show="step === 3">
							<div class="col-sm-12">

								<iframe id="requestIframe2" v-if="requesturl2" :src="requesturl2" style="width: 100%;"></iframe>

							</div>
						</div>
						
					</div>

                </div>
            </div>
		</div>

		<div class="modal-footer center-align">
			<button @click.prevent="stepBtn(-1)"
				type="button" class="btn btn-default">
				{{ step < 2 ? 'Close' : 'Back' }}
			</button>
			<button @click.prevent="stepBtn(1)"
				type="button" class="btn btn-primary">
				{{ step < 3 ? 'Next' : 'Submit' }}
			</button>
			<button @click.prevent="stepBtn(1, false, true)" v-show="step > 2"
				type="button" class="btn btn-default">
				Save as Draft
			</button>
		</div>

		<prxalert ref="requestalert"
		:id="'requestmodal'">
		</prxalert>

    </div>

</template>

<script>
import prxalert from '../Alert.vue';

export default {

	props: [
		'id',
		'header',
		'content',

		'fetchurl',
		'createurl',
		'createurl2',
		'submiturl1',
		'submiturl2',
		'templateid',
	],

	data: function() {
		return {
			loading: false,
			step: 1,

			template_id: 0,
			types: [],
		};
	},

	components: {
		prxalert,
	},

	computed: {

		template: function() {
			return this.getFormTemplateByID(this.template_id);
		},

		requesturl: function() {
			let url = false;

			if (this.template_id) {
				url = this.createurl + '/' + this.template_id;
			}

			return url;
		},

		requesturl2: function() {
			let url = false;

			if (this.template_id) {
				url = this.createurl2 + '/' + this.template_id;
			}

			return url;
		}
	},

	mounted() {
		this.init();
	},

	methods: {

		init() {
			this.fetch();
		},

		setup() {
			/* Initialize chosen */
			this.$nextTick(() => {

				if (this.templateid) {
					this.template_id = this.templateid;
					this.step = 2;
				}

				this.initChosen();
			});
		},

		fetch() {
			axios.post(this.fetchurl)
			.then(response => {
				const data = response.data;

				this.requests = data.templates;
				this.types = data.types;

				this.setup();
			}).catch(error => {
				console.log(error);
			});
		},

		initChosen: function() {
			var $this = this;

			$('.chosen-select').chosen()
				.change(function(event) {

					if(event.target == this) {
						$this.template_id = $(this).val();
					}
				});
		},

		/**
		 * @Methods
		 */
		stepBtn(value, goto = false, isDraft = false) {

			if (this.loading) return;

			const $this = this;
			let step = this.step + value;

			if (goto) {
				step = value;
			}

			let url;
			let data;
			
			this.loading = true;

			console.log(step);

			switch (step) {

				case 0: 
						$('#' + this.id).modal('hide');
					break;

				case 1:

						if (!this.templateid) {
							if (this.template_id) {
								this.step = step;
							} else {
							}
						} else {
							$('#' + this.id).modal('hide');
						}

						this.loading = false;

					break;

				case 2:
				
						if (!this.template_id) {
							this.$refs.requestalert.show('Please select a template', 'No template found', 'error',);
						} else {
							this.step = step;
						}

						this.loading = false;

					break;

				case 3:

						url = this.submiturl1 + '/' + this.template_id;
						data = $('#requestIframe1').contents().find('#requestMultiPartForm1').serialize() + '&save="1';

						axios.post(url, data)
						.then(response => {
							this.step = step;
							this.loading = false;
						}).catch(error => {
							this.$refs.requestalert.show(error);
							this.loading = false;
						});

					break;

				case 4:

						url = this.submiturl2 + '/' + this.template_id;
						data = $('#requestIframe1').contents().find('#requestMultiPartForm1').serialize();
						data = data + '&' + $('#requestIframe2').contents().find('#requestMultiPartForm2').serialize();

						if (!isDraft) {
							data = data + '&save="1"';
						} else {
							data = data + '&draft="1"';
						}

						axios.post(url, data)
						.then(response => {
							let data = response.data;
							this.$refs.requestalert.show(data.message, data.title, 'success', '', data.redirectURL, 'test');
							this.loading = false;
						}).catch(error => {
							this.$refs.requestalert.show(error);
							this.loading = false;
						});

					break;

				default:

					break;
			}
		},


		/*
		|-----------------------------------------------
		| @Helper
		|-----------------------------------------------
		*/
		getFormTemplateByType: function(type) {
			return this.requests.filter(function(obj) {
				return obj.type == type;
			});
		},

		getFormTemplateByID: function(id) {
			return this.requests.filter(function(obj) {
				return obj.id == id;
			})[0];
		},
	},
}
</script>

