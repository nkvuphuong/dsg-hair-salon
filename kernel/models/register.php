<?php
$CMS->mod_register = new ModRegister;

class ModRegister{
	public function add(){
		global $CMS, $DB, $member;
		$reg_email=$CMS->input['reg_email'];
		$reg_pass=$CMS->class->editor->input('reg_pass');
		$reg_conpass=$CMS->class->editor->input('reg_conpass');
		if (!filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
			$CMS->errormsg="{$CMS->lang['reg_err_email']}";
			return FALSE;
		}
		if (strlen($reg_pass)<6) {
			$CMS->errormsg="{$CMS->lang['reg_err_pass']}";
			return FALSE;
		}
		if ($reg_pass!=$reg_conpass) {
			$CMS->errormsg="{$CMS->lang['reg_err_conpass']}";
			return FALSE;
		}
		$DB->query("SELECT `cus_username` FROM `".root_table."customer` WHERE cus_username = '{$reg_email}'");	// AND cus_deleted=0 
		if($DB->num_rows()>0) {
			$CMS->errormsg="{$CMS->lang['reg_err_email_exist']}";
			return FALSE;
		}

		$CMS->input['cus_username']=$reg_email;
		$CMS->input['cus_email']=$reg_email;
		$CMS->input['cus_password']=$reg_pass;
		$CMS->input['cus_repassword']=$reg_conpass;
		$CMS->input['cus_phone']='0909123456';
		$CMS->input['country_id']='0';
		$CMS->input['city_id']='0';
		$CMS->input['district_id']='0';
		$CMS->input['town_id']='0';
		$CMS->input['cus_status']='1'; //1:active
		if	($CMS->customer->add()) {
			$CMS->class->cache->delete('cus_'.$reg_email);
			$member = $CMS->customer->cus_check_exist($reg_email);
			$CMS->customer->folderTmp($member);
			$CMS->customer->cus_do_in($member);
			
			$referer_url = $CMS->class->filter->clean_value($CMS->input["referer"]);
			if ($referer_url AND preg_match("/(login|logout)/", $referer_url) == false )
			{
				$page = str_replace("&amp;", "&", $referer_url);
				$page = str_replace("&amp;", "&", $page);
			}
			else
			{
				$page = $CMS->vars['root_domain'];
			}
			$_SESSION['msg']=$CMS->lang['reg_msg'];
			$CMS->global->page_transfer($CMS->lang['login_msg'], $page);
		}
		return false;	
	}
}
?>