
/**
 * Flatpickr datepicker mixin
 *-----------------------------------------------*/

/* import css style */
import 'flatpickr/dist/flatpickr.min.css';

/* require npm package */
require('flatpickr');

export default {

	computed:{

		/* flatpickr vue object */
		flatpickr:function() {

			return {

				/* initialize flatpickr */
				init:(inputName = '.flatpickr', includeTime = false, timeOnly = false) => {

					const $this = this;

					let dateFormat = 'Y-m-d';

					if (includeTime) {
						dateFormat = 'Y-m-d H:i';
					}

					if (timeOnly) {
						dateFormat = 'H:i';
					}

					$(inputName).each(function() {
						/* Set default value base on the data value attr */
			            const field = $(this);
			            let date = field.data('value');

		            	$(this).flatpickr({
			                dateFormat: dateFormat,
							enableTime: includeTime,
							noCalendar: timeOnly,
			                defaultDate: date ? date : new Date(),
			                onReady: function(selectedDates, dateStr, instance) {
			                	field.data('value', dateStr);
			                	$this.setValue(selectedDates, dateStr, instance);
			                },
			                
			                onChange: function(selectedDates, dateStr, instance) {
			                	field.data('value', dateStr);
			                	$this.setValue(selectedDates, dateStr, instance);
						    },
			            });

			        });
				},
			}
		}
	},

	methods: {
		setValue(selectedDates, dateStr, instance) {
			// console.log('Flatpickr changed!');
	    	if ($(instance.input).data('name')) {
	    		// console.log('Data Name Exist!');
	    		const name = $(instance.input).data('name');

		        this[name] = dateStr;
		        // console.log(this[name]);
	    	}
		}
	}
}