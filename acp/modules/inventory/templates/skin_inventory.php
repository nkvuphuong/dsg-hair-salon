<?php
class skin_inventory {
	public function head() {
		global $CMS, $DB, $member;

        // LHL-2018-08-10: Declare missing input
        $in_balance = \lib\input::get('in_balance');
        $in_type = \lib\input::get('in_type');
        $in_name = \lib\input::get('in_name');
        $search_type = intval(\lib\input::get('search_type'));
        $store_id = \lib\input::get('store_id');
        $user_id = \lib\input::get('user_id');

		if( $search_type == 0)
		{
			$active_0 = " active ";   $active_1 = " ";
		}
		else if( $search_type == 1)
		{
			$active_1 = " active ";   $active_0 = " ";
		}

	    $option_in_balance_search = "<option value=''>{$CMS->lang['select_balance']}</option>";
		for ($i =0; $i <= 1; $i++) {
			if ($i == $in_balance && $in_balance != '') {
				$option_in_balance_search .= "<option value='{$i}' selected>{$CMS->lang['inventory_is_balance_'.$i]}</option>";
			} else {
				$option_in_balance_search .= "<option value='{$i}'>{$CMS->lang['inventory_is_balance_'.$i]}</option>";
			}
		}

		$option_ini_amount_search = "<option value=''>{$CMS->lang['select_ini_amount']}</option>";
		for ($i =1; $i <= 3; $i++) {
			if ($i == $in_balance && $in_balance != '') {
				$option_ini_amount_search .= "<option value='{$i}' selected>{$CMS->lang['ini_amount_'.$i]}</option>";
			} else {
				$option_ini_amount_search .= "<option value='{$i}'>{$CMS->lang['ini_amount_'.$i]}</option>";
			}
		}



		$option_in_type_search = "<option value=''>{$CMS->lang['select']}</option>";
		for ($i =1; $i <= 3; $i++) {
			if ($i == $in_type && $in_type != '') {
				$option_in_type_search .= "<option value='{$i}' selected>{$CMS->lang['inventory_type_'.$i]}</option>";
			} else {
				$option_in_type_search .= "<option value='{$i}'>{$CMS->lang['inventory_type_'.$i]}</option>";
			}
		}


		$out = '';
		 
		$out .= <<<EOF

<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['in_title']}</h3>
		<figure class="pull-right right">
			 	<div class="search">
				<form method="post"  action="{$CMS->vars['root_domain']}/?site=inventory&act=search" style="display:inline-block">

						<input type="submit" class="fa-input" value="&#xf002;">
						<input type="text"  name="in_name" minlength="2" maxlength="64"  id="in_name"  placeholder="{$CMS->lang['gsearch_quick']}">
						<input type="hidden"  name="search_type" value="{$search_type}">
					</form>
					<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
				</div>

				 
		    
			<a href="{$CMS->vars['root_domain']}/?site=inventory&act=add" title="" class="add_bill">{$CMS->lang['add_new_btn']}</a>
		</figure>
	</figure>

EOF;
if($search_type == 0)
{
	$out .= <<<EOF
	<section class="search_adv" >
<form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site=inventory&act=search" >
	<figure class="box-typical box-typical box-typical-padding border">
			<h5>{$CMS->lang['gsearch_advance']}</h5>
			<ul class="input_li row match-height">
			 
				<li class="col-xl-3 col-md-3 col-sm-6">
					 
						<input  name="in_name" id="in_name" type="text" class="form-control" placeholder="{$CMS->lang['plh_id_invoice']}" value="{$in_name}" >
					 
				</li>


				<li class="col-xl-3 col-md-3 col-sm-6">
				 
						<select class="form-control select2" name="in_type" id="in_type" >
						{$option_in_type_search}
						</select>
				 
				</li>
				<li class="col-xl-3 col-md-3 col-sm-6">
					 
						<select name="store_id" id="store_id" class="form-control select2" defaultvalue="{$store_id}">
							{$CMS->store->get_list_store("",1)}
						</select>
				 
				</li>
				<li class="col-xl-3 col-md-3 col-sm-6">
							 
								<input  name="user_id" id="user_id" type="text" class="form-control" placeholder="{$CMS->lang['user_id_search']}" value="{$user_id}" >
							 
				 </li>
			
				 <li class="col-xl-3 col-md-3 col-sm-6">
							 
								<div class="input-group date datetimepicker-1">
									<input type="text" name="in_time_from" placeholder="{$CMS->lang['title_time_from']}" class="form-control">
								<span class="input-group-addon">
									<i class="font-icon font-icon-calend"></i>
								</span>
							 
				</li>

				<li class="col-xl-3 col-md-3 col-sm-6">
							 
								<div class="input-group date datetimepicker-1">
									<input type="text" name="in_time_to" placeholder="{$CMS->lang['title_time_to']}" class="form-control">
								<span class="input-group-addon">
									<i class="font-icon font-icon-calend"></i>
								</span>
							 
				</li>
				 <li class="col-xl-3 col-md-3 col-sm-6">
					 
						<select class="form-control select2" name="in_balance" id="in_balance" >
						{$option_in_balance_search}
						</select>
				 
				</li>

				 <li class="col-xl-3 col-md-3 col-sm-6">
					 
						<input type="submit" value="{$CMS->lang['comment_filter']}">
						<input type="hidden"  name="search_type" value="{$search_type}"> 	
			 
				</li>

			</ul>	
	</figure>	
 </form>	
  
</section>


EOF;



} 
else
{
	$out .= <<<EOF
	<section class="search_adv" >
<form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site=inventory&act=search" >
	<figure class="box-typical box-typical box-typical-padding border">
			<h5>{$CMS->lang['gsearch_advance']}</h5>
			<ul class="input_li row">
			 
				<li class="col-xl-3 col-md-3 col-sm-6">
					 
						<input  name="ini_name" id="in_name" type="text" class="form-control" placeholder="Tên sản phẩm" value="{$ini_name}" >
					 
				</li>


				<li class="col-xl-3 col-md-3 col-sm-6">
				 
						<select class="form-control select2" name="ini_amount" id="ini_amount" >
						{$option_ini_amount_search}
						</select>
				 
				</li>
				<li class="col-xl-3 col-md-3 col-sm-6">
					 
						<select name="store_id" id="store_id" class="form-control select2" defaultvalue="{$CMS->input['store_id']}">
							{$CMS->store->get_list_store("",1)}
						</select>
				 
				</li>
				 

				 <li class="col-xl-3 col-md-3 col-sm-6">
					 
						<input type="submit" value="{$CMS->lang['comment_filter']}">
						 <input type="hidden"  name="search_type" value="{$CMS->input['search_type']}">
			 
				</li>

			</ul>	
	</figure>	
 </form>	
  
</section>


EOF;


}
$out .= <<<EOF

<section class="tabs-section">
				<div class="tabs-section-nav tabs-section-nav-inline">
					<ul class="nav" role="tablist">
						<li class="nav-item">
							<a class="nav-link {$active_0}"  href="{$CMS->vars['root_domain']}/?site=inventory" >
								 {$CMS->lang['in_title']}
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link {$active_1}" href="{$CMS->vars['root_domain']}/?site=inventory&search_type=1"  >
								{$CMS->lang['in_inventoried_assets']}
							</a>
						</li>
						 
