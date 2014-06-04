<?php

class MVVLIVERAIL_Admin { 

    public function __construct() {
        add_action("init", array($this, 'enqueue_scripts_and_styles'));
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    public static function enqueue_scripts_and_styles() {
        //bootstrap params
        /* wp_register_style('mvvliverail-bootstrap-min-css', MVVLIVERAIL_PLUGIN_URL . 'bootstrap/bootstrap.min.css');
          wp_enqueue_style('mvvliverail-bootstrap-min-css');
          wp_register_script('mvvliverail-jquery-min-js', MVVLIVERAIL_PLUGIN_URL . 'bootstrap/jquery-1.10.2.min.js');
          wp_enqueue_script('mvvliverail-jquery-min-js');
          wp_register_script('mvvliverail-jquery-data-tables-min-js', MVVLIVERAIL_PLUGIN_URL . 'bootstrap/jquery.dataTables.min.js');
          wp_enqueue_script('mvvliverail-jquery-data-tables-min-js');
          wp_register_script('mvvliverail-jquery-td-pagination-js', MVVLIVERAIL_PLUGIN_URL . 'bootstrap/jquery-DT-pagination.js');
          wp_enqueue_script('mvvliverail-jquery-td-pagination-js'); */

        //media tables params
        wp_register_style('mvvliverail-media-jquery-data-tables-css', MVVLIVERAIL_PLUGIN_URL . 'media/css/jquery.dataTables.css');
        wp_enqueue_style('mvvliverail-media-jquery-data-tables-css');
        wp_register_script('mvvliverail-media-jquery-js', MVVLIVERAIL_PLUGIN_URL . 'media/js/jquery.js');
        wp_enqueue_script('mvvliverail-media-jquery-js');
        wp_register_script('mvvliverail-media-jquery-data-tables-js', MVVLIVERAIL_PLUGIN_URL . 'media/js/jquery.dataTables.js');
        wp_enqueue_script('mvvliverail-media-jquery-data-tables-js');
        // wp_register_script('mvvliverail-jquery-td-pagination-js', MVVLIVERAIL_PLUGIN_URL . 'bootstrap/jquery-DT-pagination.js');
        // wp_enqueue_script('mvvliverail-jquery-td-pagination-js');
        // plugin params
        wp_register_script('mvvliverail-admin-js', MVVLIVERAIL_PLUGIN_URL . 'js/mvvliverail-admin.js');
        wp_enqueue_script('mvvliverail-admin-js');
        wp_register_style('mvvliverail-admin-css', MVVLIVERAIL_PLUGIN_URL . 'css/mvvliverail-admin.css');
        wp_enqueue_style('mvvliverail-admin-css');
        wp_register_style('mvvliverail-admin-bootstrap-css', MVVLIVERAIL_PLUGIN_URL . 'bootstrap/bootstrap.min.css');
        wp_enqueue_style('mvvliverail-admin-bootstrap-css');
    }

//Smart Pricing floors

    public function admin_menu() {
        $admin = add_menu_page(
                "LiveRail Title", // $page_title
                "LiveRail Admin", // $menu_title
                current_user_can('network') ? 'network' : 'administrator', //"network" or 'administrator'
                MVVLIVERAIL . "menu", // $menu_slug
                array($this, 'admin_pages'), // $function
                MVVLIVERAIL_PLUGIN_URL . "img/wordpress.png" // $icon_url
                //plugins_url('liverail-plugin/img/wordpress.png')// $icon_url
                //MVVLIVERAIL_PLUGIN_URL . "./img/wordpress.png"  // $icon_url
        );
        add_submenu_page(
                MVVLIVERAIL . "menu", //
                "LiveRail Users", //
                "LiveRail users", //
                "network", //
                MVVLIVERAIL . "menu_liverail_all_users", //
                array($this, 'admin_pages')//
        );
        add_submenu_page(
                MVVLIVERAIL . "menu", //
                "LiveRail Configurations", //
                "LiveRail management", //
                "administrator", //
                MVVLIVERAIL . "menu_liverail_configurations", //
                array($this, 'admin_pages')//
        );
        add_submenu_page(
                MVVLIVERAIL . "menu", //
                "LiveRail Users", //
                "LiveRail user management", //
                "administrator", //
                MVVLIVERAIL . "menu_liverail_user_management", //
                array($this, 'admin_pages')//
        );
        add_submenu_page(
                MVVLIVERAIL . "menu", //
                "LiveRail campaigns", //
                "LiveRail campaigns", //
                "administrator", //
                MVVLIVERAIL . "menu_liverail_campaigns", //
                array($this, 'admin_pages')//
        );
        add_submenu_page(
                MVVLIVERAIL . "menu", //
                "LiveRail orders", //
                "LiveRail orders", //
                "administrator", //
                MVVLIVERAIL . "menu_liverail_orders", //
                array($this, 'admin_pages')//
        );
        
    }

    // Add js for plugin tabs.
    function add_admin_js() {
        //wp_enqueue_script("jquery-ui-core");
        //echo '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/' .
        //'bootstrap/bootstrap.min.css" type="text/css" media="print, projection, screen" />' . "\n";
    }

    public function admin_pages() {
        require_once(MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page.php');
        require_once(MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-form-field.php');
        switch ($_GET["page"]) {
            case MVVLIVERAIL . "menu_liverail_configurations" :
                require_once (MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page-configurations.php');
                $page = new MVVLIVERAIL_Admin_Page_Configurations();
                break;
            //menu_liverail_all_users
            case MVVLIVERAIL . "menu_liverail_all_users":
                require_once (MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page-users.php');
                $page = new MVVLIVERAIL_Admin_Page_Users();
                break;
            case MVVLIVERAIL . "menu_liverail_user_management":
                require_once (MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page-user-management.php');
                $page = new MVVLIVERAIL_Admin_Page_User_Management();
                break;
            case MVVLIVERAIL . "menu_liverail_campaigns":
                require_once (MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page-campaigns.php');
                $page = new MVVLIVERAIL_Admin_Page_Campaigns();
                break;
            case MVVLIVERAIL . "menu_liverail_campaigns":
                require_once (MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page-campaigns.php');
                $page = new MVVLIVERAIL_Admin_Page_Campaigns();
                break;
            case MVVLIVERAIL . "menu_liverail_orders" :
                require_once (MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page-orders.php');
                $page = new MVVLIVERAIL_Admin_Page_Orders();
                break;
         
            default:
                require_once (MVVLIVERAIL_PLUGIN_DIR . '/mvvliverail-class-admin-page-default.php');
                $page = new MVVLIVERAIL_Admin_Page_Default();
                break;
        }
        $page->page_slug = $_GET["page"];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page->process_post_data($_POST);
        }
        $page->head_assets();
        $page->render();
    }

}
