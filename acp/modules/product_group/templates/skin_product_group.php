<?php
class skin_product_group {
	public function head() {
		global $CMS, $DB, $member;
		$pg_id_search = $CMS->input['pg_id'];
		$pg_name_search = $CMS->input['pg_name'];
		if(isset($CMS->input['pg_type']) AND $CMS->input['pg_type'] == 0)
		{
			$active_0 = " active "; $active_all = " "; $active_1 = " ";
		}
		elseif(isset($CMS->input['pg_type']) AND $CMS->input['pg_type'] == 1)
		{
			$active_1 = " active "; $active_all = " "; $active_0 = " ";
		}
		else
		{
			$active_1 = " "; $active_all = " active "; $active_0 = " ";
		}

		$out = '';
		 
		$out .= <<<EOF

<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['pg_title']}</h3>
		<figure class="pull-right right">
			 	<div class="search">
				<form method="post"  action="{$CMS->vars['root_domain']}/?site=product_group&act=search" style="display:inline-block">

						<input type="submit" class="fa-input" value="&#xf002;">
						<input type="text"  name="pg_name_search" minlength="2" maxlength="64"  id="pg_name_search"  placeholder="{$CMS->lang['gsearch_quick']}">
					</form>
					<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
				</div>

                {$CMS->global->importExportData('product_group', "&export_type={$export_type}", 1)}
		    
			<a  onclick="return add_sub_pgroup('','',event);" title="" class="add_bill">{$CMS->lang['pg_add_button']}</a>
			<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
		</figure>
	</figure>
EOF;

	$out .= <<<EOF
 	<!-- Tabs head -->
    <section class="box-heading box-status box-filternav" style="padding-bottom: 0;margin-bottom: 0;">
        <div class="box-heading-body">
            <ul class="nav-filter clearfix">
EOF;

			if( $CMS->input['pg_type']  == "" )
			{
		    	$totalGroupAll = $CMS->product_group->countProductGroupByType()*1;
		    	$totalGroup0 = $CMS->product_group->countProductGroupByType(0)*1;
		    	$totalGroup1 = $CMS->product_group->countProductGroupByType(1)*1;

				$out .= <<<EOF
                <li class="{$active_all}">
                    <a href="{$CMS->vars['root_domain']}/?site=product_group">{$totalGroupAll}<span>{$CMS->lang['pg_type_all']}</span></a>
                </li>
                <li class="{$active_0}">
                    <a href="{$CMS->vars['root_domain']}/?site=product_group&pg_type=0">{$totalGroup0}<span>{$CMS->lang['pg_type_0']}</span></a>
                </li>

                <li class="{$active_1}">
                    <a href="{$CMS->vars['root_domain']}/?site=product_group&pg_type=1">{$totalGroup1}<span>{$CMS->lang['pg_type_1']}</span></a>
                </li>
EOF;
            }
			elseif(intval($CMS->input['pg_type']) == 0)
			{
				$totalProduct = $CMS->product->countProductByType($CMS->input['pg_type'])*1;
		    	$totalGroup = $CMS->product_group->countProductGroupByType($CMS->input['pg_type'])*1;
		    	$totalCommission = $CMS->product->countCommissionByType($CMS->input['pg_type'])*1;

				$out .= <<<EOF
                <li class="">
                    <a href="{$CMS->vars['root_domain']}/?site=product">{$totalProduct}<span>{$CMS->lang['product_list']}</span></a>
                </li>
                <li class="active">
                    <a href="{$CMS->vars['root_domain']}/?site=product_group&pg_type=0">{$totalGroup}<span>{$CMS->lang['category_product_list']}</span></a>
                </li>
EOF;
				if($CMS->vars['enabled_commission'])
                {
                	$out .= <<<EOF
	                <li class="">
	                    <a href="{$CMS->vars['root_domain']}/?site=product&commission=1">{$totalCommission}<span>{$CMS->lang['gcommission']}</span></a>
	                </li>
EOF;
				}
			}
			elseif(intval($CMS->input['pg_type']) == 1)
			{
				$totalProduct = $CMS->product->countProductByType($CMS->input['pg_type'])*1;
		    	$totalGroup = $CMS->product_group->countProductGroupByType($CMS->input['pg_type'])*1;
		    	$totalCommission = $CMS->product->countCommissionByType($CMS->input['pg_type'])*1;

				$out .= <<<EOF
                <li class="">
                    <a href="{$CMS->vars['root_domain']}/?site=service">{$totalProduct}<span>{$CMS->lang['service_list']}</span></a>
                </li>
                <li class="active">
                    <a href="{$CMS->vars['root_domain']}/?site=product_group&pg_type=1">{$totalGroup}<span>{$CMS->lang['category_service_list']}</span></a>
                </li>
EOF;
				if($CMS->vars['enabled_commission'])
                {
                	$out .= <<<EOF
	                <li class="">
	                    <a href="{$CMS->vars['root_domain']}/?site=service&commission=1">{$totalCommission}<span>{$CMS->lang['gcommission']}</span></a>
	                </li>
EOF;
				}
			}

