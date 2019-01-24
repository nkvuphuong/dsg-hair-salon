<?php
if (!defined('IN_ROOT')) exit();

$CMS->country = new ClassCountry;

class ClassCountry
{
    public function anCountry($id = null)
    {
        if (!empty($id)) {
            global $CMS, $DB, $member;
            $DB->query("SELECT 0 FROM `" . root_table . "country` WHERE `country_id`='{$id}' OR `country_iso_code`='{$id}'");
            if ($DB->num_rows() > 0) return true;
        }
        return false;
    }

    public function country($id = 0, $type = "")
    {
        global $CMS, $DB, $member;

        if ($id == -1) return false;

        $arr = array();
        if ($id) {
            $clause = " AND (country_id = '{$id}' OR country_iso_code = '{$id}')";
        } else {
            $clause = "";
        }

        $sql = "SELECT `country_id`,`country_name`, country_iso_code FROM `" . root_table . "country` WHERE 1 {$clause}";

        $cacheData = $DB->fetch_data($sql, 'country');

        if ($cacheData) {
            foreach ($cacheData as $data) {
                if ($id) {
                    return $data['country_name'];
                } else {
                    if ($type) {
                        $arr[$data['country_iso_code']] = $data['country_name'];
                    } else {
                        $arr[$data['country_id']] = $data['country_name'];
                    }
                }
            }
        }

        return $arr;
    }

    public function city($id = null, $id_city = 0)
    {
        global $CMS, $DB, $member;

        $arr = array();
        if ($id_city) {
            if ($id_city == -1) return false;
            $clause = " AND city_id = '{$id_city}' ";
        } else {
            if ($id == -1) return false;
            $clause = "";
        }

        if ($id) {
            $id = intval($id);
            $clause .= " AND country_id={$id} ";
        }

        // if (!is_null($id))
        // {

        $sql = "SELECT `city_id`,`city_name` FROM `" . root_table . "city` WHERE 1 {$clause}";

        $cacheData = $DB->fetch_data($sql, 'city');

        if ($cacheData) {
            foreach ($cacheData as $data) {
                if ($id_city) {
                    return $data['city_name'];
                } else {
                    $arr[$data['city_id']] = $data['city_name'];
                }
            }
        }
        // }
        return $arr;
    }

    public function district($id = null, $district_id = 0, $get_type = 0) // Add param $get_type for get full name
    {
        global $CMS, $DB, $member;

        $arr = array();

        if ($district_id) {
            if ($district_id == -1) return false;
            //check cookie
            $clause = " AND district_id = '{$district_id}' ";
        } else {
            if ($id == -1) return false;
            $clause = "";
        }

        if ($id) {
            $id = intval($id);
            $clause .= " AND city_id={$id} ";
        }

        // if (!is_null($id)) {

        $sql = "SELECT `district_id`,`district_name`,`district_type` FROM `" . root_table . "district` WHERE 1=1 {$clause}";

        $cacheData = $DB->fetch_data($sql, 'district');

        if ($cacheData) {
            foreach ($cacheData as $data) {
                if ($district_id) {
                    if ($get_type == 1) {
                        $data['district_name'] = $data['district_type'] ? $data['district_type'] . " " . $data['district_name'] : $data['district_name'];
                        return $data['district_name'];
                    } else {
                        return $data['district_name'];
                    }

                } else {
                    $arr[$data['district_id']] = $data['district_name'];
                }
            }
        }
        // }
        return $arr;
    }

    public function nameCountry($id = null)
    {
        if (!is_null($id)) {
            global $CMS, $DB, $member;
            $DB->query("SELECT `country_name` FROM `" . root_table . "country` WHERE `country_id`='{$id}' OR `country_iso_code`='{$id}'");
            if ($DB->num_rows() > 0) {
                return $DB->fetch_array()['country_name'];
            }
        }
        return '';
    }

    public function nameCity($id = null)
    {
        if (!is_null($id)) {
            global $CMS, $DB, $member;
            $DB->query("SELECT `city_name` FROM `" . root_table . "city` WHERE `city_id`='{$id}'");
            if ($DB->num_rows() > 0) {
                return $DB->fetch_array()['city_name'];
            }
        }
        return;
    }

    public function nameDistrict($id = null)
    {
        if (!is_null($id)) {
            global $CMS, $DB, $member;
            $DB->query("SELECT `district_name` FROM `" . root_table . "district` WHERE `district_deleted`=0 AND `district_id`='{$id}'");
            if ($DB->num_rows() > 0) {
                return $DB->fetch_array()['district_name'];
            }
        }
        return ;
    }

    public function get_country_option($default = 0, $type = 0)
    {
        global $CMS, $DB;

        $output = "<option value>{$CMS->lang['select_country']}</option>";

        $sql = "SELECT * FROM " . root_table . "country WHERE 1 ORDER BY country_name ASC";
        $cacheData = $DB->fetch_data($sql,'country');

        if( is_array($cacheData) ) 
        {
            if( $type ) 
            {
                foreach( $cacheData as $result ) 
                {
                    $selected = (string)$default == $result['country_iso_code'] ? 'selected' : '';
                    $output .= "<option {$selected} value='{$result['country_iso_code']}'>{$result['country_name']}</option>";
                }
            }
            else
            {
                foreach( $cacheData as $result ) 
                {
                    $selected = $default == $result['country_id'] ? 'selected' : '';
                    $output .= "<option {$selected} value='{$result['country_id']}'>{$result['country_name']}</option>";
                }
            }
        }

        return $output;
    }

