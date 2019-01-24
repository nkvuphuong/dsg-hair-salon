<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->config_price=new ClassConfigPrice;
class ClassConfigPrice {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";
	public $cache_prefix = "config_price";

	public function listing() {
		global $CMS, $DB, $member;
	 
	 
		$this->arrange_data = trim("product_id,config_price_name,config_price_code,user_id,config_price_time,config_price_status");
		$order = $CMS->input['order'];
		$by = $CMS->input['by'];

		// Accepted keywords
		$list_field = array("product_id", "config_price_name", "config_price_code", "user_id", "config_price_time", "config_price_status");
		$list_by = array("desc","asc");
		// Filter them
		$default_field = in_array($order, $list_field) ? $order : $list_field[0];
		$default_order = in_array($by, $list_by) ? $by : $list_by[0];
		 
 

		$where = '';		
		if (!empty($CMS->input['pg_id'])) {
			$where .= " AND `config_price_id`='{$CMS->input['pg_id']}' ";
		}
		if (!empty($CMS->input['pg_name'])) {
			$pg_name = urldecode($CMS->input['pg_name']);
			$where .= " AND `config_price_name` LIKE '%{$pg_name}%' ";
		}
		if (!empty($CMS->input['pg_type'])) {
			$where .= " AND `config_price_type` = '{$CMS->input['pg_type']}' ";
		}

		$sql = "SELECT * FROM `".root_table."product` WHERE `product_deleted`= 0 {$where} ORDER BY {$default_field} {$default_order}";

        $results = $DB->fetch_data($sql,$this->cache_prefix);

        $arr = [];

        if($results)
        {
            foreach ($results as $result) {
                array_push($arr,$this->convertvalue($result));
            }
        }

		return $arr;
	}
	public function convertvalue($data=null) {
		global $CMS, $DB, $member;
		 
		// $data['config_price_time_c'] = $CMS->class->date->date_format($data['config_price_time'],1);
		// $data['user_id_c'] = $CMS->user->get_info($data['user_id'], 'user_name');

		// $data['config_price_type_bk'] = $CMS->lang["pg_type_{$data['config_price_type']}"];

		// $data['config_price_description_short'] = $CMS->class->editor->substr($data['config_price_description'],0,50);

		$data['product_price_bk'] = $CMS->class->input->currency($data['product_price']); 
		$data['product_price_sell_bk'] = $CMS->class->input->currency($data['product_price_sell']); 
	 
		return $data;
	}
	public function getInfo($record_id = null,$field_name = '*')
    {
        global $CMS, $DB, $member;
	    if(!$record_id) return false;

	    $sql = "SELECT {$field_name} FROM `".root_table."config_price` WHERE `config_price_id`='{$record_id}' AND `config_price_deleted`=0 LIMIT 1";

	    $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if ($field_name !== '*') {
            return $data[$field_name];
        }

        return $data;
	}
	public function get_config_price($product_id = "" ) {
		
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."config_price` WHERE  product_id = '{$product_id}' LIMIT 1";

		return $DB->fetch_data($sql, $this->cache_prefix)[0];
	}

	 

	 
	public function add($pg_parent = null, $pg_name = null, $pg_code = null, $pg_status = null, $pg_description = null) {
			global $CMS, $DB, $member;
		$config_price_type = intval($CMS->input['pg_type']);

		if (!empty($pg_name)  ) {
		
			$sql = $DB->query("INSERT INTO `".root_table."config_price` (`config_price_time`, `user_id`, `config_price_parent`, `config_price_name`, `config_price_code`, `config_price_status`, `config_price_description`, `config_price_type`) VALUES ('".time()."','{$member['user_id']}','{$pg_parent}','{$pg_name}','{$pg_code}','{$pg_status}','{$pg_description}', '{$config_price_type}')");

			//Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$id = $DB->last_insert_id();
 
			$CMS->class->logs->insert("Add_config_price_{$id}");
			return $id;
		}
		return false;
	}
 
