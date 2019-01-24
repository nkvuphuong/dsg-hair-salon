<?php
/**
 * Created by SublimeText.
 * User: ThamLV
 * Date: 10/16/2017
 * Time: 2:33 PM
 */
namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;
use models\app;

class themes
{
// ********************
// *** START CLASS
// ********************

    /**
     * Get List Themes Form Folders
     * @return array
     */
    static public $themesFolders = [];
    static public $themesCustomFolders = [];

    /**
     * Get Themes info
     * @return array
     */
    static function getThemesInfo( $record_id = 0 )
    {
        global $DB;

        if ( ! $record_id ) 
        {
            return [];
        }

        // SQL Select Themes Info
        $sqlString = "
        SELECT theme_id AS id, theme_name AS name, theme_code AS code, theme_link_demo AS link_demo 
        FROM ".root_table."themes 
        WHERE ( theme_id = '{$record_id}' OR theme_token_key = '{$record_id}' OR theme_link_demo LIKE '%://{$record_id}' OR theme_link_demo LIKE '%://{$record_id}/' ) AND theme_deleted = 0 AND theme_status = 2 AND theme_display = 1 
        ORDER BY theme_id ASC 
        LIMIT 0,1 
        ";

        $sqlQuery = $DB->query($sqlString);

        // Get data
        $output = $DB->fetch_array($sqlQuery);

        return $output;
    }

    /**
     * Get List id format 1, 3, 5 ...
     * @return string id not duplicate 
     */
    static function getCategoryIdList( $codes = "" )
    {
        global $CMS;

        // Get Id List
        $idList = [];

        // split codes
        $codes = explode(',', $codes);
        foreach ( $codes as $code ) 
        {
            $code = trim($code);
            if ( $code )
            {
                $themesCategory = self::getCategoryInfo($code);
                $id = $themesCategory['id'] ? $themesCategory['id'] : -1;

                $idList[$id] = $id;
            }
        }
        
        // only get values to new array
        $idList = array_values($idList);
        $idList = implode(',', $idList);
        $idList = $idList ? $idList : '-1';

        return $idList;
    }

    /**
     * Get List code format abc, def, igh ...
     * @return string code not duplicate 
     */
    static function getCategoryCodeList( $stringKey = "" )
    {
        global $CMS, $tpl;

        // Get Code List
        $codeList = [];

        // Custom array code list
        // Get Code List Data
        $codeListData = self::getCategoryCodeListData();
        
        // only get values to new array
        $codeList = $codeListData[$stringKey];
        $codeList = array_values($codeList);
        $codeList = implode(',', $codeList);
        $codeList = $codeList ? $codeList : '-1';

        // Use for debug load themes
        $tpl->themesKeyForGetCodeList = isset($tpl->themesKeyForGetCodeList) ? $tpl->themesKeyForGetCodeList : [];
        $tpl->themesKeyForGetCodeList[] = $stringKey;

        return $codeList;
    }
    
    /**
     * Get Category Info
     * @return array
     */
    static function getCategoryInfo( $record_id = 0 )
    {
        global $DB;

        if ( ! $record_id ) 
        {
            return [];
        }

        // SQL Select Themes Category Info
        $sqlString = "
        SELECT cat_id AS id, cat_name AS name, cat_code AS code 
        FROM ".root_table."theme_cats 
        WHERE ( cat_id = '{$record_id}' OR cat_token_key = '{$record_id}' OR cat_code = '{$record_id}' ) AND cat_status = 1 AND cat_deleted  = 0 
        ORDER BY cat_id ASC 
        LIMIT 0,1 
        ";

        $sqlQuery = $DB->query($sqlString);

        // Get data
        $output = $DB->fetch_array($sqlQuery);

        return $output;
    }

    /**
     * Get List Themes By Category
     * @return array
     */
    static function getThemesByCategory( $record_id = "", $limit = 12,  $paging = false , $sort = 0 )
    {
        global $DB;

        // List Id
        $temp_record_id = '';
        $record_id = explode(',', $record_id);
        foreach( $record_id as $id ) 
        {
            $id = trim($id);
            if ( $id )
            {
                $temp_record_id .= $temp_record_id ? " OR cat_id LIKE '%\"{$id}\"%'": "cat_id LIKE '%\"{$id}\"%'";
            }
        }
        $record_id = $temp_record_id ? "( {$temp_record_id} ) AND" : "";

        // Sort 1: Latest, 2: Used more, default 0: Name ASC
        $sort = $sort == 1 ? 'theme_id DESC'  : ( $sort == 2 ? 'theme_num_sites DESC' : 'theme_name ASC');

        // Sql get theme and paging
        $sqlString = "
        SELECT theme_id AS id, theme_name AS name, theme_code AS code, theme_link_demo AS link_demo, theme_custom AS custom, theme_preview_code AS preview_code  
        FROM ".root_table."themes 
        WHERE {$record_id} theme_deleted = 0 AND theme_status = 2 AND theme_display = 1 
        ORDER BY {$sort} 
        ";

        $results = page::init($sqlString, $limit, $paging,'appweb.themes');

        $output = [];

        if($results)
        {
            foreach ( $results as $data )
            {
                $data = self::convertThemes($data);

                // check exit image of themes
                if ( !empty( $data['gallery'] ) )
                {
                    $output[] = $data;
                }
            }
        }

        return $output;
    }

