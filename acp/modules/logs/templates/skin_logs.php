<?php

class skin_logs {

public function logs()
{
	global $CMS, $DB, $member;
	
	$output = "";

	$format[0] = $CMS->lang['type_0'];
	$format[1] = $CMS->lang['type_1'];
	
	// Escalate Permission query
	if ( $CMS->vars['is_root'] == 1 )
	{
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE 1=1";
	}
	else if ( $CMS->vars['is_admin'] == 1 )
	{
		//$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE UG.userg_is_root=0";
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE 1=1";
	}
	else
	{
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE UG.userg_is_root=0 AND UG.userg_is_admin=0";
	}
	
	// Check Type
	$type_array = array("user_name", "log_name", "log_time", "log_request", "log_ip_address");

	$content = trim($CMS->input['content']);
	$type = $CMS->input['type'] ? $CMS->input['type'] : "log_name";		
	$orderby = $CMS->input['orderby'] ? $CMS->input['orderby'] : "DESC";

	if ( $CMS->input['act'] == "search" )
	{
		if ( in_array( $type, $type_array ) == false )
		{
			$type = "log_name";
		}

		if ( isset($_SESSION["log_name"]) AND !isset($content) )
		{
			$content = $_SESSION["log_name"];
			$type = $_SESSION["log_type"];
			$orderby = $_SESSION["log_orderby"];
		}

		$_SESSION["log_name"] = $content;
		$_SESSION["log_type"] = $type;
		$_SESSION["log_orderby"] = $orderby;

		if ( $type == "log_time" )
		{
			$content2 = explode("/", $content);
				
			if ( ! $content2[1] )
			{
				$content2[1] = $CMS->class->date->date_get("m", time());
			}
				
			if ( ! $content2[2] )
			{
				$content2[2] = $CMS->class->date->date_get("Y", time());
			}
				
			$content2 = strtotime("{$content2[2]}-{$content2[1]}-{$content2[0]}");

			$sql = "SELECT L.*, U.user_name FROM ".root_table."logs  AS L {$escalate_sql} AND {$type} > {$content2} AND {$type} < ({$content2}+86400) AND log_key NOT LIKE 'sms_%' ORDER BY log_time {$orderby}";
		}
		else
		{
			$sql = "SELECT L.*, U.user_name FROM ".root_table."logs AS L {$escalate_sql} AND {$type} LIKE ('%{$content}%') AND log_key NOT LIKE 'sms_%' ORDER BY log_time {$orderby}";
		}
	}
	else
	{
		$sql = "SELECT L.*, U.user_name FROM ".root_table."logs AS L {$escalate_sql} AND log_key NOT LIKE 'sms_%' ORDER BY log_time {$orderby}";
	}
	
	list($CMS->show_page, $sql) = $CMS->class->page->create($sql, 30);

$output .= <<<EOF
<script language="javascript" src="{$CMS->vars['public_url']}/js/js_boxover.js"></script>
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
			<div class="tbl-cell">
				<h3>Logs info</h3>
			</div>
		</div>
	</div>
</header>
<section class="box-typical">
	<header class="box-typical-header">
        <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
				<h3>List logs</h3>
            </div>
        </div>
    </header>
	<div class="box-typical-body">
		<div class="block_middle">
			<form method="post" name="logs" action="{$CMS->vars['root_domain']}/?site=logs&act=search">
			{$CMS->lang['search']}: <input class="input_text" type="text" id="content" name="content" value="{$content}" size="30">&nbsp;
			<select class="input_text" name="type" defaultvalue="{$type}">
				<option value="user_name">{$CMS->lang['user_name']}</option>
				<option value="log_name" selected>{$CMS->lang['content']}</option>
				<option value="log_request">{$CMS->lang['request']}</option>
				<!--<option value="log_time">{$CMS->lang['time']}</option>-->
				<option value="log_ip_address">{$CMS->lang['ip_address']}</option>
			</select>&nbsp;
			<select class="input_text" name="orderby" defaultvalue="{$orderby}">
				<option value="ASC">A-Z</option>
				<option value="DESC" selected>Z-A</option>
			</select>&nbsp;
			<input class="input_submit" type="submit" name="submit" value="{$CMS->lang['submit']}">
			&nbsp; &nbsp; <img align="absmiddle" title="{$CMS->lang['faq']}" src="{$CMS->vars['img_url']}/icon_notice.gif">
			</form>
		</div>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th width="1%">{$CMS->lang['id']}</th>
						<th width="15%">{$CMS->lang['user_name']}</th>
						<th width="29%">{$CMS->lang['content']}</th>
						<th width="20%">{$CMS->lang['request']}</th>
						<th width="15%">{$CMS->lang['time']}</th>
						<th width="10%">{$CMS->lang['ip_address']}</th>
						<th width="10%">{$CMS->lang['type']}</th>
					</tr>
				</thead>
				<tbody>
EOF;

	$i = 0;

while ( $data = $DB->fetch_array() )
{
		$data['log_ftime'] = $CMS->class->date->date_format( $data['log_time'], 1 );
		$data['log_time'] = $CMS->class->date->date_format( $data['log_time'], 1 );
		$data['log_name'] = $data['log_name'];
		$data['log_tip'] = $data['log_content'];
        
		if ( $data['log_tip'] )
		{
			$data['log_tip'] = " onmouseover=\"document.getElementById('log_{$data[louserg_id]}').style.background='#EEEEEE;'; showtip('{$data[log_tip]}');\" onmouseout=\"document.getElementById('log_{$data[louserg_id]}').style.background='#FCFCFC;'; hidetip();\" ";
		}
		else
		{
			$data['log_tip'] = " onmouseover=\"document.getElementById('log_{$data[louserg_id]}').style.background='#EEEEEE;'\" onmouseout=\"document.getElementById('log_{$data[louserg_id]}').style.background='#FCFCFC;'\" ";
		}
    		
        $data['log_request'] = $CMS->class->logs->print_request( $data['log_request'] );
        
    	$data['log_request'] = substr( $data['log_request'], 0, 50 );
    	
        if ( $i % 2 != 0 ) { $bg_color = "#FFFFFF"; } else { $bg_color = "#F6F6F6"; }
        
$output .= <<<EOF
<tr bgcolor="{$bg_color}">
	<td>#{$data['log_id']}</td>
	<td><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}">{$data['user_name']}</a></td>
    <td id="log_{$data['id']}" {$data['tip']} style="font-size: 10px;">{$data['log_name']}</td>
    <td>{$data['log_request']}</td>
    <td>{$data['log_time']}</td>
    <td><a href="{$CMS->vars['root_domain']}/?site=firewall&folder=logs&act=search&ip={$data['log_ip_address']}">{$data['log_ip_address']}</a></td>
    <td>{$format[$data['log_type']]}</td>
</tr>
EOF;
	
    $i++;
}

$output .= <<<EOF
				</tbody>
			</table>
		</div>
	</div>
	<div class="block_bottom"></div>
</div>
<div class="block_bottom">{$CMS->show_page}</div>
<br />
<script language="javascript">rebuild_form("logs");</script>
EOF;
	