	public function save_price_sell() {
 			global $CMS, $DB, $member;
		$product_id = intval($CMS->input['product_id']);
 		$product_price_sell = intval($CMS->input['product_price_sell']);

 		$data_info = $CMS->product->getInfo($product_id);
 		if(!is_array($data_info))
 		{
 			return false;
 		}
 
		$key = "config_price_{$data_info['product_id']}";
		$CMS->class->logs->key = $key;
		$CMS->class->logs->old_data = $data_info;
		$CMS->class->logs->insert($key);



		$DB->query("UPDATE `".root_table."product` SET `product_price_sell`='{$product_price_sell}'  WHERE `product_deleted`='0' AND `product_id`='{$data_info['product_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete($CMS->product->cache_prefix);

		$CMS->class->logs->key = $key;
		$CMS->class->logs->insert("{$CMS->lang['change_price_success']} <b>#{$data_info['product_id']}</b>");	
		$CMS->class->logs->save_detail("config_price",$data_info['product_id'],$CMS->product->getInfo($data_info['product_id']),"product");
	 
		return true;
	}
	public function deleted($id = null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			

			$DB->query("UPDATE `".root_table."config_price` SET `config_price_deleted`=1 WHERE `config_price_id`='{$id}'");

			//Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("Deleted_config_price_{$id}");
		}
		return false;
	}

	public function save_price() {
 		global $CMS, $DB, $member;
		$product_id = intval($CMS->input['product_id']);
		$calc_price_type = intval($CMS->input['calc_price_type']);	// 0: Giá hiện tại , 1: Giá vốn	
 		$calc_type = $CMS->input['calc_type']; // + : plus, - : minus
 		$calc_price = intval($CMS->input['calc_price']);
 		$calc_price = $calc_price >= 0 ? $calc_price : 0;
 		$calc_valuetype =  urldecode($CMS->input['calc_valuetype']);// % or currency minus



 		$data_info = $CMS->product->getInfo($product_id);
 		if(!is_array($data_info))
 		{ 
 			return false;
 		}
 
		$key = "config_price_{$data_info['product_id']}";
		$CMS->class->logs->key = $key;
		$CMS->class->logs->old_data = $data_info;
		$CMS->class->logs->insert($key);


		if($calc_price_type == 0)//Gia hien tại
		{
			 
			$new_product_price_sell = $this->cal_new_price($data_info['product_price_sell'],$calc_type,$calc_price,$calc_valuetype);
			$new_product_price_sell = $new_product_price_sell >= 0 ? $new_product_price_sell : 0;
			 
			$DB->query("UPDATE `".root_table."product` SET `product_price_sell`='{$new_product_price_sell}'  WHERE `product_deleted`='0' AND `product_id`='{$data_info['product_id']}'");

            //Clear cache
            $CMS->class->cache->mdelete($CMS->product->cache_prefix);

		}
		else
		{
			 
			$new_product_price = $this->cal_new_price($data_info['product_price'],$calc_type,$calc_price,$calc_valuetype);
			$new_product_price = $new_product_price >= 0 ? $new_product_price : 0;
			$DB->query("UPDATE `".root_table."product` SET `product_price`='{$new_product_price}'  WHERE `product_deleted`='0' AND `product_id`='{$data_info['product_id']}'");

            //Clear cache
            $CMS->class->cache->mdelete($CMS->product->cache_prefix);
		}
		$new_product = $CMS->product->getInfo($data_info['product_id']);
		$CMS->class->logs->key = $key;
		$CMS->class->logs->insert("{$CMS->lang['change_price_success']} <b>#{$data_info['product_id']}</b>");	
		$CMS->class->logs->save_detail("config_price",$data_info['product_id'],$new_product,"product");
	 
		return $new_product;
	}

 
	function cal_new_price($befor_price = 0, $calc_type = "", $calc_price = "", $calc_valuetype = "" )
	{
		if($calc_type == "plus") // +
		{
			if($calc_valuetype === "%")//Cal percent
			{
				$after_price = $befor_price + ($befor_price * $calc_price/100);
			}
			else
			{
				$after_price = $befor_price + $calc_price;
			}
		}
		else
		{
			if($calc_valuetype === "%")//Cal percent
			{
				$after_price = $befor_price - ($befor_price * $calc_price/100);
			}
			else
			{
				$after_price = $befor_price - $calc_price;
			}
		}
		return $after_price;

	}
}
?>