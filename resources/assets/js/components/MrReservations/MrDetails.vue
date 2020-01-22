<template>
	<div>

		<div class="form-group col-sm-6">
			<label>Location</label>
			<select v-model="location_id" 
			class="form-control" name="location_id">

				<option value="" selected hidden>Please select a location</option>

				<template v-for="location in locations">
				
					<option :value="location.id">{{ location.name }}</option>
				
				</template>

			</select>
		</div>

		<div class="form-group col-sm-6">
			<label>Room</label>
			<select v-model="room_id" :disabled="!cansetroom"
			class="form-control" name="room_id">

				<option value="" selected hidden>Please select a room</option>

				<template v-for="room in rooms">
				
					<option :value="room.id">{{ room.name }}</option>
				
				</template>

			</select>
		</div>

		<div class="col-sm-12 s-margin-b">
			<button @click.prevent="add()" v-show="!disabled" :disabled="disabled"
			type="button" class="btn btn-xs btn-primary">
				+ Add Reservation
			</button>
		</div>

		<div class="row">
		
			<div class="col-sm-12">

				<template v-for="(reservationtime, index) in reservationtimes">
						
					<mrreservationtime
					:index="index + 1"
					:reservationtime="reservationtime" 
					:locations="locations"
					:disabled="disabled"
					@remove="remove()">
					</mrreservationtime>

				</template>

			</div>
			
		</div>


		<transition name="fade">

	        <div v-show="loading"
	        class="overlay">
				<i class="fa fa-refresh fa-spin"></i>
	        </div>

	    </transition>

	</div>
</template>

<script>
import mrreservationtime from './MrReservationTime';
export default {

	props: [
		'fetchurl',
		'mrreservation',
		'disabled',
		'cansetroom',
	],

	data() {
		return {
			loading: false,

			locations: [],

			reservationtimes: [],

			temp_id: 1,

			location_id: '',
			room_id: '',
			has_init: false,
		}
	},

	components: {
		mrreservationtime
	},

	computed: {
		rooms() {
			let lists = [];

			if (this.locations.length > 0 && this.location_id) {
				lists = this.locations.filter(obj => { return obj.id === this.location_id})[0].rooms;
			}

			return lists;
		},
	},

	mounted() {
		this.init();
	},

	methods: {
		init() {
			this.fetch();
			this.setup();
		},

		/**
		 * @Methods
		 */

		add() {
			this.reservationtimes.push({
				id: 'tempReservation' + this.temp_id,
				name: 'New Reservation #' + this.temp_id,
			});

			this.temp_id++;
		},

		remove(id) {
			this.reservationtimes.splice(this.reservationtimes.findIndex(obj => obj.id === id), 1);
		},

		/**
		 * @Getters
		 */
		fetch() {
			this.loading = true;

			axios.post(this.fetchurl)
			.then(response => {
				this.locations = response.data.lists;
				this.loading = false;
			}).catch(error => {
				console.log(error);
				this.loading = false;
			});
		},

		/**
		 * @Setters
		 */
		setup() {
			if (this.mrreservation) {
				this.location_id = this.mrreservation.location_id ? this.mrreservation.location_id : '';
				this.room_id = this.mrreservation.room_id ? this.mrreservation.room_id : '';
				this.reservationtimes = this.mrreservation.mr_reservation_times ? this.mrreservation.mr_reservation_times : [] ;
			}

			if (!this.mrreservation) {
				if (this.reservationtimes.length < 1) {
					this.add();
				}
			}
		}
	},

}
</script>