<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

function tremtr_meta_boxes() {
    add_meta_box( 'tremtr_main_metabox', esc_html__('Cafe Plan', 'tremtr'), 'tremtr_main_metabox_output', 'trem-cafes', 'normal', 'high' );
    add_meta_box( 'tremtr_tables', esc_html__('Tables', 'tremtr'), 'tremtr_properties_metabox_output', 'trem-cafes', 'side', 'high');
    add_meta_box( 'tremtr_shortcode', esc_html__( 'Shortcode', 'tremtr' ), 'tremtr_shortcode', 'trem-cafes', 'normal', 'low' );
    add_meta_box( 'tremtr_canvas_content', esc_html__( 'Canvas Content', 'tremtr' ), 'tremtr_canvas_content', 'trem-cafes', 'normal', 'low' );

    function tremtr_default_hidden_meta_boxes( $hidden, $screen ) {
        // Grab the current post type
        $post_type = $screen->post_type;
        // If we're on a 'post'...
        if ( $post_type == 'trem-cafes' ) {
            // Define which meta boxes we wish to hide
            $hidden = array(
                'tremtr_canvas_content',
                'mymetabox_revslider_0'
            );
            // Pass our new defaults onto WordPress
            return $hidden;
        }
        // If we are not on a 'post', pass the
        // original defaults, as defined by WordPress
        return $hidden;
    }
    add_action( 'default_hidden_meta_boxes', 'tremtr_default_hidden_meta_boxes', 10, 2 );
}


function tremtr_main_metabox_output($post){
    wp_enqueue_script( 'tremtr-fontawesome' );
    wp_enqueue_script( 'tremtr-fabric' );
    wp_enqueue_script( 'tremtr-admin_context_menu' );
    wp_enqueue_script( 'tremtr-admin_main' );
    wp_enqueue_script( 'tremtr-front-data' );
?>

    <div class="trem-canvas">
        <div class="container">
            <ul id="control-items" class="column">
                <li><a href="#" id="add">Add</a></li>
                <li><a href="#" id="edit">Edit</a></li>
            </ul>
        </div>
        <canvas id="c"  class="context-menu-one" style="border:1px solid #ccc"></canvas>
    </div>
<?php
}

//Outputs the content of the description meta box
function tremtr_properties_metabox_output($post){
    ?>
    <div class="control-panel-info">
        <span class="status">Status</span>  
        <span class="number">№</span>
        <span class="users">Seats</span>
        <span class="size">Width</span>
        <span class="size">Height</span>
        <span class="remove">Remove</span>  
    </div>
    <ul id="control-items"></ul>

    <?php
}

// The function that outputs the metabox html
function tremtr_shortcode() {
    ?>
    <span>
    <?php _e('To output content of the plugin use [table-reservation] shortcode', 'tremtr'); ?>
    </span>
    <?php
}


// The function that outputs the metabox html
function tremtr_canvas_content() {

    $content = get_post_meta( get_the_ID(), 'tremtr_content', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_content', true ) : '';
    ?>
        <textarea name="tremtr_content" id="tremtr_content"><?php echo $content; ?></textarea>
    <?php
}
/**
 * Save Custom MetaBox fields
 *
 * @since 0.1
 * @return boolean
 */
function tremtr_save_meta_boxes( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );

    if (isset($_POST['tremtr_height'])) {
        update_post_meta($post_id, 'tremtr_height', $_POST['tremtr_height']);
    } else
        delete_post_meta($post_id, 'tremtr_height');

    if (isset($_POST['tremtr_content'])) {
        update_post_meta($post_id, 'tremtr_content', $_POST['tremtr_content']);
    } else
        delete_post_meta($post_id, 'tremtr_content');

}
add_action( 'save_post', 'tremtr_save_meta_boxes' );



//////////////// RESERVATION ///////////////////


function tremtr_reservation_meta_boxes() {
    add_meta_box( 'tremtr_reservation_meta', esc_html__( 'Reservation Data', 'tremtr' ), 'tremtr_reservation_meta', 'trem-reservation', 'normal', 'default' );

    function tremtr_reservatin_default_hidden_meta_boxes( $hidden, $screen ) {
        $post_type = $screen->post_type;
        if( $post_type == 'trem-reservation' ){
            $hidden = array(
                'slugdiv',
                'mymetabox_revslider_0'
            );
            return $hidden;
        }
        return $hidden;
    }
    add_action( 'default_hidden_meta_boxes', 'tremtr_reservatin_default_hidden_meta_boxes', 10, 2 );
}

