<?php
/**
 * Created by PhpStorm.
 * User: lyhuuloi
 * Date: 3/12/2017
 * Time: 10:16 PM
 */

namespace core;

use \core\app;
use \lib\db_system;
use \lib\input;
use \lib\db;
use \lib\assets;
use \lib\cookie;
use \lib\cache;
use lib\security;
use \views\layouts;

class ezy
{
    //static public $CMS = "";
    static public $html = "";
    static public $ip_address;
    static public $user_agent;
    static public $time_now = 0;
    static public $time_out = 0;
    //static public $yield; // Main HTML

    // Root application name
    static public $web_name = "web"; // Root web's folder
    static public $web_views = "views/";
    static public $web_assets = "themes/";
    static public $web_theme = "";
    static public $theme_key = ""; // LHL-08082017 Theme key, using for product/service to build custom form

    // Layouts
    static public $title = ""; // Page title

    // Application directory: / /acp
    static public $app_dir = "app"; // Real directory, Default /app/
    static public $mod_dir = "";
    static public $upload_dir = "uploads"; // Uploads + self::$user_dir
    static public $user_dir = ""; // The name loaded from config.route.php
    static public $asset_dir = ""; // Force assets dir
    static public $markas_new = false;

    // Home
    static public $site_default; // Default module to load first
    static public $site; // Old name is $CMS->input['site']
    static public $act; // Old name is $CMS->input['act']
    static public $subact; // Old name is $CMS->input['subact']

    static public $no_layouts  = false; // if site404 load tpl404_error no header and footer theme
    static public $seo = []; // New var for SEO
    static public $vars = []; // Old name is $CMS->vars
    static public $input = []; // Old name is $CMS->input
    static public $routes = []; // Old name is $menu in index.php
    static public $seo_routes = []; // New var for SEO, ex: p => product

    // GLobal Path
    static public $layouts = "/app/views/layouts"; // Layouts path

    // Mascellaneous
    static public $google_recaptcha_defined = false; // Google Recaptcha
    static public $page = []; // Use for paging

    //API | POS | BOOKING
    static public $secret_key = "ZbioRz1oub"; // Use for check security API (POS)
    static public $api_auth = false; // Use for check security API (POS)

    // Store backend type
    static public $store_backend_type = array("shopify", "woocommerce"); // Use for store

    /**
     * Init autoload
     */

    public function __construct()
    {
        global $DB, $DBS, $info;

        // Init DB (Default: MySQL)
        $DB = new db($info);

        // Prevent load if it's just a web
        if ( defined("is_web") == false )
        {
            $info_system = $info;
            $info_system['db_name'] = root_db;
            $DBS = new db($info_system);
        }

        // Init main libs
        self::init_db();
        self::init_global();

        // Init Assets, overwrite default setting by config.inc.php
        assets::$environment = $info['ezy_env'];
    }

    /**
     * Init Databsae
     */

    static public function init_db()
    {
        global $CMS, $DB;

        // Remove database info
        unset($CMS->vars['db_host']);
        unset($CMS->vars['db_port']);
        unset($CMS->vars['db_name']);
        unset($CMS->vars['db_username']);
        unset($CMS->vars['db_password']);

        // Start check
        if ( $CMS->vars['is_cachesql'] == 1 AND $CMS->class->cache->checksql("config") == true )
        {
            $CMS->vars = array_merge($CMS->vars, unserialize($CMS->class->cache->loadsql("config")));
        }
        else if ( $CMS->class->cache->check(md5("config")) == true )
        {
            $CMS->vars = array_merge($CMS->vars, unserialize($CMS->class->cache->load(md5("config"))));
        }
        else
        {
            $conf_sql = $DB->query("SELECT C.* FROM ".root_table."conf_settings AS C, ".root_table."conf_settings_titles AS CT WHERE CT.conf_id=C.conf_group"); // WHERE CT.conf_key='general'

            if ( $DB->num_rows( $conf_sql ) )
            {
                $conf = [];

                while ( $config = $DB->fetch_array( $conf_sql ) )
                {
                    $conf[$config['conf_key']] = $config['conf_value'];

                    // Generate array data for Select Type
                    if ( $config['conf_type'] == "select" )
                    {
                        $cnt = 0;
                        $select_data = explode("<br />", $config['conf_data']);
                        $select_array = [];
                        $select_html = "";

                        for ( $i = 0; $i < count($select_data); $i++ )
                        {
                            if ( $select_data[$i] )
                            {
                                $select_object = explode("|", $select_data[$i] );

                                if ( $select_object[1] AND $select_object[0] )
                                {
                                    $select_array[$cnt] = $select_object[1];
                                    $select_array[$select_object[1]] = $select_object[0];
                                    $select_html .= "<option value={$select_object[1]}>{$select_object[0]}</option>\n";
                                    $cnt++;
                                }
                            }
                        }

                        $conf[$config['conf_key']."_array"] = serialize($select_array);
                        $conf[$config['conf_key']."_data"] = $select_array;
                        $conf[$config['conf_key']."_html"] = $select_html;
                    }
                }
            }

            // Save cache
            if ( $CMS->vars['is_cachesql'] == 1 )
            {
                $CMS->class->cache->savesql("config", serialize($conf));
            }
            else
            {
                $CMS->class->cache->save(md5("config"), serialize($conf));
            }

            // Merge CMS vars
            $CMS->vars = array_merge($CMS->vars, $conf);
        }

        $CMS->vars['translations'] = @json_decode($CMS->vars['translations'], true);
        //Nếu chỉ chọn 1 ngôn ngữ thì coi như k kích hoạt đa ngôn ngữ
        if(count($CMS->vars['translations'])<=1)
        {
            $CMS->vars['translations'] = [];
        }
    }

