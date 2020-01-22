<template>

	<div>
		<div class="row">
			<div class="form-group col-sm-6">
				<label>Title <span class="has-error">*</span></label>
				<input :value="title" :disabled="settings.disabled"
				type="text" name="mr_title" placeholder="Title" class="form-control" required>
			</div>
		</div>		
		<div v-show="!form || (form && form.mr_date)"
		class="row">
			<div class="form-group col-sm-6">
				<label>Date <span class="has-error">*</span></label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="mrDate" :disabled="settings.disabled" :data-value="form.mr_date"
					name="mr_date" placeholder="Date" type="text" class="form-control mr-datepicker">
				</div>
			</div>
		</div>

		<div v-show="!form || (form && form.mr_start_time && form.mr_end_time)"
		class="row">
			<div class="form-group col-sm-3">
				<label>Start Time <span class="has-error">*</span></label>
				<div class="input-group bootstrap-timepicker">
					<input id="mrStartTime" :disabled="settings.disabled" :data-value="form.mr_start_time"
					name="mr_start_time" placeholder="Start Time" type="text" class="form-control mr-timepicker">
                    <div class="input-group-addon">
						<i class="fa fa-clock-o"></i>
                    </div>
		        </div>
			</div>
			<div class="form-group col-sm-3">
				<label>End Time <span class="has-error">*</span></label>
				<div class="input-group bootstrap-timepicker">
					<input id="mrEndTime" :disabled="settings.disabled" :data-value="form.mr_end_time"
					name="mr_end_time" placeholder="End Time" type="text" class="form-control mr-timepicker">
                    <div class="input-group-addon">
						<i class="fa fa-clock-o"></i>
                    </div>			            
		        </div>
			</div>			
		</div>		

	</div>

</template>
<script>
	import flatpickr from '../../mixins/Flatpickr';

	export default {

		props: [
			'disabled',
			'form',

			'checkscheduleurl',
		],

		mixins:[ flatpickr ],

		data: function() {
			return {
				loading: false,

				settings: {
					disabled: false,
				},

				title: '',
				date: null,
				start_time: null,
				end_time: null,
			};
		},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {
				const $this = this;
				
				/* Fetch elements */
				this.date = $('#mrDate');
				this.start_time = $('#mrStartTime');
				this.end_time = $('#mrEndTime');
				this.settings.disabled = this.disabled ? this.disabled : false;


				this.$nextTick(function() {

					/* Set default vars */
					if($this.form) { //console.log('A');
						$this.title = $this.form.mr_title;
						$this.date.val($this.form.mr_date);
						$this.start_time.val($this.convertTime($this.form.mr_start_time));
						$this.end_time.val($this.convertTime($this.form.mr_end_time) );
					}


					/* Init bind event */
					$this.bindEvents();
				});
			},

			bindEvents: function() {

				/* Initialize datepicker */
	    		this.flatpickr.init('.mr-datepicker');
	    		this.flatpickr.init('.mr-timepicker', true, true);

	    		/* Initialize timepicker */
	    		// $('.mr-timepicker').timepicker({ autoclose: true });
			},	


			/*
			|-----------------------------------------------
			| @Helper
			|-----------------------------------------------
			*/			
			convertTime: function(time) {
				return moment(time, 'hh:mm').format('hh:mm A')
			}		
		},
	}
</script>