<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */
namespace models;

use \core\ezy;
use \lib\date;
use lib\input;

class embed
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

    static public $locateData = [
        'vn' => "Viet Nam",
        'us' => "USA",
    ];

    /**
     * Add new embed
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        foreach($data as $k => $v)
        {
            $data[$k] = $CMS->class->editor->input($v, 'text');
        }

        $data['embed_token_key'] = $CMS->class->random->md5($CMS->class->random->character(16));
        $data['embed_time'] = $data['embed_update_time'] = time();
        $data['user_id'] = intval($member['user_id']);
        
        if($DB->insert('embed', $data))
        {
            //Clear cache
            $CMS->class->cache->mdelete('embed');

            $insertedRecord = self::insertedRecord($data['embed_token_key']); //Get inserted record

            $CMS->class->logs->key = "embed_{$insertedRecord['embed_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_embed_success']}: <strong>{$insertedRecord['embed_name']}</strong>");
            return $insertedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['added_embed_failed']}";
            return false;
        }
    }

    /**
     * Edit embed
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        foreach($data as $k => $v)
        {
            $data[$k] = $CMS->class->editor->input($v, 'text');
        }

        $oldData = self::getInfo(input::arrayValue($data, 'id'));
        if(!$oldData)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }
        $data['embed_update_time'] = time();

        if($DB->update("embed", $data, "embed_id"))
        {
            //Clear cache
            $CMS->class->cache->mdelete('embed');

            $updatedRecord = self::getInfo($CMS->input['id']);

            $CMS->class->logs->key = "embed_{$updatedRecord['embed_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_embed_success']}: <strong>{$updatedRecord['embed_name']}</strong>");

            return $updatedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['updated_embed_failed']}";
            return false;
        }
    }

    /**
     * Delete logo
     * @return bool
     */
    static public function delete()
    {
        global $CMS, $DB;

        // Get info
        $data = self::getInfo();

        // Check existing
        if ( ! $data ) { return false; }

        // Update info
        $DB->query("UPDATE ".root_table."embed SET embed_deleted=1 WHERE embed_id={$data['embed_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('embed');

        @unlink("{$CMS->vars['upload_dir']}/{$data['embed_src']}");

        // Create log
        $CMS->class->logs->key = "embed_{$data['embed_id']}";
        $_SESSION["msg"] = $CMS->class->logs->insert("{$CMS->lang['embed_deleted']} <b>".input::arrayValue($data, 'embed_name')."</b>")."<br />";

        return true;
    }

    /**
     * Delete multi embed
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] = "";

        for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
        {
            $id = intval(input::get("id_{$i}"));

            if ( $id )
            {
                $data = self::getInfo($id);

                $DB->query("UPDATE ".root_table."embed SET embed_deleted=1 WHERE embed_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['embed_src']}");

                $CMS->class->logs->key = "embed_{$data['embed_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['embed_deleted']} <b>{$data['embed_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            $_SESSION["msg"] .= "{$CMS->lang['embed_delete_failed']}";
        }

        //Clear cache
        $CMS->class->cache->mdelete('embed');

        return true;
    }

    /**
     * Get infomation of a logo
     * @param int $id
     * @return array
     */
    static public function getInfo($id = 0, $sql_add='')
    {
        global $CMS, $DB;

        if(!$id)
        {
            $id = $CMS->input['id'];
        }

        if(!$id) return false;

        if(is_numeric($id))
        {
            $id = intval($id);
            $sql_add .= " embed_id='{$id}' AND ";
        }
        else
        {
            $sql_add .= " (embed_token_key='{$id}' OR embed_code='{$id}') AND ";
        }

        $sql = "SELECT * FROM ".root_table."embed WHERE {$sql_add} embed_deleted=0 ORDER BY embed_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'embed')[0];
    }

    /**
     * Get infomation of record which just added
     * @param string $token_key
     * @return bool|array
     */
    static public function insertedRecord($token_key = "")
    {
        global $CMS, $DB;

        if(!$token_key) return false;

        $sql = "SELECT * FROM ".root_table."embed WHERE embed_token_key='{$token_key}' ORDER BY embed_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        return $DB->fetch_assoc($sql);
    }

    /**
     * Convert input data to add data form
     * @param array $data
     * @return array
     */
    static  public function addValue($data=[])
    {
        global $CMS;

        $data['embed_interval'] = intval(input::get('embed_interval',0));

        return $data;
    }

    /**
     * Convert original record to display on edit form
     * @param array $data
     * @return array
     */
    static  public function editValue($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        $data['embed_start_date'] = $data['embed_start_date'] ? date::format($data['embed_start_date']) : '';
        $data['embed_end_date'] = $data['embed_end_date'] ? date::format($data['embed_end_date']) : '';
        $data['embed_interval'] = intval($data_bk['embed_interval']);

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

        $data['embed_time'] = $data_bk['embed_time'] ? date::format($data['embed_time']) : '';
        $data['embed_update_time'] = $data_bk['embed_update_time'] ? date::format($data['embed_update_time']) : '';
        $data['embed_start_date'] = $data_bk['embed_start_date'] ? date::format($data['embed_start_date']) : '';
        $data['embed_end_date'] = $data_bk['embed_end_date'] ? date::format($data['embed_end_date']) : '';

        if($CMS->permit['embed_read'] || $CMS->permit['embed_is_root'])
        {
            $data['embed_name'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=embed&act=show&id={$data_bk['embed_id']}'>{$data_bk['embed_name']}</a>";
        }

        $data['record_cnt'] = self::$record_cnt;

        if($data_bk['user_id'])
        {
            if(!isset($CMS->vars['userTemp'][$data_bk['user_id']]))
            {
                $CMS->vars['userTemp'][$data_bk['user_id']] = $CMS->user->get_info($data_bk['user_id']);
            }
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }

        if($CMS->permit['user_read'] || $CMS->permit['user_is_root'])
        {
            $data['user_id'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['userInfo']['user_id']}'>{$data['userInfo']['user_display_name']}</a>";
        }
        else
        {
            $data['user_id'] = $data['userInfo']['user_display_name'];
        }

        $data['embed_status'] = $CMS->lang['embed_status_'.$data_bk['embed_status']];
        $data['embed_status_color']=  $CMS->lang['embed_status_color_'.$data_bk['embed_status']];

        $data['embed_css'] = htmlspecialchars_decode($data_bk['embed_css']);
        $data['embed_css'] = html_entity_decode($data['embed_css'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        $data['embed_html'] = htmlspecialchars_decode($data_bk['embed_html']);
        $data['embed_html'] = html_entity_decode($data['embed_html'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        $data['embed_js'] = htmlspecialchars_decode($data_bk['embed_js']);
        $data['embed_js'] = html_entity_decode($data['embed_js'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        self::$record_cnt++;

        return $data;
    }

    static  public function convertValueExport($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['embed_time'] = $data_bk['embed_time'] ? date::format($data['embed_time']) : '';
        $data['embed_start_date'] = $data_bk['embed_start_date'] ? date::format($data['embed_start_date']) : '';
        $data['embed_end_date'] = $data_bk['embed_end_date'] ? date::format($data['embed_end_date']) : '';
        $data['record_cnt'] = self::$record_cnt;

        if($CMS->vars['userTemp'][$data_bk['user_id']])
        {
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }
        else
        {
            $CMS->vars['userTemp'][$data_bk['user_id']] = $data['userInfo'] = $CMS->user->get_info($data_bk['user_id']);
        }

        $data['user_id'] = $data['userInfo']['user_display_name'];

        $data['cat_id'] = $data['catInfo']['cat_name'];
        $data['embed_status'] = $CMS->lang['embed_status_'.$data_bk['embed_status']];

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list embed
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing($sql_add = "", $disabled_paging = 0)
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("embed_id,embed_time,embed_update_time");

        // Set default for Arrange
        $default_field = input::get('order', 'embed_id');
        $default_order = input::get('by', 'desc');

        // SQL Condition
        $sql_add .= " embed_deleted=0 AND ";

        $CMS->input['keyword'] = input::get('keyword', input::get('term'));

        $sql_add .= self::getSqlAdd($CMS->input);

        $sql = "SELECT * FROM ".root_table."embed WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($disabled_paging == 1) {
            $CMS->show_page = "";
            return $DB->fetch_data($sql,'embed');
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'embed');

            return $results;
        }
    }

    /**
     * Check existed record
     * @param string $field
     * @param mixed $val
     * @param mixed $except
     * @return integer
     */
    static public function checkExisted($field, $val, $except=null)
    {
        global $CMS, $DB;

        $sql_add = "";
        if($except !== null)
        {
            $sql_add .= " {$field} != '{$except}' AND ";
        }

        $sql = "SELECT count(0) cnt FROM ".root_table."embed WHERE {$sql_add} {$field} = '{$val}' AND embed_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql,'embed')[0]['cnt'];
    }


    /**
     * clear domain, paging param
     * @param string $url
     * @return string
     */
    static public function clearUrl($url)
    {
        global $CMS;

        //remove domain
        $url = str_replace($CMS->vars['parent_domain'], '', $url);

        //remove paging param
        $url = preg_replace('/(\/page-[0-9]+)/','',$url);

        return $url;
    }

    static function validate($data, $oldData=[])
    {
        global $CMS;

        $return['valid'] = true;
        $return['msg'] = [];

        if(!$data['embed_name'])
        {
            $return['msg'][] = $CMS->lang['incomplete_name'];
        }

        if($data['embed_code']=='')
        {
            $return['msg'][] = $CMS->lang['incomplete_code'];
        }

        //Check unique code
        if(self::checkExisted('embed_code', input::arrayValue($data, 'embed_code'), input::arrayValue($oldData, 'embed_code')))
        {
            $return['msg'][] = $CMS->lang['code_existed'];
        }

        if($return['msg'])
        {
            $return['valid'] = false;
            $return['msg'] = implode('<br>',$return['msg']);
        }

        return $return;
    }

    public static function exportToExcel()
    {
        global $CMS, $DB, $member;

        $setTitle = [
            'No.',
            $CMS->lang['embed_name'],
            $CMS->lang['embed_code'],
            $CMS->lang['embed_type'],
            $CMS->lang['embed_start_time'],
            $CMS->lang['embed_end_time'],
            $CMS->lang['embed_status'],
            $CMS->lang['embed_time'],
        ];

        \models\report::excel_header();
        // Set style
        $style = array(
            'rotation' => 0,
            'wrap' => TRUE
        );

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'no,embed_name,embed_code,embed_type,embed_start_time,embed_end_time,embed_status,embed_time';

        $results = self::listing("", 1);

        $count_row = count($results) + 1;// + 1 row title

        // Set style excel
        \models\report::excel_title($setTitle, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z');
        $fields = explode(',', $fields);

        foreach ($results as $no => $result) {
            $result = self::convertValueExport($result);
            $result['no'] = $no + 1;
            foreach ($rangeChar as $charKey => $char) {
                /**
                 * Set values to cell by chars(A-Z) and fields from database
                 */
                $field = $fields[$charKey];
                $result[$field] = html_entity_decode($result[$field]);
                \models\report::$dataExcel->getActiveSheet()->setCellValue($char . $i, $result[$field]);
            };
            $i++;
        }

        // Set name file
        $file_name = "{$CMS->input['site']}_u{$member['user_id']}.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
    }

    static public function getAll($sql_add="")
    {
        return self::listing($sql_add, 1);
    }

    /**
     * Xuất file thống kê
     * @return string
     */
    static function exportToReport()
    {
        global $CMS, $member, $tpl;

        \models\report::excel_header();

        $startRows = 1;

        //Thống kê theo số lượng
        foreach($tpl->statsByUsers as $key => $item)
        {
            $setHeader = ['No', $CMS->lang['name'], $CMS->lang['num_of_embed']];
            $setTitle = $CMS->lang[$key];
            $fields = 'no,name,y';
            \models\report::fillData($fields, $item, $setHeader, $setTitle, $startRows);

            $startRows += count($item)+3;
        }


        //Danh sách chi tiết
        $setHeader = [
            'No.',
            $CMS->lang['embed_code'],
            $CMS->lang['embed_link_demo'],
            $CMS->lang['embed_status'],
            $CMS->lang['embed_layout_designer'],
            $CMS->lang['embed_frontend_designer'],
            $CMS->lang['embed_developer'],
            $CMS->lang['embed_time'],
        ];
        $setTitle = $CMS->lang['list'];

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'no,embed_code,embed_link_demo,embed_status,embed_layout_designer,embed_frontend_designer,embed_developer,embed_time';

        $results = self::listing('',1);

        foreach ($results as $no => $result)
        {
            $results[$no] = self::convertValueExport($result);
        }

        \models\report::fillData($fields, $results, $setHeader, $setTitle, $startRows);

        // Set name file
        $file_name = "report_embed_u{$member['user_id']}.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
    }

    /**
     * Lấy điều kiện cho truy vấn sql
     * nkvp - 2017.09.28
     */
    static function getSqlAdd($data = [], $prefix='')
    {
        global $CMS;

        $sql_add = '';

        if(isset($data['keyword']) && $data['keyword']!=='')
        {
            $keyword = urldecode($data['keyword']);

            $sql_add .= " ({$prefix}embed_name LIKE '%{$keyword}%' OR {$prefix}embed_code LIKE '%{$keyword}%') AND ";
        }

        if(isset($data['embed_status']) && $data['embed_status'] !== '')
        {
            $embed_status = intval($data['embed_status']);
            $sql_add .= " {$prefix}embed_status = {$embed_status} AND ";
        }

        if(isset($data['embed_name']) && $data['embed_name']!=='')
        {
            $embed_name = urldecode($data['embed_name']);
            $sql_add .= " {$prefix}embed_name LIKE '%{$embed_name}%' AND ";
        }

        if(isset($data['embed_code']) && $data['embed_code']!=='')
        {
            $embed_code = urldecode($data['embed_code']);
            $sql_add .= " {$prefix}embed_code='{$embed_code}' AND ";
        }

        if(isset($data['embed_start_date']) && $data['embed_start_date']!=='')
        {
            $embed_start_date = $CMS->class->date->date2time($data['embed_start_date'],1);
            $sql_add .= " {$prefix}embed_start_date>=$embed_start_date AND ";
        }

        if(isset($data['embed_start_date_to']) && $data['embed_start_date_to']!=='')
        {
            $embed_start_date_to = $CMS->class->date->date2time($data['embed_start_date_to'],1)+(3600*24);
            $sql_add .= " {$prefix}embed_start_date<$embed_start_date_to AND ";
        }

        if(isset($data['embed_end_date']) && $data['embed_end_date']!=='')
        {
            $embed_end_date = $CMS->class->date->date2time($data['embed_end_date'],1);
            $sql_add .= " {$prefix}embed_end_date>=$embed_end_date AND ";
        }

        if(isset($data['embed_end_date_to']) && $data['embed_end_date_to']!=='')
        {
            $embed_end_date_to = $CMS->class->date->date2time($data['embed_end_date_to'],1)+(3600*24);
            $sql_add .= " {$prefix}embed_end_date<$embed_end_date_to AND ";
        }

        if(isset($data['embed_time']) && $data['embed_time']!=='')
        {
            $embed_time = $CMS->class->date->date2time($data['embed_time'],1);
            $sql_add .= " {$prefix}embed_time>=$embed_time AND ";
        }

        if(isset($data['embed_time_to']) && $data['embed_time_to']!=='')
        {
            $embed_time_to = $CMS->class->date->date2time($data['embed_time_to'],1)+(3600*24);
            $sql_add .= " {$prefix}embed_time<$embed_time_to AND ";
        }

        return $sql_add;
    }
}