<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//var_dump('support_ticketing !!!!');
//Поддержка / билетов 
//Support /ticketing
//o Easy ticketing system built INTO the platform, where we see which publisher /
//advertiser has a problem, and deal with it in the same place.  Programmatic
//o Advanced RTB facilities already built into LiveRail, but presented with our own functional UI.
//о Удобная система продажи билетов, встроенные в платформу, где мы видим, которые издатель / 
//Рекламодатель имеет проблемы, и справиться с ней в том же месте.  Программный 
//о Расширенный RTB объекты уже встроены в LiveRail, но представлены с нашей собственной функциональной UI.
function ticketing_system() {
    //global $test1;
     echo 'ticketing_system!!!';
    //$wp_ticketing->get_setting( 'blogname' )->transport         = 'postMessage';
    //$wp_ticketing->get_setting( 'blogdescription' )->transport  = 'postMessage';
    //$wp_ticketing->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
//register_nav_menu('in_header','Header Menu');
add_action('custom_ticketing_system', 'ticketing_system');
//wp_nav_menu( array('menu' => 'Project Nav' ));
/** Step 2 (from text above). */
add_action('admin_menu', 'ticketing_system_plugin_menu');

//add_menu_page('Page title', 'Top-level menu title', 'manage_options', 'my-top-level-handle', 'ticketing_system_plugin_menu');
//add_submenu_page( 'my-top-level-handle', 'Page title', 'Sub-menu title', 'manage_options', 'my-submenu-handle', 'ticketing_system_plugin_menu');

/** Step 1. */
function ticketing_system_plugin_menu() {
    //add_management_page( 'Ticketing System Options', 'Ticketing System', 'manage_options', 'my-unique-identifier', 'ticketing_system_plugin_options' );
    //add_dashboard_page( 'Ticketing System Options', 'Ticketing System', 'manage_options', 'my-unique-identifier', 'ticketing_system_plugin_options' );
    //add_options_page($page_title, $menu_title, $capability, $menu_slug)
    add_options_page('Ticketing System Options', 'Ticketing System', 'manage_options', 'my-unique-identifier', 'ticketing_system_plugin_options');

    //add_submenu_page('Ticketing System Options', 'Ticketing System', 'manage_options', 'my-unique-identifier', 'ticketing_system_plugin_options');
}

/** Step 3. */
function ticketing_system_plugin_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    echo '<div class="wrap">';
    echo 'Ticketing System Options!!!';
    echo '<p>Here is where the form would go if I actually had options.</p>';
    echo '</div>';
}