<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->tracking = new ClassTracking;

class ClassTracking { 
	public function info($key=null) {
		if (!is_null($key)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT * FROM `".root_table."order` WHERE `ord_deleted`=0 AND `ord_name`='{$key}' LIMIT 1");
			if ($DB->num_rows()>0) {
				return $DB->fetch_array();
			}
		}
		return array();
	}
	public function anTracking($key=null,$store_id=null) {
		if (!is_null($key)) {
			global $CMS, $DB, $member;
			if (is_null($store_id)) {
				$where=" AND `store_id`='0' ";
			} else {
				$where=" AND `store_id`='".$store_id."' ";
			}
			$DB->query("SELECT `ord_id` FROM `".root_table."order` WHERE `ord_deleted`=0 AND `ord_name`='{$key}' {$where}LIMIT 1");
			if ($DB->num_rows()>0) {
				return true;
			}
		}
		return false;
	}
	public function anEmailTracking($email=null) {
		if (!is_null($email)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `ord_id` FROM `".root_table."order` WHERE `ord_deleted`=0 AND `ord_cus_email`='{$email}' LIMIT 1");
			if ($DB->num_rows()>0) {
				return true;
			}
		}
		return false;
	}
	public function sendEmailTracking($email=null) {
		if (!is_null($email)) {
			global $CMS,$DB,$member;
			$DB->query("SELECT `ord_name`,`ord_time` FROM `".root_table."order` WHERE `ord_deleted`=0 AND `ord_cus_email`='{$email}'");
			if ($DB->num_rows()>0) {
				$arr=array();
				while ($result=$DB->fetch_array()) {
					array_push($arr, $result);
				}
				$html=$CMS->class->template->load_template("skin_tracking");
				$out=$html->sendEmailTracking($arr);
				$CMS->email->email_template=$CMS->vars['email_recover'];
				$CMS->email->email_to=$email;
				$CMS->email->data['ord_body']=$out;
				$CMS->email->quick_send_2();
				return true;
			}
		}
		return false;
	}
	public function editAddress($key=null) {
		if (!is_null($key)) {
			global $CMS,$DB,$member;
			$name=$CMS->input['name'];
			$address=$CMS->input['address'];
			$address02=$CMS->input['address02'];
			$country=$CMS->input['country'];
			$city=$CMS->input['city'];
			$province=$CMS->input['province'];
			$code=$CMS->input['code'];
			
			if (empty($name)) {
				$CMS->error.=$CMS->lang['order_name_error'];
			}
			if (empty($address)) {
				$CMS->error.=$CMS->lang['order_address_error'];
			}
			// if (empty($address02)) {
				// $CMS->error.=$CMS->lang['order_address02_error'];
			// }
			if (empty($country)) {
				$CMS->error.=$CMS->lang['order_country_error'];
			}
			if (empty($city)) {
				$CMS->error.=$CMS->lang['order_city_error'];
			}
			if (empty($province)) {
				$CMS->error.=$CMS->lang['order_town_error'];
			}
			if (empty($code)) {
				$CMS->error.=$CMS->lang['order_code_error'];
			}
			$data=$this->info($key);
			if (empty($CMS->error)&&($name!=$data['ord_cus_name']||$address!=$data['ord_cus_address1']||$address02!=$data['ord_cus_address2']||$country!=$data['ord_cus_country']||$city!=$data['ord_cus_city']||$province!=$data['ord_cus_province']||$code!=$data['ord_cus_postal_code'])) {
				$DB->query("UPDATE `".root_table."order` SET `ord_cus_name`='{$name}', `ord_cus_address1`='{$address}', `ord_cus_address2`='{$address02}', `ord_cus_country`={$country}, `ord_cus_city`='{$city}', `ord_cus_province`='{$province}', `ord_cus_postal_code`='{$code}',`ord_update_time`='".time()."' WHERE `ord_deleted`=0 AND `ord_tracking_status`<3 AND `ord_name`='{$key}'");
				$this->addOrdHis($key,'Change address shipping info');
				$CMS->success.=$CMS->lang['order_edit_success'];
				return true;
			}
		}
		return false;
	}
	public function addOrdHis($key=null,$name=null,$content=null) {
		if (!is_null($key)) {
			global $CMS,$DB, $member;

			$insert=array();
			if (!empty($name)) {
				$insert['ord_his_name']= $CMS->class->editor->replace($name);
			}
			if (!empty($content)) {
				$content=is_array($content)?json_encode($content):(string)$content;
				$insert['ord_his_content']=$content;
			}
			$sql1="`ord_his_key`,`ord_his_time`";
			$sql2="'{$key}',".time();
			foreach ($insert as $k=>$v) {
				$sql1.=",`{$k}`";
				$sql2.=",'{$v}'";
			}

			$sql="INSERT INTO `".root_table."order_history` ({$sql1}) VALUES ({$sql2})";
			$DB->query($sql);
			return $DB->last_insert_id();
		}
		return false;
	}
	public function getOrdHis($key) {
		$return=array();
		if (!is_null($key)) {
			global $CMS,$DB,$member;
			$DB->query("SELECT * FROM `".root_table."order_history` WHERE `ord_his_key`='{$key}' ORDER BY ord_his_id DESC");
			if ($DB->num_rows()>0) {
				while ($result=$DB->fetch_array()) {
					array_push($return,$result);
				}
			}
		}
		return $return;
	}
	public function infoOrdCon($ord_id=null,$cam_id=null) {
		$return=array();
		if (!is_null($key)||!is_null($cam_id)) {
			global $CMS,$DB,$member;
			$return['content']=array();
			$DB->query("SELECT `pcam_id`,(SELECT a.`attr_name` FROM `".root_table."attribute` a WHERE a.`group_type`=2 AND a.`attr_deleted`=0 AND a.`attr_id`=o.`ordc_size` LIMIT 1) as ordc_size, (SELECT a.`attr_name` FROM `".root_table."attribute` a WHERE a.`group_type`=1 AND a.`attr_deleted`=0 AND a.`attr_id`=o.`ordc_color` LIMIT 1) as ordc_color,`ordc_price`,`ordc_quantity` FROM `".root_table."order_content` o WHERE o.`ord_id`={$ord_id}");
			if ($DB->num_rows()>0) {
				while ($result=$DB->fetch_array()) {
					array_push($return['content'],$result);
				}
			}
			$return['cam']=$CMS->campaigns->infoCam($cam_id);
		}
		return $return;
	}

	public function get_html_tracking_for_sell($ord_name='')
	{
		global $CMS, $DB;

		$lis_history = $this->getOrdHis($ord_name);
		$output =<<<EOF
			<header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
			            <div class="tbl-cell">
							<h3>{$CMS->lang['order_title_history']}</h3>
			            </div>
					</div>
				</div>
			</header>
			<section class="card">
			    <section class="box-typical">
			    <ul class="list_history">
EOF;
			foreach ($lis_history as $key => $value) 
			{
				
				$time_show = $CMS->class->date->date_format($value['ord_his_time'], 1);
				$output .=<<<EOF
					<li>
						<span class="time_span">{$time_show}</span>	
						<div class="content_history">
							{$value['ord_his_name']}
						</div>
					</li>
EOF;

			}
$output .=<<<EOF
				</ul>

			    </section>
			</section>

EOF;

		return $output;
	}
}
?>