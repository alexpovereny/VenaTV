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
        /* $this->mvv_liverail_options = get_option('mvv_liverail_options');
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
         */
    }

    public function license_key_validation($value) {
        return ( preg_match('/^\S*$/', $value) ) ? $value : NULL;
    }

    protected function render_overview_header($type = 'thead') {
        $type = ( 'tfoot' == $type ) ? 'tfoot' : 'thead';
        echo "<$type>";
        ?>
        <tr>
            <th>ID</th>
            <th>Campaign ID</th>
            <th>Name</th>
            <th>Status</th>
        </tr>
        <?php
        echo "</$type>";
    }

     protected function render_overview_all_creative_campaign_row($order) {
        ?>
        <tr>
            <td>
                <strong>
        <?php echo $order->creative_id; ?>
                </strong>
            </td>
            <td><?php echo $order->creative_campaign_id; ?></td>
            <td><?php echo $order->title; ?></td>
            <td><?php echo $order->status; ?></td>
        </tr>
        <?php
    }
      public function get_creative_campaign_list($param) {
        global $lrapi;
        $lrapi->callApi('/creative/list', $param);
        $get_xml_doc = $lrapi->getLastApiXmlDoc();
        return $get_xml_doc->creatives->list->creative;
    }


    public function render() {
        //Campaigns
        $this->render_page_start('LiveRail Campaigns');

        $param = array('limit' => '3');
        $get_creative_campaign_list = $this->get_creative_campaign_list($param);
        ?>
        <div class="dt-example">
            <div class="container">
                <section>
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <?php $this->render_overview_header('thead'); ?>
                        <tbody>
                            <?php
                            if ($get_creative_campaign_list) {
                                foreach ($get_creative_campaign_list as $key => $campaign) {
                                    $this->render_overview_all_creative_campaign_row($campaign);
                                }
                            }
                            ?>
                        </tbody>
                        <?php $this->render_overview_header('tfoot'); ?>
                    </table>
                </section>
            </div>
        </div>
        <?php
        $this->render_page_end();
    }

    public function set_get_params($liverail_api, $method, $array_params) {
        $get_set_params = $liverail_api->callApi($method, $array_params);
        //echo '<br>' . $method . ' --- ';
        //var_dump($get_set_params);
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
