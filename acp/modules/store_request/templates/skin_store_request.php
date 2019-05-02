<?php

use \lib\input;

class skin_store_request {
	public function head() {
		global $CMS, $DB, $member;
		$output='';
		$CMS->input['stage'] = isset($CMS->input['stage']) ? $CMS->input['stage'] : "request";
		$stage = $CMS->input['stage'];
		if($stage == "request")
		{
			$default_class = "active";
			$default_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request'";

			$rei_class = "";
			$rei_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei'";


			$reis_class = "";
			$reis_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_eis'";
			$number = 1;
			$style_btn = " style='display: inline-block'; ";

			if($CMS->permit['store_request_add_'.$stage])
			{
				$btn_add =<<<EOF
						<a href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}" title="" class="add_bill">{$CMS->lang['request_add_'.$stage]}</a>
EOF;
			}else
			{
				$btn_add = "";
			}


		}elseif($stage == "request_ei")
		{
			$default_class = "";
			$default_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request'";
			$default_role= "";
			$default_data_toggle= "";

			$rei_class = "active";
			$rei_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei'";


			$reis_class = "";
			$reis_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_eis'";
			$number = 2;
			$style_btn = " style='display: none'; ";


			if($CMS->permit['store_request_add_'.$stage])
			{
				$btn_add =<<<EOF
						<div class="btn-group">
							<button type="button"
									class="btn  dropdown-toggle"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								Tạo phiếu
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=import&subtype=0"><span class="font-icon"></span>{$CMS->lang['request_bill_import_0']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=import&subtype=1"><span class="font-icon"></span>{$CMS->lang['request_bill_import_1']}</a>

								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=3"><span class="font-icon"></span>{$CMS->lang['request_bill_export_3']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=1&sub_id=2"><span class="font-icon"></span>{$CMS->lang['request_bill_export_1_2']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=1&sub_id=1"><span class="font-icon"></span>{$CMS->lang['request_bill_export_1_1']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=2"><span class="font-icon"></span>{$CMS->lang['request_bill_export_2']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=4"><span class="font-icon"></span>{$CMS->lang['request_bill_export_4']}</a>
								
							</div>
						</div>
EOF;
			}else
			{
				$btn_add = "";
			}
		}elseif($stage == "request_eis")
		{
			$default_class = "";
			$default_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request'";

			$rei_class = "";
			$rei_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei'";


			$reis_class = "active";
			$reis_href = "href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_eis'";
			$number = 3;
			$style_btn = " style='display: none'; ";

			if($CMS->permit['store_request_add_'.$stage])
			{
				$btn_add =<<<EOF
						<div class="btn-group">
							<button type="button"
									class="btn  dropdown-toggle"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								Tạo phiếu
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=import"><span class="font-icon"></span>{$CMS->lang['request_bill_import_0']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=import&subtype=1"><span class="font-icon"></span>{$CMS->lang['request_bill_import_1']}</a>

								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=3"><span class="font-icon"></span>{$CMS->lang['request_bill_export_3']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=1&sub_id=2"><span class="font-icon"></span>{$CMS->lang['request_bill_export_1_2']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=1&sub_id=1"><span class="font-icon"></span>{$CMS->lang['request_bill_export_1_1']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=2"><span class="font-icon"></span>{$CMS->lang['request_bill_export_2']}</a>
								<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill=export&subtype=4"><span class="font-icon"></span>{$CMS->lang['request_bill_export_4']}</a>
								
							</div>
						</div>
EOF;
			}else
			{
				$btn_add = "";
			}
		}

		$info_statistic = $CMS->store_request->getNumberbill();
		$total_request_1 = isset($info_statistic[10]) ? intval($info_statistic[10]) : 0;
		$total_request_2 = isset($info_statistic[20]) ? intval($info_statistic[20]) : 0;
		$total_request_3 = isset($info_statistic[30]) ? intval($info_statistic[30]) : 0;


		$keysearch = urldecode(input::get('quick_search'));

		$output.=<<<EOF
<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['request_list_'.$number]}</h3>
		<figure class="pull-right right">
		 	<div class="search">
				<form method="post" action="{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}&act=search" style="display:inline-block">
					<input type="submit" class="fa-input" value="&#xf002;">
					<input type="text" name="quick_search" id="quick_search" value="{$keysearch}" placeholder="{$CMS->lang['gsearch_quick']}">
				</form>
				<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>

			</div>
			{$btn_add}
		</figure>
	</figure>
EOF;
		
		$request_code = urldecode(input::get("request_code"));
		$product_name = urldecode(input::get("product_name"));
		$inventory_id = isset($CMS->input['inventory_id']) ? intval($CMS->input['inventory_id']) : "";
		$shi_name = urldecode(input::get("shi_name"));
		$shi_id = $shi_name ? intval(input::get("shi_id")) : "";
		$list_store = $CMS->store->get_list_store(intval(input::get("store_id")), 1);
		if($CMS->input['act'] == "search")
		{
			$display = "display: block;";
		}else
		{
			$display = "display:none";
		}
$output .=<<<EOF

<section class="search_adv" >
	<form method="post" id="formsearch_adv" style="{$display}"  action="{$CMS->vars['root_domain']}/?site=store_request&act=search&stage={$stage}" >
		<figure class="box-typical box-typical box-typical-padding border">
				<h5>{$CMS->lang['gsearch_advance']}</h5>
				<ul class="input_li row match-height">
					<li class="col-xl-2 col-lg-4 col-md-3 col-sm-6 col-xs-12">
						<p class="form-control-static">
							<input name="request_code" id="request_code" type="text" class="form-control" placeholder="Mã phiếu" value="{$request_code}" >
						</p>
					</li>

					<li class="col-xl-2 col-lg-4 col-md-3 col-sm-6 col-xs-12">
						<p class="form-control-static">
							<input  name="product_name" id="product_name" type="text" class="form-control" placeholder="Tên sản phẩm" value="{$product_name}" >
						</p>
					</li>
					
					<li class="col-xl-2 col-lg-4 col-md-3 col-sm-6 col-xs-12">
						<p class="form-control-static">
							<input type="text" class="form-control" placeholder="ID kiểm salon" name="inventory_id" id="inventory_id" value="{$inventory_id}"/>
						</p>
					</li>
					
					<li class="col-xl-2 col-lg-4 col-md-3 col-sm-6 col-xs-12">
						<p class="form-control-static">
							<input type="text" autocomplete="off" onkeyup="autocompleteSearch('#shi_name', '{$CMS->vars['root_domain']}/?site=shipment&act=search&subact=searchkey', '#shi_id');" class="form-control" name="shi_name" id="shi_name"  placeholder="Tên lô hàng" value="{$shi_name}" />
							<input type="hidden" name="shi_id" id="shi_id" value="{$shi_id}" />
						</p>
					</li>

					<li class="col-xl-2 col-lg-4 col-md-3 col-sm-6 col-xs-12">
						<p class="form-control-static">
							<select class="form-control select2" name="store_id" id="store_id" >
							{$list_store}
							</select>
						</p>
					</li>
					
					<li class="col-xl-2 col-lg-4 col-md-3 col-sm-6 col-xs-12">
						<p class="form-control-static">
							<input type="submit" value="{$CMS->lang['comment_filter']}">
							 
						</p>
					</li>

				</ul>	
		</figure>	
	</form>	
  
</section>	
		<section class="tabs-section">
			<div class="tabs-section-nav tabs-section-nav-inline">
				<ul class="nav" role="tablist">
					<li class="nav-item">
						<a class="nav-link {$default_class}" {$default_href}>
							 {$CMS->lang['store_request_head']} ({$total_request_1})
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {$rei_class}" {$rei_href}>
							{$CMS->lang['store_request_ei']} ({$total_request_2})
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {$reis_class}" {$reis_href}>
							 {$CMS->lang['store_request_eis']}
						</a>
					</li>
				 
				</ul>
			</div><!--.tabs-section-nav-->
		</section>
	<section class="add_table">
		<div class="data-table">
EOF;

		if($_SESSION['is_mobile'] == true)
		{
$output .=<<<EOF

            <table id="table_show" class="display table table_cus">
				<thead>
					<tr>
						<!--th width="3%" data-sortable="false" > {$CMS->lang['request_id']}</th-->
						<th width="3%"></th>
						<th width="15%" data-sortable="true" > {$CMS->lang['request_code']}</th>
						<th width="15%" data-sortable="true" >{$CMS->lang['request_amount']}</th>
						<th width="15%" data-orderable="true" >{$CMS->lang['store_id']}</th>
						<th width="15%" data-orderable="true" >{$CMS->lang['shi_id']}</th>
                        <th width="10%" data-orderable="true" >{$CMS->lang['user_id']}</th>
						<th width="15%" data-orderable="true" >{$CMS->lang['request_time']}</th>
						<th width="10%" data-sortable="true" >{$CMS->lang['title_request_status']}</th>
EOF;
					if($CMS->input['stage'] != 'request_eis')
					{
$output .=<<<EOF

						<th width="5%" data-orderable="false" style="text-align:center"></th>
EOF;
					}
$output .=<<<EOF


					</tr>
				</thead>
				<tbody>

EOF;

		}else
		{
$output .=<<<EOF
		<div class="table-responsive">
            <table id="" class="table_cus">
				<thead>
					<tr>
						<!--th width="5%">{$CMS->lang['request_id']}</th-->
						<th width="15%" data-sortable="true">{$CMS->lang['request_code']}</th>
						<th width="15%" data-sortable="true">{$CMS->lang['table_product_service']}</th>
						<th width="15%" data-sortable="true">{$CMS->lang['store_id']}</th>
						<th width="15%" data-sortable="true">{$CMS->lang['shi_id']}</th>
						<th width="15%" data-sortable="true">{$CMS->lang['request_amount']}</th>
                        <!--th width="10%" data-sortable="true">{$CMS->lang['user_id']}</th-->
						<th width="15%" data-sortable="true">{$CMS->lang['request_time']}</th>
						<th width="10%" data-sortable="true">{$CMS->lang['title_request_status']}</th>
EOF;
					if($CMS->input['stage'] != 'request_eis')
					{
$output .=<<<EOF

						<th width="5%" data-sortable="false" style="text-align:center"></th>
EOF;
					}
$output .=<<<EOF

					</tr>
				</thead>
				<tbody>

EOF;
		}// End if is mobile
		return $output;
	}
	
	public function foot() {
		global $CMS, $DB, $member;
		$output=<<<EOF
				</tbody>
			</table>
		</div>
	</div>			
</section>
{$CMS->store_request->action_control}
<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
<input type="hidden" name="data_cnt" value="{$CMS->store_request->record_cnt}">
<script language="javascript">arrange_setup("{$CMS->store_request->arrange_data}");</script>
EOF;

		if(input::get('quick_search'))
		{
			$lang_empty = "Không có kết quả theo từ khóa: <b>\"{$CMS->input['quick_search']}\"</b>";
		}else
		{
			$lang_empty = "Không có dữ liệu"; 
		}

		if($_SESSION['is_mobile'] == true)
		{
				$output .=<<<EOF
				 <script>
						$(function() {
							$('#table_show').DataTable({
								language: {
                                      emptyTable: '{$lang_empty}'
                                },
								responsive: true,
								columnDefs: [
							        { responsivePriority: 1, targets: 0 },
							        { responsivePriority: 2, targets: 1 },
							        { responsivePriority: 3, targets: -1 },
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
			$output .=<<<EOF
				 <script>
						// $(function() {
						// 	$('#table_show').DataTable({
						// 		language: {
      //                                 emptyTable: '{$lang_empty}'
      //                           },
						//  	    order: [],
 
					 // 		  	paging: false,
						// 		searching: false,
						// 		info: false
						// 	});
						// });
					</script>
					<style>
						.table-responsive {
						  overflow-x: visible !important;
						  overflow-y: visible !important;
						}
					</style>
EOF;
		}
		unset($_SESSION['highlight']);
		return $output;
	}
	
	public function mid($data=NULL) 
	{ 
		global $CMS, $DB, $member;

		$stage = $CMS->input['stage'];
		$btn_edit_row = "";

		if($stage == "request")
		{
			// PHIẾU YÊU CẦU
			if($data['request_status'] == 10 and $CMS->permit['store_request_edit_request'] == 1)
			{
				
				$btn_edit_row =<<<EOF
					<a data-toggle='tooltip' data-placement='bottom' title='Sửa' href="{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$stage}&id={$data['request_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;
				
			}

			if($CMS->permit['store_request_delete_request'] == 1)
			{
				$btn_del_row =<<<EOF
					<a data-toggle='tooltip' data-placement='bottom' title='Xoá' onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=store_request&act=del&stage={$stage}&id={$data['request_id']}');"  class="edit"><i class="fa fa-trash"></i></a>
EOF;
				
			}



		}elseif($stage == "request_ei")
		{
			// PHIẾU XUẤT NHẬP

			if($data['request_status'] == 20 and $CMS->permit['store_request_edit_request_ei'] == 1)
			{
				if($data['request_type'] == 1)
				{
					if($data['request_subtype'] == 1 and $data['cus_id'])
					{
						$add_link = "&subtype=1&sub_id=2";
					}elseif($data['request_subtype'] == 1 and !$data['cus_id'])
					{
						$add_link = "&subtype=1&sub_id=1";
					}else
					{
						$add_link = "&subtype={$data['request_subtype']}";
					}
					$btn_edit_row =<<<EOF
						<a data-toggle='tooltip' data-placement='bottom' title='Sửa' href="{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$stage}&type_bill=export&id={$data['request_id']}{$add_link}" class="edit"><i class="fa fa-edit"></i></a>
EOF;
				}else
				{
					$btn_edit_row =<<<EOF
						<a data-toggle='tooltip' data-placement='bottom' title='Sửa' href="{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$stage}&id={$data['request_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;
					
				}
				
			}

			if($CMS->permit['store_request_delete_request_ei'] == 1)
			{
				$btn_del_row =<<<EOF
					<a data-toggle='tooltip' data-placement='bottom' title='Xoá' onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=store_request&act=del&stage={$stage}&id={$data['request_id']}');"  class="edit"><i class="fa fa-trash"></i></a>
EOF;
				
			}

		}elseif($stage == "request_eis")
		{
			// PHIẾU XUẤT NHẬP KHO
			$btn_edit_row ="";
			$btn_del_row ="";
					
		}



		if($_SESSION['is_mobile'] == true)
		{


		$output=<<<EOF
			<tr>
				<!--td>#{$data['request_id']}</td-->
				<td></td>
                <td>{$data['request_code_show']}</td>
                <td>{$data['request_amount_show']}</td>
                <td>{$data['store_id']} {$data['icon_ei']} {$data['store_id_to_show']}</td>
                <td>{$data['shi_id']}</td>
                
				<td><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}" target="_blank">{$data['user_name']}</a></td>
				<td>{$data['request_time']}</td>
				<td>{$data['request_status_show']}</td>
EOF;
					if($stage != 'request_eis')
					{
$output .=<<<EOF
					<td align="center">
						<div class="pull-right">
		                    {$data['html_action']}
		                    {$btn_edit_row}
							{$btn_del_row}
						</div>
					</td>
EOF;
					}
$output .=<<<EOF

				</tr>
EOF;

		}else
		{
			if($_SESSION['highlight'] == $data['request_id'])
			{
				$class_hl = "highlight_row";
			}else
			{
				$class_hl = "";
			}
	$output=<<<EOF
			<tr class="{$class_hl}">
                <td>{$data['request_code_show']}</td>
                <td>{$data['request_product_show']}</td>
                <td>{$data['store_id']} {$data['icon_ei']}</td>
                <td>{$data['shi_id']}</td>
                <td>{$data['request_amount_show']}</td>
				<!--td><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}" target="_blank">{$data['user_name']}</a></td-->
				<td>{$data['request_time']}</td>
				<td>{$data['request_status_show']}</td>
EOF;
					if($stage != 'request_eis')
					{
$output .=<<<EOF
				<td align="center">
					<div class="pull-right">
						{$data['html_action']}
						{$btn_edit_row}
						{$btn_del_row}
					</div>
				</td>
EOF;
					}
$output .=<<<EOF

				</tr>
EOF;

		}// End if mobile
		return $output;
	}
	
	public function none() {
		global $CMS, $DB, $member;

		if(!$_SESSION['is_mobile'])
		{
$output=<<<EOF
		<tr>
			<td colspan="8">Không có dữ liệu</td>
		</tr>		
EOF;
		}else
		{
			$output= "";
		}
		return $output;
	}

	public function show($data=NULL) 
	{
		global $CMS, $DB, $member;
		// print "<pre>";
		// print_r($data);exit;
		if($_SESSION['is_mobile'] and !$_SESSION['is_tablet']) 
		{
			$label_125 = 'title-label-120';
			$label_100 = 'title-label-120';
		}else
		{
			$label_125 = 'title-label-125';
			$label_100 = 'title-label-100';
		}

		if($_SESSION['is_mobile']) 
		{
			$position = "pull-left";
		}else
		{
			$position = "pull-right";
		}


		$btn_approve = "";
		$btn_approve_mobile = "";
		$btn_edit = "";
		$btn_edit_mobile = "";
		$btn_delete = "";
		$btn_delete_mobile = "";
		$stage = $CMS->input['stage']; 
		$type_bill = $data['request_type'] == 0 ? "import" : "export";

		if($stage == "request")
		{
			// Duyet
			if($CMS->permit['store_request_approve_request'] == 1 and $data['request_status'] == 10)
			{
				$btn_approve =<<<EOF
					<a href="{$CMS->vars['root_domain']}/?site=store_request&act=approve&stage={$stage}&id={$data['request_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['request_btn_approved_'.$stage]}</a>
EOF;
				$btn_approve_mobile =<<<EOF

				<li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=store_request&act=approve&stage={$stage}&id={$data['request_id']}" ><i class="fa fa-check-circle-o"></i>{$CMS->lang['request_btn_approved_'.$stage]}</a></li>
EOF;

			}

			// Sua xoa
			if($CMS->permit['store_request_edit_request'] and $data['request_status'] == 10)
			{
				$btn_edit = <<<EOF
					<a href="{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$stage}&id={$data['request_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['edit']}</a>
EOF;
				$btn_edit_mobile =<<<EOF

				<li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$stage}&id={$data['request_id']}"><i class="fa fa-pencil-square-o"></i>{$CMS->lang['edit']}</a></li>
EOF;
			}

			if($CMS->permit['store_request_delete_request'] and $data['request_status'] == 10)
			{
				$btn_delete = <<<EOF
					<a href="{$CMS->vars['root_domain']}/?site=store_request&act=delete&stage={$stage}&id={$data['request_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['delete']}</a>
EOF;
				$btn_delete_mobile =<<<EOF

				<li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=store_request&act=delete&stage={$stage}&id={$data['request_id']}"><i class="fa fa-trash"></i>{$CMS->lang['delete']}</a></li>
EOF;

			}



		}elseif($stage == "request_ei")
		{
			if($data['request_subtype'] == 1)
			{
				if($data['cus_id'])
				{
					$link = "&subtype=1&sub_id=2";
				}else
				{
					$link = "&subtype=1&sub_id=1";
				}
			}elseif($data['request_subtype'] == 2)
			{
				$link = "&subtype=2";
			}elseif($data['request_subtype'] == 3 or $data['request_subtype'] == 4)
			{
				$link = "&subtype={$data['request_subtype']}";
			}
			// Duyet
			if($CMS->permit['store_request_approve_request_ei'] == 1 and $data['request_status'] == 20)
			{
				if($data['request_type'] == 0)
				{
					$key_lang = 'request_approve_'.$type_bill."_".$stage."_".$data['request_subtype'];
				}else
				{
					if($data['request_subtype'] == 1)
					{
						if($data['cus_id'])
						{
							$key_lang = 'request_approve_'.$type_bill."_1_2";
						}else
						{
							$key_lang = 'request_approve_'.$type_bill."_1_1";
						}
					}else
					{
						$key_lang = 'request_approve_'.$type_bill."_".$data['request_subtype'];
					}
					
				}
				$btn_approve =<<<EOF
					<a href="{$CMS->vars['root_domain']}/?site=store_request&act=approve&stage={$stage}&type_bill={$type_bill}&id={$data['request_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang[$key_lang]}</a>
EOF;
				$btn_approve_mobile =<<<EOF

				<li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=store_request&act=approve&stage={$stage}&type_bill={$type_bill}&id={$data['request_id']}" ><i class="fa fa-check-circle-o"></i>{$CMS->lang[$key_lang]}</a></li>
EOF;

			}

			// Sua xoa

			if($CMS->permit['store_request_edit_request_ei'] and $data['request_status'] == 20)
			{
				$btn_edit = <<<EOF
					<a href="{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$stage}&type_bill={$type_bill}{$link}&id={$data['request_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['edit']}</a>
EOF;
				$btn_edit_mobile =<<<EOF

				<li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$stage}&type_bill={$type_bill}{$link}&id={$data['request_id']}"><i class="fa fa-pencil-square-o"></i>{$CMS->lang['edit']}</a></li>
EOF;
			}

			if($CMS->permit['store_request_delete_request_ei'] and $data['request_status'] == 20)
			{
				$btn_delete = <<<EOF
					<a href="{$CMS->vars['root_domain']}/?site=store_request&act=delete&stage={$stage}&id={$data['request_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['delete']}</a>
EOF;
				$btn_delete_mobile =<<<EOF

				<li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=store_request&act=delete&stage={$stage}&id={$data['request_id']}"><i class="fa fa-trash"></i>{$CMS->lang['delete']}</a></li>
EOF;

			}
		}

		
		$sub_id = 0;
		if($data['request_subtype'] == 1)
		{
			if($data['cus_id'])
			{
				$sub_id = 2;
				$id_change = $data['cus_id'];
			}elseif($data['supplier_id'])
			{
				$sub_id = 1;
				$id_change = $data['supplier_id'];
			}else
			{
				if($data['request_type'] == 0)
				{
					$sub_id = 3; // Nhập
				}
							}
		}elseif($data['request_subtype'] == 2)
		{
			$id_change = $data['store_id_to'];
			$store_name_from = $data['store_name'];
		}elseif($data['request_subtype'] == 3 or $data['request_subtype'] == 4)
		{
			$id_change = $data['request_reason'];
		}

		if($data['ret_id'])
		{
			$title_bill = "Trả hàng";
		}else
		{
			list($html_show_type, $title_bill) = $CMS->store_request->getHtmlShowTypeBill($data['request_subtype'], $sub_id, $id_change, $store_name_from, $label_100);
		}

		if($title_bill)
		{
			$sub_title_bill = ": {$title_bill}";
		}else
		{
			$sub_title_bill = "";
		}

		$output=<<<EOF

<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang["request_info_{$stage}_".$data['request_type']]}{$sub_title_bill}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
				<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_100} pull-left">{$CMS->lang['request_id']}</div>
			            <div class="form-control-span2">#{$data['request_id']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_100} pull-left">{$CMS->lang['request_code']}</div>
			            <div class="form-control-span2">{$data['request_code']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_100} pull-left">{$CMS->lang['request_time']}</div>
			            <div class="form-control-span2">{$data['request_time']}</div>
			        </div>
				</fieldset>
				
EOF;
		if($data['request_subtype'] != 2)
		{
$output .=<<<EOF
			{$html_show_type}
EOF;
		}

$output .=<<<EOF
				<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_100} pull-left">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2">{$data['user_name_show']}</div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
				
EOF;
		if($data['request_subtype'] != 2)
		{
$output .=<<<EOF
				<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_100} pull-left">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2">{$data['store_id_show']}</div>
			        </div>
				</fieldset>
EOF;
		}else
		{
			$output .=<<<EOF
				{$html_show_type}
EOF;

		}

		if($data['trx_id'])
		{
			$returns = $CMS->transactions->getInfo($data['trx_id']);
$output .=<<<EOF
				<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_100} pull-left">{$CMS->lang['trx_id']}</div>
			            <div class="form-control-span2"><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$data['trx_id']}">{$returns['trx_code']}</a></div>
			        </div>
				</fieldset>

