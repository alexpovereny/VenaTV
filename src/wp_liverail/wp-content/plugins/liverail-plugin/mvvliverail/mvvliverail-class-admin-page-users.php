<?php

class MVVLIVERAIL_Admin_Page_Users extends MVVLIVERAIL_Admin_Page {

    //protected $liverail_api;
    //protected $getToken;

    public function __construct() {
        parent::__construct();
        $mvv_liverail_options = get_option('mvv_liverail_options');
        //$this->liverail_api = new LiveRailApi($mvv_liverail_options['mvv_liverail_url']);
        //$this->liverail_api->login($mvv_liverail_options['mvv_liverail_user'], $mvv_liverail_options['mvv_liverail_pass']);
        //$this->getToken = $this->liverail_api->getToken();

        $this->get_all_users();
    }

    public function get_all_users() {
        global $lrapi;
        $method = "/user/list";
        $lrapi->callApi($method, $array_params);
        $get_json_doc = $lrapi->getLastApiJsonDoc();

        foreach ($get_json_doc->users->user as $key => $user) {
            echo '<br>$key - ' . $key;
            //var_dump($entity);
            echo '<br>user name -' . $user->name; //.' parent_id ='.$entity->parent_id;
            //parent_id
            //organization
            //creation_time
        }
    }

    public function render() {
        if ($_POST['liverail_hidden'] == 'Y') {
            //Settings...
        }
        ?>
        <h3>LiveRail All Users</h3>
   
        <?php
        $this->render_page_end();
    }

}
