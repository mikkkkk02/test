<template>
	<div>
		<div class="row">
			<div class="col-sm-12">
				<form id="tickettravelorderdetails" class="relative" action="#" @submit.prevent="submit()">
					<div class="box-header">
						<i class="fa fa-map-pin"></i>
						<h3 class="box-title">{{ active.id ? 'Update' : 'Add' }} Travel Details</h3>
					</div>
					<div class="row">
						<div class="col col-sm-4">
							<div class="form-group">
								<label>Accommodation <span class="has-error">*</span></label>
								<input class="form-control" type="text" name="accommodation"
								v-model="active.accommodation">
							</div>
						</div>

						<div class="col col-sm-4">
							<div class="form-group">
								<label>Transportation Type <span class="has-error">*</span></label>
								<input class="form-control" type="text" name="transportation_type"
								v-model="active.transportation_type">
							</div>
						</div>

						<div class="col col-sm-4">
							<div class="form-group">
								<label>Remarks/Others</label>
								<input class="form-control" type="text" name="remarks"
								v-model="active.remarks">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col col-sm-12">
							<div class="form-group">
								<label>Details <span class="has-error">*</span></label>
								<textarea class="form-control" name="details"
								v-model="active.details">
								</textarea>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<button class="btn btn-primary" type="submit" :disabled="loading">Submit</button>
						</div>
					</div>

					<transition name="fade">

				        <div v-if="loading"
						class="overlay">
							<i class="fa fa-refresh fa-spin"></i>
				        </div>

				    </transition>
				</form>

				<prxalert ref="ticketalert"
				:id="'ticketmodal'">
				</prxalert>
			</div>
		</div>

	</div>
</template>

<script>

import { ebi } from '../../EventBus.js';
import prxalert from '../Alert.vue';

export default {
	props: [
		'submiturl',
		'disabled',
	],

	components: {
		prxalert,
	},

	data() {
		return {
			travelForm: null,			

			loading: false,
			lists: [],

			active: {},
		}
	},

	mounted() {
		this.init();

		ebi.$on('setActiveTravelDetails', (list) => {
			this.active = list;
		});
	},

	methods: {

		init() {

			/* Set default variables */
			this.travelForm = $('#tickettravelorderdetails');
		},

		/*
		|-----------------------------------------------
		| @Controller
		|-----------------------------------------------
		*/	
		submit() {
			if(this.loading) 
				return false;

			this.loading = true;

			const data = this.travelForm.serialize();


			let url = this.active.id ? this.active.extra.updateurl : this.submiturl;

			axios.post(url, data)
				.then(response => {

					let type;

					this.$refs.ticketalert.show(response.data.message, response.data.title, response.data.response, false, false);

					ebi.$emit('travelDetailsFetch');

					this.resetAll();
					this.loading = false;

				}).catch(error => {

					this.$refs.ticketalert.show(error);

					this.loading = false;
				});
		},

		resetAll() {
			this.active = {};
		},


		/*
		|-----------------------------------------------
		| @Helper
		|-----------------------------------------------
		*/
		// 
	},
}
</script>