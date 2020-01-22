<template>

	<div :id="settings.id">
		
		<template v-for="field in fields">

			<template v-if="isSame('textfield', field)">

				<div class="row">
					<div class="form-group col-sm-6">
						<label>{{ field.label }} <span v-if="field.isRequired" class="has-error">*</span></label>
						<input
						:name="'fields['+ field.id + ']'" :placeholder="field.label" :disabled="settings.disabled"
						type="text" class="form-control">
					</div>
				</div>

			</template>
			<template v-else-if="isSame('textarea', field)">

				<div class="row">
					<div class="form-group col-sm-12">
						<label>{{ field.label }} <span v-if="field.isRequired" class="has-error">*</span></label>
						<textarea
						:name="'fields['+ field.id + ']'" :placeholder="field.label" :disabled="settings.disabled"
						class="form-control"></textarea>
					</div>
				</div>

			</template>
			<template v-else-if="isSame('datefield', field)">
				
				<div class="row">
					<div class="form-group col-sm-6">
						<label>{{ field.label }} <span v-if="field.isRequired" class="has-error">*</span></label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input
							:name="'fields['+ field.id + ']'" :data-value="getAnswer(field).value" :placeholder="field.label" :disabled="settings.disabled"
							type="text" class="form-control flatpickr">
						</div>
					</div>
				</div>

			</template>
			<template v-else-if="isSame('time', field)">
				
				<div class="row">
					<div class="form-group col-sm-6">
						<div class="input-group bootstrap-timepicker">
							<input :name="'fields['+ field.id + ']'" :placeholder="field.label" :disabled="settings.disabled" :data-value="getAnswer(field).value"
				            type="text" class="form-control timepicker" >
		                    <div class="input-group-addon">
								<i class="fa fa-clock-o"></i>
		                    </div>			            
				        </div>
					</div>
				</div>

			</template>			
			<template v-else-if="isSame('radiobox', field) || isSame('checkbox', field)">

				<div class="row">
					<div class="form-group col-sm-12">
						<label>{{ field.label }} <span v-if="field.isRequired" class="has-error">*</span></label>
						<div :class="{
								'radio': isSame('radiobox', field),
								'checkbox': isSame('checkbox', field),
							}">

							<template v-for="option in field.options">

								<label class="col-sm-3">
									<input 
									:name="'fields['+ field.id + '][]'" 
									:type="isSame('radiobox', field) ? 'radio' : 'checkbox'"
									:value="option.id"
									:disabled="settings.disabled">
									{{ option.value }}
								</label>

							</template>

							<label v-show="field.hasOthers"
							class="col-sm-3">
								<input
								value="on"
								:name="'fields['+ field.id + '][]'" 
								:type="isSame('radiobox', field) ? 'radio' : 'checkbox'"
								:disabled="settings.disabled">
								Others
								<input 
								:name="'fields['+ field.id + '.others]'"
								:value="getAnswer(field, 1).value_others"
								:disabled="settings.disabled"
								class="m-margin-l" type="textfield">
							</label>							

						</div>
					</div>
				</div>

			</template>
			<template v-else-if="isSame('dropdown', field)">

				<div class="row">
					<div class="form-group col-sm-6">
						<label>{{ field.label }} <span v-if="field.isRequired" class="has-error">*</span></label>
						<select
						:name="'fields['+ field.id + ']'" :disabled="settings.disabled"
						class="form-control">

							<template v-for="option in field.options">
								
								<option :value="option.value" :selected="getAnswer(field).value == option.value">{{ option.value }}</option>

							</template>

						</select>
					</div>
				</div>

			</template>
			<template v-else-if="isSame('table', field)">
				
				<div class="row">
					<div class="form-group col-xs-12 table-responsive">
						<label>{{ field.label }} <span v-if="field.isRequired" class="has-error">*</span></label>
						<table class="table table-bordered s-margin-b">
							<thead>
								<tr>

								<template v-for="option in field.options">

									<th>{{ option.value }}</th>
									
								</template>

								</tr>
							</thead>
							<tbody>

								<template v-for="(answer, index) in getTableVar(field)">

									<tr>

										<template v-for="option in field.options">

											<td class="form-group" style="min-width: 120px">

												<template v-if="isSame('textfield', option)">

													<input
													:name="'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']'"
													:value="getTableAnswer(answer, option.id, 'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']')"
													:disabled="settings.disabled"
													text="text" class="form-control btn-border">

												</template>
												<template v-else-if="isSame('datefield', option)">

													<input 
													:name="'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']'"
													:data-value="getTableAnswer(answer, option.id, 'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']')"
													:disabled="settings.disabled"
													type="text" class="form-control flatpickr">

												</template>
												<template v-else-if="isSame('number', option)">

													<input
													:name="'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']'"
													:value="getTableAnswer(answer, option.id, 'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']')"
													:disabled="settings.disabled"
													type="number" class="form-control">

												</template>
												<template v-else-if="isSame('dropdown', option)">

													<select
													:name="'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']'" :disabled="settings.disabled"
													:value="getTableAnswer(answer, option.id, 'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']', 0)"
													class="form-control">

														<template v-for="opt in getOptions(option.type_value, ',')">

															<option :value="opt">{{ opt }}</option>

														</template>

													</select>

												</template>
												<template v-else-if="isSame('time', option)">
													
													<div class="input-group bootstrap-timepicker">
														<input 
														:name="'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']'"
														:data-value="getTableAnswer(answer, option.id, 'fields['+ field.id + ']' + '[' + index +'][' + option.id + ']')"
														:disabled="settings.disabled"
														type="text" class="form-control timepicker">		            
											        </div>

												</template>

											</td>
		
										</template>

									</tr>

								</template>

							</tbody>
						</table>

						<div class="row s-margin" v-show="!noadding">
							<a v-if="!settings.disabled"
							@click="addColumn(field.id)"
							class="link" style="cursor: pointer;">+ Add Row</a>
							<a v-if="table[field.id] && table[field.id].length > 1"
							@click="removeColumn(field.id)"
							class="link m-margin-l" style="cursor: pointer;">- Remove Row</a>							
						</div>

					</div>
				</div>

			</template>
			<template v-else-if="isSame('header', field)">

				<div class="row s-margin-t">
					<div class="form-group col-sm-12 no-margin-b">
						<h4>{{ field.label }}</h4>
					</div>
				</div>				

			</template>	
			<template v-else-if="isSame('paragraph', field)">

				<div class="row">
					<div class="form-group col-sm-12 no-margin-b">
						<pre v-html="field.label"></pre>
					</div>
				</div>				

			</template>								

		</template>

	</div>

