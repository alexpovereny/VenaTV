<?php

//Creative Campaigns
///creative/campaign/add 
///creative/campaign/edit 
///creative/campaign/list 
///creative/campaign/delete 
///creative/campaign/resolve/namespace

class MVVLIVERAIL_Admin_Page_Campaigns extends MVVLIVERAIL_Admin_Page {

    public $liverail_api;
    public $mvv_entity_id;

    public function __construct() {
        parent::__construct();
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

    public function set_get_params($method, $array_params) {
        global $lrapi;
        $get_set_params = $lrapi->callApi($method, $array_params);
        if ($get_set_params == TRUE) {
            $get_json_doc = $lrapi->getLastApiJSONDoc();
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
        <div id="wpbody-content" class="mvvliverail">
            <div class="wrap">
                <div id="icon-mvvliverail-main" class="icon32"></div>
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
