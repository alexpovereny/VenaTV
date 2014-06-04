<?php

class MVVLIVERAIL_Admin_Page_Configurations extends MVVLIVERAIL_Admin_Page {

    public $liverail_api;

    public function __construct() {
        global $lrapi;
        parent::__construct();
    }

    public function set_entity($entityId) {
        global $lrapi;
        $setEntity = $lrapi->callApi("/set/entity", $entityId);
        if ($setEntity == TRUE) {
            $setEntity_json_doc = $lrapi->getLastApiJSONDoc();
            return $setEntity_json_doc;
            // return json_encode($get_user_json_doc);
        }
        return $setEntity;
    }

    public function set_get_params($method, $array_params) {
        global $lrapi;
        $get_set_params = $lrapi->callApi($method, $array_params);
        if ($get_set_params == TRUE) {
            $get_json_doc = $lrapi->getLastApiJSONDoc();
            return $get_json_doc;
        }
        return $get_set_params;
    }

    // token string Authentication session token (received after /login).
    // name string User's full name.
    // email string User's email address, used as a login name too.
    // password string User's clear text password; at least 5 characters.
    public function add_user($array_params) {
        global $lrapi;
        $get_user_res = $lrapi->callApi("/user/add", $array_params);

        if ($get_user_res == TRUE) {
            $get_user_json_doc = $lrapi->getLastApiJSONDoc();
            return $get_user_json_doc;
            // return json_encode($get_user_json_doc);
        }
        return $get_user_res;
    }

    public function license_key_validation($value) {
        return ( preg_match('/^\S*$/', $value) ) ? $value : NULL;
    }

    public function render() {
        $this->render_page_start('Options');
        if ($_POST['mvvliverail_hidden'] == 'Y') {
            //Edit Settings...
        }
        //$this->render_all_messages();
        ?>
        <form method="post" action="<?php echo $this->page_url(); ?>">
            <div class="alignleft actions">
                <?php if (current_user_can('promote_users')) : ?>
                    <label class="screen-reader-text" for="new_role"><?php _e('Change role to&hellip;') ?></label>
                    <select name="new_role" id="new_role">
                        <option value=''> <?php _e('Change role to&hellip;') ?></option>
                        <?php wp_dropdown_roles(); ?>
                    </select>
                    <?php
                    submit_button(__('Change'), 'button', 'changeit', false);
                endif;

                do_action('restrict_manage_users');
              
                ?>
            </div>
        </form>
        <!--
        <form method="post" action="<?php //echo $this->page_url();   ?>">
        <input type="hidden" name="mvvliverail_hidden" value="Y">
        <?php //settings_fields(MVVLIVERAIL . 'menu_liverail_configurations');  ?>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  />
        </p>
        </form>
        -->
        <?php
        $this->render_page_end();
    }

}