function tremtr_reservation_meta() {
    $date_reserv = get_post_meta( get_the_ID(), 'tremtr_reservation_date', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_date', true ) : '';
    $time_begin = get_post_meta( get_the_ID(), 'tremtr_reservation_time_begin', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_time_begin', true ) : '';
    $time_end = get_post_meta( get_the_ID(), 'tremtr_reservation_time_end', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_time_end', true ) : '';
    $table = get_post_meta( get_the_ID(), 'tremtr_reservation_table', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_table', true ) : '';
    $persons = get_post_meta( get_the_ID(), 'tremtr_reservation_persons', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_persons', true ) : '';
    $name = get_post_meta( get_the_ID(), 'tremtr_reservation_name', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_name', true ) : '';
    $email = get_post_meta( get_the_ID(), 'tremtr_reservation_email', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_email', true ) : '';
    $phone = get_post_meta( get_the_ID(), 'tremtr_reservation_phone', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_phone', true ) : '';
    $message = get_post_meta( get_the_ID(), 'tremtr_reservation_message', true ) != '' ? get_post_meta( get_the_ID(), 'tremtr_reservation_message', true ) : '';
    ?>
    <div class="tremtr-reservation">
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_date"><?php _e('Date', 'tremtr'); ?>
                <input type="text" class="tremtr-date" name="tremtr_reservation_date" id="tremtr_reservation_date" required value="<?php echo esc_attr($date_reserv); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_time_begin"><?php _e('Time Begin', 'tremtr'); ?>
                <input type="text" class="tremtr-time" name="tremtr_reservation_time_begin" id="tremtr_reservation_time_begin" required value="<?php echo esc_attr($time_begin); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_time_end"><?php _e('Time End', 'tremtr'); ?>
                <input type="text" class="tremtr-time" name="tremtr_reservation_time_end" id="tremtr_reservation_time_end" value="<?php echo esc_attr($time_end); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_table"><?php _e('Table', 'tremtr'); ?>
                <input name="tremtr_reservation_table" id="tremtr_reservation_table" required type="number" value="<?php echo esc_attr($table); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_persons"><?php _e('Persons', 'tremtr'); ?>
                <input name="tremtr_reservation_persons" id="tremtr_reservation_persons" required type="number" value="<?php echo esc_attr($persons); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_name"><?php _e('Name', 'tremtr'); ?>
                <input name="tremtr_reservation_name" id="tremtr_reservation_name" type="text" value="<?php echo esc_attr($name); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_email"><?php _e('E-mail', 'tremtr'); ?>
                <input name="tremtr_reservation_email" id="tremtr_reservation_email" required type="email" value="<?php echo esc_attr($email); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_phone"><?php _e('Phone number', 'tremtr'); ?>
                <input name="tremtr_reservation_phone" id="tremtr_reservation_phone" type="tel" value="<?php echo esc_attr($phone); ?>"/>
            </label>
        </div>
        <div class="tremtr-reservation-field">
            <label for="tremtr_reservation_message"><?php _e('Message', 'tremtr'); ?>
                <textarea name="tremtr_reservation_message" id="tremtr_reservation_message"><?php echo wp_kses_post($message); ?></textarea>
            </label>
        </div>
    </div>
    <?php
}

function tremtr_reservation_save_meta( $post_id ) {

    if (isset($_POST['tremtr_reservation_date'])) {
        update_post_meta($post_id, 'tremtr_reservation_date', $_POST['tremtr_reservation_date']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_date');

    if (isset($_POST['tremtr_reservation_time_begin'])) {
        update_post_meta($post_id, 'tremtr_reservation_time_begin', $_POST['tremtr_reservation_time_begin']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_time_begin');

    if (isset($_POST['tremtr_reservation_time_end'])) {
        update_post_meta($post_id, 'tremtr_reservation_time_end', $_POST['tremtr_reservation_time_end']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_time_end');

    if (isset($_POST['tremtr_reservation_table'])) {
        update_post_meta($post_id, 'tremtr_reservation_table', $_POST['tremtr_reservation_table']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_table');

    if (isset($_POST['tremtr_reservation_persons'])) {
        update_post_meta($post_id, 'tremtr_reservation_persons', $_POST['tremtr_reservation_persons']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_persons');

    if (isset($_POST['tremtr_reservation_name'])) {
        update_post_meta($post_id, 'tremtr_reservation_name', $_POST['tremtr_reservation_name']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_name');

    if (isset($_POST['tremtr_reservation_email'])) {
        update_post_meta($post_id, 'tremtr_reservation_email', $_POST['tremtr_reservation_email']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_email');

    if (isset($_POST['tremtr_reservation_phone'])) {
        update_post_meta($post_id, 'tremtr_reservation_phone', $_POST['tremtr_reservation_phone']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_phone');

    if (isset($_POST['tremtr_reservation_message'])) {
        update_post_meta($post_id, 'tremtr_reservation_message', $_POST['tremtr_reservation_message']);
    } else
        delete_post_meta($post_id, 'tremtr_reservation_message');
}
add_action( 'save_post', 'tremtr_reservation_save_meta' );


add_filter('manage_edit-trem-cafes_columns', 'trem_cafes_edit_columns');
function trem_cafes_edit_columns($columns){
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => 'Title',
        'image' => 'Image',
        'id' => 'ID',
        'date' => 'Date'
    );

    return $columns;
}

add_action('manage_posts_custom_column',  'trem_cafes_custom_columns');
function trem_cafes_custom_columns($column){
    global $post;
    switch ($column) {
        case 'id':
            echo $post->ID;
            break;

        case 'image':
            the_post_thumbnail('thumbnail');
            break;
    }
}

add_filter('manage_trem-reservation_posts_columns', 'trem_reservation_edit_columns', 10);
function trem_reservation_edit_columns($columns){
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => 'Name',
        'reservation_date' => 'Reserved for',
        'table' => 'Table №',
        'persons' => 'Persons',
        'phone' => 'Phone',
        'email' => 'E-mail',
        'date' => 'Date'
    );

    return $columns;
}

add_action('manage_trem-reservation_posts_custom_column', 'trem_reservation_custom_column', 10, 2);
function trem_reservation_custom_column($column_name, $post_ID){
    switch ($column_name) {
        case 'reservation_date':
            $datetime = __( 'Date:', 'tremtr' ).' <span class="tremtr_reservation_date">'.get_post_meta( $post_ID, 'tremtr_reservation_date', true ).'</span><br>'.__( 'Time:', 'tremtr' ).' <span class="tremtr_reservation_time_begin">'.get_post_meta( $post_ID, 'tremtr_reservation_time_begin', true ).'</span>';
            if(get_post_meta( $post_ID, 'tremtr_reservation_time_end', true ) != ''){
                $datetime .= ' - <span class="tremtr_reservation_time_end">'.get_post_meta( $post_ID, 'tremtr_reservation_time_end', true ).'</span>';
            }
            echo $datetime;
            break;

        case 'table':
            echo '<span class="tremtr_reservation_table">'.get_post_meta( $post_ID, 'tremtr_reservation_table', true ).'</span>';
            break;

        case 'persons':
            echo '<span class="tremtr_reservation_persons">'.get_post_meta( $post_ID, 'tremtr_reservation_persons', true ).'</span>';
            break;

        case 'phone':
            echo '<span class="tremtr_reservation_phone">'.get_post_meta( $post_ID, 'tremtr_reservation_phone', true ).'</span>';
            break;

        case 'email':
            echo '<span class="tremtr_reservation_email">'.get_post_meta( $post_ID, 'tremtr_reservation_email', true ).'</span>';
            break;
    }
}

add_filter( 'manage_edit-trem-reservation_sortable_columns', 'trem_reservation_sortable_columns' );
function trem_reservation_sortable_columns( $columns ) {
    $columns['reservation_date'] = 'reservation_date';

    return $columns;
}

add_action( 'pre_get_posts', 'trem_custom_orderby' );
function trem_custom_orderby( $query ) {
    if ( ! is_admin() )
        return;

    $orderby = $query->get( 'orderby');
    $order = $query->get( 'order');

    if ( 'reservation_date' == $orderby ) {

        $query->set( 'meta_query', array(
            'tremtr_reservation_date' => array(
                'key' => 'tremtr_reservation_date',
            ),
            'tremtr_reservation_time_begin' => array(
                'key' => 'tremtr_reservation_time_begin',
            ),
            'tremtr_reservation_time_end' => array(
                'key' => 'tremtr_reservation_time_end',
            )
        ));
        $query->set( 'orderby', array(
            'tremtr_reservation_date' => $order,
            'tremtr_reservation_time_begin'   => 'ASC',
            'tremtr_reservation_time_end'   => 'ASC',
        ) );
    }
}

// Add our text to the quick edit box
add_action('quick_edit_custom_box', 'trem_reservation_quick_edit_custom_box', 10, 2);
function trem_reservation_quick_edit_custom_box($column_name, $post_type){
    if( $post_type != 'trem-reservation' ) {
        return;
    }

    switch ( $column_name ) {
        case 'reservation_date':
            echo '
            <fieldset class="inline-edit-col-left tremtr" style="clear:both;">
                <div class="inline-edit-col">
                    <div class="inline-edit-group">
                        <label class="alignleft">                                    
                            <span class="title">'.__( 'Reserv. Date', 'tremtr' ).'</span>
                            <span class="input-text-wrap"><input type="text" class="tremtr-date" name="tremtr_reservation_date" id="tremtr_reservation_date" value="'.get_post_meta( get_the_ID(), 'tremtr_reservation_date', true ).'"></span>
                        </label>
                        <label class="alignleft">                                    
                            <span class="title" style="text-align:right;margin-left:-10px;">'.__( 'From', 'tremtr' ).'</span>
                            <span class="input-text-wrap"><input type="text" class="tremtr-time" name="tremtr_reservation_time_begin" id="tremtr_reservation_time_begin" value="'.get_post_meta( get_the_ID(), 'tremtr_reservation_time_begin', true ).'"></span>
                        </label>
                        <label class="alignleft">                                    
                            <span class="title" style="text-align:right;margin-left:-10px;">'.__( 'To', 'tremtr' ).'</span>
                            <span class="input-text-wrap"><input type="text" class="tremtr-time" name="tremtr_reservation_time_end" id="tremtr_reservation_time_end" value="'.get_post_meta( get_the_ID(), 'tremtr_reservation_time_end', true ).'"></span>
                        </label>
                    </div>
            ';
            break;
        case 'table':
            echo '
                    <div class="inline-edit-group">
                        <label class="alignleft">                                    
                            <span class="title">'.__( 'Table N', 'tremtr' ).'</span>
                            <span class="input-text-wrap"><input type="text" name="tremtr_reservation_table" value="'.get_post_meta( get_the_ID(), 'tremtr_reservation_table', true ).'"></span>
                        </label>
                    </div>
            ';
            break;
        case 'persons':
            echo '
                    <div class="inline-edit-group">
                        <label class="alignleft">                                    
                            <span class="title">'.__( 'Persons', 'tremtr' ).'</span>
                            <span class="input-text-wrap"><input type="text" name="tremtr_reservation_persons" value="'.get_post_meta( get_the_ID(), 'tremtr_reservation_persons', true ).'"></span>
                        </label>
                    </div>
            ';
            break;
        case 'phone':
            echo '
                    <div class="inline-edit-group">
                        <label class="alignleft">                                    
                            <span class="title">'.__( 'Phone', 'tremtr' ).'</span>
                            <span class="input-text-wrap"><input type="text" name="tremtr_reservation_phone" value="'.get_post_meta( get_the_ID(), 'tremtr_reservation_phone', true ).'"></span>
                        </label>
                    </div>
            ';
            break;
        case 'email':
            echo '
                    <div class="inline-edit-group">
                        <label class="alignleft">                                    
                            <span class="title">'.__( 'E-mail', 'tremtr' ).'</span>
                            <span class="input-text-wrap"><input type="text" name="tremtr_reservation_email" value="'.get_post_meta( get_the_ID(), 'tremtr_reservation_email', true ).'"></span>
                        </label>
                    </div>
            </fieldset>
            ';
            break;
    }

}

add_action('save_post', 'tremtr_save_quick_edit_data');
function tremtr_save_quick_edit_data($post_id) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;
    if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;
    }

    if ( isset($_POST['tremtr_reservation_date']) ) {
        update_post_meta($post_id, 'tremtr_reservation_date', $_POST['tremtr_reservation_date']);
    } else {
        delete_post_meta($post_id, 'tremtr_reservation_date');
    }

    if ( isset($_POST['tremtr_reservation_time_begin']) ) {
        update_post_meta($post_id, 'tremtr_reservation_time_begin', $_POST['tremtr_reservation_time_begin']);
    } else {
        delete_post_meta($post_id, 'tremtr_reservation_time_begin');
    }

    if ( isset($_POST['tremtr_reservation_time_end']) ) {
        update_post_meta($post_id, 'tremtr_reservation_time_end', $_POST['tremtr_reservation_time_end']);
    } else {
        delete_post_meta($post_id, 'tremtr_reservation_time_end');
    }

    if ( isset($_POST['tremtr_reservation_table']) ) {
        update_post_meta($post_id, 'tremtr_reservation_table', $_POST['tremtr_reservation_table']);
    } else {
        delete_post_meta($post_id, 'tremtr_reservation_table');
    }

    if ( isset($_POST['tremtr_reservation_persons']) ) {
        update_post_meta($post_id, 'tremtr_reservation_persons', $_POST['tremtr_reservation_persons']);
    } else {
        delete_post_meta($post_id, 'tremtr_reservation_persons');
    }

    if ( isset($_POST['tremtr_reservation_phone']) ) {
        update_post_meta($post_id, 'tremtr_reservation_phone', $_POST['tremtr_reservation_phone']);
    } else {
        delete_post_meta($post_id, 'tremtr_reservation_phone');
    }

    if ( isset($_POST['tremtr_reservation_email']) ) {
        update_post_meta($post_id, 'tremtr_reservation_email', $_POST['tremtr_reservation_email']);
    } else {
        delete_post_meta($post_id, 'tremtr_reservation_email');
    }

}

add_action( 'admin_footer', 'tremtr_quick_edit_js' );
function tremtr_quick_edit_js() {
    global $current_screen;
    if ( ($current_screen->id !== 'edit-trem-reservation') || ($current_screen->post_type !== 'trem-reservation') ) {
        ?>
        <script type="text/javascript">
            function trem_inline_quick_edit_defaults() { /*_*/ }
        </script>
        <?php
    } else {
        ?>
        <script type="text/javascript">
            jQuery('.editinline').live('click', function(){
                var post_id = jQuery(this).parents('tr').attr('id');
                var edit_id = post_id.replace('post', 'edit');

                /// hide fields
                var fieldset = jQuery('.inline-edit-col-left', '#'+edit_id).first();
                jQuery('.title:first', fieldset).text('Name');
                jQuery('label:nth-of-type(2)', fieldset).hide();
                jQuery('.inline-edit-date', fieldset).addClass('hidden');
                jQuery('.inline-edit-group', fieldset).addClass('hidden');
                jQuery('br', fieldset).hide();


                /// set values
                var reserv_date = jQuery('.reservation_date .tremtr_reservation_date', '#'+post_id).text();
                var time_begin = jQuery('.reservation_date .tremtr_reservation_time_begin', '#'+post_id).text();
                var time_end = jQuery('.reservation_date .tremtr_reservation_time_end', '#'+post_id).text();
                var table = jQuery('.table .tremtr_reservation_table', '#'+post_id).text();
                var persons = jQuery('.persons .tremtr_reservation_persons', '#'+post_id).text();
                var phone = jQuery('.phone .tremtr_reservation_phone', '#'+post_id).text();
                var email = jQuery('.email .tremtr_reservation_email', '#'+post_id).text();
                jQuery(':input[name="tremtr_reservation_date"]', '.inline-edit-row').val(reserv_date);
                jQuery(':input[name="tremtr_reservation_time_begin"]', '.inline-edit-row').val(time_begin);
                jQuery(':input[name="tremtr_reservation_time_end"]', '.inline-edit-row').val(time_end);
                jQuery(':input[name="tremtr_reservation_table"]', '.inline-edit-row').val(table);
                jQuery(':input[name="tremtr_reservation_phone"]', '.inline-edit-row').val(phone);
                jQuery(':input[name="tremtr_reservation_email"]', '.inline-edit-row').val(email);
                return false;
            });

            function trem_quick_flatpickr() {

                jQuery(".tremtr-date").flatpickr({
                    dateFormat: 'Y/m/d'
                });

                jQuery(".tremtr-time").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    enableSeconds: false,
                    dateFormat: "H:i",
                    time_24hr: true,
                    minuteIncrement: parseInt(tremtr_data.time_interval)
                });

            }

            function trem_inline_quick_edit_defaults() {
                // revert Quick Edit menu so that it refreshes properly
                inlineEditPost.revert();
                setTimeout(trem_quick_flatpickr, 500);
            }

        </script>
        <?php
    }
}

add_filter( 'post_row_actions', 'tremtr_expand_quick_edit_link', 10, 2 );
function tremtr_expand_quick_edit_link($actions, $post) {
    global $current_screen;

    $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
    $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
    $actions['inline hide-if-no-js'] .= " onclick=\"trem_inline_quick_edit_defaults()\">";
    $actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
    $actions['inline hide-if-no-js'] .= '</a>';
    return $actions;
}

add_action( 'rest_api_init', 'tremtr_rest_meta_register' );
function tremtr_rest_meta_register(){

    register_rest_field( 'trem-reservation',
        'tremtr_reservation_date',
        array(
            'get_callback'    => 'tremtr_rest_get_date',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_date( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    register_rest_field( 'trem-reservation',
        'tremtr_reservation_time_begin',
        array(
            'get_callback'    => 'tremtr_rest_get_time_begin',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_time_begin( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    register_rest_field( 'trem-reservation',
        'tremtr_reservation_time_end',
        array(
            'get_callback'    => 'tremtr_rest_get_time_end',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_time_end( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    register_rest_field( 'trem-reservation',
        'tremtr_reservation_table',
        array(
            'get_callback'    => 'tremtr_rest_get_table',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_table( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    register_rest_field( 'trem-reservation',
        'tremtr_reservation_persons',
        array(
            'get_callback'    => 'tremtr_rest_get_persons',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_persons( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }



    register_rest_field( 'trem-cafes',
        'slide_template',
        array(
            'get_callback'    => 'tremtr_rest_get_template',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_template( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    register_rest_field( 'trem-cafes',
        'tremtr_height',
        array(
            'get_callback'    => 'tremtr_rest_get_height',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_height( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    register_rest_field( 'trem-cafes',
        'tremtr_content',
        array(
            'get_callback'    => 'tremtr_rest_get_content',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    function tremtr_rest_get_content( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }
}

function tremtr_time_convert($arr){
    if(is_array($arr)){
        foreach($arr as $key => $val){
            if(isset($val['time']['start']) && strstr($val['time']['start'], 'AM')){
                $arr[$key]['time']['start'] = str_replace(' AM', '', $val['time']['start']);
            } elseif(isset($val['time']['start']) && strstr($val['time']['start'], 'PM')) {
                $temp = explode(':', str_replace(' PM', '', $val['time']['start']));
                $arr[$key]['time']['start'] = ($temp[0]+12).':'.$temp[1];
            }
            if(isset($val['time']['end']) && strstr($val['time']['end'], 'AM')){
                $arr[$key]['time']['end'] = str_replace(' AM', '', $val['time']['end']);
            } elseif(isset($val['time']['end']) && strstr($val['time']['end'], 'PM')) {
                $temp = explode(':', str_replace(' PM', '', $val['time']['end']));
                $arr[$key]['time']['end'] = ($temp[0]+12).':'.$temp[1];
            }
        }
        return $arr;
    } else {
        return 0;
    }
}

function tremtr_hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    $rgb = array($r, $g, $b);
    //return $rgb; // returns an array with the rgb values
    return implode(",", $rgb); // returns the rgb values separated by commas
}

?>