    /**
     * Convert Data Of Themes
     * @return mixed array
     */
    static private function convertThemes( $data = [] )
    {
        global $CMS;

        // Subtr code
        // preg_match('/[0-9]/', $data['code'], $matches, PREG_OFFSET_CAPTURE); // first position of numbers
        // $data['short_code'] = substr($data['code'], intval($matches[0][1]), strlen($data['code']));

        $data['link_demo'] = strtolower($data['link_demo']); 
        $data['link_demo'] = trim($data['link_demo']);
        $data['link_demo_short'] = str_replace(array('http://', 'https://'), '', $data['link_demo']); // Remove http:// and https://
        $data['link_demo_short'] = '/kho-giao-dien-thiet-ke-moi/xem-giao-dien/' . $data['link_demo_short'];

        // detect webaz demo to redirect url directy for view demo: ex: http://electronic03.webaz.com.vn
        if ( preg_match('/webaz.com.vn/', $data['link_demo_short']) ) 
        {
            $data['link_demo_short'] = $data['link_demo'];
        }

        // Load image preview
        $data['gallery'] = self::getImageOfThemes($data['custom'] ? $data['preview_code'] : $data['code'], $data['custom']);

        return $data;
    }

    /*
    * Load themes image preview from folder themes
    * @return array
    */
    static public function getImageOfThemes( $code = "", $custom = 0 ) 
    {
        global $CMS, $tpl;

        $output = array();

        // get data Themes Folders
        if ( empty( self::$themesFolders ) ) 
        {
            $dirs = array_filter(glob(root_path.'/themes/*'), 'is_dir');

            foreach ( $dirs as $dir ) 
            {
                $path_img_preview = $dir . '/preview-web4s.jpg';
                $path_img_mobile = $dir . '/preview-web4s-mobile.png';

                // Check exits file image preview
                if( file_exists($path_img_preview) AND file_exists($path_img_mobile) ) 
                {
                    // Load name and image preview
                    $template = str_replace(root_path.'/themes/', '', $dir);
                    $url_img_preview = $CMS->vars['root_domain'] . '/themes/' . $template . '/preview-web4s.jpg';
                    $url_img_mobile = $CMS->vars['root_domain'] . '/themes/' . $template . '/preview-web4s-mobile.png';

                    // Rule: theme code => data image
                    self::$themesFolders[$template] = array(
                        'url_img_preview' => $url_img_preview,
                        'url_img_mobile' => $url_img_mobile,
                    );
                }
            }
        }

        // get data Themes Custom Folders
        if ( empty( self::$themesCustomFolders ) ) 
        {
            $data = [];
            $files = array_filter(glob(root_path.'/public/themes/*.*'), 'is_file');

            foreach ( $files as $file ) 
            {
                // Load name
                $file = str_replace(root_path.'/public/themes/', '', $file);

                // Load Image
                if ( substr($file, -18) == '-preview-web4s.jpg' ) 
                {
                    $template = substr_replace($file ,"",-18);
                    $data[$template]['url_img_preview'] = $CMS->vars['root_domain'] . '/public/themes/' . $file;
                }
                else if ( substr($file, -25) == '-preview-web4s-mobile.png' ) 
                {
                    $template = substr_replace($file ,"",-25);
                    $data[$template]['url_img_mobile'] = $CMS->vars['root_domain'] . '/public/themes/' . $file;
                }

                // check image and image mobile
                // Rule: theme code => data image
                if ( $data[$template]['url_img_preview'] AND $data[$template]['url_img_mobile'] ) 
                {
                   self::$themesCustomFolders[$template] = $data[$template];
                }
            }
        }

        // Set output
        $output = $custom ? self::$themesCustomFolders[$code] : self::$themesFolders[$code];

        return $output;
    }

    /*
    * Custom code list format key => array list code
    * @return array
    */
    static public function getCategoryCodeListData() 
    {
        global $CMS, $tpl; 

        // Custom array code list
        // Get Code List Data
        $codeListData = [];

        // Bán hàng
        $codeListData['ban-hang'] = array("MER", "FAS");
        
        // Bất động sản
        $codeListData['bat-dong-san'] = array("REA");

        // Doanh nghiệp
        $codeListData['doanh-nghiep'] = array("WCO");

        // Du lịch
        $codeListData['du-lich'] = array("TRA");

        // Mỹ phẩm
        $codeListData['my-pham'] = array("COSMETIC");

        // Thời trang
        $codeListData['thoi-trang'] = array("FAS");

        // Điện tử điện máy
        $codeListData['dien-tu-dien-may'] = array("MER");

        // Làm đẹp
        $codeListData['lam-dep'] = array("HAI");

        // Nhà hàng
        $codeListData['nha-hang'] = array("WRE");

        return $codeListData;
    }
// ********************
// *** END CLASS
// ********************
}