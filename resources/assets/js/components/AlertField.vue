<template>
	<div :id="id" class="modal fade" tabindex="-1">
	    <div class="modal-dialog">
	        <div class="alert alert-dismissible" :class="'alert-' + type">

	            <h4 class="alert-title">
					<i class="icon fa" :class="icon"></i>
					{{ title }}
	            </h4>
	            <!-- /.modal-header -->

	            <div class="alert-body">
	            	<template v-if="!showList">
	            		<p>{{ response }}</p>
	            	</template>
	            	<template v-else>
		            	<li v-for="error in response">{{ error.message }}</li>
	            	</template>
	            </div>
	            <!-- /.modal-body -->

	            <div class="alert-footer center-align m-margin-t">
		            <button type="button" class="btn btn-primary" @click.prevent="close()">OKAY</button>
				</div>

	        </div>
	    </div>
	</div>
</template>

<script>
export default {
	props: [
		'id',
	],

	data() {
		return {
			response: [],

			title: '',
			type: '',
			icon: '',
			url: '',

			settings: {
				title: 'Oops! Something went wrong!',
				type: 'danger',
				icon: 'fa-ban',
				url: false,
			},

			modal: null,
		}
	},

	computed: {
		showList() {
			return typeof this.response !== 'string';
		}
	},

	mounted() {
		this.init();
	},

	methods: {
		init() {
			this.modal = $('#' + this.id);
		},

		show() {
			let errors = this.$store.state.fields.errors;
			if (errors.length > 0) {
				this.title = 'Oops! Something went wrong!';
				this.type = 'danger';
				this.icon = 'fa-ban';
				this.response = errors;
			} else {
				this.title = 'Success!';
				this.type = 'success';
				this.icon = 'fa-check';
				this.response = 'Update Successful';
			}

			if (typeof response === 'string') {
				this.response = response;
			}



			Vue.nextTick(() => {
				this.modal.modal('show');
				this.modal.on('hidden.bs.modal', () => {
					this.$store.commit('fields/clearErrors');
				});
			});
		},

		close() {
			this.modal.modal('hide');
		}
	},
}
</script>