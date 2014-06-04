<?php

class MVVLIVERAIL_Admin_Page_Default extends MVVLIVERAIL_Admin_Page {

    private $entitys = array();
    public $errors = array();
    //
    private $overview_or_edit = 'overview';
    public static $entity_options = array();

    public function render() {
        if ('new' == $this->overview_or_edit || (isset($this->entity_options) && $this->errors)) {
            return $this->render_edit_page();
        }
        return $this->render_overview();
    }

    public function __construct() {
        parent::__construct();

        $this->entity_options = array(
            'status' => array(
                'name' => 'status',
                'label' => 'Status',
                'options' => array(
                    'active' => 'Active',
                    'trial' => 'Trial',
                    'inactive' => 'Inactive',
                    'archived' => 'Archived'
                ),
                'default' => 'NULL',
                'discard_if_default' => true,
                'help_text' => 'Help text'
            )
        );

        //Open page for create new entity
        if (isset($_POST['new_entity_id'])) {
            $this->overview_or_edit = 'new';
            return $this->_init_add_params();
        }
        if (isset($_POST['entity_organization'])) {
            //   echo '<br> if entity_organization !!!';
            $entity_params = array(
                'organization' => $_POST['entity_organization_name'],
                'address' => $_POST['entity_address'],
                'status' => $_POST['status'],
                'contact_email' => $_POST['entity_contact_email'],
                'site_url' => $_POST['entity_site_url']
            );
            $this->add_entity($entity_params);
        } else {
            //  echo '<br> else entity_organization !!!';
        }

        //Adding and updating entity data into the database 
        //synchronization with LiveRail API
        //$this->get_entity_and_save_params();
        if (isset($_POST['update_entity_params'])) {
            return $this->get_entity_and_save_params();
        }
        return $this->_init_overview_page();
    }

    protected function add_entity($param) {
        global $wpdb, $lrapi;
        $message = array();

        if (isset($param)) {
            $set_entity = $lrapi->callApi('/entity/add', $param);

            if ($set_entity != FALSE) {
                $message[] = __('Create new entity in LiveRail API.');
                $get_xml_doc = $lrapi->getLastApiXmlDoc();

                $query_add_entity = "insert into wp_entity
                  (entity_id, organization, parent_id, status)
                  values ($get_xml_doc->entity_id, '" . $param['organization'] . "', " . $get_xml_doc->auth->entity->entity_id . ", '" . $param['status'] . "')";

                $res_add_entity = $wpdb->query($query_add_entity);

                if ($res_add_entity == 1) {
                    $message[] = __('Create new entity in local DB.');
                } else {
                    $this->errors = new WP_Error();
                    $error_msg_field = '';
                    $error_msg_ = $res_add_entity;
                    $this->errors->add($error_msg_field, __('<strong>ERROR</strong>: ' . $error_msg_));
                }
            } else {
                //Error creating entity in LiveRail
                $get_xml_doc = $lrapi->getLastApiXmlDoc();
                $error_msg_ = (string) $get_xml_doc->error->message;
                $error_msg_field = (string) $get_xml_doc->error->field;
                $this->errors = new WP_Error();
                $this->errors->add($error_msg_field, __('<strong>ERROR</strong>: ' . $error_msg_));
            }
            $this->header_error_params($this->errors, $message);
        }
    }

    protected function get_entity($params = array()) {
        global $lrapi;
        $get_entity = $lrapi->callApi('/entity/list', $params);
        $get_xml_doc = $lrapi->getLastApiXmlDoc();
        if ($get_entity != FALSE) {
            if ($get_xml_doc->warnings) {
                $warning_msg_ = (string) $get_xml_doc->warnings->warning->message;
                $warning_msg_field = $get_xml_doc->warnings->warning->field;
                $this->errors = new WP_Error();
                $this->errors->add($warning_msg_field, __('<strong>WARNING</strong>: ' . $warning_msg_));
                $this->header_error_params($this->errors);
            }
            return $get_xml_doc;
        } else {
            //$get_xml_doc = $lrapi->getLastApiXmlDoc();
            $error_msg_ = (string) $get_xml_doc->error->message;
            $error_msg_field = (string) $get_xml_doc->error->field;
            $this->errors = new WP_Error();
            $this->errors->add($error_msg_field, __('<strong>ERROR</strong>: ' . $error_msg_));
            $this->header_error_params($this->errors);
            return $get_xml_doc;
        }
    }