    /**
     * Init global headers
     */

    static public function init_global()
    {
        global $CMS, $DB;

        $CMS->input = input::parse_incoming();

        // Root path
        $filename = explode("/", $_SERVER['SCRIPT_NAME']);
        $request = str_replace("/".$filename[count($filename)-1], "", $_SERVER['SCRIPT_NAME']);

        // Set cookie
        cookie::$path = $request;

        $CMS->vars['img_url'] = "{$CMS->vars['root_domain']}/templates/images";
        $CMS->vars['js_url'] = "{$CMS->vars['root_domain']}/templates/javascript";
        $CMS->vars['css_url'] = "{$CMS->vars['root_domain']}/templates/css";
        $CMS->vars['public_url'] = "{$CMS->vars['root_domain']}/public";
        $CMS->vars['tool_url'] = "{$CMS->vars['root_domain']}/tools";
        $CMS->vars['phone_format'] = isset($CMS->vars['phone_format']) ? $CMS->vars['phone_format'] : "(000) 000-0000";

        // User input
        $CMS->input["id"] = isset($CMS->input["id"]) ? intval($CMS->input["id"]) : 0;

        // Check mobile
        $_SESSION['is_mobile'] = intval($CMS->class->mobile_detect->isMobile());
        // Check tablet
        $_SESSION['is_tablet'] = intval($CMS->class->mobile_detect->isTablet());

        $CMS->vars['request_url'] = isset($_SERVER['REQUEST_URI']) ? $CMS->class->filter->clean_value($_SERVER['REQUEST_URI']) : "";
        $CMS->vars['http_referer'] = isset($_SERVER['HTTP_REFERER']) ? $CMS->class->filter->clean_value($_SERVER['HTTP_REFERER']) : "";

        // User info
        $CMS->ip_address = self::$ip_address = $CMS->class->filter->ip_cleaner( $_SERVER['REMOTE_ADDR'] );
        self::$user_agent = $CMS->class->filter->clean_value( $_SERVER['HTTP_USER_AGENT']);

        // Set upload dir
        self::$upload_dir = self::$upload_dir."/".self::$user_dir;

        // Set cache dir
        cache::$default_dir = root_path.self::$upload_dir."/cache";

        // Init upload dir
        $CMS->vars['upload_url'] = $CMS->vars['root_domain'] . "/" . self::$upload_dir ;
        $CMS->vars['upload_dir'] = root_path.self::$upload_dir;

        // Update assets info
        //assets::setBase("/".self::$app_dir,root_path, "/public/assets/acp/"); // "/".self::$upload_dir."/assets/"

        //Load language
        $CMS->class->language->auto_run();
        $CMS->class->date->auto_run();

        // Migration before run
        if ( isset($DB) ){
            // DB Migration first
            include_once root_path."db/migrate.php";

            // PHP Migration after if DB migration successful
            if ( \lib\migration::$result == true )
            {
                include_once root_path."db/migratephp.php";
            }
        }

        return true;
    }

    /**
     * ACP: Load URL
     */

    static public function load_url( $is_array = 0 )
    {
        global $CMS;

        $url = explode("&", $_SERVER["REQUEST_URI"]);

        if ( $is_array == 0 )
        {
            for ( $i = 1; $i < count($url); $i++ )
            {
                $url2 = explode( "=", $url[$i] );
                $CMS->input[$url2[0]] = $url2[1];
            }
        }
        else
        {
            $array = parse_url($url);

            return $array;
        }

        return true;
    }

    /**
     * Init index, use for frontend.
     */

    static public function init_index()
    {
        global $CMS, $tpl;

        // Check location
        $CMS->input['location'] = "all";

        // Load session
        $CMS->class->session->auto_run();

        // Routes
        $path = explode("/", strtolower($_SERVER['REQUEST_URI']));

        // Check for the first level of path
        $cnt = $path[1] == ezy::$app_dir ? 2 : 1;

        // Compile path
        $site = $path[$cnt++];

        $site = $site == 'sitemap.xml' ? 'sitemap' : $site;

        // nhathh, xu ly link tu zalo qua ?dutm_source=zalo&utm_medium=zalo&utm_campaign=zalo&zarsrc=30
        $site =  explode('?',$site)[0];

        $act = isset($path[$cnt]) ? input::route_get_id($path[$cnt++]) : ""; // Get ID for path level 2
        $subact = isset($path[$cnt]) ? input::route_get_id($path[$cnt++]) : ""; // Get ID for path level 3

        // Init SEO
        $seo = self::init_seo($site);

        $error_404 = false; // Display error 404

        // If the url match with SEO format (suffix: domain.com/xxxx-pyyyy.html)
        if ( $seo != false  AND   self::$seo_routes[$seo['module_name']] )
        {
            $seo_detail = self::$seo_routes[$seo['module_name']];

            // If we found a key of controller name in self::$seo_routes
            if ( !empty($seo_detail[0]) )
            {
                self::$site = isset($seo_detail[0]) ? $seo_detail[0] : self::$site_default;
                self::$act = $seo_detail[1];
                self::$subact = $seo['module_id'];
            }
            else
            {
                // Display error 404
                $error_404 = true;
            }
        }

        // Normal Vars
        if ( empty(self::$site) ) {
            self::$site = isset(self::$routes[$site]) ? $site : self::$site_default;
            self::$act = isset($act) ? $act : "";
            self::$subact = isset($subact) ? $subact : "";

            // Check error 404
            $error_404 = !empty($site) ? (!isset(self::$routes[$site]) ? true : false) : false;
        }

        // Get param url
        $path_length = count($path);
        for ( $i = $cnt; $i <= $path_length; $i++ ) {
            isset($path[$i]) ? input::route_get_id($path[$i]) : "";
        }

        // Site status (on/off)
        if( isset($CMS->vars['sitestatus']) && $CMS->vars['sitestatus'] == 0 )
        {
            echo self::load_layout("error_maintenance", "app");
            exit;
        }

        // Load default model for web
        //\models\app::getHtml();
        if ( ezy::$app_dir == self::$web_name )
        {
            self::load_model("application");
            \models\app::auto_run();
        }

        // Assets
        if ( self::$app_dir == "web" ){
            assets::setBase("/themes/".ezy::$web_theme."/assets",root_path, "/themes/".ezy::$web_theme."/assets/");
        }
        //assets::setBase("",root_path, "/acp/assets/");

        // Default SEO
        self::$seo['description'] = $CMS->vars['seo_description']; // Meta Description
        self::$seo['keywords'] = $CMS->vars['seo_keyword']; // Meta Keywords
        self::$seo['author'] = $CMS->vars['seo_author']; // Meta Author
        self::$seo['title'] = $CMS->vars['website_title']; // Default <title> </title> tag

        // Display error 404
        if( $error_404 == true )
        {
            if(self::checkFile404())
            {
                echo self::html("tpl.error_404", "layouts");
            }else
            {
                echo self::html();
            }

            return true;
        }

        // Load model
        self::load_model(self::$routes[self::$site]);

        // Load controller
        require_once root_path.self::$app_dir. "/controllers/" . self::$routes[self::$site] .".php";

        // Generate module name based on $site
        $modulename = '\controller\\'.self::$routes[self::$site];

        // Return html
        $output = $modulename::auto_run();

        print $output;
    }

