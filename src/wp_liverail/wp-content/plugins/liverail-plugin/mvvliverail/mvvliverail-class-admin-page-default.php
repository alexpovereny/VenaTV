<?php

class MVVLIVERAIL_Admin_Page_Default extends MVVLIVERAIL_Admin_Page {

    private $entitys = array();
    public $errors = array();
    //
    private $overview_or_edit = 'overview';
    public static $entity_options = array();

    //private $players = array();
    //private $player;

    public function render() {

        // echo '<br>--- $this ---<br>';
        // var_dump($this);
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
        //echo '<br> --- $_GET --- <br>';
        //var_dump($_GET);
        //echo '<br> --- $_POST --- <br>';
        //var_dump($_POST);
        //exit();
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

        //Добавление и обновление entity данных в БД
        //синхронизация данных с LiveRail API
        //$this->get_entity_and_save_params();
        if (isset($_POST['update_entity_params'])) {
            //   echo '<br>$_GET[update_entity_params] !!! <br>';
            return $this->get_entity_and_save_params();
        }
        return $this->_init_overview_page();
    }

    protected function add_entity($param) {
        global $wpdb, $lrapi;
        $message = array();

        // echo '<br>!!! if  add_entity';
        //  var_dump($param);
        //echo '<br><br>$param - ' . $param["site_url"];
        //echo '<br>!!! add_entity';
        if (isset($param)) {
            $set_entity = $lrapi->callApi('/entity/add', $param);
            //echo '<br>!!! $set_entity  ---<br>';
            //var_dump($set_entity);
            if ($set_entity != FALSE) {
                $message[] = __('Create new entity in LiveRail API.');
                $get_xml_doc = $lrapi->getLastApiXmlDoc();
                //echo '<br><br> $get_xml_doc ---';
                //var_dump($get_xml_doc);
                //echo '<br>2 $get_xml_doc ---';
                //var_dump(json_encode($get_xml_doc));
                $query_add_entity = "insert into wp_entity
                  (entity_id, organization, parent_id, status)
                  values ($get_xml_doc->entity_id, '" . $param['organization'] . "', " . $get_xml_doc->auth->entity->entity_id . ", '" . $param['status'] . "')";
                // echo '<br>$query -' . $query_add_entity;
                $res_add_entity = $wpdb->query($query_add_entity);
                //  echo '<br>$res -' . $res_add_entity;
                if ($res_add_entity == 1) {
                    $message[] = __('Create new entity in local DB.');
                } else {
                    $this->errors = new WP_Error();
                    $error_msg_field = '';
                    $error_msg_ = $res_add_entity;
                    $this->errors->add($error_msg_field, __('<strong>ERROR</strong>: ' . $error_msg_));
                }
                //  $this->header_error_params('', $message);
            } else {
                //Ошибка при создании entity в LiveRail 
                $get_xml_doc = $lrapi->getLastApiXmlDoc();
                $error_msg_ = (string) $get_xml_doc->error->message;
                $error_msg_field = (string) $get_xml_doc->error->field;
                $this->errors = new WP_Error();
                $this->errors->add($error_msg_field, __('<strong>ERROR</strong>: ' . $error_msg_));
                //echo '<br>~~~~~~~~~ ERROR<br>';
                //var_dump($errors);
                // $this->errors = $errors;
                // $this->header_error_params($this->errors);
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

    protected function process_action() {
        if ('delete' == $_GET['action']) {
            $player = new JWPLIMELIGHT_Player($_GET['player_id']);
            $player->purge();
            $this->add_message("Player {$player->get_id()} has been deleted.");
            unset($player);
        }
    }

    public function process_post_data($post_data) {
        parent::process_post_data($post_data, false);
        if (isset($_GET['player_id'])) {
            return $this->process_edit_post_data($post_data);
        } else {
            //   return $this->render_edit_page();
            //  return $this->process_overview_post_data($post_data);
            // exit();
        }
    }

    protected function process_overview_post_data($post_data) {
        echo '<br> function process_overview_post_data<br>';
        var_dump($post_data);
        echo '<br> --- $this ---<br>';
        var_dump($this);
        if (!count($this->form_error_fields)) {
            if (isset($_POST['new_entity_id']) && $_POST['new_entity_id']) {
                
            }
            // При обновлении всех entity данных
            if (isset($_POST['update_entity_params']) && $_POST['update_entity_params']) {
                exit();
            }
        }

        // var_dump($this);
        wp_redirect($this->page_url());
        exit();
    }

    protected function process_edit_post_data($post_data) {
        foreach ($this->form_fields as $field) {
            $ok = $this->player->set($field->name, $field->value);
        }
        if (!count($this->form_error_fields)) {
            $this->player->save();
            wp_redirect($this->page_url(array('player_saved' => $this->player->get_id())));
            exit();
        } else {
            wp_head();
        }
    }

    private function _init_overview_page() {
        // global $lrapi;
        // echo '<br>function _init_overview_page <br>';
        // $get_entity = $lrapi->callApi('/entity/list', $params);
        // echo '<br> --- $get_entity --- <br>';
        // var_dump($get_entity);

        /* if ( isset($_GET['player_saved']) && array_key_exists($_GET['player_saved'], $this->players) ) {
          $this->add_message("Changes for <strong>player {$_GET['player_saved']}</strong> have been saved successfully.");
          } */
    }

    //$wpdb->query("UPDATE $wpdb->comments SET comment_approved = '1' WHERE comment_ID = '$comment'");

    public function get_all_entity_params() {
        global $wpdb;
        $get_results = $wpdb->get_results("SELECT id, entity_id, organization, parent_id, status FROM wp_entity");
        return $get_results;
    }

    private function upd_row($msRows, $myRows, $total) {
        $foundKeys = array(); //массив ключей строк, которые надо обновить
        $foundKeys2 = array();
        $tmpKey = 1;
        /* echo '<br> 1  $myRows -<br>';
          var_dump($myRows);
          echo '<br> 1  $msRows -<br>';
          var_dump($msRows);
          echo '<br> --- $total=' . $total; */
        if ($msRows) {
            foreach ($msRows as $key => $msRow) {
                //  echo '<br> 2  $msRow -<br>';
                //  var_dump($msRow);
                //  echo '<br> -- $msRow->entity_id ---' . $msRow->entity_id;
                // echo '<br> $myRows -<br>';
                // var_dump($myRows);
                /* if (!array_key_exists('"'.$msRow->entity_id.'"', $myRows))
                  $foundKeys[] = $msRow->entity_id;
                 */
                if (!in_array($msRow->entity_id, $myRows)) {
                    //  echo '<br>entity_id' . $msRow->entity_id;
                    $foundKeys[] = $msRow->entity_id;
                    // echo '<br>--- $key --- ' . $key;
                    // echo '<br>--- $foundKeys ---<br>';
                    //  var_dump($foundKeys);
                } else {
                    $foundKeys2[] = $msRow->entity_id;
                }

                if ($total == $tmpKey) {
                    $entity_params = array('add_entity' => $foundKeys, 'upd_entity' => $foundKeys2);
                    // var_dump($entity_params);
                    return $entity_params;
                }
                $tmpKey++;
                /* if ($total - 1 == $key) {
                  return $foundKeys;
                  } */
                /* echo '<br> 1  $msRow -<br>';
                  var_dump($msRow);
                  echo '<br> --1 entity_id --' . $msRow->entity_id;
                  // echo '<br> --1 entity_id --' . $msRow['entity_id'];
                  echo '<br> 2  $myRows -<br>';
                  var_dump($myRows);
                  echo '<br> --2 entity_id --' . $myRows->entity_id;
                  // echo '<br> --2 entity_id --' . $myRows['entity_id']; */
                /* if(!in_array($msRow, $myRows))
                  $foundKeys[] = $key;

                  if ($total - 1 == $key) {
                  return $foundKeys;
                  } */
            }
        }
        /* foreach ($msRows as $key => $msRow) {
          if (!array_key_exists($msRow['unique_name'], $myRows)) {
          $foundKeys[] = $key;
          }
          if ($total - 1 == $key) {
          return $foundKeys;
          }
          } */
    }

    private function get_entity_and_save_params() {
        global $wpdb;
        //return $this->render_overview();
        $get_entity = $this->get_entity();
        $tmp = 1;
        $local_entity_array = array();
        $local_entity_id = $wpdb->get_results("SELECT entity_id FROM wp_entity");

        foreach ($local_entity_id as $key => $value) {
            $local_entity_array[] = $value->entity_id;
        }

        $get_upd_row = $this->upd_row($get_entity->entities->entity, $local_entity_array, $get_entity->total);
        if ($get_entity->entities->entity) {
            foreach ($get_entity->entities->entity as $key => $entity) {
                if ($entity->parent_id <= 0 || $entity->parent_id == '')
                    $entity->parent_id = 0;

                if (in_array($entity->entity_id, $get_upd_row['add_entity'])) {
                    //усли таких entity нет в локадбной БД, то записываем
                    $query = "insert into wp_entity
                      (entity_id, organization, parent_id, status)
                      values ($entity->entity_id, '$entity->organization', $entity->parent_id, '$entity->status')";
                    $wpdb->query($query);
                    //echo '<br><br> $entity->entity_id = ' . $entity->entity_id;
                    //echo '<br> $entity->organization = ' . $entity->organization;
                    //echo '<br> $entity->parent_id = ' . $entity->parent_id;
                    //echo '<br> $entity->type = ' . $entity->type; 
                } else {
                    $wpdb->query("UPDATE wp_entity SET organization = '$entity->organization', parent_id=$entity->parent_id, status='$entity->status' "
                            . "WHERE entity_id = '$entity->entity_id'");
                    /*  echo '<br><br>else!!!!<br>';
                      echo '<br>else! $entity->entity_id =' . $entity->entity_id;
                      echo '<br>else! $entity->parent_id =' . $entity->parent_id; */
                }
                /*     $query = "insert into wp_entity
                  (entity_id, organization, parent_id, type)
                  values ($entity->entity_id, '$entity->organization', $entity->parent_id, '$entity->type')";
                  $wpdb->query($query);
                  echo '<br><br> $entity->entity_id = ' . $entity->entity_id;
                  echo '<br> $entity->organization = ' . $entity->organization;
                  echo '<br> $entity->parent_id = ' . $entity->parent_id;
                  echo '<br> $entity->type = ' . $entity->type;
                 * 
                 */
            }
        }
        if ($get_upd_row['upd_entity']) {
            // echo '<br>upd_entity!!!';
        }
    }

    // $get_results = $wpdb->get_results("SELECT * FROM wp_entity WHERE entity_id = '$entity->entity_id'");
    /*
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
      // echo '<br> $get_entity->total !!!   ' . $get_entity->total . '  $key=' . $key;
      return $this->render_overview();
      }
      $tmp++;
      }
      // echo '$get_entity->total -' . $get_entity->total;
      // echo '<br> get_entity_and_save_params!!!'; */



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
                // echo '<br> $get_entity->total !!!   ' . $get_entity->total . '  $key=' . $key;
                return $this->render_overview();
            }
            $tmp++;
        }
        // echo '$get_entity->total -' . $get_entity->total;
        // echo '<br> get_entity_and_save_params!!!';
    }

