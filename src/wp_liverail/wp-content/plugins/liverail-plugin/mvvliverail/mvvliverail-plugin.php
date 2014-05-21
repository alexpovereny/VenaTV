<?php
// Enable the JW Player Embed Wizard with preview.
// This can be buggy!
define('JWPLIMELIGHT_EMBED_WIZARD', false);


// Simple function to log to the debug log.
function jwplimelight_l( $message ) {
  if( WP_DEBUG === true ){
    if( is_array( $message ) || is_object( $message ) ){
      error_log( print_r( $message, true ) );
    } else {
      error_log( $message );
    }
  }
}
// Let's make some space in the log.
jwplimelight_l("\n--------------------------------------\nREQUEST: " . $_SERVER['REQUEST_URI'] . "\n\n");

define('MVVLIVERAIL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('MVVLIVERAIL_PLUGIN_DIR', dirname(__FILE__));

// Global variable to manage states.
$mvvliverail_global = array();


// Register the actions
//MVVLIVERAIL_Plugin::register_actions();

if ( is_admin() ) {
    require_once dirname(__FILE__) . '/liverailapi.php';
    require_once dirname(__FILE__) . '/mvvliverail-class-admin.php';
    //require_once dirname(__FILE__) . '/mvvliverail/mvvliverail-class-media.php';
    $mvvliverail_admin = new MVVLIVERAIL_Admin();
}