    /**
     * Init SEO
     * Syntax: domain.com/productname-p11.html
     * productname: Product Name
     * p = key of Product's controller
     * 11 = Example of Product ID
     */

    static private function init_seo($site)
    {
        // Convert all to lower
        $site = strtolower($site);

        // Detect if it's a SEO link
        if ( substr($site,-4) == "html")
        {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            exit();

        }else
        {
            $items = explode("-", $site);
            list($module, $id) = sscanf(end($items), "%[a-zA-Z]%[0-9]");

            return array("module_name" => $module, "module_id" => $id);
        }

        // return false;
    }

    /**
     * Init ACP (Admin Control Panel)
     */

    static public function init_acp()
    {
        global $CMS, $tpl, $DB, $member;

        $tpl->maskFormat = preg_replace('/[^\/]/','0',$CMS->vars['date_format']) ;
        $tpl->maskPlaceHolder = preg_replace('/[^\/]/','_',$CMS->vars['date_format']) ;

        $CMS->output = "";

        // Load default language
        $CMS->class->language->load("menu");
        $CMS->class->language->load("global");

        // Load global template
        self::$html = $CMS->class->template->load_simple(self::$app_dir."/views/layouts/acp_global.php");
        $CMS->global = self::$html;

        // Check security
        if ( $CMS->class->security->check_whitelist() == false )
        {
            echo self::error_maintenance("403: Forbidden");
            exit();
        }

        // Assets
        //!empty(self::$asset_dir) ? self::$asset_dir : "/".self::$app_dir
        assets::setBase(!empty(self::$asset_dir) ? self::$asset_dir : "/".self::$app_dir,root_path, "/acp/assets/");

        // Load session
        $CMS->class->session->auto_run();

        // Re-load URL to get the value of varibles in the GET METHOD
        ezy::load_url();

        // User Permissionv
        if ( $member['user_permission'] )
        {
            $CMS->permit = $member['user_permission'];
        }


        // Location
        if ( isset($CMS->input['reset_location']) )
        {
            $_SESSION['location'] = $CMS->input['reset_location'];
            $CMS->global->redirect(str_replace("&amp;", "&", $CMS->class->filter->clean_value($_SERVER['HTTP_REFERER'])));
        }

        $CMS->input['location'] = isset($_SESSION['location']) ? $_SESSION['location'] : $member['user_location'];

        // Load addition data
        $CMS->class->search->auto_run();

        // Display
        $data = "";
        $data .= "<option value=''>{$CMS->lang['select_display']}</option>";
        $data .= "<option value='0'>{$CMS->lang['display_0']}</option>";
        $data .= "<option value='1'>{$CMS->lang['display_1']}</option>";
        $CMS->vars['display_status'] = $data;

        // Menu
        $CMS->menu = $CMS->class->xml->convert(self::load_layout("menu", true));

        $CMS->menu = $CMS->menu['rootmenu']['menu'];

        // Menu Permission
        $CMS->permission = $CMS->class->xml->convert(self::load_layout("permission", true)); // Old: self::$html->skin_menu_permission()
        $CMS->permission = $CMS->permission['rootmenu']['menu'];

        // Filter the input
        $site = isset($CMS->input["site"]) ? $CMS->class->filter->clean_value($CMS->input["site"]) : "dashboard";
        $act = isset($CMS->input["act"]) ? $CMS->class->filter->clean_value($CMS->input["act"]) : "";

        $CMS->input['act'] = isset($CMS->input['act']) ? $CMS->input['act'] : "";
        $CMS->input['page'] = isset($CMS->input['page']) ? $CMS->input['page'] : 0;
        $CMS->input['keyword'] = isset($CMS->input['keyword']) ? $CMS->input['keyword'] : "";

        // Error message
        $CMS->errormsg = $CMS->errormsg ? $CMS->errormsg : isset($_SESSION['msg']) ? $_SESSION['msg'] : "";

        // Declare templates keys
        $tpl->member = $member;

        // Remove search
        if (!$CMS->class->search->url_return) {
            unset($_SESSION["url_return"]);
        }

        // Check login
        if ( ! $CMS->vars['is_login'] == 1 )
        {
            // Secured login
            /*if ( $CMS->vars['board_is_ssl'] == 1 AND $_SERVER['HTTPS'] != "on" )
            {
                $CMS->vars['login_url'] = str_replace("http:", "https:", $CMS->vars['root_domain']);
                $CMS->global->redirect("{$CMS->vars['login_url']}/?site=login");
                exit;
            }*/

            // Load language
            $CMS->class->language->load("login");

            // Form login
            ezy::acp_load_module("login");
        }
        // Load module
        else if ( $site )
        {
            //create subfolder to manage file for each user
            $CMS->class->image->check_folder_img("filemanager/",'',0,'');
            $CMS->class->image->check_folder_img("filemanager/{$member['user_name']}/",'',0,'thumbs');

            $_SESSION['RF']['subfolderPath'] = $CMS->vars['upload_dir'].'/filemanager/';

            if($member['user_id'])
            {
                if($member['userg_id'] == 1) //root admin
                {
                    $_SESSION['RF']['subfolder'] = str_replace('uploads/','',ezy::$upload_dir).'/filemanager/';
                }
                else
                {
                    if($member['user_name'])
                    {
                        $_SESSION['RF']['subfolderPath'] = $CMS->vars['upload_dir'].'/filemanager/'.$member['user_name'].'/';
                        $_SESSION['RF']['subfolder'] = str_replace('uploads/','',ezy::$upload_dir).'/filemanager/'.$member['user_name'].'/';
                    }
                    else
                    {
                        unset($_SESSION['RF']['subfolder']);
                    }

                }

                $_SESSION['RF']['access_key'] = $member['user_login_key'];
            }
            else
            {
                unset($_SESSION['RF']['access_key']);
            }

            // Prevent load if it's just a web
            if ( defined("is_web") == true )
            {
                // Check site in array list module site - store
                $list_module_store = array("store_request", "inventory", "returns", "shipment", "store","sites","addons");
                if (in_array($site, $list_module_store))
                {
                    //exit;
                }
            }

            // Load Permission
            $user_permission = $CMS->permit;
            // Load language
            $page = $site;
            $CMS->class->language->load( $page );

            // Replace value
            $act = isset($act) ? str_replace(array("_do", "show", "delete_all", "preview", "_reply"), array("", "read", "delete", "read", ""), $act) : "";

            // Checking...
            $act = $act ? $act : "read";

            $command = $page."_".$act;
            $is_all = isset($user_permission[$page."_is_all"]) ? $user_permission[$page."_is_all"] : 0;
            $is_admin = isset($user_permission[$page."_is_admin"]) ? $user_permission[$page."_is_admin"] : 0;
            $is_access = isset($user_permission[$command]) ? $user_permission[$command] : 0;

            // Load page
            if ( $CMS->vars['is_root'] == 1 OR $is_all == 1 OR ($CMS->vars['is_admin'] == 1 AND $is_admin == 1) OR $is_access == 1 )
            {
                //Plong: detect site=service -> convert site=product
                if($site == "service")
                {
                    // LHL-07-08-2017 Swap position between file_name and module_name
                    ezy::acp_load_module( "service","product" );
                }
                else
                {
                    ezy::acp_load_module( $page );
                }

            }
            else
            {
                $CMS->output = self::error_maintenance("{$CMS->lang['unpermitted']}");
            }
        }
        else
        {
            // Load language
            $CMS->class->language->load("dashboard");

            // Default page
            ezy::acp_load_module("dashboard");
        }

        // Load App by new workflow
        if ( ezy::$markas_new == true )
        {
            echo $CMS->output;
        }
        // Load App by old workflow
        else{
            $tpl->yield = $CMS->output;
            echo self::load_layout("application", true );
        }
    }



