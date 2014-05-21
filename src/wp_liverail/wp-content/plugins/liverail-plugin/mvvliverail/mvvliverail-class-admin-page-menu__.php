<?php

class MVVLIVERAIL_Admin_Page_Menu extends MVVLIVERAIL_Admin_Page {

    public function __construct() {

        parent::__construct();
        $license_version_field = new MVVLIVERAIL_Form_Field_Select(
                'license_version', array(
            // 'options' => MVVLIVERAIL_Plugin::$license_versions,
            'default' => 'free',
            'description_is_value' => true,
            'help_text' => 'Select which edition of JW Player you own to unlock additional template settings and to hide the player watermark.',
                )
        );
        $license_key_field = new MVVLIVERAIL_Form_Field(
                'license_key', array(
            'validation' => array($this, "license_key_validation"),
            'help_text' => 'A license key is required for the Pro, Premium and Ads edition.',
                )
        );

        if (MVVLIVERAIL_USE_CUSTOM_SHORTCODE_FILTER) {
            $default_config_options = array(
                'label' => 'Category pages',
                'options' => array(
                    'excerpt' => 'Use excerpt',
                    'content' => 'Use content',
                    'disable' => 'Strip shortcode',
                ),
                'default' => 'content',
                'single_line' => true,
            );

            $category_config_options = $default_config_options;
            $category_config_options['label'] = 'Category pages';
            $category_config_field = new MVVLIVERAIL_Form_Field_Radio(
                    'category_config', $category_config_options
            );

            $search_config_options = $default_config_options;
            $search_config_options['label'] = 'Search pages';
            $search_config_field = new MVVLIVERAIL_Form_Field_Radio(
                    'search_config', $search_config_options
            );

            $tag_config_options = $default_config_options;
            $tag_config_options['label'] = 'Tag pages';
            $tag_config_field = new MVVLIVERAIL_Form_Field_Radio(
                    'tag_config', $tag_config_options
            );

            $home_config_options = $default_config_options;
            $home_config_options['label'] = 'Home page';
            $home_config_field = new MVVLIVERAIL_Form_Field_Radio(
                    'home_config', $home_config_options
            );
        }

        $tracking_field = new MVVLIVERAIL_Form_Field_Toggle(
                'allow_anonymous_tracking', array(
            'label' => 'Anonymous tracking',
            'text' => 'Allow anonymous tracking of plugin feature usage',
            'help_text' => 'We track which overall features (player edition, external urls, playlists, etc.) you use. This will help us improve the plugin in the future.',
            'default' => true,
                )
        );

        $purge_field = new MVVLIVERAIL_Form_Field_Toggle(
                'purge_settings_at_deactivation', array(
            'label' => 'Purge settings',
            'text' => 'Purge all plugin settings when I deactivate the plugin.',
            'default' => false,
            'help_text' => 'Note. This process is irreversible. If you ever decide to reactivate the plugin all your settings will be gone. Use with care!',
                )
        );

        $this->license_fields = array(
            $license_version_field,
            $license_key_field,
        );

        $this->other_fields = array(
            $tracking_field,
            $purge_field,
        );

        if (MVVLIVERAIL_USE_CUSTOM_SHORTCODE_FILTER) {
            $this->shortcode_fields = array(
                $category_config_field,
                $search_config_field,
                $tag_config_field,
                $home_config_field,
            );
        } else {
            $this->shortcode_fields = array();
        }

        $this->form_fields = array_merge(
                $this->license_fields, $this->shortcode_fields, $this->other_fields
        );
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
            <input type="hidden" name="limelight_hidden" value="Y">
            <?php settings_fields(JWPLIMELIGHT . 'menu_limelight_options'); ?>

            <h3>Embed Code Overview</h3>
            <p>
                To insert a video or channel into a post or page, using the following shortcode:<code>[limelight FLASHVARS]</code>
            </p>
            <p>
                For example:
                <code>[limelight mediaId="1fcedd0a66334ac28fbb2a4117707145"]</code>
            </p>

            <?php echo '<h4>' . __('API Settings', 'jwp_limelight_text_domain') . '</h4>'; ?>
            <p><?php _e("Organization ID: "); ?><input type="text" name="jwp_limelight_org_id" value="<?php echo $jwp_limelight_options['jwp_limelight_org_id']; ?>" size="45"><?php echo __(" ex: 1fcedd0a66334ac28fbb2a4117707145", 'jwp_limelight_text_domain'); ?></p>

                                   <!-- <p class="submit">
                                        <input type="submit" name="Submit" value="<?php _e('Update Options', 'jwp_limelight_text_domain') ?>" />
                                    </p>-->

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  />
            </p>
        </form>

        <script type="text/javascript">
            jQuery(function() {
                var $ = jQuery;
                function check_key(e) {
                    var
                            version = $('#license_version').val();
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
