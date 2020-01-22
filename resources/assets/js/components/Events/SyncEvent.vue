<template>



</template>
<script>
	export default {

		props: [
			'client',
			'secret',
		],

		data: function() {
			return {

			};
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() { console.log(this.gapi);
				//
			},
		},
	}
</script>