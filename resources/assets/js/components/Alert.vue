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
		            	<li v-for="errors in response">
		            		<template v-if="errors.length > 0">
		            			<template v-for="error in errors">
		            				<template v-for="message in error">{{ message }}</template>
		            			</template>
		            		</template>
		            	</li>
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

		show(response, title = '', type = '', icon = '', redirectURL = false) {
			console.log(type);

			const $this = this;
			this.title = title ? title : this.settings.title;
			this.type = type ? type : this.settings.type;
			this.icon = icon;

			if (!icon) {
				switch (type) {
					case 'success': case 1:
							this.icon = 'fa-check'
						break;
					case 'error': case 0:
							this.icon = 'fa-ban';
						break;
					default:
							this.icon = 'fa-ban';
						break;
				}
			}


			if (response.response) {
				switch (response.response.status) {
					case 422:
							this.response = response.response.data;
						break;
					default:
							this.response = response.response.statusText;
						break;
				}
			}

			if (typeof response === 'string') {
				this.response = response;
			}



			Vue.nextTick(() => {
				this.modal.modal('show');
				this.modal.on('hidden.bs.modal', () => {

					if (redirectURL) {
						window.location.href = redirectURL;
					}
					
				});
			});
		},

		close() {
			this.modal.modal('hide');
		}
	},
}
</script>