    public function get_state_option($default = 0, $type = 0)
    {
        global $CMS, $DB;

        $output = "<option value>{$CMS->lang['select_state']}</option>";

        $sql = "SELECT * FROM " . root_table . "state WHERE 1 ORDER BY state_name ASC";
        $cacheData = $DB->fetch_data($sql,'country');
        if( is_array($cacheData) )
        {
            if( $type ) 
            {
                foreach( $cacheData as $result )
                {
                    $selected = (string)$default == $result['state_code'] ? "selected='selected'" : '';
                    $output .= "<option value='{$result['state_code']}' id='{$result['state_id']}' {$selected}>{$result['state_name']}</option>";
                }
            }
            else
            {
                foreach( $cacheData as $result )
                {
                    $selected = $default == $result['state_id'] ? "selected='selected'" : '';
                    $output .= "<option value='{$result['state_id']}' code='{$result['state_code']}' {$selected}>{$result['state_name']}</option>";
                }
            }
        }

        return $output;
    }

    public function get_li_country($key_search = "")
    {
        global $CMS, $DB;

        $output = "";
        if ($key_search) {
            $clause = " AND country_name LIKE '%{$key_search}%' ";
        } else {
            $clause = "";
        }
        $sql = $DB->query("SELECT * FROM " . root_table . "country WHERE 1 {$clause} ORDER BY country_sort DESC, country_name ASC");
        if ($DB->num_rows($sql) > 0) {
            while ($result = $DB->fetch_array($sql)) {
                $code_flags = strtolower($result['country_iso_code']);
                $check_exist = $this->checkExistCountry($result['country_iso_code']);
                if ($check_exist) {
                    $disable = " disabled='disabled' ";
                    $style = "style = 'background: #ccc; opacity: 0.5;'";
                    $check_disable = "checkdisable='1'";
                } else {
                    $disable = "";
                    $style = "";
                    $check_disable = "checkdisable='0'";
                }

                if ($result['country_iso_code'] == "ROW") {
                    $check = "check='0'";
                } else {
                    $check = "";
                }

                $output .= "<li for='{$result['country_iso_code']}' {$style} {$disable} {$check_disable}><input type='checkbox' class='checkbox_flags' value='{$result['country_iso_code']}' {$disable} {$check} {$check_disable} class='check_country' name='list_country[]' name_country='{$result['country_name']}' style='float: left;' /><span>{$result['country_name']}</span></li>";
            }
        }

        return $output;
    }

    public function idCountry($iso_code = null)
    {
        if (!is_null($iso_code)) {
            global $CMS, $DB, $member;

            //Cache
            $sql =  "SELECT `country_id` FROM `" . root_table . "country` WHERE  `country_iso_code`='{$iso_code}'";

            $results = $DB->fetch_data($sql, 'country');
            $data = isset($results[0]) ? $results[0] : null;

            if ($data) {
                return $data['country_id'];
            }
        }
        return 0;
    }

    public function search_country($key_search = '')
    {
        global $CMS, $DB;
        $data = array();
        if ($key_search) {
            $clause = " AND country_name LIKE '%{$key_search}%' ";
        } else {
            $clause = "";
        }
        $sql = $DB->query("SELECT * FROM " . root_table . "country WHERE 1 {$clause} ORDER BY country_name ASC");
        if ($DB->num_rows($sql) > 0) {
            while ($result = $DB->fetch_array($sql)) {
                $data[] = $result['country_iso_code'];
            }
        }

        return $data;
    }