					</ul>
				</div><!--.tabs-section-nav-->
</section>				
<section class="add_table">
	 <div class="data_table">
			<table id="example" class="display table table_cus" cellspacing="0" width="100%">
				<thead>
EOF;

				if($search_type == 0)
				{
					$out  .=<<<EOF

					<tr>
EOF;

		if($_SESSION['is_mobile'] == true)
		{
				$out .=<<<EOF
				<th width="2%" data-orderable="false" data-sortable="false"></th>
EOF;
		}
					$out .=<<<EOF
						<th  width="3%"  data-orderable="false" data-sortable="false" >ID</th>
						<th width="10%"  data-sortable="true" >{$CMS->lang['inventory_type']}</th>
						<th  width="10%"  data-orderable="false"  >{$CMS->lang['store_id']}</th>
						<th width="7%"  data-orderable="false" >{$CMS->lang['user_id']}</th>
						<th width="5%"  data-orderable="false" >{$CMS->lang['total_quantity']}</th>
	 
						<th width="5%" data-orderable="false" >{$CMS->lang['total_check']}</th>
							<th width="10%"  data-orderable="false"  data-sortable="false">{$CMS->lang['inventory_time']}</th>
						<th width="5%"  data-orderable="false"  data-sortable="false">{$CMS->lang['balance_store']}</th>

					 
		 
					</tr>
EOF;


				}					 
				else
				{

					$out .=<<<EOF

					<tr>
EOF;

		if($_SESSION['is_mobile'] == true)
		{
				$out .=<<<EOF
					<th width="2%" data-orderable="false" data-sortable="false"></th>
EOF;
		}
					$out .=<<<EOF
						<th  width="3%"  data-orderable="false" data-sortable="false"  >ID</th>
						
						<th  width="10%"  data-orderable="false"  >{$CMS->lang['store_id']}</th>
						<th width="10%"  data-sortable="true" >{$CMS->lang['ass_name']}</th>
						<th   width="10%" data-orderable="false"  >{$CMS->lang['ass_keyname']}</th>
						<th width="7%"  data-orderable="false" >{$CMS->lang['ass_purchase_price']}</th>
						<th width="12%"  data-orderable="false" >{$CMS->lang['ass_price']}</th>
	 
						<th width="6%" data-orderable="false" >{$CMS->lang['ass_balance']}</th>
						<th width="5%"  data-orderable="false"  data-sortable="false">{$CMS->lang['balance_store']}</th>
						<th width="5%"  data-orderable="false"  data-sortable="false">{$CMS->lang['in_note']}</th>

						<th width="6%"  data-orderable="false"> </th>
		 
					</tr>
EOF;
				}	
					$out .=<<<EOF

