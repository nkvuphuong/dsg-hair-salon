<?php

class skin_attach {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function attach_header()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<style type="text/css" media="all">@import url({$CMS->vars['css_url']}/style.css);</style>
<style type="text/css">
body
{
		background: {$CMS->attach->backgroundcolor};
}
table
{
	font-size: 11px;
}
</style>
<script language="javascript">
<!--
	var site_parent_domain = "{$CMS->vars['parent_domain']}";
	var site_root_domain = "{$CMS->vars['root_domain']}";
//-->
</script>
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_attach.js"></script>
<table width="100%" cellspacing="0" cellpadding="4" style="border: solid #CCCCCC 1px;">
<tr>
  <td width="1%">{$CMS->lang['attach_id']}</td>
  <td width="50%">{$CMS->lang['attach_name']}</td>
  <td width="15%">{$CMS->lang['attach_size']}</td>
  <td width="24%">{$CMS->lang['attach_time']}</td>
  <td width="1%">{$CMS->lang['delete']}</td>
</tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function attach_middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<tr>
  <td>#{$result['attach_id']}</td>
  <td><p class="align_right" style="color: #333333;">
EOF;

	if ( $result['attach_is_image'] == 1 )
    {  
        list($twidth,$theight) = @getimagesize("{$CMS->vars['upload_dir']}/attach/thumbnail/{$result['attach_location']}");
        list($width,$height) = @getimagesize("{$CMS->vars['upload_dir']}/attach/{$result['attach_location']}");

$output .= <<<EOF
	[<a href="javascript:insert_thumbnail('{$result['attach_name']}', '{$result['attach_location']}', '{$twidth}', '{$theight}');">Thumbnail</a>]&nbsp; 
	[<a href="javascript:insert_image('{$result['attach_name']}', '{$result['attach_location']}', '{$width}', '{$height}');">Image</a>]&nbsp;
EOF;

	}

$output .= <<<EOF
	[<a href="javascript:insert_file('{$result['attach_name']}', '{$result['attach_location']}');">File</a>]&nbsp;
	[<a href="javascript:insert_url('{$result['attach_name']}', '{$result['attach_location']}');">URL</a>]&nbsp;
	</p>
  <a href="{$CMS->vars['upload_url']}/attach/{$result['attach_location']}" target="_blank">{$result['attach_name']}</a>
  </td>
  <td><b>{$result['attach_size']}</b>&nbsp;KB</td>
  <td>{$result['attach_time']}</td>
  <td align="center"><a onclick="javascript:delete_attach('{$result['attach_name']}', '{$result['attach_location']}', '{$twidth}', '{$theight}', '{$width}', '{$height}');" href="{$CMS->vars['root_domain']}/?site=attach&act=delete&id={$result['attach_id']}&referer={$CMS->input['referer']}&mod_name={$CMS->attach->mod_name}&mod_id={$CMS->attach->mod_id}"><img align="absmiddle" src="{$CMS->vars['img_url']}/icon_delete.gif"></a></td>
</tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function attach_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="5">{$CMS->lang['no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function attach_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
</table>
<br />
EOF;

	return $output;
}

//===========================================================================
//  HTML CONTROL
//===========================================================================

public function attach_control( $cnt = 0 )
{
	global $CMS, $DB, $member;
	
	$output = "";

	$get_id = ($CMS->input['referer'] == "edit") ? $CMS->input['id'] : 0;

$output .= <<<EOF
<style type="text/css" media="all">@import url({$CMS->vars['css_url']}/style.css);</style>
<style type="text/css">
body
{
	background: {$CMS->attach->backgroundcolor};
}

table
{
	font-size: 11px;
}
</style>

<form method="post" action="{$CMS->vars['root_domain']}/?site=attach&act=add_do&id={$get_id}&referer={$CMS->input['referer']}&mod_name={$CMS->attach->mod_name}&mod_id={$CMS->attach->mod_id}" enctype="multipart/form-data">
<input class="input_text multi" type="file" name="upload_file[]" accept="jpg|gif|png" multiple="">
<input class="input_submit2" type="submit" name="submit" value="{$CMS->lang['submit']}">
</form>
<script language="javascript">
<!--
	
	if ( parent.document.getElementById("attach_file") )
	{
		if ( '{$cnt}' != '0' )
		{
			parent.document.getElementById("attach_file").height = {$cnt}*35+167;
		}
		else
		{
			parent.document.getElementById("attach_file").height = 27;
		}
	}
	
//-->
</script>
EOF;

	return $output;
}

//===========================================================================
//  HTML FORM
//===========================================================================

public function attach_form( $data )
{
	global $CMS, $DB, $member;
    
    $output = "";
    
	$form_name = "attach_file";

$output .= <<<EOF
	<iframe name="{$form_name}" id="{$form_name}" width="100%" height="auto" scrolling="no" frameborder="0"  src="{$CMS->vars['root_domain']}/?site=attach"></iframe>

	<textarea style="display: none;" name="{$form_name}_data" id="{$form_name}_data">{$data}</textarea>

    <script language="javascript">
	<!--
	
		var attachment = frames["{$form_name}"].document;
		attachment.open();
		attachment.write(document.getElementById("{$form_name}_data").value);
		attachment.close();
		
	//-->
	</script>
EOF;

	
    return $output;
}

//===========================================================================
//  SHOW HEADER
//===========================================================================

public function attach_show( $data = "" )
{
	global $CMS, $DB, $member;
    
    $output = "";

$output .= <<<EOF
<center>
<fieldset style="width: 80%; text-align: left; margin-top: 20px; margin-bottom: 10px;"><legend><b>{$CMS->lang['attachment']}</b></legend>
{$data}
</fieldset>
</center>
EOF;

	return $output;
}

}

?>