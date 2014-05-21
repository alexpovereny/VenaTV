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
        $method = "/entity/list";
        //$array_params = array("token" => $this->getToken);
        $get_set_params = $lrapi->callApi($method, $array_params);
        //$get_set_params = $this->liverail_api->callApi($method, $array_params);
        echo '<br>' . $method . ' --- ';
        var_dump($get_set_params);

        $get_json_doc = $lrapi->getLastApiJsonDoc();
        //$get_json_doc = $this->liverail_api->getLastApiJsonDoc();
        // echo '<br> $get_json_doc --- ';
        // var_dump(json_encode($get_json_doc));
        echo 'total -' . $get_json_doc->total;
        //var_dump($get_json_doc->entities->entity);

        foreach ($get_json_doc->entities->entity as $key => $entity) {
            echo '<br>$key - ' . $key;
            //var_dump($entity);
            echo '<br>entity_id -' . $entity->entity_id; //.' parent_id ='.$entity->parent_id;
            //parent_id
            //organization
            //creation_time
        }
    }

    public function render() {
        //echo '<br> ---2  $getToken --- ' . $this->getToken . '<br>';
        /* <entities>
         * <entity>
         * <entity_id>14415</entity_id>
         * <parent_id>8988</parent_id>
         * <perspective>P</perspective>
         * <previous_perspective/>
         * <creation_time>2013-04-09 08:48:13</creation_time>
         * <organization>123greetings INDO Female</organization>
         * <address/><description/><status>archived</status>
         * <hosted>1</hosted><type>0000</type><contact_email/>
         * <built_in>0</built_in>
         * <site_url/>
         * <admap>in::0</admap>
         * <campaign_acceptance>automatically</campaign_acceptance>
         * <parent_campaign_acceptance>automatically</parent_campaign_acceptance>
         * <marketplaces_availability>unavailable</marketplaces_availability>
         * <domain_policy>deny</domain_policy><inte_truste_id/>
         * <inte_evidon_client_id/><inte_evidon_campaign_id/>
         * <inte_proximic_enabled>no</inte_proximic_enabled>
         * <inte_affine_line_item_id/><inte_safecount_campaign_id/>
         * <inte_aperture_enabled>no</inte_aperture_enabled>
         * <inte_doubleverify_campaign_id/><inte_doubleverify_client_id/>
         * <inte_doubleverify_behavior>R</inte_doubleverify_behavior>
         * <inte_adxpose_id/><inte_vizu_campaign_id/>
         * <inte_vizu_agency_enabled>no</inte_vizu_agency_enabled>
         * <inte_safecount_in_enabled>no</inte_safecount_in_enabled>
         * <inspector_enabled>no</inspector_enabled>
         * <inte_adsafe_enabled>no</inte_adsafe_enabled>
         * <inte_adsafe_allocation_enabled>no</inte_adsafe_allocation_enabled>
         * <realtime_dashboard_enabled>no</realtime_dashboard_enabled>
         * <adsource_available>yes</adsource_available>
         * <rtb_enabled>yes</rtb_enabled>
         * <rtb_certified>no</rtb_certified>
         * <is_deleted>0</is_deleted>
         * <deleted_time/><inte_nielsen_client_id/><inte_nielsen_password/><inte_nielsen_api_key/>
         * <campaign_availability>available</campaign_availability>
         * <nielsen_diagnostic_enabled>no</nielsen_diagnostic_enabled>
         * <inte_peer39_enabled>no</inte_peer39_enabled>
         * <deal_available>no</deal_available>
         * <blocklist_availability>no</blocklist_availability>
         * <last_status_update>2014-02-10 02:19:46</last_status_update>
         * <api_tools_enabled>no</api_tools_enabled>
         * <inte_nielsen_via_lr>no</inte_nielsen_via_lr><network_children>unavailable</network_children><blocklist_enforced>no</blocklist_enforced><buyer_dashboard_enabled>no</buyer_dashboard_enabled><last_updated_timestamp>2014-04-18 08:05:59</last_updated_timestamp><checkpoint_customer_curation>no</checkpoint_customer_curation><buyer_id_enabled>no</buyer_id_enabled><ldb_enabled>no</ldb_enabled><skip_available>no</skip_available><last_updated_unix_timestamp>1397808359</last_updated_unix_timestamp><inherited>1</inherited><parent_campaign_availability>available</parent_campaign_availability></entity> */
        $mvv_liverail_options = get_option('mvv_liverail_options');
        $this->render_page_start('LiveRail Options');
        //var_dump($_POST);
        if ($_POST['liverail_hidden'] == 'Y') {
            // API Settings
            $this->mvv_liverail_options['mvv_liverail_url'] = $_POST['mvv_liverail_url'];
            update_option('mvv_liverail_options', $mvv_liverail_options);
            $this->mvv_liverail_options['mvv_liverail_user'] = $_POST['mvv_liverail_user'];
            update_option('mvv_liverail_options', $mvv_liverail_options);
            $this->mvv_liverail_options['mvv_liverail_pass'] = $_POST['mvv_liverail_pass'];
            update_option('mvv_liverail_options', $this->mvv_liverail_options);
        }
        //$this->render_all_messages();
        ?>
        <h3>LiveRail All Users</h3>
        <!-- <form method="post" action="<?php echo $this->page_url(); ?>">
            <input type="hidden" name="liverail_hidden" value="Y">
        <?php settings_fields(MVVLIVERAIL . 'menu_liverail_settings'); ?>

        <?php
        ?>
            <div class="row">
                <div class="col-xs-6 col-sm-4">LiveRail url:</div>
                <div class="col-xs-6 col-md-4"><p><input type="text" name="mvv_liverail_url" value="<?php echo $this->mvv_liverail_options['mvv_liverail_url'] ?>" size="45"></p></div>
            </div>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  />
            </p>
        </form>-->
        <?php
        $this->render_page_end();
    }

}
