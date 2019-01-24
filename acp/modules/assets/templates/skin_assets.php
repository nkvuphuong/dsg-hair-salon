<?php

class skin_assets {
	public function head() {
		global $CMS, $DB, $member;
		$output='';

		$output.=<<<EOF
<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['ass_head']}</h3>
		<figure class="pull-right right">
			<div class="search">
				<form method="post"  action="{$CMS->vars['root_domain']}/?site=assets&act=search" style="display:inline-block">
                                        <input type="hidden" name="current_search" value="1" />
					<input type="submit" class="fa-input" value="&#xf002;">
					<input type="text" name="p_quick_search" id="p_quick_search" placeholder="{$CMS->lang['gsearch_quick']}">
				</form>
				<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
			</div>
			<a href="{$CMS->vars['root_domain']}/?site=assets&act=add" title="" class="add_bill">{$CMS->lang['ass_add']}</a>
		</figure>
	</figure>
    
<section class="search_adv" >
<form name="formsearch_adv" id="formsearch_adv" style="display:none" method="post" class="quick_search" action="{$CMS->vars['root_domain']}/?site=assets&act=search" >
<figure class="box-typical box-typical box-typical-padding border">
			<h5>{$CMS->lang['gsearch_advance']}</h5>
			<ul class="input_li row">
                                <input type="hidden" name="current_search" value="1" />
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="ass_name" id="ass_name" size="45" type="text" class="form-control" placeholder="{$CMS->lang['ass_name']}" value="{$CMS->input['ass_name']}">
					</p>
				</li>
				
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="ass_code" id="ass_code" size="45" type="text" class="form-control" placeholder="{$CMS->lang['ass_code']}" value="{$CMS->input['ass_code']}">
					</p>
				</li>
	
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="user_name" id="user_name" size="45" type="text" class="form-control" placeholder="{$CMS->lang['user_id']}" value="{$CMS->input['user_name']}">
					</p>
				</li>
	
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<select name="store_id" id="store_id" class="form-control select2" defaultvalue="{$CMS->input['store_id']}">
							{$CMS->store->get_list_store()}
						</select>
					</p>
				</li>
	
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="shi_id" id="shi_id" size="45" type="text" class="form-control" placeholder="{$CMS->lang['shi_id']}" value="{$CMS->input['shi_name']}">
					</p>
				</li>
				
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<select name="supplier_id" id="supplier_id" class="form-control select2" placeholder="{$CMS->lang['supplier_id']}" defaultvalue="{$CMS->input['supplier_id']}">
							{$CMS->supplier->get_list_supplier()}
						</select>
					</p>
				</li>
				
				 <li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input type="submit" value="{$CMS->lang['comment_filter']}">
						 
