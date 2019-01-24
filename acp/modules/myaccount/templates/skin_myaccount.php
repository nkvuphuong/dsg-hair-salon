<?php

class skin_myaccount {

public function account( $data = array() )
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<form method="post" id="myaccount" name="myaccount" action="{$CMS->vars['root_domain']}/?site=myaccount&act=edit" onSubmit="return check_form(this.id);" enctype="multipart/form-data">
<header class="section-header">
	<div class="tbl">
	  <div class="tbl-row">
		<div class="tbl-cell">
		  <h3>{$CMS->lang['header']}</h3>              
		</div>
	  </div>
	</div>
  </header>
	  
	  <section class="card">
	  	<div class="card-block">
			<div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['user_oldpassword']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input" style="margin-bottom:0">    
					<input class="form-control" size="30" type="password" name="old_password" id="old_password" autocomplete="off" value="" emsg="{$CMS->lang['incomplete_oldpassword']}">
                </div>
            </div>
			<div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['user_name']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">{$member['user_name']}</p>
                </div>
            </div>
			<div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['user_display_name']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">    
					{$member['user_display_name']}
                </div>
            </div>
			<div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['user_email']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">    
					<input class="form-control" size="30" type="text" name="user_email" id="user_email" value="{$member['user_email']}" emsg="{$CMS->lang['incomplete_email']}">
                </div>
            </div>
			<div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['user_newpassword']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">    
					<input class="form-control" size="35" type="password" name="user_password" id="user_password" autocomplete="off" maxlength="32" value="" onkeyup="password_change(this);">
					<font id="test"></font>
					<span style="position: absolute; margin-left: 10px; display: none;" id="password_checker">
			<span style="font-size: 9px; line-height: 110%; color: gray;">
			{$CMS->lang['password_strength']}<br />
			<font id="pwd1"><span class="password_1">&nbsp;</span></font>
			<font id="pwd2"><span class="password_1">&nbsp;</span></font>
			<font id="pwd3"><span class="password_1">&nbsp;</span></font>
			<font id="pwd4"><span class="password_1">&nbsp;</span></font>
			<font id="pwd5"><span class="password_1">&nbsp;</span></font>
			</span>
		</span>
                </div>
            </div>
			<div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['user_repassword']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">    
					<input class="form-control" size="35" type="password" name="user_repassword" id="user_repassword" autocomplete="off" maxlength="32" value="">
                </div>
            </div>
			<div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['user_avatar']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">    
					<input class="form-control" type="file" name="user_avatar" size="30">
				</p>
				<div class="list_img_show">{$data['user_avatar']}</div>	
                </div>
            </div>
			<div class="form-group row">
              <label class="col-sm-3 form-control-label hidden-xs"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-noinput">
				 <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['submit']} "> &nbsp; <input class="btn btn-rounded" type="reset" name="submit" value=" {$CMS->lang['reset']} ">
                </p>
              </div>
            </div>
		</div>
			  
	  </section>

</form>
<script language="javascript">rebuild_form("myaccount");</script>
EOF;
	
	return $output;	
}

}

?>