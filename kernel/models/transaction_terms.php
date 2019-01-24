<?php

use lib\security;

if (!defined('IN_ROOT')) {
    print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
    exit();
}

$CMS->transaction_terms = new classTransactionTerms;

class classTransactionTerms
{

    public $CMS = "";

    /**
     * @param @record_cnt
     *        The order number of Data
     */

    public $record_cnt = 0;

    /**
     * @param @arrange_data
     *        Arrange Data, using for re-order the listing
     */

    public $arrange_data = "";

    /**
     * @param @sql_query
     *        The SQL Query for listing Data
     */

    public $sql_query = "";

    /**
     * @param @sql_add
     *        The additional SQL for $sql_query
     */

    public $sql_add = "";

    /**
     * @param $control
     *        0 for no control, 1 for has control, DONT CHANGE the default value
     */

    public $control = 0;

    /**
     * @param $action_control
     *        HTML action control
     */

    public $action_control = "";

    /**
     * @param $html_data
     *        HTML of records
     */

    public $html_data = 0;

    /**
     * @param $html
     *        The templates
     */

    public $html;

    /**
     * @param $cache
     *        Temp store transaction_terms info
     */

    public $cache = array();

    /**
     * @param $token_key
     *        Unique token
     */
    public $token_key;

    public $cache_prefix = 'transaction_terms';

    //===========================================================================
    //  LISTING DATA
    //===========================================================================

    public function loadhtml()
    {
        global $CMS;

        if (!isset($this->html)) {
            $this->html = $CMS->class->template->load_template("skin_transaction_terms");
        }
    }

    public function listing()
    {
        global $CMS, $DB;

        // Update Arrange Data
        $this->arrange_data = trim("term_id,term_days,term_time");

        // Set default for Arrange
        $default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "term_id";
        $default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";

        // SQL Condition
        $this->sql_add .= " term_deleted=0 AND ";

        $sql = "SELECT * FROM " . root_table . "transaction_terms WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($this->show_page, $data) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

        return $data;
    }

    public function html($data = [])
    {
        global $CMS, $DB, $member;

        $this->loadhtml();

        // Display Header
        $output .= $this->html->header();

        if ($data) {
            foreach ($data as $result) {
                // Convert info
                $result = $CMS->transaction_terms->convertvalue($result);

                // Display Middle
                $output .= $this->html->middle($result);
            }
        } else {
            // Display No data
            $output .= $this->html->none();

            // No data
            $CMS->is_error = 1;
        }

        // Display
        $output .= $this->html->footer();

        // If the page is giving no data
        if ($CMS->input['page'] > 1 && $CMS->is_error == 1) {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transaction_terms");
        }

        return $output;
    }

    //===========================================================================
    //  AUTO RUN
    //===========================================================================

    public function auto_run()
    {
        global $CMS, $DB, $member;

        $this->loadhtml();

        //-----------------------------------------------------------
        // ACTION CONTROLLER
        //-----------------------------------------------------------

        if ($CMS->class->cache->check("user_{$member['user_id']}_transaction_terms_controller_{$CMS->vars['default_language']}")) {
            $CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_transaction_terms_controller_{$CMS->vars['default_language']}");
        } else {
            $data = "";

            // Check permission to Delete
            if ($CMS->permit["transaction_terms_delete"] == true) {
                $data .= "<option value='delete_all'>{$CMS->lang['action_delete']}</option>";
                $this->control = 1;
            }

            $data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

            $CMS->class->cache->save("user_{$member['user_id']}_transaction_terms_controller_{$CMS->vars['default_language']}", $data);
            $CMS->vars['action_controller'] = $data;
        }

        if ($CMS->vars['action_controller'] OR $CMS->permit["transaction_terms_search"] == 1) {
            $this->action_control = $this->html->control();
        }
    }

    public function data()
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM " . root_table . "transaction_terms WHERE transaction_terms_deleted=0";

        $output = "";

        $data = $DB->fetch_data($sql, $this->cache_prefix);

        if ($data) {
            foreach ($data as $item) {
                $output .= "<option value='{$item['term_days']}'>{$item['term_days']} {$CMS->lang['day']}</option>";
            }
        }