EOF;

		}

		if($data['ret_id'] and $CMS->permit['returns_read'])
		{
			$returns = $CMS->returns->get_info($data['ret_id']);
$output .=<<<EOF
			<fieldset class="row">
				<div class="form-control-label2" >
		            <div class="title_label {$label_100} pull-left">{$CMS->lang['ret_id']}</div>
		            <div class="form-control-span2"><a href="{$CMS->vars['root_domain']}/?site=returns&act=show&id={$data['ret_id']}">{$returns['ret_code']}</a></div>
		        </div>
			</fieldset>

EOF;

		}else
		{
$output .=<<<EOF
				
			<fieldset class="row">
				<div class="form-control-label2" >
		            <div class="title_label {$label_100} pull-left">{$CMS->lang['shi_id']}</div>
		            <div class="form-control-span2">{$data['shi_id_show']}</div>
		        </div>
			</fieldset>
EOF;
		}				
$output .=<<<EOF
				
				<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_100} pull-left">{$CMS->lang['request_note']}</div>
			            <div class="form-control-span2">{$data['request_note']}</div>
			        </div>
				</fieldset>

			</div>

			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="{$position}">
					<fieldset class="form-group">
			          <h4>{$CMS->lang['table_subtotal']}</h4>
			          <p class="total_price" style="margin: 0">{$data['request_amount_show']}</p>
			        </fieldset>
			        <fieldset class="form-group">
			          {$data['request_status_show']}
			        </fieldset>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>