    protected function header_error_params($errors = array(), $messages = array()) {
        ?>
        <div class="wrap">
            <?php
            //  echo '<br>error!!! -<br>';
            //  var_dump($errors)
            ?>
            <?php if (isset($errors) && is_wp_error($errors)) : ?>
                <div class="error">
                    <ul>
                        <?php
                        foreach ($errors->get_error_messages() as $err)
                            echo "<li>$err</li>\n";
                        ?>
                    </ul>
                </div>
                <?php
            endif;

            if (!empty($messages)) {
                foreach ($messages as $msg)
                    echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
            }
            ?>
        </div>
        <?php
    }

    public function process_post_data($post_data) {
        parent::process_post_data($post_data, false);
    }

    protected function process_overview_post_data($post_data) {

        if (!count($this->form_error_fields)) {
            if (isset($_POST['new_entity_id']) && $_POST['new_entity_id']) {
                
            }
            // When you update all the data entity 
            if (isset($_POST['update_entity_params']) && $_POST['update_entity_params']) {
                exit();
            }
        }

        wp_redirect($this->page_url());
        exit();
    }

    private function _init_overview_page() {
        // global $lrapi;
        // echo '<br>function _init_overview_page <br>';
        // $get_entity = $lrapi->callApi('/entity/list', $params);
        // echo '<br> --- $get_entity --- <br>';
        // var_dump($get_entity);
    }

    //$wpdb->query("UPDATE $wpdb->comments SET comment_approved = '1' WHERE comment_ID = '$comment'");

    public function get_all_entity_params() {
        global $wpdb;
        $get_results = $wpdb->get_results("SELECT id, entity_id, organization, parent_id, status FROM wp_entity");
        return $get_results;
    }

    private function upd_row($msRows, $myRows, $total, $api_entity_array) {
        $addKeys = array(); //The array keys of rows that need to update their
        $updKeys = array();
        $delKeys = array();
        $tmpKey = 1;
        if ($msRows) {
            foreach ($msRows as $key => $msRow) {
                if (!in_array($msRow->entity_id, $myRows)) {
                    $addKeys[] = $msRow->entity_id;
                } else {
                    $updKeys[] = $msRow->entity_id;
                }

                if ($total == $tmpKey) {
                    foreach ($myRows as $key => $my_value) {
                        if (!in_array($my_value, $api_entity_array)) {
                            $delKeys[] = $my_value;
                        }
                    }
                    $entity_params = array('add_entity' => $addKeys, 'del_entity' => $delKeys, 'upd_entity' => $updKeys);
                    //var_dump($entity_params);
                    return $entity_params;
                }
                $tmpKey++;
            }
        }
    }

    private function get_entity_and_save_params() {
        global $wpdb;
        $get_entity = $this->get_entity();
        $tmp = 1;
        $local_entity_array = array();
        $local_entity_id = $wpdb->get_results("SELECT entity_id FROM wp_entity");

        foreach ($local_entity_id as $key => $value)
            $local_entity_array[] = $value->entity_id;

        $api_entity_array = array();
        foreach ($get_entity->entities->entity as $key => $value)
            $api_entity_array[] = (string) $value->entity_id;

        $get_upd_row = $this->upd_row($get_entity->entities->entity, $local_entity_array, $get_entity->total, $api_entity_array);

        if ($get_entity->entities->entity) {
            foreach ($get_entity->entities->entity as $key => $entity) {
                if ($entity->parent_id <= 0 || $entity->parent_id == '')
                    $entity->parent_id = 0;

                if (in_array($entity->entity_id, $get_upd_row['add_entity'])) {
                    //if such entity is not in the local database, then write
                    $query = "insert into wp_entity
                      (entity_id, organization, parent_id, status)
                      values ($entity->entity_id, '$entity->organization', $entity->parent_id, '$entity->status')";
                    $wpdb->query($query);
                } else if (in_array($entity->entity_id, $get_upd_row['upd_entity'])) {
                    /*  $wpdb->query("UPDATE wp_entity SET organization = '$entity->organization', parent_id=$entity->parent_id, status='$entity->status' "
                      . "WHERE entity_id = '$entity->entity_id'"); */
                }
            }
        }
        if ($get_upd_row['del_entity']) {
            //Delete records from the local database that are not in API
            foreach ($get_upd_row['del_entity'] as $key => $entity_id) {
                $wpdb->query("DELETE FROM wp_entity WHERE entity_id = '$entity_id'");
            }
        }
    }

