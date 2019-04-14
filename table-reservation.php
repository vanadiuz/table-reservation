<?php
/***********************************
 *
 * Plugin Name:  ☕️Table Reservation
 * Plugin URI:   https://github.com/vanadiuz/table-reservation
 * Description:  Pick a place ⚡️ No collisions. Rich settings. Mobile UX.
 * Version:      3.3.3
 * Author:       True Emotions Studio
 * Author URI:   http://true-emotions.studio
 * License:      GPLv2 or later
 * Text Domain:  tremtr
 * Domain Path: /languages/
 ***********************************/
if (!defined('ABSPATH')) {
    exit; // disable direct access
}



if (!class_exists('TREMTableReservation')) :

    class TREMTableReservation {

		var $vc_dir;

        public function __construct() {

            define( 'TREMTR_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
            define( 'TREMTR_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

            // Initialize the plugin
		    add_action( 'init', array( $this, 'load_textdomain' ) );

            // Register the post type.
            add_action( 'init', array( $this, 'tremtr_post_type_init' ) );

            require_once ( TREMTR_PLUGIN_DIR . '/includes/meta_box.php' );

            require_once( TREMTR_PLUGIN_DIR . '/includes/settings.class.php' );
            $this->settings = new TREMtrSettings();

            require_once( TREMTR_PLUGIN_DIR . '/includes/reservation.class.php' );
            $this->reservation = new TREMtrReservation();

            // Handle notifications
            require_once( TREMTR_PLUGIN_DIR . '/includes/notifications.class.php' );
            $this->notifications = new TREMtrNotifications();

            $this->vc_dir = TREMTR_PLUGIN_DIR.'/vc/';
            add_action( 'admin_init', array($this,'tremtr_add_admin_menu_separator') );
            add_action( 'after_setup_theme', array($this,'tremtr_vc_init'));

            add_action( 'wp_enqueue_scripts', array($this, 'tremtr_enqueue_scripts'));
            add_action( 'admin_enqueue_scripts', array($this, 'tremtr_admin_enqueue_scripts'));
            add_action( 'wp_ajax_tremtr_reservation', array( $this, 'tremtr_reservation' ) );
            add_action( 'wp_ajax_nopriv_tremtr_reservation', array( $this, 'tremtr_reservation' ) );

            add_action( 'save_post', array($this, 'tremtr_save_postdata') );

            add_shortcode( 'table-reservation', array($this, 'tremtr_shortcode_output') );


            //Delete irrelevant reservations with wp-cron
            register_activation_hook( __FILE__, 'tremtr_activation_delete_irrelevant_reservations' );
            function tremtr_activation_delete_irrelevant_reservations() {
                if (! wp_next_scheduled ( 'tremtr_daily_cleaning_event')) {
                    wp_schedule_event( time(), 'twicedaily', 'tremtr_daily_cleaning_event');
                }
            }
            add_action( 'tremtr_daily_cleaning_event', array($this, 'delete_old_table_reservations'), 10, 0);

            register_deactivation_hook( __FILE__, 'tremtr_deactivation_delete_irrelevant_reservations' );
            function tremtr_deactivation_delete_irrelevant_reservations() {
                wp_clear_scheduled_hook( 'tremtr_daily_cleaning_event' );
            }

        }




        //Delete irrelevant reservations with wp-cron
        public function delete_old_table_reservations() {
            $query = new WP_Query(array('nopaging' => true, 'post_type' => 'trem-reservation'));
            if ( $query->have_posts() ) {
                $i=0;
                while( $query->have_posts() ) {
                    $query->the_post();
                    if(get_post_meta( get_the_ID(), 'tremtr_reservation_date', true ) < date( 'Y/m/d', current_time( 'timestamp', 0 ) )){
                        wp_delete_post( get_the_ID(), true);
                    }

                }
            }
            wp_reset_query();
        }

        /**
         * Load the plugin textdomain for localistion
         * @since 0.0.1
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'tremtr', false, plugin_basename( dirname( __FILE__ ) ) . "/languages/" );
        }


        // Register tremtr post type
        public function tremtr_post_type_init() {

            $labels = array(
                    'name' => esc_html__('Cafe', 'tremtr'),
                    'singular_name' => esc_html__('Cafe', 'tremtr'),
                    'add_new' => esc_html__('Add New', 'tremtr'),
                    'add_new_item' => esc_html__('Add New Cafe', 'tremtr'),
                    'edit_item' => esc_html__('Edit Cafe', 'tremtr'),
                    'new_item' => esc_html__('New Cafe', 'tremtr'),
                    'view_item' => esc_html__('View Cafe', 'tremtr'),
                    'search_items' => esc_html__('Search Cafe', 'tremtr'),
                    'not_found' => esc_html__('Not found', 'tremtr'),
                    'not_found_in_trash' => esc_html__('Not found in Trash', 'tremtr'),
            );
            $args = array(
                    'labels' => $labels,
                    'hierarchical' => false,
                    'supports' => array('title', 'thumbnail'),
                    'public' => true,
                    'show_in_rest' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'show_in_nav_menus' => false,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'menu_position' => 31,
                    'menu_icon' => 'dashicons-store',
                    'has_archive' => true,
                    'query_var' => true,
                    'can_export' => true,
                    'rewrite' => array( 'slug' => 'cafe' ),
                    'capability_type' => 'post',
		            'register_meta_box_cb'	=> 'tremtr_meta_boxes'
            );
            register_post_type('trem-cafes', $args);

            $labels = array(
                    'name' => esc_html__('Reservations', 'tremtr'),
                    'singular_name' => esc_html__('Reservation', 'tremtr'),
                    'add_new' => esc_html__('Add New', 'tremtr'),
                    'add_new_item' => esc_html__('Add Reservation', 'tremtr'),
                    'edit_item' => esc_html__('Edit Reservation', 'tremtr'),
                    'new_item' => esc_html__('New Reservation', 'tremtr'),
                    'view_item' => esc_html__('View Reservation', 'tremtr'),
                    'search_items' => esc_html__('Search Reservation', 'tremtr'),
                    'not_found' => esc_html__('Not found', 'tremtr'),
                    'not_found_in_trash' => esc_html__('Not found in Trash', 'tremtr'),
            );
            $args = array(
                    'labels' => $labels,
                    'hierarchical' => false,
                    'supports' => array('title'),
                    'public' => true,
                    'show_in_rest' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'show_in_nav_menus' => false,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'menu_position' => 30,
                    'menu_icon' => 'dashicons-clipboard',
                    'has_archive' => true,
                    'query_var' => true,
                    'can_export' => true,
                    'rewrite' => array( 'slug' => 'reservation' ),
                    'capability_type' => 'post',
		            'register_meta_box_cb'	=> 'tremtr_reservation_meta_boxes'
            );
            register_post_type('trem-reservation', $args);

        }

        public function tremtr_add_admin_menu_separator(){
            global $menu;
            $sep_begin = 0;
            foreach($menu as $offset => $section) {
                if ( strpos($section[2],'trem-reservation') )
                  $sep_begin = $offset;
            }
            if( $sep_begin != 30 ) {
                $rev_menu = array_reverse( $menu, true );
                if( is_array($rev_menu) && !empty($rev_menu) ) {
	                foreach ( $rev_menu as $offset => $section ) {
		                if ( $offset >= $sep_begin ) {
			                $rev_menu[ $offset + 1 ] = $rev_menu[ $offset ];
		                }
	                }
	                $menu = $rev_menu;
                }
            } else {
                $sep_begin = 29;
            }
            $menu[ $sep_begin ] = array(
                0	=>	'',
                1	=>	'read',
                2	=>	'separator'.$sep_begin,
                3	=>	'',
                4	=>	'wp-menu-separator'
            );
            ksort ($menu);
        }

		function tremtr_vc_init(){
			require_once($this->vc_dir."vc_shortcode_tremtr.php");
		}

        public function tremtr_reservation() {

            if( check_ajax_referer( 'wp_rest', 'nonce' ) ){

                $this->reservation->name = sanitize_text_field($_POST['tremtr_reservation_name']);
                $this->reservation->post_type = 'trem-reservation';
                $this->reservation->post_status = 'publish';
                $this->reservation->table = sanitize_text_field($_POST['tremtr_reservation_table']);
                $this->reservation->persons = sanitize_text_field($_POST['tremtr_reservation_persons']);
                $this->reservation->reservation_date = preg_replace("([^0-9/])", "", $_POST['tremtr_reservation_date']);
                $this->reservation->reservation_time_begin = preg_replace("([^0-9:])", "", $_POST['tremtr_reservation_time_begin']);
                $this->reservation->reservation_time_end = preg_replace("([^0-9:])", "", $_POST['tremtr_reservation_time_end']);
                $this->reservation->email = sanitize_email($_POST['tremtr_reservation_email']);
                $this->reservation->phone = sanitize_text_field($_POST['tremtr_reservation_phone']);
                $this->reservation->message = sanitize_textarea_field($_POST['tremtr_reservation_message']);

//                if( $this->reservation->check_nonce(sanitize_text_field($_POST['nonce'])) >= 3 ){
//                    wp_send_json_error( array( 'error' => 'nonce_limit' ) );
//                }

                $table_reservs = $this->reservation->get_table_reservations($this->reservation->reservation_date, $this->reservation->table);
                if( !empty($table_reservs) ){
                    foreach($table_reservs as $val){
                        //last condition for case 23:30 - 02:30
                        if( $this->reservation->reservation_time_end != '' && $val['time_end'] != '' && $this->reservation->reservation_time_begin != '' && $val['time_begin'] != '' && $val['time_begin'] < $val['time_end']) {
                            if( $val['time_begin'] >= $this->reservation->reservation_time_begin && $val['time_end'] <= $this->reservation->reservation_time_end ){
                                wp_send_json_error(array('error' => 'not_free2'));
                            } elseif( $val['time_begin'] <= $this->reservation->reservation_time_begin && $val['time_end'] > $this->reservation->reservation_time_begin ){
                                wp_send_json_error(array('error' => 'not_free3'));
                            } elseif( $val['time_begin'] < $this->reservation->reservation_time_end && $val['time_end'] >= $this->reservation->reservation_time_end ){
                                wp_send_json_error(array('error' => 'not_free4'));
                            }
                        }
                    }
                }

                $reservation = array(
                    'ID' => '',
                    'post_type' => $this->reservation->post_type,
                    'post_title' => $this->reservation->name,
                    'post_status' => $this->reservation->post_status,
                );

                $this->reservation->ID = wp_insert_post($reservation);

                if ($this->reservation->ID) {

                    $options = array(
                        'tremtr_reservation_name' => $this->reservation->name,
                        'tremtr_reservation_date' => $this->reservation->reservation_date,
                        'tremtr_reservation_time_begin' => $this->reservation->reservation_time_begin,
                        'tremtr_reservation_time_end' => $this->reservation->reservation_time_end,
                        'tremtr_reservation_table' => $this->reservation->table,
                        'tremtr_reservation_persons' => $this->reservation->persons,
                        'tremtr_reservation_email' => $this->reservation->email,
                        'tremtr_reservation_phone' => $this->reservation->phone,
                        'tremtr_reservation_message' => $this->reservation->message,
                        'tremtr_reservation_nonce' => sanitize_text_field($_POST['nonce']),
                    );

                    foreach ($options as $key => $value) {
                        update_post_meta($this->reservation->ID, $key, $value);
                    }

                    $this->notifications->new_submission($this->reservation);
                }

                wp_send_json_success($this->reservation);

            } else {
                wp_send_json_error(array('error' => 'empty_data'));
            }
        }

        public function tremtr_save_postdata($post_id) {
            if(get_post_type( $post_id ) == 'trem-cafes'){
                // Checks save status
                $is_autosave = wp_is_post_autosave($post_id);
                $is_revision = wp_is_post_revision($post_id);
                $is_valid_nonce = (isset($_POST['tremtr_nonce']) && wp_verify_nonce($_POST['tremtr_nonce'], basename(__FILE__))) ? 'true' : 'false';

                // Exits script depending on save status
                if ($is_autosave || $is_revision || !$is_valid_nonce) {
                    return;
                }
            }
        }

        //Shortcode output
        public function tremtr_shortcode_output($atts) {

            $content = '';

            if (true) {

                wp_enqueue_script( 'tremtr-fontawesome' );
                wp_enqueue_script( 'tremtr-fabric' );
                wp_enqueue_script( 'tremtr-manifest' );
                wp_enqueue_script( 'tremtr-vendor' );
                wp_enqueue_script( 'tremtr-app' );

                $content = '<div id="reservation" class="reservation"></div>';
            }

            return $content;

        }


        //Load Admin Scripts
        public function tremtr_admin_enqueue_scripts() {
            wp_enqueue_style('tremtr_admin_styles', TREMTR_PLUGIN_URL . '/assets/css/trem.css', array(), '1.0.0', 'screen, all');
            wp_enqueue_style('tremtr_admin_styles_context_menu', TREMTR_PLUGIN_URL . '/assets/css/jquery.contextMenu.min.css');

            wp_enqueue_media();
            wp_enqueue_style( 'wp-color-picker' );

            wp_enqueue_style('tremtr-admin-flatpickr', TREMTR_PLUGIN_URL . '/assets/css/flatpickr.min.css');
            wp_enqueue_script('tremtr-admin-flatpickr', TREMTR_PLUGIN_URL . '/assets/js/flatpickr.js');

            wp_register_script('tremtr-fontawesome', TREMTR_PLUGIN_URL . '/assets/js/fontawesome.js');
            wp_register_script('tremtr-fabric', TREMTR_PLUGIN_URL . '/assets/js/fabricBackEnd.min.js');

            wp_register_script('tremtr-fabricBackEnd', TREMTR_PLUGIN_URL . '/assets/js/fabricBackEnd.min.js');
            wp_register_script('tremtr-admin_context_menu', TREMTR_PLUGIN_URL . '/assets/js/jquery.contextMenu.min.js');
            wp_register_script('tremtr-admin_main', TREMTR_PLUGIN_URL . '/assets/js/main.js');

            wp_register_script( 'tremtr-front-data', TREMTR_PLUGIN_URL . '/assets/js/front-data.js', array( 'jquery' ) );
            wp_localize_script(
                'tremtr-front-data',
                'tremtr_data',
                array(
                    'date_format' => $this->settings->get_setting( 'date-format' ),
                    'time_format'  => $this->settings->get_setting( 'time-format' ),
                    'schedule_open' => $this->settings->get_setting( 'schedule-open' ),
                    'schedule_closed' => $this->settings->get_setting( 'schedule-closed' ),
                    'early_reservations' => is_admin() && current_user_can( 'manage_reservations' ) ? '' : $this->settings->get_setting( 'early-reservations' ),
                    'late_reservations' => is_admin() && current_user_can( 'manage_reservations' ) ? '' : $this->settings->get_setting( 'late-reservations' ),
                    'time_interval' => $this->settings->get_setting( 'time-interval' ),
                    'nonce' => wp_create_nonce('wp_rest'),
                )
            );
            wp_enqueue_script( 'tremtr-front-data','3.1.1' );

        }



        public function tremtr_enqueue_scripts() {

            wp_enqueue_style('tremtr-icons', TREMTR_PLUGIN_URL . '/assets/css/trem-reservation-icons/css/trem-reservation.css');
            wp_enqueue_style('tremtr-app', TREMTR_PLUGIN_URL . '/assets/css/tremtr-client.css');

            $i8n = $this->settings->get_setting( 'i8n' );
            if ( !empty( $i8n ) ) {
                wp_register_script( 'pickadate-i8n', TREMTR_PLUGIN_URL . '/includes/simple-admin-pages/lib/pickadate/translations/' . esc_attr( $i8n ) . '.js', array( 'jquery' ), '', true );

                // Arabic and Hebrew are right-to-left languages
                if ( $i8n == 'ar' || $i8n == 'he_IL' ) {
                    wp_register_style( 'pickadate-rtl', TREMTR_PLUGIN_URL . '/includes/simple-admin-pages/lib/pickadate/themes/rtl.css' );
                }
            }

            wp_register_script('tremtr-fabric', TREMTR_PLUGIN_URL . '/assets/js/fabric.min.js');

            wp_register_script('tremtr-manifest', TREMTR_PLUGIN_URL . '/assets/js/manifest.0ff1271966222f6500e7.js', array(), '1.0.0', 'screen, all');
            wp_register_script('tremtr-vendor', TREMTR_PLUGIN_URL . '/assets/js/vendor.6d137089d7b61cb49866.js', array(), '1.0.0', 'screen, all');
            wp_register_script('tremtr-app', TREMTR_PLUGIN_URL . '/assets/js/app.504a35b9300c6a960f4b.js', array(), '1.0.0', 'screen, all');
            //wp_register_script( 'tremtr-app', 'http://localhost:8080/app.js' , '', '', true );
            wp_localize_script(
                'tremtr-app',
                'tremtr_data',
                array(
                    'date_format' => $this->settings->get_setting( 'date-format' ),
                    'time_format'  => $this->settings->get_setting( 'time-format' ),
                    'schedule_open' => $this->settings->get_setting( 'schedule-open' ) == '' ? '' : tremtr_time_convert($this->settings->get_setting( 'schedule-open' )),
                    'schedule_closed' => tremtr_time_convert($this->settings->get_setting( 'schedule-closed' )),
                    'early_reservations' => is_admin() && current_user_can( 'manage_reservations' ) ? '' : $this->settings->get_setting( 'early-reservations' ),
                    'late_reservations' => is_admin() && current_user_can( 'manage_reservations' ) ? '' : $this->settings->get_setting( 'late-reservations' ),
                    'time_interval' => $this->settings->get_setting( 'time-interval' ),
                    'reservation_duration' => $this->settings->get_setting( 'reservation-duration' ),
                    'week_start' => $this->settings->get_setting( 'week-start' ) == '' ? '1' : $this->settings->get_setting( 'week-start' ),
                    'url'   => admin_url( 'admin-ajax.php' ),
                    'endpoint_cafe' => home_url() . '/wp-json/wp/v2/trem-cafes',
                    'endpoint_reservation' => home_url() . '/wp-json/wp/v2/trem-reservation/?per_page=100',
                    'nonce' => wp_create_nonce('wp_rest'),
                    'mainColor' => $this->settings->get_setting( 'main-color' ),
                    'fillActive' => tremtr_hex2rgb($this->settings->get_setting( 'available-color' )),
                    'fillBooked' => tremtr_hex2rgb($this->settings->get_setting( 'reserved-color' )),
                    'translation' => array(
                        'header' => __('Plan your day with us', 'tremtr'),
                        'calendar' => $this->settings->get_setting('i8n') == '' ? 'en' : $this->settings->get_setting('i8n'),
                        'workingHoursOpen' => __('open', 'tremtr'),
                        'bookTableButton' => __('Book a table', 'tremtr'),
                        'bookTableButtonWarning' => __('Please, select a table!', 'tremtr'),
                        'canvasClickWarning' => __('Please, select date and time!', 'tremtr'),
                        'table' => __('Table', 'tremtr'),
                        'no' => __('№', 'tremtr'),
                        'for' => __('for', 'tremtr'),
                        'people' => __('People', 'tremtr'),
                        'on' => __('on', 'tremtr'),
                        'from' => __('at', 'tremtr'),
                        'to' => __('till', 'tremtr'),
                        'in' => __('in', 'tremtr'),
                        'cafe' => get_bloginfo( 'name' ),
                        'changeButton' => __('Change', 'tremtr'),
                        'confirmButton' => __('Confirm', 'tremtr'),
                        'confirmButtonWarning' => __('Please, fill all fields correctly!', 'tremtr'),
                        'name' => __('Name', 'tremtr'),
                        'email' => __('Email', 'tremtr'),
                        'phone' => __('Phone', 'tremtr'),
                        'message' => __('Message', 'tremtr'),
                        'thanksAtTheEnd' => $this->settings->get_setting( 'success-message' ),
                        'privacy' => $this->settings->get_setting( 'privacy-message' ),
                        'rejected' => __('Someone has already reserved the table. Please, select another one.', 'tremtr'),
                    ),
                )
            );

        }

    }

endif;

global $tremtr_controller;
$tremtr_controller = new TREMTableReservation();