				</thead>
				<tbody>
EOF;
		return $out;
	}
	public function foot() {
		global $CMS, $DB, $member;
		$out = <<<EOF
				

					</tbody>
				</table>

		</div><!--.box-typical-body-->
		 <div class="fuction_table">
				<div class="pull-left">

					<p class="form-control-static ">
					 

					</p>
					 

				</div>
				<nav class="pull-right">
				    	{$CMS->inventory->show_page}
				</nav>
		 </div>
	 </section>
	 
		 
</section>


EOF;
	if($_SESSION['is_mobile'] == true)
	{	 
		$out .=<<<EOF

		<script>
			$(function() {
				$('#example').DataTable({
					 order: [],
				responsive: true,
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
	else
	{
		 
		$out .=<<<EOF

					 <script>
								$(function() {
								$('#example').DataTable({
								language: {
								      emptyTable: 'Không tìm thấy dữ liệu!'
								    },
	 							 order: [],
						 		  paging: false,
									  searching: false,
									  info: false
								});
							});
					 </script>

EOF;
	}
	$out .=<<<EOF

 
<script language="javascript">arrange_setup("{$CMS->inventory->arrange_data}");</script>
EOF;
		return $out;
	}
	public function mid($data=null) {
		global $CMS, $DB, $member;
		
 

				$search_type = intval($CMS->input['search_type']);
				if($search_type == 0)
				{
					$out .=<<<EOF

	 
					<tr>
EOF;

	if($_SESSION['is_mobile'] == true)
		{
				$out .=<<<EOF
				<td></td>
EOF;
		}
				$out .=<<<EOF
						<td>
							 {$data['inventory_name_bk']}
						</td>
						<td >
							 {$data['inventory_type_bk']}

						</td>
						<td>
							{$data['store_name']}
						</td>
						<td>
							{$data['user_id_c']}
						</td>
						 <td>
							 {$data['ini_ton']}
						</td>
						 <td>
							 {$data['ini_check']}
						</td>
						<td>
							{$data['inventory_time_bk']}
						</td>
						<td>
							 
							

							{$data['is_balance_bk']}
						 
						 

EOF;

				if($data['is_balance'] == 0)
				{
			 	$out .=<<<EOF
						<div class="btn-group">
							<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								{$CMS->lang['gaction']}
							</button>
							<div class="dropdown-menu">

							
EOF;
						if($CMS->permit['inventory_approve'] == 1)
						{
							$out .=<<<EOF
							{$data['act_balance']}

EOF;
						}							
						 
						if($CMS->permit['inventory_edit'] == 1)
						{
							$out .=<<<EOF
							<a class="dropdown-item"  href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&id={$data['inventory_id']}" title""="" class="edit"><i class="fa fa-edit"></i> {$CMS->lang['edit']}</a>
EOF;

						}

						if($CMS->permit['inventory_delete'] == 1)
						{
							$out .=<<<EOF
							<a class="dropdown-item"  onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=delete&id={$data['inventory_id']}');"  class="edit"><i class="fa fa-trash-o"></i> {$CMS->lang['delete']}</a>

EOF;

						}
					$out .=<<<EOF


							 
							</div>
						</div>
EOF;
					}
						$out .=<<<EOF


 
						</td>
					</tr>
EOF;

	 	}
	 	else
	 	{
	 	 
	 		$ass_purchase_price = number_format($data['assets']['ass_purchase_price']);
	 		$ass_price = number_format($data['assets']['ass_price']);
 
	 		$out .=<<<EOF

	 
					<tr>
EOF;

	if($_SESSION['is_mobile'] == true)
		{
				$out .=<<<EOF
				<td></td>
EOF;
		}
				$out .=<<<EOF
						<td>
							 {$data['inventory_name_bk']}
						</td>
						<td >
							 {$data['store_name']}

						</td>
						<td>
							{$data['assets']['ass_name']}
						</td>
						<td>
							{$data['assets']['ass_keyname']}
						</td>
						<td>
						
							{$ass_purchase_price}<sup>đ</sup>
						</td>
						 <td>
							{$ass_price}<sup>đ</sup>
						</td>
						 <td>
							 {$data['ini_ton']}/{$data['ini_check']}
						</td>

						<td>
							 
							{$data['ini_balance']}
						</td>
					 


						<td>
EOF;
							 if($data['ini_balance'] < 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="span-warning">{$CMS->lang['ini_amount_2']}</span>
EOF;
							 }
							elseif($data['ini_balance'] > 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="span-success">{$CMS->lang['ini_amount_3']}</span>
EOF;
							 }
							 elseif($data['ini_balance'] == 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="blue">{$CMS->lang['ini_amount_1']}</span>
EOF;
							 }
						 $out .=<<<EOF
						</td>
 					
			
						<td align="center">
 
EOF;
					 
 					 
						
			 		 
						if($CMS->permit['inventory_edit'] == 1 AND $data['is_balance'] == 0)
						{
							$out .=<<<EOF
					 

							 <a  href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&subact=edit_item&id={$data['inventory_id']}&ini_id={$data['ini_id']}"  class="edit"><i class="fa fa-edit"></i></a>
EOF;

						}
						if($CMS->permit['inventory_delete'] == 1 AND $data['is_balance'] == 0)//Da tao bu tru
						{
							$out .=<<<EOF
						 
								<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=delete&subact=del_item&id={$data['inventory_id']}&ini_id={$data['ini_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
EOF;

						}
					$out .=<<<EOF

 
 
						</td>
					</tr>
EOF;
	 	}
		return $out;
	}
	public function none() {
		global $CMS, $DB, $member;
		$out = <<<EOF
					<tr>
						<td colspan="7">
							<center><p style="font-size:14px">{$CMS->lang['not_data']}</p></center>
						</td>
					</tr>
EOF;
		return $out;
	}
	public function add() {
		global $CMS, $DB, $member;

		// LHL-2018-08-10: Add isset to all session
        $add = isset($_SESSION['add_inventory']) ? $_SESSION['add_inventory'] : [];

	    $data['store_id'] = isset($add['store_id']) ? $add['store_id'] : 0;
        $data['store_id_bk'] = isset($add['store_id_bk']) ? $add['store_id_bk'] : 0;
		$data['inventory_type'] = isset($add['inventory_type']) ? $add['inventory_type'] : 0;
		// $_SESSION['add_inventory']['inventory_pgroup'] = $inventory_pgroup;
		$data['inventory_note'] = isset($add['inventory_note']) ? $add['inventory_note'] : "";
	 	$data['inventory_pgroup'] = isset($add['inventory_pgroup_str']) ? $add['inventory_pgroup_str'] : "";

	 	list($row_store, $option_store) = $CMS->store->get_list_store($data['store_id_bk'],0,0);	
		$option_p_product_group = "<option value=''>{$CMS->lang['select_pgroup']}</option>";
		 
		$group = $CMS->product_group->getAll(0);
 
		foreach ($group as $g) {
		 
				$option_p_product_group .= "<option value='{$g['product_group_id']}'>{$g['product_group_name']}</option>";
				if(count($g['data_item']) > 0)
				{
					foreach ($g['data_item'] as $key => $value) {
						$option_p_product_group .= "<option value='{$value['product_group_id']}'> |__{$value['product_group_name']}</option>";
					}
				}
		}


 
		$out = '';
		 
	$out .= <<<EOF

 <ul class="progressbar">
		        <li class="active">{$CMS->lang['in_add_title']}</li>
		        <li >{$CMS->lang['addproduct_title']}</li>
		        <li class="">{$CMS->lang['title_complete']}</li>
		    
 </ul>
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=inventory&act=add&subact=add_step_1" method="POST" enctype="multipart/form-data">
  <section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['in_add_title']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site=inventory" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

   <figure class="box-typical box-typical box-typical-padding border">
	<div class="row">
	 	<div class="col-md-6">
	  
			 	 
			  <fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;

						if($row_store >= 3)
						{
							$out .=<<<EOF
							<div class="form-control-wrapper">  
							   	<select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control" autocomplete="off" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['in_store_err']}">						
		                       		{$option_store}
		                        </select>
		                    </div>    							
EOF;
						}									
						else
						{
							$out .=<<<EOF
							 {$option_store}
EOF;
						}
						///onkeyup="qsearch('shipment', 'shipment_list', this);"
						$out .=<<<EOF
						<input type="hidden" name="store_id_param" value="{$data['store_id']}" />
				</fieldset>	
					


				 <fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['in_type']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
 
							<div class="form-control-wrapper">  
							   	<select name="inventory_type" id="inventory_type" defaultvalue="{$data['inventory_type']}" class="form-control" autocomplete="off" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['inventory_type_err']}">						
		                       		<option value="">--{$CMS->lang['inventory_type_select']}--</option>
		                       		<option value="1">{$CMS->lang['inventory_type_1']}</option>
		                       		<option value="2">{$CMS->lang['inventory_type_2']}</option>
		                       		<option value="3">{$CMS->lang['inventory_type_3']}</option>
		                        </select>
		                    </div>    
							

 
					 
				</fieldset>	
				<fieldset class="form-group" id="hide_show_pgroup" style="display:none">
					<label class="form-label" >{$CMS->lang['in_type_2list']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
 
					 
							   <select style="width:100%" class="form-control select2 select_product_group"   multiple="multiple" name="inventory_pgroup[]" defaultvalue="{$data['inventory_pgroup']}" id="inventory_pgroup"    >
								{$option_p_product_group}
							  </select>
		                 
				 				 <input type="hidden" name="inventory_pgroup_tmp" value="{$data['inventory_pgroup']}" />
				 

 
					 
				</fieldset>	
						
			  
			 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['in_note']}</label>
	
						<textarea rows="3" class="form-control" name="inventory_note" id="inventory_note" >{$data['inventory_note']}</textarea> 
				 
			</fieldset>
			  
			 	
			 
	 	</div><!-- col-md-6 -->
	 	


	 	<div class="col-md-6">
	 		  <div style="font-size:13px; margin-top:15px">
				<p style="color:#f00; font-weight:bold">{$CMS->lang['inventory_type']}:</p>
				<p style="font-style:italic">{$CMS->lang['note_invoice_1']}</p>
				<p style="font-style:italic; margin-bottom:0">{$CMS->lang['note_invoice_2']}</p>
				 
			</div>
			
			
	 	</div>	<!-- col-md-6 -->

	
		
		

	 

	</div>

	</figure>
</section>
 

	<section class="add_cart_footer">

EOF;

		$url_back['list'] =	"{$CMS->vars['root_domain']}/?site=inventory";
 
		$out .= $CMS->global->footer_back($url_back);
		$out .=<<<EOF

		 
			 
			<button    type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['in_step1_button']}</span><span class="ladda-spinner"></span></button>


		</section>

 
</form>
 
 <script src="{$CMS->vars['js_acp']}/inventory.js"></script>
 <script>
 $(document).ready(function() {
    select_pgroup();
  });

  </script>
EOF;
		return $out;
	}
	


    public function edit_item($data=null, $item = "") {
		global $CMS, $DB, $member;
		 
		 
	$out .= <<<EOF
 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=inventory&act=edit&subact=edit_item_do&id={$data['inventory_id']}&ini_id={$item['ini_id']}" method="POST" enctype="multipart/form-data">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['edititem_title']} #{$item['ini_id']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=inventory" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>


  <figure class="box-typical box-typical box-typical-padding border">
  	<div class="row">
 
 


		<div class="col-md-6">	
			 	 
				    <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_name']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['inventory_name_bk']}

							</div>	
						</div>
					</fieldset>
					 <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['store_id']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['store_name']}

							</div>	
						</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_type_bk']}

							</div>	
						</div>	
					</fieldset>
					  
EOF;
				if($data['inventory_type'] == 2)
				{
					$out .= <<<EOF
				   <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type_2list']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_pgroup_bk']}

							</div>	
						</div>	
					</fieldset>

EOF;

				}			 
  				$out .= <<<EOF
				
		</div><!-- col-md-6 -->
		
	 	<div class="col-md-6">
  		
	 	</div>	<!-- col-md-6 -->


 	
		 
	
	 </div>
 	</figure>

</section>
 <section class="add_table">
	 	<div class="table_cus">
             <div class="table-responsive" style="overflow-x: initial;">
	 	 <!-- responsive-table -->
				<table>				
								<thead>
									<tr>
 										<th scope="col" width="10%">{$CMS->lang['ass_keyname']}</th>
										<th scope="col" width="15%">{$CMS->lang['ass_name']}</th>
										<th scope="col" width="25%">{$CMS->lang['title_description']}</th>
										<th scope="col" width="7%">{$CMS->lang['total_quantity']}</th>
										<th scope="col" width="10%">{$CMS->lang['title_number_invoice']}</th>
										<th scope="col" width="7%">{$CMS->lang['title_compare']}</th>
										 
									</tr>
								</thead>	
								<tbody id="data_table" class="ui-sortable">
EOF;

						if(is_array($item))
						{ 

							$amount = $item['ini_check'] - $item['ini_quantity'];
							$ini_check = $ini_quantity = "";
							$ini_check = $item['ini_check'];
							$ini_quantity = $item['ini_quantity'];
	                		$description = $item['ini_description'];
	                		$item = $CMS->inventory->convertvalue($item);
							$out .=<<<EOF

	                <tr>
	                	
	                	<td><a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$item['assets']['ass_id']}">{$item['assets']['ass_keyname']}</a></td>
	                	<td>{$item['ini_name']}</td>
	                	<td>
	                		<textarea class='form-control' maxlength='100'  name='ini_description'>{$description}</textarea>
	                	</td>
	                	<td>
	                		<span>{$ini_quantity}</span>
	                	</td>
	                	<td>
	                		<input type='text' class='form-control' onkeypress='return check_enter_number(event,this);'   name="ini_check" onkeyup='cal_quantity({$ini_quantity},this)'  maxlength='6' value="{$ini_check}" />
	                	</td>
	                	<td>
	                		<span id='container_ass_quantity'>{$amount}</span>
	                	</td>
	                	 
	                	<input type='hidden' name='ass_name[]' value='{$item['ini_name']}'/>
	                	<input type='hidden' name='ass_key[]' value='{$item['ini_key']}'/>
	                	<input type='hidden' name='ass_code[]' value='{$item['ini_key']}'/>
	                	<input type='hidden' name='ass_quantity[]' value='{$ini_quantity}'/>
	                </tr>


EOF;

						}
						$out .=<<<EOF


						 	</tbody>
					</table>
			 </div>
		 </div>	 			 
	 </section>	

<section class="add_table title-invoice">
	<div class="alert alert-warning alert-icon alert-close alert-dismissible fade in" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
							<i class="font-icon font-icon-warning"></i>
							{$CMS->lang['list_note_ass']}
	 </div>

</section>	 
 	<section class="add_cart_footer">
			 
EOF;
		
		$url_back['list'] =	"{$CMS->vars['root_domain']}/?site=inventory";
	 
		$out .= $CMS->global->footer_back($url_back);


		if($_SESSION['is_mobile'] == true)
		{
			$out  .=<<<EOF
		 
			  <div class="btn-group dropup pull-right hidden-xl-up">
					  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa"></i>{$CMS->lang['gaction']}
					  </button>
					  <div class="dropdown-menu">
					  	<ul>
							<li class="hidden-xl-up"><a id="action_submit_mobile" ><i class="fa  fa-save"></i>{$CMS->lang['ini_save_btn']}</a></li>
		 					<li class="hidden-xl-up"><a  onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=delete&subact=del_item&id={$data['inventory_id']}&ini_id={$item['ini_id']}');"   ><i class="fa fa-trash-o"></i>{$CMS->lang['ini_del_btn']}</a></li>
		 			 </ul>
		 			 	<input type="submit" style="display:none" id="form_submit" />
				 </div>	
			 </div>			  


EOF;
		}
		else
		{
			$out .=<<<EOF

			<button id="form_submit" type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['ini_save_btn']}</span><span class="ladda-spinner"></span></button>


				<button   onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=delete&subact=del_item&id={$data['inventory_id']}&ini_id={$item['ini_id']}');"  class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['ini_del_btn']}</span><span class="ladda-spinner"></span></button>

			
EOF;

		}
		$out .=<<<EOF
			

		</section>


	</form>
 <script>
 $("#action_submit_mobile").click(function(){

 	$("#form_submit").trigger("click");
 });
 </script>
 <script src="{$CMS->vars['js_acp']}/inventory.js"></script>
EOF;
		return $out;
	}


	public function editproduct($data=null, $list_ass = "") {
		global $CMS, $DB, $member;
		 
		 
	$out .= <<<EOF
  <ul class="progressbar">
		        <li class="active">{$CMS->lang['in_edit_title']}</li>
		        <li  class="active">{$CMS->lang['editproduct_title']}</li>
		        <li class="">{$CMS->lang['title_complete']}</li>
		    
 </ul>
 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=inventory&act=edit&subact=edit_do&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['editproduct_title']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=inventory" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>


  <figure class="box-typical box-typical box-typical-padding border">
  	<div class="row">
 
 


		<div class="col-md-6">	
			 	 
				     
					 <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['store_id']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['store_name']}

							</div>	
						</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_type_bk']}

							</div>	
						</div>	
					</fieldset>
					  
