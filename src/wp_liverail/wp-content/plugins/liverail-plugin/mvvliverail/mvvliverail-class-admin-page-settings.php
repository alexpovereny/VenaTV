<?php

class MVVLIVERAIL_Admin_Page_Settings extends MVVLIVERAIL_Admin_Page {

    public $mvv_liverail_options;

    public function __construct() {
        parent::__construct();
        $this->mvv_liverail_options = get_option('mvv_liverail_options');
        //update_option('mvv_liverail_options', $jwp_limelight_options);
    }

    public function license_key_validation($value) {
        return ( preg_match('/^\S*$/', $value) ) ? $value : NULL;
    }

    public function render() {

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
        <form method="post" action="<?php echo $this->page_url(); ?>">
            <input type="hidden" name="liverail_hidden" value="Y">

        <?php settings_fields(MVVLIVERAIL . 'menu_liverail_settings'); ?>
            <div class="row">
                <div class="col-xs-6 col-sm-4">LiveRail url:</div>
                <div class="col-xs-6 col-md-4"><p><input type="text" name="mvv_liverail_url" value="<?php echo $this->mvv_liverail_options['mvv_liverail_url'] ?>" size="45"></p></div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-4">LiveRail username:</div>
                <div class="col-xs-6 col-md-4"><p><input type="text" name="mvv_liverail_user" value="<?php echo $this->mvv_liverail_options['mvv_liverail_user']; ?>" size="45"></p></div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-4">LiveRail password:</div>
                <div class="col-xs-6 col-md-4"><p><input type="text" name="mvv_liverail_pass" value="<?php echo $this->mvv_liverail_options['mvv_liverail_pass']; ?>" size="45"></p></div>
            </div>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  />
            </p>
        </form>
        <?php
        $this->render_page_end();
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
