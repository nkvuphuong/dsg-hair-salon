<?php
namespace models;

use \core\ezy;
use \lib\date;
use \lib\security;

class abandoned
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
     * @param $cache_prefix
     *      cache_prefix for cache data
     */
    static public $cache_prefix = 'order_abandoned';

    static public $locateData = [
        'vn' => "Viet Nam",
        'us' => "USA",
    ];

    /**
     * Get list abandoned
     * @param string $sql_add
     * @param boolean $disabled_paging
     * @param boolean $disabled_convertvalue
     * @return array
     */
    static public function listing( $sql_add = "", $disabled_paging = false, $disabled_convertvalue = false, $type = 'rows' )
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("id,time,email,name");

        // Set default for Arrange
        $default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "id";
        $default_field = in_array($default_field, explode(',', self::$arrangeData)) ? $default_field : "id";

        $default_order = isset($CMS->input['by']) ? strtolower($CMS->input['by']) : "desc";
        $default_order = in_array($default_order, array('asc', 'desc')) ? $default_order : "desc";

        // SQL Condition
        $sql_add .= self::getSqlAdd($CMS->input);

        $select = $type == 'cnt' ? " COUNT(0) AS cnt " : " * ";
        $sql = "
        SELECT {$select} 
        FROM ".root_table."order_abandoned 
        WHERE deleted=0 {$sql_add} 
        ORDER BY {$default_field} {$default_order}
        ";
        
        if( $type == 'cnt' )
        {
            $results = $DB->fetch_data($sql, self::$cache_prefix);
            return isset($results[0]['cnt']) ? $results[0]['cnt'] : 0;
        }

        // Create SQL Query for listing Data
        if( $disabled_paging ) 
        {
            if( self::$maxPage > 0 )
            {
                $sql .= ' LIMIT ' . self::$maxPage;
            }

            $CMS->show_page = "";
            $results = $DB->fetch_data($sql, self::$cache_prefix);
        }
        else
        {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], self::$cache_prefix);
        }

        $output = [];

        if( is_array($results) )
        {
            if( $disabled_convertvalue )
            {
                $output = $results;
            }
            else
            {
                foreach( $results as $result ) 
                {
                    $output[] = self::convertValue($result);
                }
            }
        }

        return $output;
    }

    /**
     * Get clase query for listing
     * @param array $data
     * @param string $prefix
     * @return string
     */
    static function getSqlAdd( $data = [], $prefix='' )
    {
        global $CMS;

        $sql_add = '';

        if( isset($data['keywords']) AND $data['keywords'] !== '' )
        {
            $keywords = urldecode($data['keywords']);
            $sql_add .= " AND ({$prefix}bill_email LIKE '%{$keywords}%' OR {$prefix}bill_full_name LIKE '%{$keywords}%' OR {$prefix}bill_phone LIKE '%{$keywords}%' OR {$prefix}ship_email LIKE '%{$keywords}%' OR {$prefix}ship_full_name LIKE '%{$keywords}%' OR {$prefix}ship_phone LIKE '%{$keywords}%') ";
        }

        if( isset($data['email']) AND $data['email'] !== '' )
        {
            $email = urldecode($data['email']);
            $sql_add .= " AND ({$prefix}bill_email LIKE '%{$email}%' OR {$prefix}ship_email LIKE '%{$email}%') ";
        }

        if( isset($data['name']) AND $data['name'] !== '' )
        {
            $name = urldecode($data['name']);
            $sql_add .= " AND ({$prefix}bill_full_name LIKE '%{$name}%' OR {$prefix}ship_full_name LIKE '%{$name}%') ";
        }

        if( isset($data['ip']) AND $data['ip'] !== '' )
        {
            $ip = urldecode($data['ip']);
            $sql_add .= " AND {$prefix}ip = '{$ip}' ";
        }

        if( isset($CMS->input['total']) AND $CMS->input['total'] != '' ) 
        {
            $start_end = explode('-', urldecode($CMS->input['total']));
            $start = isset($start_end['0']) ? $start_end['0'] : 0;
            $end   = isset($start_end['1']) ? $start_end['1'] : 0;

            if( $start >= 0 )
            {
                $sql_add.=" AND {$prefix}total >='{$start}'";
            }

            if( $end >= 0 )
            {
                $sql_add.=" AND {$prefix}total <='{$end}'";
            }
        }

        if( isset($CMS->input['time']) AND $CMS->input['time'] != ''  ) 
        {
            $start_end = explode('-', urldecode($CMS->input['time']));
            $start = isset($start_end['0']) ? $start_end['0'] : 0;
            $start = is_numeric($start) ? $start : $CMS->class->date->date2time($start);
            $end   = isset($start_end['1']) ? $start_end['1'] : 0;
            $end   = is_numeric($end) ? $end : $CMS->class->date->date2time($end);

            if( $start >= 0 )
            {
                $sql_add.=" AND {$prefix}time >='{$start}'";
            }

            if( $end >= 0 )
            {
                $end  += (60*60*24)-1; // in day
                $sql_add.=" AND {$prefix}time <='{$end}'";
            }
        }

        if( isset($CMS->input['count_sent']) AND $CMS->input['count_sent'] != '' ) 
        {
            $start_end = explode('-', urldecode($CMS->input['count_sent']));
            $start = isset($start_end['0']) ? $start_end['0'] : 0;
            $end   = isset($start_end['1']) ? $start_end['1'] : 0;

            if( $start >= 0 )
            {
                $sql_add.=" AND {$prefix}count_sent >='{$start}'";
            }

            if( $end >= 0 )
            {
                $sql_add.=" AND {$prefix}count_sent <='{$end}'";
            }
        }

        if( isset($data['sent_email']) )
        {
            if( is_numeric($data['sent_email']) )
            {
                $sql_add .= " AND {$prefix}sent_email = '{$data['sent_email']}' ";
            }
        }

        if( isset($data['status']) )
        {
            if( is_numeric($data['status']) )
            {
                $sql_add .= " AND {$prefix}status = '{$data['status']}' ";
            }
        }

        return $sql_add;
    }

    /**
     * Convert original record to show
     * @param array $data
     * @return array
     */
    static  public function convertValue($data=[])
    {
        global $CMS, $DB;

        $data['time_c'] = $data['time'] ? $CMS->class->date->date_format($data['time']) : 'N/A';
        $data['next_time_c'] = $data['next_time'] ? $CMS->class->date->date_format($data['next_time']) : 'N/A';
        $data['total_c'] = $CMS->class->input->currency($data['total']);

        $data['btn_delete'] = "";
        if( security::checkPermission('abandoned', 'delete') )
        {
            $data['btn_delete'] = "<a  onclick=\"delete_confirm('{$CMS->vars['root_domain']}/?site=abandoned&act=delete&id={$data['id']}');\" class='pointer edit' class='edit' data-toggle='tooltip' data-placement='bottom' title=\"{$CMS->lang['act_delete']}\"><i class='fa fa-trash-o'></i></a>";
        }

        $data['sent_email_c'] = $CMS->lang["sent_email_{$data['sent_email']}"];
        switch( $data['sent_email'] ) 
        {
            case '1':
                $data['sent_email_label'] = "<span class=\"label label-success\">{$data['sent_email_c']}</span>";
            break;

            default:
                $data['sent_email_label'] = "<span class=\"label label-default\">{$data['sent_email_c']}</span>";
            break;
        }

        $data['status_c'] = $CMS->lang["status_{$data['status']}"];
        switch( $data['status'] ) 
        {
            case '1':
                $data['status_label'] = "<span class=\"label label-success\">{$data['status_c']}</span>";
            break;
            
            default:
                $data['status_label'] = "<span class=\"label label-default\">{$data['status_c']}</span>";
            break;
        }

        // Address bill
        $bill_city_name  = $CMS->country->nameCity($data['bill_city']);
        $data['bill_city_name']  = $bill_city_name ? $bill_city_name : $data['bill_city'];

        $bill_province_name = $CMS->country->nameState($data['bill_province']);
        $data['bill_province_name'] = $bill_province_name ? $bill_province_name : $data['bill_province'];

        $bill_country_name = $CMS->country->nameCountry($data['bill_country']);
        $data['bill_country_name'] = $bill_country_name ? $bill_country_name : $data['bill_country'];
        $bill_data_address = array(
            'address'   => $data['bill_address'], 
            'address2'  => $data['bill_address2'], 
            'city'      => is_numeric($data['bill_city']) ? $data['bill_city_name'] : $data['bill_city'], 
            'province'  => is_numeric($data['bill_province']) ? $data['bill_province_name'] : $data['bill_province'], 
            'zipcode'   => $data['bill_zipcode'], 
        );
        $data['bill_address_full'] = self::generalAddress($bill_data_address);
        $data['bill_address_full_us'] = self::generalAddress($bill_data_address, 1);

        // Address ship
        $ship_city_name  = $CMS->country->nameCity($data['ship_city']);
        $data['ship_city_name']  = $ship_city_name ? $ship_city_name : $data['ship_city'];

        $ship_province_name = $CMS->country->nameState($data['ship_province']);
        $data['ship_province_name'] = $ship_province_name ? $ship_province_name : $data['ship_province'];

        $ship_country_name = $CMS->country->nameCountry($data['ship_country']);
        $data['ship_country_name'] = $ship_country_name ? $ship_country_name : $data['ship_country'];
        $ship_data_address = array(
            'address'   => $data['ship_address'], 
            'address2'  => $data['ship_address2'], 
            'city'      => is_numeric($data['ship_city']) ? $data['ship_city_name'] : $data['ship_city'], 
            'province'  => is_numeric($data['ship_province']) ? $data['ship_province_name'] : $data['ship_province'], 
            'zipcode'   => $data['ship_zipcode'], 
        );
        $data['ship_address_full'] = self::generalAddress($ship_data_address);
        $data['ship_address_full_us'] = self::generalAddress($ship_data_address, 1);

        // Cart
        $data['cart_content_c'] = \lib\input::jsonDecode($data['cart_content']);

        // List mail
        $list_mail = str_replace(" ", "", $data['list_mail']);
        $list_mail = preg_replace('!\,+!', ',', $list_mail);
        $list_mail = explode(',', $list_mail);
        $data['list_mail_c'] = $list_mail;

        $data['record_cnt'] = self::$record_cnt;
        self::$record_cnt++;

        return $data;
    }

    static function generalAddress( $data=[], $type = 0 )
    {
        global $CMS, $DB;

        $output = '';

        if( $data['address'] )
        {
            $output .= $data['address'];
        }

        if( $type )
        {
            $output .= ' <br>';
        }

        if( $data['address2'] )
        {
            $output .= $data['address2'];
        }

        if( $type )
        {
            $output = trim($output, ' <br>') . ' <br>';
        }

        if( $data['city'] )
        {
            $output .= $data['city'];
        }

        if( $data['province'] )
        {
            $output .= ', ' . $data['province'];
        }

        if( $data['zipcode'] )
        {
            $output .= $data['province'] ? '' : ', ';
            $output .= ' ' . $data['zipcode'];
        }

        return $output;
    }

    /**
     * Option action
     * @return string
     */
    static  public function optionAction()
    {
        global $CMS, $DB;

        $output = "<option value=''>---{$CMS->lang['choose_action']}---</option>";
        if( security::checkPermission('abandoned', 'delete') )
        {
            $output .= "<option value='delete_all'>{$CMS->lang['act_delete']}</option>";
        }

        return $output;
    }

    /**
     * Delete
     * @return bool
     */
    static public function delete( $id = 0 )
    {
        global $CMS, $DB;

        if( !$id )
        {
            $id = $CMS->input['id'];
        }

        // Get info
        $data = self::getInfo($id);

        // Check existing
        if( !$data )
        {
            $_SESSION['errormsg'] .= $CMS->lang['data_not_found'] . '<br>';
            return false;
        }

        // Sql
        $sql_deleted = "
        UPDATE ".root_table."order_abandoned 
        SET deleted=1 
        WHERE id='{$data['id']}' 
        ";
        $query_deleted = $DB->query($sql_deleted);

        //Clear cache
        $CMS->class->cache->mdelete(self::$cache_prefix);

        if( $query_deleted )
        {
            // Create log
            $CMS->class->logs->key = self::$cache_prefix.'_'.$data['id'];
            $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['abandoned_deleted']} <b>#{$data['id']}:{$data['name']}</b>") . "<br>";
            return true;
        }
        else
        {
            $_SESSION["error_msg"] .= "{$CMS->lang['abandoned_delete_failed']} <b>#{$data['id']}:{$data['name']}</b><br>";
            return false;
        }
    }

    /**
     * Delete multi
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $cnt = intval($CMS->input["data_cnt"]);
        for( $i = 0; $i < $cnt; $i++ )
        {
            $id = intval($CMS->input["id_{$i}"]);
            if( $id )
            {
                if( self::delete($id) )
                {
                    $deleted = 1;
                }
            }
        }

        if ( $deleted == 0 )
        {
            $_SESSION["error_msg"] .= "{$CMS->lang['abandoned_delete_failed']}<br>";
        }

        return true;
    }

    /**
     * Get infomation
     * @param int $id
     * @return array
     */
    static public function getInfo( $id = 0, $field_name = '*', $sql_add = '' )
    {
        global $CMS, $DB;

        $output = false;

        if( !$id )
        {
            $id = $CMS->input['id'];
        }

        $sql = "
        SELECT {$field_name} 
        FROM ".root_table."order_abandoned 
        WHERE id='{$id}' AND deleted=0 {$sql_add} 
        ORDER BY id DESC 
        LIMIT 1 
        ";
        // print $sql; exit;

        $data = $DB->fetch_data($sql, self::$cache_prefix);
        $data = isset($data[0]) ? $data[0] : false;

        if ( $field_name !== '*' AND count(explode(",", $field_name)) == 1 ) 
        {
            if( isset($data[$field_name]) )
            {
                $output = $data[$field_name];
            }
        }
        else
        {
            $output = $data;
        }

        return $output;
    }
}