    private function _init_add_params() {
        echo '<br> !!! _init_add_params <br>';
    }

    private function _init_edit_page() {
        echo '<br> !!! _init_edit_page <br>';
    }

    private function _init_edit_page2() {
        // Basic settings
        $cannot_edit = false;
        if ($this->imported_players && array_key_exists($this->player->get('description'), $this->imported_players)) {
            $cannot_edit = 'You cannot edit the description of this player, because this is an imported JW5 player configuration and the description is used to map your old shortcodes to this player.';
        } else if (!$this->player->get_id()) {
            $cannot_edit = 'You cannot edit the description of the default editor.';
        }
        if ($cannot_edit) {
            $description_field = new JWPLIMELIGHT_Form_Field_Uneditable(
                    'description', array(
                'value' => $this->player->get('description'),
                'why_not' => $cannot_edit,
                    )
            );
        } else {
            
        }
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
        echo '<br> $field->options - ';
        var_dump($field->options);

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
        // $this->render_page_start('Edit player: <strong>' . $this->player->full_description() . '</strong>.');
        ?>
        <div class="backlink">
            <a href="<?php echo $this->page_url(); ?>">← Back to the list entity</a>
        </div>
        <script type="text/javascript">
            jQuery(function() {
                var $ = jQuery;
                function check_aspect_ratio() {
                    if ('NULL' == $('#id_aspectratio').val()) {
                        $('#width_row, #height_row').show();
                    } else {
                        $('#width_row, #height_row').hide();
                    }
                }
                $('#id_aspectratio').bind('change', function() {
                    check_aspect_ratio();
                });
                check_aspect_ratio();
            });
        </script>
        <?php
        $this->render_all_messages();
        ?>

        <?php //echo $this->page_url(array('noheader' => 'true', 'player_id' => '1'))                      ?>

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

        <form method="post" id="update_entity_params_form" name="update_entity_params_form" action="<?php //echo $this->page_url(array( 'update_entity_params' => 'true'))                                                                                                                   ?>">
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


        <form method="post" id="add_player_form" name="add_player_form" action="<?php //echo $this->page_url(array('noheader' => 'true', 'new_entity_id' => 'true'))                                                                                               ?>">
            <?php settings_fields(JWPLIMELIGHT . 'menu'); ?>
            <p class="submit">
                <input type="hidden" name="noheader" value="true" />
                <input type="hidden" name="new_entity_id" id="new_entity_id" value="true"/>
                <input type="submit" name="submit_form" id="submit_form" class="button-primary" value="Create a new entity"  />
            </p>
        </form>
        <script type="text/javascript">
            jQuery(function() {

            });
            /* jQuery(function () {
             var jwplimelight = new JWPLIMELIGHTAdmin();
             jwplimelight.player_copy();
             jwplimelight.player_delete();
             });*/
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
           <!-- <td><a href="<?php //echo $player->admin_url($this, 'edit');                                                                                                                                                             ?>" class="button jwplimelight_edit">Edit</a></td>
            <td><a href="<?php //echo $player->admin_url($this, 'copy');                                                                                                                                                           ?>" class="button jwplimelight_copy">Copy</a></td>
            <td>
            <?php if ($entity->entity_id): ?>
                                                                                                                                                                                                                    <a href="<?php //echo $player->admin_url($this, 'delete');                                                                                                                                     ?>" class="button jwplimelight_delete">Delete</a>
            <?php endif; ?>
            </td>-->
        </tr>
        <?php
    }