        $this->html_data = $output;
    }

    //===========================================================================
    //  DATA
    //===========================================================================

    public function defaultvalue($data)
    {
        global $CMS, $member;

        $data['term_status'] = $data['term_status'] ? $data['term_status'] : 1;

        return $data;
    }

    public function convertvalue($data, $data_convert = array())
    {
        global $CMS, $DB;

        $data['data_bk'] = $data;

        $data['term_status'] = $CMS->lang["display_{$data['term_status']}"];

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        // Convert unix time to GMT time
        $data['term_time'] = $CMS->class->date->date_format($data['term_time'], 1);

        // Check permission to read Info
        if ($CMS->permit["transaction_terms_read"] == true) {
            $data['term_days'] = "<a href='{$CMS->vars['root_domain']}/?site=transaction_terms&act=show&id={$data['term_id']}'>{$data['term_days']}</a>";
        }

        // Bgcolor
        $data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";

        // Check permission to read Info
        $user_name = $CMS->user->get_info($data['user_id'], "user_display_name");

        if ($CMS->permit["user_read"] == true) {
            $data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$user_name}</a>";
        } else {
            $data['user_id'] = $user_name;
        }

        // Count
        $data['record_cnt'] = $this->record_cnt;

        $this->record_cnt++;

        return $data;
    }

    public function editvalue($data)
    {
        global $CMS, $DB;

        return $data;
    }

    public function searchvalue($data)
    {
        global $CMS, $DB;

        $data = $this->convertvalue($data);

        return $data;
    }

    //===========================================================================
    //  INFO
    //===========================================================================

    public function get_info($record_id = 0, $field_name = "")
    {
        global $CMS, $DB, $member;

        if (!$record_id AND $CMS->input['site'] == "transaction_terms") {
            $record_id = intval($CMS->input['id']);
        }

        // Clear record
        $record_id = strip_tags($record_id);

        // Check record		
        if (!$record_id) {
            return false;
        }

        // Continue

        $sql_add = "term_id='{$record_id}' AND ";


        $sql = "SELECT * FROM " . root_table . "transaction_terms WHERE {$sql_add} term_deleted=0 ORDER BY term_id DESC LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if ($data) {
            if ($field_name) {
                if ($data[$field_name]) {
                    return $data[$field_name];
                } else {
                    return false;
                }
            }

            return $data;
        } else {
            return false;
        }
    }

    public function check_exist($field, $value = "", $except_value = "")
    {
        global $CMS, $DB, $member;

        if (!$field) {
            return true;
        }

        $sql_add = '';

        if ($except_value) {
            $sql_add .= "{$field}!='{$except_value}' AND";
        }

        $sql = "SELECT count(0) cnt FROM " . root_table . "transaction_terms WHERE {$sql_add} {$field}='{$value}' AND term_deleted=0";

        return $DB->fetch_data($sql, $this->cache_prefix)[0]['cnt'];
    }

    /****************************************************************************
     * add
     * add new transaction_terms to database
     ****************************************************************************/
    public function add($data = array())
    {
        global $CMS, $DB, $member;


        //check token
        if (!lib / security::check_token()) {
            $_SESSION['msg'] = $CMS->lang['invalid_token'];
            return false;
        }

        if (empty($data) || !is_array($data)) {
            $data = $CMS->input;
        }

        $this->token_key = $CMS->class->random->md5("add_transaction_terms");
        $data['term_token_key'] = $this->token_key;
        $data['term_time'] = time();
        $data['user_id'] = $member['user_id'];
        unset($data['term_id']);

        // Check required input
        $required_input = array('term_name', 'term_days', 'term_status');

        if (!$this->check_required($required_input, $data)) return false;

        if ($this->check_exist('term_days', $data['term_days'])) {
            $_SESSION['msg'] = $CMS->lang['existed_term'];
            return false;
        }

        $table_name = 'transaction_terms';

        // Insert data
        $columns = $DB->get_column_names($table_name);
        $sql_insert_fields = "";
        $sql_insert_values = "";
        foreach ($columns as $field) {
            if (isset($data[$field])) {
                $sql_insert_fields .= "{$field},";
                $sql_insert_values .= "'{$data[$field]}',";
            }
        }

        $sql_insert_fields = trim($sql_insert_fields, ',');
        $sql_insert_values = trim($sql_insert_values, ',');

        if (empty($sql_insert_fields) || empty($sql_insert_values)) {
            $CMS->errormsg .= "{$CMS->lang['error_while_added']} (err:1)<br/>";
            return false;
        } else {
            $sql_insert_data = "INSERT INTO " . root_table . "{$table_name} ($sql_insert_fields) VALUES ($sql_insert_values)";
            if ($DB->query($sql_insert_data)) {
                //Clear cache
                $CMS->class->cache->mdelete($this->cache_prefix);


                // Get info
                $inserted_data = $this->inserted_record();

                // Create log
                $CMS->class->logs->key = "transaction_terms_{$data['transaction_terms_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['added']} <strong>{$data['term_name']}</strong>") . "<br />";
            } else {
                $CMS->errormsg .= "{$CMS->lang['error_while_added']}  (err:2)<br/>";
                return false;
            }
        }

        // Update Module ID
        $CMS->attach->update("transaction_terms", $inserted_data['term_id']);

        return $inserted_data;

    }

    //===========================================================================
    //  EDIT
    //===========================================================================

    public function edit($data = array(), $required_input = array())
    {
        global $CMS, $DB, $member;

        //check token
        if (!lib\security::check_token()) {
            $_SESSION['msg'] = $CMS->lang['invalid_token'];
            return false;
        }

        if (empty($data) || !is_array($data)) {
            $data = $CMS->input;
        }

        $old_data = $this->get_info($data['term_id']);

        if (!$old_data) {
            $_SESSION['msg'] = $CMS->lang['no_data'];
            return false;
        }

        if ($required_input != -1) // If $required_input = -1 => disabled check required
        {
            // Check required input
            $required_input = array('term_name', 'term_days', 'term_status');
        }


        if (!$this->check_required($required_input, $data)) return false;

        if ($this->check_exist('term_days', $data['term_days'], $old_data['term_days'])) {
            $_SESSION['msg'] = $CMS->lang['existed_term'];
            return false;
        }

        $table_name = 'transaction_terms';

        // update data
        $columns = $DB->get_column_names($table_name);
        $sql_update = "";
        foreach ($columns as $field) {
            if (isset($data[$field])) {
                $sql_update .= " {$field} = '{$data[$field]}',";
            }
        }

        $sql_update = trim($sql_update, ',');

        if (empty($sql_update)) {
            $CMS->errormsg .= "{$CMS->lang['error_while_edited']} (err:1)<br/>";
            return false;
        } else {
            $sql_update_data = "UPDATE " . root_table . "{$table_name} SET $sql_update WHERE term_id = '{$data['term_id']}'";

            if ($DB->query($sql_update_data)) {
                //Clear cache
                $CMS->class->cache->mdelete($this->cache_prefix);

                // Create log
                $CMS->class->logs->key = "transaction_terms_{$data['term_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['edited']} <strong>{$data['term_name']}</strong>") . "<br />";
            } else {
                $CMS->errormsg .= "{$CMS->lang['error_while_edited']}  (err:2)<br/>";
                return false;
            }
        }

        // Update Module ID
        $CMS->attach->update("transaction_terms", $data['term_id']);

        return $data;
    }

    //===========================================================================
    //  DELETE
    //===========================================================================

    public function delete()
    {
        global $CMS, $DB;

        // Get info
        $data = $this->get_info();

        // Check existing
        if (!$data) {
            return false;
        }


        // Update info
        $DB->query("UPDATE " . root_table . "transaction_terms SET term_deleted=1 WHERE term_id={$data['term_id']}");

        // Create log
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <strong>{$data['term_days']}</strong>") . "<br />";


        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        // Redirect
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transaction_terms&page={$CMS->input['page']}");

        return true;
    }

    public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] .= "";

        for ($i = 0; $i < intval($CMS->input["data_cnt"]); $i++) {
            $id = intval($CMS->input["id_{$i}"]);

            if ($id) {
                $data = $this->get_info($id);

                if ($data['transaction_terms_protected'] == 1) {
                    $_SESSION["msg"] .= $CMS->lang['delete_failed'];
                } else {
                    $DB->query("UPDATE " . root_table . "transaction_terms SET term_deleted=1WHERE term_id={$id}");

                    $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <strong>{$data['term_days']}</strong>") . "<br />";

                    $deleted = 1;
                }
            }
        }

        if ($deleted == 0) {
            $_SESSION["msg"] .= "{$CMS->lang['delete_failed']}";
        }

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        return true;
    }

    //===========================================================================
    //  SEARCH
    //===========================================================================

    public function search()
    {
        global $CMS, $DB;

        // Setup data
        $CMS->class->search->mod_name = "transaction_terms";
        $CMS->class->search->table_name = "transaction_terms";
        $CMS->class->search->fields_type = array("term_time" => "time");

        // Output
        $data = $CMS->class->search->get_info();

        // Update SQL Query
        $this->sql_add .= $data;

        // Get List
        return $this->listing();
    }

    public function check_required($required_fields = array(), $data = array())
    {
        global $CMS;

        if (empty($required_fields) || empty($data) || !is_array($required_fields) || !is_array($data)) return true;

        foreach ($required_fields as $field) {
            if (isset($data[$field])) {
                if (trim($data[$field]) == '' || $data[$field] == null) {
                    $lang_key = 'incomplete_' . preg_replace('/^term_/', '', $field);
                    $_SESSION['msg'] .= "{$CMS->lang[$lang_key]}<br />";
                    return false;
                }
            }
        }

        return true;
    }

    public function load_status_html()
    {
        global $CMS;
        $output = '';

        $data = array(1, 0);

        foreach ($data as $key) {
            $output .= "<option value='{$key}'>{$CMS->lang['display_'.$key]}</option>";
        }

        return $output;
    }

    public function inserted_record()
    {
        global $CMS, $DB, $member;

        // Check record
        if (!$this->token_key) {
            return false;
        }

        $sql = $DB->query("SELECT * FROM " . root_table . "transaction_terms WHERE term_token_key = '{$this->token_key}' AND term_deleted=0 ORDER BY term_id DESC LIMIT 1");

        if ($DB->num_rows($sql) > 0) {
            $data = $DB->fetch_assoc($sql);
            return $data;
        } else {
            return false;
        }
    }

    public function dataAjax()
    {
        global $CMS, $DB;
        $term = trim($CMS->input['term']);

        $sql_add = '';

        if ($term) {
            $sql_add .= " (term_days='{$term}' OR term_name LIKE '%{$term}%') AND ";
        }

        $sql = "SELECT * FROM " . root_table . "transaction_terms WHERE {$sql_add} term_deleted=0 AND term_status=1 ORDER BY term_days";

        $results = $DB->fetch_data($sql, $this->cache_prefix);

        $return = [];

        if ($results) {
            foreach ($results as $item) {
                $return[] = ['key' => $item['term_id'], 'id' => intval($item['term_days']), 'value' => $item['term_name']];
            }
        }

        return $return;
    }

    function formAddTransactionTerm($data=null)
    {
        global $CMS;

        $CMS->class->language->load("transaction_terms");

        $output = <<<EOF
		<div id="box_add_transaction_terms" class="popup_add_term mfp-hide">
			<p class="title_add_transaction_terms  " style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['add_form']}</p>
			<p style="color:red" class="shimanu_error_msg"></p>
			<form id="add_term_form" name="add_term_form">
			<input type="hidden" name="term_id" />
			<input type="hidden" name="term_status" value="1"/>
				<ul class="list_field_shipment">		
					  
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['term_name']} <span style="color:red">(*)</span></label>
										<input class="form-control" type="text" name="term_name" id="term_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_name']}" value="{$data['term_name']}">
									
							</fieldset>
						</li>
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['term_days']} <span style="color:red">(*)</span></label>
										<input class="form-control" type="number" name="term_days" id="term_days" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_days']}" value="{$data['term_days']}">
									
							</fieldset>
						</li>
						<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                            <fieldset class="form-group">
                                <div class="typeahead-field"> 
                                    <span class="typeahead-query change_action_transaction_terms"><input class="btn btn_add_term" type="button" value="{$CMS->lang['add_form']}"></span>
                                </div>
                            </fieldset>
                        </li>
				</ul>
			</form>
		</div>	

		 

EOF;
        return $output;
    }
}

?>