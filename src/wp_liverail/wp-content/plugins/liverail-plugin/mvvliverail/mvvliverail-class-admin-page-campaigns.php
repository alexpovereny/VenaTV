<?php

// Виды табличек
//http://mottie.github.io/tablesorter/docs/example-widget-bootstrap-theme.html
//http://jsfiddle.net/JPVUk/4/
//https://editor.datatables.net/   редактирование
//https://editor.datatables.net/examples/simple/simple
//Creative Campaigns
///creative/campaign/add 
///creative/campaign/edit 
///creative/campaign/list 
///creative/campaign/delete 
///creative/campaign/resolve/namespace

class MVVLIVERAIL_Admin_Page_Campaigns extends MVVLIVERAIL_Admin_Page {

    public $liverail_api;
    public $mvv_liverail_options;
    public $mvv_entity_id;

    public function __construct() {
        parent::__construct();
        $this->mvv_liverail_options = get_option('mvv_liverail_options');
        var_dump($this->mvv_liverail_options);
        echo '<br>url - ' . $this->mvv_liverail_options['mvv_liverail_url'];

        $this->liverail_api = new LiveRailApi($this->mvv_liverail_options['mvv_liverail_url']);
        $res_login = $this->liverail_api->login($this->mvv_liverail_options['mvv_liverail_user'], $this->mvv_liverail_options['mvv_liverail_pass']);
        $getToken = $this->liverail_api->getToken();
        echo '<br> ---! $getToken --- ' . $getToken . '<br>';

        // Get User List
        $get_creative_campaign_list = $this->set_get_params($this->liverail_api, "creative/campaign/list", array("token" => $getToken)); //, "entity_id" => $entity_id));
        echo '<br> --- $get_creative_campaign_list --- <br>';
        var_dump(json_encode($get_creative_campaign_list));
        
        //update_option('mvv_liverail_options', $jwp_limelight_options);
        /*
          $liverail_url = 'http://api4.int.liverail.com';
          $liverail_user = 'alex.povereny@gmail.com';
          $liverail_pass = 'alexvena2014';

          $liverail_api = new LiveRailApi($liverail_url);
          echo '<br> --- $liverail_api --- <br>';
          var_dump($liverail_api);
          $res_login = $liverail_api->login($liverail_user, $liverail_pass);
          echo '<br> --- $res_login --- ' . $res_login . '<br>';

          $getToken = $liverail_api->getToken();
          echo '<br> ---! $getToken --- ' . $getToken . '<br>'; */
    }

    public function license_key_validation($value) {
        return ( preg_match('/^\S*$/', $value) ) ? $value : NULL;
    }

    public function render() {
        //Campaigns
        $this->render_page_start('LiveRail Campaigns');
        /*   $mvv_liverail_options = get_option('mvv_liverail_options');
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
          } */
        //$this->render_all_messages();
        ?>
        <form method="post" action="<?php echo $this->page_url(); ?>">
            <input type="hidden" name="liverail_hidden" value="Y">

            <?php settings_fields(MVVLIVERAIL . 'menu_liverail_campaigns'); ?>
            <div class="row">
                <div class="col-xs-6 col-sm-4">LiveRail url:</div>
                <div class="col-xs-6 col-md-4"><p><input type="text" name="mvv_liverail_url" value="<?php echo $this->mvv_liverail_options['mvv_liverail_url'] ?>" size="45"></p></div>
            </div>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  />
            </p>
        </form>
        <?php
        $this->render_page_end();
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

    public function page_url($additional_params = NULL) {
        $params = array();
        if ($this->page_slug) {
            $params['page'] = $this->page_slug;
        }
        if (is_array($additional_params)) {
            $params = array_merge($params, $additional_params);
        }
        return admin_url($this->base_url . '?' . http_build_query($params));
    }

    protected function render_page_start($title) {
        ?>
        <div id="wpbody-content" class="jwplimelight">
            <div class="wrap">
                <div id="icon-jwplimelight-main" class="icon32"></div>
                <h2><?php echo $title; ?></h2>
                <?php
            }

            protected function render_page_end() {
                ?>
            </div>
            <div class="clear"></div>
        </div><!-- wpbody-content -->
        <div class="clear"></div>
        <?php
    }

}