    /**
     * Init CRM (CRM CHAT)
     */

    static public function init_crm()
    {
        global $CMS, $tpl, $DB, $member;

        $tpl->maskFormat = preg_replace('/[^\/]/','0',$CMS->vars['date_format']) ;
        $tpl->maskPlaceHolder = preg_replace('/[^\/]/','_',$CMS->vars['date_format']) ;

        $CMS->output = "";

        // Load default language
        $CMS->class->language->load("menu");
        $CMS->class->language->load("global");

        // Load global template
        self::$html = $CMS->class->template->load_simple(self::$app_dir."/views/layouts/acp_global.php");
        $CMS->global = self::$html;

        // Check security
        if ( $CMS->class->security->check_whitelist() == false )
        {
            echo self::error_maintenance("403: Forbidden");
            exit();
        }

        // Assets
        //!empty(self::$asset_dir) ? self::$asset_dir : "/".self::$app_dir
        assets::setBase(!empty(self::$asset_dir) ? self::$asset_dir : "/".self::$app_dir,root_path, "/acp/assets/");

        // Load session
        $CMS->class->session->auto_run();

        // Re-load URL to get the value of varibles in the GET METHOD
        ezy::load_url();

        // User Permissionv
        if ( $member['user_permission'] )
        {
            $CMS->permit = $member['user_permission'];
        }


        // Location
        if ( isset($CMS->input['reset_location']) )
        {
            $_SESSION['location'] = $CMS->input['reset_location'];
            $CMS->global->redirect(str_replace("&amp;", "&", $CMS->class->filter->clean_value($_SERVER['HTTP_REFERER'])));
        }

        $CMS->input['location'] = isset($_SESSION['location']) ? $_SESSION['location'] : $member['user_location'];

        // Load addition data
        $CMS->class->search->auto_run();

        // Display
        $data = "";
        $data .= "<option value=''>{$CMS->lang['select_display']}</option>";
        $data .= "<option value='0'>{$CMS->lang['display_0']}</option>";
        $data .= "<option value='1'>{$CMS->lang['display_1']}</option>";
        $CMS->vars['display_status'] = $data;

        // Menu
        $CMS->menu = $CMS->class->xml->convert(self::load_layout("menu", true));

        $CMS->menu = $CMS->menu['rootmenu']['menu'];

        // Menu Permission
        $CMS->permission = $CMS->class->xml->convert(self::load_layout("permission", true)); // Old: self::$html->skin_menu_permission()
        $CMS->permission = $CMS->permission['rootmenu']['menu'];

        // Filter the input
        $site = isset($CMS->input["site"]) ? $CMS->class->filter->clean_value($CMS->input["site"]) : "dashboard";
        $act = isset($CMS->input["act"]) ? $CMS->class->filter->clean_value($CMS->input["act"]) : "";

        $CMS->input['act'] = isset($CMS->input['act']) ? $CMS->input['act'] : "";
        $CMS->input['page'] = isset($CMS->input['page']) ? $CMS->input['page'] : 0;
        $CMS->input['keyword'] = isset($CMS->input['keyword']) ? $CMS->input['keyword'] : "";

        // Error message
        $CMS->errormsg = $CMS->errormsg ? $CMS->errormsg : isset($_SESSION['msg']) ? $_SESSION['msg'] : "";

        // Declare templates keys
        $tpl->member = $member;

        // Remove search
        if (!$CMS->class->search->url_return) {
            unset($_SESSION["url_return"]);
        }

        if ( $site )
        {
            //create subfolder to manage file for each user
            $CMS->class->image->check_folder_img("filemanager/",'',0,'');
            $CMS->class->image->check_folder_img("filemanager/{$member['user_name']}/",'',0,'thumbs');

            $_SESSION['RF']['subfolderPath'] = $CMS->vars['upload_dir'].'/filemanager/';

            if($member['user_id'])
            {
                if($member['userg_id'] == 1) //root admin
                {
                    $_SESSION['RF']['subfolder'] = str_replace('uploads/','',ezy::$upload_dir).'/filemanager/';
                }
                else
                {
                    if($member['user_name'])
                    {
                        $_SESSION['RF']['subfolderPath'] = $CMS->vars['upload_dir'].'/filemanager/'.$member['user_name'].'/';
                        $_SESSION['RF']['subfolder'] = str_replace('uploads/','',ezy::$upload_dir).'/filemanager/'.$member['user_name'].'/';
                    }
                    else
                    {
                        unset($_SESSION['RF']['subfolder']);
                    }

                }

                $_SESSION['RF']['access_key'] = $member['user_login_key'];
            }
            else
            {
                unset($_SESSION['RF']['access_key']);
            }

            // Prevent load if it's just a web
            if ( defined("is_web") == true )
            {
                // Check site in array list module site - store
                $list_module_store = array("store_request", "inventory", "returns", "shipment", "store","sites","addons");
                if (in_array($site, $list_module_store))
                {
                    //exit;
                }
            }

            // Load Permission
            $user_permission = $CMS->permit;
            // Load language
            $page = $site;

            // Load language
            $CMS->class->language->load( $page );

            // Replace value
            $act = isset($act) ? str_replace(array("_do", "show", "delete_all", "preview", "_reply"), array("", "read", "delete", "read", ""), $act) : "";

            // Checking...
            $act = $act ? $act : "read";

            $command = $page."_".$act;
            $is_all = isset($user_permission[$page."_is_all"]) ? $user_permission[$page."_is_all"] : 0;
            $is_admin = isset($user_permission[$page."_is_admin"]) ? $user_permission[$page."_is_admin"] : 0;
            $is_access = isset($user_permission[$command]) ? $user_permission[$command] : 0;

            ezy::acp_load_module( $page );
        }
        else
        {
            // Load language
            $CMS->class->language->load("dashboard");

            // Default page
            ezy::acp_load_module("dashboard");
        }

        // Load App by new workflow
        if ( ezy::$markas_new == true )
        {
            echo $CMS->output;
        }
        // Load App by old workflow
        else{
            $tpl->yield = $CMS->output;
            echo self::load_layout("application", true );
        }
    }