    protected function render_overview_entity_row($entity) {
        /* $description = $player->get('description');
          if ($description) {
          if ($this->imported_players && array_key_exists($description, $this->imported_players)) {
          $description .= " <em>(imported JW5 player)</em>";
          }
          } else {
          $description = "<em>no description</em>";
          }
          if ('NULL' == $player->get('aspectratio') || null === $player->get('aspectratio')) {
          $player_size = $player->get('width') . " x " . $player->get('height');
          } else {
          $player_size = "Responsive (" . $player->get('aspectratio') . ")";
          } */
        //echo '<br> -- $entity ';
        // var_dump($entity);
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
            <td><a href="<?php //echo $player->admin_url($this, 'edit');                                                                                                                                                             ?>" class="button jwplimelight_edit">Edit</a></td>
            <td><a href="<?php //echo $player->admin_url($this, 'copy');                                                                                                                                                           ?>" class="button jwplimelight_copy">Copy</a></td>
            <td>
                <?php if ($entity->entity_id): ?>
                    <a href="<?php //echo $player->admin_url($this, 'delete');                                                                                                                                    ?>" class="button jwplimelight_delete">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    protected function render_overview_row($player) {
        $description = $player->get('description');
        if ($description) {
            if ($this->imported_players && array_key_exists($description, $this->imported_players)) {
                $description .= " <em>(imported JW5 player)</em>";
            }
        } else {
            $description = "<em>no description</em>";
        }
        if ('NULL' == $player->get('aspectratio') || null === $player->get('aspectratio')) {
            $player_size = $player->get('width') . " x " . $player->get('height');
        } else {
            $player_size = "Responsive (" . $player->get('aspectratio') . ")";
        }
        ?>
        <tr valign="middle">
            <td align="center">
                <strong>
                    <?php echo $player->get_id(); ?>
                </strong>
            </td>
            <td><?php echo $description; ?></td>
            <td><?php echo $player_size; ?></td>
            <td><?php echo $player->get('primary'); ?></td>
            <td><a href="<?php echo $player->admin_url($this, 'edit'); ?>" class="button jwplimelight_edit">Edit</a></td>
            <td><a href="<?php echo $player->admin_url($this, 'copy'); ?>" class="button jwplimelight_copy">Copy</a></td>
            <td>
                <?php if ($player->get_id()): ?>
                    <a href="<?php echo $player->admin_url($this, 'delete'); ?>" class="button jwplimelight_delete">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

}