EOF;
				if($data['inventory_type'] == 2)
				{
					$out .= <<<EOF
				   <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type_2list']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_pgroup_bk']}

							</div>	
						</div>	
					</fieldset>

EOF;

				}			 
  				// Kiem kho theo sản pham
  				if($data['inventory_type'] == 1)
				{
					$out .= <<<EOF
				  <fieldset class="form-group">
						<label class="form-label">{$CMS->lang['title_add_assets_invoice']}:</label>
			
							<div class="box_container">
								 
								 <div class="input-group">
										<input type="text" class="form-control" name="find_asset_inventory" id="find_asset_inventory"  store_id="{$data['store_id']}" maxlength="100" autocomplete="off" value="" style="position: :relative;">
										 <div class="input-group-addon" style="cursor:pointer" onclick="getall_asset(this);">
											<span  class="fa fa-arrow-down"></span>
										</div>
								  </div><!-- input-group -->

								 <div id="suggesstion-asset" class="box_result_find" style="display:none"></div>
								 
							</div>
 
					</fieldset>

EOF;

				}			 
  

				$out .= <<<EOF
				
		</div><!-- col-md-6 -->
		
	 	<div class="col-md-6">
  		
	 	</div>	<!-- col-md-6 -->


 	
		 
	
	 </div>
 	</figure>