	return $output;	
}





public function detail( $data )
{
	global $CMS, $DB, $member;
    
	$output = "";
    
		$data['log_time'] = $CMS->class->date->date_format( $data['log_time'], 1 );
        $data['log_type'] = $CMS->lang["type_{$data['log_type']}"];
        
        $user = $CMS->user->get_info($data['user_id']);
        $data['user_name'] = $CMS->permit["user_read"] ? "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$user['user_id']}'>{$user['user_display_name']}</a>" : $user['user_display_name'];
        
		if ( $data['log_tip'] )
		{
			$data['log_tip'] = " onmouseover=\"document.getElementById('log_{$data[louserg_id]}').style.background='#EEEEEE;'; showtip('{$data[log_tip]}');\" onmouseout=\"document.getElementById('log_{$data[louserg_id]}').style.background='#FCFCFC;'; hidetip();\" ";
		}
		else
		{
			$data['log_tip'] = " onmouseover=\"document.getElementById('log_{$data[louserg_id]}').style.background='#EEEEEE;'\" onmouseout=\"document.getElementById('log_{$data[louserg_id]}').style.background='#FCFCFC;'\" ";
		}
    		
        $data['log_request'] = $CMS->class->logs->print_request( $data['log_request'] );
        
    	$data['log_request'] = substr( $data['log_request'], 0, 50 );
        
	$output .= <<<EOF
<form method="post" name="logs_detail" id="logs_detail" >
<div class="block_wrapper">
<div class="block_top">
EOF;
	if($CMS->input['type']!= "ajax")
    {
    	$output .=<<<EOF
<p class="align_right">
<a href="{$CMS->vars['root_domain']}/?site=logs{$CMS->class->search->url_return}">&laquo; {$CMS->lang['header_back']}</a>
</p>
EOF;
	}    $output .=<<<EOF
{$CMS->lang['logs_detail']} <b>{$data['log_id']}</b></div>
<div class="block_mid">
<div class="table-responsive">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['user_name']}</b>:</td>
    <td>{$data['user_name']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['content']}</b>:</td>
    <td>{$data['log_name']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['request']}</b>:</td>
    <td>{$data['log_request']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['time']}</b>:</td>
    <td>{$data['log_time']}</td>
  </tr>
   <tr>
    <td class="left25"><b>{$CMS->lang['ip_address']}</b>:</td>
    <td>{$data['log_ip_address']}</td>
  </tr>
    <tr>
    <td class="left25"><b>{$CMS->lang['type']}</b>:</td>
    <td>{$data['log_type']} </td>
  </tr>
  
  <tr>
  	<td class="left25"><b>{$CMS->lang['log_detail_change']}</b>:</td>
    <td>{$data['log_content']}</td>
  </tr>
  </table>
</div>  
</div>
</div>
</form>
<script language="javascript">rebuild_form("logs_detail")</script>

EOF;

	return $output;	
}



}

?>