    private function get_entity_and_save_params2() {
        global $wpdb;
        //return $this->render_overview();
        $get_entity = $this->get_entity();
        $tmp = 1;
        foreach ($get_entity->entities->entity as $key => $entity) {
            $get_results = $wpdb->get_results("SELECT * FROM wp_entity WHERE entity_id = '$entity->entity_id'");

            if (!$get_results['0']->entity_id && $entity->parent_id != '') {
                $query = "insert into wp_entity
                            (entity_id, organization, parent_id, type)
                            values ($entity->entity_id, '$entity->organization', $entity->parent_id, '$entity->type')";
                $wpdb->query($query);
                echo '<br><br> $entity->entity_id = ' . $entity->entity_id;
                echo '<br> $entity->organization = ' . $entity->organization;
                echo '<br> $entity->parent_id = ' . $entity->parent_id;
                echo '<br> $entity->type = ' . $entity->type;
            } else if ($get_results['0']->entity_id != '' && $entity->entity_id != '' && $entity->parent_id != '') {
                $wpdb->query("UPDATE wp_entity SET organization = '$entity->organization', parent_id=$entity->parent_id, type='$entity->type' "
                        . "WHERE entity_id = '$entity->entity_id'");
            }

            if ($get_entity->total == $tmp) {
                return $this->render_overview();
            }
            $tmp++;
        }
        // echo '$get_entity->total -' . $get_entity->total;
        // echo '<br> get_entity_and_save_params!!!';
    }

    private function _init_add_params() {
        // echo '<br>_init_add_params <br>';
    }

    private function _init_edit_page() {
        // echo '<br>_init_edit_page <br>';
    }

    protected function render_form_row($field) {
        $selected = '';
        ?>
        <tr valign="top" id="<?php echo $field['name']; ?>_row">
            <th scope="row" id="<?php echo $field['name']; ?>_label">
                <label for="<?php echo esc_attr($field['name']) ?>"> <?php echo $field['label']; ?></label>
            </th>
            <td id="<?php echo $field['name']; ?>_field">
                <?php
                echo "<select id='id_{$field['name']}' name='{$field['name']}' class=''>\n";
                foreach ($field['options'] as $key => $value) {
                    echo "\t<option $selected value='" . esc_attr($key) . "'>";
                    echo ucfirst($value);
                    echo "</option>\n";
                }
                echo "</select>";
                ?>
            </td>
        </tr>
        <?php
    }

    protected function render_form_select($field) {

        $selected_value = ( is_null($field->post_value) ) ? $field->value : $field->post_value;
        echo "<select id='id_{$field->name}' name='{$field->name}' class='{$field->class}'>\n";
        foreach ($field->options as $value => $description) {
            echo '<br>$value -' . $value;
            echo '<br>$description -' . $description;
            $value = ( $field->description_is_value ) ? $description : $value;
            if ($selected_value) {
                $selected = ( $selected_value == $value ) ? 'selected="selected"' : "";
            } else {
                $selected = ( $field->default == $value ) ? 'selected="selected"' : "";
            }
            echo "\t<option $selected value='" . esc_attr($value) . "'>";
            echo ucfirst($description);
            echo "</option>\n";
        }
        echo "</select>";
    }

    protected function render_edit_page() {
        ?>
        <div class="backlink">
            <a href="<?php echo $this->page_url(); ?>">← Back to the list entity</a>
        </div>
        <?php
        $this->render_all_messages();
        ?>

        <h3>Create Entity</h3>
        <h4>Add entity params:</h4>
        <br>
        <form method="post" id="add_entity_form" name="add_entity_form" action="">
            <div class="form-group">
                <label for="entity_organization_name">Organization <span class="description">(required)</span></label>
                <input type="text" class="form-control" id="entity_organization_name" name="entity_organization_name" placeholder="Organization name" value="">
                <p class="help-block">The name of the new entity.</p>
            </div>
            <div class="form-group">
                <label for="entity_address">Address</label>
                <input type="text" class="form-control" id="entity_address" name="entity_address" placeholder="Address" value="">
            </div>
            <div class="form-group">
                <label for="entity_site_url">Site url</label>
                <input type="text" class="form-control" id="entity_site_url" name="entity_site_url" placeholder="Site url" value="">
                <p class="help-block">The site url. This parameter is optional.</p>
            </div>
            <div class="form-group">
                <label for="entity_contact_email">Contact email</label>
                <input type="email" class="form-control" id="entity_contact_email" name="entity_contact_email" placeholder="Contact email" value="">                    
                <p class="help-block">A contact email address or addresses. Multiple addresses can be used, separated by comma. .</p>
            </div>
            <div class="form-group">
                <table class="form-table">
                    <?php
                    foreach ($this->entity_options as $field) {
                        if ($field) {
                            $this->render_form_row($field);
                        }
                    }
                    ?>
                </table>
            </div>
            <p class="submit">
                <input type="hidden" name="entity_organization" id="entity_organization" value="true"/>
                <input type="submit" name="submit" id="submit" class="button-primary" value="Create entity"/>
            </p>
        </form>

        <?php if (isset($this->logo_settings_fields)): ?>
            <h3>Logo/Watermark Settings</h3>
            <table class="form-table">
                <?php
                foreach ($this->logo_settings_fields as $field) {
                    $this->render_form_row($field);
                }
                ?>
            </table>
        <?php endif; ?>
        </form>
        <?php
        $this->render_page_end();
    }

