<?php
namespace controller;

use core\ezy;
use Firebase\JWT\JWT;
use lib\input;

class staff
{
    static function get_staffs()
    {
        global $CMS;

        $staffs = \models\staff::getStaffs(input::get('order'), input::get('by'));
        input::jsonEncode($staffs);
    }

    static function login() {
        global $CMS;
        $username = urldecode(input::get('username'));
        $password = urldecode(input::get('password'));
        $result = \models\staff::login($username, $password);
        input::jsonEncode($result);
    }

    static function update_store() {
        global $CMS;

        $return = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = file_get_contents("php://input");
            $data = input::jsonDecode($data);
            $userId = $data['userId'] ? $data['userId'] : $data['id'];
            if(\models\staff::updateStore($userId, $data['storeId'])) {

                $staff = \models\staff::getStaff($userId);
                $staff = \models\staff::convertKeys($staff);
                $accessToken = JWT::encode(['staff' => $staff], ezy::$secret_key);

                $return = [
                    'accessToken' => $accessToken,
                    'status' => 'success',
                    'msg' => "Cập nhật thành công"
                ];
            } else {
                $return = [
                    'status' => 'fail',
                    'msg' => "Cập nhật thất bại"
                ];
            }
        }

        input::jsonEncode($return);
    }
}