EOF;
		if($data['request_type'] == 0)
		{
			$title_1 = $CMS->lang['table_product_service'];
			$title_2 = $CMS->lang['table_description'];
		}else
		{
			$title_1 = $CMS->lang['title_name_assets'];
			$title_2 = $CMS->lang['title_code_assets'];
		}

$output .=<<<EOF
<section class="add_table">				 			 

	 <h4 class="heading"><i class="fa fa-caret-down"></i><span>{$CMS->lang['request_property']}</span></h4>
						 
			 <div class="table_cus" style="overflow-x: initial;">
					<table id="table_show" width="100%">
						<thead>
							<tr>
									<th width="1%"></th>
									<th data-sortable="false" width="5%">#ID</th>
									<th data-sortable="false" width="20%">{$title_1}</th>
									<th data-sortable="false" width="25%">{$title_2}</th>
									<th data-sortable="false" width="10%">{$CMS->lang['table_quantity']}</th>
									<th data-sortable="false" width="15%">{$CMS->lang['table_price']}</th>
									<th data-sortable="false" width="5%">{$CMS->lang['table_tax']}</th>
									<th data-sortable="false" width="15%">{$CMS->lang['table_amount']}</th>
					 
							</tr>
						</thead>	
						<tbody>
EOF;

				$data_product = json_decode($data['request_product'], true);
// print "<pre>";
// print_r($data_product);exit;
				if(is_array($data_product))
				{
					$count = count($data_product);
					$i=1;
					$total = 0;
					foreach ($data_product as $key => $value) 
					{
						$subtotal = $value['product_price'] * $value['product_quantity'];
						$subtotal = $subtotal > 0 ? $subtotal : $value['ass_price'] *  $value['ass_quantity'];
						$fee_tax = $value['product_tax'] ? $value['product_tax'] : "";
						$fee_tax = $fee_tax ? $fee_tax : $value['ass_tax'];

						$total += $subtotal + round(($subtotal * $fee_tax)/100);

						$amount = $subtotal + round(($subtotal * $fee_tax)/100);
						$product_amount = $CMS->class->input->currency($amount);

						$name_product = $value['product_name'] ? $value['product_name'] : $value['ass_name'];
						if($value['product_name'])
						{
							$name_product = "<a style='display: inline-block;' href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$value['product_id']}'>{$name_product}</a>";
						}elseif($value['product_id'])
						{
							$name_product = "<a style='display: inline-block;' href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$value['product_id']}'>{$name_product}</a>";
						}

						if($CMS->permit['assets_read'] and $value['product_id'])
						{
							$name_product .= " <a data-toggle='tooltip' data-placement='bottom' title='{$CMS->lang['tooltip_search_assets']}' style='display: inline-block;' href='{$CMS->vars['root_domain']}/?site=assets&product_id={$value['product_id']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
						}

						$description = $value['product_description'] ? $value['product_description'] : $value['ass_code'];
						$quantity = $value['product_quantity'] ? $value['product_quantity'] : $value['ass_quantity'];
						$price = $value['product_price'] ? $value['product_price'] : $value['ass_price'];
						$price = $CMS->class->input->currency($price);
						$fee_tax_show = $fee_tax ? $fee_tax."%" : "";
$output .=<<<EOF

							<tr>
								<td></td>
								<td>#{$i}</td>
								<td>{$name_product}</td>
								<td>{$description}</td>
								<td>{$quantity}</td>
								<td>{$price}</td>
								<td>{$fee_tax_show}</td>
								<td>{$product_amount}</td>
							</tr>
EOF;
						$i++;
					}
				}

				$total_show = $CMS->class->input->currency($total);
				if($_SESSION['is_mobile'] == true)
				{
$total_html =<<<EOF
					   <table width="100%">
							<tr>
								<td width="70%" colspan="6"></td>
								<td width="15%">{$CMS->lang['table_total']}</td>
								<td width="16%">{$total_show}</td>
							</tr>
						</table>

EOF;
				}else
				{

$total_html =<<<EOF
						<table width="100%">
							<tr>
								<td width="70%" colspan="7"></td>
								<td width="15%">{$CMS->lang['table_total']}</td>
								<td width="15%">{$total_show}</td>
							</tr>
						</table>
								 
EOF;
				}
