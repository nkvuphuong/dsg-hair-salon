<?php

namespace models;

use core\ezy;
use Firebase\JWT\JWT;
use lib\input;
use lib\page;

class staff
{
    private static $keys = [
        "user_id" => "id",
        "user_display_name" => "name",
        "user_email" => "email",
        "user_busy_from" => "busyFrom",
        "user_busy_to" => "busyTo",
    ];

    static public function getStaffs($order = "id", $by = "asc")
    {
        $keys = array_flip(self::$keys);

        $order = input::arrayValue($keys, $order, 'user_display_name');
        $by = !in_array(strtolower($by), ['asc', 'desc']) ? 'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "user WHERE user_deleted=0 AND user_status=1 ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, $cache_prefix = 'user');

        if ($results) {
            foreach ($results as $key => $result) {
                $result['user_id'] *= 1;
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

        $keys = self::$keys;

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

        return $data;
    }

    static function login($username, $password)
    {
        global $CMS;

        $lang_bk = $CMS->lang;
        $CMS->class->language->load("login", "admin_");

        if (!$username) {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->lang['incomplete_username'],
            ];
            $CMS->lang = $lang_bk;
            return $result;
        }

        if (!$password) {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->lang['incomplete_password'],
            ];
            $CMS->lang = $lang_bk;
            return $result;
        }

        $member = $CMS->user->log_check_exist($username);

        if (!$member) {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->lang['wrong_username'],
            ];
            $CMS->lang = $lang_bk;
            return $result;
        }

        if ($member['user_status'] == 0) {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->lang['locked_username'],
            ];
            $CMS->lang = $lang_bk;
            return $result;
        }

        if ($CMS->user->log_check_password(md5($password), $member["user_hash"], $member["user_salt"]) == false) {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->lang['wrong_password'],
            ];
            $CMS->lang = $lang_bk;
            return $result;
        }

        //Generate access token
        $member['user_id'] *= 1;
        $member['userg_id'] *= 1;
        $member['user_status'] *= 1;

        $result = [
            'status' => 'success',
            'msg' => 'login_success',
            'cookie' => $CMS->user->log_do_in($member) //Login ACP
        ];
        $CMS->lang = $lang_bk;
        return $result;
    }

    static function getStaff($record_id = 0, $field_return = "*", $sql_add = "")
    {
        global $DB, $CMS;

        if ($record_id and $field_return) {
            // Count field
            $countField = count(explode(",", $field_return));

            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM " . root_table . "user WHERE {$sql_add} user_deleted=0 AND user_status=1 AND (user_id='{$record_id}') LIMIT 1", 'user')[0];

            //Check return
            if ($countField == 1 and $field_return != "*") {
                return $data[$field_return];
            } else {
                return $data;
            }
        } else {
            return false;
        }
    }
}