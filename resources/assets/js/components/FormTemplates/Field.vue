<template>

	<div class="formtemplate-field">
		
		<div class="row m-margin-t">
			<div class="form-group col-sm-12 no-padding no-margin">
				<label>{{ field.extra.type }}</label>

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

				<div class="col-sm-12 no-padding">

					<div v-if="!isSame('header', field) && !isSame('paragraph', field)"
					class="col-sm-3 no-padding">

						<input v-model="field.label"
						type="text" class="form-control">

					</div>
					<div v-else
					class="col-sm-6 no-padding">
						
						<input v-if="isSame('header', field)"
						v-model="field.label"
						type="text" class="form-control">

						<textarea v-else-if="isSame('paragraph', field)"
						v-model="field.label"
						type="text" class="form-control">
						</textarea>

					</div>

					<div>
						<a @click="sortUp()" :disabled="loading"
						class="btn btn-primary btn-xs s-margin-l">
							<span class="fa fa-caret-up"> </span>
						</a>
						<a @click="sortDown()" :disabled="loading"
						class="btn btn-primary btn-xs">
							<span class="fa fa-caret-down"> </span>
						</a>							
						<a @click="update()" :disabled="loading"
						class="btn btn-success btn-xs s-margin-l">

							<span :class="{
								'fa-refresh fa-spin' : loading && (state == 3),
								'fa-save' : state != 3,
							}"
							class="fa"> </span>

						</a>
						<a @click="destroy()" :disabled="loading"
						class="btn btn-danger btn-xs">

							<span :class="{
								'fa-refresh fa-spin' : loading && (state == 4),
								'fa-times' : state != 4,
							}"
							class="fa"> </span>

						</a>
					</div>
				</div>
			</div>
			<div v-if="!isSame('header', field) && !isSame('paragraph', field)"
			class="form-group col-sm-12 no-margin-b">
				<div class="checkbox">
					<label class="m-padding-r">
	        			<input v-model="field.isRequired" :disabled="loading"
	        			type="checkbox" value="1"> Toggle field as required
					</label>
					<label v-if="(isSame('radiobox', field) || isSame('checkbox', field))">
	        			<input v-model="field.hasOthers" :disabled="loading"
	        			type="checkbox" value="1"> Add in Others option
					</label>
				</div>
			</div>
		</div>

		<template v-for="option in sortedOptions">
				
			<div class="row s-margin-b">

				<template v-if="isSame('table', field)">

					<div class="col-xs-5 no-padding">
						<input v-model="option.value" :disabled="loading"
						type="text" name="column" placeholder="Column name" class="form-control">
					</div>
					<div class="col-xs-5 no-padding s-margin-l">
						<div class="form-group no-margin">
							<select v-model="option.type" :disabled="loading"
							class="form-control">
								<option value="0">Text</option>
								<option value="1">Date</option>
								<option value="2">Number</option>
								<option value="3">Dropdown</option>
								<option value="4">Time</option>
							</select>
						</div>
					</div>

				</template>
				<template v-else>
	
					<div class="col-xs-5 no-padding">
						<div class="input-group">
							<span class="input-group-addon">
								<input type="checkbox">
		                    </span>
							<input v-model="option.value" :disabled="loading"
							type="text" class="form-control">
						</div>
					</div>

				</template>				

				<div>
					<a @click="removeColumn(option.id)" :disabled="loading"
					class="btn btn-danger btn-xs s-margin-l">
						<span class="fa fa-times"></span>
					</a>
				</div>							
			</div>
			<div v-if="option.type == 3"
			class="row s-margin-b">
	
				<div class="col-xs-5 no-padding">
					<input v-model="option.type_value" :disabled="loading"
					type="text" name="type_value" placeholder='Dropdown Options (Separate by comma ",")' class="form-control">
					</label>
				</div>

			</div>

		</template>

		<template v-if="isSame('radiobox', field) || isSame('checkbox', field) || isSame('table', field) || isSame('dropdown', field)">

			<div class="row s-margin-t">

				<template v-if="isSame('table', field)">
			
					<div v-if="adding.show"
					class="col-xs-5 no-padding">
						<input v-model="newfield.value" :disabled="loading"
						type="text" name="column" placeholder="Column name" class="form-control">
					</div>
					<div v-if="adding.show"
					class="col-xs-5 no-padding s-margin-l">
						<div class="form-group no-margin">
							<select v-model="newfield.type" :disabled="loading"
							class="form-control">
								<option value="0">Text</option>
								<option value="1">Date</option>
								<option value="2">Number</option>
								<option value="3">Dropdown</option>
								<option value="4">Time</option>
							</select>
						</div>
					</div>					
	
				</template>
				<template v-else>
						
					<div v-if="adding.show"
					class="col-xs-5 no-padding">
						<div class="input-group">
							<span class="input-group-addon">
								<input type="checkbox">
		                    </span>
							<input v-model="newfield.value" :disabled="loading"
							type="text" class="form-control">
						</div>
					</div>			

				</template>

				<div v-if="adding.show">
					<a @click="addOption()" :disabled="loading"
					class="btn btn-primary btn-xs s-margin-l">

						<span :class="{
							'fa-refresh fa-spin' : loading && (state == 5),
							'fa-plus' : state != 5,
						}"
						class="fa"> </span>

					</a>
				</div>				

			</div>

			<div class="row s-margin-tb">
				<a @click="addColumn()"
				class="link" style="cursor: pointer;">+ {{ adding.show ? 'Hide' : 'Add Column' }}</a>
			</div>			

		</template>

	</div>