    /**
     * User session, use for backend.
     */

    static public function init_session()
    {
        global $CMS, $member;

        $CMS->vars['is_login'] = 0;

        self::$time_now = time();
        self::$time_out = self::$time_now - ($CMS->vars['session_time_out'] ? $CMS->vars['session_time_out'] : 3600);

        // Allow acp & whm & crm to access table nh_user
        if ( in_array(self::$app_dir, array("acp","whm","crm")) == true )
        {
            $member = $CMS->user->session_admin();
        }
        // The others will access table nh_customer
        else
        {
            $cus_code = isset($_SESSION['cus_code']) ? $_SESSION['cus_code'] : "";

            if(isset($cus_code)&&$cus_code!='')
            {
                // Load customer
                $customer = $CMS->customer->getInfo($cus_code);

                // Convert value
                $member = $CMS->customer->convertvalue($customer);
                if($customer)
                {
                    $CMS->vars['is_login'] = 1;
                    $_SESSION['member'] = $member;
                }
                else
                {    $CMS->vars['is_login'] = 0;
                    unset($_SESSION['member']);
                }

            }
            else
            {

                $cookie_username = $CMS->class->cookie->get_cookie('cususername');
                $cookie_username = urldecode($cookie_username);
                $cookie_password = $CMS->class->cookie->get_cookie('cushash');
                if($cookie_username && $cookie_password)
                {
                    $customer = $CMS->customer->getInfo($cookie_username);
                    if($customer)
                    {
                        if($customer['cus_password'] == $cookie_password)
                        {
                            // Convert value
                            $member = $CMS->customer->convertvalue($customer);
                            $CMS->vars['is_login'] = 1;
                            $_SESSION['member'] = $member;
                        }
                    }
                    else
                    {
                        $CMS->vars['is_login'] = 0;
                        unset($_SESSION['member']);
                    }
                }
                else
                {
                    $CMS->vars['is_login'] = 0;
                    unset($_SESSION['member']);
                }
            }
        }
    }

