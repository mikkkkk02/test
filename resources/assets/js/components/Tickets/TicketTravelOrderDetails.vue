<template>
	<div>
		<div>
			<table class="table table-bordered travel-details-table">
				<thead slot="thead">
					<tr>
						<th>Accommodation</th>
						<th>Transportation Type</th>
						<th>Details</th>
						<th>Remarks/Others</th>
						<th v-if="!disabled"
						>Actions</th>
					</tr>
				</thead>
				<tr v-for="list in lists" :id="list.id">
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" :value="list.accommodation" name="accommodation" id="accommodation" readonly>
						</div>
					</td>
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" :value="list.transportation_type" name="transportation_type" id="transportation_type" readonly>
						</div>
					</td>
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" :value="list.details" name="details" id="details" readonly>
						</div>
					</td>
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" :value="list.remarks" name="remarks" id="remarks" readonly>
						</div>
					</td>
					<td v-if="!disabled">
						<center style="min-width: 120px; margin: 8px">
							<template v-if="active.id !== list.id">

									<div class="btn btn-primary btn-xs" @click="edit(list)">
										<span class="fa fa-edit"></span>
									</div>

								</template>
								<template v-else>

									<div class="btn btn-warning btn-xs" @click="unedit(list)">
										<span class="fa fa-edit"></span>
									</div>					

							</template>

							<div class="btn btn-danger btn-xs" @click="archive(list)">
								<span class="fa fa-close"></span>
							</div>								
						</center>
					</td>						
				</tr>
				<tr>
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" name="add_accommodation" id="add_accommodation" v-model="active.add_accommodation">
						</div>
					</td>
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" name="add_transportation_type" id="add_transportation_type" v-model="active.add_transportation_type">
						</div>
					</td>
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" name="add_details" id="add_details" v-model="active.add_details">
						</div>
					</td>
					<td>
						<div class="form-group" style="min-width: 120px; margin: 8px">
							<input class="form-control btn-border" name="add_remarks" id="add_remarks" v-model="active.add_remarks">
						</div>
					</td>
					<td v-if="!disabled">
						<center class="form-group" style="min-width: 120px; margin: 8px">
							<div class="btn btn-primary btn-xs" @click="submit()">
								<span class="fa fa-plus"></span>
							</div>
							
						</center>
					</td>						
				</tr>
			</table>
			<prxalert ref="ticketalert"
			:id="'ticketmodal'">
			</prxalert>
			<prxalert ref="tickettablealert"
			:id="'tickettablemodal'">
			</prxalert>
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
		'fetchurl',
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

		ebi.$on('travelDetailsFetch', () => {
			this.fetch();
		});

		ebi.$on('setActiveTravelDetails', (list) => {
			this.active = list;
		});
	},

	methods: {

		init() {
			this.fetch();
			this.travelForm = $('#tickettravelorderdetails');
		},

		fetch() {

			axios.post(this.fetchurl)
				.then(response => {

					this.lists = response.data.lists;

				}).catch(error => {
					// console.log(error);
				});
		},

		submit() {
			this.loading = true;

			let url = this.active.id ? this.active.extra.updateurl : this.submiturl;

			axios.post(url, {
				accommodation: $('#add_accommodation').val(),
				transportation_type: $('#add_transportation_type').val(),
				details: $('#add_details').val(),
				remarks: $('#add_remarks').val(),
			})
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

		edit(list) {
			ebi.$emit('setActiveTravelDetails', list);
			$('table').find('#' +list.id).find('input').prop('readonly', false);

		},

		unedit(list) {
			this.loading = true;
			$('table').find('#' +list.id).find('input').prop('readonly', true);

			let url = this.active.id ? this.active.extra.updateurl : this.submiturl;
			axios.post(url, {
				accommodation: $('table').find('#' +list.id).find('#accommodation').val(),
				transportation_type: $('table').find('#' +list.id).find('#transportation_type').val(),
				details: $('table').find('#' +list.id).find('#details').val(),
				remarks: $('table').find('#' +list.id).find('#remarks').val(),
			})
				.then(response => {
					console.log(response);
					let type;

					this.$refs.ticketalert.show(response.data.message, response.data.title, response.data.response, false, false);

					ebi.$emit('travelDetailsFetch');

					this.resetAll();
					this.loading = false;

				}).catch(error => {

					this.$refs.ticketalert.show(error);

					this.loading = false;
				});
			ebi.$emit('setActiveTravelDetails', {});
			$('table').find('#' +list.id).find('input').prop('readonly', true);
		},		

		archive(list) {
			if(this.loading && !list.id) 
				return false;

			this.loading = true;


			axios.post(list.extra.removeurl, {})
				.then(response => {

					console.log(response.data.response);

					this.$refs.tickettablealert.show(response.data.message, response.data.title, response.data.response, false);
					this.fetch();
					this.loading = false;

				}).catch(error => {

					this.$refs.tickettablealert.show(error);
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