            $out .= <<<EOF
            </ul>
        </div>
    </section>
EOF;

$link = isset($CMS->input['pg_type']) ? "&pg_type=".$CMS->input['pg_type'] : "";
$out .=<<<EOF
<section class="add_table" style="margin-top: 15px !important;">
	<div class="data_table">
		<form name="product_group" id="product_group" method="POST" action="{$CMS->vars['root_domain']}/?site=product_group{$link}"  >

			<table id="example" class="display table table_cus" cellspacing="0" width="100%">
				<thead>
					 
					<tr>

EOF;

					if($_SESSION['is_mobile'] == true)
					{
						$out .=<<<EOF
					
						<th  width="1%"  data-sortable="false"  data-orderable="false"  ></th>
						<th data-orderable="false" data-sortable="false" width="1%" >
		                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('product_group');" id="checkall">
			                   	<input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
			                   	<label></label>
		                    </div>
		                </th>
						<th width="10%"  data-sortable="true" >{$CMS->lang['pg_name']}</th>
						<th  width="3%"   data-orderable="false"  >ID</th>
EOF;
					}
					else
					{
						$out .=<<<EOF
						<th data-orderable="false" data-sortable="false" width="1%">
			                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('product_group');" id="checkall">
				                   	<input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
				                   	<label></label>
			                    </div>
			            </th>
						<th  width="3%"  data-orderable="false"  >ID</th>
						<th width="10%"  data-sortable="true" >{$CMS->lang['pg_name']}</th>
EOF;
					}
					$out .=<<<EOF
						
						<th  width="10%"  data-orderable="false"  >{$CMS->lang['pg_code']}</th>
						<th width="7%"  data-orderable="false" >{$CMS->lang['pg_type']}</th>
						<th width="12%"  data-orderable="false" >{$CMS->lang['pg_description']}</th>
	 
						<th width="6%" data-orderable="false" >{$CMS->lang['pg_status']}</th>
						<th width="5%"  data-orderable="false"  data-sortable="false">{$CMS->lang['pg_child']}</th>
						<th width="6%"  data-orderable="false"> </th>
		 