    /**
     * Template loader, use for backend.
     * @param $name: Module name
     * @param string $mod
     * @return mixed
     */

    static public function template( $name, $mod = "" )
    {
        global $CMS;

        $mod = $mod ? $mod : ($CMS->vars['is_admin_module'] == 1 ? $CMS->input['site'] : self::$routes[ strtolower($CMS->input['site']) ]);

        // Set URL to load
        $url = root_path.self::$app_dir."/modules/{$mod}/views/{$name}.php";

        // Load template
        if ( include_once($url) )
        {
            $name = "\\view\\$name";
            return new $name;
        }
        else
        {
            echo "<p>Template <strong>{$name}</strong> ({$url}) not found</p>".PHP_EOL;
            exit;
        }
    }

    /**
     * Get Layout URL for self::html() and self::render() and self::tpl()
     * @param $name
     * @param $mod
     * @return string
     */

    static public function getLayoutPath($name, $mod)
    {
        // Set default $mod
        if ( empty($mod) && self::$mod_dir )
        {
            $mod = self::$mod_dir;
            self::$markas_new = true; // New workflow to load html layouts
        }
        // Mark custom module as new template engine
        else if ( !empty($mod) )
        {
            self::$markas_new = true;
        }
        // Normal template
        else{
            $mod = $mod ? $mod : self::$routes[self::$site];
        }

        // Set default $name
        $name = $name ? $name : "main";

        // (Web + ACP) Check for customize template
        if ( self::$app_dir == self::$web_name)
        {
            // Check custom template
            $urlcustomize = self::getTemplatePath($mod, str_replace("tpl.", "", $name), true);

            if ( substr($name, 0, 3) == "tpl" )
            {
                if ( file_exists($urlcustomize) == true )
                {
                    return $urlcustomize;
                }
            }

            // Check web's theme to get path again
            return root_path.ezy::$web_views."/{$mod}/{$name}.html.php";
        }

        // Original template (System's template)
        return root_path.self::$app_dir ."/views/{$mod}/{$name}.html.php";
    }

    /**
     * Load HTML Partial
     * @param $name: Module name, leave blank to load default name "main"
     * @return mixed
     * Example: echo ezy::html(); => /xxx/views/yyy/layouts/main.html.php
    echo ezy::html("taskbar"); => /xxx/views/yyy/layouts/taskbar.html.php
    xxx for application name
    yyy for module name
     */

    static public function html($name = "", $mod  = "")
    {
        global $CMS, $tpl;

        // Set URL to load
        $url = self::getLayoutPath($name, $mod);

        // Check file exists
        if ( file_exists($url) )
        {
            // Load module's html
            $tpl->yield = self::load_layout($url); // .self::$yield

            // Load application's html
            $output = self::load_layout("application", self::$app_dir == "app" ? false : true );

            return $output;
        }
        // If not exist
        else
        {
            echo "<p>Load HTML <strong>{$name}</strong> ({$url}) not success</p>".PHP_EOL;
            exit;
        }
    }

    /**
     * @param $name
     * @return string
     */

    static public function render($name = "", $mod = "" )
    {
        global $CMS, $tpl;

        // Set URL to load
        $url = self::getLayoutPath($name, $mod);

        // Check file exists
        if ( file_exists($url) )
        {
            //self::$yield .=
            return self::load_layout($url);
        }
        // If not exist
        else
        {
            echo "<p>Render <strong>{$name}</strong> ({$url}) not success</p>".PHP_EOL;
            exit;
        }
    }

    /**
     * Usage: \core\ezy::tpl($name, $mod) = \core\ezy::render("tpl.".$name, $mod)
     * Files with prefix "tpl." are editable
     * @param $name
     * @return string
     */

    static public function tpl($name, $mod = "" )
    {
        global $CMS, $tpl;

        $content = self::render("tpl.".$name, $mod);

        //Fix src image
        $content = str_replace('/acp/','',$content);

        return $content;
    }

    /**
     * ACP Load Module
     * @param $file_name: Module name
     * @return bool
     */

    static public function acp_load_module( $file_name = "", $module_name = "" )
    {
        global $CMS;

        $CMS->input["site"] = $CMS->class->filter->clean_value($file_name);

        // Set module dir
        self::$mod_dir = $file_name;

        // Check for custom
        if ( !empty($module_name) )
        {
            $url = root_path.self::$app_dir."/modules/{$module_name}/{$file_name}.php";
        }
        else
        {
            $url = root_path.self::$app_dir."/modules/{$file_name}/{$file_name}.php";
        }

        if ( file_exists( $url ) )
        {
            require_once( $url );
        }
        else
        {
            $CMS->output .= self::error_maintenance("{$CMS->lang['unavailable']}");
        }

        return false;
    }

    /**
     * Simple load layout /app/views/layout/xxx.html.php
     * @param string $layout_name: xxx - layout's name
     */

