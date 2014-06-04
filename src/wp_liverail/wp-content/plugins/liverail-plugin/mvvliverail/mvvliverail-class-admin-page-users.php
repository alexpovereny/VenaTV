<?php

class MVVLIVERAIL_Admin_Page_Users extends MVVLIVERAIL_Admin_Page {

    public function __construct() {
        parent::__construct();

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