					</tr>
				</thead>
				<tbody>
EOF;
		return $out;
	}
	public function foot() {
		global $CMS, $DB, $member;

		if($CMS->permit['product_group_delete'])
		{
			$option = "<option value='delete_all'>{$CMS->lang['title_delete_all']}</option>";
		}
		$out .= <<<EOF
					</tbody>
				</table>
				<input type="hidden" name="data_cnt" value="{$CMS->product_group->record_cnt}">
				<div class="fuction_table">
					<div class="pull-left">
						<p class="form-control-static ">
							<select class="form-control" name="act" onchange="return check_submit_form('{$CMS->lang['gnotice_confirm_action']}', 'product_group', this);" defaultvalue="delete_all">
								<option value="">{$CMS->lang['choose_action']}</option>
								{$option}
							</select>
						</p>
					</div>
					<nav class="pull-right">
					   {$CMS->product_group->show_page}
					</nav>
				</div> 
			</form>
		</div><!--.box-typical-body-->
		
	 </section>
	
		 
</section>
<script>
var lang_pg_add = "{$CMS->lang['pg_add']}";
var lang_pg_edit = "{$CMS->lang['pg_edit']}";

</script>


<script src="{$CMS->vars['js_acp']}/product_group.js"></script>

EOF;
	if($_SESSION['is_mobile'] == true)
	{	 
		$out .=<<<EOF

	  <script>
						$(function() {
							$('#example').DataTable({
								 order: [],
							responsive: true,
							language: {
								      emptyTable: 'Not found'
								    },
						    columnDefs: [
						        { responsivePriority: 1, targets: 1 },
						        { responsivePriority: 2, targets: 2 },
						        
						    ],
					 		  paging: false,
								  searching: false,
								  info: false
							});
						});
					</script>			
EOF;
	}
	
	$out .=<<<EOF

<script language="javascript">arrange_setup("{$CMS->product_group->arrange_data}");</script>
{$CMS->global->formProductgroup("{$CMS->vars['root_domain']}/?site=product_group")}

EOF;
		return $out;
	}
	public function mid($data=null) {
		global $CMS, $DB, $member;
		$link = isset($CMS->input['pg_type']) ? "&pg_type=".$CMS->input['pg_type'] : "";
		$out = <<<EOF
					<tr>
						
EOF;
				if($_SESSION['is_mobile'] == true)
				{
						$out .=<<<EOF

						<td></td>
						<td>
							<div class="checkbox checkbox-only">
		                      	<input type="checkbox"  name="id_{$data['record_cnt']}" id="id_{$data['record_cnt']}" value="{$data['product_group_id']}"/>
		                      	<label for="id_{$data['record_cnt']}"></label>
						    </div>
						</td>
						<td class="add_pro">
							<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$data['product_group_id']}">
								{$data['product_group_name']}
							</a>

						</td>
						<td>
							#{$data['product_group_id']}
						</td>
EOF;
				}
				else
				{
				$out .=<<<EOF
						<td>
							<div class="checkbox checkbox-only">
		                      	<input type="checkbox"  name="id_{$data['record_cnt']}" id="id_{$data['record_cnt']}" value="{$data['product_group_id']}"/>
		                      	<label for="id_{$data['record_cnt']}"></label>
						    </div>
						</td>

						<td>
							#{$data['product_group_id']}
						</td>
						<td class="add_pro">
							<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$data['product_group_id']}">
								{$data['product_group_name']}
							</a>

						</td>
EOF;
				}
				$out .=<<<EOF
						
						<td>
							{$data['product_group_code']}
						</td>
						<td>
							{$data['product_group_type_bk']}
						</td>
						<td>
							 
							{$CMS->class->editor->substr($data['product_group_description'],0,30)}
						</td>
					 
						<td>
							{$data['product_group_statupg_c']}
						</td>
						<td>
EOF;
					if($data['product_group_parent'] == 0)
					{
						$out .=<<<EOF
							 
				 
                            <a onclick="return add_sub_pgroup({$data['product_group_id']},{$data['product_group_type']},event);"  class="edit">
								<i class="fa fa-plus"  ></i>	
							</a>
                   
EOF;

					}
				 
					$out .=<<<EOF
						</td>
						<td align="center">
EOF;
						if($CMS->permit['product_group_edit'] == 1)
						{
							$out .=<<<EOF
							<a onclick="return edit_sub_pgroup({$data['product_group_id']},event);"  title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

						}

 
						if($CMS->permit['product_group_delete'] == 1)
						{
							$out .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=product_group&act=delete{$link}&id={$data['product_group_id']}');"  class="edit"><i class="fa fa-trash-o"></i></a>

EOF;

						}
					$out .=<<<EOF
						</td>
					</tr>
