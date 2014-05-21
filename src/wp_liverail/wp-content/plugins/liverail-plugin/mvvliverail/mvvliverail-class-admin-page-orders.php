<?php

class MVVLIVERAIL_Admin_Page_Orders extends MVVLIVERAIL_Admin_Page {

    public function __construct() {

        parent::__construct();
        global $lrapi;

        /* $param = array(
          'order_number_id' => 'string',
          'entity_id' => 'integer',
          'search_string' => 'string'
          );
          $order_number_list = $lrapi->callApi('/order/number/list', $param);
         */

        $order_number_list = $lrapi->callApi('/order/package/list', $param);
        $get_xml_doc = $lrapi->getLastApiXmlDoc();
        //echo '<br><br> $get_xml_doc ---';
        //var_dump($get_xml_doc);
        echo '<br> count -' . $get_xml_doc->count;
        foreach ($get_xml_doc->orders->order as $key => $value) {
            //  echo '<br><br> $key ---' . $key;
            //  echo '<br> $value ---<br>';
            //  var_dump($value);
            //  $value->order_id;
            echo '<br><br>name ='.$value->name;
            //  $value->network_id;
            //  $value->order_status;
            foreach ($value->publishers->publisher as $publisher_key =>$publisher) {
                echo '<br> $publisher ---'.$publisher;
            }
        }
    }

    public function license_key_validation($value) {
        return ( preg_match('/^\S*$/', $value) ) ? $value : NULL;
    }

    public function render() {

        $jwp_limelight_options = get_option('jwp_limelight_options');
        $this->render_page_start('Limelight Orders');
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
