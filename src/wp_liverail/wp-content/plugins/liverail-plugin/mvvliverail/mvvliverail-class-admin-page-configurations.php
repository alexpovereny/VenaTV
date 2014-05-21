<?php

class MVVLIVERAIL_Admin_Page_Configurations extends MVVLIVERAIL_Admin_Page {

    public $liverail_api;

    public function __construct() {

        parent::__construct();

        if ($liverail_api == NULL) {
            
        }

        $options_tmp = get_option(MVVLIVERAIL . 'options');
        echo '<br> --- options!!! --- <br>';
        var_dump($options_tmp);

        $token_tmp = get_option(MVVLIVERAIL . 'token');
        echo '<br> --- token!!! --- <br>';
        var_dump($token_tmp);

        if ($token_tmp) {
            $liverail_url = $options_tm->MVVLIVERAIL . "url"; //$liverail_url = $options_tmp[MVVLIVERAIL . "url"];
            $liverail_url_test = $options_tmp->MVVLIVERAIL . "test_url"; // $liverail_url_test = $options_tmp[MVVLIVERAIL . "test_url"];

            update_option(MVVLIVERAIL . 'token', $getToken);
        } else {
            echo '<br> else!!!<br>';
            $getToken_ = LiveRailApi::getToken();
            var_dump($getToken_);
        }

        // extract( $this->_args );
        // wp_nonce_field( "fetch-list-" . get_class( $this ), '_ajax_fetch_list_nonce' );
        // $this->display_tablenav( 'top' );
        // WP_Comments_List_Table
        ?>
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
            echo '</div>';


            /*  $jwp_limelight_options = array();
              $jwp_limelight_options['mvv_liverail_url'] = "http://api4.int.liverail.com";
              $jwp_limelight_options['mvv_liverail_test_url'] = "http://test.api.liverail.com";
              $jwp_limelight_options['mvv_liverail_user'] = "alex.povereny@gmail.com";
              $jwp_limelight_options['mvv_liverail_pass'] = "alexvena2014";
              $jwp_limelight_options['mvv_liverail_org_id'] = "";

              //add_option('mvv_liverail_options', $jwp_limelight_options, '', 'no');
              update_option('mvv_liverail_options', $jwp_limelight_options); */
        }

        public function set_entity($liverail_api, $entityId) {
            //$setEntity = $liverail_api->setEntity($entityId);
            $get_user_res = $liverail_api->callApi("/set/entity", $entityId);
            //echo '<br> --- $setEntity --- <br>';
            //var_dump($setEntity);
            //  $get_user_res = $liverail_api->callApi("/entity/list", array("token" => $get_token));
            if ($setEntity == TRUE) {
                $setEntity_json_doc = $liverail_api->getLastApiJSONDoc();
                return $setEntity_json_doc;
                // return json_encode($get_user_json_doc);
            }
            return $setEntity;
        }

        public function set_get_params($liverail_api, $method, $array_params) {
            $get_set_params = $liverail_api->callApi($method, $array_params);
            echo '<br>' . $method . ' --- ';
            var_dump($get_set_params);
            if ($get_set_params == TRUE) {
                $get_json_doc = $liverail_api->getLastApiJSONDoc();
                return $get_json_doc;
            }
            return $get_set_params;
        }

        /*
          public function get_user_list($liverail_api, $get_token, $entity_id) {
          $get_user_res = $liverail_api->callApi("/user/list", array("token" => $get_token, "entity_id" => $entity_id));
          if ($get_user_res == TRUE) {
          $get_user_json_doc = $liverail_api->getLastApiJSONDoc();
          return $get_user_json_doc;
          // return json_encode($get_user_json_doc);
          }
          return $get_user_res;
          }
         */

        // token string Authentication session token (received after /login).
        // name string User's full name.
        // email string User's email address, used as a login name too.
        // password string User's clear text password; at least 5 characters.
        public function add_user($liverail_api, $array_params) {
            $get_user_res = $liverail_api->callApi("/user/add", $array_params);
            echo '<br>_ _ _ _ _ _ _ _ _ _<br>';
            echo 'add_user --- ';
            var_dump($get_user_res);

            if ($get_user_res == TRUE) {
                $get_user_json_doc = $liverail_api->getLastApiJSONDoc();
                return $get_user_json_doc;
                // return json_encode($get_user_json_doc);
            }
            return $get_user_res;
        }

        public function license_key_validation($value) {
            return ( preg_match('/^\S*$/', $value) ) ? $value : NULL;
        }

        public function render() {

            $jwp_limelight_options = get_option('jwp_limelight_options');
            $this->render_page_start('Limelight VPS Options');
            //var_dump($_POST);
            if ($_POST['limelight_hidden'] == 'Y') {
                // API Settings
                $jwp_limelight_options['jwp_limelight_org_id'] = $_POST['jwp_limelight_org_id'];
                update_option('jwp_limelight_options', $jwp_limelight_options);
            }
            //$this->render_all_messages();
            ?>
            <form method="post" action="<?php echo $this->page_url(); ?>">
                <input type="hidden" name="liverail_hidden" value="Y">
                <?php settings_fields(MVVLIVERAIL . 'menu_liverail_configurations'); ?>

                <?php echo '<h4>' . __('API Settings', 'jwp_limelight_text_domain') . '</h4>'; ?>
                <p><?php _e("Organization ID: "); ?><input type="text" name="jwp_limelight_org_id" value="<?php echo $jwp_limelight_options['jwp_limelight_org_id']; ?>" size="45"><?php echo __(" ex: 1fcedd0a66334ac28fbb2a4117707145", 'jwp_limelight_text_domain'); ?></p>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  />
                </p>
            </form>

            <script type="text/javascript">
                jQuery(function() {
                    var $ = jQuery;
                    function check_key(e) {
                        var version = $('#license_version').val();
                        key = $('#license_key').val();
                        alert('We have version ' + version + ' with key ' + key);
                    }
                    $('#license_version').bind('change', check_key);
                });
            </script>
            <?php
            $this->render_page_end();
        }

    }
    