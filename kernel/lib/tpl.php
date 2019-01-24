<?php
namespace lib;

class tpl
{
    /**
     * nkvp
     * Get value $tpl->xxx
     * @param $key
     * @return null
     * ex:
     * $abc = isset($tpl->abc) ? $tpl->abc : null <=> $abc = \lib\tpl::get('abc');
     * $abc = isset(tpl->abc) ? $tpl->abc : 1 <=> $abc = \lib\tpl::get('abc', 1);
     */
    static function get($key, $default = null) {
        global $tpl;
        return !isset($tpl->$key) ? $default : $tpl->$key;
    }
}