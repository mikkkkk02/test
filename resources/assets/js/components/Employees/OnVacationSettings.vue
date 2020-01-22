<template>

	<div class="box-body">

		<div class="row">
			<div class="form-group col-sm-12 no-margin-b">
				<label for="assigneeList" class="control-label">Vacation Settings</label>
				<p>Toggle the checkbox below to enable the <b>On Vacation</b> settings</p>
			</div>
		</div>
		<div class="row">
            <div class="form-group col-sm-12">
				<div class="checkbox no-margin-t no-margin-b">
					<label>
						<input v-model="onVacation" :checked="onVacation"
						type="checkbox" name="onVacation">On Vacation
					</label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6">
				<label>Start Date</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input :required="onVacation" :disabled="!onVacation"
					type="text" name="vacation_start_date" placeholder="Start Date" class="form-control flatpickr-vacation" :data-value="startDate">
				</div>
			</div>	
			<div class="form-group col-sm-6">
				<label>End Date</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input :required="onVacation" :disabled="!onVacation"
					type="text" name="vacation_end_date" placeholder="End Date" class="form-control flatpickr-vacation" :data-value="endDate">
				</div>
			</div>								
		</div>
		<div class="row">
			<div class="form-group col-xs-12">
				<label for="assigneeList" class="control-label">Employee</label>
				<p>Select the employees that will receive any notification on your behalf</p>
				<select :required="onVacation" :disabled="proxies.length == 0 || !onVacation"
				name="vacation_proxy_id" class="form-control" required>
					<option value="0" disabled selected>Select employee...</option>
					<template v-for="proxy in proxies">
				
						<option :value="proxy.id" :selected="employee.vacation_proxy_id == proxy.id">{{ proxy.extra.fullname }}</option>

					</template>

				</select>
			</div>
		</div>						

	</div>
	<!-- /.box-body -->

</template>
<script>
	
	import flatpickr from '../../mixins/Flatpickr';

	export default {

		props: [
			'employee',
			'proxies',
		],

		mixins:[ flatpickr ],

		data: function() {
			return {
				onVacation: false,

				startDate: "",
				endDate: "",
			};
		},

		computed: {},

		mounted: function() {
			this.init();

			this.bindEvents();			
		},

		methods: {

			init: function() {

				/* Set default values */
				this.onVacation = this.employee.onVacation;

				if(this.employee.vacation_start_date)
					this.startDate = this.employee.vacation_start_date;

				if(this.employee.vacation_end_date)
					this.endDate = this.employee.vacation_end_date;
			},

			bindEvents: function() {

				/* Initialize flatpickr */
				this.$nextTick(() => {
					this.flatpickr.init('.flatpickr-vacation');
				});
			},			
		},
	}
</script>