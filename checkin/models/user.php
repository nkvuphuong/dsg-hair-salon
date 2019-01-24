<?php
/**
 * Created by PhpStorm.
 * User: PhuongNKV
 * Date: 9/26/2018
 * Time: 2:58 PM
 */

namespace CheckIn\Model;

use lib\security;

class user
{
    static function getCurrentStaff()
    {
        return security::jwtDecode($_COOKIE['accessToken']);
    }
}