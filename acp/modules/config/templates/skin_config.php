<?php
class skin_config {

public function skin_admin_top( $name, $url , $href, $col)
{
	global $CMS;
	
	$output = "";

	$style = $col ? 'style="margin-left:auto;margin-right:auto;float:none;"' : '';

	$output .= <<<EOF
	<section class="add_table main_form {$col}" {$style}>
		<figure class="heading">
			<h3>{$name}</h3>
			<figure class="pull-right right">
			{$href}
		</figure>
	</figure>

	<form method="post" name="config" action="{$CMS->vars['root_domain']}/{$url}" enctype="multipart/form-data">
	<section class="add_table">
EOF;

	return $output;
}

public function skin_admin_add_group( $data = "", $name_act)
{
	global $CMS;
	
	$output = "";
	
	if ( $data['conf_protected'] == 0 )
	{
		$protected[$data['conf_protected']] = "selected";
	}
	else
	{
		$protected[1] = "selected";
	}
	
	$output .= <<<EOF
	<figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			<div class="col-md-12">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['group_name']}</label>
				 	<input class="form-control" type="text" name="title" value="{$data['conf_title']}" emsg="{$CMS->lang['news_incomplete_name']}">
				</fieldset>
			</div>
			<div class="col-md-12">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['group_key']}</label>
				 	<input class="form-control" type="text" name="key" value="{$data['conf_key']}" emsg="{$CMS->lang['news_incomplete_name']}">
				</fieldset>
			</div>
			<div class="col-md-12">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['group_protect']}</label>
					<select class="select2" name="protected">
						<option value="0" {$protected[0]}>{$CMS->lang['no']}</option>
						<option value="1" {$protected[1]}>{$CMS->lang['yes']}</option>
					</select>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<fieldset class="form-group" style="margin-bottom: 0px;">
					<input class="btn btn_add_line" style="margin: 0 auto;display: block;" type="submit" name="submit" value="{$name_act}">
				</fieldset>
			</div>
		</div>
	</figure>
EOF;

	return $output;
}

public function skin_admin_add_setting( $data = "" , $name_act)
{
	global $CMS, $DB;
	
	$output = "";
	
	if ( $data['conf_type'] )
	{
		if ( $data['conf_type'] == "input" )
		{
			$test[2] = "selected";
		}
		else if ( $data['conf_type'] == "textarea" )
		{
			$test[3] = "selected";
		}
		else if ( $data['conf_type'] == "select" )
		{
			$test[4] = "selected";
		}
		else
		{
			$test[1] = "selected";
		}		
	}
	else
	{
		$test[2] = "selected";
	}

	if ( $data['conf_protected'] == 0 )
	{
		$protected[$data['conf_protected']] = "selected";
	}
	else
	{
		$protected[1] = "selected";
	}
	
	$data['conf_value'] = str_replace("<br />", "\n", $data['conf_value']);
	$data['conf_data'] = str_replace("<br />", "\n", $data['conf_data']);
	
	$output .= <<<EOF
	<figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			<div class="col-md-12">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['setting_name']}</label>
				 	<input class="form-control" type="text" name="title" size="50" value="{$data['conf_title']}">
				</fieldset>
			</div>
			<div class="col-md-12">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['setting_key']}</label>
					<input class="form-control" type="text" name="key" value="{$data['conf_key']}">
				</fieldset>
			</div>
			<div class="col-md-6">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['setting_type']}</label>
				 	<select class="select2" name="type">
						<option value="yes_no" {$test[1]}>Yes / No</option>
						<option value="input" {$test[2]}>Text input</option>
						<option value="textarea" {$test[3]}>Textarea</option>
						<option value="select" {$test[4]}>Select</option>
					</select>
				</fieldset>
			</div>
			<div class="col-md-6">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['setting_protect']}</label>
				 	<select class="select2" name="protected">
		                <option value="0" {$protected[0]}>{$CMS->lang['no']}</option>
		                <option value="1" {$protected[1]}>{$CMS->lang['yes']}</option>
		            </select>
				</fieldset>
			</div>
			<div class="col-md-12">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['setting_value']}</label>
					<textarea class="form-control" type="text" rows="5" name="value" style="width:100% !important;">{$data['conf_value']}</textarea>
				</fieldset>
			</div>
			<div class="col-md-12">
				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['setting_data']}</label>
					<textarea class="form-control" type="text" rows="5" name="data" style="width:100% !important;">{$data['conf_data']}</textarea>
				</fieldset>
			</div>
		</div>
		<div class="row">
