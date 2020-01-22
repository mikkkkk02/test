<template>
	
	<th @click="toggle"
	class="column middle-align pointer">
		<slot name="column"></slot>
		<span class="fa darkgray" :class="{
			'fa-caret-up': direction == 1 && name == sort,
			'fa-caret-down': direction == 0 && name == sort,
			'fa-unsorted' : name != sort 
		}"></span>
	</th>

</template>
<script>
	export default {

		props: [
			'name',
			'sort',
			'direction',
			'loading'
		],

		data: function() {
			return {
				//
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

			},


			/*
			|-----------------------------------------------
			| @Methods
			|-----------------------------------------------
			*/			
			toggle: function() { console.log()

				if(this.loading)
					return false;

				if(this.direction == 1) {
					this.$emit('toggle', this.name);
				} else {
					this.$emit('toggle', this.name);
				}
			},
		},
	}
</script>