<?php

class MVVLIVERAIL_Admin_Page_User_Management extends MVVLIVERAIL_Admin_Page {

    public function __construct() {
        parent::__construct();
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

    protected function render_overview_header($type = 'thead') {
        $type = ( 'tfoot' == $type ) ? 'tfoot' : 'thead';
        echo "<$type>";
        ?>
        <tr>
            <th>LiveRail ID</th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Role</th>
            <th>LiveRail Entity ID</th>
        </tr>
        <?php
        echo "</$type>";
    }

    protected function render_overview_all_creative_campaign_row($order) {
        ?>
        <tr>
            <td>
                <strong>
                    <?php echo $order->data->liverail_user_id; ?>
                </strong>
            </td>
            <td><?php echo $order->data->display_name; ?></td>
            <td><?php echo $order->data->user_email; ?></td>
            <td><?php echo $order->roles['0']; ?></td>
            <td><?php echo $order->data->liverail_entity_id; ?></td>
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
        $get_users = get_users();
        ?>
        <div class="dt-example">
            <div class="container">
                <section>
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <?php $this->render_overview_header('thead'); ?>
                        <tbody>
                            <?php
                            if ($get_users) {
                                foreach ($get_users as $key => $user) {
                                    $this->render_overview_all_creative_campaign_row($user);
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

}
