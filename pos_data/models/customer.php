<?php

namespace models;

use core\ezy;
use lib\image;
use lib\input;
use lib\page;

ezy::load_model("city");
ezy::load_model("district");

class customer
{
    private static $keys = [
        "cus_id" => "id",
        "cus_code" => "code",
        "cus_full_name" => "name",
    ];

    static public function keys2Get($keys){
        $expand = [
            'cus_email' => 'email',
            'cus_birthday' => 'birthday',
            'cus_phone' => 'phone',
            'cus_address' => 'address',
            'cus_city' => 'city',
            'cus_district' => 'district',
            'cus_tax_code' => 'taxcode',
            'cus_note' => 'note',
            'cus_image' => 'avatarStr',
            'cus_sex' => 'gender',
            'cus_group' => 'groupIds'
        ];

        $keys = array_merge($keys, $expand);

        return $keys;
    }

    static public function keys2Set($keys){

        $expand = [
            'cus_email' => 'email',
            'cus_birthday' => 'birthday',
            'cus_phone' => 'phone',
            'cus_address' => 'address',
            'cus_city' => 'city',
            'cus_district' => 'district',
            'cus_tax_code' => 'taxcode',
            'cus_note' => 'note',
            'cus_sex' => 'gender',
        ];

        $keys = array_merge($keys, $expand);

        return $keys;
    }

    static public function getCustomers($order="id", $by="asc")
    {
        global $CMS;

        $keys = array_flip(self::$keys);

        $order = input::arrayValue($keys, $order, 'cus_full_name');
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql_add = self::getSqlAdd($CMS->input);

        $sql = "SELECT * FROM " . root_table . "customer WHERE {$sql_add} cus_deleted=0 ORDER BY {$order} {$by}";
        $results = page::init($sql, $CMS->input['limit'] ? $CMS->input['limit']*1 : 10, true, $cache_prefix = 'customer');

        if ($results) {
            foreach ($results as $key => $result) {
                $result['cus_id'] *= 1;
                $result['cus_sex'] *= 1;
                $results[$key] = self::convertToDisplay($result);
            }
        }
        return $results;
    }

