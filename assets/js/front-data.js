/* Javascript for Tables Reservations form */

jQuery(document).ready(function ($) {


		// Enable datepickers on load
	if ( typeof tremtr_data !== 'undefined' ) {
		console.log(tremtr_data);

		$(".tremtr-date").flatpickr({
			dateFormat: 'Y/m/d',
			altFormat: 'F j, Y',

		});

		$(".tremtr-time").flatpickr({
			altFormat: 'H:i',
			enableTime: true,
    		noCalendar: true,
    		enableSeconds: false,
    		dateFormat: "H:i",
    		time_24hr: true,
			minuteIncrement: parseInt(tremtr_data.time_interval),
			//format: tremtr_data.time_format,

		});

	}
});