EOF;

	if ( $CMS->input['set'] )
	{
		$output .= <<<EOF
		<div class="col-md-12">
			<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['setting_group']}</label>
				<select class="input_text" name="new_group" style="width:100%;">
EOF;

		$DB->query("SELECT * FROM ".root_table."conf_settings_titles");
	
		while ( $conf_group = $DB->fetch_array() )
		{
			$output .= <<<EOF
			<option id="conf_{$conf_group['conf_id']}" value="{$conf_group['conf_id']}">{$conf_group['conf_title']}</option>
EOF;

		}
		
		$output .= <<<EOF
				</select>
			</fieldset>
			<script language="javascript">
	    	<!--
	    	if ( '{$CMS->input['group']}' != '' )
			{
				document.getElementById('conf_{$CMS->input['group']}').selected = "selected";
			}
	    	//-->
	    	</script>
		</div>
EOF;

	}

	$output .= <<<EOF
			<div class="col-md-12">
				<fieldset class="form-group" style="margin-bottom: 0px;">
					<input class="btn btn_add_line" style="margin: 0 auto;display: block;" type="submit" name="submit" value="{$name_act}">
				</fieldset>
			</div>
		</div>
	</figure>
EOF;

	return $output;
}

public function skin_admin_group( $data )
{
	global $CMS;
	
	$output = "";

	if ( $data['keyrow'] == '1' )
	{
		$output .= <<<EOF
		<div class="table_cus">
	        <div class="table-responsive">
	            <table>
	                <thead>
	                    <tr role="row">
	                        <th width="90%">Tên Nhóm</th>
							<th width="5%"></th>
	                    </tr>
	                </thead>
	                <tbody id="" class="ui-sortable tbl-typical">
EOF;
	}

	$output .= <<<EOF
	<tr keyrow="{$data['keyrow']}" class="row-grid" rowtr="{$data['rowtr']}">
	    <td class="grid-td" >
	    	<label class="form-control-label"><a href="{$CMS->vars['root_domain']}/?site=config&code=03&group={$data['conf_id']}" style="float:left;">{$data['conf_title']}</a> &nbsp;<font class="desc">[{$data['conf_key']}]</font></label>
	    </td>
	    <td class="grid-td" >
	    	<a href="{$CMS->vars['root_domain']}/?site=config&code=01&group={$data['conf_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;
	if ( $data['conf_protected'] == 0 )
	{
		$output .= <<<EOF
	    <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=config&group={$data['conf_id']}');" class="edit"><i class="fa fa-trash-o"></i></a>
EOF;
	
	}

	$output .= <<<EOF
		</td>
	</tr>
EOF;

	
	if ( $data['rowtr'] != '' )
	{
		$output .= <<<EOF
					</tbody>
	            </table>
	        </div>
	    </div>
EOF;
	}

	return $output;
}

