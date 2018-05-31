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
		

		if (window.location.href.indexOf('edit.php?post_type=trem-cafes') !== -1 || window.location.href.indexOf('edit.php?post_type=trem-reservation') !== -1) {
			jQuery('h1.wp-heading-inline').before(
				'<div style="display: flex;justify-content: center;margin: auto; width:100%;" ><a href="https://true-emotions.studio" style="display: flex;justify-content: center;margin: auto;width: 150px;"><img  src="https://true-emotions.studio/wp-content/uploads/2018/03/tremlogoblack-1.png"></a></div>'
			)
		}

		if (window.location.href.indexOf('edit.php?post_type=trem-cafes') !== -1 || window.location.href.indexOf('edit.php?post_type=trem-reservation') !== -1) {
			jQuery('h1.wp-heading-inline').before(
				'<button class = "mouse-cursor-gradient-tracking"><span> Get Pro! </span></button>'
			)
		}


		

		let btn = document.querySelector('.mouse-cursor-gradient-tracking')

		if (btn != null) {
			btn.onmousemove = function (e) {
				let x = e.pageX - btn.offsetLeft
				let y = e.pageY - btn.offsetTop
				btn.style.setProperty('--x', x + 'px')
				btn.style.setProperty('--y', y + 'px')
			}
		}


		$('.mouse-cursor-gradient-tracking').click(function () {
			window.location = "https://true-emotions.studio/products/plugins/table-reservation-pro/";
		});

		

	}
});