    protected function render_overview() {
        $this->render_page_start('Default Page LiveRail API');
        $this->render_all_messages();
        $get_entity_params = $this->get_all_entity_params();
        ?>
        <h3>LiveRail Entity Params </h3>

        <form method="post" id="update_entity_params_form" name="update_entity_params_form" action="<?php //echo $this->page_url(array( 'update_entity_params' => 'true'))                                                                                                                                   ?>">
            <p class="submit">
                <input type="hidden" name="update_entity_params" id="update_entity_params" value="true" />
                <input type="submit" name="update_entity_submit_form" id="update_entity_submit_form" class="button-primary" value="Update Entity Params"/>
            </p>
        </form>
        <div class="dt-example">
            <div class="container">
                <section>
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <?php $this->render_overview_header('thead'); ?>
                        <tbody>
                            <?php
                            if ($get_entity_params) {
                                foreach ($get_entity_params as $key => $entity) {
                                    $this->render_overview_all_entity_row($entity);
                                }
                            }
                            ?>
                        </tbody>
                        <?php $this->render_overview_header('tfoot'); ?>
                    </table>
                </section>
            </div>
        </div>


        <form method="post" id="add_entity_form" name="add_entity_form" action="<?php //echo $this->page_url(array('noheader' => 'true', 'new_entity_id' => 'true'))  ?>">
            <p class="submit">
                <input type="hidden" name="noheader" value="true" />
                <input type="hidden" name="new_entity_id" id="new_entity_id" value="true"/>
                <input type="submit" name="submit_form" id="submit_form" class="button-primary" value="Create a new entity"  />
            </p>
        </form>
        <script type="text/javascript">
            jQuery(function() {

            });
        </script>
        <?php
        $this->render_page_end();
    }

    protected function render_overview_header($type = 'thead') {
        $type = ( 'tfoot' == $type ) ? 'tfoot' : 'thead';
        echo "<$type>";
        ?>
        <tr>
            <th>ID</th>
            <th>Organization</th>
            <th>Parent ID</th>
            <th>Status</th>
           <!-- <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>-->
        </tr>
        <?php
        echo "</$type>";
    }

    protected function render_overview_all_entity_row($entity) {
        ?>
        <tr>
            <td>
                <strong>
                    <?php echo $entity->entity_id; ?>
                </strong>
            </td>
            <td><?php echo $entity->organization; ?></td>
            <td><?php echo $entity->parent_id; ?></td>
            <td><?php echo $entity->status; ?></td>
           <!-- <td><a href="<?php //echo $entity->admin_url($this, 'edit');    ?>                                                                                                                                                                       
            <td><a href="<?php //echo $entity->admin_url($this, 'copy');   ?>                                                                                                                                                                      
            <td>
            <?php if ($entity->entity_id): ?>
            <?php endif; ?>
            </td>-->
        </tr>
        <?php
    }

    protected function render_overview_entity_row($entity) {
        ?>
        <tr valign="middle">
            <td align="center">
                <strong>
                    <?php echo $entity->entity_id; ?>
                </strong>
            </td>
            <td><?php echo $entity->organization; ?></td>
            <td><?php echo $entity->parent_id; ?></td>
            <td><?php echo $entity->type; ?></td>
            <td><a href="<?php //echo $entity->admin_url($this, 'edit');  ?>" class="button mvvliverail_edit">Edit</a></td>
            <td><a href="<?php //echo $entity->admin_url($this, 'copy');   ?>" class="button mvvliverail_copy">Copy</a></td>
            <td>
                <?php if ($entity->entity_id): ?>
                    <a href="<?php //echo $entity->admin_url($this, 'delete');   ?>" class="button mvvliverail_delete">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

}