</section>
 <section class="add_table">
	 	<div class="table_cus">
                <div class="table-responsive" style="overflow-x: initial;">
	 	 <!-- responsive-table -->
				<table>				
								<thead>
									<tr>
										<th scope="col" width="15%">{$CMS->lang['ass_name']}</th>
										<th scope="col" width="10%">{$CMS->lang['ass_keyname']}</th>
										<th scope="col" width="25%">{$CMS->lang['title_description']}</th>
										<th scope="col" width="10%">{$CMS->lang['ass_purchase_price']}</th>
										<th scope="col" width="7%">{$CMS->lang['total_quantity']}</th>
										<th scope="col" width="10%">{$CMS->lang['title_number_invoice']}</th>
										<th scope="col" width="7%">{$CMS->lang['title_compare']}</th>

										<th scope="col" width="3%"></th>

									</tr>
								</thead>	
								<tbody id="data_table" class="ui-sortable">
 									{$list_ass}
								</tbody>
					</table>
			 </div>
		 </div>	 			 
	 </section>	

<section class="add_table title-invoice">
	<div class="alert alert-warning alert-icon alert-close alert-dismissible fade in" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
							<i class="font-icon font-icon-warning"></i>
							{$CMS->lang['list_note_ass']}
	 </div>

</section>	 
 	<section class="add_cart_footer">


EOF;

		if($_SESSION['is_mobile'] == false)
		{
			$out .=<<<EOF
	 	
			<a href="{$CMS->vars['root_domain']}/?site=inventory" class="pull-left cancel">{$CMS->lang['title_back']}</a>
			 <a href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&id={$CMS->input['id']}" class="pull-left cancel">{$CMS->lang['back_step_1']}</a>

EOF;
		}
		else
		{

		 $out .=<<<EOF
			 <div class="btn-group dropup pull-left hidden-xl-up">
				  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-mail-reply"></i>{$CMS->lang['back']}
				  </button>
				  <div class="dropdown-menu">
				  	<ul>
						<li><a href="{$CMS->vars['root_domain']}/?site=inventory"   title=""><i class="fa  fa-mail-reply"></i>{$CMS->lang['back_step_1']}</a></li>
						<li><a title=""  href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&id={$CMS->input['id']}" ><i class="fa   fa-file-text-o"></i>{$CMS->lang['back_detail']}</a></li>
					</ul>
				  </div>
				</div>
EOF;

			}
			 $out .=<<<EOF
				<button id="form_submit" type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['ini_save_btn']}</span><span class="ladda-spinner"></span></button>


		</section>


	</form>
 
 <script src="{$CMS->vars['js_acp']}/inventory.js"></script>
EOF;
		return $out;
	}




	

	public function edit($data = "") {
		global $CMS, $DB, $member;
 
	 	list($row_store, $option_store) = $CMS->store->get_list_store($data['store_id_bk'],0,0);	
		$option_p_product_group = "<option value=''>{$CMS->lang['select_pgroup']}</option>";
		
		 
		$group = $CMS->product_group->getAll(0);
 
		foreach ($group as $g) {
		 
				$option_p_product_group .= "<option value='{$g['product_group_id']}'>{$g['product_group_name']}</option>";
				if(count($g['data_item']) > 0)
				{
					foreach ($g['data_item'] as $key => $value) {
						$option_p_product_group .= "<option value='{$value['product_group_id']}'> |__{$value['product_group_name']}</option>";
					}
				}
		}

		 
 
		$out = '';
		 
	$out .= <<<EOF
 <ul class="progressbar">
		        <li class="active">{$CMS->lang['in_edit_title']}</li>
		        <li >{$CMS->lang['editproduct_title']}</li>
		        <li class="">{$CMS->lang['title_complete']}</li>
		    
 </ul>
 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=inventory&act=edit&subact=edit_step_1&id={$data['inventory_id']}" method="POST" enctype="multipart/form-data">
  <section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['in_edit_title']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site=inventory" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

   <figure class="box-typical box-typical box-typical-padding border">
 
 
	<div class="row">
 



	 	<div class="col-md-6">
	  
			 	 
			  <fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;

						if($row_store >= 3)
						{
							$out .=<<<EOF
							<div class="form-control-wrapper">  
							   	<select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control select2" autocomplete="off" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['in_store_err']}">						
		                       		{$option_store}
		                        </select>
		                    </div>    
							

EOF;
						}									
						else
						{
							$out .=<<<EOF
							 {$option_store}

EOF;
						}
						
						///onkeyup="qsearch('shipment', 'shipment_list', this);"
						$out .=<<<EOF
						<input type="hidden" name="store_id_param" value="{$data['store_id']}" />
				</fieldset>	
					


				 <fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['in_type']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
 
							<div class="form-control-wrapper">  
							   	<select name="inventory_type" id="inventory_type" defaultvalue="{$data['inventory_type']}" class="form-control select2" autocomplete="off" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['inventory_type_err']}">						
		                       		<option value="">--{$CMS->lang['inventory_type_select']}--</option>
		                       		<option value="1">{$CMS->lang['inventory_type_1']}</option>
		                       		<option value="2">{$CMS->lang['inventory_type_2']}</option>
		                       		<option value="3">{$CMS->lang['inventory_type_3']}</option>
		                        </select>
		                    </div>    
							

 
					 
				</fieldset>	
				<fieldset class="form-group" id="hide_show_pgroup" style="display:none">
					<label class="form-label" >{$CMS->lang['in_type_2list']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
 
					 
							   <select style="width:100%" class="form-control select2 select_product_group"   multiple="multiple" name="inventory_pgroup[]" defaultvalue="{$inventory_pgroup}" id="inventory_pgroup"    >
								{$option_p_product_group}
							  </select>
		                 	 <input type="hidden" name="inventory_pgroup_tmp" value="{$data['inventory_pgroup']}" />
				 

 
					 
				</fieldset>	
						
			  
			 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['in_note']}</label>
	
						<textarea rows="3" class="form-control" name="inventory_note" id="inventory_note" >{$data['inventory_note']}</textarea> 
				 
			</fieldset>
			  
			 	
			 
	 	</div><!-- col-md-6 -->
	 	


	 	<div class="col-md-6">
	 		  <div style="font-size:13px; margin-top:15px">
				<p style="color:#f00; font-weight:bold">{$CMS->lang['inventory_type']}:</p>
				<p style="font-style:italic">{$CMS->lang['note_invoice_1']}</p>
				<p style="font-style:italic; margin-bottom:0">{$CMS->lang['note_invoice_2']}</p>
				 
			</div>
			
			
	 	</div>	<!-- col-md-6 -->

	
		
		

	 

	</div>

	</figure>