					</p>
				</li>
			</ul>
		</figure>
	</section>
</form>

<section class="add_table box-typical box-typical-dashboard">
	<div class="box-typical-body">
        <div class="table-responsive">
            <table class="table table-hover table_cus">
				<thead>
					<tr>
						<th width="5%" id="order_assets_id">{$CMS->lang['ass_id']}</th>
						<th width="15%" id="order_assets_name">{$CMS->lang['ass_name']}</th>
						<th width="15%" id="order_assets_code">{$CMS->lang['ass_code']}</th>
						<th width="15%" id="order_assets_type">{$CMS->lang['store_id']}</th>
						<th width="15%" >{$CMS->lang['shi_id']}</th>
                        <th width="15%" >{$CMS->lang['user_id']}</th>
						<th width="10%" id="order_assets_time_creat">{$CMS->lang['ass_time']}</th>
						<th width="5%" style="text-align:center">{$CMS->lang['edit']}</th>
						<th width="5%" style="text-align:center">{$CMS->lang['delete']}</th>
					</tr>
				</thead>
				<tbody>
<script>rebuild_form('formsearch_adv')</script>
EOF;
		return $output;
	}
    
	public function head_search($title) {
		global $CMS, $DB, $member;
		$output='';
                $CMS->input['ass_name'] = urldecode($CMS->input['ass_name']);
                $title_search = $CMS->input['as'] ? "<span style=\"font-size:17px;line-height: 34px;margin-left: 5px;\">{$CMS->lang['search_title']}</span>" : "";
		$output.=<<<EOF
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_assets.js"></script>                        
<section class="add_table main_form">
	<figure class="heading">
		<a href="{$CMS->vars['root_domain']}/?site=assets"><h3>{$title}</h3></a>{$title_search}
		<figure class="pull-right right">
			<div class="search">
				<form method="post" name="frm_quick_search_ass" id="frm_quick_search_ass" action="{$CMS->vars['root_domain']}/?site=assets&act=search" style="display:inline-block" onsubmit="quick_search_submit();return false">
                                        <input type="hidden" name="current_search" value="1" />
					<input type="submit" class="fa-input" value="&#xf002;" id="quick_search_btn">
					<input type="text" value="{$CMS->input['ass_name']}" name="ass_quick_search" id="ass_quick_search" autocomplete="off" placeholder="{$CMS->lang['gsearch_quick']}">
                    <div id="suggesstion-box" class="box_result_find" style="display: none;"></div>
				</form>
				<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
			</div>
			<a href="{$CMS->vars['root_domain']}/?site=assets&act=add" title="" class="add_bill">{$CMS->lang['ass_add']}</a>
			<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
		</figure>
	</figure>
    
<section class="search_adv" >
<form name="formsearch_adv" id="formsearch_adv" style="display:none" method="post" class="quick_search" action="{$CMS->vars['root_domain']}/?site=assets&act=search" onsubmit="check_quick_search()">
<input type="hidden" id="quick_search_keyword" name="quick_search_keyword" value="" />
<figure class="box-typical box-typical box-typical-padding border">
			<h5>{$CMS->lang['gsearch_advance']}</h5>
			<ul class="input_li row match-height">
                                <input type="hidden" name="current_search" value="1" />
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="ass_name" id="ass_name" size="45" type="text" class="form-control" placeholder="{$CMS->lang['ass_name']}" value="{$CMS->input['ass_name']}">
					</p>
				</li>
				
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="ass_code" id="ass_code" size="45" type="text" class="form-control" placeholder="{$CMS->lang['ass_code']}" value="{$CMS->input['ass_code']}">
					</p>
				</li>
	
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="user_name" id="user_name" size="45" type="text" class="form-control" placeholder="{$CMS->lang['user_id']}" value="{$CMS->input['user_name']}">
					</p>
				</li>
	
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<select name="store_id" id="store_id" class="form-control select2" defaultvalue="{$CMS->input['store_id']}">
							{$CMS->store->get_list_store("",1)}
						</select>
					</p>
				</li>
	
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input name="shi_id" id="shi_id" size="45" type="text" class="form-control" placeholder="{$CMS->lang['shi_id']}" value="{$CMS->input['shi_id']}">
					</p>
				</li>
				
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<select name="supplier_id" id="supplier_id" class="form-control select2" placeholder="{$CMS->lang['supplier_id']}" defaultvalue="{$CMS->input['supplier_id']}">
                                                        
							{$CMS->supplier->get_list_supplier()}
						</select>
					</p>
				</li>
				
				 <li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input type="submit" value="{$CMS->lang['comment_filter']}">
						 
					</p>
				</li>
			</ul>
		</figure>
	</section>
</form>
<script>rebuild_form('formsearch_adv')</script>
<form name="form_assets" id="form_assets" method="POST" action="{$CMS->vars['root_domain']}/?site=assets"  >

<section class="add_table">
 
			<div class="data_table">
             <table id="example" class="display table table_cus" width="100%">
				<thead>


					<tr>
EOF;

  if($_SESSION['is_mobile'] == false)
    {
        $output .=<<<EOF
						 <th width="2%" data-orderable="false"  data-sortable="false">
		                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_assets');" id="checkall">
		                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
		                   <label for="id_{$result['record_cnt']}"></label>
		                    </div>
		                  </th>  
EOF;

	}
	else
	{
		$output .=<<<EOF
						 <th width="2%" data-orderable="false"  data-sortable="false"></td>
						 <th  width="3%" data-orderable="false"  data-sortable="false">
		                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_assets');" id="checkall">
		                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
		                   <label for="id_{$result['record_cnt']}"></label>
		                    </div>
		                  </th>  
EOF;
	}
		$output .=<<<EOF

		                
						<th width="25%" id="order_assets_name">{$CMS->lang['ass_name']}</th>
                        <th width="3%">SL</th>
						<th width="15%"  data-orderable="false"  data-sortable="false" >{$CMS->lang['store_id']}</th>
						<th width="15%"  data-orderable="false"  data-sortable="false"  >{$CMS->lang['shi_id']}</th>
						<th width="10%"  data-orderable="false"  data-sortable="false" >{$CMS->lang['ass_price']}</th>
						
						<th width="10%"  data-orderable="false"  data-sortable="false"  >{$CMS->lang['ass_time']}</th>
                        
						<th width="5%" data-orderable="false"  data-sortable="false" style="text-align:center"></th>
					</tr>


				</thead>
				<tbody>

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
		return $output;
	}
	
	public function foot() {
		global $CMS, $DB, $member;

		$output=<<<EOF
				</tbody>
			</table>
		</div>
 
	<div class="fuction_table">
				<div class="pull-left">

					<p class="form-control-static ">
						{$CMS->assets->action_control}

					</p>
					 

				</div>
				<nav class="pull-right">
				  {$CMS->show_page}
				</nav>
			</div>
</section>


 
<input type="hidden" name="data_cnt" value="{$CMS->assets->record_cnt}">
</form>

{$this->form_move_subitem()}
 
{$CMS->global->print_barcode_popup()}
{$CMS->global->preview_barcode_popup()}
<script language="javascript">arrange_setup("{$CMS->assets->arrange_data}");</script>
EOF;
 $ass_name_temp = urldecode($CMS->input['ass_name']);
if($_SESSION['is_mobile'] == true)
		{
				$output .=<<<EOF
				 <script>
						$(function() {
							$('#example').DataTable({
							language: {
								      emptyTable: '{$CMS->lang['no_data_follow_keywords']}: "{$ass_name_temp}"'
								  },
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
                                        if($CMS->input['ass_name'] != "" )
						{
							
							$output .=<<<EOF
							
							 <script>
								$(function() {
								$('#example').DataTable({
														 	   language: {
								      emptyTable: '{$CMS->lang['no_data_follow_keywords']}: "{$ass_name_temp}"'
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
                                                else
                                                {
                                                    $output .=<<<EOF
							 <script>
								$(function() {
								$('#example').DataTable({
								 language: {
								      emptyTable: '{$CMS->lang['no_data']}'
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

	}

 

		return $output;
	}
	
	public function mid($data=NULL) { 
		global $CMS, $DB, $member;

		
		$output=<<<EOF
			<tr>
			
				<td>{$data['ass_id']}</td>
				<td><a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$data['ass_id']}">{$data['ass_name']}</a></td>
                <td>{$data['ass_code']}</td>
                <td>{$data['store_id']}</td>
                <td>{$data['shi_id']}</td>
				<td><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}" target="_blank">{$data['user_name']}</a></td>
				<td>{$data['ass_time']}</td>
				<td align="center">
EOF;
						if($CMS->permit['assets_edit'] == 1)
						{
							$output .=<<<EOF
							<a href="{$CMS->vars['root_domain']}/?site=assets&act=edit&id={$data['ass_id']}" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

						}

							$output .=<<<EOF
						</td>
						<td align="center">
EOF;

						if($CMS->permit['assets_delete'] == 1)
						{
							$output .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=assets&act=delete&id={$data['ass_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
					 
EOF;

						}
					$output .=<<<EOF
</td>
				</tr>
EOF;
		return $output;
	}
    
	public function mid_search($data=NULL) {
		global $CMS, $DB, $member;

		$ass_price_tr = $data['data_bk']['ass_price'] ? strip_tags($data['ass_price']) : strip_tags($data['ass_purchase_price']);

		$output=<<<EOF
			<tr class="move_subitem_row_{$data['ass_id']}" a_key="{$data['ass_key']}" a_name="{$data['ass_name']}" a_quantity="{$data['quantity']}" a_price="{$ass_price_tr}">
			
EOF;

  if($_SESSION['is_mobile'] == true)
    {
        $output .=<<<EOF
        <td></td>
EOF;
	}


	  $output .=<<<EOF
				<td>
				    <div class="checkbox checkbox-only" data-sortable="false">
                      <input type="checkbox"  name="id_{$data['record_cnt']}" id="id_{$data['record_cnt']}" class="ass_record" is_exist_subitem="{$data['is_exist_subitem']}" value="{$data['ass_id']}"/>
                      <label for="id_{$data['record_cnt']}"></label>
				                      
                        <input type="hidden" name="name[_{$data['record_cnt']}]" value="{$data['data_bk']['ass_name']}" />
                        <input type="hidden" name="barcode[_{$data['record_cnt']}]" value="{$data['data_bk']['ass_code']}" />
                        <input type="hidden" name="price[_{$data['record_cnt']}]" value="{$ass_price_tr}" />
				     </div>
				  </td>
				<td >{$data['ass_name_bk']}</td>
                <td>{$data['quantity']}</td>
                <td>
                	{$data['store_id']}
				</td>
                <td>
                	{$data['shi_id']}</td>
                <td>{$data['ass_price']}</td>
				
				<td>{$data['ass_time']}</span></td>
				<td align="center">
                                    <div class="btn-group">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {$CMS->lang['ass_action']}
                                            </button>
                                            <div class="dropdown-menu" style="right:inherit !important">
                                                  
EOF;


                                   if($CMS->permit['order_add'] == 1)
                                    {
                                        $output .=<<<EOF
                                        <ul style="display:block">
                                            <li style='display:inline-block;margin-left:10px;width:auto !important;border:none !important'>
                                                <i class="fa fa-cart-plus"></i>
                                            </li>
                                            <li style='display:inline-block;width:auto !important'>
                                                <a class="dropdown-item" href='{$CMS->vars['root_domain']}/?site=assets&subact=order_add&asset_id={$data['ass_id']}'>{$CMS->lang['action_create_order']}</a>    
                                            </li>        
                                        </ul>
                                        
EOF;
                                    }

                                    if($CMS->permit['store_request_add_request_ei'] == 1)
                                    {
                                        $output .=<<<EOF
                                                <ul style="display:block">
                                                <li style='display:inline-block;margin-left:10px;width:auto !important;border:none !important'>
                                                    <i class="fa fa-refresh" aria-hidden="true"></i>
                                                </li>
                                                <li style='display:inline-block;width:auto !important'>
                                                    <a class="dropdown-item" href='{$CMS->vars['root_domain']}/?site=assets&subact=transfer&asset_id={$data['ass_id']}'>{$CMS->lang['action_move_store']}</a>
                                                </li>
                                                </ul>    
                                                
                                                <ul style="display:block">
                                                <li style='display:inline-block;margin-left:10px;color:red;width:auto !important;border:none !important'>
                                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                </li>
                                                <li style='display:inline-block;width:auto !important'>
                                                    <a class="dropdown-item" href='{$CMS->vars['root_domain']}/?site=assets&subact=export&asset_id={$data['ass_id']}'>{$CMS->lang['action_export_store']}</a>
                                                </li>    
                                                </ul>            
EOF;

                                    }

        if (!defined("is_web_us")) {
            if($CMS->permit['assets_is_root'] || $CMS->permit['assets_preview_barcode'])
            {
                $output .= <<<EOF
                                                <ul style="display:block; cursor: pointer" onclick="print_barcode_popup('{$data['ass_key']}')">
                                                    <li style='display:inline-block;margin-left:10px;width:auto !important;border:none !important'>
                                                        <i class="fa fa-barcode" aria-hidden="true"></i></i>
                                                    </li>
                                                    <li style='display:inline-block;width:auto !important'>
                                                        <span class="dropdown-item">{$CMS->lang['print_barcode']}</span>    
                                                    </li>   
                                                </ul>
EOF;

            }
        }



                                   $output .=<<<EOF
                                           
                                            </div>
                                    </div>
EOF;
						if($CMS->permit['assets_edit'] == 1)
						{
							$output .=<<<EOF
							<a href="{$CMS->vars['root_domain']}/?site=assets&act=edit&id={$data['ass_id']}" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

						}
						if($CMS->permit['assets_delete'] == 1)
						{
							$output .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=assets&act=delete&id={$data['ass_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
					 
EOF;

						}
					$output .=<<<EOF
</td>                                                        
			</tr>
EOF;
		return $output;
	}
    
	
 
	
	public function none() {
		global $CMS, $DB, $member;
		$output=<<<EOF
				<tr>
					<td colspan="9" align="center" class="no_data"><h6>Not assets</h6></td>
				</tr>
EOF;
		return $output;
	}

	public function show($data=NULL) {

		global $CMS, $DB, $member;
		$output.=<<<EOF
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['ass_info']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=assets" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

 <figure class="box-typical box-typical box-typical-padding border">
 	<div class="row">
		<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_id']}</label>
			<div class="col-xl-9 form-control-span2"> 
					#{$data['ass_id']}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_name']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['ass_name_bk']}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['pgroup_id']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['pgroup_id_bk']}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_code']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['ass_code']}
			</div>
		</fieldset>

		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['store_id']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['store_id']}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['shi_id']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['shi_id']}
			</div>
		</fieldset>

		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_supplier_id']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['supplier_id']}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_desc']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['ass_desc']}
			</div>
		</fieldset>


	</div>
    
    <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_purchase_price']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$CMS->class->input->currency($data['data_bk']['ass_purchase_price'])}
			</div>
		</fieldset>
		 <fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_original_price']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$CMS->class->input->currency($data['data_bk']['ass_original_price'])}
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_price']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$CMS->class->input->currency($data['data_bk']['ass_price'])}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_tax']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['ass_tax']}
			</div>
		</fieldset>

		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_status']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['ass_status']}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['ass_time']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['ass_time']}
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-xl-3 form-control-label2">{$CMS->lang['user_id']}</label>
			<div class="col-xl-9 form-control-span2"> 
					{$data['user_name']}
			</div>
		</fieldset>
    </div>
    </div>
    </figure>
</section>
EOF;
	if( $data['list_item'] != "")
    {
    	$output .=<<<EOF
<section class="add_table">
							<div class="table-responsive" style="overflow-x: initial;">
							<table class="table_cus" width="100%">
								<thead>
									<tr>
										<th width="2%">#</th>
										<th width="25%">{$CMS->lang['ass_name']}</th>
										<th width="15%">{$CMS->lang['ass_purchase_price']}</th>
										<th width="15%">{$CMS->lang['ass_price']}</th>
										<th width="10%">{$CMS->lang['ass_code']}</th>
										<th width="15%">{$CMS->lang['ass_warranty']}</th> 
							 
									</tr>
								</thead>	
								<tbody id="data_subitem">
EOF;
						foreach($data['list_item'] as $key => $value)
							{
								$key = $key + 1;
								$output .=<<<EOF
								<tr class="row-item" rowtr="last-row">
									<td> <span class="number">#{$key}</span></td>
									<td> <span>{$value['ass_name']}</span></td>
									<td> <span>{$CMS->class->input->currency($value['ass_purchase_price'])}</span></td>
									<td> <span>{$CMS->class->input->currency($value['ass_price'])}</span></td>
									<td> <span>{$value['ass_code']}</span></td>
                                    <td> <span>{$value['ass_warranty']}</span></td>
								 
								</tr>
EOF;

							}
							$output.=<<<EOF
							</tbody>
							</table>
						</div>		
</section>
EOF;
    }
    $output .=<<<EOF

		<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=assets" class="pull-left cancel">Trở về danh sách</a>
			 
EOF;
						if($CMS->permit['assets_delete'] == 1)
						{
							$output .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=assets&act=delete&id={$data['ass_id']}');"    class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['gdelete']}</a>
					 
EOF;

						}

						if($CMS->permit['assets_edit'] == 1)
						{
							$output .=<<<EOF
							<a  href="{$CMS->vars['root_domain']}/?site=assets&act=edit&id={$data['ass_id']}"   class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['ass_edit']}</a>
EOF;

						}




					$output .=<<<EOF


					<div class="btn-group dropup pull-right hidden-sm-up">
					  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-save"></i>{$CMS->lang['gaction']}
					  </button>
					  <div class="dropdown-menu">
					  	<ul>
									 
EOF;

						if($CMS->permit['assets_delete'] == 1)
						{
							$output .=<<<EOF

							<li class="hidden-sm-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=assets&act=delete&id={$data['ass_id']}');"  id="form_submit_mobile"  title=""><i class="fa fa-plus"></i>{$CMS->lang['gdelete']}</a></li>
EOF;
						}

						if($CMS->permit['assets_edit'] == 1)
						{
							$output .=<<<EOF
							<li class="hidden-sm-up"><a href="{$CMS->vars['root_domain']}/?site=assets&act=edit&id={$data['ass_id']}"  id="form_submit_option_mobile" title=""><i class="fa fa-save"></i>Sửa</a></li>
EOF;

						}




					$output .=<<<EOF
	
						</ul>
					  </div>
					</div>


		</section>

EOF;
		return $output;
	}
    
	public function edit($data=null) 
        {
		global $CMS, $DB, $member;
        
        
        list($row,$list) = $CMS->store->get_list_store($data['store_id']);
		$option_p_product_group = "<option value=''>{$CMS->lang['select']}</option>";
		$group = $CMS->product_group->getAll();
		foreach ($group as $g) 
        {
			if ($data['pgroup_id'] == $g['product_group_id']) {
				$option_p_product_group .= "<option value='{$g['product_group_id']}' selected>{$g['product_group_name']}</option>";
			} else {
				$option_p_product_group .= "<option value='{$g['product_group_id']}'>{$g['product_group_name']}</option>";
			}
		}
        
	$option_ass_show = "";
        $ass_status = isset($data['ass_status']) ? $data['ass_status'] : 1;
		for ($i = 1; $i >= 0; $i--) {
			if ($i == $ass_status) {
				$option_ass_show .= "   <div class='radio w25'><input type='radio' checked  name='ass_status' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['ass_status_'.$i]}</label></div>";
			} else {
				$option_ass_show .= "   <div class='radio w25'><input type='radio'    name='ass_status' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['ass_status_'.$i]}</label></div>";
			}
		}
        
        $supplier = $CMS->supplier->get_list_supplier(1);
        
        
 
		foreach ($supplier as $s) { 
		 
			if ($s['supplier_id'] == $data['supplier_id_bk']) {
				$option_p_supplier .= "<option value='{$s['supplier_id']}' selected>{$s['supplier_name']}</option>";
			} else {
				 
				$option_p_supplier .= "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
			}
		}

		$ass_tax_0 = "selected";
        $ass_tax_1 = "";

        if($data['ass_tax'] == 10)
        {
            $ass_tax_0 = "";
            $ass_tax_1 = "selected";
        }

		if($data['shi_id_bk'])
        {
            $shi_info = $CMS->shipment->get_info($data['shi_id_bk']);
        }
        
        $max_price_length = $CMS->vars['assets_max_price'] ? strlen((string)$CMS->vars['assets_max_price']) : 11;
        
        // Load user list
        $user_list = $CMS->user->load_list_user($data['ass_user_id']);
        $userg_list = $CMS->user->load_list_userg($data['ass_userg_id']);

		$out=<<<EOF
<script>
 	var add_new_note = "{$CMS->lang['ass_add_new_note']}"; 
        var lang_supplier_add = "{$CMS->lang['ass_add_supplier']}";
        var lang_supplier_edit = "{$CMS->lang['ass_edit_supplier']}";
        var module_name = "assets";
        var ass_expect_id = "{$data['ass_id']}";
 </script>    

		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=assets&act=edit_do&id={$data['ass_id']}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="p_type" value="0">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['ass_edit']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=assets" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
    
 <figure class="box-typical box-typical box-typical-padding border">
 	<div class="row">
		<div class="col-md-6">
                
                
                <div class="row">
                	<div class="col-xl-12"> 
                    <fieldset class="form-group">
			<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
                    if($row < 3)
                    {
                    	$out .=<<<EOF
                        {$list}
EOF;
                    }
                    else
                    {
                    	$out .=<<<EOF
					   <select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_store']}">						
                       		{$list}
                                            </select>
EOF;
                    }
                    $out .=<<<EOF
		</fieldset></div>
                
                    <div class="col-xl-6">        
                        <fieldset class="form-group">
                                <label class="form-label pull-left" for="cat_id">{$CMS->lang['cat_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
				 	if($CMS->permit["product_group_add"] == 1)
					{
						$out .=<<<EOF
						<a data-size="s" class="add_new_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>	</a>
EOF;

					}					
					
					if($CMS->permit["product_group_edit"] == 1)
					{
						$out .=<<<EOF
                                                        <span class="box_product_group pull-right" style="margin-left:10px">
                                                         <a id='{$data['pgroup_id']}' class='btn_gen btn_edit_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>    
                                                        </span>
                       
EOF;
					}

 
					$out .=<<<EOF
                                <select name="pgroup_id" id="pgroup_id" defaultvalue="{$data['pgroup_id']}" class="form-control select_product_group select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_cat']}">						
                                        {$option_p_product_group}
                                </select>
                        </fieldset>
                    </div>
                    <div class="col-xl-6">
                        <fieldset class="form-group" >
                                <label class="form-label pull-left" for="ass_name">{$CMS->lang['ass_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
                                if($CMS->permit['product_edit'] AND $data['product_id'])
                                {
                                    $out .=<<<EOF
                                    <a data-size="s" id="{$data['product_id']}" class="edit_product pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>        
EOF;
                                }
                                $out .=<<<EOF
                                
                                
                                <input name="ass_name" id="ass_name" size="45" type="text" value="{$data['ass_name']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_name']}" autocomplete="off" readonly>
                                <input type="hidden" name="product_id" id="product_id" value="{$data['product_id']}" />  
                                <div id="ass_list" style="display:none" ></div>
                        </fieldset>
                    </div>
                </div>
   
                <div class="row">
                    <div class="col-xl-6">
                        <fieldset class="form-group">
			<label class="form-label pull-left" for="supplier_id">{$CMS->lang['ass_supplier_id']}</label>
EOF;
				 	if($CMS->permit["supplier_add"] == 1)
					{
						$out .=<<<EOF
						<a data-size="s" class="add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>	</a>
EOF;

					}					
					
					if($CMS->permit["supplier_edit"] == 1)
					{
                    	if($data['supplier_id'])
                        {
						$out .=<<<EOF
						<a id='{$data['supplier_id']}' class='btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;
                        }
                        else
                        {
						$out .=<<<EOF
						<span class="box_edit_supplier pull-right" style="margin-left:10px"></span>
EOF;
                        }
					}

 
					$out .=<<<EOF
					
					   <select name="supplier_id" id="supplier_id" value="{$data['supplier_id']}" class="form-control select_supplier select2" for="change">
                                                <option value=''>{$CMS->lang['select']}</option>
                                                {$option_p_supplier}
                                           </select>
                        </fieldset>
                        <fieldset class="form-group">
                            <div class="col-xl-3 col-lg-8 col-sm-9 col-xs-12"></div>
                            <div class="col-xl-9 col-lg-8 col-sm-9 col-xs-12"><div id="sup_list" style="display:none"></div>
                            </div>
                        </fieldset>
    
                    </div>
                    <div class="col-xl-6">
                        <fieldset class="form-group">
                                <label class="form-label" for="shi_id">{$CMS->lang['shi_id']}</label>
                                                   <input name="shi_name" id="shi_name" value="{$shi_info['shi_name']}" class="form-control" autocomplete="off">
                                                   <input type="hidden" name="shi_id" id="shi_id" value="{$shi_info['shi_id']}"/>
                                                   <div id="shi_list" style="display:none"></div>
                        </fieldset>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xl-3">
                        <fieldset class="form-group">
                                <label class="form-label" for="ass_purchase_price">{$CMS->lang['ass_purchase_price']}<font style="margin-left:5px" color="#FF0000">(*)</font><i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></label>
                                                        <input name="ass_purchase_price" id="ass_purchase_price" size="45" type="text" value="{$data['ass_purchase_price']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_pprice']}" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" />
                        </fieldset>
                    </div>
                     <div class="col-xl-3">
                        <fieldset class="form-group">
                                <label class="form-label" for="ass_original_price">{$CMS->lang['ass_original_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></label>
								<input name="ass_original_price" id="ass_price" size="45" type="text" value="{$data['ass_original_price']}" class="form-control" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" />
                        </fieldset>
                    </div>
                    <div class="col-xl-3">
                        <fieldset class="form-group">
                                <label class="form-label" for="ass_price">{$CMS->lang['ass_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></label>
								<input name="ass_price" id="ass_price" size="45" type="text" value="{$data['ass_price']}" class="form-control" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" />
                        </fieldset>
                    </div>
                    
                    <div class="col-xl-3">
                        <fieldset class="form-group">
							<label class="form-label" for="ass_tax">{$CMS->lang['ass_tax']}</label>
							<select name="ass_tax" class="form-control bootstrap-select">
											<option value="0" {$ass_tax_0}>0%</option>
											<option value="10" {$ass_tax_1}>10%</option>
							</select>
                        </fieldset>
                    </div>
                </div>  
 

        </div>
        
        <div class="col-md-6">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-xl-4 col-lg-12 mb1rem">
                                    <label class="form-label" for="ass_code">{$CMS->lang['ass_code']}</label>
                                    <input name="ass_code" id="ass_code" size="45" type="text" value="{$data['ass_code']}" class="form-control" autocomplete="off">
                                </div>    
                            
                                
                            
                                <div class="col-xl-4 col-lg-12 mb1rem">
                                    <label class="form-label" for="ass_year">{$CMS->lang['ass_guarantee_default']}</label>
                                    <input placeholder="{$CMS->lang['unit_month']}" name="ass_guarantee_default" id="ass_guarantee_default" size="45" type="text" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" min="0" max="999" maxlength="3" value="{$data['ass_guarantee_default']}" class="form-control" autocomplete="off">
                                </div>    
                                 
                            
                            
                                <div class="col-xl-4 col-lg-12 mb1rem">
                                <label class="form-label" >{$CMS->lang['ass_warranty']}</label>
                                                        <div class='input-group'>
                                                        <input name="ass_warranty" id="ass_warranty" type="text" value="{$data['ass_warranty']}" class="form-control datetimepicker-1">

                                 <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                
                            </div>
                                
                        </fieldset>
                    
                        

                                
               
            <fieldset class="form-group">
                <label class="form-label" >{$CMS->lang['ass_desc']}</label>
                <textarea name="ass_desc" id="ass_desc" rows="3" cols="50" class="form-control">{$data['ass_desc']}</textarea>
            </fieldset>
            
            <fieldset class="form-group">
            				<div class="row">
                                  <div class="col-xl-6 col-lg-12">
									<h4 style="font-family: 'robor', sans-serif;color:#000">{$CMS->lang['ass_stocktaking_info']}</h4>
                                    <ul class="row">
                                    	<li class="col-xl-12 col-md-4 col-sm-6">
										<p class="form-control-static">
										<select class="form-control select2" name="ass_userg_id" id="ass_userg_id">
											<option value="">{$CMS->lang['ass_select_userg_id']}</option>
											{$userg_list}
										</select>
										</p>
										</li>
                                        
                                    	<li class="col-xl-12 col-md-4 col-sm-6">
										<p class="form-control-static">
										<select class="form-control select2" name="ass_user_id" id="ass_user_id">
                                       		<option value="">{$CMS->lang['ass_select_user_id']}</option>
											{$user_list}
										</select>
										</p>
										</li>
                                        
                                        <li class="col-xl-12 col-md-4 col-sm-6">
											<div class='input-group'>
											<input name="ass_stocktaking_date" id="ass_stocktaking_date" type="text" value="{$data['ass_stocktaking_date']}"  class="form-control datetimepicker-1" placeholder="{$CMS->lang['ass_stocktaking_date']}">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span>
                                
                          		  			</div>
										</li>
                                    </ul>
								</div>
                                <div class="col-xl-6 col-lg-12">
									<fieldset class="form-group">
                            			<label class="form-label" for="ass_status">{$CMS->lang['ass_status']}</label>
                                                    {$option_ass_show}
                    				</fieldset>
								</div>
							</div>
        </fieldset>
		
        
        </div>
	</div>
</section>

<section class="add_table">
<div class="table_cus"> 
				<div class="table-responsive" style="overflow-x: initial;">
					<table width="100%">
                    	<thead>
						<tr>
                                                        <th width="5%">
                                    <input name="all_top" id="all_top" class="checkbox_all css-checkbox" type="checkbox" style="position: absolute;overflow: hidden;height: 1px;width: 1px;margin: -1px;padding: 0;border: 0;"> 
                                    <label class="css-label-checkbox-1" for="all_top" onclick="javascript:form_checkbox_all('form-signin_v1'); return false;" ></label>
                                                        </th>
                                                        <th width="5%">#</th>
							<th scope="col" width="20%">{$CMS->lang['ass_name']}</th>
							
							<th scope="col" width="12%">{$CMS->lang['ass_purchase_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></th>
                            <th scope="col" width="15%">{$CMS->lang['ass_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></th>
                            <th scope="col" width="7%">{$CMS->lang['ass_tax']}</th>
							<th scope="col" width="10%">{$CMS->lang['ass_code']}</th>
                            <th scope="col" width="5%">{$CMS->lang['ass_guarantee_default_sub']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['ass_guarantee_default']}"></i></th>
							<th scope="col" width="10%">{$CMS->lang['ass_warranty']}</th>
                            <th scope="col" width="2%"></th>
						</tr>
                        </thead>
						<tbody id="data_table">
EOF;
                            for($i=0;$i <= count($data['list_item']);$i++)
                            {
                            	$last_row = $i == (count($data['list_item']) - 1) ? "last-row" : "";
                                $select_tax_0 = $data['list_item'][$i]['ass_tax'] == 0 ? "selected='selected'" : "";
                                $select_tax_10 = $data['list_item'][$i]['ass_tax'] == 10 ? "selected='selected'" : "";
                                $key = $i+1;
                            	$out .=<<<EOF
                                <tr class="row-grid move_subitem_row_{$data['list_item'][$i]['ass_id']}" rowtr="{$last_row}">
                                    <td>
                                        <input class="checkbox css-checkbox subitem-for-move" type="checkbox" name="id_{$key}" id="id_{$key}" value="{$data['list_item'][$i]['ass_id']}" style="position: absolute;overflow: hidden;height: 1px;width: 1px;margin: -1px;padding: 0;border: 0;"/>
                                        <label class="css-label-checkbox-1" for="id_{$key}" onclick="javascript:form_checkbox(this,'form-signin_v1');"></label>
                                    </td>
                                    <td class="grid-td" for="dgrid-1"><span class="number">#{$key}</span></td>
                                    <td class="grid-td" for="dgrid-2">
                                	<div class="box_container">
                                    <figure class="text_r">
									<input type="text" for="dgrid-2" name="sub_name[]" check="chtd" class="form-control find_product tr_name" autocomplete="off" value="{$data['list_item'][$i]['ass_name']}"/>
                                   	</figure>
                                    <div class="box_result_find" style="display:none" ></div>
                                    </div>
                                    <input type="hidden" class="product_id" name="sub_id[]" value="{$data['list_item'][$i]['product_id']}" value=""/>
                                    <input type="hidden" class="sub_ass_id" name="sub_ass_id[]" value="{$data['list_item'][$i]['ass_id']}" value=""/>
                                    </td>
    
                                    <td class="grid-td" for="dgrid-5">
                                        <input type="text" for="dgrid-5" name="sub_purchase_price[]" value="{$data['list_item'][$i]['ass_purchase_price']}" class="form-control tr_pprice" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}"/>
                                    </td>
                                                            
                                    <td class="grid-td" for="dgrid-9">
                                        <input type="text" for="dgrid-9" name="sub_price[]" value="{$data['list_item'][$i]['ass_price']}" class="form-control tr_price" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}"/>
                                    </td>
                                    
								<td data-title="{$CMS->lang['ass_tax']}" class="grid-td" for="dgrid-10"> 
											<figure class="text_r"> 	
												<select for="dgrid-10" class="form-control tax_list" name ="sub_tax[]">
														<option value="0" {$select_tax_0}>0%</option>
														<option value="10" {$select_tax_10}>10%</option>
													</select>
											</figure>	 
								</td>                                
                                    
                                    <td class="grid-td" for="dgrid-3"><input type="text" for="dgrid-3" name="sub_code[]" value="{$data['list_item'][$i]['ass_code']}" class="form-control tr_code"></td>
                                    
                                <td  class="grid-td" for="dgrid-8">
									<input for="dgrid-8" name="sub_guarantee_default[]" check="chtd" type="text" onkeypress="return check_enter_number(event,this);" min="0" max="999" maxlength="3" onfocusout="check_enter_number_2(this,0);" class="form-control" autocomplete="off">

                                </td>
    
                                    <td class="grid-td" for="dgrid-7">
                                        <input for="dgrid-7" name="sub_warranty[]" id="sub_warranty" value="{$data['list_item'][$i]['ass_warranty']}" type="text" class="form-control datetimepicker-1">
                                    </td>
    
                                    <td class="trash" for="dgrid-8"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
                                </tr>
EOF;
                            }
                            $out .=<<<EOF
						</tbody>
					</table>
                                <input type="button" class="pull-left btn btn-inline btn-primary ladda-button move-subitem" style="text-transform: uppercase;font-size:12px;font-family: robob;line-height:15px;margin-left:0 !important;margin-top:10px;margin-bottom:5px" value="{$CMS->lang['ass_move_subitem']}">
				</div>
</div>
</section>

		<section class="add_cart_footer">
			<input type="hidden" value="0" name="add_product_option"  />
			<a href="{$CMS->vars['root_domain']}/?site=assets" class="pull-left cancel">Trở về danh sách</a>
                        <a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$data['ass_id']}" class="pull-left cancel">Trở về xem chi tiết</a>
                        
                        <!--<input type="button" class="pull-right btn btn-inline btn-primary ladda-button move-subitem" style="text-transform: uppercase;font-size:12px;font-family: robob;line-height:25px" value="{$CMS->lang['ass_move_subitem']}">-->
                        <input type="hidden" name="ass_is_edit_all" id="ass_is_edit_all" value="0" />
                        <input type="button" class="pull-right btn btn-inline btn-primary ladda-button edit_all_assets" style="text-transform: uppercase;font-size:12px;font-family: robob;line-height:25px" value="{$CMS->lang['ass_edit_all_type']}">
			<button id="form_submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs">
                            <span class="ladda-label">{$CMS->lang['ass_edit']}</span>
                                <span class="ladda-spinner"></span>
                        </button>
			

		</section>

</form>
<script>
 $("#form_submit").click(function(){ });
	$('.datetimepicker-1').datetimepicker({
                                format:'DD/MM/YYYY',
			});                           

</script>
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_assets.js"></script>                       
                        {$this->formAddSupplier()}
                        {$this->formAddproductgroup()}
                        {$CMS->global->formProduct()}
                        {$this->form_move_subitem()}
EOF;
		return $out;
	}
    
	public function add($data) 
    {
		global $CMS, $DB, $member;
        
        $data['ass_status'] = $data['ass_status'] ? $data['ass_status'] : 1;
        $data['ass_warranty'] = strip_tags($data['ass_warranty']);
        $data['ass_quantity'] = $data['ass_quantity'] ? $data['ass_quantity'] : 1;
        
        list($row,$list) = $CMS->store->get_list_store($data['store_id']);
        
		$option_p_product_group = "<option value=''>{$CMS->lang['select']}</option>";
		$group = $CMS->product_group->getAll();
		foreach ($group as $g) 
        {
			if ($data['pgroup_id'] == $g['product_group_id']) {
				$option_p_product_group .= "<option value='{$g['product_group_id']}' selected>{$g['product_group_name']}</option>";
			} else {
				$option_p_product_group .= "<option value='{$g['product_group_id']}'>{$g['product_group_name']}</option>";
			}
		}

		$option_ass_show = "";
        $ass_status = isset($CMS->input['ass_status']) ? $CMS->input['ass_status'] : 1;
		for ($i = 1; $i >= 0; $i--) {
			if ($i == $ass_status) {
				$option_ass_show .= "   <div class='radio w25'><input type='radio' checked  name='ass_status' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['ass_status_'.$i]}</label></div>";
			} else {
				$option_ass_show .= "   <div class='radio w25'><input type='radio'    name='ass_status' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['ass_status_'.$i]}</label></div>";
			}
		}
                
        $supplier = $CMS->supplier->get_list_supplier(1);
 
		foreach ($supplier as $s) { 
		 
			if ($s['supplier_id'] == $data['supplier_id']) {
				$option_p_supplier .= "<option value='{$s['supplier_id']}' selected>{$s['supplier_name']}</option>";
			} else {
				 
				$option_p_supplier .= "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
			}
		}  
                
        $max_price_length = $CMS->vars['assets_max_price'] ? strlen((string)$CMS->vars['assets_max_price']) : 11;
        $max_quantity_length = $CMS->vars['assets_max_quantity'] ? strlen((string)$CMS->vars['assets_max_quantity']) : 7;
        
        // Load user list
        $user_list = $CMS->user->load_list_user($data['ass_user_id']);
        $userg_list = $CMS->user->load_list_userg($data['ass_userg_id']);
        
		$out=<<<EOF
 <script>
 	var add_new_note = "{$CMS->lang['ass_add_new_note']}"; 
        var lang_supplier_add = "{$CMS->lang['ass_add_supplier']}";
        var lang_supplier_edit = "{$CMS->lang['ass_edit_supplier']}";
        var module_name = "assets";
 </script>       

		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=assets&act=add_do" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="p_type" value="0">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['ass_add']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=assets" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
    
 <figure class="box-typical box-typical box-typical-padding border">
 	<div class="row">
		<div class="col-md-6">
                
                <div class="row">
                        	<div class="col-xl-12"> 
                    <fieldset class="form-group">
					<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
                    if($row < 3)
                    {
                    	$out .=<<<EOF
                        {$list}
EOF;
                    }
                    else
                    {
                    	$out .=<<<EOF
					   <select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_store']}">						
                       		{$list}
                                            </select>
EOF;
                    }
                    $out .=<<<EOF
		</fieldset></div>                    <div class="col-xl-6">        
                        <fieldset class="form-group">
                                <label class="form-label pull-left" for="cat_id">{$CMS->lang['cat_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
				 	if($CMS->permit["product_group_add"] == 1)
					{
						$out .=<<<EOF
						<a data-size="s" class="add_new_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>	</a>
EOF;

					}					
					
					if($CMS->permit["product_group_edit"] == 1)
					{
						$out .=<<<EOF
						<span class="box_product_group pull-right" style="margin-left:10px"></span>
EOF;
					}

 
					$out .=<<<EOF
                                <select name="pgroup_id" id="pgroup_id" defaultvalue="{$data['pgroup_id']}" class="form-control select_product_group select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_cat']}">						
                                        {$option_p_product_group}
                                </select>
                        </fieldset>
                    </div>
                    <div class="col-xl-6">
                        <fieldset class="form-group" >
                                <label class="form-label" for="ass_name">{$CMS->lang['ass_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <input name="ass_name" id="ass_name" size="45" type="text" value="{$data['ass_name']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_name']}" autocomplete="off">
                                <input type="hidden" name="product_id" id="product_id" value="{$data['product_id']}" />  
                                <div style="position: absolute;z-index:10000;background-color: #fff;border: 1px solid #d8e2e7;min-width:180px;display:none" id="ass_list" ></div>
                        </fieldset>
                    </div>
                </div>
   
                <div class="row">
                    <div class="col-xl-6">
                        <fieldset class="form-group">
			<label class="form-label pull-left" for="supplier_id">{$CMS->lang['ass_supplier_id']}</label>
EOF;
				 	if($CMS->permit["supplier_add"] == 1)
					{
						$out .=<<<EOF
						<a data-size="s" class="add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>	</a>
EOF;

					}					
					
					if($CMS->permit["supplier_edit"] == 1)
					{
						$out .=<<<EOF
						<span class="box_edit_supplier pull-right" style="margin-left:10px"></span>
EOF;
					}

 
					$out .=<<<EOF
					
					   <select name="supplier_id" id="supplier_id" value="{$data['supplier_id']}" class="form-control select_supplier select2" for="change">
                                                <option value=''>{$CMS->lang['select']}</option>
                                                {$option_p_supplier}
                                           </select>
                        </fieldset>
                        <fieldset class="form-group">
                            <div class="col-xl-3 col-lg-8 col-sm-9 col-xs-12"></div>
                            <div class="col-xl-9 col-lg-8 col-sm-9 col-xs-12"><div id="sup_list"></div>
                            </div>
                        </fieldset>
    
                    </div>
                    <div class="col-xl-6">
                        <fieldset class="form-group">
                                <label class="form-label" for="shi_id">{$CMS->lang['shi_id']}</label>
                                                   <input name="shi_name" id="shi_name" value="{$data['shi_name']}" class="form-control" autocomplete="off">
                                                   <input type="hidden" name="shi_id" id="shi_id" />
                                                   <div id="shi_list" style="display:none"></div>
                        </fieldset>
                    </div>
                </div>
                
                <div class="row">
                    
                    <div class="col-xl-3">
                        <fieldset class="form-group">
                                <label class="form-label" for="ass_purchase_price">{$CMS->lang['ass_purchase_price']}<font style="margin-left:5px" color="#FF0000">(*)</font><i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></label>
								<input name="ass_purchase_price" id="ass_purchase_price" size="45" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" type="text" value="{$data['ass_purchase_price']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_pprice']}">
                        </fieldset>
                    </div>
                    <div class="col-xl-3">
                        <fieldset class="form-group">
                                <label class="form-label" for="ass_price">{$CMS->lang['ass_original_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></label>
								<input name="ass_original_price" id="ass_price" size="45" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" type="text" value="{$data['ass_original_price']}" class="form-control" >
                        </fieldset>
                    </div> 
                    
                    <div class="col-xl-3">
                        <fieldset class="form-group">
                                <label class="form-label" for="ass_price">{$CMS->lang['ass_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></label>
								<input name="ass_price" id="ass_price" size="45" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" type="text" value="{$data['ass_price']}" class="form-control" >
                        </fieldset>
                    </div> 
				</div>
                <div class="row">
                    <div class="col-xl-3">
                        <fieldset class="form-group">
                                <label class="form-label" for="ass_quantity">{$CMS->lang['ass_quantity']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <input name="ass_quantity" id="ass_quantity" size="45" type="text" value="{$data['ass_quantity']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_quantity']}" min="{$CMS->vars['assets_min_quantity']}" max="{$CMS->vars['assets_max_quantity']}" maxlength="{$max_quantity_length}" />
                        </fieldset>
                    </div>
                   
                    
                    <div class="col-xl-3">
                        <fieldset class="form-group">
							<label class="form-label" for="ass_tax">{$CMS->lang['ass_tax']}</label>
							<select name="ass_tax" class="form-control bootstrap-select">
											<option value="0">0%</option>
											<option value="10" selected>10%</option>
							</select>
                        </fieldset>
                    </div>
				</div>
        </div>
        
        <div class="col-md-6">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-xl-4 col-lg-12 mb1rem">
                                    <label class="form-label" for="ass_code">{$CMS->lang['ass_code']}</label>
                                    <input name="ass_code" id="ass_code" size="45" type="text" value="{$data['ass_code']}" class="form-control" autocomplete="off">
                                </div>    
                            
                                
                            
                                <div class="col-xl-4 col-lg-12 mb1rem">
                                    <label class="form-label" for="ass_year">{$CMS->lang['ass_guarantee_default']}</label>
                                    <input placeholder="{$CMS->lang['unit_month']}" name="ass_guarantee_default" id="ass_guarantee_default" size="45" type="text" min="0" max="999" maxlength="3" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" value="{$data['ass_guarantee_default']}" class="form-control" autocomplete="off">
                                </div>    
                                 
                            
                            
                                <div class="col-xl-4 col-lg-12">
                                <label class="form-label" >{$CMS->lang['ass_warranty']}</label>
									<div class='input-group'>
										<input name="ass_warranty" id="ass_warranty" type="text" value="{$data['ass_warranty']}"  class="form-control datetimepicker-1">
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
                                
                          		  </div>
                                  
                        	</fieldset>
                    
                        

                                
               
            <fieldset class="form-group">
                <label class="form-label" >{$CMS->lang['ass_desc']}</label>
                <textarea name="ass_desc" id="ass_desc" rows="3" cols="50" class="form-control">{$data['ass_desc']}</textarea>
            </fieldset>
            <fieldset class="form-group" style="margin-bottom: 0px !important">
            				<div class="row">
                                  <div class="col-xl-6 col-lg-12">
									<h4 style="font-family: 'robor', sans-serif;color:#000;margin-top:0px !important">{$CMS->lang['ass_stocktaking_info']}</h4>
                                    <ul class="row">
                                    	<li class="col-xl-12 col-md-4 col-sm-6">
										<p class="form-control-static">
										<select class="form-control select2" name="ass_userg_id" id="ass_userg_id">
											<option value="">{$CMS->lang['ass_select_userg_id']}</option>
											{$userg_list}
										</select>
										</p>
										</li>
                                        
                                    	<li class="col-xl-12 col-md-4 col-sm-6">
										<p class="form-control-static">
										<select class="form-control select2" name="ass_user_id" id="ass_user_id">
                                       		<option value="">{$CMS->lang['ass_select_user_id']}</option>
											{$user_list}
										</select>
										</p>
										</li>
EOF;
                                        if($_SESSION['is_mobile'] == true)
                                        {
                                            $out .=<<<EOF
                                            
                                            <li class="col-xl-12 col-md-4 col-sm-6" style="margin-bottom: 15px !important" >
                                                <div class='input-group'>
                                                <input name="ass_stocktaking_date" id="ass_stocktaking_date" type="text" value="{$data['ass_stocktaking_date']}"  class="form-control datetimepicker-1" placeholder="{$CMS->lang['ass_stocktaking_date']}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                    
                                                </div>
                                            </li>
EOF;
                                        }
                                        else
                                        {
                                        $out .=<<<EOF
                                        <li class="col-xl-12 col-md-4 col-sm-6">
											<div class='input-group'>
											<input name="ass_stocktaking_date" id="ass_stocktaking_date" type="text" value="{$data['ass_stocktaking_date']}"  class="form-control datetimepicker-1" placeholder="{$CMS->lang['ass_stocktaking_date']}">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span>
                                
                          		  			</div>
										</li>
EOF;
                                        }
$out .=<<<EOF
                                    </ul>
								</div>
                                <div class="col-xl-6 col-lg-12">
									<fieldset class="form-group" style="margin-bottom: 0px !important">
                            			<label class="form-label" for="ass_status">{$CMS->lang['ass_status']}</label>
                                                    {$option_ass_show}
                    				</fieldset>
								</div>
							</div>
        </fieldset>
                                                            
        </div>
	</div>
</section>

<section class="add_table">
 
				
					<table width="100%" class="responsive-table table_cus">
                                        <thead>
						<tr>
                                                        <th scope="col" width="2%"></th>
							<th scope="col" width="2%">STT</th>
							<th scope="col" width="20%">{$CMS->lang['ass_name']}</th>
							
							<th scope="col" width="5%">{$CMS->lang['ass_quantity']}</th>
							<th scope="col" width="12%">{$CMS->lang['ass_purchase_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></th>
                            <th scope="col" width="15%">{$CMS->lang['ass_price']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['price_novat']}"></i></th>
                            <th  scope="col" width="7%">{$CMS->lang['ass_tax']}</th>
							<th scope="col" width="10%">{$CMS->lang['ass_code']}</th>
                            <th scope="col" width="5%">{$CMS->lang['ass_guarantee_default_sub']}<i style="margin-left:5px" class="fa fa-question-circle" aria-hidden="true" title="{$CMS->lang['ass_guarantee_default']}"></i></th>
							<th scope="col" width="10%">{$CMS->lang['ass_warranty']}</th>
                            <th scope="col" width="2%"></th>
						</tr>
                        </thead>
						<tbody id="data_table">
EOF;
                                                    if(empty($data['sub_name']))
                                                    {
                                                        $out .=<<<EOF
                                                            <tr class="row-grid" rowtr="last-row">
                                                                <td scope="row"  ><i class="hidden-sm-down fa fa-th handle"></i></td>
								<td class="grid-td" for="dgrid-1"><span class="number">#1</span></td>
								<td class="grid-td" for="dgrid-2">
                                                                    <div class="box_container">
                                                                    <figure class="text_r">
									<input type="text" for="dgrid-2" name="sub_name[]" check="chtd" class="form-control find_product tr_name" autocomplete="off" placeholder="{$CMS->lang['ass_name']}"/>
                                                                    </figure>
                                                                    <div class="box_result_find" style="display:none" ></div>
                                                                    </div>
                                                                    <input type="hidden" class="product_id tr_id" name="sub_id[]" value=""/>
								</td>

								<td class="grid-td" for="dgrid-3">
                                                                    <input type="text" check="chtd" for="dgrid-3" name="sub_quantity[]" value="1" class="form-control quan_list tr_quantity" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_quantity']}" max="{$CMS->vars['assets_max_quantity']}" maxlength="{$max_quantity_length}" placeholder="{$CMS->lang['ass_quantity']}"/>
								</td>

								<td class="grid-td" for="dgrid-4">
                                                                    <input type="text" check="chtd" for="dgrid-4" name="sub_purchase_price[]" class="form-control tr_pprice" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" placeholder="{$CMS->lang['ass_purchase_price']}"/>
                                                                </td>
                                                        
								<td class="grid-td" for="dgrid-5">
                                                                    <input type="text" check="chtd" for="dgrid-5" name="sub_price[]" class="form-control tr_price" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" placeholder="{$CMS->lang['ass_price']}"/>
								</td>
                                
								<td data-title="{$CMS->lang['ass_tax']}" class="grid-td" for="dgrid-10"> 
											<figure class="text_r"> 	
												<select for="dgrid-10" class="form-control tax_list select2" name ="sub_tax[]">
														<option value="0">0%</option>
														<option value="10" selected="selected">10%</option>
													</select>
											</figure>	 
								</td>                                
								<td class="grid-td" for="dgrid-9"><input type="text" check="chtd" for="dgrid-9" name="sub_code[]" class="form-control tr_code" placeholder="{$CMS->lang['ass_code']}"></td>
                                <td  class="grid-td" for="dgrid-8">
									<input for="dgrid-8" name="sub_guarantee_default[]" check="chtd" type="text" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" class="form-control" autocomplete="off" min="0" max="999" maxlength="3" placeholder="{$CMS->lang['ass_guarantee_default']}">

                                </td>

								<td class="grid-td" for="dgrid-7">
									<input for="dgrid-7" name="sub_warranty[]" check="chtd" id="sub_warranty" type="text" class="form-control tr_warranty datetimepicker-1" placeholder="{$CMS->lang['ass_warranty']}">
								</td>

								<td class="trash" for="dgrid-8"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
							</tr>
EOF;
                                                    }
                                                    else
                                                    {
                                                        for($i=0;$i<count($data['sub_name']);$i++)
                                                        {
                                                            $key = $i+1;
                                                            $last_row = $i == count($data['sub_name'])-1 ? "last-row" : "";
                                                            $out.=<<<EOF
                                                            <tr class="row-grid" rowtr="{$last_row}">
                                                                <td scope="row"  ><i class="hidden-sm-down fa fa-th handle"></i></td>
								<td class="grid-td" for="dgrid-1"><span class="number">#{$key}</span></td>
								<td class="grid-td" for="dgrid-2">
                                                                    <div class="box_container">
                                                                    <figure class="text_r">
									<input type="text" for="dgrid-2" name="sub_name[]" value="{$data['sub_name'][$i]}" check="chtd" class="form-control find_product tr_name" autocomplete="off" placeholder="{$CMS->lang['ass_name']}"/>
                                                                    </figure>
                                                                    <div class="box_result_find" style="display:none" ></div>
                                                                    </div>
                                                                    <input type="hidden" class="product_id" name="sub_id[]" value=""/>
								</td>

								<td class="grid-td" for="dgrid-3">
									<input type="text" for="dgrid-3" name="sub_quantity[]" value="{$data['sub_quantity'][$i]}" class="form-control quan_list tr_quantity" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_quantity']}" max="{$CMS->vars['assets_max_quantity']}" maxlength="{$max_quantity_length}" placeholder="{$CMS->lang['ass_quantity']}"/>
								</td>

								<td class="grid-td" for="dgrid-4">
                                                                    <input type="text" for="dgrid-4" name="sub_purchase_price[]" value="{$data['sub_purchase_price'][$i]}" class="form-control tr_pprice" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" placeholder="{$CMS->lang['ass_purchase_price']}"/>
                                                                </td>
                                                        
								<td class="grid-td" for="dgrid-5">
                                                                    <input type="text" for="dgrid-5" name="sub_price[]" value="{$data['sub_price'][$i]}" class="form-control tr_price" onkeypress="return check_enter_number(event,this);" min="{$CMS->vars['assets_min_price']}" max="{$CMS->vars['assets_max_price']}" maxlength="{$max_price_length}" placeholder="{$CMS->lang['ass_price']}"/>
								</td>
                                
								<td class="grid-td" for="dgrid-6"><input type="text" for="dgrid-6" name="sub_code[]" class="form-control tr_code" placeholder="{$CMS->lang['ass_code']}"></td>
                                                                
                                                                <td  class="grid-td" for="dgrid-8">
									<input for="dgrid-8" name="sub_guarantee_default[]" check="chtd" type="text" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" class="form-control" autocomplete="off" min="0" max="999" maxlength="3" placeholder="{$CMS->lang['ass_guarantee_default']}">
                                                                </td>                                                                    
								<td class="grid-td" for="dgrid-7">
                                                                    <input for="dgrid-7" name="sub_warranty[]" id="sub_warranty" value="{$data['sub_warranty'][$i]}" type="text" class="form-control tr_warranty datetimepicker-1" placeholder="{$CMS->lang['ass_warranty']}">
								</td>

								<td class="trash" for="dgrid-8"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
							</tr>        
EOF;
                                                        }
                                                    }
                                                    $out.=<<<EOF
							
						</tbody>
					</table>


</section>

		<section class="add_cart_footer">
			<input type="hidden" value="0" name="add_product_option"  />
			<a href="{$CMS->vars['root_domain']}/?site=assets" class="pull-left cancel">Trở về danh sách</a>
			<button id="form_submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['ass_add']}</span><span class="ladda-spinner"></span></button>
			

		</section>

</form>
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_assets.js"></script>
<script>
var module_name = "assets";

 $("#form_submit, #form_submit_mobile").click(function(){
 	  
 	 	$("tbody#data_table tr.child").remove();
 	 	$("#trigger_submit").trigger("click");
 	 
 });

	$('.datetimepicker-1').datetimepicker({
                                format:'DD/MM/YYYY',
			}); 
                            
     var table_item = $('#table_item').DataTable({
          'columnDefs': [
             {
                'targets': [1, 2, 3, 4, 5],
                'render': function(data, type, row, meta){
                   if(type === 'display'){
                      var api = new $.fn.dataTable.Api(meta.settings);

                      var el = $('input, select, textarea', api.cell({ row: meta.row, column: meta.col }).node());

 					//  $(  'input,select,textarea').attr('name', el.prop('nodeName')+'_'+el.prop('name'));
                      var _html = $(data).wrap('<div/>').parent();
                      
 					if(el.prop('tagName') === 'INPUT'){
                         $('input', _html).attr('value', el.val());
                       
                         if(el.prop('checked')){
                            $('input', _html).attr('checked', 'checked');
                         }
                      } else if (el.prop('tagName') === 'TEXTAREA'){
                         
                         $('textarea', _html).html(el.val());

                      } else if (el.prop('tagName') === 'SELECT'){
                        
                        // $('option:selected', _html).removeAttr('selected');
                         $('option', _html).filter(function(){
                            return ($(this).attr('value') === el.val());
                         }).attr('selected', 'selected');
                      }
                      data = _html.html();
                   }

                   return data;
                }
             }
          ],
          'responsive': true,
           order: [],
           paging: false,
                  searching: false,
                  info: false

       });
</script>

                        {$CMS->global->formAddSupplier()}
                        {$this->formAddproductgroup()}
                        {$CMS->global->formProduct()}
                        
    
EOF;
		return $out;
	}
        
public function formAddSupplier()
	{
		global $CMS;
                
                $CMS->class->language->load("supplier");

		$output =<<<EOF
		<div id="box_add_supplier" class="popup_add_supplier mfp-hide">
			<p class="title_add change_title" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['supplier_add']}</p>
			<form id="add_supplier_form" name="add_supplier_form">
					<input type="hidden" name="checkReturn" value="" />
				<ul class="list_field_supplier">
				    <li style="min-height: 0px;"><p class="error_msg" style="display:none;"></p></li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_title">{$CMS->lang['supplier_name']}<font style="margin-left:5px;" color="#FF0000">(*)</font></label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input name="supplier_name" id="supplier_name" size="45" type="text" value="{$data['supplier_name']}" class="form-control">
									<input name="supplier_id" type="hidden" value="" />

								</span>
							</div>
						</fieldset>
					</li>
					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_code">{$CMS->lang['supplier_code']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input name="supplier_code" id="supplier_code" size="45" type="text" value="{$data['supplier_code']}" class="form-control">
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_phone">{$CMS->lang['supplier_phone']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input onkeypress="return check_phone(event);" name="supplier_phone" id="supplier_phone" size="45" type="text" value="{$data['supplier_phone']}" class="form-control">
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_email">{$CMS->lang['supplier_email']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input name="supplier_email" id="supplier_email" size="45" type="text" value="{$data['supplier_email']}" class="form-control">
								</span>
							</div>
						</fieldset>
					</li>
				</ul>
			<span class="show_more_info col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="cursor:pointer" check="0"><span class="btn_check"><i class="fa fa-plus-square-o" aria-hidden="true"></i></span> {$CMS->lang['tilte_info_more_supplier']}</span>
			<div class="box_show" style="display: none;">
				<ul class="list_field_supplier">
					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_taxcode">{$CMS->lang['supplier_taxcode']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input onkeypress="return check_enter_number(event,this);" name="supplier_taxcode" id="supplier_taxcode" size="45" type="text" value="{$data['supplier_taxcode']}" class="form-control" >
								</span>
							</div>
						</fieldset>
					</li>
					
					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_get_invoice">{$CMS->lang['supplier_get_invoice']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<select name="supplier_get_invoice" id="supplier_get_invoice" class="form-control select2" defaultvalue="{$data['supplier_get_invoice']}" style="width: 100%">
										<option value="-1">{$CMS->lang['plz_choose']}</option>
			                            <option value="0">{$CMS->lang['no']}</option>
			                            <option value="1">{$CMS->lang['yes']}</option>
									</select>
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_type">{$CMS->lang['supplier_type']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<select name="supplier_type" id="supplier_type" class="form-control select2" onchange="change_supplier_type(this.value)" defaultvalue="{$data['supplier_type']}" style="width: 100%">
										<option value="-1">{$CMS->lang['plz_choose']}</option>
			                            <option value="1">{$CMS->lang['supplier_type_1']}</option>
			                            <option value="2">{$CMS->lang['supplier_type_2']}</option>
									</select>
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group change_supplier">
							<label class="form-control-label row" for="supplier_idcard_number">{$CMS->lang['supplier_idcard_number']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input onkeypress="return check_enter_number(event,this);" name="supplier_idcard_number" id="supplier_idcard_number" size="45" type="text" value="{$data['supplier_idcard_number']}" class="form-control" >
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="city_id">{$CMS->lang['city_id']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<select name="city_id" id="city_id" class="form-control select2" onchange="change_city(this.value)" defaultvalue="{$data['city_id']}" style="width: 100%">
			                        {$CMS->country->getOptionCity(238)}
									</select>
								</span>
							</div>
						</fieldset>
					</li>
					
					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="district_id">{$CMS->lang['district_id']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<select name="district_id" id="district_id" class="form-control select2" style="width: 100%">
			                        	<option value="">{$CMS->lang['select_district']}</option>
			                        </select>
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_address">{$CMS->lang['supplier_address']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input name="supplier_address" id="supplier_address" size="45" type="text" value="{$data['supplier_address']}" class="form-control" >
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_bank">{$CMS->lang['supplier_bank']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input name="supplier_bank" id="supplier_bank" size="45" type="text" value="{$data['supplier_bank']}" class="form-control" >
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_branch">{$CMS->lang['supplier_branch']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input name="supplier_branch" id="supplier_branch" size="45" type="text" value="{$data['supplier_branch']}" class="form-control" >
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_bank_number">{$CMS->lang['supplier_bank_number']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input onkeypress="return check_enter_number(event,this);" name="supplier_bank_number" id="supplier_bank_number" size="45" type="text" value="{$data['supplier_bank_number']}" class="form-control" >
								</span>
							</div>
						</fieldset>
					</li>

					<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_bank_owner">{$CMS->lang['supplier_bank_owner']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<input name="supplier_bank_owner" id="supplier_bank_owner" size="45" type="text" value="{$data['supplier_bank_owner']}" class="form-control">
								</span>
							</div>
						</fieldset>
					</li>
					
					<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_note">{$CMS->lang['supplier_note']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<textarea name="supplier_note" id="supplier_note" rows="5" cols="50" class="form-control">{$data['supplier_note']}</textarea>
								</span>
							</div>
						</fieldset>
					</li>

					<!--li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
						<fieldset class="form-group">
							<label class="form-control-label row" for="supplier_status" style="margin-top: 10px;">{$CMS->lang['supplier_status']}</label>
							<div class="typeahead-field"> 
								<span class="typeahead-query">
									<select name="supplier_status" id="supplier_status" class="form-control select2" defaultvalue="{$data['supplier_status']}" style="width: 100%">
			                            <option value="0">{$CMS->lang['supplier_status_0']}</option>
			                            <option value="1" selected='selected'>{$CMS->lang['supplier_status_1']}</option>
									</select>
								</span>
							</div>
						</fieldset>
					</li-->
				</ul>	
			</div>

				<ul class="list_field_supplier">
					<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
						<fieldset class="form-group">
							<label class="form-control-label"></label>
							<div class="typeahead-field"> 
								<span class="typeahead-query change_action">
									<input class="btn btn_add_supplier" type="button" value="{$CMS->lang['supplier_add']}"/>
								</span>
							</div>
						</fieldset>
					</li>
				
				</ul>
			
			</form>
		</div>

EOF;

		return $output;		
	} 
        
public function formAddproductgroup()
	{
		global $CMS;
		$pg_type = 0;
		$category = $CMS->product_group->getParent_bytype($pg_type);
			$option_pg_parent = "<option value=''>{$CMS->lang['select']}</option>";
		foreach ($category as $c) {
			if ($c['product_group_id'] == $pg_parent) {
				$option_pg_parent .= "<option value='{$c['product_group_id']}' selected>{$c['product_group_name']}</option>";
			} else {
				$option_pg_parent .= "<option value='{$c['product_group_id']}' >{$c['product_group_name']}</option>";
			}
		}

		$pg_type = 0;
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $pg_type ) {
				$option_pg_type .= "
     <div class='radio w25'><input type='radio' checked  name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	

				";
			} else {
				$option_pg_type .= "  <div class='radio w25'><input type='radio' name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	";
			}
		}


		$pg_status = 1;
		for ($i = 1; $i >= 0; $i--) {
			if ($i == $pg_status) {
				$option_pg_status .= "   <div class='radio w25'><input type='radio' checked  name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			} else {
				$option_pg_status .= "   <div class='radio w25'><input type='radio'    name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			}
		}
		$output =<<<EOF
		<div id="box_add_group_product" class="popup_add_group_product mfp-hide">
			<p class="title_group_product  " style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['group_product_add']}</p>
			<p style="color:red" class="pgmanu_error_msg"></p>
			<form id="add_group_product_form" name="add_group_product_form">

				<ul class="list_field_supplier">		
					 <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
					 		<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['pg_parent']}</label>
										<select class="form-control select2" name="pg_parent" id="pg_parent" >
											{$option_pg_parent}
										</select>
									
							</fieldset>
						</li>
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['pg_name']} <span style="color:red">(*)</span></label>
										<input class="form-control" type="text" name="pg_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_name_err']}" value="{$pg_name}">
									
							</fieldset>
						</li>
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['pg_code']}</label>
							
										<input class="form-control " type="text" name="pg_code" id="pg_code" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_code_err']}" value="{$pg_code}">
									
							</fieldset>
						</li>
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
						   <fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['pg_description']}</label>
					
										<textarea rows="3" class="form-control" name="pg_description" id="pg_description" >{$pg_description}</textarea> 
								 
							</fieldset>
						</li>
					  <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
					 		 <fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['pg_type']}</label>
										 
											{$option_pg_type}

							</fieldset>
						</li>
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['pg_status']}</label>	 
											{$option_pg_status}
								
							</fieldset>
						</li>
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['pg_avartar']}</label>

									 
										    <div class="drop-zone fileinput-button">
					                              <img id="upload_img_show" width="205" />

					                              <i class="font-icon font-icon-cloud-upload-2"></i>
					                               <div class="drop-zone-caption">Drag file to upload</div>
					                                 <input type="file"  name="pg_avartar" id="ufile" accept="image/*">
					                         </div><!--.drop-zone-->
					                  		 <div class="actionButtons">
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
					                        
					                 

							</fieldset>
							</li>
							<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
								<fieldset class="form-group">
									<div class="typeahead-field"> 
										<span class="typeahead-query change_action_product_group"><input class="btn btn_add_product_group" type="button" value="Thêm nhóm sản phẩm"></span>
									</div>
								</fieldset>
							</li> 

				</ul>
				  
				<input name="product_group_id" type="hidden" value="" />

			</form>
		</div>	

		 <script src="{$CMS->vars['js_acp']}/product_group.js"></script>	
EOF;
		return $output;
	}  



//===========================================================================
//  USER CONTROL
//===========================================================================

public function control()
{
	global $CMS, $DB, $member;
    
	$output = "";
 
$output .= <<<EOF
<input type="hidden" name="act" id="act" value='' />
<select class="form-control select2" name="subact" id="subact" onchange="return submit_action_control_assets(this,'form_assets');" defaultvalue="delete_all" emsg="{$CMS->lang['incomplete_action']}" ehide="1">
	<option value="">-- {$CMS->lang['ass_action']} --</option>
	{$CMS->vars['action_controller']}

</select>

 
EOF;

    return $output;
}

public function form_move_subitem()
{
		global $CMS;

		$output =<<<EOF
		<div id="box_move_subitem" class="box_move_subitem mfp-hide" style="width:40%">
			<p class="title_group_product" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['ass_move_subitem']}</p>
			<p style="color:red" class="error_msg"></p>
			<form id="move_subitem_form" name="move_subitem_form">
				<input type="hidden" name="list_subitem_for_move" id="list_subitem_for_move"/>
				<ul class="list_field_supplier">
EOF;
                                            if($CMS->input['act']  == "edit")
                                            {
                                                $output .=<<<EOF
						<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">	
                                                    <fieldset class="form-group">
                                                            
                                                            <label class="form-label">{$CMS->lang['ass_move_to_item']}</label>
                                                             
                                                            <div class="checkbox checkbox-only" data-sortable="false">
                                                                <input type="checkbox" name="ass_is_move_to_item" id="ass_is_move_to_item" value="1">
                                                                <label for="ass_is_move_to_item"></label>
                                                            </div>
                                                        
                                                    </fieldset>
						</li>
EOF;
                                            }
                                            $output .=<<<EOF
                        
						<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">	
                                                    <fieldset class="form-group">
                                                        <label class="form-label" >{$CMS->lang['ass_name_to']} <span style="color:red">(*)</span></label>
                                                        <input class="form-control" type="text" name="ass_name_to" id="ass_name_to" autocomplete="off" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_name_to_error']}" value="{$ass_name_to}">
							<input type="hidden" name="product_id_to" id="product_id_to" value="{$product_id_to}" />  
                                                        <div id="ass_list_to" style="display:none" ></div>		
                                                    </fieldset>
						</li>
						
						<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                                                    <fieldset class="form-group">
                                                        <div class="typeahead-field"> 
                                                            <span class="typeahead-query change_action_move_subitem"><input class="btn btn_move_subitem" type="button" value="Di chuyển"></span>
                                                        </div>
                                                    </fieldset>
                                                </li> 

				</ul>
				  
			</form>
		</div>	
EOF;
		return $output;
	}

    function preview_barcode()
    {
        global $CMS;

            $output = <<<EOF
        <table style="width: 100%">
EOF;

            if(is_array($CMS->input['name']))
            {
                foreach ($CMS->input['name'] as $index => $name)
                {
                    if($CMS->input['id'.$index])
                    {
                        $barcode = trim($CMS->input['barcode'][$index]);

                        if(!$barcode)
                        {
                            echo $CMS->lang['barcode_incomplete']; exit;
                        }

                        $barcode = lib\Barcode::generatorJPG($barcode);

                        if(lib\Barcode::$error)
                        {
                            echo $barcode; exit;
                        }

                        $barcode = base64_encode($barcode);
                        $barcode = "<img src=\"data:image/jpeg;base64,'{$barcode}'\">";
                        $price = strip_tags(html_entity_decode($CMS->input['price'][$index]));

                        $output .= "<tr>";
                        for($i=1; $i<=3; $i++)
                        {
                            $output .= <<<EOF
                <td style="padding: 15px 0px; text-align: center">
                    <p>{$name}</p>
                    <p>{$barcode}</p>
                    <p>{$price}</p>
                </td>
EOF;
                        $output .= "</tr>";
                        }
                    }
                }
            }
            else
            {
                $barcode = trim($CMS->input['barcode']);

                if(!$barcode)
                {
                    echo $CMS->lang['barcode_incomplete']; exit;
                }

                $barcode = lib\Barcode::generatorJPG($barcode);

                if(lib\Barcode::$error)
                {
                    echo $barcode; exit;
                }

                $barcode = base64_encode($barcode);
                $barcode = "<img src=\"data:image/jpeg;base64,'{$barcode}'\">";
                $CMS->input['name'] = urldecode($CMS->input['name']);
                $price = strip_tags($CMS->class->input->currency($CMS->input['price']));
                $output .= "<tr>";
                for($i=1; $i<=3; $i++)
                {
                    $output .= <<<EOF
                <td style="padding: 15px 0px; text-align: center">
                    <p>{$CMS->input['name']}</p>
                    <p>{$barcode}</p>
                    <p>{$price}</p>
                </td>
EOF;
                }
                $output .= "</tr>";
            }

            $output .= <<<EOF
        </table>
EOF;

        return $output;
    }
}