    static public function load_layout($layout_name = "", $load_custom = false )
    {
        global $CMS, $tpl; // BEWARE: Don't remove this line even your IDE alert that it's not necessary

        // Default path
        $default_views = self::$app_dir."/views";
        $default_layouts = self::$layouts;

        // Overwrite views/layout path
        if ( self::$app_dir == self::$web_name )
        {
            $default_views = self::$web_views;
            $default_layouts = self::$web_views."layouts";
        }

        // Load layout via custom app, ex: app, acp, web...
        if ( is_bool($load_custom) == false )
        {
            $path = count(explode("/", $layout_name)) > 1 ? $layout_name : root_path.$load_custom."/views/layouts/{$layout_name}.html.php";
        }
        // Load layout via custom path
        else if ( $load_custom == true )
        {
            $path = count(explode("/", $layout_name)) > 1 ? $layout_name : root_path.$default_views."/layouts/{$layout_name}.html.php";
        }
        // Load layout via default path
        else
        {
            $path = count(explode("/", $layout_name)) > 1 ? $layout_name : root_path.$default_layouts."/{$layout_name}.html.php";
        }

        ob_start();
        if ( !include($path) ) { die("EzyPHP: Can't load {$path}"); }
        return ob_get_clean();
    }

    /**
     * Google ReCaptcha v2 Verifier
     * @param string $data: g-recaptcha-response
     * @return bool|string
     */

    static public function google_recaptcha_verify()
    {
        global $CMS;

        if( ! $CMS->vars['recaptcha_google'] )
        {
            return true; // Flag recaptcha of google is off then return true
        }

        $google_recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
        $data = $CMS->input['g-recaptcha-response'];

        $context  = stream_context_create(array('http' =>
            array(
                'method'  => 'POST',
                'timeout' => 60,
                'content' => "&secret={$CMS->vars['gg_secrectkey']}&response={$data}&remoteip=".$CMS->class->input->get_client_ip()
            )
        ));

        $success = file_get_contents($google_recaptcha_url, false, $context, -1, 1024);

        return $success == true ? true : false;
    }

    /**
     * @param $msg: Message to show
     * @param $conn: MySQL Connection object
     */

    static public function error_mysql($msg, $conn)
    {
        global $tpl;

        $tpl->msg = $msg;
        $tpl->conn = $conn;

        return self::load_layout("error_mysql", "app");
    }

    /**
     * @param $msg: Message to show
     */

    static public function error_maintenance($msg)
    {
        global $tpl;

        $tpl->msg = $msg;

        return self::load_layout("error_maintenance", "app");
    }

    /**
     * Load model
     * @param string $model_name
     * @param string $app_dir - nkvp - 2017-10-04
     */

    static public function load_model($model_name = "", $app_dir =  null)
    {
        // Load default model
        $model_path = ($app_dir !== null ? root_path.$app_dir : root_path.self::$app_dir) . "/models/" . $model_name .".php";

        if ( file_exists($model_path) )
        {
            require_once $model_path;
        }
    }

    /**
     * (Web's App)
     * Create URL for web
     * @param bool $profile
     */

    static public function getTemplatePath($folder, $file, $customize = false, $lang = '', $type='get')
    {
        global $CMS;
        $lang = trim($lang);

        if($CMS->vars['translations'])
        {
            $lang = $lang ? $lang : $CMS->vars['default_language'];
        }
        else
        {
            $lang = '';
        }

        // User customize
        if ( $customize == true )
        {
            if($lang)
            {
                //Ưu tiên check theo file đa ngôn ngữ trước, k thấy thì lấy theo file 1 ngôn ngữ
                if(is_file($filePath = "{$CMS->vars['upload_dir']}/views/".ezy::$web_theme."/".$folder."-".$file."-".$lang.".html.php") || $type=='set')
                {
                    return $filePath;
                }
                else
                {
                    return "{$CMS->vars['upload_dir']}/views/".ezy::$web_theme."/".$folder."-".$file.".html.php";
                }
            }
            else
            {
                //Ưu tiên lấy theo file 1 ngôn ngữ, k thấy thì lấy theo file đa ngôn ngữ có hậu tố theo ngôn ngữ mặc định
                if(is_file($filePath = "{$CMS->vars['upload_dir']}/views/".ezy::$web_theme."/".$folder."-".$file.".html.php")  || $type=='set')
                {
                    return $filePath;
                }
                else
                {
                    return "{$CMS->vars['upload_dir']}/views/".ezy::$web_theme."/".$folder."-".$file."-".$CMS->vars['default_language'].".html.php";
                }
            }
        }
        // Original template
        else
        {
            return root_path.ezy::$web_views.$folder."/tpl.".$file.".html.php";
        }
    }