    public function checkExistCountry($country_zone = '')
    {
        global $CMS, $DB;

        $sql = $DB->query("SELECT 0 FROM " . root_table . "price_zone WHERE country_zone = '{$country_zone}'");
        if ($DB->num_rows($sql) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getOptionDistrict($city_id = 0, $default = 0, $disable_title = 0)
    {
        global $CMS, $DB;

        $output = $disable_title ? "" : "<option value>{$CMS->lang['select_district']}</option>";
        if ($city_id) {

            //Cache
            $sql = "SELECT * FROM " . root_table . "district WHERE city_id ='{$city_id}' ORDER BY district_type DESC, district_name ASC";

            $cacheData = $DB->fetch_data($sql, 'district');

            if ($cacheData) {
                foreach ($cacheData as $result) {
                    $selected = $result['district_id'] == intval($default) ? 'selected' : '';
                    $output .= "<option {$selected} value='{$result['district_id']}'>{$result['district_type']} {$result['district_name']}</option>";
                }
            }
        }

        return $output;
    }

    public function getOptionCity($country_id = 0, $default = 0, $disable_title = 0)
    {
        global $CMS, $DB;

        $output = $disable_title ? "" : "<option value>{$CMS->lang['cus_select_city']}</option>";
        if ($country_id) {

            $sql = "SELECT * FROM " . root_table . "city WHERE country_id ='{$country_id}' ORDER BY city_type ASC, city_name ASC";

            $cacheData = $DB->fetch_data($sql,'city');

            if ($cacheData) {
                foreach ($cacheData as $result) {
                    $selected = $result['city_id'] == intval($default) ? 'selected' : '';

                    $output .= "<option {$selected} value='{$result['city_id']}'>{$result['city_type']} {$result['city_name']}</option>";
                }
            }
        }

        return $output;
    }

    /////////////////////////// CODE MOI 22-05-2017 //////////////////////////////////
    //

    function getIdByNameCity($city_name = "")
    {
        global $CMS, $DB;

        if ($city_name) {
            $city_name = $CMS->class->seo->remove_vietnamese($city_name);
            $DB->query("SELECT city_id FROM " . root_table . "city WHERE city_name LIKE '%{$city_name}%' LIMIT 1");
            return $DB->fetch_array()['city_id'];
        } else {
            return false;
        }
    }

    function getIdByNameDistrict($district_name = "")
    {
        global $CMS, $DB;

        if ($district_name) {
            $district_name = $CMS->class->seo->remove_vietnamese($district_name);
            $DB->query("SELECT district_id FROM " . root_table . "district WHERE district_name LIKE '%{$district_name}%' LIMIT 1");
            return $DB->fetch_array()['district_id'];
        } else {
            return false;
        }
    }

    function getIdByNameCountry($country_name = "")
    {
        global $CMS, $DB;

        if ($country_name) {
            $country_name = $CMS->class->seo->remove_vietnamese($country_name);
            $DB->query("SELECT country_id FROM " . root_table . "district WHERE country_name LIKE '%{$country_name}%' LIMIT 1");
            return $DB->fetch_array()['country_id'];
        } else {
            return false;
        }
    }

    public function getOptionWards($district_id = 0, $default = 0)
    {
        global $CMS, $DB;

        $output = "<option value>{$CMS->lang['cus_select_town']}</option>";
        if ($district_id) {
            $sql = $DB->query("SELECT * FROM " . root_table . "town WHERE district_id ='{$district_id}' ORDER BY town_name ASC");
            if ($DB->num_rows($sql) > 0) {
                while ($result = $DB->fetch_array($sql)) {
                    $selected = $result['town_id'] == intval($default) ? 'selected' : '';

                    $output .= "<option {$selected} value='{$result['town_id']}'>{$result['town_name']}</option>";
                }
            }
        }

        return $output;
    }

    function updateIDdistrictforWards()
    {
        global $CMS, $DB;
        exit;
        $sql = $DB->query("SELECT dv_id, dv_name, cv_id FROM " . root_table . "district_vn");
        while ($result = $DB->fetch_array($sql)) {
            $name = addslashes($result['dv_name']);
            $sql2 = $DB->query("SELECT district_id, city_id FROM " . root_table . "district WHERE district_name='{$name}'");
            $result2 = $DB->fetch_array($sql2);
            // print $result2['district_id']."---".$result2['district_name']."<br/>";
            $dv_id = intval($result['dv_id']);
            $cv_id = intval($result['cv_id']);
            print "UPDATE " . root_table . "town SET district_id='{$result2['district_id']}', city_id='{$result2['city_id']}' WHERE district_id='{$dv_id}' <br/>";
            if ($dv_id and $cv_id) {
                $DB->query("UPDATE " . root_table . "town SET district_id='{$result2['district_id']}', city_id='{$result2['city_id']}' WHERE district_id='{$dv_id}'");
            }
        }

        //Clear cache
        $CMS->class->cache->mdelete('district');
        $CMS->class->cache->mdelete('town');

        exit;
    }

    /**
     * Get infomation
     * @param int $id
     * @return array
     */
    public function getInfoCountry( $id = 0, $field_name = '*' )
    {
        global $CMS, $DB;

        $output = false;

        if( $id )
        {
            $sql = "
            SELECT {$field_name} 
            FROM ".root_table."country 
            WHERE country_id = '{$id}' OR country_iso_code = '{$id}' 
            ORDER BY country_id DESC 
            LIMIT 1 
            ";
            // print $sql; exit;

            $data = $DB->fetch_data($sql, 'country');
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
        }

        return $output;
    }

    /**
     * Get infomation
     * @param int $id
     * @return array
     */
    public function getInfoState( $id = 0, $field_name = '*' )
    {
        global $CMS, $DB;

        $output = false;

        if( $id )
        {
            $sql = "
            SELECT {$field_name} 
            FROM ".root_table."state 
            WHERE state_id = '{$id}' OR state_code = '{$id}' 
            ORDER BY state_id DESC 
            LIMIT 1 
            ";
            // print $sql; exit;

            $data = $DB->fetch_data($sql, 'state');
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
        }

        return $output;
    }

    public function nameState( $id = '' )
    {
        global $CMS, $DB, $member;
        
        $output = '';
        
        if( $id ) 
        {
            $output = $this->getInfoState($id, 'state_name');
        }

        return $output;
    }
}

?>