public function skin_admin_setting( $data, $cnt )
{
	global $CMS;

	$output = "";

	if ( $data['keyrow'] == '1' )
	{
		$output .= <<<EOF
		<div class="table_cus">
	        <div class="table-responsive">
	            <table>
	                <thead>
	                    <tr role="row">
	                        <th width="5%">Thứ tự</th>
	                        <th width="25%">Tên</th>
	                        <th width="40%">Giá trị</th>
	                        <th width="25%">Từ saloná</th>
							<th width="5%"></th>
	                    </tr>
	                </thead>
	                <tbody id="" class="ui-sortable tbl-typical">
EOF;
	}

	$output .= "<tr keyrow=\"{$data['keyrow']}\" class=\"row-grid\" rowtr=\"{$data['rowtr']}\">";
  
    // Check previous
	$data['conf_order'] = $data['conf_order'] <= $cnt ? $data['conf_order'] : 1;
        
	if ( $data['conf_order'] <= $CMS->previous_sort )
    {
		$data['conf_order'] = $CMS->previous_sort+1;
	}

 	if ( $data['conf_type'] == "textarea" )
	{
     	$output .= <<<EOF
     	<td class="grid-td table-check">
    		<select class="select2" name="order_{$data['conf_id']}">
        		<script language="javascript">
        		<!--
            	for ( var i = 1; i <= {$cnt}; i ++ )
            	{
                	if ( i == {$data['conf_order']} )
                	{
                    	document.writeln("<option name='option_"+i+"' value='"+i+"' selected>"+i+"</option>");
                	}
                	else
                	{
                    	document.writeln("<option name='option_"+i+"' value='"+i+"'>"+i+"</option>");
                	}
            	}
        		//-->
        		</script>
    		</select>
      	</td>
      	<td colspan="2" class="grid-td">
      		<div style="margin-bottom:10px">{$data['conf_title']}:</div>
      		<div style="clear:both"></div>
     		<textarea class="input_textarea" name="{$data['conf_key']}" cols="40" rows="7" style="width:100%;">{$data['conf_value']}</textarea>
       	</td>
EOF;
	}
	else
	{
        
        $output .= <<<EOF
        <td class="grid-td table-check" style="vertical-align: middle;padding: 11px 10px 10px;border-bottom:1px solid #d8e2e7" width="1%">
        	<select class="select2" name="order_{$data['conf_id']}">
        		<script language="javascript">
        		<!--
            	for ( var i = 1; i <= {$cnt}; i ++ )
            	{
                	if ( i == {$data['conf_order']} )
                	{
                    	document.writeln("<option name='option_"+i+"' value='"+i+"' selected>"+i+"</option>");
                	}
                	else
                	{
                    	document.writeln("<option name='option_"+i+"' value='"+i+"'>"+i+"</option>");
                	}
            	}
        		//-->
        		</script>
        	</select>
        </td>
        <td class="grid-td">{$data['conf_title']}:</td>
EOF;

		$CMS->previous_sort = $data['conf_order'];
        
        if ( $data['conf_type'] == "input" )
        {
        	$output .= <<<EOF
          	<td class="grid-td"><input class="form-control" type="text" name="{$data['conf_key']}" value="{$data['conf_value']}"></td>
EOF;

		}
        else if ( $data['conf_type'] == "yes_no" )
        {
            $select[$data['conf_value']] = "selected";

        	$output .= <<<EOF
          	<td class="grid-td">
          		<select class="select2" name="{$data['conf_key']}">
            		<option value="1" {$select[1]}>Yes</option>
          			<option value="0" {$select[0]}>No</option>
          		</select>
          	</td>
EOF;

		}
        else if ( $data['conf_type'] == "select" )
        {
        	$output .= <<<EOF
          	<td class="grid-td">
          		<select class="select2" name="{$data['conf_key']}">
EOF;

        	$array = explode("<br />", $data['conf_data']);

        	for ( $i = 0; $i < count($array); $i++ )
        	{
            	$option = explode("|", $array[$i]);
        
            	$select[1] = "selected";
            
        		$output .= <<<EOF
            	<option value="{$option[1]}" {$select[$option[2]]}>{$option[0]}</option>
EOF;

			}
        
        	$output .= <<<EOF
            	</select>
          	</td>
EOF;

		}
	}

	$output .= <<<EOF
  	<td class="grid-td"><font style="font-size:13px; font-style:italic">{$data['conf_key']}</font></td>
  	<td class="grid-td">
  		<a href="{$CMS->vars['root_domain']}/?site=config&code=04&group={$data['conf_group']}&set={$data['conf_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;

	if ( $data['conf_protected'] == 0 )
	{
		$output .= <<<EOF
  		<a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=config&code=03&group={$data['conf_group']}&set={$data['conf_id']}')" class="edit"><i class="fa fa-times-circle"></i></a>
EOF;

	}

	$output .= "</td>";
	$output .= "</tr>";

	if ( $data['rowtr'] != '' )
	{
		$output .= <<<EOF
					</tbody>
	            </table>
	        </div>
	    </div>
EOF;
	}
 
	return $output;
}

public function skin_admin_bot( $name )
{
	global $CMS;
	
	$output = "";

	if ( $CMS->input['code'] == 3 OR $CMS->input['code'] == 5 )
	{

		$output .= <<<EOF
        <div class="form-group-bottom">
            <input class="btn btn_add_line" type="submit" name="submit" value="{$name}">
        </div>
EOF;

	}

	$output .= <<<EOF
	                <div class="box_line clearfix">{$CMS->show_page}</div>
	            </div>
	    	</section>
		</form>
	</section>
EOF;

	return $output;
}

}

?>