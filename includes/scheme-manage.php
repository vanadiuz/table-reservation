<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TREMtrScheme' ) ) {
    class TREMtrSchemeRoute extends WP_REST_Controller {

        public function __construct() {

            add_action( 'rest_api_init', array($this, 'register_routes'));
        }


        public function my_awesome_func( $data ) {
            $posts = get_posts( array(
                'author' => $data['id'],
            ) );
            
            if ( empty( $posts ) ) {
                return new WP_Error( 'no_author', 'Invalid author', array( 'status' => 404 ) );
            }
            
            $response = new WP_REST_Response($posts[0]->post_title);

            //return $response;

            $a =  $data->get_params();

            //print_r($a);

            return $data['test'];
        }


        /**
         * Register the routes for the objects of the controller.
         */
        public function register_routes() {
            $version = '1';
            $namespace = 'tremtr/v' . $version;
            $base = 'reservations';
            register_rest_route( $namespace, '/' . $base, array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' )
                )
            ) );
            register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_item' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'id' => array(
                            'validate_callback' => function($param, $request, $key) {
                                return is_numeric( $param );
                            }
                        ),
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_item' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'force' => array(
                            'default' => false,
                        ),
                        'id' => array(
                            'validate_callback' => function($param, $request, $key) {
                                return is_numeric( $param );
                            }
                        ),
                    ),
                ),
            ) );
        }

        /**
         * Get a collection of items
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_items( $request ) {
            $items = array();
            $query = new WP_Query(array('nopaging' => true, 'post_type' => 'trem-reservation'));
            if ( $query->have_posts() ) {
                $i=0;
                while( $query->have_posts() ) {
                    $query->the_post();
                    $items[] = array(
                        "id" => get_the_ID(),
                        "name" => esc_html(get_the_title()),
                        "table" => get_post_meta( get_the_ID(), 'tremtr_reservation_table', true),
                        "timeStart" => get_post_meta( get_the_ID(), 'tremtr_reservation_time_begin', true),
                        "timeEnd" => get_post_meta( get_the_ID(), 'tremtr_reservation_time_end', true),
                        "date" => get_post_meta( get_the_ID(), 'tremtr_reservation_date', true),
                        "persons" => get_post_meta( get_the_ID(), 'tremtr_reservation_persons', true),
                        "message" => get_post_meta( get_the_ID(), 'tremtr_reservation_message', true),
                        "email" => get_post_meta( get_the_ID(), 'tremtr_reservation_email', true),
                        "phone" => get_post_meta( get_the_ID(), 'tremtr_reservation_phone', true),
                        "cafe" => get_post_meta( get_the_ID(), 'tremtr_reservation_cafe', true)
                    );
                }
            }
            wp_reset_query();

            $data = array();
            foreach( $items as $item ) {
                $data[] = $this->prepare_response_for_collection( $item );
            }
        
            return new WP_REST_Response( $data, 200 );
        }

        /**
         * Update item
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function update_item( $request ) {
          
          $my_post = array(
              'ID'           => $request['id'],
              'post_title'   => sanitize_text_field($request['name']),
          );
          wp_update_post( $my_post );

          update_post_meta( $request['id'], 'tremtr_reservation_table',  sanitize_text_field($request['table']));
          update_post_meta( $request['id'], 'tremtr_reservation_email',  sanitize_email($request['email']));
          update_post_meta( $request['id'], 'tremtr_reservation_phone',  sanitize_text_field($request['phone']));
          update_post_meta( $request['id'], 'tremtr_reservation_date',  preg_replace("([^0-9/])", "", $request['date']));
          update_post_meta( $request['id'], 'tremtr_reservation_time_end',  preg_replace("([^0-9:])", "", $request['timeEnd']));
          update_post_meta( $request['id'], 'tremtr_reservation_time_begin',  preg_replace("([^0-9:])", "", $request['timeStart']));
          update_post_meta( $request['id'], 'tremtr_reservation_persons',  sanitize_text_field($request['persons']));
          update_post_meta( $request['id'], 'tremtr_reservation_message',  sanitize_textarea_field($request['message']));
          update_post_meta( $request['id'], 'tremtr_reservation_cafe',  sanitize_textarea_field($request['cafe']));
          
          return new WP_REST_Response( 200 );
        }

        /**
         * Delete one item from the collection
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function delete_item( $request ) {
          wp_delete_post( $request['id']);
      
          return new WP_REST_Response( 200 );
        }
        


        /**
         * Check if a given request has access to get items
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|bool
         */
        public function get_items_permissions_check( $request ) {
            //return true; <--use to make readable by all
            return current_user_can( 'edit_others_posts' );
        }


    }
}

