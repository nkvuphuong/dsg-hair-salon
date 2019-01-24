<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */

namespace models;

use lib\date;
use lib\input;
use lib\db;
use \lib\template;

class attribute
{
   
    /**
     * @param $record_cnt
     *		The order number of Data
     */
    static public $record_cnt = 0;

    /**
    * @param @$arrangeData
    *		The SQL Query for listing Data
    */
    static public $arrangeData;

    /**
     * @param $sqlAdd
     *		The additional SQL for $sql_query
     */
    static public $sqlAdd;

    /**
     * @param $sqlQuery
     *		The SQL Query for listing Data
     */
    static public $sqlQuery;

    /**
     * @param $maxPage
     *		Number of records on per page
     */
    static public $maxPage = 20;

    /**
     * @param $prefixPaging
     *		Prefix for paging url
     */
    static public $prefixPaging = '';

    /**
     * @param $prefixPaging
     *		Suffix for paging url
     */
    static public $suffixPaging = '';

    /**
     * Add new postion
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $group_time = time();
        $group_name = $data['group_name'];
        $group_description = $data['group_description'];
        $group_status = intval($data['group_status']);
        $group_attribute_list = !empty($data['attr_id']) ? ",".implode(",", $data['attr_id'])."," : "";
        // $group_sort = intval($data['group_sort']);

        if(!$group_name)
        {
            $_SESSION['msg'] = $CMS->lang['attr_name_err'];
            return false;
        }

        // Insert group
        $sql = "INSERT INTO ".root_table."attribute_group (group_name, group_description, group_status, group_time, group_attribute_list) VALUES ('{$group_name}', '{$group_description}', '{$group_status}', '{$group_time}', '{$group_attribute_list}')";

        $DB->query($sql);
        // get ID
        $group_id = $DB->last_insert_id();
        $data_new = self::getInfo_group($group_id);
        //Clear cache
        $CMS->class->cache->mdelete('attribute');
        $CMS->class->logs->key = "attribute_{$data_new['group_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['msg_add_attribute_success']}: <strong>{$group_name}</strong>");

        return $data_new;
    }

    /**
     * Edit logo_positions
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $oldData = self::getInfo_group($data['id']);

        if(!$oldData)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $group_time_update = time();
        $group_name = $data['group_name'];
        $group_description = $data['group_description'];
        $group_status = intval($data['group_status']);
        $group_attribute_list = !empty($data['attr_id']) ? ",".implode(",", $data['attr_id'])."," : "";
        // $group_sort = intval($data['group_sort']);

        if(!$group_name)
        {
            $_SESSION['msg'] = $CMS->lang['attr_name_err'];
            return false;
        }

        $CMS->class->logs->key = "attribute_{$oldData['group_id']}";
        $CMS->class->logs->old = $oldData;

        $sql = "UPDATE ".root_table."attribute_group SET group_name='{$group_name}', group_status='{$group_status}', group_description='{$group_description}', group_attribute_list = '{$group_attribute_list}', group_time_update='{$group_time_update}' WHERE group_id='{$CMS->input['id']}'";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('attribute');

        $data_new = self::getInfo_group($CMS->input['id']);

        $CMS->class->logs->key = "attribute_{$data_new['group_id']}";
        $CMS->class->logs->save_detail("attribute_group",$data_new['group_id'],$data_new);
        $_SESSION['msg'] = "{$CMS->lang['msg_edit_attribute_success']}: <strong>{$data_new['group_name']}</strong>";

        return $data_new;
    }

    /**
     * Delete logo
     * @return bool
     */
    static public function delete()
    {
        global $CMS, $DB;

        // Get info
        $data = self::getInfo_group();

        // Check existing
        if ( ! $data ) { return false; }

        // Update info
        $DB->query("UPDATE ".root_table."attribute_group SET group_deleted=1 WHERE group_id={$data['group_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('attribute');

        // Create log
        $CMS->class->logs->key = "attribute_{$data['group_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['group_deleted']} <b>{$data['group_name']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi attribute
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] .= "";
        for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
        {
            $id = intval( $CMS->input["id_{$i}"] );

            if ( $id )
            {
                $data = self::getInfo_group($id);

                $DB->query("UPDATE ".root_table."attribute_group SET group_deleted=1 WHERE group_id={$id}");

                $CMS->class->logs->key = "attribute_{$data['group_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['group_deleted']} <b>{$data['group_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $CMS->class->cache->mdelete('attribute');

            $_SESSION["msg"] .= "{$CMS->lang['group_delete_failed']}";
        }

        return true;
    }

    /**
     * Get infomation of a logo
     * @param int $id
     * @return array
     */
    static public function getInfo($id = 0)
    {
        global $CMS, $DB;

        if(!$id) { return false; }

        $sql = "SELECT * FROM ".root_table."attribute WHERE attr_deleted=0 AND attr_id='{$id}' LIMIT 1";

        return $DB->fetch_data($sql,'attribute')[0];
    }

    static public function getInfo_group($group_id = 0, $return_field="")
    {
        global $CMS, $DB;

        if(!$group_id)
        {
            $group_id = intval($CMS->input['id']);
        }

        $sql = "SELECT * FROM ".root_table."attribute_group WHERE group_deleted=0 AND group_id='{$group_id}' LIMIT 1";
        if($return_field)
        {
            return $DB->fetch_data($sql,'attribute')[0][$return_field];
        }else
        {
            return $DB->fetch_data($sql,'attribute')[0];
        }
        
    }

    static public function getInfo_options($id = 0)
    {
        global $CMS, $DB;

        if(!$id) { return false; }

        $sql = "SELECT * FROM ".root_table."attribute_options WHERE options_deleted=0 AND options_id='{$id}' LIMIT 1";

        return $DB->fetch_data($sql,'attribute')[0];
    }

    /**
     * Get infomation of record which just added
     * @param string $token_key
     * @return bool|array
     */
    public function insertedRecord($token_key = "")
    {
        global $CMS, $DB;

        if(!$token_key) return false;

        $sql = "SELECT * FROM ".root_table."attribute WHERE group_token_key='{$token_key}' ORDER BY group_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        return $DB->fetch_assoc($sql);
    }

    /**
     * Convert original record to display on edit form
     * @param array $data
     * @return array
     */
    static  public function editValue($data=[])
    {
        global $CMS;

        $data['group_status'] = isset($data['group_status']) ? intval($data['group_status']) : 1;
        return $data;
    }

    /**
     * Convert original record to show
     * @param array $data
     * @return array
     */
    static  public function convertValue($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        if($CMS->permit['attribute_read'] || $CMS->permit['attribute_is_root'])
        {
            $data['group_name'] = "<a href='{$CMS->vars['root_domain']}/?site=attribute&act=show&id={$data['group_id']}'>{$data['group_name']}</a>";
        }

        $data['group_time'] = $data['group_time'] ? date::format($data['group_time']) : '';
        $data['group_status_bk'] = $data['group_status'];
        $data['group_status'] = $CMS->lang['group_status_'.$data['group_status']];

        $data['record_cnt'] = self::$record_cnt;

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list attribute
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("group_id,group_name,group_description,group_status,group_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "group_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "DESC";

        // SQL Condition
        $sql_add .= " group_deleted=0 AND ";

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            $keyword = urldecode($CMS->input['keyword']);
            $sql_add .= " (group_name LIKE '%{$keyword}%' OR group_description LIKE '%{$keyword}%') AND ";
        }

        $sql = "SELECT * FROM ".root_table."attribute_group WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($CMS->show_page, $data) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'attribute');

        return $data;
    }


    /**
     * get all attribute
     * @param string $sql_add
     * @return array
     */
    static function getAll($sql_add = "")
    {
        global $CMS, $DB;

        $sql_add = $sql_add.self::$sqlAdd;
        $sql = "SELECT * FROM ".root_table."attribute WHERE {$sql_add} group_deleted=0 ORDER BY group_name";

        return $DB->fetch_data($sql,'attribute');
    }

    static function add_attr()
    {
        global $CMS, $DB;
        // p($CMS->input);exit;
        // Add ajax
        $attr_name = $CMS->input['attr_name'];
        $attr_key = str_replace(" ","",$CMS->class->seo->remove_vietnamese($CMS->input['attr_key']));
        
        $attr_type = 1;//$CMS->input['attr_type'];
        $attr_unit = $CMS->input['attr_unit'];
        $attr_order = intval($CMS->input['attr_order']);
        $attr_description = $CMS->input['attr_description'];
        $attr_required = intval($CMS->input['attr_required']);
        $attr_search = intval($CMS->input['attr_search']);
        $attr_time = time();

        // Data attribute value
        $arr_name = array_values($CMS->input['options_name']);
        $arr_value = array_values($CMS->input['options_value']);
        $arr_id = array_values($CMS->input['options_id']);

        // check input 
        if(self::checkInput($attr_name, "attr_name"))
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['msg_attribute_name_exist']));
            exit;
        }

        if(self::checkInput($attr_key, "attr_key"))
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['msg_attribute_key_exist']));
            exit;
        }

        $check = $DB->query("INSERT INTO ".root_table."attribute (attr_name, attr_key, attr_type, attr_unit, attr_order, attr_description, attr_required, attr_search, attr_time) VALUES ('{$attr_name}', '{$attr_key}', '{$attr_type}', '{$attr_unit}', '{$attr_order}', '{$attr_description}', '{$attr_required}', '{$attr_search}', '{$attr_time}')");
        $id = $DB->last_insert_id();

        // Insert attribute value
        for($x=0;$x<count($arr_name);$x++)
        {
            $options_name = $arr_name[$x];
            $options_value = $arr_value[$x]; 
            $options_key = md5($options_name.$options_value.$attr_time);
            $options_id = intval($arr_id[$x]);

            // Check name
            if(!$options_name) { continue;}

            // Check name get options id
            if(!$options_id)
            {
                $options_id = self::getOptionsIdByName($options_name, $id);
            }

            if($options_id)
            {
                // Update theo options id
                $DB->query("UPDATE ".root_table."attribute_options SET options_name='{$options_name}', options_value='{$options_value}', options_time_update='{$attr_time}' WHERE options_id='{$options_id}'");
            }else
            {
                // Insert mới
                $DB->query("INSERT INTO ".root_table."attribute_options (attr_id, options_name, options_value, options_key, options_time) VALUES ('{$id}', '{$options_name}', '{$options_value}', '{$options_key}', '{$attr_time}')");
            }
        }

        $data = self::getInfo($id);
        list($data['options_count'], $data['attribute_options']) = self::countOptionsAttribute($id, 1);
        // $data['attribute_options'] = json_encode($data['attribute_options'], JSON_UNESCAPED_UNICODE);
        //Clear cache
        $CMS->class->cache->mdelete('attribute');
        $CMS->class->logs->key = "attribute_{$data['attr_id']}";
        $CMS->class->logs->insert("{$CMS->lang['msg_add_attribute_success']}: <strong>{$attr_name}</strong>");

        return $data;
    }


    static function edit_attr()
    {
        global $CMS, $DB;

        // Data old
        $id = intval($CMS->input['id']);
        $data = self::getInfo($id);
        if(!$data) {return false;}
        // Add ajax
        $attr_name = $CMS->input['attr_name'];
        $attr_key = str_replace(" ","",$CMS->class->seo->remove_vietnamese($CMS->input['attr_key']));

        $attr_type = 1;//$CMS->input['attr_type'];
        $attr_unit = $CMS->input['attr_unit'];
        $attr_order = intval($CMS->input['attr_order']);
        $attr_description = $CMS->input['attr_description'];
        $attr_required = intval($CMS->input['attr_required']);
        $attr_search = intval($CMS->input['attr_search']);
        $attr_time_update = time();

        // Data attribute value
        $arr_name = array_values($CMS->input['options_name']);
        $arr_value = array_values($CMS->input['options_value']);
        $arr_id = array_values($CMS->input['options_id']);

        // check input 
        if(self::checkInput($attr_name, "attr_name", $data['attr_name']))
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['msg_attribute_name_exist']));
            exit;
        }

        if(self::checkInput($attr_key, "attr_key", $data['attr_key']))
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['msg_attribute_key_exist']));
            exit;
        }

        $CMS->class->logs->key = "attribute_{$id}";
        $CMS->class->logs->old_data = $data;
        $CMS->class->logs->insert($CMS->class->logs->key);

        $DB->query("UPDATE ".root_table."attribute SET attr_name='{$attr_name}', attr_key='{$attr_key}', attr_value='{$attr_value}', attr_type='{$attr_type}', attr_unit='{$attr_unit}', attr_order='{$attr_order}', attr_description='{$attr_description}', attr_required='{$attr_required}', attr_search='{$attr_search}', attr_time_update='{$attr_time_update}' WHERE attr_id='{$id}' AND attr_deleted=0");
        $data_new = self::getInfo($id);
        // Insert attribute value
        for($x=0;$x<count($arr_name);$x++)
        {
            $options_name = $arr_name[$x];
            $options_value = $arr_value[$x]; 
            $options_key = md5($options_name.$options_value.$attr_time_update);
            $options_id = intval($arr_id[$x]);
            // Check name
            if(!$options_name) { continue;}

            // Check name get options id
            if(!$options_id)
            {
                $options_id = self::getOptionsIdByName($options_name, $id);
            }

            if($options_id)
            {
                // Update theo options id
                $DB->query("UPDATE ".root_table."attribute_options SET options_name='{$options_name}', options_value='{$options_value}', options_time_update='{$attr_time_update}' WHERE options_id='{$options_id}'");
            }else
            {
                // Insert mới
                $DB->query("INSERT INTO ".root_table."attribute_options (attr_id, options_name, options_value, options_key, options_time) VALUES ('{$id}', '{$options_name}', '{$options_value}', '{$options_key}', '{$attr_time_update}')");
            }
        }
        // Bo sung them option count
        list($data_new['options_count'], $data_new['attribute_options']) = self::countOptionsAttribute($id, 1);

        //Clear cache
        $CMS->class->cache->mdelete("attribute");

        $CMS->class->logs->key = "attribute_{$data_new['attr_id']}";
        $CMS->class->logs->save_detail("attribute",$data_new['attr_id'], $data_new);

        return $data_new;
    }

    static function checkInput($value="", $field="", $value_accept="")
    {
        global $CMS, $DB;

        $sql_add = "";
        if($value_accept)
        {
            $sql_add .= " {$field} != '{$value_accept}' AND ";
        }

        $data = $DB->fetch_data("SELECT 0 FROM ".root_table."attribute WHERE {$sql_add} {$field}='{$value}' AND attr_deleted=0 ");
        return count($data) > 0 ? true: false;
    }

    static function searchAttr()
    {
        global $CMS, $DB;

        $key = urldecode($CMS->input['term']);
        $results = $DB->fetch_data("SELECT * FROM ".root_table."attribute WHERE attr_deleted=0 AND attr_name LIKE '%{$key}%' OR attr_key LIKE '%{$key}%' ORDER BY attr_name ASC");
        $output = [];
        foreach ($results as $data) 
        {
            // Bo sung them option count
            list($data['options_count'], $data['attribute_options']) = self::countOptionsAttribute($data['attr_id'], 1);
            $output[] = $data;
        }
        return $output;
    }

    static function getListAttribute($data=[])
    {
        global $CMS, $DB;

        $listAttrId = ltrim(rtrim($data['group_attribute_list'], ","), "," ); 
        $listAttrId = !empty($listAttrId) ? explode(",", $listAttrId) : [];
        $output = [];

        foreach ($listAttrId as $attr_id) 
        {
            $data = self::getInfo($attr_id);
            if(!$data) {continue;}
            list($data['options_count'], $data['attribute_options']) = self::countOptionsAttribute($attr_id, 1);
            $output[] = $data;
        }
        
        return $output;
    }

    static public function getOptionAttrGroup()
    {
        global $CMS, $DB;

        $results = $DB->fetch_data("SELECT group_name, group_id, group_attribute_list FROM ".root_table."attribute_group WHERE group_deleted=0 AND group_status=1 ORDER BY group_name ASC", "attribute_select_option");
        $output = "";

        foreach ($results as $data) 
        {
            $output .= "<option value='{$data['group_id']}'>{$data['group_name']}</option>";
        }
        return $output;

    }

    static function getHtmlAttributeByGroup($attr_group=0)
    {
        global $CMS, $DB;

        $data = self::getInfo_group($attr_group, "group_attribute_list");
       
        $listAttr = ltrim(rtrim($data, ","),",");
        $listAttr = explode(",", $listAttr);
        $output = "";
        $i = 1;
        foreach ($listAttr as $key => $attr_id) 
        {
            $info = self::getInfo($attr_id);
            if(!$info) {continue; }
            if($info['attr_type'] == 1)
            {
                // Select 
                $is_required_title = $info['attr_required'] ? "<span style='color:red'>(*)</span>" : "";
                $is_required_html = $info['attr_required'] ? " data-validation='[NOTEMPTY]' data-validation-message='{$CMS->lang['field_is_required']}' " : "";
                $listvalue = self::getListValueByGroupId($attr_id);
                // p($listvalue);exit;
                //{$is_required_title}
                //{$is_required_html}
                $output .=<<<EOF
                <fieldset class='form-group'>
                  <label class='form-label semibold'>{$info['attr_name']} </label>
                  <select class='form-control select2 listattr clist' id="sel_{$i}" onchange="changeCombination(this);" inc="{$i}" multiple="multiple" name='{$info['attr_key']}[]'>
EOF;
                  //{$data['options_id']}
                        foreach ($listvalue as $data) 
                        {
                            $json = array(
                                        "id" => $data['options_id'],
                                        "name" => $data['options_name'],
                                        "unit" => $info['attr_unit'],
                                        "key" => $data['options_key'],
                                        "attr_id" => $attr_id 
                                    );
                            $json = json_encode($json, JSON_UNESCAPED_UNICODE);
                            $output .=<<<EOF
                            <option value='{$json}'>{$data['options_name']} {$info['attr_unit']}</option>
EOF;

                        }
$output .=<<<EOF

                    </select>
                </fieldset>
EOF;

            }else
            {
                // input 
                $is_required_title = $info['attr_required'] ? "<span style='color:red'>(*)</span>" : "";
                $is_required_html = $info['attr_required'] ? " data-validation='[NOTEMPTY]' data-validation-message='{$CMS->lang['field_is_required']}' " : "";
                $unit_show = $info['attr_unit'] ? "({$info['attr_unit']})" : "";
                // $listvalue = json_decode($info['attr_value'], 1);
                // {$is_required_title}
                // {$is_required_html}
                $output .=<<<EOF
                <fieldset class='form-group'>
                  <label class='form-label semibold'>{$info['attr_name']} </label>
                  <input class='form-control' type="text" value='' name='{$info['attr_key']}'  />
                </fieldset>
EOF;
            }

            $i++;
        }


        return $output;
    }

    static function getOptionsIdByName($options_name="", $attr_id=0)
    {
        global $CMS, $DB;

        $results = $DB->fetch_data("SELECT options_id FROM ".root_table."attribute_options WHERE options_name='{$options_name}' AND attr_id='{$attr_id}'")[0];
        return intval($results['options_id']);
    }

    static function countOptionsAttribute($attr_id=0, $type=0)
    {
        global $CMS, $DB;

        $results = $DB->fetch_data("SELECT options_id, options_name, options_value, attr_id FROM ".root_table."attribute_options WHERE attr_id='{$attr_id}' AND options_deleted=0");
        if($type==1)
        {
            return array(count($results), $results);
        }else
        {
            return count($results);
        }
    }

    static function delvalueoption($options_id=0)
    {
        global $CMS, $DB;

        $DB->query("UPDATE ".root_table."attribute_options SET options_deleted=1 WHERE options_id='{$options_id}'");
        return true;
    }

    static function delattr($attr_id=0)
    {
        global $CMS, $DB;

        $DB->query("UPDATE ".root_table."attribute SET attr_deleted=1 WHERE attr_id='{$attr_id}'");
        return true;
    }

    static function getListValueByGroupId($attr_id=0)
    {
        global $CMS, $DB;

        $results = $DB->fetch_data("SELECT * FROM ".root_table."attribute_options WHERE options_deleted=0 AND attr_id='{$attr_id}' ORDER BY options_name ASC");
        return $results;
    }


    static function getDataAttribute($attr_group=0)
    {
        global $CMS, $DB;

        $data = self::getInfo_group($attr_group, "group_attribute_list");
       
        $listAttr = ltrim(rtrim($data, ","),",");
        $listAttr = explode(",", $listAttr);
        $output = "";
        $i = 1;
        foreach ($listAttr as $key => $attr_id) 
        {
            $info = self::getInfo($attr_id);
            if(!$info) {continue; }
            if($info['attr_type'] == 1)
            {
                $listvalue = self::getListValueByGroupId($attr_id);
                $output .=<<<EOF
                <div class='col-xl-6 col-md-6'>
                <fieldset class='form-group'>
                  <label class='form-label semibold'>{$info['attr_name']} </label>
                  <select class='form-control select2 attr_{$info['attr_id']}' name='attribute[{$info['attr_id']}]'>
                    <option value="">- {$info['attr_name']} -</option>
EOF;
                  //{$data['options_id']}
                        foreach ($listvalue as $data) 
                        {
                            $output .=<<<EOF
                            <option value='{$data['options_id']}'>{$data['options_name']} {$info['attr_unit']}</option>
EOF;

                        }
$output .=<<<EOF

                    </select>
                </fieldset>
              </div>
EOF;

            }else
            {
                $unit_show = $info['attr_unit'] ? "({$info['attr_unit']})" : "";
                $output .=<<<EOF
                <div class='col-xl-6 col-md-6'>
                    <fieldset class='form-group'>
                      <label class='form-label semibold'>{$info['attr_name']} {$unit_show}</label>
                      <input class='form-control' type="text" value='' name='attribute[{$info['attr_id']}]' />
                    </fieldset>
                </div>
EOF;
            }

            $i++;
        }


        return $output;
    }

    static function listtingAttribute()
    {
        global $CMS, $DB;

        $results = $DB->fetch_data("SELECT * FROM ".root_table."attribute WHERE attr_deleted=0 ORDER BY attr_id DESC", "attribute");
        $output = [];
        foreach ($results as $data) 
        {
            list($count, $data_option) = self::countOptionsAttribute($data['attr_id'], 1);

            $data['view_quantity'] = "<a class=\"view_list_options\" datajson='".json_encode($data_option, JSON_UNESCAPED_UNICODE)."' id=\"{$data['attr_id']}\" onclick=\"event.stopPropagation();openPopup('#box_list_options', viewListOptions(this, '#open_list_attribute'));\">{$count}</a>";
            $output[] = $data;
        }
        return $output;
    }


}