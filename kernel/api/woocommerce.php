<?php

/**
 * Ezy-Sync System
 * Added: 2018-05-20
 * Modified: 2018-05-29
 */

use \lib\input;

new api_woo;

class api_woo
{
    static private $debug_mode = false;

    static public $store_id = 1;
    static public $store_url;
    static public $store_backend = "woo";
    static public $store_module;
    static public $woo;

    static private $msg; // Sync message
    static private $sync_method;
    static private $sync_original = []; // Original data to compare with local & online

    static private $checksum_field = "checksum_result"; // Field to check checksum to detect changes from local to online
    static private $checksum_seperate = "-";

    static public $field = [

        //-------------------
        // PRODUCT CATEGORIES
        //-------------------

        "products/categories" => [
            "id" => "product_group_id",
            "name" => "product_group_name",
            "slug" => "product_group_code",
            "parent" => "product_group_parent",
            "description" => "product_group_description",
            //"display" => "product_group_status", // Ex: display = 1 // Online: Category archive display type. Options: default, products, subcategories and both. Default is default.
            "menu_order" => "product_group_order",
            "image" => "product_group_avatar",
        ],
        "products/categories/checksum" => [
            "product_group_name", "product_group_parent",
            "product_group_description",
            "product_group_order"
            //"product_group_status",
        ],
        //, "product_group_avatar", "product_group_id",
        "products/categories/table" => [
            "table_name" => "product_group", // Local table name
            "field_id" => "product_group_id", // Use to search local_id
            "field_name" => "product_group_name", // Use to search local_id
            "field_deleted" => "product_group_deleted",
        ],
        "products/categories/status" => [
            "display" => 1, "default" => 0
        ],
        "products/categories/status/keys" => [
            1 => "display",
            0 => "default"
        ],
        "products/categories/show" => [],
        "products/categories/show/keys" => [],
        "products/categories/parent" => [ // Category
            "detect_parent_name" => "parent", // Online table name
            "detect_field_name" => "name", // Online field name
            "table_name" => "product_group", // Local table name
            "field_name" => "product_group_name", // Local field name
            "field_id" => "product_group_id", // Local field id
            "upload_field" => "product_group_avatar",
            "upload_dir" => "product",
        ],

        //-------------------
        // PRODUCT
        //-------------------

        "products" => [
            "id" => "product_id", // 36
            "name" => "product_name", // Tshirt
            "slug" => "product_code",
            //"date_created" => "product_time", // 2018-05-03T19:51:18
            ///"date_modified" => "", // 2018-05-03T19:51:18
            "status" => "product_status", // Options: any, draft, pending, private and publish. Default is any.
            "catalog_visibility" => "product_show", // visible
            "description" => "product_description", // Text....
            //"type" => "", // Options: simple, grouped, external and variable.
            //"short_description" => "", // Ussally empty...
            "sku" => "product_sku", //
            "price" => "product_price_original", // 18
            "regular_price" => "product_price", // 18
            "sale_price" => "product_price_sell", //
            "categories" => "product_group", // [0] => Array ( [id] => 18 [name] => Tshirts [slug] => tshirts )
            "images" => "product_image", // [0] => Array ( [id] => 17
            //[date_created] => 2018-05-03T19:51:17
            //[date_modified] => 2018-05-03T19:51:17
            //[src] => https://pattereo.mystagingwebsite.com/wp-content/uploads/2018/05/tshirt.jpg
            //[name] => Tshirt
            //[alt] =>
            //[position] => 0 )
        ],
        "products/checksum" => [
            "product_name",
            "product_description",
            "product_status",
            "product_show",
            "product_price",
            //"product_price_original",
            //"product_price_sell",
        ],
        "products/status" => [
            "any" => 1,
            "draft" => 0, "pending" => 0, "private" => 0, "publish" => 1, // status
            "visible" => 1 // visible
        ],
        // Alway publish...
        "products/status/keys" => [
            "0" => "publish",
            "1" => "publish",
            "2" => "publish",
        ],
        "products/show" => [
            "hidden" => 0,
            "visible" => 1,
        ],
        // Alway publish...
        "products/show/keys" => [
            "0" => "hidden",
            "1" => "visible",
        ],

        "products/parent" => [ // Category
            "detect_parent_name" => "categories", // Online (field) table name
            "detect_field_name" => "name", // Online field name
            "table_name" => "product_group", // Local table name
            "field_name" => "product_group_name", // Local field name
            "field_id" => "product_group_id", // Local field id
            "upload_field" => "product_image",
            "upload_dir" => "product",
        ],
        "products/table" => [
            "table_name" => "product", // Local table name
            "field_id" => "product_id", // Use to search local_id
            "field_name" => "product_name", // Use to search local_id
            "field_deleted" => "product_deleted",
        ],

        //-------------------
        // ORDER
        //-------------------

        // , "product_image" "product_id",
        "orders" => [
            "id" => "ord_id", // 41
            //"number" => "ord_id", // 41
            "order_key" => "ord_name", // wc_order_5af707572fa7e
            "status" => "ord_status", // pending
            // any, pending, processing, on-hold, completed, cancelled, refunded and failed. Default is any
            //"currency", // USD
            "date_created" => "ord_time", // 2018-05-12T15:20:47
            "date_modified" => "ord_time_update", // 2018-05-12T15:25:11
            "discount_total" => "ord_discount", // 0.00
            //"discount_tax", // 0.00
            //"shipping_total", // 0.00
            //"shipping_tax", // 0.00
            //"cart_tax", // 0.00
            "total" => "ord_total", // 38.00
            //"total_tax", // 0.00
            "customer_id" => "cus_id", // 0
            //"customer_ip_address",
            //"customer_user_agent",
            "customer_note" => "ord_note",
            //"billing",
            /*Array
            (
                [first_name] =>
                    [last_name] =>
                        [company] =>
                        [address_1] =>
                        [address_2] =>
                        [city] =>
                        [state] =>
                        [postcode] =>
                        [country] =>
                        [email] =>
                        [phone] =>
                    )*/
            //"shipping",
            /*(
                        [first_name] =>
                        [last_name] =>
                        [company] =>
                        [address_1] =>
                        [address_2] =>
                        [city] =>
                        [state] =>
                        [postcode] =>
                        [country] =>
                    )*/
            //"payment_method" => "payment_method",
            //"payment_method_title",
            //"transaction_id" => "transaction_id",
            //"date_paid",
            //"date_completed",
            //"cart_hash", // Unused
            "line_items" => "ord_item",
            /*[0] => Array
                            (
                                [id] => 1
                                [name] => Polo
                                [product_id] => 34
                                [variation_id] => 0
                                [quantity] => 1
                                [tax_class] =>
                                [subtotal] => 20.00
                                [subtotal_tax] => 0.00
                                [total] => 20.00
                                [total_tax] => 0.00
                                [taxes] => Array
                                    (
                                    )

                                [meta_data] => Array
                                    (
                                    )

                                [sku] =>
                                [price] => 20
                            )

            [{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"},{"product_id":"38","product_name":"iphone 9","product_description":"","product_cycle":1,"product_cycle_type":"iphone 9","product_quantity":"1","product_price":"500000","product_tax":"10"}]
            */
        ],
        "orders/checksum" => [
            "ord_total", "ord_status"
        ],
        "orders/status" => [
            "any" => 0,
            "pending" => 0,
            "processing" => 1,
            "on-hold" => 1,
            "completed" => 2,
            "cancelled" => 3,
            "refunded" => 3,
            "failed" => 3
        ],
        "orders/status/keys" => [
            "0" => "pending",
            "1" => "processing",
            "2" => "completed",
            "3" => "cancelled"
        ],
        "orders/table" => [
            "table_name" => "order", // Local table name
            "field_id" => "ord_id", // Use to search local_id
            "field_name" => "ord_name", // Use to search local_id
            "field_deleted" => "ord_deleted",
        ],
        "orders/parent" => [ // Category
            "detect_parent_name" => "product_id", // Online table name
            "detect_parent_module" => "products", // Online module name
            //"detect_field_name" => "product_name", // Online field name
            //"detect_field_id" => "product_id", // Online field name
            "table_name" => "product", // Local table name
            "field_name" => "product_name", // Local field name
            "field_id" => "product_id", // Local field id
            //"upload_field" => "ord_image",
            //"upload_dir" => "order",
        ],
        "orders/child" => [
            "ord_item" => [
                //"id" => "ordi_id",
            "name" => "product_name",
            "product_id" => "product_id",
            // [variation_id] => 0
            "quantity" => "product_quantity",
            //"subtotal" => "ordi_subtotal",
            //"subtotal_tax" => "ordi_total_tax", // Quy ra % mới đúng.
            //"total" => "ordi_total",
            //"total_tax" => "ordi_total",
            "price" => "product_price"
            //product_cycle_type
            //product_cycle
                ]
        ],
        // Use edit to limit what fields will update.
        "orders/edit" => [
            "ord_status",
            "ord_note"
            ],
        "orders/show/keys" => [],
    ];

