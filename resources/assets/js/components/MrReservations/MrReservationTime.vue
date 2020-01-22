<template>
	<div>

		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">{{ reservationtime.name ? reservationtime.name : 'Reservation #' + index }}</h3>
					<a @click.prevent="remove(reservationtime.id)" v-show="!disabled"
						class="pull-right btn btn-xs btn-danger" href="#">
						<i class="fa fa-close"></i>
					</a>
				</div>
				<!-- /.box-header -->
				<div class="box-body">

					<div class="form-group col-sm-4">
						<label>Date</label>
						<input :class="'flatpickr' + index" type="text" :name="'mrreservationtime[' + reservationtime.id + '][date]'" 
						:data-value="reservationtime.date ? reservationtime.date : ''"
						class="form-control">
					</div>

					<div class="form-group col-sm-4">
						<label>Start Time</label>
						<input :class="'timepicker' + index" type="text" :name="'mrreservationtime[' + reservationtime.id + '][start_time]'" 
						:data-value="reservationtime.start_time ? reservationtime.start_time : '8:00'"
						class="form-control">
					</div>

					<div class="form-group col-sm-4">
						<label>End Time</label>
						<input :class="'timepicker' + index" type="text" :name="'mrreservationtime[' + reservationtime.id + '][end_time]'" 
						:data-value="reservationtime.end_time ? reservationtime.end_time : '17:00'"
						class="form-control">
					</div>

				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>

	</div>
</template>

<script>
import flatpickr from '../../mixins/Flatpickr';

export default {
	props: {
		index: {
			type: Number,
		},

		reservationtime: {
			type: Object,
			default: {},
		},

		disabled: {
			type: Boolean,
			default: false,
		}
	},

	mixins: [ flatpickr ],

	data() {
		return {
			loading: false,
		}
	},

	computed: {

	},

	watch: {

	},

	mounted() {
		this.init();
	},

	methods: {

		init() {
			this.setup();
		},

		setup() {
			this.$nextTick(() => {
	    		this.flatpickr.init('.timepicker' + this.index, true, true);		
	    		this.flatpickr.init('.flatpickr' + this.index);		
    		});
		},

		/**
		 * @Helpers
		 */
		

		/**
		 * Methods
		 */
		remove(id) {
			this.$emit('remove', id);
		},

		/**
		 * @Setters
		 */
	}
}
</script>