</template>
<script>

	import flatpickr from '../../mixins/Flatpickr';

	export default {

		props: [
			'id',
			'disabled',
			'answers',
			'fields',
			'noadding',
		],

		mixins:[ flatpickr ],

		data: function() {
			return {
				loading: false,
				storage: {},

				settings: {
					id: 'form-details',
					disabled: false,
				},

				table: {},
			};
		},

		computed: {},

		mounted: function() {

			/* Set vars */
			this.settings.id = this.id ? this.id : this.settings.id;
			this.settings.disabled = this.disabled ? this.disabled : false;


			this.$nextTick(function() {
				this.init();
				
				this.bindEvents();			
			});	
		},

		methods: {

			init: function() {

				/* Set the answers */
				for(var i = 0; i < this.fields.length; i++) { // console.log(this.fields[i]);
					const field = this.fields[i];
					const answer = this.getAnswer(this.fields[i]);
					let input = null, inputOthers = null;


					switch(field.extra.type) {
						case 'Textfield': case 'Datefield': case 'Time':

							input = $('#' + this.settings.id + ' ' + 'input[name="fields['+ field.id + ']"]');  //console.log(input);

							$(input).val(answer.value);

						break;
						case 'Textarea':

							input = $('#' + this.settings.id + ' ' + 'textarea[name="fields['+ field.id + ']"]'); // console.log(input);

							$(input).val(answer.value);

						break;
						case 'Checkbox':

							input = $('#' + this.settings.id + ' ' + 'input[name="fields['+ field.id + '][]"][value="on"]'); // console.log(input);
							inputOthers = $('#' + this.settings.id + ' ' + 'input[name="fields['+ field.id + '.others]"]'); // console.log(inputOthers);

							$(input).prop('checked', this.isBoxSelected(field, 'on'));
							$(inputOthers).val(this.getAnswer(field).value_others);

						case 'Radiobox': 

							for(let f = 0; f < field.options.length; f++) { // console.log(field.options[f]);
								const option = field.options[f];

								input = $('#' + this.settings.id + ' ' + 'input[name="fields['+ field.id + '][]"][value="' + option.id + '"]');
								// console.log(input);

								$(input).prop('checked', this.isBoxSelected(field, option.id));
							}

						break;
						case 'Table':

							if(!answer.length) {
								this.createTableRow(field.id, {});
							} else {
								for(let f = 0; f < answer.length; f++) { //console.log(answer.length);
									this.createTableRow(field.id, answer[f]); //console.log(this.field[field.id]);
								}
							}

						break;
					}
				}
			},

			bindEvents: function() {
				/* Initialize datepicker */
	    		this.$nextTick(() => {
		    		this.flatpickr.init('.flatpickr');				
		    		this.flatpickr.init('.timepicker', true, true);				
	    		});

	    		/* Initialize timepicker */
	    		// $('.timepicker').timepicker({ autoclose: true });
			},


			/*
			|-----------------------------------------------
			| @Methods
			|-----------------------------------------------
			*/
			createTableRow: function(id, parsed = null) { //console.log(id + ' : ' + this.table[id]);

				/* Return row if it already has one */
				if(this.table[id]) {

					/* Set answer */
					if(parsed)
						this.table[id].push(parsed);
			
					return this.table[id];
				}

				/* Create temp table row*/
				this.$set(this.table, id, []);

				/* Set answer */
				if(parsed)
					this.table[id].push(parsed);

				return this.table[id];
			},

			addColumn: function(id) {

				this.table[id].push({});

				this.$nextTick(function() {

					/* Rebind newly created inputs */
					this.bindEvents();
				});
			},

			removeColumn: function(id) {
				this.table[id].splice(this.table[id].length - 1, 1);

				this.$nextTick(function() {

					/* Rebind newly created inputs */
					this.bindEvents();
				});
			},			


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/
			getAnswer: function(field, getObject = false) {
				for(let answer of this.answers) {

					/* Find form field answer */
					if(answer.form_template_field_id == field.id) {

						/* Check if no parsing is needed */
						if(getObject)
							return answer;

						try {

							/* Check if answer is a JSON string */
							var parsed = JSON.parse(answer.value);	

							/* If field is object, return object */
							if(parsed && typeof parsed === "object") {
					            return parsed;
				            }

						} catch(e) { 
							return answer; 
						}

						return answer;
					}
				}

				return {};
			},

			getTableVar: function(field) { //console.log('Get Table Var');
				return this.createTableRow(field.id);
			},

			getTableAnswer: function(obj, id, field, isInput = true) {
				return obj[id] ? obj[id] : $('#' + this.settings.id + ' ' + (isInput ? 'input' : 'select') + "[name='" + field + "']").val();
			},

			getOptions: function(string = '', delimeter = ',') {
				return string.toString().split(delimeter);
			},

			isOption: function(a = '', b = '') {
				return a.trim() == b.trim();
			},

			isBoxSelected: function(field, id) { 

				var answer = this.getAnswer(field);

				/* Check if not an array */
				if(jQuery.isEmptyObject(answer) || !(answer instanceof Array))
					return false;


				return answer.filter(function(obj) {
					return obj == id;
				}).length > 0 ? true : false;
			},

			isTableRowExist: function(id) {
				return this.table.id;
			},		

			isSame: function(string, field) {

				/* Check field type */
				if(field.extra.type && field.extra.type.toLowerCase() == string.toLowerCase())
					return true;

				return false;
			},
		},
	}
</script>