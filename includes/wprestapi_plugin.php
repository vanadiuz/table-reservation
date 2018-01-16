<?php

// require_once( TREMTR_PLUGIN_DIR . '/includes/class-tgm-plugin-activation.php' );
// add_action( 'tremtrpa_register', 'tremtr_register_required_plugins' );

// function tremtr_register_required_plugins(){

// 	$plugins = array(
// 		array(
// 			'name'      => 'WordPress REST API',
// 			'slug'      => 'rest-api',
// 			'required'  => true,
// 		),
// 	);

// 	$config = array(
// 		'id'           => 'termtr',                 // Unique ID for hashing notices for multiple instances of TGMPA.
// 		'default_path' => '',                      // Default absolute path to bundled plugins.
// 		'menu'         => 'tremtr-install-plugins', // Menu slug.
// 		'parent_slug'  => 'plugins.php',            // Parent menu slug.
// 		'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
// 		'has_notices'  => true,                    // Show admin notices or not.
// 		'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
// 		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
// 		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
// 		'message'      => '',                      		// Message to output right before the plugins table.
// 	);

// 	tremtrpa( $plugins, $config );

// }