$output .=<<<EOF

						
					    </tbody>
				</table>
					{$total_html}
			</div>
 </section>




<section class="add_cart_footer">
	<a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
	{$btn_delete}	
	{$btn_edit}		
	{$btn_approve}
	<div class="btn-group dropup pull-right hidden-xl-up">
	  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<i class="fa fa-save"></i>{$CMS->lang['gaction']}
	  </button>
	  <div class="dropdown-menu">
	  	<ul>
			{$btn_delete_mobile}	
			{$btn_edit_mobile}		
			{$btn_approve_mobile}			

		</ul>
	  </div>
	</div>
		
</section>
EOF;

		if($_SESSION['is_mobile'] == true)
		{
				$output .=<<<EOF
				 <script>
						$(function() {
							$('#table_show').DataTable({
							order: [],
							responsive: true,
						    columnDefs: [
						        { responsivePriority: 1, targets: 0 },
						        { responsivePriority: 2, targets: 1 },
						        { responsivePriority: 3, targets: 3 },
						        { responsivePriority: 4, targets: -1 },
						        { responsivePriority: 5, targets: -2 }
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
    
	public function edit($data=null) 
	{
		global $CMS, $DB, $member;

        $is_taxable_selected[$data['is_taxable']] = 'selected';

        $request_product = json_decode($data['request_product'], true);
        // print_r($data['request_product']);exit;
        $_SESSION['list_product'] = $request_product;
        // print '<pre>';
        // print_r($_SESSION['list_product']);exit;
        $html_product = $CMS->global->htmlTableProduct($request_product, 0,0);
		list($row_store, $option_store) = $CMS->store->get_list_store($data['store_id_bk']);

		if($CMS->permit['supplier_add'] == 1)
		{
		 	$btn_add_ncc =<<<EOF
				<a class="btn_gen add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
			
		}else
		{
			$btn_add_ncc = "";
		}

		if($CMS->permit['supplier_edit'] == 1)
		{
		 	$btn_edit_ncc =<<<EOF
				<a id='{$data['supplier_id']}' class='btn_gen btn_edit_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;
			
		}else
		{
			$btn_edit_ncc = "";
		}

		if($CMS->permit['shipment_add'] == 1)
		{
			$btn_add_shipment = <<<EOF
				<a class="btn_gen add_new_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

		}else
		{
			$btn_add_shipment = "";
		}

		if($CMS->permit['shipment_edit'] == 1)
		{
			$btn_edit_shipment = <<<EOF
				<a id='{$data['shi_id_bk']}' class='btn_gen btn_edit_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;

		}else
		{
			$btn_edit_shipment = "";
		}	

		$stage = $CMS->input['stage'] ? $CMS->input['stage'] : $data['stage'];
		$type_act = $CMS->input['type'] ? $CMS->input['type'] : $data['type'];
		// Check approve
		if($CMS->input['act'] == "approve")
		{
			$text_btn = $CMS->lang['request_btn_approved_'.$stage];
			if($stage == "request" and $CMS->permit['store_request_approve_request'])
			{
				$btn_submit =<<<EOF

				<button class="btn btn_submit_form btn-inline btn-primary ladda-button pull-right add_cart btn_check_form" data-style="expand-right" data-size="xs" title_alert="{$CMS->lang['confirm_approve_bill']}" val="" type="button" checkclick="1"><span class="ladda-label">{$CMS->lang['title_approved_'.$stage]}</span><span class="ladda-spinner"></span></button>
EOF;
	
			}

		}else
		{
			if($stage != "request")
			{
				$subtype = $data['request_subtype'];
				$text_btn = $CMS->lang['request_edit_'.$subtype.'_'.$stage];
			}else
			{
				$text_btn = $CMS->lang['request_edit_'.$stage];
			}
			$btn_submit =<<<EOF
				<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_check_form" data-style="expand-right" data-size="xs" type="button"><span class="ladda-label">{$text_btn}</span><span class="ladda-spinner"></span></button>
EOF;
		
		}

		$out=<<<EOF
<form id="form_edit_request" name="form_edit_request" class="form_request" action="{$CMS->vars['root_domain']}/?site=store_request&act=edit_do&stage={$stage}&type={$type_act}&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">

<section class="add_form main_form">
	<figure class="heading">
		<h3>{$text_btn}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
	<figure class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-md-6">
				<fieldset class="form-group">
					<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;

						if($row_store >= 3)
						{
							$out .=<<<EOF
							<div class="form-control-wrapper">
								<div class="box_validate">
								   	<select name="store_id" id="store_id" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_store']}" defaultvalue="{$data['store_id']}" class="form-control select2" autocomplete="off">						
			                       		{$option_store}
			                        </select>
			                    </div>
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
						//{$CMS->supplier->get_list_supplier()}
						$out .=<<<EOF
						<input type="hidden" name="type_bill" value="import"/>
			    		<input type="hidden" name="request_type" value="0" />
			    		<input type="hidden" name="request_subtype" value="{$subtype}" />
			    		
				</fieldset>                 	
EOF;

		$user_name = $CMS->user->get_info($data['user_id_assign'], 'user_display_name');
		if(is_numeric($data['request_time_delivery']))
		{
			$data['request_time_delivery'] = $data['request_time_delivery'] ? date("d/m/Y", $data['request_time_delivery']) : "";
		}

		if(is_numeric($data['request_time_create']))
		{
			$data['request_time_create'] = $data['request_time_create'] ? date("d/m/Y", $data['request_time_create']) : "";
		}

$out .=<<<EOF
	
				<div class="row">                                        
					<div class="col-md-6">

						<fieldset class="form-group">
							<label class="form-label pull-left" for="shi_id">{$CMS->lang['shi_id']}</label>
									<div class="box_action_shipment pull-right">
								   		{$btn_add_shipment}
								   		<span class="box_edit_shipment pull-right" style="margin-left: 10px;">{$btn_edit_shipment}</span>
								    </div>
							<div class="input-group"  style="clear: both;">
								<input name="shi_name" value="{$data['shi_name']}" class="form-control search_shipment" autocomplete="off">	
								<div class="input-group-addon">
				                  <span style="cursor:pointer" onclick="autocompleteAll('.search_shipment')" class="fa fa-arrow-down"></span>
				              </div>
							</div>
						    <input type="hidden" name="shi_id" class="data_input" value="{$data['shi_id_bk']}"/>

						    <script>
				            	$(document).ready(() =>  autocompleteSearch('.search_shipment', site_root_domain + '/?site=shipment&act=search&subact=searchkey&type=1', '.data_input','shipment',0));
				            </script>
						</fieldset>

						<fieldset class="form-group">
							<label class="form-label pull-left" for="request_time_create">{$CMS->lang['request_time_create']}</label>
							<div class="input-group" style="width: 100%">
		                        <input name="request_time_create" id="request_time_create" type="text" value="{$data['request_time_create']}" class="form-control datetimepicker-1">
		                        <span class="input-group-addon">
		                        	<span class="glyphicon glyphicon-calendar"></span>
		                        </span>
		                    </div>
						</fieldset>

					</div>
					<div class="col-md-6">
						<fieldset class="form-group">
							<label class="form-label pull-left" for="user_id_assign">{$CMS->lang['user_id_assign']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_assign', 'user_id_assign')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
							<div class="input-group"  style="clear: both;">
								<input name="user_name_assign" value="{$user_name}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_user_assign']}" class="form-control search_user" autocomplete="off">
								<div class="input-group-addon">
				                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user')" class="fa fa-arrow-down"></span>
				              </div>
							</div>
							<input name='user_id_assign' type='hidden' value="{$data['user_id_assign']}" class="user_assign" />
							<script>
				            	$(document).ready(() =>  autocompleteSearch('.search_user', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_assign','',0));
				            </script>
						</fieldset>

						<fieldset class="form-group">
							<label class="form-label pull-left" for="request_time_delivery">{$CMS->lang['request_time_delivery']}</label>
							<div class="input-group" style="width: 100%">
		                        <input name="request_time_delivery" id="request_time_delivery" type="text" value="{$data['request_time_delivery']}" class="form-control datetimepicker-1">
		                        <span class="input-group-addon">
		                        	<span class="glyphicon glyphicon-calendar"></span>
		                        </span>
		                    </div>
						</fieldset>
					</div>
	 			</div>

			</div>

			<div class="col-md-6">
				<div class="row">
					<div class="col-md-6">
						<fieldset class="form-group">
							<label class="form-label" for="store_id">{$CMS->lang['request_type_transaction']}</label>
							<select name="is_taxable" class="select2-arrow manual select2-no-search-arrow auto_select" style="width:100%;" defaultvalue="{$data['is_taxable']}">
								<option value="1" {$is_taxable_selected[1]}>{$CMS->lang['is_taxable_1']}</option>
								<option value="2" {$is_taxable_selected[2]}>{$CMS->lang['is_taxable_2']}</option>
							</select>	
						</fieldset>
					</div>
					<div class="col-md-12">
						<fieldset class="form-group">
							<label class="form-label" for="request_note">{$CMS->lang['request_note']}</label>
							<textarea name="request_note" id="request_note" rows="5" cols="50" class="form-control">{$data['request_note']}</textarea>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</figure>
</section>
	{$html_product}
	<section class="add_cart_footer">
		<a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
		<input type="hidden" value="{$type_act}" name="type_act" />
		<input type="hidden" value="{$stage}" name="stage" />
		{$btn_submit}
	</section>
	</form>

		{$CMS->global->fullFormHtml()}
		
	</div>
</section>
<script>
var module_name = 'module_storerequest';
rebuild_form('form-signin_v1');
$(document).ready(function(){
	show_total_goods();
	validate_form_custom("#form_edit_request",".btn_check_form", "store_request");
});
</script>


EOF;
		return $out;
	}
    
	public function add($data=array()) 
    {
		global $CMS, $DB, $member;
        // print "<pre>";
        // print_r($data);exit;

        $is_taxable_selected[$data['is_taxable']] = 'selected';

        $stage = $CMS->input['stage'];
		list($row_store, $option_store) = $CMS->store->get_list_store($data['store_id']);
		if($CMS->permit['supplier_add'] == 1)
		{
		 	$btn_add_ncc =<<<EOF
				<a class="btn_gen add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
			
		}else
		{
			$btn_add_ncc = "";
		}

		if($CMS->permit['shipment_add'] == 1)
		{
			$btn_add_shipment = <<<EOF
				<a class="btn_gen add_new_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

		}else
		{
			$btn_add_shipment = "";
		}

		

		$html_product = $CMS->global->htmlTableProduct($_SESSION['list_product'], 0, 0);
		// print "<pre>";
		// print_r($_SESSION['list_product']);exit;
		$subtype = intval($CMS->input['subtype']);
		if($stage != "request")
		{
			$key_title = "request_add_{$subtype}_".$stage;
		}else
		{
			$key_title = 'request_add_'.$stage;
		}
		$out=<<<EOF
<form id="form_add_request" name="form_add_request" class="form_request" action="{$CMS->vars['root_domain']}/?site=store_request&act=add_do&stage={$stage}" method="POST" enctype="multipart/form-data">
	
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang[$key_title]}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
<figure class="box-typical box-typical box-typical-padding border">
	<div class="row">
		<div class="col-md-6">
			<div class="row">                                        
				<div class="col-md-6">
					<fieldset class="form-group">
						<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
//data-validation-message="Test thử xem nó nằm ở đâu" data-validation="[NOTEMPTY]" data-prompt-position="bottomRight"
				if($row_store >= 3)
				{
					$out .=<<<EOF
							<div class="form-control-wrapper">
								<div class="box_validate">
								   	<select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_store']}" autocomplete="off">						
			                       		{$option_store}
			                        </select>
								</div>
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
				$data['request_time_create'] = $data['request_time_create'] ? $data['request_time_create']: date("d/m/Y");
				$data['request_time_delivery'] = $data['request_time_delivery'] ? $data['request_time_delivery'] : date("d/m/Y");
				$out .=<<<EOF
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label pull-left" for="shi_id">{$CMS->lang['shi_id']}</label>
								<div class="box_action_shipment pull-right">
							   		{$btn_add_shipment}
							   		<span class="box_edit_shipment pull-right" style="margin-left: 10px;"></span>
							    </div>
						<div class="input-group"  style="clear: both;">
							<input name="shi_name" value="{$data['shi_name']}" class="form-control search_shipment" autocomplete="off">	
							<div class="input-group-addon">
			                  <span style="cursor:pointer" onclick="autocompleteAll('.search_shipment')" class="fa fa-arrow-down"></span>
			              </div>
						</div>
					    <input type="hidden" name="shi_id" class="data_input" value="{$data['shi_id']}"/>
					    <input type="hidden" name="type_bill" value="import"/>
					    <input type="hidden" name="request_type" value="0" />
					    <input type="hidden" name="request_subtype" value="{$subtype}" />

					    <script>
			            	$(document).ready(() =>  autocompleteSearch('.search_shipment', site_root_domain + '/?site=shipment&act=search&subact=searchkey&type=1', '.data_input','shipment',0));
			            </script>
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label pull-left" for="request_time_create">{$CMS->lang['request_time_create']}</label>
						<div class="input-group" style="width: 100%">
	                        <input name="request_time_create" id="request_time_create" type="text" value="{$data['request_time_create']}" class="form-control datetimepicker-1">
	                        <span class="input-group-addon">
	                        	<span class="glyphicon glyphicon-calendar"></span>
	                        </span>
	                    </div>
					</fieldset>

				</div>
				<div class="col-md-6">
					<fieldset class="form-group">
						<label class="form-label" for="is_taxable">{$CMS->lang['request_type_transaction']}</label>
						<select name="is_taxable" class="form-control auto_select" style="width:100%;" defaultvalue="{$data['is_taxable']}">
							<option value="1" {$is_taxable_selected[1]}>{$CMS->lang['is_taxable_1']}</option>
							<option value="2" {$is_taxable_selected[2]}>{$CMS->lang['is_taxable_2']}</option>
						</select>	
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label pull-left" for="user_id_assign">{$CMS->lang['user_id_assign']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
						<div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_assign', 'user_id_assign')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
						<div class="input-group"  style="clear: both;">
							<input name="user_name_assign" value="{$data['user_name_assign']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_user_assign']}" class="form-control search_user" autocomplete="off">
							<div class="input-group-addon">
			                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user')" class="fa fa-arrow-down"></span>
			              </div>
						</div>
						<input name='user_id_assign' type='hidden' value="{$data['user_id_assign']}" class="user_assign" />
						<script>
			            	$(document).ready(() =>  autocompleteSearch('.search_user', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_assign','',0));
			            </script>
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label pull-left" for="request_time_delivery">{$CMS->lang['request_time_delivery']}</label>
						<div class="input-group" style="width: 100%">
	                        <input name="request_time_delivery" id="request_time_delivery" type="text" value="{$data['request_time_delivery']}" class="form-control datetimepicker-1">
	                        <span class="input-group-addon">
	                        	<span class="glyphicon glyphicon-calendar"></span>
	                        </span>
	                    </div>
					</fieldset>
				</div>

 			</div>
 		</div>
 		<div class="col-md-6">
 			<div class="row">
				<div class="col-md-12">
					<fieldset class="form-group">
						<label class="form-label" for="request_note">{$CMS->lang['request_note']}</label>
						<textarea name="request_note" id="request_note" rows="6" cols="50" class="form-control">{$data['request_note']}</textarea>  
					</fieldset>
				</div>
			</div>
		</div>
</section>
		
		{$html_product}
EOF;
			
		if($_SESSION['is_mobile'] == true)
		{
			if($stage == "request")
			{
				$btn_html_request = <<<EOF
				<div class="btn-group dropup pull-right hidden-xl-up">
				  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-save"></i>Lưu
				  </button>
				  <div class="dropdown-menu">
				  	<ul>
						<li class="hidden-xl-up">
							<a class="btn_add_request" page_type="0" ><i class="fa fa-save"></i>{$CMS->lang['request_add_'.$stage]}</a>
						</li>
						<li class="hidden-xl-up">
							<a class="btn_add_request" page_type="1" ><i class="fa fa-save"></i>{$CMS->lang['request_add_1_'.$stage]}</a>
						</li>
						<li class="hidden-xl-up">
							<a class="btn_add_request" page_type="2" ><i class="fa fa-save"></i>{$CMS->lang['request_add_2_'.$stage]}</a>
						</li>			

					</ul>
				  </div>
				</div>
				
EOF;
			}else
			{
				$btn_html_request =<<<EOF
					<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request" data-style="expand-right" data-size="xs" type="button" ><span class="ladda-label">{$CMS->lang[$key_title]}</span><span class="ladda-spinner"></span></button>
EOF;

			}

		}else
		{
			if($stage == "request")
			{
				$btn_html_request = <<<EOF

				<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request hidden-sm-down" data-style="expand-right" data-size="xs" type="button" page_type="2" ><span class="ladda-label">{$CMS->lang['request_add_2_'.$stage]}</span><span class="ladda-spinner"></span></button>

				<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request hidden-sm-down" data-style="expand-right" data-size="xs" type="button" page_type="1" ><span class="ladda-label">{$CMS->lang['request_add_1_'.$stage]}</span><span class="ladda-spinner"></span></button>

				<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request hidden-sm-down" data-style="expand-right" data-size="xs" type="button" page_type="0" ><span class="ladda-label">{$CMS->lang['request_add_'.$stage]}</span><span class="ladda-spinner"></span></button>
				
EOF;
			}else
			{
				$btn_html_request =<<<EOF
					<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request" data-style="expand-right" data-size="xs" type="button" ><span class="ladda-label">{$CMS->lang[$key_title]}</span><span class="ladda-spinner"></span></button>
EOF;

			}
		}
$out .=<<<EOF

		<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>

			{$btn_html_request}

			<input type="hidden" value="{$CMS->input['stage']}" name="stage" />
			<input type="hidden" value="{$data['page_type']}" name="page_type" />

		</section>
	</form>
	
	{$CMS->global->fullFormHtml()}
	</div>
	<script>
		 var module_name = 'module_storerequest';
	</script>
</section>

EOF;
		return $out;
	}


	public function approve($data=null) 
	{
		global $CMS, $DB, $member;
        // print "<pre>";
        // print_r($data);exit;
        $request_product = json_decode($data['request_product'], true);
        // print_r($data['request_product']);exit;
        $_SESSION['list_product'] = $request_product;
        // print '<pre>';
        // print_r($_SESSION['list_product']);exit;
        // $html_product = $CMS->global->htmlTableProduct($request_product);
        $html_product = $CMS->global->htmlTableProduct($_SESSION['list_product'], 0, 0);
        $data_product = $this->html_tr($request_product);
		list($row_store, $option_store) = $CMS->store->get_list_store($data['store_id_bk']);

		if($CMS->permit['supplier_add'] == 1)
		{
		 	$btn_add_ncc =<<<EOF
				<a class="btn_gen add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
			
		}else
		{
			$btn_add_ncc = "";
		}

		if($CMS->permit['supplier_edit'] == 1)
		{
		 	$btn_edit_ncc =<<<EOF
				<a id='{$data['supplier_id']}' class='btn_gen btn_edit_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;
			
		}else
		{
			$btn_edit_ncc = "";
		}

		if($CMS->permit['shipment_add'] == 1)
		{
			$btn_add_shipment = <<<EOF
				<a class="btn_gen add_new_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

		}else
		{
			$btn_add_shipment = "";
		}

		if($CMS->permit['shipment_edit'] == 1)
		{
			$btn_edit_shipment = <<<EOF
				<a id='{$data['shi_id_bk']}' class='btn_gen btn_edit_shipment pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;

		}else
		{
			$btn_edit_shipment = "";
		}	

		$stage = $CMS->input['stage'];
		// Check approve
		if($stage != "request")
		{
			$text_btn = $CMS->lang['request_btn_approved_'.$data['request_subtype']."_".$stage];
		}else
		{
			$text_btn = $CMS->lang['request_btn_approved_'.$stage];
		}
		if($stage == "request_ei" and $CMS->permit['store_request_approve_request_ei'])
		{
			$btn_submit =<<<EOF
			<button class="btn btn_submit_form btn-inline btn-primary ladda-button pull-right add_cart hidden-sm-down" data-style="expand-right" data-size="xs" type="button" title_alert="{$CMS->lang['confirm_approve_enough_goods']}" val="1" checkclick="1" ><span class="ladda-label">{$CMS->lang['title_enough_goods']}</span><span class="ladda-spinner"></span></button>

			<button disabled="disabled" for="not_enough" class="btn btn_submit_form btn-inline btn-primary ladda-button pull-right add_cart hidden-sm-down" data-style="expand-right" data-size="xs" type="button" title_alert="{$CMS->lang['confirm_approve_not_enough_goods']}" val="0" checkclick="0"><span class="ladda-label">{$CMS->lang['title_not_enough_goods']}</span><span class="ladda-spinner"></span></button>
			<input type="hidden" value="" name="enough_goods" /> 

			<div class="btn-group dropup pull-right hidden-xl-up">
			  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fa fa-save"></i>{$CMS->lang['gaction']}
			  </button>
			  <div class="dropdown-menu">
			  	<ul>
			  		<li class="hidden-xl-up"><a class="btn_submit_form" title_alert="{$CMS->lang['confirm_approve_enough_goods']}" val="1" checkclick="1"><i class="fa fa-check-circle-o"></i>{$CMS->lang['title_enough_goods']}</a></li>
			  		<li class="hidden-xl-up"><a for="not_enough" class="btn_submit_form" checkclick="0" title_alert="{$CMS->lang['confirm_approve_not_enough_goods']}" val="0"><i class="fa fa-check-circle-o"></i>{$CMS->lang['title_not_enough_goods']}</a></li>
				</ul>
			  </div>
			</div>
EOF;
		}elseif($stage == "request" and $CMS->permit['store_request_approve_request'])
		{
			$btn_submit =<<<EOF

			<button class="btn btn_submit_form btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs" title_alert="{$CMS->lang['confirm_approve_bill']}" val="" type="button" checkclick="1"><span class="ladda-label">{$CMS->lang['title_approved_'.$stage]}</span><span class="ladda-spinner"></span></button>
EOF;

		}

	

		$out=<<<EOF
<form id="form_edit_request" name="form_edit_request" class="form_request" action="{$CMS->vars['root_domain']}/?site=store_request&act=approve_do&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">
	{$data['processbar']}
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$text_btn}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
	<figure class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-md-6">
				<fieldset class="form-group">
					<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;

						if($row_store >= 3)
						{
							$out .=<<<EOF
							<div class="form-control-wrapper">
								<div class="box_validate">
								   	<select name="store_id" id="store_id" defaultvalue="{$data['store_id_bk']}" class="form-control select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_store']}" autocomplete="off">						
			                       		{$option_store}
			                        </select>
								</div>
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
						<input type="hidden" name="type_bill" value="import"/>
			    		<input type="hidden" name="request_type" value="0" />
			    		<input type="hidden" name="request_subtype" value="{$data['request_subtype']}" />
					
				</fieldset>
EOF;

		$user_name = $CMS->user->get_info($data['user_id_assign'], 'user_display_name');

		if(is_numeric($data['request_time_delivery']))
		{
			$data['request_time_delivery'] = $data['request_time_delivery'] ? date("d/m/Y", $data['request_time_delivery']) : "";
		}
		
		if(is_numeric($data['request_time_create']))
		{
			$data['request_time_create'] = $data['request_time_create'] ? date("d/m/Y", $data['request_time_create']) : "";
		}

$out .=<<<EOF
	
				<div class="row">                                        
					<div class="col-md-6">
						
						<fieldset class="form-group">
							<label class="form-label pull-left" for="shi_id">{$CMS->lang['shi_id']}</label>
									<div class="box_action_shipment pull-right">
								   		{$btn_add_shipment}
								   		<span class="box_edit_shipment pull-right" style="margin-left: 10px;">{$btn_edit_shipment}</span>
								    </div>
							<div class="input-group" style="clear: both;">
								<input name="shi_name" value="{$data['shi_name']}" class="form-control search_shipment" autocomplete="off">	
								<div class="input-group-addon">
				                  <span style="cursor:pointer" onclick="autocompleteAll('.search_shipment')" class="fa fa-arrow-down"></span>
				              </div>
							</div>
						    <input type="hidden" name="shi_id" class="data_input" value="{$data['shi_id_bk']}"/>
						    
						    <script>
				            	$(document).ready(() =>  autocompleteSearch('.search_shipment', site_root_domain + '/?site=shipment&act=search&subact=searchkey&type=1', '.data_input','shipment',0));
				            </script>
						</fieldset>



						<fieldset class="form-group">
							<label class="form-label pull-left" for="request_time_create">{$CMS->lang['request_time_create']}</label>
							<div class="input-group" style="width: 100%">
		                        <input name="request_time_create" id="request_time_create" type="text" value="{$data['request_time_create']}" class="form-control datetimepicker-1">
		                        <span class="input-group-addon">
		                        	<span class="glyphicon glyphicon-calendar"></span>
		                        </span>
		                    </div>
						</fieldset>
					</div>
					<div class="col-md-6">
						<fieldset class="form-group">
							<label class="form-label pull-left" for="user_id_assign">{$CMS->lang['user_id_assign']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_assign', 'user_id_assign')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
							<div class="input-group"  style="clear: both;">
								<input name="user_name_assign" value="{$user_name}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_user_assign']}" class="form-control search_user" autocomplete="off">
								<div class="input-group-addon">
				                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user')" class="fa fa-arrow-down"></span>
				              </div>
							</div>
							<input name='user_id_assign' type='hidden' value="{$data['user_id_assign']}" class="user_assign" />
							<script>
				            	$(document).ready(() =>  autocompleteSearch('.search_user', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_assign','',0));
				            </script>
						</fieldset>

						<fieldset class="form-group">
							<label class="form-label pull-left" for="request_time_delivery">{$CMS->lang['request_time_delivery']}</label>
							<div class="input-group" style="width: 100%">
		                        <input name="request_time_delivery" id="request_time_delivery" type="text" value="{$data['request_time_delivery']}" class="form-control datetimepicker-1">
		                        <span class="input-group-addon">
		                        	<span class="glyphicon glyphicon-calendar"></span>
		                        </span>
		                    </div>
						</fieldset>

					</div>
	 			</div>
		       
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-6">
						<fieldset class="form-group">
							<label class="form-label" for="store_id">{$CMS->lang['request_type_transaction']}</label>
							<select name="is_taxable" class="select2-arrow manual select2-no-search-arrow auto_select" style="width:100%;" defaultvalue="{$data['is_taxable']}">
								<option value="1" selected="selected">{$CMS->lang['is_taxable_1']}</option>
								<option value="2">{$CMS->lang['is_taxable_2']}</option>
							</select>	
						</fieldset>
					</div>
					<div class="col-md-12">
						<fieldset class="form-group">
							<label class="form-label" for="request_note">{$CMS->lang['request_note']}</label>
							<textarea name="request_note" id="request_note" rows="5" cols="50" class="form-control">{$data['request_note']}</textarea>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</figure>
</section>

EOF;
	
	if($stage == "request_ei")
	{
$out .=<<<EOF

	<section style="margin-bottom:0;">
		<p style="margin-bottom:0;">{$CMS->lang['title_approve_goods']}</p>
	</section>
	<section class="add_table">
		<div class="table_cus">
			<div class="table-responsive" style="overflow-x: initial;">
				<input type="hidden" id="keyrow_active" value=""/>
				<input type="hidden" id="is_zero" name="is_zero" value="1"/>
				<table>
					<thead>
						<tr>
							<th width="2%">#</th>
							<th width="15%">{$CMS->lang['table_product_service']}</th>
							<th width="25%">{$CMS->lang['table_description']}</th>
							<th width="7%">{$CMS->lang['table_quantity']}</th>
							<th width="10%">{$CMS->lang['table_price']}</th>
							<th width="10%">{$CMS->lang['table_tax']}</th>
							<th width="15%">{$CMS->lang['table_amount']}</th>
						</tr>
					</thead>
					<tbody id="data_table">
						{$data_product}
					</tbody>
						<tr>
							<td colspan="6" style="text-align: right; font-weight: bold;">{$CMS->lang['table_total']}</td>
							<td colspan="2" style="text-align: right; font-weight: bold;"><span id="total_show">0</span></td>
						</tr>
				</table>
			</div>
			
		</div>
	</section>
EOF;

	}else
	{
		$out .= $html_product;
	}
$out .=<<<EOF

	<section class="add_cart_footer">
		<a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
		<input type="hidden" value="{$CMS->input['type']}" name="type_act" />
		<input type="hidden" value="{$CMS->input['stage']}" name="stage" />
		{$btn_submit}
	</section>
	</form>

		{$CMS->global->fullFormHtml()}
		
	</div>
</section>
<script>
rebuild_form('form-signin_v1');
$(document).ready(function(){
	show_total_goods();
	validate_form_custom('#form_edit_request','.btn_submit_form', 'approve_request');
});
</script>

EOF;
		return $out;
	}

	
	public function html_tr($data_product = array())
	{
		global $CMS;

		// print "<pre>";
		// print_r($data_product);exit;
		$output = "";
		if(is_array($data_product))
		{
			$count = count($data_product);
			$i = 1;
			$inc = 0;
			ksort($data_product);
			foreach ($data_product as $key => $value) 
			{
				$tax_fee = $value['product_tax'] ? $value['product_tax'] : "";
				$quantity = $value['product_quantity'] ? $value['product_quantity'] : 1;
				$output .=<<<EOF
						<tr keyrow="{$i}" class="row-grid" rowtr="">
							<td class="grid-td" for="dgrid-1" check="chtd"><span class="number">#{$i}</span></td>
							<td class="grid-td" for="dgrid-2" check="chtd">
								<div class="td_dropdown_btn">
	                                <input type="text" for="dgrid-2" check="chtd" name="product_name_show[]" class="form-control hidden_border" value="{$value['product_name']}" autocomplete="off"/>
	                                <input type="hidden" for="dgrid-2" check="chtd" name="product_name[]" class="form-control hidden_border" value="{$value['product_name']}" autocomplete="off"/>
									<input type="hidden" class="product_id" name="product_id[]" value="{$value['product_id']}"/>
									<input type="hidden" class="item_id" name="item_id[]" value="{$inc}"/>
								
                                </div>
							</td>

							<td class="grid-td" for="dgrid-3" check="chtd"><textarea for="dgrid-3" name="product_description[]" check="chtd" class="form-control hidden_border" style="resize: none;" rows="3">{$value['product_description']}</textarea></td>

							<td class="grid-td" for="dgrid-4" check="chtd"><input type="text" for="dgrid-4" name="product_quantity[]" check="chtd" class="form-control hidden_border quan_list" onkeypress="return check_enter_number(event, this);" max_value="{$quantity}" value="{$quantity}" onkeyup="calculate_money('#data_table')"/></td>

							<td class="grid-td" for="dgrid-5" check="chtd"><input type="text" for="dgrid-5" name="product_price[]" value="{$value['product_price']}" check="chtd" onkeypress="return check_enter_number(event,this);" onkeyup="calculate_money()" class="form-control hidden_border" /></td>

							<td class="grid-td" for="dgrid-7" check="chtd">
								<select for="dgrid-7" name="product_tax[]" defaultvalue="{$tax_fee}" class="form-control hidden_border auto_select" onchange="calculate_money()" check="chtd">
										<option value="0">0%</option>
										<option value="10">10%</option>
								</select>	
							</td>

							<td class="grid-td" for="dgrid-6" check="chtd"><input type="text" for="dgrid-6" name="product_amount[]" value="{$value['product_amount']}" check="chtd" class="form-control hidden_border total_amount" disabled="disabled"/></td>

						</tr>
EOF;

				$i++;
				$inc ++;
			}

			
		}

		return $output;

	}


	public function addExport($data = array())
	{
		global $CMS, $DB, $member;
        
        $stage = $CMS->input['stage'];
        $subtype = $CMS->input['subtype'] ? intval($CMS->input['subtype']) : $data['request_subtype'];
        // print "<pre>";
        // print_r($data);exit;
        if(!in_array($subtype, array(1,2,3,4)))
        {
        	$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}");
        }
        $store_id = $CMS->input['store_id'] ? intval($CMS->input['store_id']) : $data['store_id']; // dùng khi ben tài sản chuyển qua
        
		list($row_store, $option_store, $store_id_first) = $CMS->store->get_list_store($store_id);

		$html_product = $CMS->global->htmlTableAsset($_SESSION['list_product']);
		// print "<pre>";
		// print_r($_SESSION['list_product']);exit;

		$request_subtype = $data['request_subtype'] ? $data['request_subtype'] : intval($CMS->input['subtype']);
		// print $request_subtype;exit;
		$sub_id = $CMS->input['sub_id'] ? intval($CMS->input['sub_id']) : $data['sub_id'];
		
		if($request_subtype == 2)
		{
			$id_change = $data['store_id_to'] ? $data['store_id_to'] : $store_id_first;
		}elseif($request_subtype == 1)
		{
			if($sub_id == 2)
			{
				$id_change = $data['cus_id'];
			}else
			{
				$id_change = $data['supplier_id'];
			}
		}elseif($request_subtype == 3 or $request_subtype == 4)
		{
			$id_change = $data['request_reason'];
		}
		
// print $id_change;exit;
		$store_id = $store_id ? $store_id : $store_id_first;
		$data_output = $CMS->store_request->change_request_subtype($request_subtype, $sub_id, $id_change, $store_id);

		if($request_subtype == 3 or $request_subtype == 4)
		{
			$row_note = 9;
		}else
		{
			$row_note = 5;
		}

		if($request_subtype == 1)
		{
			$title_page = $CMS->lang['request_bill_export_1_'.$sub_id];
			$title_btn_submit = $CMS->lang['request_bill_add_export_1_'.$sub_id];
		}else
		{
			$title_page = $CMS->lang['request_bill_export_'.$request_subtype];
			$title_btn_submit = $CMS->lang['request_bill_add_export_'.$request_subtype];
		}
		$out=<<<EOF
<form id="form_add_request" name="form_add_request" class="form_request" action="{$CMS->vars['root_domain']}/?site=store_request&act=add_do&stage={$stage}" method="POST" enctype="multipart/form-data">
	
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$title_page}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
<figure class="box-typical box-typical box-typical-padding border">
	<div class="row">
		<div class="col-md-6">
			<fieldset class="form-group choose_store">
				<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;

				if($row_store >= 3)
				{
					$out .=<<<EOF
						<div class="form-control-wrapper">
							<div class="box_validate">
							   	<select name="store_id" id="store_id" defaultvalue="{$data['store_id_bk']}" class="form-control select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_store']}" autocomplete="off">						
		                       		{$option_store}
		                        </select>
							</div>
						</div>

EOF;
				}									
				else
				{
					$out .=<<<EOF
					<span class="typeahead-query">
						   	{$option_store}
					</span>
					 
EOF;
				}
				
	
	$out .=<<<EOF
				<input type="hidden" name="type_bill" value="export"/>
			    <input type="hidden" name="request_type" value="1" />
			    <input type="hidden" name="request_subtype" value="{$subtype}" />
			    <input type="hidden" name="check_sup_id" value="{$sub_id}" />
			    <input type="hidden" name="invalid_store_id" value="{$store_id_first}" />
			</fieldset>

			<fieldset class="form-group" id="content_change">
				 {$data_output}
			</fieldset>
			
			
	        
 		</div>
 		<div class="col-md-6">
			
			<fieldset class="form-group">
				<label class="form-label" for="request_note">{$CMS->lang['request_note']}</label>
				<textarea name="request_note" id="request_note" rows="{$row_note}" cols="50" class="form-control">{$data['request_note']}</textarea>  
			</fieldset>
			

		</div>
</section>

		{$html_product}
		<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
			<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request" data-style="expand-right" data-size="xs" type="button" ><span class="ladda-label">{$title_btn_submit}</span><span class="ladda-spinner"></span></button>
			<input type="hidden" value="{$CMS->input['stage']}" name="stage" />
		</section>
	</form>
	
	{$CMS->global->fullFormHtml()}
	</div>
</section>
<script src="{$CMS->vars['js_acp']}/custom_transaction.js?20180312"></script>
<script>
	$(document).ready(function(){
		var invalid_store_id = $(".choose_store [name='store_id']:checked").val();
		$("select#request_subtype").attr("invalid_store_id", invalid_store_id);
		edit_do_customer('#box_customer');
		add_new_customer('#content_change');
		add_do_new_customer('#box_customer');

		validate_form_custom("#form_add_request",".btn_add_request", "store_request");
	});
</script>

EOF;
		return $out;
	}


	public function editExport($data=array())
	{
		global $CMS, $DB, $member;
        // print "<pre>";
        // print_r($data);exit;
        $request_product = json_decode($data['request_product'], true);
        $_SESSION['list_product'] = $request_product;
// print "<pre>";
        // print_r($data['store_id_bk']);exit;
        $stage = $CMS->input['stage'];
		list($row_store, $option_store) = $CMS->store->get_list_store($data['store_id_bk']);

		$html_product = $CMS->global->htmlTableAsset($_SESSION['list_product']);
		// print "<pre>";
		// print_r($_SESSION['list_product']);exit;

		$data['request_subtype'] = $data['request_subtype'] ? $data['request_subtype'] : intval($CMS->input['subtype']);

		$sub_id = 0;
		
		if($data['request_subtype'] == 1)
		{
			if($data['cus_id'])
			{
				$sub_id = 2;
				$id_change = $data['cus_id'];
				$title_edit_bill = $CMS->lang['request_edit_export_1_2'];
			}else
			{
				$sub_id = 1;
				$id_change = $data['supplier_id'];
				$title_edit_bill = $CMS->lang['request_edit_export_1_1'];
			}
			$row_note = 5;
		}elseif($data['request_subtype'] == 2)
		{
			$row_note = 5;
			$id_change = $data['store_id_to'];
			$title_edit_bill = $CMS->lang['request_edit_export_2'];
		}elseif($data['request_subtype'] == 3 or $data['request_subtype'] == 4)
		{
			$row_note = 9;
			$id_change = $data['request_reason'];
			$title_edit_bill = $CMS->lang['request_edit_export_'.$data['request_subtype']];
		}
		$data_output = $CMS->store_request->change_request_subtype($data['request_subtype'], $sub_id, $id_change, $data['store_id_bk']);


		$out=<<<EOF
<form id="form_add_request" name="form_add_request" class="form_request" action="{$CMS->vars['root_domain']}/?site=store_request&act=edit_do&stage={$stage}&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">
	
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$title_edit_bill}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
<figure class="box-typical box-typical box-typical-padding border">
	<div class="row">
		<div class="col-md-6">
			<fieldset class="form-group choose_store">
				<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;

				if($row_store >= 3)
				{
					$out .=<<<EOF
						<span class="typeahead-query">
						   	<select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control select2" autocomplete="off">						
	                       		{$option_store}
	                        </select>
						</span>

EOF;
				}									
				else
				{
					$out .=<<<EOF
					 {$option_store}
EOF;
				}
				
	// $option_cus = $CMS->customer->getListOption();
	
	$out .=<<<EOF
				<input type="hidden" name="type_bill" value="export"/>
			    <input type="hidden" name="request_type" value="1" />
			    <input type="hidden" name="request_subtype" value="{$data['request_subtype']}" />
			    <input type="hidden" name="check_sup_id" value="{$data['supplier_id']}" />
			    <input type="hidden" name="invalid_store_id" value="{$data['store_id_bk']}">
			</fieldset>
			

			<!--fieldset class="form-group">
				<label class="form-label" for="request_note">{$CMS->lang['request_subtype']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
				<select name="request_subtype" id="request_subtype" sub_id="{$sub_id}" invalid_store_id = '{$data['store_id_bk']}' class="form-control auto_select" defaultvalue="{$data['request_subtype']}">
					<option value>{$CMS->lang['title_choose_plz']}</option>
					<option value="1">{$CMS->lang['request_subtype_1']}</option>
					<option value="2">{$CMS->lang['request_subtype_2']}</option>
					<option value="3">{$CMS->lang['request_subtype_3']}</option>
				</select>  
			</fieldset-->
			<fieldset class="form-group" id="content_change">
				 {$data_output}
			</fieldset>
	        
 		</div>
 		<div class="col-md-6">
			<fieldset class="form-group">
				<label class="form-label" for="request_note">{$CMS->lang['request_note']}</label>
				<textarea name="request_note" id="request_note" rows="{$row_note}" cols="50" class="form-control">{$data['request_note']}</textarea>  
			</fieldset>

		</div>
</section>

		{$html_product}
		<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
			<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request" data-style="expand-right" data-size="xs" type="button" ><span class="ladda-label">{$title_edit_bill}</span><span class="ladda-spinner"></span></button>
			<input type="hidden" value="{$CMS->input['stage']}" name="stage" />
		</section>
	</form>
	
	{$CMS->global->fullFormHtml()}
	</div>
</section>
<script src="{$CMS->vars['js_acp']}/custom_transaction.js?20180312"></script>
<script>
	 
    var module_name = 'module_storerequest';
	 
	$(document).ready(function(){
		
		edit_do_customer('#box_customer');
		add_new_customer('#content_change');
		add_do_new_customer('#box_customer');
	});
</script>
EOF;
		return $out;
	}


	public function approveExport($data=array())
	{
		global $CMS;

		// print "<pre>";
        // print_r($data);exit;
        $request_product = json_decode($data['request_product'], true);
        $_SESSION['list_product'] = $request_product;
// print "<pre>";
//         print_r($request_product);exit;
        $stage = $CMS->input['stage'];
		list($row_store, $option_store) = $CMS->store->get_list_store($data['store_id_bk']);

		$html_product = $CMS->global->htmlTableAsset($_SESSION['list_product']);
		// print "<pre>";
		// print_r($_SESSION['list_product']);exit;
		$data['request_subtype'] = $data['request_subtype'] ? $data['request_subtype'] : intval($CMS->input['subtype']);
		$sub_id = 0;
		if($data['request_subtype'] == 1)
		{
			if($data['cus_id'])
			{
				$sub_id = 2;
				$id_change = $data['cus_id'];
				$title_approve_bill = $CMS->lang['request_approve_export_1_2'];
			}else
			{
				$sub_id = 1;
				$id_change = $data['supplier_id'];
				$title_approve_bill = $CMS->lang['request_approve_export_1_1'];
			}
			$row_note = 5;
			
		}elseif($data['request_subtype'] == 2)
		{
			$id_change = $data['store_id_to'];
			$row_note = 5;
			$title_approve_bill = $CMS->lang['request_approve_export_2'];
		}elseif($data['request_subtype'] == 3 or $data['request_subtype'] == 4)
		{
			$id_change = $data['request_reason'];
			$row_note = 9;
			$title_approve_bill = $CMS->lang['request_approve_export_'.$data['request_subtype']];
		}
		
		$data_output = $CMS->store_request->change_request_subtype($data['request_subtype'], $sub_id, $id_change, $data['store_id_bk']);

		$out=<<<EOF
<form id="form_add_request" name="form_add_request" class="form_request" action="{$CMS->vars['root_domain']}/?site=store_request&act=approve_do&stage={$stage}&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">
	
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$title_approve_bill}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
<figure class="box-typical box-typical box-typical-padding border">
	<div class="row">
		<div class="col-md-6">
			<fieldset class="form-group choose_store">
				<label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;

				if($row_store >= 3)
				{
					$out .=<<<EOF
						<span class="typeahead-query">
						   	<select name="store_id" id="store_id" defaultvalue="{$data['store_id_bk']}" class="form-control auto_select select2" autocomplete="off">						
	                       		{$option_store}
	                        </select>
						</span>

EOF;
				}									
				else
				{
					$out .=<<<EOF
					 {$option_store}
EOF;
				}
				
	
	$out .=<<<EOF
				<input type="hidden" name="type_bill" value="export"/>
			    <input type="hidden" name="request_type" value="1" />
			    <input type="hidden" name="request_subtype" value="{$data['request_subtype']}" />
			    <input type="hidden" name="check_sup_id" value="{$data['supplier_id']}">
			    <input type="hidden" name="invalid_store_id" value="{$data['store_id_bk']}">
			</fieldset>
			

			<!--fieldset class="form-group">
				<label class="form-label" for="request_note">{$CMS->lang['request_subtype']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
				<select name="request_subtype" id="request_subtype" sub_id="{$sub_id}" invalid_store_id = '{$data['store_id_bk']}' class="form-control auto_select" defaultvalue="{$data['request_subtype']}">
					<option value>{$CMS->lang['title_choose_plz']}</option>
					<option value="1">{$CMS->lang['request_subtype_1']}</option>
					<option value="2">{$CMS->lang['request_subtype_2']}</option>
					<option value="3">{$CMS->lang['request_subtype_3']}</option>
				</select>  
			</fieldset-->
			<fieldset class="form-group" id="content_change">
				 {$data_output}
			</fieldset>

	        
 		</div>
 		<div class="col-md-6">
			<fieldset class="form-group">
				<label class="form-label" for="request_note">{$CMS->lang['request_note']}</label>
				<textarea name="request_note" id="request_note" rows="{$row_note}" cols="50" class="form-control">{$data['request_note']}</textarea>  
			</fieldset>
		</div>
</section>

		{$html_product}
		<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
			<button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_request" data-style="expand-right" data-size="xs" type="button" ><span class="ladda-label">{$title_approve_bill}</span><span class="ladda-spinner"></span></button>
			<input type="hidden" value="{$CMS->input['stage']}" name="stage" />
		</section>
	</form>
	
	{$CMS->global->fullFormHtml()}
	</div>
</section>
<script src="{$CMS->vars['js_acp']}/custom_transaction.js?20180312"></script>
<script>
	$(document).ready(function(){
		var invalid_store_id = $(".choose_store [name='store_id']:checked").val();
		$("select#request_subtype").attr("invalid_store_id", invalid_store_id);
	});
</script>
EOF;
	
	return $out;
	}
	
}