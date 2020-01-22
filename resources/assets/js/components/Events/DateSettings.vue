<template>

	<div>
		<div class="row">
			<div class="form-group col-sm-3">
				<label>Start Date</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" name="start_date" placeholder="Start Date" class="form-control flatpickr" data-name="startDate" :data-value="startDate">
				</div>
			</div>
			<div class="form-group col-sm-3">
				<label>End Date</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" name="end_date" placeholder="End Date" class="form-control flatpickr" data-name="endDate" :data-value="endDate">
				</div>
			</div>
			<div class="form-group col-sm-3">
				<label>Registration Deadline</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" name="registration_date" placeholder="Registration Deadline" class="form-control flatpickr" data-name="registrationDate" :data-value="registrationDate">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-sm-12 no-margin-b">
	        	<label>Time settings</label>
			</div>
		</div>		
		<div class="row">
	        <div class="form-group col-sm-3">
				<div class="radio no-margin-t no-margin-b">
					<label>
						<input v-model="isSameTime"
						type="radio" name="isSameTime" value="1">
						Set the start/end time for all day
					</label>
				</div>
			</div>
			<div class="form-group col-sm-3">
				<div class="radio no-margin-t no-margin-b">
					<label>
						<input v-model="isSameTime" :disabled="!days"
						type="radio" name="isSameTime" value="0">
						Set the start/end time per day
					</label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-12 no-margin-b">
	        	<label>Day settings</label>
			</div>
		</div>
		<div class="row">
	        <div class="form-group col-sm-3">
				<div class="checkbox no-margin-t no-margin-b">
					<label>
						<input v-model="includeWeekend"
						type="checkbox" name="hasWeekend" value="1">
						Do you want to include weekends?
					</label>
				</div>
			</div>
		</div>		

		<div class="row">
			<div class="form-group col-xs-12 no-margin-b">
				<label>Time</label>
			</div>
		</div>

		<template v-for="(day, index) in noOfDays">

			<div class="row">
				<div class="form-group col-xs-3">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input 
						:name="'times[' + index + '][start_time]'" 
						:placeholder="'Day ' + (index + 1) + ': Start Time'"
						type="text" class="form-control timepicker-input day-1 read-only">
					</div>
				</div>
				<div class="form-group col-xs-3">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input 
						:name="'times[' + index + '][end_time]'" 
						:placeholder="'Day ' + (index + 1) + ': End Time'"
						type="text" class="form-control timepicker-input day-2 read-only">
					</div>
				</div>
			</div>

		</template>

	</div>	

</template>
<script>

	import flatpickr from '../../mixins/Flatpickr';

	export default {

		props: [
			'event',
		],

		mixins:[ flatpickr ],

		data: function() {
			return {
				loading: false,

				days: 0,
				startDate: '',
				endDate: '',
				registrationDate: '',

				includeWeekend: 0,
				isSameTime: 1,
			};
		},

		computed: {

			noOfDays: function() {

				/* Check if all days have the same time setting */
				if(parseInt(this.isSameTime))
					return 1;

				return this.days;
			},
		},

		watch: {

			noOfDays: function (val, oldVal) {
				var $this = this;

				/* Initialize timepicker for the new input fields */
				this.$nextTick(function() {
					$this.initTimepicker();
				});
			},

			includeWeekend: function() { this.calculateDays(); },
			startDate: function() { this.calculateDays(); },
			endDate: function() { this.calculateDays(); },
		},

		mounted: function() {
			this.init();

			this.bindEvents();
		},

		methods: {

			init: function() {

				/* Set default variables */
				if(this.event) {

					this.startDate = this.getFormattedDate(this.event.start_date);
					this.endDate = this.getFormattedDate(this.event.end_date);
					this.registrationDate = this.getFormattedDate(this.event.registration_date);
					this.isSameTime = this.event.isSameTime;
					this.includeWeekend = this.event.hasWeekend;
				}
			},

			initTimepicker() {

				/* Initialize timepicker for the new input fields */
				$('.timepicker-input.day-1').flatpickr(this.setFlatpickr());
				$('.timepicker-input.day-2').flatpickr(this.setFlatpickr(true));
			},

			bindEvents: function() {

				const $this = this;

				this.initTimepicker();

				this.$nextTick(() => {
					$this.flatpickr.init('.flatpickr');
				});

				/* Initialize flatpickr */
	    		// $('.flatpickr').flatpickr({ 
	    		// 	dateFormat: 'Y-m-d',
	    		// 	enableTime: false,
	    		// 	onChange: function(selectedDates, dateStr, instance) {
				   //      const name = $(instance.input).data('name');

				   //      $this[name] = dateStr;
				   //  },
	    		// });	
			},


			/*
			|-----------------------------------------------
			| @Methods
			|---------------------------------------------*/			
			calculateDays: function() {

				/* Check if there dates are already set */
				if(this.startDate && this.endDate) {

					this.days = this.$parent.$options.methods.workingDaysBetweenDates(new Date(this.startDate + "Z"), new Date(this.endDate  + "Z"), this.includeWeekend);

					/* Reset if end date is before start date */
					if(this.days < 0) {
						
						this.isSameTime = 1;
						this.endDate = '';
					}
				}
			},

			setFlatpickr: function(time = false) {
				return {
				    enableTime: true,
					noCalendar: true,
					dateFormat: "h:i K",
					time_24hr: false,
					defaultDate: time ? '05:00 PM' : '08:00 AM'
				};
			},


			/*
			|-----------------------------------------------
			| @Helper
			|---------------------------------------------*/
			checkEventTime: function(index, dir) {

				/* Check if there is no event obj */
				if(!this.event)
					return dir ? '05:00 PM' : '08:00 AM';

				/* Check if there is a time obj */
				if(!this.event.times || !this.event.times[index])
					return dir ? '05:00 PM' : '08:00 AM';

				return dir ? this.getFormattedTime(this.event.times[index].end_time) : this.getFormattedTime(this.event.times[index].start_time);
			},

			getFormattedTime: function(time) {

				/* Check if time exists */
				if(!time)
					return null;
				
				return moment(time).format('hh:mm A');
			},

			getFormattedDate: function(date) {

				/* Check if date exists */
				if(!date)
					return null;

				return moment(date).format('YYYY-MM-DD')
			},
		},
	}
</script>

<style src="flatpickr/dist/flatpickr.min.css"></style>