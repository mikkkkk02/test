<template>

	<div class="form-group col-sm-6">
		<label>{{ label }}</label>
		<input :placeholder="label" :disabled="!editable" v-model="formattedValue" 
		@blur="onHover = false" @focus="onHover = true"
		type="text" class="form-control" required>
		<input :name="name"
		type="hidden" :value="rawValue">
	</div>			

</template>
<script>
	export default {

		props: [
			'label',
			'value',
			'editable',
			'name',
		],

		data: function() {
			return {
				onHover: false,
			};
		},

		computed: {
			formattedValue: {
	            get: function() {

	                if(this.onHover) {

	                    /* Remove format on hover */
	                    return this.value.toString();

	                } else {

	                    /* Format w/ PH currency */
	                    return 'P ' + this.value.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")
	                }
	            },
	            set: function(modifiedValue) {

	            	/* Remove currency symbol and commas */
	                let newValue = parseFloat(modifiedValue.replace(/[^\d\.]/g, ""));
	                

	                if(isNaN(newValue))
	                    newValue = 0

	                /* Update value */
	                this.$emit('input', newValue);
	            },
			},			
			rawValue: function() {
				return this.value.toString();
			},
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {},
		},
	}
</script>