</section>
	<section class="add_cart_footer">
 
EOF;

			
		$url_back['list'] =	"{$CMS->vars['root_domain']}/?site=inventory";
 
		$out .= $CMS->global->footer_back($url_back);
		$out .=<<<EOF

			<button    type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['in_step1_button']}</span><span class="ladda-spinner"></span></button>


		</section>

 
</form>
 
 <script src="{$CMS->vars['js_acp']}/inventory.js"></script>
<script>
$(document).ready(function() {
			  select_pgroup();
	});

</script>
EOF;
		return $out;
	}




	public function addproduct($data=null, $list_ass = "") {
		global $CMS, $DB, $member;
		 
		 
	$out = <<<EOF
 <ul class="progressbar">
		        <li class="active">{$CMS->lang['in_add_title']}</li>
		        <li class="active" >{$CMS->lang['addproduct_title']}</li>
		        <li class="">{$CMS->lang['title_complete']}</li>
		    
 </ul>
 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=inventory&act=add_do" method="POST" enctype="multipart/form-data">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['addproduct_title']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=inventory" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>


  <figure class="box-typical box-typical box-typical-padding border">
  	<div class="row">
 
 


		<div class="col-md-6">	
			 	 
				     
					 <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['store_id']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['store_name']}

							</div>	
						</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_type_bk']}

							</div>	
						</div>	
					</fieldset>
					  
EOF;
				if($data['inventory_type'] == 2)
				{
					$out .= <<<EOF
				   <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type_2list']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_pgroup_bk']}

							</div>	
						</div>	
					</fieldset>

EOF;

				}			 
  				// Kiem kho theo sản pham
  				if($data['inventory_type'] == 1)
				{
					$out .= <<<EOF
				  <fieldset class="form-group">
						<label class="form-label">{$CMS->lang['title_add_assets_invoice']}:</label>
			
							<div class="box_container">
								  <div class="input-group">
								 
										<input type="text" class="form-control" name="find_asset_inventory" id="find_asset_inventory"  store_id="{$data['store_id']}" maxlength="100" autocomplete="off" value="" style="position: :relative;">
										 <div class="input-group-addon" style="cursor:pointer" onclick="getall_asset(this);">
											<span  class="fa fa-arrow-down"></span>
										</div>
								   </div><!-- input-group -->

								 <div id="suggesstion-asset" class="box_result_find" style="display:none"></div>
								 
							</div>
 
					</fieldset>

EOF;

				}			 
  

				$out .= <<<EOF
				
		</div><!-- col-md-6 -->
		
	 	<div class="col-md-6">
  		
	 	</div>	<!-- col-md-6 -->


 	
		 
	
	 </div>
 	</figure>

</section>
 <section class="add_table">
	 <div class="table_cus">
                <div class="table-responsive" style="overflow-x: initial;">
	 	 <!-- responsive-table -->
				<table>				
								<thead>
									<tr>
										<th scope="col" width="10%">{$CMS->lang['ass_keyname']}</th>
										<th scope="col" width="15%">{$CMS->lang['ass_name']}</th>
										<th scope="col" width="25%">{$CMS->lang['title_description']}</th>
										<th scope="col" width="10%">{$CMS->lang['ass_purchase_price']}</th>
										<th scope="col" width="7%">{$CMS->lang['total_quantity']}</th>
										<th scope="col" width="10%">{$CMS->lang['title_number_invoice']}</th>
										<th scope="col" width="7%">{$CMS->lang['title_compare']}</th>
										<th scope="col" width="3%"></th>
									</tr>
								</thead>	
								<tbody id="data_table" class="ui-sortable">
 									{$list_ass}
								</tbody>
					</table>
			</div>
		</div>				 
	 </section>	

<section class="add_table title-invoice">
	<div class="alert alert-warning alert-icon alert-close alert-dismissible fade in" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
							<i class="font-icon font-icon-warning"></i>
							{$CMS->lang['list_note_ass']}
	 </div>

</section>	 
 	<section class="add_cart_footer">
			

EOF;
			if($_SESSION['is_mobile'] == true)
			{
				$out  .=<<<EOF

		 	
		 	<div class="btn-group dropup pull-left hidden-xl-up">
				  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-mail-reply"></i>{$CMS->lang['back']}
				  </button>
				  <div class="dropdown-menu">
				  	<ul>
						<li><a id="form_submit_mobile" href="{$CMS->vars['root_domain']}/?site=inventory"  title=""><i class="fa  fa-mail-reply"></i>{$CMS->lang['back_list']}</a></li>
						<li><a id="form_submit_option_mobile" title=""  href="{$CMS->vars['root_domain']}/?site=inventory&act=add"  ><i class="fa   fa-file-text-o"></i>{$CMS->lang['back_step_1']}</a></li>
					</ul>
				  </div>
				</div>
EOF;
			}
			else
			{
				$out  .=<<<EOF

					<a href="{$CMS->vars['root_domain']}/?site=inventory" class="pull-left cancel">{$CMS->lang['title_back']}</a>
			 <a href="{$CMS->vars['root_domain']}/?site=inventory&act=add" class="pull-left cancel">{$CMS->lang['back_step_1']}</a>

EOF;
			}			 

		 
			if($_SESSION['is_mobile'] == true)
			{
				$out  .=<<<EOF
				 <div class="btn-group dropup pull-right hidden-xl-up">
					  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-save"></i>{$CMS->lang['gaction']}
					  </button>
					  <div class="dropdown-menu">
					  	<ul>
							<li class="hidden-xl-up"><a  id="form_savesubmit_mobile" ><i class="fa fa-save"></i>{$CMS->lang['in_save_btn']}</a></li>
		 					<li class="hidden-xl-up"><a  id="save_ex_btn"  title=""><i class="fa fa-save"></i>{$CMS->lang['in_save_ex_btn']}</a></li>
		 			 </ul>
		 				<button type="submit" style="display:none"  id="form_submit"/>
				 </div>	
			 </div>			  
EOF;

			}
			else
			{
				$out  .=<<<EOF
	
				<button  id="form_submit"    type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['in_save_btn']}</span><span class="ladda-spinner"></span></button>
				
				<button type="button" class="btn btn-inline btn-primary ladda-button pull-right add_cart" id="save_ex_btn" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['in_save_ex_btn']}</span><span class="ladda-spinner"></span></button>