    /**
     * Dùng để nhúng các đoạn script, css, html vào tpl theo dạng
     * VD: {{embled.contact_popup}} => ezy::embled('contact_popup')
     * nkvp - 2017-10-04
     * @param $embed_code
     * @return string
     */
    static function embed($embed_code)
    {
        global $CMS;

        self::load_model('embed', 'acp');

        $data = \models\embed::getInfo($embed_code, ' embed_status=1 AND ');

        $html = '';

        //Check time
        if($data)
        {
            if( ($data['embed_start_date'] <= time() || $data['embed_start_date'] == 0) && ($data['embed_end_date']+(3600*24) > time() || $data['embed_end_date'] == 0))
            {
                $checkInterval = true;

                if($data['embed_interval'] > 0) //Check interval
                {
                    if(isset($_SESSION['embed_last_time'][$embed_code]))
                    {
                        if($_SESSION['embed_last_time'][$embed_code] + ($data['embed_interval']*60) < time())
                        {
                            $_SESSION['embed_last_time'][$embed_code] = time();
                        }
                        else
                        {
                            $checkInterval = false;
                        }
                    }
                    else
                    {
                        $_SESSION['embed_last_time'][$embed_code] = time();
                    }
                }

                if($checkInterval)
                {
                    $data['embed_css'] = htmlspecialchars_decode($data['embed_css']);
                    $data['embed_css'] = html_entity_decode($data['embed_css'], ENT_QUOTES | ENT_XML1, 'UTF-8');

                    $data['embed_html'] = htmlspecialchars_decode($data['embed_html']);
                    $data['embed_html'] = html_entity_decode($data['embed_html'], ENT_QUOTES | ENT_XML1, 'UTF-8');

                    $data['embed_js'] = htmlspecialchars_decode($data['embed_js']);
                    $data['embed_js'] = html_entity_decode($data['embed_js'], ENT_QUOTES | ENT_XML1, 'UTF-8');


                    $html .= <<<EOF
        <!-- Start - Embed {$embed_code} -->
        <style>
            {$data['embed_css']}
        </style>
        {$data['embed_html']}
        <script>
            {$data['embed_js']}
        </script>
        <!-- End - Embed {$embed_code} -->
EOF;
                }

            }

        }
        /*else
        {
            die("EzyPHP: Embled code \"{$embed_code}\" doesn't existed");
        }*/

        return $html;
    }

    static function checkFile404()
    {
        return file_exists(root_path.ezy::$web_views."layouts/tpl.error_404.html.php");
    }

    /**
     * Build url to arrange data
     * @param $text
     * @param $orderField
     * @return string
     */
    static function arrangeData($text,$orderField)
    {
        global $CMS;

        $query_string = $_SERVER['QUERY_STRING'];

        $query_string =  explode('&',$query_string);

        foreach($query_string as $key => $item)
        {
            if(preg_match('/^order=/',$item) || preg_match('/^order_by=/',$item))
            {
                unset($query_string[$key]);
            }
        }

        $query_string[] = 'order='.$orderField;

        if(isset($CMS->input['order_by']) && $CMS->input['order'] == $orderField)
        {
            $activeStyle = 'color: blue';
            $orderBy = $CMS->input['order_by'] == 'desc' ? 'asc' : 'desc';
        }
        else
        {
            $activeStyle = '';
            $orderBy = 'desc';
        }

        $query_string[] = 'order_by='.$orderBy;

        $query_string = implode('&',$query_string);

        $output = "<a style='{$activeStyle}' href='{$CMS->vars['root_domain']}/?{$query_string}'>{$text} <i class=\"fa fa-sort\" aria-hidden=\"true\"></i></a>";

        return $output;
    }

    /**
     * Init for api app
     * nkvp 15.03.2018
     * @return bool
     */
    static public function init_api()
    {
        global $CMS, $tpl;


        $devlist = array("localhost","127.0.0.1","::1");
        if(in_array($_SERVER['REMOTE_ADDR'],$devlist))
        {
            header('Access-Control-Allow-Origin: *');
        }

//        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
//        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description, Authorization');
        header('Access-Control-Allow-Headers: *');

        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            return;
        }

        if(ezy::$api_auth) {
            if($CMS->input['site'] != 'staff' || $CMS->input['act'] != 'login') {
                if(!security::checkValidAccess()) { //Check login
                    $return = [
                        'status' => 'fail',
                        'msg' => 'Invalid access'
                    ];
                    input::jsonEncode($return);
                }
            }
        }

        // Load session
        $CMS->class->session->auto_run();

        ezy::$input = array_merge(ezy::$input, $CMS->input);

        if(!input::get('site')) return;

        // Load model
        self::load_model(input::arrayValue(self::$routes, input::get('site')));

        // Load controller
        require_once root_path.self::$app_dir. "/controllers/" . input::arrayValue(self::$routes, input::get('site')) .".php";

        // Generate module name based on $site
        $modulename = '\controller\\'.self::$routes[$CMS->input['site']];

        //Load function by action
        if(is_callable([$modulename, input::get('act')]))
        {
            $act = input::get('act');
            $modulename::{$act}();
        }
    }

    static public function init_checkin()
    {
        global $CMS, $tpl, $DB, $member;

        $CMS->output = "";

        // Load default language
        $CMS->class->language->load("global", "acp");

        // Check security
        if ( $CMS->class->security->check_whitelist() == false )
        {
            echo self::error_maintenance("403: Forbidden");
            exit();
        }

        // Assets
        assets::setBase(!empty(self::$asset_dir) ? self::$asset_dir : "/".self::$app_dir,root_path, "/checkin/assets/");

        // Load session
        $CMS->class->session->auto_run();

        //Check login
        $jwt = security::jwtDecode($_COOKIE['accessToken']);

        if(!isset($jwt->staff->id) || !$jwt->staff->id) {
            header('Location: ' . $CMS->vars['parent_domain'] . '/acp/?returnUrl=' . urlencode($CMS->vars['root_domain']));
            exit;
        }

        ezy::$input = array_merge(ezy::$input, $CMS->input);

        $site = input::get('site', 'home');

        if(!input::arrayValue(self::$routes, $site)) {
            header("HTTP/1.0 404 Not Found"); exit;
        }

        // Load model
        self::load_model(input::arrayValue(self::$routes, $site));

        // Load controller
        require_once root_path.self::$app_dir. "/controllers/" . input::arrayValue(self::$routes, $site) .".php";

        // Generate module name based on $site
        $modulename = '\\Checkin\\Controller\\'.self::$routes[$site];

        //Load function by action
        $act = input::get('act', 'index');
        if(is_callable([$modulename, $act]))
        {
            $modulename::{$act}();
        }

        echo ezy::html("main", $site);
    }
}