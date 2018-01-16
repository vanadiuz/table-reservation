<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TREMtrNotifications' ) ) {
/**
 * Class to process notifications for Restaurant Reservations
 *
 * This class contains the registered notifications and sends them when the
 * event is triggered.
 *
 * @since 0.0.1
 */
class TREMtrNotifications {

	/**
	 * tremtrReservation object (class tremtrtremtrReservation)
	 *
	 * @var object
	 * @since 0.0.1
	 */
	public $reservation;

	/**
	 * Array of TREMtrNotification objects
	 *
	 * @var array
	 * @since 0.0.1
	 */
	public $notifications;

	/**
	 * Register notifications hook early so that other early hooks can
	 * be used by the notification system.
	 * @since 0.0.1
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_notifications' ) );
	}

	/**
	 * Register notifications
	 * @since 0.0.1
	 */
	public function register_notifications() {

		// Hook into all events that require notifications
		$hooks = array(
			'tremtr_insert_reservation'	=> array( $this, 'new_submission' ), 		// tremtrReservation submitted
			'pending_to_confirmed'	=> array( $this, 'pending_to_confirmed' ), 	// tremtrReservation confirmed
			'pending_to_closed'		=> array( $this, 'pending_to_closed' ), 	// tremtrReservation can not be made
		);

		$hooks = apply_filters( 'tremtr_notification_transition_callbacks', $hooks );

		foreach ( $hooks as $hook => $callback ) {
			add_action( $hook, $callback );
		}

		// Register notifications
		require_once( TREMTR_PLUGIN_DIR . '/includes/notification.class.php' );
		require_once( TREMTR_PLUGIN_DIR . '/includes/notification.email.class.php' );

		$this->notifications = array(
			new TREMtrNotificationEmail( 'new_submission', 'user' ),
		);


		global $tremtr_controller;
		$admin_email_option = $tremtr_controller->settings->get_setting( 'admin-email-option' );
		if ( $admin_email_option ) {
			$this->notifications[] = new TREMtrNotificationEmail( 'new_submission', 'admin' );
		}

	}


	/**
	 * Set reservation data
	 * @since 0.0.1
	 */
	public function set_reservation( $reservation_post ) {
		require_once( TREMTR_PLUGIN_DIR . '/includes/reservation.class.php' );
		$this->reservation = new TREMtrReservation();
		$this->reservation->load_wp_post( $reservation_post );
	}

	/**
	 * New reservation submissions
	 *
	 * @var object $reservation
	 * @since 0.0.1
	 */
	public function new_submission( $reservation ) {

		// Bail early if $reservation is not a TREMtrReservation object
		if ( get_class( $reservation ) != 'TREMtrReservation' ) {
			return;
		}

		$this->reservation = $reservation;
		$this->event( 'new_submission' );

	}

	/**
	 * tremtrReservation was confirmed and is now completed. Send out an optional
	 * follow-up email.
	 *
	 * @since 0.0.1
	 */
	public function confirmed_to_closed( $reservation_post ) {

		if ( $reservation_post->post_type != 'trem-reservation' ) {
			return;
		}

		$this->set_reservation( $reservation_post );
		$this->event( 'confirmed_to_closed' );

	}

	/**
	 * Process notifications for an event
	 * @since 0.0.1
	 */
	public function event( $event ) {

		foreach( $this->notifications as $notification ) {

			if ( $event == $notification->event ) {
				$notification->set_reservation( $this->reservation );
				if ( $notification->prepare_notification() ) {
					do_action( 'tremtr_send_notification_before', $notification );
					$notification->send_notification();
					do_action( 'tremtr_send_notification_after', $notification );
				}
			}
		}

	}

}
} // endif;
