<?php
if (!defined('IN_ROOT')) exit();

$warehouse = new Warehouse;
$warehouse->autorun();

class Warehouse{
	public $html;
	public function autorun(){
		global $CMS, $DB, $member;
		$CMS->class->language->load("warehouse");
		$CMS->core->page_title = $CMS->lang['warehouse'];
		$this->html = $CMS->class->template->load_template("skin_warehouse");
		$output='';
		switch ($CMS->input['act']){
			default:
				$this->defaultPage();
				break;
			case 'add':
				$this->add();
				break;
			case 'delete':
				$this->delete();
				break;
			case 'edit':
				$this->edit();
				break;
			case 'show':
				$this->show();
				break;
			case 'additem':
				$this->addItem();
				break;
			case 'edititem':
				$this->editItem();
				break;
			case 'deleteditem':
				$this->deletedItem();
				break;
			case 'getcolorsize':
				$this->getColorSize();
				break;
		}
	}
	public function defaultPage() {
		global $CMS, $DB, $member;
		$CMS->output.=$CMS->warehouse->acpListing();
	}
	public function add(){
		global $CMS, $DB, $member;
		if ($CMS->input['request_method']=='post') {
			$name=$CMS->input['name'];
			$note=$CMS->input['note'];
			$check=true;
			if (empty($name)||preg_match("/^[0-9a-zA-Z-_ ]$/",$promotion_code)) {
				$CMS->errormsg=$CMS->lang['section_name_err'];
				$check=false;
			}
			$note=substr($note,0,225);
			if ($check) {
				if ($CMS->warehouse->addSection($name,$note)) {
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=warehouse");
				}
			}
		}
		$CMS->output.=$this->html->add();
	}
	public function delete() {
		global $CMS, $DB, $member;
		$data=$CMS->warehouse->info($CMS->input['id']);
		if (count($data)>0) {
			// $CMS->warehouse->deleted($CMS->input['id']);
		}
		$CMS->global->redirect("{$CMS->vars['http_referer']}");
	}
	public function edit() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data=$CMS->warehouse->info($CMS->input['id']);
			if (count($data)>0) {
				if ($CMS->input['request_method']=='post') {
					$name=$CMS->input['name'];
					$note=$CMS->input['note'];
					$check=true;
					if (empty($name)||preg_match("/^[0-9a-zA-Z-_ ]$/",$promotion_code)) {
						$CMS->errormsg=$CMS->lang['section_name_err'];
						$check=false;
					}
					$note=substr($note,0,225);
					if ($check) {
						if ($CMS->warehouse->editSection($CMS->input['id'],$name,$note)) {
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=warehouse");
						}
					}
				}
				$data['content']=$CMS->warehouse->infoItem($CMS->input['id']);
				if (!empty($CMS->input['ajax'])) {
					die($this->html->edit($data));
				} else {
					$CMS->output.=$this->html->edit($data);
				}
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=warehouse");
	}
	public function show() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data=$CMS->warehouse->info($CMS->input['id']);
			if (count($data)>0) {
				$data['content']=$CMS->warehouse->infoItem($CMS->input['id']);
				$CMS->output.=$this->html->show($data);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=warehouse");
	}
	public function addItem() {
		global $CMS, $DB, $member;
		$arr=array('status'=>'error','message'=>$CMS->lang['add_item_err']);
		if ($CMS->input['request_method']) {
			if (!empty($CMS->input['id'])) {
				$data=$CMS->warehouse->info($CMS->input['id']);
				if (count($data)>0) {
					$name=$CMS->input['name'];
					$note=$CMS->input['note'];
					$quantity=$CMS->input['quantity'];
					$color=$CMS->input['color'];
					$size=$CMS->input['size'];
					$check=true;
					if (!preg_match('/^[0-9]*$/', $name)) {
						$arr['message']=$CMS->lang['add_item_name_worng'];
						$check=false;
					}
					$note=substr($note,0,225);
					if ($quantity<=0) {
						$arr['message']=$CMS->lang['add_item_quantity_worng'];
						$check=false;
					}
					if (!preg_match('/^[0-9]*$/', $color)) {
						$arr['message']=$CMS->lang['add_item_name_worng'];
						$check=false;
					}
					if (!preg_match('/^[0-9]*$/', $size)) {
						$arr['message']=$CMS->lang['add_item_name_worng'];
						$check=false;
					}
					if ($check) {
						if ($CMS->warehouse->addItem($CMS->input['id'],$name,$note,$quantity,$color,$size)) {
							$arr=array('status'=>'success','message'=>$CMS->lang['add_item_success']);
						}
					}
				}
			}
		}
		die(json_encode($arr));
	}
	public function editItem() {
		global $CMS, $DB, $member;
		$arr=array('status'=>'error','message'=>$CMS->lang['edit_item_err']);
		$data=$CMS->warehouse->info($CMS->input['id']);
		if (count($data)>0) {
			$data['content']=$CMS->warehouse->infoItem($data['section_id'],$CMS->input['item']);
			if (count($data['content'])>0) {
				$data['content']=$data['content'][0];
				$name=$CMS->input['item_name'];
				$note=$CMS->input['item_note'];
				$quantity=$CMS->input['item_quantity'];
				$color=$CMS->input['item_color'];
				$size=$CMS->input['item_size'];
				$check=true;
				if (!preg_match('/^[0-9]*$/', $name)) {
					$arr['message']=$CMS->lang['add_item_name_worng'];
					$check=false;
				}
				$note=substr($note,0,225);
				if ($quantity<=0) {
					$arr['message']=$CMS->lang['add_item_quantity_worng'];
					$check=false;
				}
				if (!preg_match('/^[0-9]*$/', $color)) {
					$arr['message']=$CMS->lang['add_item_name_worng'];
					$check=false;
				}
				if (!preg_match('/^[0-9]*$/', $size)) {
					$arr['message']=$CMS->lang['add_item_name_worng'];
					$check=false;
				}
				if ($check) {
					if ($CMS->warehouse->editItem($data['content']['ware_con_id'],$name,$note,$quantity,$color,$size)) {
						$arr=array('status'=>'success','message'=>$CMS->lang['edit_item_success']);
					}
				}
			}
		}
		die(json_encode($arr));
	}
	public function deletedItem() {
		global $CMS, $DB, $member;
		$arr=array('status'=>'error','message'=>$CMS->lang['deleted_item_err']);
		$data=$CMS->warehouse->info($CMS->input['id']);
		if (count($data)>0) {
			$data['content']=$CMS->warehouse->infoItem($data['section_id'],$CMS->input['item']);
			if (count($data['content'])>0) {
				$data['content']=$data['content'][0];
				if ($CMS->warehouse->deletedItem($data['content']['ware_con_id'])) {
					$arr=array('status'=>'success','message'=>$CMS->lang['deleted_item_success']);
				}
			}
		}
		die(json_encode($arr));
	}
	public function getColorSize() {
		global $CMS, $DB, $member;
		$arr=array('color'=>'','size'=>'');
		if (!empty($CMS->input['exp_id'])) {
			$tmp=$CMS->exproduct->get_info($CMS->input['exp_id']);
			if (count($tmp)>0) {
				$color=$CMS->attribute->getColorSize($tmp['group_attr']);
				foreach($color as $k=>$v) {
					$arr['color'].="<option value='{$k}' style='background-color: #{$v[0]}'>{$v[1]}</option>";
				}
				$size=$CMS->attribute->getColorSize($tmp['group_attr_size']);
				foreach ($size as $k=>$v) {
					$arr['size'].="<option value='{$k}'>{$v[1]}</option>";
				}
			}
		}
		die(json_encode($arr));
	}
}