EOF;

		// Get child product
		// if($data['product_group_parent'] == 0)
		// {

			$child = $CMS->product_group->countitem_group($data['product_group_id'],1, intval($CMS->input['pg_type']));
			
			if(count($child) > 0)
			{
				foreach ($child as $key => $value) 
				{
					$value = $CMS->product_group->convertvalue($value);
					$out .= "<tr>";
					if($_SESSION['is_mobile'] == true)
					{
						$out .=<<<EOF
							<td></td>
							<td>
								<div class="checkbox checkbox-only">
			                      	<input type="checkbox"  name="id_{$value['record_cnt']}" id="id_{$value['record_cnt']}" value="{$value['product_group_id']}"/>
			                      	<label for="id_{$value['record_cnt']}"></label>
							    </div>
							</td>
							<td>
								<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$value['product_group_id']}">
									|__ {$value['product_group_name']}
								</a>
							</td>
							<td>#{$value['product_group_id']}</td>
EOF;
					}
					else
					{
						$out .=<<<EOF
							<td>
								<div class="checkbox checkbox-only">
			                      	<input type="checkbox"  name="id_{$value['record_cnt']}" id="id_{$value['record_cnt']}" value="{$value['product_group_id']}"/>
			                      	<label for="id_{$value['record_cnt']}"></label>
							    </div>
							</td>
							<td>
								#{$value['product_group_id']}
							</td>
							<td>
								<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$value['product_group_id']}">
									|__ {$value['product_group_name']}
								</a>
							</td>

EOF;
					}
					$out .=<<<EOF
							<td>{$value['product_group_code']}</td>
							<td>{$value['product_group_type_bk']}</td>
							<td>{$CMS->class->editor->substr($value['product_group_description'],0,30)}</td>
							<td>{$value['product_group_statupg_c']}</td>
							<td></td>
							<td align="center">
EOF;
						if($CMS->permit['product_group_edit'] == 1)
						{
							$out .=<<<EOF
							<a onclick="return edit_sub_pgroup({$value['product_group_id']},event);" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

						}

						if($CMS->permit['product_group_delete'] == 1)
						{
							$out .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=product_group&act=delete&id={$value['product_group_id']}');"  class="edit"><i class="fa fa-trash-o"></i></a>
EOF;

						}
					$out .=<<<EOF
						</td>
					</tr>		
EOF;

				// }

			// output cap 3
			$child2 = $CMS->product_group->countitem_group($value['product_group_id'],1, intval($CMS->input['pg_type']));
			
			if(count($child2) > 0)
			{
				foreach ($child2 as $data_group) 
				{
					$data_group = $CMS->product_group->convertvalue($data_group);
					$out .= "<tr>";
					if($_SESSION['is_mobile'] == true)
					{
						$out .=<<<EOF
							<td></td>
							<td>
								<div class="checkbox checkbox-only">
			                      	<input type="checkbox"  name="id_{$data_group['record_cnt']}" id="id_{$data_group['record_cnt']}" value="{$data_group['product_group_id']}"/>
			                      	<label for="id_{$data_group['record_cnt']}"></label>
							    </div>
							</td>
							<td>
								<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$data_group['product_group_id']}">
									&nbsp; &nbsp; &nbsp; &nbsp;|__ {$data_group['product_group_name']}
								</a>
							</td>
							<td>#{$data_group['product_group_id']}</td>
EOF;
					}
					else
					{
						$out .=<<<EOF
							<td>
								<div class="checkbox checkbox-only">
			                      	<input type="checkbox"  name="id_{$data_group['record_cnt']}" id="id_{$data_group['record_cnt']}" value="{$data_group['product_group_id']}"/>
			                      	<label for="id_{$data_group['record_cnt']}"></label>
							    </div>
							</td>
							<td>
								#{$data_group['product_group_id']}
							</td>
							<td>
								<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$data_group['product_group_id']}">
									&nbsp; &nbsp; &nbsp; &nbsp;|__ {$data_group['product_group_name']}
								</a>
							</td>

EOF;
					}
					$out .=<<<EOF
							<td>{$data_group['product_group_code']}</td>
							<td>{$data_group['product_group_type_bk']}</td>
							<td>{$CMS->class->editor->substr($data_group['product_group_description'],0,30)}</td>
							<td>{$data_group['product_group_statupg_c']}</td>
							<td></td>
							<td align="center">
EOF;
						if($CMS->permit['product_group_edit'] == 1)
						{
							$out .=<<<EOF
							<a onclick="return edit_sub_pgroup({$data_group['product_group_id']},event);" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

						}

						if($CMS->permit['product_group_delete'] == 1)
						{
							$out .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=product_group&act=delete&id={$data_group['product_group_id']}');"  class="edit"><i class="fa fa-trash-o"></i></a>
EOF;

						}
					$out .=<<<EOF
						</td>
					</tr>		