EOF;

			}
				$out  .=<<<EOF
				<input type="hidden" name="is_balance" id="is_balance" value="0" />

		</section>


	</form>
 
 <script src="{$CMS->vars['js_acp']}/inventory.js"></script>
 <script>
 	$("#save_ex_btn").click(function(){
 
 		$("#form-signin_v1 #is_balance").val("1");
 		$("#form_submit").trigger("click");
 	});
 	$("#form_savesubmit_mobile").click(function(){
 		$("#form_submit").trigger("click");
 	});
 

 </script>
EOF;
		return $out;
	}








	public function show($data=null) {
		global $CMS, $DB, $member;
		 

		 $list_ass = $CMS->inventory->list_asset_2($data['inventory_id']);
 
		$out = <<<EOF


<section class="add_form main_form">
	<figure class="heading">
		<figure class="heading">
		<h3>{$CMS->lang['in_info']} #{$data['inventory_name']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=inventory" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
	 

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			


		<div class="col-md-5">	
			 	 
				     <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_name']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['inventory_name_bk']}

							</div>	
						</div>
					</fieldset>
					 <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['store_id']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['store_name']}

							</div>	
						</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_type_bk']}

							</div>	
						</div>	
					</fieldset>
					  
EOF;
				if($data['inventory_type'] == 2)
				{
					$out .= <<<EOF
				   <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type_2list']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_pgroup_bk']}

							</div>	
						</div>	
					</fieldset>

EOF;

				}			 
  				 
				$out .= <<<EOF
				 

		</div><!-- col-md-6 -->
		
	 	<div class="col-md-5">
	 			
  				 <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_note']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 									<p>	{$data['inventory_note']}</p>

							</div>	
						</div>
				 </fieldset>
	 	</div>	<!-- col-md-6 -->
 
 		<div class="col-md-2">
 				 <fieldset class="form-group row">
						 
						<div class="col-xl-12 form-control-span2"> 
							 
 									{$data['is_balance_bk']}
 									{$data['ref_store_request']}

					 
						</div>
				 </fieldset>

 		</div>
		 
	  
	   </div>
 	</figure>	
</section>

EOF;

		if(count($list_ass) > 0 AND is_array($list_ass))
		{
				$out .= <<<EOF
				<section class="add_table">				 			 

				  
						 
					 <div class="table-responsive" style="overflow-x: initial;">
							<table class="table_cus" width="100%">
								<thead>
									<tr>
										<th scope="col" width="10%">{$CMS->lang['ass_keyname']}</th>
										<th scope="col" width="15%">{$CMS->lang['ass_name']}</th>
										<th scope="col" width="25%">{$CMS->lang['title_description']}</th>
										<th scope="col" width="10%">{$CMS->lang['ass_purchase_price']}</th>
										<th scope="col" width="7%">{$CMS->lang['total_quantity']}</th>
										<th scope="col" width="10%">{$CMS->lang['title_number_invoice']}</th>
										<th scope="col" width="7%">{$CMS->lang['title_compare']}</th>
										<th scope="col" width="25%">{$CMS->lang['in_note']}</th>
 
									</tr>
								</thead>	
								<tbody id="data_subitem">							 
EOF;
						foreach ($list_ass as $key => $value) {
							# code...
							$ini_check = $ini_quantity = 	$ini_amount = "";
							$ini_check = $value['ini_check'];
							$ini_quantity = $value['ini_quantity'];
							$ini_amount = $ini_check - $ini_quantity;

							$value = $CMS->inventory->convertvalue($value,1);
							//print_r ($value);exit;
							$ass_purchase_price = number_format($value['assets']['ass_purchase_price']); 
							$out .= <<<EOF
							<tr>
								
								<td><a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$value['assets']['ass_id']}">{$value['assets']['ass_keyname']}</a></td>
								<td>{$value['ini_name']}</td>
								<td>{$value['ini_description']}</td>
								<td>{$ass_purchase_price} <sup>đ</sup></td>
								<td>{$ini_quantity}</td>
								<td>{$ini_check}</td>
									<td>{$ini_amount}</td>
								<td>
EOF;
							 if($ini_amount < 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="span-warning">{$CMS->lang['ini_amount_2']}</span>
EOF;
							 }
							elseif($ini_amount > 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="span-success">{$CMS->lang['ini_amount_3']}</span>
EOF;
							 }
							 elseif($ini_amount == 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="blue">{$CMS->lang['ini_amount_1']}</span>
EOF;
							 }
						 $out .=<<<EOF
						 	</td>
 
							

							</tr>
EOF;
						}

						$out .= <<<EOF
								 </tbody>
							</table>
						</div>		


			 
	 
	 
</section>

EOF;

		}


		$out .= <<<EOF

		<section class="add_cart_footer">
		 

EOF;
			
		$url_back['list'] =	"{$CMS->vars['root_domain']}/?site=inventory";
 
		$out .= $CMS->global->footer_back($url_back);	

				if($_SESSION['is_mobile'] == false)
				{

						if($CMS->permit['inventory_delete'] == 1 AND $data['is_balance'] == 0)
						{
							$out .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=delete&id={$data['inventory_id']}');"  class="pull-right add_cart_2">{$CMS->lang['gdelete']}</a>

EOF;

						}

						if($CMS->permit['inventory_edit'] == 1  AND $data['is_balance'] == 0)
						{
							$out .=<<<EOF
							<a href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&id={$data['inventory_id']}" class="pull-right add_cart_2">{$CMS->lang['gedit']}</a>
EOF;

						}
						if($CMS->permit['inventory_approve'] == 1  AND $data['is_balance'] == 0)
						{
							$out .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=approve_do&id={$data['inventory_id']}');" class="pull-right add_cart_2">{$CMS->lang['btn_approve']}</a>
EOF;

						}

				}
				else
				{
					$out  .=<<<EOF
		 
			  <div class="btn-group dropup pull-right hidden-xl-up">
					  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa  "></i>{$CMS->lang['gaction']}
					  </button>
					  <div class="dropdown-menu">
					  	<ul>

EOF;
						if($CMS->permit['inventory_delete'] == 1 AND $data['is_balance'] == 0)
						{
							$out .=<<<EOF
					 
							<li class="hidden-xl-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=delete&id={$data['inventory_id']}');"    ><i class="fa   fa-trash-o"></i>{$CMS->lang['gdelete']}</a></li>
EOF;

						}
						if($CMS->permit['inventory_edit'] == 1 AND $data['is_balance'] == 0)
						{
							$out .=<<<EOF
							 
							<li class="hidden-xl-up"><a   href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&id={$data['inventory_id']}"   ><i class="fa fa-edit"></i>{$CMS->lang['btn_edit']}</a></li>	
EOF;

						}
						if($CMS->permit['inventory_approve'] == 1 AND $data['is_balance'] == 0)
						{
							$out .=<<<EOF
							<li class="hidden-xl-up"><a  onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=approve_do&id={$data['inventory_id']}');"   ><i class="fa fa-chain"></i>{$CMS->lang['btn_approve']}</a></li>	

EOF;

						}

						
						$out .=<<<EOF
							
		 					
		 			 </ul>
		 			 
				 </div>	
			 </div>			  


EOF;
				}		 
						
					$out .=<<<EOF
		</section>


