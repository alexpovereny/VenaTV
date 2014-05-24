<?php

/**
 * Description of reporting-dashboard
 *
 * @author Melnik Valeriya
 */
add_action('admin_menu', 'my_users_menu');

function my_users_menu() {

    add_users_page('Reporting and Dashboard', 'Reporting and Dashboard', 'read', 'my-unique-identifier', 'my_plugin_function');
}

function my_plugin_function() {
    echo '<br>Reporting and Dashboard<br>my_plugin_function test!!!';
}

//echo 'reporting-dashboard php1111';
/*
  add_action( 'admin_menu', 'register_my_custom_menu_page' );

  function register_my_custom_menu_page(){
  echo 'register_my_custom_menu_page!!!';
  add_menu_page( 'custom menu title', 'custom menu', 'manage_options', 'liverail/liverail-admin.php', '', plugins_url( 'liverail/images/liverail.png' ), 6 );
  }
 */

/*
//work!!!
add_action('admin_menu', 'register_my_custom_menu_page');

function register_my_custom_menu_page() {
    //echo '<br> --- register_my_custom_menu_page!!!';
    add_menu_page('LiveRail title', 'LiveRail', 'manage_options', 'custompage', 'my_custom_menu_page', plugins_url('liverail/images/search-icon.png'), 6);
}

function my_custom_menu_page() {
    echo "<br>LiveRail Admin Page Test";
}
*/


/*
add_action( 'admin_menu', 'liverail_page' );
function liverail_page() {
      add_users_page('My Plugin Users', 'Reporting and Dashboard', 'read', 'my-unique-identifier', 'my_plugin_function');

}*/

// add page visible to editors
/* add_action( 'admin_menu', 'register_my_page' );
  function register_my_page(){
  //add_menu_page( 'My Page Title', 'My Page', 'edit_others_posts', 'my_page_slug', 'my_page_function', plugins_url( 'liverail/images/liverail.png' ), 6 );


  add_menu_page('Web Invoice System', 'Web Invoice!', 6, __FILE__, 'wp_invoice_options_page');
  }

  // modify capability
  function my_page_capability( $capability ) {
  return 'edit_others_posts';
  }
  add_filter( 'option_page_capability_my_page_slug', 'my_page_capability' );
 */
/*
  add_action('admin_menu', 'your_menu');
  function your_menu () {
  echo 'your_menu!!!';
  //add_users_page('Title', 'Your Title', 1, basename(__FILE__), 'some_function');
  // add_menu_page('Web Invoice System', 'Web Invoice', 6, __FILE__, 'wp_invoice_options_page');
  add_users_page('Web Invoice System', 'Web Invoice', 6, __FILE__, 'wp_invoice_options_page');
  }
 */