    /**
     * convert key data for api
     * @param $result
     */
    static public function convertKeys($data = [])
    {
        $return = [];

        $keys = self::keys2Get(self::$keys);

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    static public function revertKeys($keys, $data = [])
    {
        $return = [];

        $keys = array_flip($keys);

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    static function convertToDisplay($data = [])
    {
        $data = self::convertKeys($data);

        $oriData = $data;
        $data['oriData'] = $oriData;

        $data['city'] = ($cityId = $data['city']*1) ? city::getCity($cityId) : null;
        $data['district'] = ($districtId = $data['district']*1) ? district::getDistrict($districtId) : null;
        $data['avatarStr'] = input::checkImage("customer/".$data['avatarStr'],"assets/images/avartar.png");
        $data['birthday'] = [
            'year' => date("Y", $data['birthday']*1)*1,
            'month' => date("m", $data['birthday']*1)*1,
            'day' => date("d", $data['birthday']*1)*1,
        ];
        $data['gender'] *= 1;

        $data['birthday'] = input::jsonEncode($data['birthday'], 0);

        $data['groupIds'] = input::jsonDecode($data['groupIds']);
        $data['groupIds'] = is_array($data['groupIds']) ? $data['groupIds'] : [];
        $data['groupIds'] = array_values($data['groupIds']);
        if($data['groupIds']) {
            foreach ($data['groupIds'] as $k => $v) {
                $data['groupIds'][$k] = $v * 1;
            }
        }

        return $data;
    }

    /**
     * Get infomation
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
            $sql_add .= " cus_id='{$id}' AND ";
        }
        else
        {
            $sql_add .= " (cus_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."customer WHERE {$sql_add} cus_deleted=0 ORDER BY cus_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'customer')[0];
    }

    static public function getInfoByPhone($phoneNumber = '', $sql_add='')
    {
        global $CMS, $DB;
        $phoneNumber = trim($phoneNumber);
        if(!$phoneNumber)
        {
            $phoneNumber = $CMS->input['phone'];
        }
        if(!$phoneNumber) return false;
        $sql = "SELECT * FROM ".root_table."customer WHERE {$sql_add} cus_phone='{$phoneNumber}' AND cus_deleted=0 ORDER BY cus_id DESC LIMIT 0,1";
        return $DB->fetch_data($sql, 'customer')[0];
    }

    static function getSqlAdd($data = [], $prefix='')
    {
        global $CMS;

        $sql_add = '';

        foreach ($data as $k => $v) {
            $data[$k] = urldecode($v);
        }

        if(isset($data['term']) && $data['term']!=='')
        {
            $keyword = urldecode($data['term']);
            $sql_add .= " ({$prefix}cus_full_name LIKE '%{$keyword}%' OR {$prefix}cus_code LIKE '%{$keyword}%' OR {$prefix}cus_phone LIKE '%{$keyword}%' OR {$prefix}cus_email LIKE '%{$keyword}%') AND ";
        }

        return $sql_add;
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

        $sql = "SELECT * FROM ".root_table."customer WHERE cus_token_key='{$token_key}' ORDER BY cus_id DESC LIMIT 1";

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
        global $CMS, $member;

        $data = self::revertKeys(self::keys2Set(self::$keys), $data);
        $data['cus_token_key'] = $CMS->class->random->md5(time().'_'.$CMS->class->random->character(16));
        $data['cus_time'] = $data['cus_updated_time'] = time();
        $data['user_id'] = intval($member['user_id']);
        $data['data_bk'] = $data_bk = $data;
        return $data;
    }

    /**
     * Add new customer
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $DB;

        unset($data['cus_id']);

        if($DB->insert('customer', $data))
        {
            //Clear cache
            $CMS->class->cache->mdelete('customer');

            $insertedRecord = self::insertedRecord($data['cus_token_key']); //Get inserted record

            $CMS->class->logs->key = "customer_{$insertedRecord['cus_id']}";

            $result = [
                'status' => 'success',
                'msg' => $CMS->class->logs->insert("Đã thêm mới khách hàng: {$insertedRecord['cus_full_name']}"),
            ];

            //Update code
            $cus_code = "CUS{$insertedRecord['cus_id']}";
            //Update avatar
            $cus_image = image::uploadFile(input::arrayValue($_FILES, 'files'),'customer');
            $updateCodeResult =  self::edit(['cus_id' => $insertedRecord['cus_id'], 'cus_code' => $cus_code, 'cus_image' => $cus_image]);

            if($updateCodeResult['status'] == 'success') {
                $result['data'] = $updateCodeResult['data'];
            } else {
                $result['data'] = self::convertToDisplay($insertedRecord);
            }
        }
        else
        {
            $result = [
                'status' => 'failed',
                'msg' => "Có lỗi xảy ra. Không thêm được khách hàng mới",
                'data' => null,
            ];
        }

        return $result;
    }

    /**
     * Convert original record to display on edit form
     * @param array $data
     * @return array
     */
    static  public function editValue($data=[])
    {
        global $CMS, $member;
        $data = self::revertKeys(self::keys2Set(self::$keys), $data);
        $data['data_bk'] = $data_bk = $data;
        return $data;
    }

    /**
     * Edit rating
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $result = ['status' => 'failed'];

        $oldData = self::getInfo($data['cus_id']);
        if(!$oldData)
        {
            $result['msg'] = $CMS->lang['data_not_found'];
            return $result;
        }

        //Update avatar
        if(!empty($_FILES['files'])) {
            $data['cus_image'] = image::uploadFile($_FILES['files'],'customer', $oldData['cus_image']);
        }


        if($DB->update("customer", $data, "cus_id"))
        {
            //Clear cache
            $CMS->class->cache->mdelete('customer');

            $updatedRecord = self::getInfo($data['cus_id']);

            $CMS->class->logs->key = "customer_{$updatedRecord['cus_id']}";
            $result['status'] = 'success';
            $result['msg'] = $CMS->class->logs->insert("Đã cập nhật thông tin khách hàng: {$updatedRecord['cus_full_name']}");
            $result['data'] = self::convertToDisplay($updatedRecord);
            return $result;
        }
        else
        {
            $result['msg'] = "{$CMS->lang['updated_rating_failed']}";
            return $result;
        }
    }
}