EOF;
		return $out;
	}





	public function approve($data=null) {
		global $CMS, $DB, $member;
		 

		 $list_ass = $CMS->inventory->list_asset_2($data['inventory_id']);
 
		$out = <<<EOF


<section class="add_form main_form">
	<figure class="heading">
		<figure class="heading">
		<h3>{$CMS->lang['in_balance_info']} #{$data['inventory_name']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=inventory" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
	 

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			


		<div class="col-md-6">	
			 	 
				     <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_name']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['inventory_name_bk']}

							</div>	
						</div>
					</fieldset>
					 <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['store_id']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 										{$data['store_name']}

							</div>	
						</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_type_bk']}

							</div>	
						</div>	
					</fieldset>
					  
EOF;
				if($data['inventory_type'] == 2)
				{
					$out .= <<<EOF
				   <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_type_2list']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label">
 										{$data['inventory_pgroup_bk']}

							</div>	
						</div>	
					</fieldset>

EOF;

				}			 
  				 
				$out .= <<<EOF
				
		</div><!-- col-md-6 -->
		
	 	<div class="col-md-6">
  				 
  				 <fieldset class="form-group row">
						<label class="col-xl-3 form-control-label2">{$CMS->lang['in_note']}</label>
						<div class="col-xl-9 form-control-span2"> 
							 <div class="form-label semibold">
 									<p>	{$data['inventory_note']}</p>

							</div>	
						</div>
					</fieldset>
		 

	 	</div>	<!-- col-md-6 -->


 	
		 
	  
	   </div>
 	</figure>	
</section>

EOF;

		if(count($list_ass) > 0 AND is_array($list_ass))
		{
				$out .= <<<EOF
				<section class="add_table">				 			 

				  
						 
					 <div class="table-responsive" style="overflow-x: initial;">
							<table class="table_cus" width="100%">
								<thead>
									<tr>
										<th scope="col" width="10%">{$CMS->lang['ass_keyname']}</th>
										<th scope="col" width="15%">{$CMS->lang['ass_name']}</th>
										<th scope="col" width="25%">{$CMS->lang['title_description']}</th>
										<th scope="col" width="10%">{$CMS->lang['ass_purchase_price']}</th>
										<th scope="col" width="7%">{$CMS->lang['total_quantity']}</th>
										<th scope="col" width="10%">{$CMS->lang['title_number_invoice']}</th>
										<th scope="col" width="7%">{$CMS->lang['title_compare']}</th>
										<th scope="col" width="25%">{$CMS->lang['in_note']}</th>
										<th scope="col" width="10%">{$CMS->lang['in_status']}</th>
 
									</tr>
								</thead>	
								<tbody id="data_subitem">							 
EOF;
						foreach ($list_ass as $key => $value) {
							# code...
 
							$text_color = "";
							if($value['ini_amount'] < 0)
							{
								$text_color = "class='span-warning' ";
							}
							elseif($value['ini_amount'] > 0)
							{
								$text_color = "class='span-success' ";
							}
							elseif($value['ini_amount'] == 0)
							{
								$text_color = "class='blue' ";
							}

							$ini_check = $value['ini_check'];
							$ini_quantity = $value['ini_quantity'];
							$ini_amount = $ini_check - $ini_quantity;


							$value = $CMS->inventory->convertvalue($value);
							$ass_purchase_price = number_format($value['assets']['ass_purchase_price']);
							$out .= <<<EOF
							<tr>
								<td><a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$value['assets']['ass_id']}">{$value['assets']['ass_keyname']}</a></td>
								<td>{$value['ini_name']}</td>
								
								<td>{$value['ini_description']}</td>
								<td>{$ass_purchase_price} <sup>đ</sup></td>
								<td>{$ini_quantity}</td>
								<td>{$ini_check}</td>
								<td><span {$text_color} >{$ini_amount}</span></td>
								<td>
EOF;
							 if($ini_amount < 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="span-warning">{$CMS->lang['ini_amount_2']}</span>
EOF;
							 }
							elseif($ini_amount > 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="span-success">{$CMS->lang['ini_amount_3']}</span>
EOF;
							 }
							 elseif($ini_amount == 0)
							 {
							 	 $out .=<<<EOF
							 	 <span class="blue">{$CMS->lang['ini_amount_1']}</span>
EOF;
							 }
						 $out .=<<<EOF
						 	</td>
EOF;
								if($ini_amount <= 0)
								{
									$out .= <<<EOF
									 <td>Xuất kho {$data['store_name']}</td>
EOF;

								}
								else
								{
									$out .= <<<EOF
									 <td>Nhập kho {$data['store_name']}</td>
EOF;
								}
								
								$out .= <<<EOF
							</tr>
EOF;
						}

						$out .= <<<EOF
								 </tbody>
							</table>
						</div>		


			 
	 
	 
</section>

EOF;

		}


		$out .= <<<EOF

		<section class="add_cart_footer">
		 
EOF;

			
		$url_back['list'] =	"{$CMS->vars['root_domain']}/?site=inventory";
	 
		$out .= $CMS->global->footer_back($url_back);
			if($_SESSION['is_mobile'] == true)
			{
				$out  .=<<<EOF
		 
			  <div class="btn-group dropup pull-right hidden-xl-up">
					  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-cogs"></i>{$CMS->lang['gaction']}
					  </button>
					  <div class="dropdown-menu">
					  	<ul>
							<li class="hidden-xl-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=approve_do&id={$data['inventory_id']}');"  ><i class="fa  fa-check"></i>{$CMS->lang['btn_balance']}</a></li>
		 					<li class="hidden-xl-up"><a   href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&id={$data['inventory_id']}"   ><i class="fa fa-edit"></i>{$CMS->lang['btn_edit']}</a></li>
		 			 </ul>
		 			 
				 </div>	
			 </div>			  


EOF;

			}
			else
			{
				$out  .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=inventory&act=approve_do&id={$data['inventory_id']}');"  class="pull-right add_cart_2">{$CMS->lang['btn_balance']}</a>
							<a href="{$CMS->vars['root_domain']}/?site=inventory&act=edit&id={$data['inventory_id']}"  class="pull-right add_cart_2">{$CMS->lang['btn_edit']}</a>
EOF;
			}
			$out  .=<<<EOF
			 
		</section>


EOF;
		return $out;
	}


}
?>