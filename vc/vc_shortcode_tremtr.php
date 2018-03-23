<?php
/*
* Module - Scrollme Section
*/
if(!class_exists('TREMTR_VC')){
	class TREMTR_VC{
		function __construct(){
			add_shortcode('trem_table_reservation',array($this,'table_reservation_shortcode'));
			add_action('init',array($this,'table_reservation_shortcode_mapper'));
		}

		function table_reservation_shortcode($atts){
			$output = '';
			extract(shortcode_atts(array(
				"tremtr_id" => "0",
			),$atts));

			$output = do_shortcode('[table-reservation]');

			return $output;
		}

		function table_reservation_shortcode_mapper(){
			if(function_exists('vc_map')){
				$args = array( 'post_type' => 'trem-cafes');
				$tremtr_forms = get_posts($args);
				$tremtr = array();
				if(empty($tremtr_forms['errors'])){
					$tremtr[esc_html__('All Cafes', 'tremtr')] = 0;
					foreach($tremtr_forms as $form){
						$tremtr[$form->post_title] = $form->ID;
					}
				}

				vc_map(
					array(
						'name' => esc_html__('Table Reservation', 'tremtr'),
						'base' => 'trem_table_reservation',
						'class' => 'pix-theme-icon',
						'category' => esc_html__('Plugins', 'tremtr'),
						'show_settings_on_create' => false,
						'params' => array(/*
							array(
								'type' => 'dropdown',
								'heading' => esc_html__('Select Cafe', 'tremtr'),
								'param_name' => 'tremtr_id',
								'value' => $tremtr,
								'description' => esc_html__('Select cafe to show form reservation', 'tremtr'),
							),*/
						)
					)
				);
			}
		}
	}

	// Instantiate the class
	new TREMTR_VC;
	if ( class_exists( 'WPBakeryShortCodes' ) ) {
		class WPBakeryShortCode_trem_table_reservation extends WPBakeryShortCodes {
		}
	}
}