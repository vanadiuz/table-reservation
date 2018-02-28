<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TREMtrReservation' ) ) {
/**
 * Class to handle a reservation for Cafe Table Bookings
 *
 * @since 0.0.1
 */
class TREMtrReservation {

	/**
	 * Whether or not this request has been processed. Used to prevent
	 * duplicate forms on one page from processing a reservation form more than
	 * once.
	 * @since 0.0.1
	 */
	public $request_processed = false;

	/**
	 * Whether or not this request was successfully saved to the database.
	 * @since 0.0.1
	 */
	public $request_inserted = false;

	public function __construct() {}

	/**
	 * Load the reservation information from a WP_Post object or an ID
	 *
	 * @uses load_wp_post()
	 * @since 0.0.1
	 */
	public function load_post( $post ) {

		if ( is_int( $post ) || is_string( $post ) ) {
			$post = get_post( $post );
		}

		if ( get_class( $post ) == 'WP_Post' && $post->post_type == 'trem-reservation' ) {
			$this->load_wp_post( $post );
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Load data from WP post object and retrieve metadata
	 *
	 * @uses load_post_metadata()
	 * @since 0.0.1
	 */
	public function load_wp_post( $post ) {

		// Store post for access to other data if needed by extensions
		$this->post = $post;

		$this->ID = $post->ID;
		$this->name = $post->post_title;
		$this->date = $post->post_date;
		$this->post_status = $post->post_status;

		$this->load_post_metadata();

		do_action( 'tremtr_reservation_load_post_data', $this, $post );
	}

    public function check_nonce( $nonce = '' ) {
		if( !empty( $nonce ) ) {
            $args_arr = array(
                'post_type'	 => 'trem-reservation',
                'meta_query' => array(
                    array(
                        'key'     => 'tremtr_reservation_nonce',
                        'value'   => $nonce,
                    )
                )
            );

            $query = new WP_Query( $args_arr );
            $nonce_count = $query->found_posts;
            wp_reset_query();
            return $nonce_count;
		} else {
		    return 0;
		}
    }

	
	public function get_table_reservations( $date = '', $table = '' ) {

	    $time_arr = array();

        if( !empty( $date ) && !empty( $table ) ) {
            $args_arr = array(
                'post_type' => 'trem-reservation',
                'meta_query' => array(
                    array(
                        'key' => 'tremtr_reservation_date',
                        'value' => $date,
                    ),
                    array(
                        'key' => 'tremtr_reservation_table',
                        'value' => $table,
                    )
                )
			);


            $query = new WP_Query($args_arr);
            if ( $query->have_posts() ) {
                $i=0;
                while( $query->have_posts() ) {
                    $query->the_post();
                    $time_arr[$i]['date'] = get_post_meta( get_the_ID(), 'tremtr_reservation_date', true );
                    $time_arr[$i]['table'] = get_post_meta( get_the_ID(), 'tremtr_reservation_table', true );
                    $time_arr[$i]['time_begin'] = get_post_meta( get_the_ID(), 'tremtr_reservation_time_begin', true );
                    $time_arr[$i]['time_end'] = get_post_meta( get_the_ID(), 'tremtr_reservation_time_end', true );
                    $i++;
                }
            }
            wp_reset_query();
        }

        return $time_arr;
	}


	/**
	 * Store metadata for post
	 * @since 0.0.1
	 */
	public function load_post_metadata() {

		$this->table = get_post_meta( $this->ID, 'tremtr_reservation_table', true );
		$this->persons = get_post_meta( $this->ID, 'tremtr_reservation_persons', true );
		$this->reservation_date = get_post_meta( $this->ID, 'tremtr_reservation_date', true );
		$this->reservation_time_begin = get_post_meta( $this->ID, 'tremtr_reservation_time_begin', true );
		$this->reservation_time_end = get_post_meta( $this->ID, 'tremtr_reservation_time_end', true );
		$this->email = get_post_meta( $this->ID, 'tremtr_reservation_email', true );
		$this->phone = get_post_meta( $this->ID, 'tremtr_reservation_phone', true );
		$this->message = get_post_meta( $this->ID, 'tremtr_reservation_message', true );
		$this->date_submission = get_post_meta( $this->ID, 'tremtr', true );

	}

	public function format_date( $date ) {
		$date = mysql2date( get_option( 'date_format' ) . ' ', $date);
		return apply_filters( 'get_the_date', $date );
	}

	public function format_time( $time ) {
		$time = mysql2date( get_option( 'time_format' ), $time);
		return apply_filters( 'get_the_time', $time );
	}

	/**
	 * Format a timestamp into a human-readable date
	 *
	 * @since 1.7.1
	 */
	public function format_timestamp( $timestamp ) {
		$time = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
		return $time;
	}

}
} // endif;