    /**
     * Init before sync
     */

    static private function sync_init_log()
    {
        global $CMS, $DB;

        // Init this variable to load admin languge
        $CMS->vars['is_admin_module'] = 1;

        // Last request data.
        $lastRequest = self::$woo->http->getRequest();

        // Return null
        if ($lastRequest == null)
        {
            return array(204, [], []); // 204 = No content in header's standard.
        }

        // Continue request
        $url = $lastRequest->getUrl(); // Requested URL (string).
        $method = $lastRequest->getMethod(); // Request method (string).
        $lastRequest->getParameters(); // Request parameters (array).
        $lastRequest->getHeaders(); // Request headers (array).
        $lastRequest->getBody(); // Request body (JSON).

        // Last response data.
        $lastResponse = self::$woo->http->getResponse();

        // Get input data
        $sync_code = md5($url.input::jsonEncode($method,0).time());
        $store_id = self::$store_id;
        $sync_store_url = self::$store_url;
        $sync_store_backend = self::$store_backend;
        $sync_module = self::$store_module;
        $sync_content = base64_encode($lastResponse->getBody()); // Response body (JSON)
        $sync_header = input::jsonEncode($lastResponse->getHeaders(), 0); // Response headers (array)
        $sync_header_status = $lastResponse->getCode();
        $sync_time = time();

        // Additional data
        $sync_original = input::jsonEncode(self::$sync_original, 0);

        self::$msg .= "*Create sync data".PHP_EOL;

        //Insert into database
        $DB->query("INSERT INTO ".root_table."sync (sync_code, store_id, sync_store_url, sync_store_backend, sync_module, sync_content, sync_header, sync_header_status, sync_time, sync_method, sync_original)
                        VALUES ('{$sync_code}', '{$store_id}', '{$sync_store_url}', '{$sync_store_backend}', '{$sync_module}', '{$sync_content}', '{$sync_header}', '{$sync_header_status}', '{$sync_time}', '".self::$sync_method."', '{$sync_original}')");

        // Return last id
        //$last_id = $DB->last_insert_id();

        $last = $DB->query("SELECT * FROM ".root_table."sync WHERE sync_code='{$sync_code}' ORDER BY sync_id DESC");
        $lastsync = $DB->fetch_array($last);

        return array($lastsync, $sync_header_status, base64_decode($sync_content), $sync_original);
    }

    /**
     * Init record to determine add or edit
     * @param $online_id
     */

    static private function init_record($online_id, $checksum, $rec_content, $local_id = 0, $rec_content_str = "")
    {
        global $DB;

        // Prevent insert 0 data
        if ( $online_id == 0 )
        {
            return "error";
        }

        // Find record via Module & online_id (Online store, which one we will download data)
        //echo "SELECT * FROM ".root_table."sync_records WHERE sync_module='".self::$store_module."' AND online_id='{$online_id}'".PHP_EOL;
        $sql = $DB->query("SELECT * FROM ".root_table."sync_records WHERE sync_module='".self::$store_module."'  AND store_id='".self::$store_id."' AND online_id='{$online_id}' AND rec_status!=3");

        // If existed
        if ( $DB->num_rows($sql) > 0 )
        {
            $data = $DB->fetch_array($sql);

            // Detect for changes
            if ( $data['rec_checksum'] != $checksum )
            {
                // Update status, marked as edit if checksum not match. Process to download.
                $DB->query("UPDATE ".root_table."sync_records SET rec_log='{$rec_content_str}', rec_update_time='".time()."', rec_status='1', rec_checksum='{$checksum}', rec_content_old=rec_content, rec_content='{$rec_content}' WHERE rec_id='{$data['rec_id']}'");
                return array("edit", $data['local_id']);
            }

            // Check to update rec_content
            if ( empty($data['rec_content']) OR empty($data['rec_log']) )
            {
                $DB->query("UPDATE ".root_table."sync_records SET rec_log='{$rec_content_str}', rec_update_time='".time()."', rec_content='{$rec_content}' WHERE rec_id='{$data['rec_id']}'");
            }

            // Do nothing.
            return array("nope", $data['local_id'], $data['rec_id']);
        }
        // Not exist
        else {
            // Init record
            $store_id = self::$store_id;
            $rec_time = time();
            $rec_checksum = $checksum;

            //echo "INSERT INTO ".root_table."sync_records (store_id, sync_module, online_id, rec_time, rec_checksum, rec_content, local_checksum, local_id)
            //            VALUES ('{$store_id}', '".self::$store_module."', '{$online_id}', '{$rec_time}', '{$rec_checksum}', '{$rec_content}', '".time()."', '{$local_id}')".PHP_EOL;

            // Create record, status marked as add.
            $DB->query("INSERT INTO ".root_table."sync_records (store_id, sync_module, online_id, rec_time, rec_checksum, rec_content, local_checksum, local_id)
                        VALUES ('{$store_id}', '".self::$store_module."', '{$online_id}', '{$rec_time}', '{$rec_checksum}', '{$rec_content}', '".time()."', '{$local_id}')");

            //echo "SELECT * FROM ".root_table."sync_records WHERE rec_checksum='{$rec_checksum}' ORDER BY rec_id DESC".PHP_EOL;

            // Get record id
            $sql = $DB->query("SELECT * FROM ".root_table."sync_records WHERE rec_checksum='{$rec_checksum}' AND rec_status!=3 ORDER BY rec_id DESC");
            $data = $DB->fetch_array($sql);

            return array("add", $data['local_id'], $data['rec_id']);
        }
    }

    /**
     * Sync init, detect current task
     */

    static private function sync_init($sync_method = "download")
    {
        global $DB;

        // Set sync method
        self::$sync_method = $sync_method;

        // Declare
        $data = [];
        $original = [];
        $status = "";

        // Detect Task
        $DB->query("SELECT * FROM ".root_table."sync WHERE sync_status<2 AND store_id='".self::$store_id."' AND sync_module='".self::$store_module."' AND sync_method='".self::$sync_method."' ORDER BY sync_id DESC LIMIT 1");
        $lastsync = $DB->fetch_array();

        self::$msg .= "Current sync <".self::$store_module."/{$sync_method}> #".self::$store_id."".PHP_EOL;

        if ( $DB->num_rows() > 0 )
        {
            if ( $lastsync['sync_status'] == 1 )
            {
                echo "<b>A task sync is being synced on another session, please wait a moment!</b>".PHP_EOL;
                //exit;
            }

            // Update processing
            $DB->query("UPDATE ".root_table."sync SET sync_status=1 WHERE sync_id='{$lastsync['sync_id']}'");
            //self::$msg .= "<b>Task is being synced on this session</b>".PHP_EOL;

            // Load json data
            $data = base64_decode($lastsync['sync_content']);
            $original = $lastsync['sync_original'];
            $status = $lastsync['sync_header_status'];
        }

        return array($lastsync, $data, $status, $original);
    }

    /**
     * Sync end: Footer
     * @param $lastsync
     */

    static private function sync_end($lastsync, $status = 2)
    {
        global $DB;

        self::$msg .= "Task has been synced on this session" . PHP_EOL;

        $sql = "UPDATE ".root_table."sync SET sync_status='{$status}', sync_message='" . self::$msg . "' WHERE sync_id='{$lastsync['sync_id']}'";

        // Debug mode
        if ( self::$debug_mode == true ) {
            echo $sql.PHP_EOL;
        }

        // Update completed for sync
        $DB->query($sql);

        echo self::$msg;

        // Remove old message
        self::$msg = "";
    }

    /**
     * Check parent table, use this function to check REAL product id in LOCAL.
     */

    static private function convert_onlineid_to_localid($col, $item)
    {
        global $DB;

        // Parent table
        $parent = self::$field[self::$store_module . "/parent"];

        // Detect CATEGORY
        if ( $col == $parent['detect_parent_name'] )
        {
            // Detect for field name
            if ( isset($parent['detect_field_name']) )
            {
                $parent_id = $item[$col][0][$parent['detect_field_name']];

                $sql = $DB->query("SELECT {$parent['field_id']} as result_id FROM ".root_table."{$parent['table_name']} WHERE {$parent['field_name']}='{$parent_id}'");
            }
            // Load from sync_records
            else{
                $online_id = $item[$parent['detect_parent_name']];
                $sql_record = $DB->query("SELECT local_id FROM ".root_table."sync_records WHERE sync_module='".$parent['detect_parent_module']."' AND store_id='".self::$store_id."' AND online_id='{$online_id}' AND rec_status!=3");
                $data = $DB->fetch_array($sql_record);
                $parent_id = $data['local_id'];

                $sql = $DB->query("SELECT {$parent['field_id']} as result_id FROM ".root_table."{$parent['table_name']} WHERE {$parent['field_id']}='{$parent_id}'");
            }

            $parent_data = $DB->fetch_array($sql);

            // Set ID to current column
            $item[$col] = $parent_data['result_id'];
        }

        return $item;
    }

    /**
     * Init checksum
     * @param $item
     * @return array
     */

    static private function init_checksum_download($item)
    {
        global $CMS, $DB;

        // Declare for rec_content data
        $rec_content = [];

        // Checksum
        $rec_content_str = "";

        // Check checksum array
        if (!isset(self::$field[self::$store_module . "/checksum"])) {
            exit("Please declare checksum data for: " . self::$store_module . "/checksum");
        }

        if (!isset(self::$field[self::$store_module . "/status"])) {
            exit("Please declare status data for: " . self::$store_module . "/status");
        }

        if (!isset(self::$field[self::$store_module . "/parent"])) {
            exit("Please declare parent data for: " . self::$store_module . "/parent");
        }

        if (!isset(self::$field[self::$store_module . "/status/keys"])) {
            exit("Please declare parent data for: " . self::$store_module . "/status/keys");
        }

        if (!isset(self::$field[self::$store_module . "/show/keys"])) {
            exit("Please declare parent data for: " . self::$store_module . "/show/keys");
        }

        // Generate data
        foreach (self::$field[self::$store_module] as $col => $val)
        {
            // Detect IMAGE
            if ( preg_match("/(image|avatar)/", $col) )
            {
                // Single image
                if ( isset($item[$col]['src']) )
                {
                    $item[$col] = $item[$col]['src'];
                }
                // Multi-image
                else if ( isset($item[$col][0]['src']) )
                {
                    $item[$col] = $item[$col][0]['src'];

                    // Production: cần phải cho nó multi-src
                }
            }
            // Detect STATUS
            // status|show|visibility: is a part of a field
            else if ( preg_match("/(status)/", $col) == true && in_array(strtolower($item[$col]), self::$field[self::$store_module . "/status"]) )
            {
                $item[$col] = self::$field[self::$store_module . "/status"][strtolower($item[$col])]; // 1 = Display, 0 = none
            }
            // status|show|visibility: is a part of a field
            else if ( preg_match("/(show|visibility|display)/", $col) == true && in_array(strtolower($item[$col]), self::$field[self::$store_module . "/status"]) )
            {
                $item[$col] = 1; // 1 = Display, 0 = none
            }
            // Check time
            else if ( preg_match("/(date)/", $col) == true )
            {
                // Woo-commerce only
                list($get_date, $get_time) = explode("T", $item[$col]);
                $get_time = explode(":", $get_time);

                // Unixtime (Removed time zone=1) + Hours*3600 + Minutes*60 + Seconds
                $item[$col] = $CMS->class->date->date2time($get_date,1)+($get_time[0] * 3600 + $get_time[1] * 60 + $get_time[2]);
            }
            // Check child
            else if ( isset(self::$field[self::$store_module . "/child"][$val]) == true )
            {
                $child = self::$field[self::$store_module . "/child"][$val];
                $local_data = [];

                // Fetch array data
                foreach( $item[$col] as $list_items => $item_value ){
                    // Fetch item data
                    $item_data = [];
                    foreach ( $child as $online_field => $local_field )
                    {
                        // Normal value
                        $item_data[$local_field] = $item_value[$online_field];

                        // Check for parent value (Ex: product_id)
                        $item_data = self::convert_onlineid_to_localid($local_field, $item_data);
                    }
                    // Merge
                    $local_data = array_merge($local_data, [$item_data]);
                }

                // Encode array
                $item[$col] = input::jsonEncode($local_data,0);
            }
            // Detect array
            else if ( is_array($item[$col]) )
            {
                // Detect for parent table
                $item = self::convert_onlineid_to_localid($col, $item);
            }
            // Convert to float
            else if ( preg_match("/(price)/", $col) == true )
            {
                $item[$col] = sprintf("%.2f", $item[$col]);
            }

            // Filter fields for checksum only.
            if ( in_array($val, self::$field[self::$store_module . "/checksum"]) == true )
            {
                // Encode description (Required in init_checksum_upload)
                if ( preg_match("/(description)/", $col) == true ) {
                    $rec_content_str .= strlen(trim($item[$col]));
                }
                // Normal value
                else{
                    $rec_content_str .= $item[$col];
                }

                $rec_content_str .= self::$checksum_seperate; // Add to seperate data.
            }

            // Assign: local data (column) = online data
            $rec_content[$val] = trim($item[$col]);

        }

        $online_id = $item['id'];

        // Syntax: // md5(trim(concat(store module, store_id, online_id, rec_content)))
        $rec_content_str = trim(self::$store_module . self::$store_id . self::$checksum_seperate . $online_id . self::$checksum_seperate . $rec_content_str);
        $checksum = md5($rec_content_str);

        return array($rec_content, $checksum, $online_id, $rec_content_str);
    }

    /**
     * Init checksum for upload
     * @return array
     */

    static private function init_checksum_upload()
    {
        $checksum = "";

        // Load default fields in current module
        foreach (self::$field[self::$store_module] as $col => $val)
        {
            // Detect description (Required in init_checksum_download)
            if ( preg_match("/(description)/", $val) == true  )
            {
                $checksum .= ", length(trim(".$val.")), '".self::$checksum_seperate."'";
            }
            // Detect checksum
            else {
                $checksum .= in_array($val, self::$field[self::$store_module . "/checksum"]) ? ", (T.".$val."), '".self::$checksum_seperate."'" : ""; // Generate checksum
            }
        }

        // return "checksum_result" 0 (incorrect) or 1 (correct)
        $checksum_str = "CONCAT('".trim(self::$store_module . self::$store_id . self::$checksum_seperate. "', R.online_id, '".self::$checksum_seperate."'" . $checksum).")";
        $checksum = "if(R.rec_checksum=md5({$checksum_str}), '1', '0') AS ".self::$checksum_field.", ";

        return array($checksum, $checksum_str);
    }

    /**
     * Sync table update
     * Update data auto to table without call any functions
     * @param $record
     */

    static private function sync_table_update($record, $local_id)
    {
        global $DB;

        $table = self::$field[self::$store_module . "/table"];
        $data_table = self::$field[self::$store_module];

        // Remove uncessary data
        unset($data_table['id']);
        unset($data_table['display']);

        // Init
        $fields = "";
        $count = count($data_table)-1;
        $i = 0;

        // Load default fields in current module
        foreach ($data_table as $col => $val)
        {
             // Continue
             $fields .= "{$val}='{$record[$val]}'";

             $fields .= $count > $i ? ", " : "";

             $i++;
        }

        $sql = "UPDATE ".root_table."{$table['table_name']} SET {$fields} WHERE {$table['field_id']}='{$local_id}' ";

        // Debug mode
        if ( self::$debug_mode == true ){
            echo $sql;
        }

        $DB->query($sql);
    }

    /**
     * Detect exception
     * @param $lastsync
     * @param $e
     */

    static private function init_exception($lastsync, $e)
    {
        global $CMS, $DB;

        $log = "Error message".$e->getMessage(); // Error message.
        $log .= "\nRequest:".$e->getRequest(); // Last request data.
        $log .= "\nResponse data:".$e->getResponse(); // Last response data.
        $log = $CMS->class->filter->clean_value($log);

        echo $log;

        $DB->query("UPDATE ".root_table."sync SET sync_status=3, sync_message='{$log}' WHERE sync_id='{$lastsync['sync_id']}'");
    }

    /**
     * Sync download
     *
     */

    static public function sync_download()
    {
        global $CMS, $DB;

        // Call sync init
        list($lastsync, $data, $status) = self::sync_init("download");

        // Continue
        try {
            // Check if data is not declared
            if ( empty($status) )
            {
                // Get data list
                self::$woo->get(self::$store_module."");

                // Get data
                list($lastsync, $status, $data) = self::sync_init_log();
            }

            // Sync data: Download data
            if ( $status == 200 ){

                // Decode json
                $data = input::jsonDecode($data);

                foreach ( $data as $item )
                {
                    // Call checksum
                    list($record, $checksum, $online_id, $rec_content_str) = self::init_checksum_download($item);

                    // Init sync record
                    list($result, $local_id, $rec_id) = self::init_record($online_id, $checksum, input::jsonEncode($record, 0), 0, $rec_content_str);

                    // Write log
                    self::$msg .= "- Online id: " . $online_id . "; Status: ";

                    // Call action
                    $local_id = self::sync_download_create($result, $record, $local_id);

                    // Check error
                    if ( $local_id == false && isset($_SESSION['error_msg']) )
                    {
                        self::$msg .= $_SESSION['error_msg'].PHP_EOL;

                        // Set record = failed (3)
                        $DB->query("UPDATE " . root_table . "sync_records SET rec_status_msg='".$CMS->class->filter->clean_value($_SESSION['error_msg'])."', rec_status=3 WHERE rec_id='{$rec_id}' AND local_id=0");
                    }

                    // Update local id, if not exist
                    if ( isset($local_id) && $local_id > 0 ) {
                        //echo "UPDATE " . root_table . "sync_records SET local_id='{$local_id}' WHERE rec_id='{$rec_id}' AND local_id=0".PHP_EOL;
                        $DB->query("UPDATE " . root_table . "sync_records SET local_id='{$local_id}' WHERE rec_id='{$rec_id}' AND local_id=0");
                    }
                }

                // Sync end
                self::sync_end($lastsync);
            }
            // Nothing to do
            else if ( $status == 204 )
            {
                self::$msg .= "- No content to sync".PHP_EOL;
                echo self::$msg;
            }
            // Error
            else {
                self::$msg .= "- Error code: {$status}";

                // Sync end: Error
                self::sync_end($lastsync, 3);
            }

        } catch (HttpClientException $e) {
            self::init_exception($lastsync, $e);
        }
    }

    /**
     * Sync upload
     *
     */

    static public function sync_upload($run_only = "")
    {
        global $DB;

        // Get table info
        $table = self::$field[self::$store_module . "/table"];

        // Call sync init
        list($lastsync, $data, $status, $data_origin) = self::sync_init("upload");

        // Continue
        try {

            // Check if data is not declared, try to put data.
            if (empty($status)) {

                // Check put data
                self::sync_upload_create($run_only);

                // Get data
                list($lastsync, $status, $data, $data_origin) = self::sync_init_log();

                //print $status;
                //exit;
            }

            // Sync data: CREATE
            if ($status == 200) {

                // Decode json
                $data = input::jsonDecode($data);
                $data_origin = input::jsonDecode($data_origin);

                $data_create = $data['create'];
                $data_update = $data['update'];

                // Have data to sync (CREATE)
                if (count($data_create) > 0) {
                    foreach ($data_create as $item) {

                        // Call checksum
                        list($record, $checksum, $online_id, $rec_checksum_str) = self::init_checksum_download($item);

                        // Custom message
                        $local_id = $data_origin[$record[$table['field_name']]] . PHP_EOL;

                        //self::$msg .= "Added".PHP_EOL;

                        // Init sync record
                        self::init_record($online_id, $checksum, input::jsonEncode($record, 0), $local_id, $rec_checksum_str);
                    }
                }

                // Data update
                if (count($data_update) > 0) {
                    foreach ($data_update as $item) {

                        // Call checksum
                        list($record, $checksum, $online_id, $rec_checksum_str) = self::init_checksum_download($item);

                        // Custom message
                        $local_id = $data_origin[$record[$table['field_name']]] . PHP_EOL;

                        //self::$msg .= "Updated".PHP_EOL;

                        // Init sync record
                        self::init_record($online_id, $checksum, input::jsonEncode($record, 0), $local_id, $rec_checksum_str);
                    }
                }

                if (count($data_update) == 0 && count($data_create) == 0 )
                {
                    self::$msg .= "- No content to upload".PHP_EOL;
                }

                // Sync end: Success
                self::sync_end($lastsync);
            }
            // Nothing to do
            else if ( $status == 204 )
            {
                self::$msg .= "- No content return".PHP_EOL;
                echo self::$msg;
            }
            // Error
            else {
                self::$msg .= "- Error code: {$status}";

                // Sync end: Error
                self::sync_end($lastsync, 3);
            }
        }
        catch (HttpClientException $e) {
            self::init_exception($lastsync, $e);
        }
    }

    /**
     * Sync start
     * @param $module: module name
     */

    static public function sync_start($module, $run_only = "")
    {
        // Module
        self::$store_module = $module;

        // Download only
        if ( $run_only == "download" ){
            self::sync_download();
        }
        // Upload only
        else if ( $run_only == "upload" )
        {
            self::sync_upload();
        }
        // Sync online to local only, any new data from local will not upload to online.
        else if ( $run_only == "sync" )
        {
            self::sync_download();
            self::sync_upload($run_only);
        }
        // Both of them
        else
        {
            self::sync_download();
            self::sync_upload();
        }
    }

    /**
     * Generate data to put or update
     */

    static private function sync_upload_init($record_value)
    {
        $table = self::$field[self::$store_module . "/table"];
        $data_table = self::$field[self::$store_module];

        // Remove uncessary data
        unset($data_table['id']);
        unset($data_table['display']);

        // Init data to insert
        $data_item = [];

        // Generate data
        foreach ($data_table as $item => $value)
        {
            // Check for limit update data
            if ( isset(self::$field[self::$store_module."/edit"]) )
            {
                // Check if it's not in array, by pass this loop
                if ( !in_array($value, self::$field[self::$store_module."/edit"]) ) continue; // Skip this loop
            }

            // Data isset
            if (preg_match("/(image)/", $item) == true) {
                // Production: Image goes here
            }
            // status: is a part of a field
            else if ( preg_match("/(status)/", $item) == true )
            {
                //echo "AA-".$record_value[$value]."-BB".PHP_EOL;
                //$item[$item] = self::$field[self::$store_module . "/status"][strtolower($item[$col])]; // 1 = Display, 0 = none
                $data_item[$item] = self::$field[self::$store_module . "/status/keys"][$record_value[$value]];
            }
            // visible: is a part of a field
            else if ( preg_match("/(show|visibility)/", $item) == true )
            {
                //echo "CC-".$record_value[$value]."-BB".PHP_EOL;
                //$item[$item] = self::$field[self::$store_module . "/status"][strtolower($item[$col])]; // 1 = Display, 0 = none
                $data_item[$item] = self::$field[self::$store_module . "/show/keys"][$record_value[$value]];
            }
            // Another fields
            else{
                $data_item[$item] = $record_value[$value];
            }
        }

        // Push data to orgin (search local id after upload to online)
        $data_origin[$record_value[$table['field_name']]] = trim($record_value[$table['field_id']]);

        return array($data_origin, $data_item);
    }

    /**
     * Sync put & create data to online database
     */

    static private function sync_upload_create($run_only = "")
    {
        global $DB;

        // Re-init to easily use.
        $table = self::$field[self::$store_module . "/table"];

        // Generate compare checksum command
        list($checksum, $checksum_str) = self::init_checksum_upload();

        // Debug mode
        if ( self::$debug_mode == true ) {
            echo "SELECT R.local_id, R.online_id, R.rec_content, R.rec_log, {$checksum_str} AS rec_og2, {$checksum} T.*
                        FROM " . root_table . "{$table['table_name']} as T LEFT JOIN " . root_table . "sync_records AS R 
                        ON (R.local_id=T.{$table['field_id']} AND R.store_id='" . self::$store_id . "' AND R.sync_module='" . self::$store_module . "'  AND R.rec_status!=3)
                        WHERE T.{$table['field_deleted']}=0". ($run_only == "sync" ? " AND R.store_id > 0" : "") . PHP_EOL;
        }

        // Select to compare
        $sql = $DB->query("SELECT R.local_id, R.online_id, R.rec_content, {$checksum_str} AS rec_log_local, R.rec_log AS rec_log_online, {$checksum} T.*  
                        FROM " . root_table . "{$table['table_name']} as T LEFT JOIN " . root_table . "sync_records AS R 
                        ON (R.local_id=T.{$table['field_id']} AND R.store_id='" . self::$store_id . "' AND R.sync_module='" . self::$store_module . "'  AND R.rec_status!=3)
                        WHERE T.{$table['field_deleted']}=0". ($run_only == "sync" ? " AND R.store_id > 0" : ""));

        // Batch update
        $data_origin = []; // User for search local_id
        $data_insert = []; // Array to add new data
        $data_update = []; // Array to update old data

        // Fetch data
        while ($record_value = $DB->fetch_array($sql))
        {
            self::$msg .= "- Online id: " . $record_value['online_id'] . "; Status: ";

            // Data not exist in online database; try to generate data to create.
            if (!$record_value['online_id'])
            {
                // Generate data to upload
                list($data_origin, $data_item) = self::sync_upload_init($record_value);

                // Push data to array
                array_push($data_insert, $data_item);

                // Add Status
                self::$msg .= "Add".PHP_EOL;
            }
            // Data exists, try to update, use "checksum_result"
            else if ( $record_value['online_id'] && $record_value[self::$checksum_field] == 0 )
            {
                // Generate data to upload
                list($data_origin, $data_item) = self::sync_upload_init($record_value);

                // Update require online id to put data
                $data_item['id'] = $record_value['online_id'];

                // Push data to array
                array_push($data_update, $data_item);

                // Add Status
                self::$msg .= "Update".PHP_EOL;

                echo self::$msg .= "Compared between Local: {$record_value['rec_log_local']} / Online: {$record_value['rec_log_online']}".PHP_EOL;
            }
            // Do nothing
            else
            {
                // Add Status
                self::$msg .= "N/A".PHP_EOL;
            }
        }

        // Debug mode
        //if ( self::$debug_mode == true ) {
         //   print_r( ["create" => $data_insert, "update" => $data_update]);
        //exit;
        //}

        // Send batch inserted
        if ( count($data_insert) > 0 OR count( $data_update) > 0 ){
            self::$woo->post(self::$store_module . "/batch", ["create" => $data_insert, "update" => $data_update]);
        }

        // Save data original
        self::$sync_original = $data_origin;
    }

    /**
     * Upload image
     * @param $result
     */

    static private function sync_download_image($result)
    {
        global $CMS;

        // Detect field
        $parent = self::$field[self::$store_module."/parent"];
        $image = $result[$parent['upload_field']];

        if ( isset($image) == true && substr(strtolower($image),0,4) == "http" )
        {
            // Download image
            $image_name = self::$field[self::$store_module."/table"]['field_id']."_".$CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz')."_".time().'.'.$CMS->class->attachment->get_ext( $image );
            $image_data = file_get_contents($image);
            $image_dir = $CMS->vars['upload_dir']."/".$parent['upload_dir'];

            // Check dir
            if (! is_dir($image_dir)) {
                mkdir($image_dir, 0755, true);
            }

            // Upload image to server
            file_put_contents($image_dir."/".$image_name, $image_data);

            // Overwrite url path by the image uploaded to server
            $result[$parent['upload_field']] = $image_name;
        }

        return $result;
    }

    /**
     * Sync get & create data to local database
     */

    static private function sync_download_create($result, $record, $local_id)
    {
        // Error
        if ( $result == "error" ){
            self::$msg .= "Invalid online id".PHP_EOL;
        }
        // Nothing
        else if ( $result == "nope" ){
            self::$msg .= "N/A".PHP_EOL;
        }
        // Add & Edit
        else {
            switch ( self::$store_module ) {
                case "products/categories":
                    $local_id = self::sync_download_products_categories($result, $record, $local_id);
                    break;
                case "products":
                    $local_id = self::sync_download_products($result, $record, $local_id);
                    break;
                case "orders":
                    $local_id = self::sync_download_orders($result, $record, $local_id);
                    break;
            }
        }

        return $local_id;
    }

    /**
     * @param $result: add, edit
     * @param $record
     * @param $local_id: local record id (item id)
     */

    static public function sync_download_products_categories($result, $record, $local_id)
    {
        global $CMS;

        // Add product category
        //$CMS->class->language->load("store_request");
        //$CMS->class->language->load("product_group");

        self::$msg .= "Status: ".$result.PHP_EOL;

        // Action
        switch ( $result ) {
            case "add":
                $local_id = $CMS->product_group->add($record['product_group_parent'], $record['product_group_name'], $record['product_group_code'], $record['product_group_status'], $record['product_group_description'], $record['product_group_order']);
                break;
            case "edit":
                $CMS->product_group->edit($CMS->product_group->getInfo($local_id), $record['product_group_parent'], $record['product_group_name'], $record['product_group_code'], $record['product_group_status'], $record['product_group_description'], $record['product_group_order']);
                break;
        }

        return $local_id;
    }

    /**
     * @param $result: add, edit
     * @param $record
     * @param $local_id: local record id (item id)
     */

    static public function sync_download_products($result, $record, $local_id)
    {
        global $CMS;

        // Add product category
        $CMS->class->language->load("store_request");
        $CMS->class->language->load("product");

        self::$msg .= "Status: ".$result.PHP_EOL;

        // Action
        switch ( $result ) {
            case "add":
                // Detect image and upload
                $record = self::sync_download_image($record);
                $local_id = $CMS->product->addQuick($record);
                break;
            case "edit":
                $record = self::sync_download_image($record); // Update image
                self::sync_table_update($record, $local_id); // Update id
                $CMS->class->cache->mdelete($CMS->product->cache_prefix); // Clear cache
                break;
        }

        return $local_id;
    }

    /**
     * @param $result
     * @param $record
     * @param $local_id
     * @return mixed
     */

    static public function sync_download_orders($result, $record, $local_id)
    {
        global $CMS;

        self::$msg .= "Status: ".$result.PHP_EOL;

        $CMS->class->language->load("order");
        $CMS->class->language->load("transactions");

        // Convert ord_item to array
        $record['ord_item'] = input::jsonDecode($record['ord_item']);

        $table_child = self::$field[self::$store_module . "/child"]['ord_item'];
        //$new_data = [];

        // List [0] => [], [1] => []....
        foreach( $record['ord_item'] as $item => $data )
        {
            // List product_id, product_name...
            foreach($table_child as $col => $value)
            {
                // Set new array
                if ( !isset($record[$value])) { $record[$value] = []; }
                //echo $value."|". $new_data[$value]."=>".$data[$value].PHP_EOL;
                // Merge data for array
                array_push($record[$value], $data[$value]);
            }

            //echo PHP_EOL;
        }

        // Set array after re-calculate data
        unset($record['ord_item']);
        //array_push($record, $new_data);

        // Action
        switch ( $result ) {
            case "add":
                $local_id = $CMS->order->quick_add($record); // Quick add
                $local_id = $local_id['ord_id']; // Return ord_id
                break;
            case "edit":
                //self::sync_table_update($record, $local_id); // Update id
                //$CMS->class->cache->mdelete($CMS->product->cache_prefix); // Clear cache
                break;
        }

        return $local_id;
    }

}
