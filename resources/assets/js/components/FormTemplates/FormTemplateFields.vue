<template>

	<div class="box box-widget box-body">

		<transition name="fade">

	        <div v-if="loading"
			class="overlay">
				<i class="fa fa-refresh fa-spin"></i>
	        </div>

	    </transition>

		<div class="col-sm-12">

			<div class="row">
				<div class="form-group">
					<label for="assigneeList" class="control-label">Choose field type</label>
					<p><!-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --></p>
				</div>
			</div>

			<transition name="fade">

				<label v-show="message.show" :class="{
					'has-success': message.state,
					'has-warning': !message.state,
				}"
				class="form-group no-margin-l">
                	<label class="control-label" for="inputSuccess">
              			<i :class="{
              				'fa-check': message.state,
              				'fa-times': !message.state,
              			}"
              			class="fa"></i> {{ message.label }}
              		</label>
                </label>

			</transition>	
	
			<!-- Choose field -->
			<div class="row">
				<div class="form-group col-sm-5">
					<input v-model="field.label"
					type="text" placeholder="Fieldname" class="form-control">
				</div>
				<div class="form-group col-sm-5">
					<select v-model="field.type"
					class="form-control">

						<template v-for="type in types">
							
							<option :value="type.value">{{ type.label }}</option>

						</template>

					</select>
				</div>
			</div>	
			<div class="row">
                <div class="form-group col-sm-10">
                	<div class="checkbox">
                    	<label>
                        	<input v-model="field.isRequired"
                        	type="checkbox"> Toggle field as required
                    	</label>
                	</div>
                </div>				
			</div>

			<div class="row">
				<div class="form-group">
					<a @click="add()"
					class="btn btn-primary" data-toggle="modal" data-target="#view-addfield">Add Field</a>
				</div>
			</div>		

			<div class="row">
				<div class="form-group">
					<label for="assigneeList" class="control-label">Preview</label>
					<p><!-- Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt --></p>
				</div>
			</div>

			<template v-for="(field, index) in sortedFields">

				<field :ref="'field' + index"
				:field="field"
				@field-was-sortedup="sortUp"
				@field-was-sorteddown="sortDown"
				@field-was-deleted="destroy"></field>	

			</template>

			<div v-if="fields.length"
			class="row">
				<div class="m-margin-t">
					<a @click="saveAll()"
					class="btn btn-primary">Save All</a>
				</div>
			</div>				

		</div>
		
		<prxalert ref="fieldalert"
		:id="'fieldalert'">
		</prxalert>

	</div>

</template>
<script>

	import prxalert from '../AlertField.vue';
	import field from './Field.vue';

	export default {

		props: [
			'types',
			'autofetch',
			'addurl',
			'updateurl',
			'fetchurl',
		],

		components: {
			field,
			prxalert,
		},

		data: function() {
			return {
				loading: false,

				fields: [],
				field: {
					label: '',
					type: 0,
					type_value: 0,
					isRequired: false,
				},

				message: {
					show: 0,
					state: 0,
					label: null,
				},
				settings: {
					autofetch: false,
					initialfetch: false,
				},				
			};
		},

		computed: {
			sortedFields: function() {
				return orderBy(this.fields, ['sort']);
			},			
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Set method overwrites */
				this.overwrites();

				this.settings.autofetch = this.autofetch ? true : false;


				/* Check if auto fetch is enable */
				if(this.settings.autofetch)
					this.fetch();				
			},

			fetch: function() {
				this.run(this.fetchurl);
			},

			sort: function(post) {

				/* Update the sorting */
				this.run(this.updateurl, 1, { sorting: post });
			},

			run: function(url, state = 0, post = {}) {
				var $this = this;

				/* Check if loading */
				if(this.loading)
					return false;

				this.loading = true;


				/* Fetch form template fields */
				axios.post(url, post)
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {
							var data = response.data;

							switch(state) {
								case 0: $this.fields = data.lists; break;
								case 1: break;
								case 2: 

									$this.fields.push(data.field); 

									$this.setMessage(data.message, 1);

								break;
							}
							
							$this.reset();
							$this.show();
						}
					}).catch(function(error) {

						$this.setMessage('Please input the value', 0);

						$this.reset();
						$this.show();
				    });
			},

           	overwrites: function() {
				Array.prototype.move = function(from, to) {
				    this.splice(to, 0, this.splice(from, 1)[0]);
				};           		
           	},			


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/
			saveAll: function() { 
				for(var i = this.fields.length - 1; i >= 0; i--) {
					let comp = this.$refs['field' + i];

					/* Check if component exists */
					if(comp) {
						console.log(comp[0].update());
					}
				};

				setTimeout(() => {
					this.$refs.fieldalert.show();
				}, 1500);
			},

			add: function() {
				this.run(this.addurl, 2, this.field);
			},

			destroy: function(element) {
				this.fields.splice(this.findFieldByID(element.id), 1);

				this.updateSorting();
			},

			sortUp: function(element) {

				var fieldIndex = this.findFieldByID(element.id),
					nextIndex = fieldIndex - 1;

				/* Check if sort no. */
				if(nextIndex < 0)
					return false;


				this.fields.move(fieldIndex, nextIndex);

				/* Update the field sorting */
				this.sort(this.updateSorting());
			},

			sortDown: function(element) {

				var fieldIndex = this.findFieldByID(element.id), 
					nextIndex = fieldIndex + 1;

				/* Check if sort no. */
				if(nextIndex > this.fields.length)
					return false;


				this.fields.move(fieldIndex, nextIndex);

				/* Update the field sorting */
				this.sort(this.updateSorting());
			},

			updateSorting: function() {
				var post = [];

				/* Loop & update the sort variable */
				for(var i = 0; i < this.fields.length; i++) {

					post.push(this.fields[i].sort);

					this.fields[i].sort = i;
				}

				return post;
			},

			show: function() {
				this.loading = false;

				if(!this.settings.initialfetch)
					this.settings.initialfetch = true;
			},

			reset: function() {

				/* Reset new field inputs */
				this.field.label = '';
				this.field.type = 0;
				this.field.isRequired = 0;
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
				setTimeout(function() {
			        $this.message.show = false;
				}, 1500);
			},           

           	isInitialFetch: function() {
           		return this.settings.initialfetch;
           	},

           	findFieldByID: function(id) {
           		return this.fields.map(function(obj) {
           			return obj.id;
           		}).indexOf(id);
           	},	
		},
	}
</script>