<?php

class MVVLIVERAIL_Admin_Page_Orders extends MVVLIVERAIL_Admin_Page {

    public function __construct() {
        parent::__construct();
    }

    public function get_order_package_list($param) {
        global $lrapi;
        $lrapi->callApi('/order/package/list', $param);
        $get_xml_doc = $lrapi->getLastApiXmlDoc();
        return $get_xml_doc->orders->order;
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
            <th>Order Name</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php
        echo "</$type>";
    }

    protected function render_overview_all_order_row($order) {
        ?>
        <tr>
            <td>
                <strong>
        <?php echo $order->order_id; ?>
                </strong>
            </td>
            <td><?php echo $order->name; ?></td>
            <td><?php echo $order->creation_time; ?></td>
            <td><?php echo $order->order_status; ?></td>
        </tr>
        <?php
    }

    public function render() {
        $this->render_page_start('Limelight Orders');

        $param = array('limit' => '3');
        $get_order_package_list = $this->get_order_package_list($param);
        ?>
        <div class="dt-example">
            <div class="container">
                <section>
                    <table id="example" class="display" cellspacing="0" width="100%">
                            <?php $this->render_overview_header('thead'); ?>
                        <tbody>
                            <?php
                            if ($get_order_package_list) {
                                foreach ($get_order_package_list as $key => $order) {
                                    $this->render_overview_all_order_row($order);
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