EOF;

				// }
					
					// output cap 4
					$child3 = $CMS->product_group->countitem_group($data_group['product_group_id'],1, intval($CMS->input['pg_type']));
				
					if ( count($child3) > 0 )
					{
						foreach ($child3 as $data_group2) 
						{
							$data_group2 = $CMS->product_group->convertvalue($data_group2);
							$out .= "<tr>";
							if($_SESSION['is_mobile'] == true)
							{
								$out .=<<<EOF
								<td></td>
								<td>
									<div class="checkbox checkbox-only">
				                      	<input type="checkbox"  name="id_{$data_group2['record_cnt']}" id="id_{$data_group2['record_cnt']}" value="{$data_group2['product_group_id']}"/>
				                      	<label for="id_{$data_group2['record_cnt']}"></label>
								    </div>
								</td>
								<td>
									<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$data_group2['product_group_id']}">
										&nbsp; &nbsp; &nbsp; &nbsp;|__ {$data_group2['product_group_name']}
									</a>
								</td>
								<td>#{$data_group2['product_group_id']}</td>
EOF;
							}
							else
							{
								$out .=<<<EOF
								<td>
									<div class="checkbox checkbox-only">
				                      	<input type="checkbox"  name="id_{$data_group2['record_cnt']}" id="id_{$data_group2['record_cnt']}" value="{$data_group2['product_group_id']}"/>
				                      	<label for="id_{$data_group2['record_cnt']}"></label>
								    </div>
								</td>
								<td>
									#{$data_group2['product_group_id']}
								</td>
								<td>
									<a href="{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$data_group2['product_group_id']}">
										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|__ {$data_group2['product_group_name']}
									</a>
								</td>

EOF;
							}

							$out .=<<<EOF
							<td>{$data_group2['product_group_code']}</td>
							<td>{$data_group2['product_group_type_bk']}</td>
							<td>{$CMS->class->editor->substr($data_group2['product_group_description'],0,30)}</td>
							<td>{$data_group2['product_group_statupg_c']}</td>
							<td></td>
							<td align="center">
EOF;
							if($CMS->permit['product_group_edit'] == 1)
							{
								$out .=<<<EOF
								<a onclick="return edit_sub_pgroup({$data_group2['product_group_id']},event);" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

							}

							if($CMS->permit['product_group_delete'] == 1)
							{
								$out .=<<<EOF
								<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=product_group&act=delete&id={$data_group2['product_group_id']}');"  class="edit"><i class="fa fa-trash-o"></i></a>
EOF;

							}
							$out .=<<<EOF
							</td>
						</tr>		
EOF;

						}// End for 3
					}// End if 3
				
				}// End for 2
			}// End if 2
				
		}// End for 1
	}// End3if 1
		return $out;
}
	public function none() {
		global $CMS, $DB, $member;
		$out = "";
		if($_SESSION['is_mobile'] != true)
		{
		$out = <<<EOF
					<tr>
						<td colspan="9">
							<center><p style="font-size:14px">{$CMS->lang['not_data']}</p></center>
						</td>
					</tr>
EOF;
		}
		return $out;
	}
	public function add() {
		global $CMS, $DB, $member;

		//Check exit param parent_id
		if(isset($CMS->input['parent_id']) )
		{
			$parent = $CMS->product_group->getInfo($CMS->input['parent_id']);

		}

		$pg_parent = isset($CMS->input['pg_parent']) ? $CMS->input['pg_parent'] : '';
		$pg_name = isset($CMS->input['pg_name']) ? $CMS->input['pg_name'] : '';
		$pg_code = isset($CMS->input['pg_code']) ? $CMS->input['pg_code'] : '';
		$pg_status = isset($CMS->input['pg_status']) ? $CMS->input['pg_status'] : 1;
		$pg_description = isset($CMS->input['pg_description']) ? $CMS->input['pg_description'] : '';
		
		$option_pg_parent = "<option value=''>{$CMS->lang['select']}</option>";

		$pg_type = isset($CMS->input['pg_type']) ? $CMS->input['pg_type'] : 0;

		if(isset($CMS->input['parent_id']) )
		{
			//Overwrite default field follow parent
			$pg_parent = $parent['product_group_id'];
			$pg_type = $parent['product_group_type'];
		}


		$option_pg_type ="";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $pg_type) {
				$option_pg_type .= "
     <div class='radio w25'><input type='radio' checked  name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	

				";
			} else {
				$option_pg_type .= "  <div class='radio w25'><input type='radio' name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	";
			}
		}

	
		$category = $CMS->product_group->getParent_bytype($pg_type);
		foreach ($category as $c) {
			if ($c['product_group_id'] == $pg_parent) {
				$option_pg_parent .= "<option value='{$c['product_group_id']}' selected>{$c['product_group_name']}</option>";
			} else {
				$option_pg_parent .= "<option value='{$c['product_group_id']}' >{$c['product_group_name']}</option>";
			}
		}
		$option_pg_status = "";
		for ($i = 1; $i >= 0; $i--) {
			if ($i == $pg_status) {
				$option_pg_status .= "   <div class='radio w25'><input type='radio' checked  name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			} else {
				$option_pg_status .= "   <div class='radio w25'><input type='radio'    name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			}
		}
		$out = '';
		 
	$out .= <<<EOF

 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=product_group&act=add" method="POST" enctype="multipart/form-data">
  <section class="add_form main_form"> 
	<figure class="heading">
		<h3>{$CMS->lang['pg_add']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site=product_group" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

   <figure class="box-typical box-typical box-typical-padding border">
 
 
	<div class="row">
 



	 	<div class="col-md-6">
	 		<div class="row">
			 	<div class="col-xl-6">

			 		<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_parent']}</label>
								<select class="form-control" name="pg_parent" id="pg_parent" >
									{$option_pg_parent}
								</select>
							
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_name']} <span style="color:red">(*)</span></label>
							 <div class="form-control-wrapper">	
								<input class="form-control" type="text" name="pg_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_name_err']}" value="{$pg_name}" onfocusout="generate_pg_code(this,'#pg_code');"  >
							</div>	
							
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_code']} <span style="color:red">(*)</span></label>
							 <div class="form-control-wrapper">	
								<input class="form-control " type="text" name="pg_code" id="pg_code" maxlength="3" data-validation="[NOTEMPTY,L<=3]" data-validation-message="{$CMS->lang['pg_code_err']}. Nhóm sản phẩm tối đa 3 ký tự! " value="{$pg_code}">
							</div>	
							
					</fieldset>
				</div><!-- col-xl-6-->
				 
			 	<div class="col-xl-6">
			 		<fieldset class="form-group">
						<label class="form-label  pull-left" >{$CMS->lang['pg_avartar']}</label>
									 <div class="actionButtons pull-right">
		                                <ul>
		                                    <li onclick="return performClick('ufile');">
		                                        <i tabindex="0" class="fa fa-pencil" ></i>
		                                    </li>
		                                    <li>
		                                        <span class="text-left">|</span>
		                                    </li>
		                                    <li onclick="return delete_fileToAttach();">
		                                        <i class="fa fa-trash-o"></i>
		                                    </li>
		                                </ul>
										<input  type="hidden" id="ufile_output_b64" name="base64_image" />
									  </div>	
							 
								    <div class="drop-zone fileinput-button">
			                              <img id="upload_img_show" width="205" />

			                              <i class="font-icon font-icon-cloud-upload-2"></i>
			                               <div class="drop-zone-caption">Drag file to upload</div>
			                                 <input type="file"  name="pg_avartar" id="ufile" accept="image/*">
			                         </div><!--.drop-zone-->
			                  			
			                        
			                 

					</fieldset>
			 	</div><!-- col-xl-6-->
			 	
			</div> 	<!-- row-->	
		  

	 	</div><!-- col-md-6 -->
	 	


	 	<div class="col-md-6">
	 		 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_description']}</label>
	
						<textarea rows="3" class="form-control" name="pg_description" id="pg_description" >{$pg_description}</textarea> 
				 
			</fieldset>
	 		 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_type']}</label>
						 
							{$option_pg_type}

			</fieldset>
			
			<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_status']}</label>	 
							{$option_pg_status}
				
			</fieldset>
			
			
	 	</div>	<!-- col-md-6 -->

	
		
		

	 

	</div>

	</figure>
</section>
	<section class="add_cart_footer">
		 
 
		 {$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=product_group"))}
		 {$CMS->global->footer_save()}

		</section>

 
</form>
 <script>
 $(document).ready(function(){

	validate_form_custom("#form-signin_v1",".act_submit_save");
});

</script>
 
 <script src="{$CMS->vars['js_acp']}/product_group.js"></script>
EOF;
		return $out;
	}
	public function edit($data=null) {
		global $CMS, $DB, $member;
		$pg_parent = isset($CMS->input['pg_parent']) ? $CMS->input['pg_parent'] : $data['product_group_parent'];
		$pg_name = isset($CMS->input['pg_name']) ? $CMS->input['pg_name'] : $data['product_group_name'];
		$pg_code = isset($CMS->input['pg_code']) ? $CMS->input['pg_code'] : $data['product_group_code'];
		$pg_status = isset($CMS->input['pg_status']) ? $CMS->input['pg_status'] : $data['product_group_status'];
		$pg_description = isset($CMS->input['pg_description']) ? $CMS->input['pg_description'] : $data['product_group_description'];
		$pg_avartar = isset($data['product_group_avatar']) ? $data['product_group_avatar'] :'no-img.jpg';
		$pg_avartar = file_exists($CMS->vars['upload_dir'].'/product/'.$pg_avartar) ? $CMS->vars['upload_url'].'/product/'.$pg_avartar : $CMS->vars['upload_url'].'/product/no-img.jpg';
		$option_pg_parent = "<option value=''>{$CMS->lang['select']}</option>";


		$pg_type = isset($CMS->input['pg_type']) ? $CMS->input['pg_type'] : $data['product_group_type'];
 
		$option_pg_type ="";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $pg_type) {
				$option_pg_type .= "
     <div class='radio w25'><input type='radio' checked  name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	

				";
			} else {
				$option_pg_type .= "  <div class='radio w25'><input type='radio' name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	";
			}
		}



		$parent = $CMS->product_group->getParent_bytype($pg_type);
		foreach ($parent as $p) {
			if ($p['product_group_id'] == $pg_parent) {
				$option_pg_parent .= "<option value='{$p['product_group_id']}' selected>{$p['product_group_name']}</option>";
			} else {
				$option_pg_parent .= "<option value='{$p['product_group_id']}' >{$p['product_group_name']}</option>";
			}
		}
		$option_pg_status = "";
		for ($i = 1; $i >= 0; $i--) {
			if ($i == $pg_status) {
				$option_pg_status .= "   <div class='radio w25'><input type='radio' checked  name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			} else {
				$option_pg_status .= "   <div class='radio w25'><input type='radio'    name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			}
		}

		$out = '';
		 
	$out .= <<<EOF

 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=product_group&act=edit&id={$data['product_group_id']}" method="POST" enctype="multipart/form-data">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['pg_edit']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=product_group" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>


  <figure class="box-typical box-typical box-typical-padding border">
  	<div class="row">
 
 


		<div class="col-md-6">	
			<div class="row">
			 	<div class="col-xl-6">
			    	<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_parent']}</label>
						
								<select class="form-control" name="pg_parent" id="pg_parent" >
									{$option_pg_parent}
								</select>
							
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_name']} <span style="color:red">(*)</span></label>
							<div class="form-control-wrapper">	
								<input class="form-control " type="text" name="pg_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_name_err']}" value="{$pg_name}" onfocusout="generate_pg_code(this,'#pg_code');">
							</div>	
							
					</fieldset>
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_code']} <span style="color:red">(*)</span></label>
							<div class="form-control-wrapper">	
								<input class="form-control " type="text" name="pg_code" id="pg_code" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_code_err']}" value="{$pg_code}"  >
							</div>	
							
					</fieldset>
					
				</div>

				<div class="col-xl-6">
						
						<fieldset class="form-group">
							<label class="form-label  pull-left" >{$CMS->lang['pg_avartar']}</label>
								 <div class="actionButtons pull-right">
		                                <ul>
		                                    <li onclick="return performClick('ufile');">
		                                        <i tabindex="0" class="fa fa-pencil" ></i>
		                                    </li>
		                                    <li>
		                                        <span class="text-left">|</span>
		                                    </li>
		                                    <li onclick="return delete_fileToAttach();">
		                                        <i class="fa fa-trash-o"></i>
		                                    </li>
		                                </ul>
										<input  type="hidden" id="ufile_output_b64" name="base64_image" />
									  </div>	
								 
									 <div class="drop-zone fileinput-button">
				                              <img id="upload_img_show" width="205" />

				                              <i class="font-icon font-icon-cloud-upload-2"></i>
				                               <div class="drop-zone-caption">Drag file to upload</div>
				                                 <input type="file"  name="pg_avartar" id="ufile" accept="image/*">
				                         </div><!--.drop-zone-->
				                  		 
								 
									<img class="img-responsive" src="{$pg_avartar}" style="max-width: 100%;" alt="{$data['product_group_name']}"> 
								
						</fieldset>
				</div>
			</div><!-- row -->	
			

		</div><!-- col-md-6 -->
		
	 	<div class="col-md-6">
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_description']}</label>
				
						<textarea rows="3" class="form-control" name="pg_description" id="pg_description">{$pg_description}</textarea> 
					
			</fieldset>
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_type']}</label>
					 
							{$option_pg_type}
			</fieldset>
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_status']}</label>
				 
							{$option_pg_status}
					 
			
			</fieldset>
	 		
	 	</div>	<!-- col-md-6 -->


 	
		 
	
	 </div>
 	</figure>
</section>
 	<section class="add_cart_footer">

 
			 
				{$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=product_group", "detail_id" => "{$data['product_group_id']}" ))}
				{$CMS->global->footer_edit()}
		</section>


	</form>
<script>
 $(document).ready(function(){

	validate_form_custom("#form-signin_v1",".act_submit_save");
});

</script>
 <script src="{$CMS->vars['js_acp']}/product_group.js"></script>
EOF;
		return $out;
	}
	public function show($data=null) {
		global $CMS, $DB, $member;
		$data['product_group_parent_c'] = $data['product_group_parent_c'] != 0 ? $data['product_group_parent_c'] : "{$CMS->lang['root_group']}";
		$data['product_group_name'] = $data['product_group_name'] != "" ? $data['product_group_name'] : "N/A";
		$data['product_group_code'] = $data['product_group_code'] != "" ? $data['product_group_code'] : "N/A";
		$data['product_group_description'] = $data['product_group_description'] != "" ? $data['product_group_description'] : "N/A";
 
		$out = <<<EOF


<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['pg_info']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=product_group" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_parent']}</label>
					<div class="col-xl-9 form-control-span2"> 
 

							{$data['product_group_parent_c']}
				 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_name']}</label>
					<div class="col-xl-9 form-control-span2"> 
				 
							{$data['product_group_name']}
		 
				    </div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_code']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_group_code']} 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_description']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_group_description']} 
					</div>
				</fieldset>
			</div>
			
			<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">	
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_type']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_group_type_bk']}
						 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_status']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_group_statupg_c']}
						 
					</div>
				</fieldset>
				
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_time']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_group_time_c']}
						 
					</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_avartar']}</label>
					<div class="col-xl-9 form-control-span2"> 
					 
							<img class="img-responsive" src="{$data['product_group_avatar_c']}" style="max-width: 100%;" alt="{$data['product_group_name']}"> 
					 
					</div>
				</fieldset>
			</div> 
	 </div>
 	</figure>	
</section>

		<section class="add_cart_footer">
			 {$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=product_group"))} 
EOF;
		
		 
						if($CMS->permit['product_group_delete'] == 1)
						{
							$out .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=product_group&act=delete&id={$data['product_group_id']}');"  class="pull-right add_cart_2">{$CMS->lang['gdelete']}</a>

EOF;

						}

						if($CMS->permit['product_group_edit'] == 1)
						{
							$out .=<<<EOF
							<a href="{$CMS->vars['root_domain']}/?site=product_group&act=edit&id={$data['product_group_id']}" class="pull-right add_cart_2">{$CMS->lang['gedit']}</a>
EOF;

						}

						 
						
					$out .=<<<EOF
		</section>


EOF;
		return $out;
	}
}
?>