</template>
<script>
	export default {

		props: [
			'field',
			'types',
			'tabletypes',			
		],

		data: function() {
			return {
				loading: false,
				message: {
					show: 0,
					state: 0,
					label: null,
				},
				adding: {
					show: 0,
				},
				newfield: {
					value: '',
					type: 0,					
				},
				state: 0,
			};
		},

		computed: {
			sortedOptions: function() {
				return orderBy(this.field.options, ['sort']);
			},			
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {},

			run: function(url, post = {}) {
				var $this = this;

				/* Check if still loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Update/Delete/Sort the field */
				axios.post(url, post)
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data, 
								status = 1;

							switch(data.response) {
								case 3: console.log(data.status);

									status = data.status;

								break;
								case 4: 

									status = data.status;

									if(status) {
										$this.$emit('field-was-deleted', { id: parseInt(data.id) }); 

										return $this.reset();
									}

								break;
								case 5:

									if($this.field.options == undefined)
										$this.$set($this.field, 'options', []);

									$this.field.options.push(data.option);

								break;
								case 6:

									$this.field.options.splice($this.getOptionByID(parseInt(data.option)), 1);

								break;
							}

							$this.setMessage(data.message, status);
							$this.reset();
						}
					})
					.catch(function(error) { //console.log(error.response);
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
				    });		
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/
			sortUp: function() {
				this.state = 1;

				this.$emit('field-was-sortedup', { id: this.field.id });
			},

			sortDown: function() {
				this.state = 2;

				this.$emit('field-was-sorteddown', { id: this.field.id });
			},

			update: function() {
				this.state = 3;

				this.run(this.field.extra.updateurl, this.field);
			},

			destroy: function() {
				this.state = 4;

				this.run(this.field.extra.deleteurl);
			},

			addOption: function() {
				this.state = 5;

				this.run(this.field.extra.optionurl, this.getOptionPost());
			},			

			addColumn: function() {
				this.adding.show = !this.adding.show;
			},

			removeColumn: function(id) { // console.log(id);
				this.state = 6;

				this.run(this.field.extra.optionremoveurl, { form_template_option_id: id });
			},

			reset: function() {

				/* Reset loading & state variable */
				this.loading = false;
				this.state = 0;

				this.newfield.value = '';
				this.newfield.type = 0;
				this.adding.show = 0;
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

				if (!state) {
					this.$store.commit('fields/setErrors', {
						message: this.message.label
					});
				}

				/* Hide message after */
				setTimeout(function() {
			        $this.message.show = false;
				}, 10000);
			},

			getOptionPost: function() {
				return {
					value: this.newfield.value,
					type: this.newfield.type,
				};
			},

			getOptionByID: function(id) {
				return this.field.options.map(function(obj) {
					return obj.id;
				}).indexOf(id);
			},

			isSame: function(string, field) {

				/* Check field type */
				if(field.extra.type && field.extra.type.toLowerCase() == string.toLowerCase())
					return true;

				return false;
			},

			isArray: function(obj) {
			    return Object.prototype.toString.call(obj) === '[object Array]';
			},			
		},
	}
</script>