<?php
if (!defined('IN_ROOT')) exit();

$CMS->warehouse = new ModWarehouse();
class ModWarehouse {
	public $show_page=0;
	public $CMS = "";
	public $sql_query = "";
	public $sql_query_bk = "";
	public $arrange_data = "";
	public $record_cnt = 0;
	public $control = 0;
	public $total = 0;
	public $sql_add = "";
	public $action_control = "";
	public $news_project = "";
	public $data_array = array();	 
	public $per_page = 20;
	public $prefix_html = "";
	public $suffix_html = "";
	public $html;
	
	public function loadHtml() {
		global $CMS,$DB,$member;
		if ($CMS->vars['is_admin_module']) {
			$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/shipment/templates/skin_warehouse.php");
		} else {
			$this->html = $CMS->class->template->load_template("skin_warehouse");
		}
	}
	public function addSection($name=null,$note=null) {
		if (!empty($name)) {
			global $CMS, $DB, $member;
			$DB->query("INSERT INTO `".root_table."warehouse` (`time`, `section_name`, `section_note`) VALUES ('".time()."','{$name}','{$note}');");
			$id=$DB->last_insert_id();
			$CMS->class->logs->insert("{$member['cus_username']} create <b>section {$name} ({$id})</b>");
			$_SESSION['msg']="{$CMS->lang['section_add_success']}<strong>{$name} (id: {$id})</strong>";
			return true;
		}
		return false;
	}
	public function acpListing() {
		global $CMS,$DB,$member;
		// $where='';
		// if (isset($CMS->input['code'])) {
			// $where.=" AND `promotion_code` LIKE '%{$CMS->input['code']}%'";
			// $this->prefix_html.="&code={$CMS->input['code']}";
		// }
		// if (isset($CMS->input['user'])) {
			// $where.=" AND `cus_id` IN (SELECT `cus_id` from `".root_table."customer` WHERE `cus_username` LIKE '%{$CMS->input['user']}%')";
			// $this->prefix_html.="&user={$CMS->input['user_id']}";
		// }
		
		// if (isset($CMS->input['type'])&&in_array($CMS->input['type'],['1','0'])) {
			// $where.=" AND `promotion_type`={$CMS->input['type']}";
			// $this->prefix_html.="&type={$CMS->input['type']}";
		// }
		// $this->prefix_html=empty($this->prefix_html)?'':'?site=promotion'.$this->prefix_html.'&page=';
		
		$this->arrange_data = trim("section_id,section_name,time");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "section_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
	 	list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT w.*,SUM(wc.`item_quantity`) as quantity FROM `".root_table."warehouse` w LEFT JOIN `nh_warehouse_content` wc ON wc.`section_id`=w.`section_id` WHERE w.`deleted`=0 GROUP BY w.`section_id` ORDER BY {$default_field} {$default_order}",$this->per_page, $this->prefix_html, $this->suffix_html);
		
		$this->loadHtml();
		$out=$this->html->head();
		if ($DB->num_rows($this->sql_query)>0) {
			while ($data=$DB->fetch_array($this->sql_query)) {
				$out.=$this->html->mid($data);
			}
		} else {
			$out.=$this->html->none();
		}
		$out.=$this->html->foot();
		return $out;
	}
	public function info($id=null,$field=null) {
		if(!empty($id)) {
			global $CMS, $DB, $member;
			$where='';
			if (is_array($id)) {
				foreach ($id as $k=>$v) {
					if(empty($where)) {
						$where.="`{$k}`='{$v}'";
					} else {
						$where.=", `{$k}`='{$v}'";
					}
				}
			} else {
				$where.="`section_id`='{$id}'";
			}
			$field=empty($field)?'*':$field;
			$DB->query("SELECT {$field} FROM `".root_table."warehouse` WHERE `deleted`=0 AND {$where}");
			if ($DB->num_rows()>0) {
				return $DB->fetch_array();
			}
		}
		return array();
	}
	public function infoItem($section_id=null,$id=null,$field=null) {
		$arr=array();
		if(!empty($section_id)) {
			global $CMS, $DB, $member;
			$where='';
			if (is_array($section_id)) {
				foreach ($section_id as $k=>$v) {
					if(empty($where)) {
						$where.="`{$k}`='{$v}'";
					} else {
						$where.=" AND `{$k}`='{$v}'";
					}
				}
			} else {
				$where.="`section_id`='{$section_id}'";
			}
			if (!empty($id)) {
				if (is_array($id)) {
					foreach ($id as $k=>$v) {
						if(empty($where)) {
							$where.="`{$k}`='{$v}'";
						} else {
							$where.=" AND `{$k}`='{$v}'";
						}
					}
				} else {
					if(empty($where)) {
						$where.="`ware_con_id`='{$id}'";
					} else {
						$where.=" AND `ware_con_id`='{$id}'";
					}
				}
			}
			$field=empty($field)?'*':$field;
			$DB->query("SELECT {$field} FROM `".root_table."warehouse_content` WHERE `deleted`=0 AND {$where}");
			if ($DB->num_rows()>0) {
				while ($result=$DB->fetch_array()) {
					array_push($arr,$result);
				}
			}
		}
		return $arr;
	}
	public function addItem($section_id=null,$name=null,$note=null,$quantity=null,$color=null,$size=null) {
		if(!empty($section_id)&& !empty($name)&&$quantity>0) {
			global $CMS,$DB,$member;
			$DB->query("INSERT INTO `".root_table."warehouse_content`(`time`,`section_id`,`item_name`,`item_note`, `item_quantity`, `item_color`, `item_size`) VALUES ('".time()."','{$section_id}','{$name}','{$note}','{$quantity}','{$color}','{$size}')");
			return true;
		}
		return false;
	}
	public function editSection($id=null,$name=null,$note=null) {
		if (!empty($id)&& !empty($name)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."warehouse` SET `section_name`='{$name}',`section_note`='{$note}' WHERE `section_id`='{$id}'");
			$_SESSION['msg']="{$CMS->lang['section_edit_success']}<strong>{$name} (id: {$id})</strong>";
			return true;
		}
		return false;
	}
	public function editItem($ware_con_id=null,$name=null,$note=null,$quantity=null,$color=null,$size=null) {
		if (!empty($ware_con_id)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."warehouse_content` SET `item_name`='{$name}',`item_note`='{$note}',`item_quantity`='{$quantity}',`item_color`='{$color}',`item_size`='{$size}' WHERE `deleted`=0 AND `ware_con_id`='{$ware_con_id}'");
			return true;
		}
		return false;
	}
	public function deletedItem($ware_con_id=null) {
		if (!empty($ware_con_id)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."warehouse_content` SET `deleted`='1' WHERE `deleted`=0 AND `ware_con_id`='{$ware_con_id}'");
			return true;
		}
		return false;
	}
}
?>