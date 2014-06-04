<?php

define('MVVLIVERAIL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MVVLIVERAIL_PLUGIN_DIR', dirname(__FILE__));

// Global variable to manage states.
$mvvliverail_global = array();

// Register the actions
//MVVLIVERAIL_Plugin::register_actions();

if (is_admin()) {
    require_once dirname(__FILE__) . '/liverailapi.php';
    require_once dirname(__FILE__) . '/mvvliverail-class-admin.php';
    $mvvliverail_admin = new MVVLIVERAIL_Admin();
}
