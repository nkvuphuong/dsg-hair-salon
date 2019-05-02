<?php
use core\ezy;
use models\custom_status;
use lib\input;

ezy::load_model("custom_status");

class skin_order{

    /**
     * Header data
     * @return string
     */

	public function head() {
		global $CMS, $DB, $member;
		// Check enable salon hang
		if($CMS->vars['addon_goods_enable'] == 1)
		{
			$addon_goods_enable = "block";
			$stock = $CMS->order->check_stock($data['ord_id']);
		}
		else
		{
			$addon_goods_enable = "none";
			$stock = 0;
		}

		$dis='';$free='';
		if($CMS->input['type']!=''&&$CMS->input['type']==1) $dis='selected';
		if($CMS->input['type']!=''&&$CMS->input['type']==0) $free='selected';

		$time_from=isset($CMS->input['time_from'])?date("d/m/Y",$CMS->input['time_from']):'';
		$time_to=isset($CMS->input['time_to'])?date("d/m/Y",$CMS->input['time_to']):'';
		$ord_name = urldecode($CMS->input['name']) != "" ? $CMS->input['name'] : "";
		$ord_status =  $CMS->input['status']  != "" ? $CMS->input['status'] : "";
		// Get custom status
		$data_status = json_decode($CMS->vars['custom_status'], 1);
		$listStatus = $data_status[$CMS->input['status']]; //custom_status::getStatusByOrdId($CMS->input['status']);
		$listnumberOrder = $CMS->order->getNumberOrderByCstatus($listStatus, $ord_status);
		if(isset($CMS->input['status']) AND $CMS->input['status'] == 0)
		{
			$active_0 = " active "; $active_all = " "; $active_1 = " ";  $active_2 = " ";
		}
		elseif(isset($CMS->input['status']) AND $CMS->input['status'] == 1)
		{
			$active_1 = " active "; $active_all = " "; $active_0 = " ";$active_2 = " ";
		}elseif(isset($CMS->input['status']) AND $CMS->input['status'] == 2)
		{
			$active_2 = " active "; $active_all = " "; $active_0 = " ";$active_1 = " ";
		}
		else
		{
			$active_1 = " "; $active_all = " active "; $active_0 = " "; $active_2 = " ";
		}

		$selected_store_id[$CMS->input['store_id']] = 'selected';
		$selected_ord_status[$CMS->input['ord_status']] = 'selected';
		$selected_payment_method[$CMS->input['payment_method']] = 'selected';

		$ss_name = urldecode($CMS->input['name']);
		$check_search = input::get("check_search");
		// p($check_search); print "asdfa";exit;

        $sql_add = "";

        if(! \lib\security::checkPermission($CMS->input['site'], 'all_branches')) {
            $sql_add = "store_id = {$member['store_id']} AND";
        }

		$cnt_status_0 = $CMS->order->count_order_by_status(0, $sql_add)*1;
		$cnt_status_1 = $CMS->order->count_order_by_status(1, $sql_add)*1;
		$cnt_status_2 = $CMS->order->count_order_by_status(2, $sql_add)*1;
		$cnt_status_all = $cnt_status_0 + $cnt_status_1 + $cnt_status_2;

		$output=<<<EOF

<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['order_title']}</h3>
		<figure class="pull-right right">
			<div class="search">
				<form method="get" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/" style="display:inline-block">
                    <input type="hidden" name="site" value="order">
					<input type="submit" class="fa-input" value="&#xf002;">
					<input type="text" name="o_quick_search" id="o_quick_search" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['gsearch_quick']}" style="position: :relative;" value="{$ss_name}" >
					<div id="suggesstion-box" class="box_result_find" style="display:none"></div>

				</form>
				<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
			</div>
			<a href="{$CMS->vars['root_domain']}/?site=order&act=add" title="" class="add_bill">{$CMS->lang['order_add_button']}</a>
		</figure>
	</figure>
	
	<!-- Advance search -->
 	<section class="search_adv" >
		<form method="get" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/" >
			<input type="hidden" name="site" value="order" />
			<figure class="box-typical box-typical box-typical-padding border">
				<h5>{$CMS->lang['gsearch_advance']}</h5>
				<ul class="input_li row match-height">
EOF;

		if(\lib\security::checkPermission(input::get('site'), 'all_branches')) {
            $output .= <<<EOF
					<li class="col-xl-3 col-md-3 col-sm-6" style="display:{$addon_goods_enable}">
						<select name="store_id" id="store_id" class="form-control select2" defaultvalue="{$CMS->input['store_id']}">
							{$CMS->store->get_list_store($CMS->input['store_id'],1)}
						</select>
					</li>
EOF;
        }

		$output .= <<<EOF
					<li class="col-xl-3 col-md-3 col-sm-6">
						<input  name="ord_name" id="ord_name" type="text" class="form-control" placeholder="{$CMS->lang['search_ord_name']}" value="{$CMS->input['ord_name']}" >
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<input name="trx_id" id="trx_id" type="text" class="form-control" placeholder="{$CMS->lang['search_trx_id']}" value="{$CMS->input['trx_id']}" >
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<input  name="cus_id" id="cus_id" type="text" class="form-control" placeholder="{$CMS->lang['search_cus_id']}" value="{$CMS->input['cus_id']}" >
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<input  name="ord_booking_phone" id="cus_id" type="text" class="form-control" placeholder="{$CMS->lang['ord_booking_phone']}" value="{$CMS->input['ord_booking_phone']}" >
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<select name="user_id" id="user_id" class="form-control select2" defaultvalue="{$CMS->input['user_id']}">
							{$CMS->user->load_list_user($CMS->input['user_id'], 0, false)}
						</select>
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<input name="total_from" id="ord_total_from" type="text" class="form-control" placeholder="{$CMS->lang['ord_total_from']}" value="{$CMS->input['ord_total_from']}" >
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<div class="input-group date daterange-1">
							<input type="text" name="time_from_to" placeholder="{$CMS->lang['time_from_to']}" class="form-control" value="{$CMS->input['time_from_to']}">
							<span class="input-group-addon">
							<i class="font-icon font-icon-calend"></i>
						</span>
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<input  name="total_to" id="ord_total_to" type="text" class="form-control" placeholder="{$CMS->lang['ord_total_to']}" value="{$CMS->input['ord_total_to']}" >
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<select name="status" onchange="changeCusStatus(this, '.result_status');" class="form-control auto_select" defaultvalue="{$CMS->input['status']}" data-json='{$CMS->vars['custom_status']}'>
							<option value="">{$CMS->lang['select_order_status']}</option>
							<option value="0">{$CMS->lang['order_status_0']}</option>
							<option value="1">{$CMS->lang['order_status_1']}</option>
							<option value="2">{$CMS->lang['order_status_2']}</option>
							<option value="3">{$CMS->lang['order_status_3']}</option>
						</select>
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<select name="custom_status" class="form-control result_status auto_select2" defaultvalue="{$CMS->input['custom_status']}">
							<option value="">{$CMS->lang['select_custom_status']}</option>
						</select>
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<select name="payment_method" class="form-control select2">
							<option value="">{$CMS->lang['payment']}</option>
							<option {$selected_payment_method[0]} value="0">{$CMS->lang['payment_short_type_0']}</option>
							<option {$selected_payment_method[1]} value="1">{$CMS->lang['payment_short_type_1']}</option>
							<option {$selected_payment_method[2]} value="2">{$CMS->lang['payment_short_type_2']}</option>
							<option {$selected_payment_method[5]} value="5">{$CMS->lang['payment_short_type_5']}</option>
							<option {$selected_payment_method[6]} value="6">{$CMS->lang['payment_short_type_6']}</option>
							<option {$selected_payment_method[7]} value="7">{$CMS->lang['payment_short_type_7']}</option>
						</select>
					</li>
					<li class="col-xl-3 col-md-3 col-sm-6">
						<input type="submit" onclick="$('[name=check_search]').val(1);" value="{$CMS->lang['comment_filter']}">
						<input type="hidden" value="{$CMS->input['sort']}" name="sort" />
						<input type="hidden" value="{$CMS->input['check_search']}" name="check_search" />
						<input type="hidden" value="{$CMS->input['order_by']}" name="order_by" /> 	
					</li>
				</ul>
			</figure>
		</form>	
		<script>
		$(document).ready(function(){
			var check_search = "{$check_search}";
			if(check_search)
			{
				$("#formsearch_adv").show();
			}
		});
		</script> 
	</section>
EOF;
	
// 	$output .=<<<EOF
// 	<!-- Tabs news -->
// 	<div class="tabs-section-nav tabs-section-nav-data">
// 		<div class="tbl">
// 			<ul class="nav" role="tablist">
// 				<li class="nav-item">
// 					<a class="nav-link {$active_all}"  href="{$CMS->vars['root_domain']}/?site=order">
// 						<span class="nav-link-in">
// 							<span class="number color-gray">{$cnt_status_all}</span>
// 							<span class="percent color-gray"></span>
// 							<span class="title">{$CMS->lang['ord_status_all']}</span>
// 						</span>
// 					</a>
// 				</li>
// 				<li class="nav-item">
// 					<a class="nav-link {$active_0} " href="{$CMS->vars['root_domain']}/?site=order&status=0">
// 						<span class="nav-link-in">
// 							<span class="number color-gray">{$cnt_status_0}</span>
// 							<span class="percent color-gray"></span>
// 							<span class="title">{$CMS->lang['ord_status_0']}</span>
// 						</span>
// 					</a>
// 				</li>
// 				<li class="nav-item">
// 					<a class="nav-link  {$active_1}" href="{$CMS->vars['root_domain']}/?site=order&status=1">
// 						<span class="nav-link-in">
// 							<span class="number color-yellow">{$cnt_status_1}</span>
// 							<span class="percent color-yellow"></span>
// 							<span class="title">{$CMS->lang['ord_status_1']}</span>
// 						</span>
// 					</a>
// 				</li>
// 				<li class="nav-item">
// 					<a class="nav-link  {$active_2}" href="{$CMS->vars['root_domain']}/?site=order&status=2">
// 						<span class="nav-link-in">
// 							<span class="number color-green">{$cnt_status_2}</span>
// 							<span class="percent color-green"></span>
// 							<span class="title">{$CMS->lang['ord_status_2']}</span>
// 						</span>
// 					</a>
// 				</li>
// 			</ul>
// 		</div>
// 	</div>
// EOF;
	
	$output .=<<<EOF
	<section class="box-heading box-status box-filternav" style="padding-bottom: 0;margin-bottom: 0;">
		<div class="box-heading-body">
			<ul class="nav-filter clearfix">
				<li class="{$active_all}">
					<a href="{$CMS->vars['root_domain']}/?site=order">{$cnt_status_all}<span>{$CMS->lang['ord_status_all']}</span></a>
				</li>
				<li class="{$active_0}">
					<a href="{$CMS->vars['root_domain']}/?site=order&status=0">{$cnt_status_0}<span>{$CMS->lang['ord_status_0']}</span></a>
				</li>
				<li class="{$active_1}">
					<a href="{$CMS->vars['root_domain']}/?site=order&status=1">{$cnt_status_1}<span>{$CMS->lang['ord_status_1']}</span></a>
				</li>
				<li class="{$active_2}">
					<a href="{$CMS->vars['root_domain']}/?site=order&status=2">{$cnt_status_2}<span>{$CMS->lang['ord_status_2']}</span></a>
				</li>
			</ul>
		</div>
	</section>
EOF;

	$output .=<<<EOF
	<script>
	        $("select[name='sort_by']").change(function(){
				var sorder = $(this).val();
				var orderby = $("select[name='by']").val();
				
				$("input[name='order_by']").val(orderby);
				$("input[name='sort']").val(sorder);
				$("form.quick_search").submit();	
			});
			$("select[name='by']").change(function(){
				var sorder = $("select[name='sort_by']").val();
				var orderby = $(this).val();

				$("input[name='order_by']").val(orderby);
				$("input[name='sort']").val(sorder);
				$("form.quick_search").submit();	
			});
			$("select[name='sort_by'] option[value='{$CMS->input['sort']}']").prop("selected",true);
			$("select[name='by'] option[value='{$CMS->input['order_by']}']").prop("selected",true);
	</script>
EOF;

		if(count($listStatus) > 1)
		{
	$output .=<<<EOF
<!--Filter custom status-->
<section class="filter_custom_status" style="clear: both;padding: 10px 0; background: #fff; overflow: hidden; border: 1px solid #d8e2e7; border-top: none;">
	<form method="get" name="filter" id="formsearch_adv" action="{$CMS->vars['root_domain']}" >
	<input type="hidden" name="site" value="order" />
	<input type="hidden" name="status" value="{$CMS->input['status']}" />
	<!--div class="col-lg-2 col-md-3 col-ms-3 col-xs-6">
		<div class="checkbox">
			<input type="checkbox" value="all" check=1 checked="checked" id="check-all">
			<label for="check-all">All</label>
		</div>
	</div-->
EOF;

			foreach ($listStatus as $status) 
			{
				$style_background = $listnumberOrder[$status] ? "" : "gray";
$output .=<<<EOF
			
	<div class="col-lg-2 col-md-2 col-ms-3 col-xs-6">
		<div class="checkbox">
			<input type="checkbox" id="check-{$status}" check=0 class="findstatus" value="{$status}">
			<label for="check-{$status}">
				<span class="nav-link-in nav-link-in-custom">
					<span class="title">{$CMS->lang["title_custom_status_{$status}"]}</span>
					<span class="label label-pill label-primary {$style_background}">{$listnumberOrder[$status]}</span>
				</span>
			</label>
		</div>
	</div>
EOF;
			}
		
$output .=<<<EOF
</section>
<input type='hidden' name='list_status' value='{$CMS->input['list_status']}' />
</form>
EOF;
 		}

$output .=<<<EOF
<script>
	$(document).ready(function(){
		var liststatus = $("[name=list_status]").val();

		var status_active = liststatus ? liststatus.split(",") : [];
        for(var x in status_active) 
        {
            if(status_active[x])
            {
                $("[id='check-"+status_active[x]+"']").prop("checked", true);
                $("[id='check-"+status_active[x]+"']").attr("check", 1);
            }
        }

		$(".findstatus").click(function(){
			var curr_id = $(this).val();

			var checkClick = $(this).attr("check");
	        if(checkClick == 0)
	        {
	            $(this).attr("check", 1);
	            liststatus += ","+curr_id;
	        }else
	        {
	            $(this).attr("check", 0);
	            var Reg = new RegExp(","+curr_id, "g");
	            liststatus = liststatus.replace(Reg, "");
	        }

	        $("input[name='list_status']").val(liststatus);
	        setTimeout(function(){
				$("form[name='filter']").submit();
	        }, 1000);
	        
		});
	});
</script>
<section class="add_table">
EOF;

		if($_SESSION['is_mobile'] == true)
		{
				$output .=<<<EOF
			<div class="data_table">
             <table id="example" class="display table table_cus">
				<thead class="vertical-middle">

					   
					   <tr>		 
							<th width="2%" data-orderable="false" data-sortable="false"></th>
							<th width="3%">{$CMS->lang['order_name']}</th>	
							<th width="20%" data-sortable="false">{$CMS->lang['cus_id']}</th>
							<th width="45%" data-sortable="false">{$CMS->lang['order_item']}</th>
							<th width="10%">{$CMS->lang['order_total_short']}</th>
							<th width="5%" data-sortable="false">{$CMS->lang['invoice_no']}</th>
							<th width="5%">{$CMS->lang['order_status_ls']}</th> 
							<th width="5%">{$CMS->lang['order_time']}</th> 
							<th width="5%" data-sortable="false"> </th>
								 
						</tr>
					</thead>
				<tbody>	

EOF;
		}
		else
		{
			$output .=<<<EOF
			<div class="data_table">
             <table id="example" class="display table table_cus tbl-typical">
				<thead class="vertical-middle">

				   <tr>		 
						<th width="5%">{$CMS->lang['order_name']}</th>
						<th width="20%" data-sortable="false">{$CMS->lang['cus_id']}</th>
						<th width="45%" data-sortable="false">{$CMS->lang['order_item']}</th>
						<th width="10%">{$CMS->lang['order_total_short']}</th>
						<th width="5%" data-orderable="false">{$CMS->lang['invoice_no']}</th>
						<th width="5%">{$CMS->lang['order_status_ls']}</th> 
						<th width="5%">{$CMS->lang['order_time']}</th> 
						<th width="5%" data-sortable="false"> </th>
							 
					</tr>
				</thead>
			<tbody>	

EOF;
		}										 
					$output .=<<<EOF
				 			 
							

							
						


EOF;
		return $output;
	}

    /**
     * Footer list
     * @return string
     */
	
	public function foot() {
		global $CMS, $DB, $member;
		$output =<<<EOF
 

			 	</tbody>
				</table>
			</div>
          	<div class="fuction_table">
				<div class="pull-left">

					<p class="form-control-static ">
					 

					</p>
					 

				</div>
				<nav class="pull-right">
				     {$CMS->order->show_page}
				</nav>
		 </div>


	 </section>
		 
		 
</section>		
      <script src="{$CMS->vars['js_acp']}/order.js"></script>

 
EOF;

		if($_SESSION['is_mobile'] == true)
		{
				$output .=<<<EOF
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
			$output .=<<<EOF
							 
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

 
		return $output;
	}

    /**
     * Middle data
     * @param null $data
     * @return string
     */
	
	public function mid($data=NULL) {
		global $CMS, $DB, $member;
		// print "<pre>";
		// print_r($data);exit;
	 
		
		$output =<<<EOF
				<tr>
EOF;

	if($_SESSION['is_mobile'] == true)
		{
				$output .=<<<EOF
				<td></td>
EOF;
		}
				$output .=<<<EOF
					<td>
					    {$data['ord_name_bk']} 
					    <div class="store-name-text color-initial display-flex">{$data['store_name']}</div>
					</td>
					<td><div class="customer-name-text" style='display: inline; max-width: 100%; width: 100%'>{$data['cus_name_bk']}</div></td>
					<td>{$data['product_name']}</td>
					<td> {$data['ord_total_n']}</td>	
					<td>{$data['trx_name_ls']}</td>
					<td>
					 	<div>{$data['payment_status_bk']}</div>
					 	<div>{$data['ord_status_n']}</div>
					</td>
					<td>
						<div>{$data['ord_time_bk_date']}</div>
						<div>{$data['ord_time_bk_hours']}</div>
					</td> 
					<td>
			 
EOF;
					if( $CMS->permit['order_edit'] == 1 AND $data['payment_status'] == 0 AND !in_array($data['ord_status'], array('2', '3')) )
					{
						$output .=<<<EOF
						<div class="clearfix">
						<a href="{$CMS->vars['root_domain']}/?site=order&act=edit&id={$data['ord_id']}" class="edit" data-toggle="tooltip" data-placement="bottom" title="{$CMS->lang['act_edit_order']}"><i class="fa fa-edit"></i></a>
						</div>

EOF;

					}	

					if( $CMS->permit['order_delete'] == 1 AND $data['ord_status'] == 3 )
					{
						$output .=<<<EOF
						<div class="clearfix" style="margin-top: 5px;">
						<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=order&act=delete&id={$data['ord_id']}',  '{$CMS->lang['confirm_delete']}', '{$CMS->lang['confirm_delete_note']}');" class="edit" data-toggle="tooltip" data-placement="bottom" title="{$CMS->lang['act_delete_order']}"><i class="fa fa-trash-o"></i></a>
						</div>

EOF;

					}
					
					$output .=<<<EOF
						<div class="clearfix" style="margin-top: 5px;">
							<a href="{$CMS->vars['root_domain']}/?site=order&act=show&id={$data['ord_id']}&tab=tabs-4-tab-1&position=worklogs" class="edit" data-toggle="tooltip" data-placement="bottom" title="{$CMS->lang['act_worklogs']}"><i class="fa fa-list"></i></a>
						</div>
					</td>
				</tr>
EOF;
		return $output;
	}

    /**
     * Middle no data
     * @return string
     */
	
	public function none() {
		global $CMS, $DB, $member;
		$output=<<<EOF
				<tr>
					<td colspan="10" align="center" class="no_data"><h5>No Order</h5></td>
				</tr>
EOF;
		return $output;
	}

    /**
     * Show Order
     * @param null $data
     * @param string $order_item
     * @return string
     */
		
public function show($data=null, $order_item = "", $list_transaction = "") {
		global $CMS, $DB, $member;

		// Check enable salon hang
		if($CMS->vars['addon_goods_enable'] == 1)
		{
			$addon_goods_enable = "block";
			$stock = $CMS->order->check_stock($data['ord_id']);
		}
		else
		{
			$addon_goods_enable = "none";
			$stock = 0;
		}


 		// Check tồn salon
  		$option_status = "";
 		
		for ($i =0 ; $i <= 2; $i++) { 
		 
			if ($i== $data['ord_status'] AND $data['ord_status'] != "") {
				$option_status .= "<option value='{$i}' selected>{$CMS->lang['order_status_'.$i]}</option>";
			} else {
				 
				$option_status .= "<option value='{$i}'>{$CMS->lang['order_status_'.$i]}</option>";
			}
		}

$output =<<<EOF
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['order_detail_title']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=order" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 		    <!-- Column 1 -->
 			<div class="col-xl-4 col-md-6 col-sm-5 col-xs-6">
 				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['service_type']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['service_type_bk']}</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['order_name']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">   {$data['ord_name_bk']}</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['order_user']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['cus_name_bk']}</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['sale_user_id']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['user_name_bk']}</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['order_time']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['ord_time_bk']}</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['order_time_update']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['ord_time_update_bk']}</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['is_shipping']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['is_shipping_bk']}</div>
				</fieldset>

			</div>
			<!-- Column 2 -->
EOF;
// print "<pre>"; print_r($data);exit;
			$ord_content = json_decode($data['ord_content'], 1);
			if(substr(\core\ezy::$web_theme,0,3) == "tra")
			{
$output .=<<<EOF
				<div class="col-xl-4 col-md-6 col-sm-4 col-xs-6">
					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['cus_phone']}</label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['cus_phone']}</div>
					</fieldset>
					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >Email </label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['cus_email']}</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['date_booking_start']}</label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['booking_date_start']}</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['date_booking_end']} </label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['booking_date_end']}</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['total_people']}</label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['guest_total']}</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['guest_adult']}</label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['guest_adult']}</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['guest_child_1']}</label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['guest_child_1']}</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['guest_child_1']}</label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['guest_child_2']}</div>
					</fieldset>

					<fieldset class="form-group row">
						<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['cus_note']}</label>
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$ord_content['cus_note']}</div>
					</fieldset>
				</div>
EOF;

			}else
			{

$output .=<<<EOF

			<div class="col-xl-4 col-md-6 col-sm-4 col-xs-6">
				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['payment_method']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2"> 
							{$data['payment_mothod_bk']}
EOF;
						if($data['account_id'] > 0)
						{
									$output .=<<<EOF
								 <span>- {$CMS->lang['account_id']}:	{$data['account_name']}</span>
EOF;
						}	
						$output .=<<<EOF
						 
					</div>
				</fieldset>

				 
EOF;
//				if($data['service_type'] == 0)
				{
					$output .=<<<EOF
				 <fieldset class="form-group row"  style="display:{$addon_goods_enable}">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['store_name']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['store_name']}</div>
				</fieldset>
EOF;

				}				
//				 else
                if($data['service_type'] == 1)
				 {
				$output .=<<<EOF
				<fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['booking_date']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['booking_date_bk']}</div>
				</fieldset>
				 <fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['booking_hours']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['booking_hours_bk']}</div>
				</fieldset>
EOF;
				 }
				 
				$output .=<<<EOF

				 <fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['order_note']}</label>
					<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">{$data['ord_note']}</div>
				</fieldset>

				 <fieldset class="form-group row">
					<label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" >{$CMS->lang['order_last_comment']}</label>
					
EOF;
						if( $data['ord_last_comment'] != "")
						{
							$output .=<<<EOF
							<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2" style="border:1px solid #e3e3e3;">
							<span style="padding:5px;word-wrap: break-word;width: 100%;">{$data['ord_last_comment']}</span>
								</div>
EOF;
						}
						else
						{
							$output .=<<<EOF
						<div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2" >
							<span style="padding:5px;word-wrap: break-word;width: 100%;">{$data['ord_last_comment']}</span>
						</div>
EOF;
						}
					$output .=<<<EOF

					
				</fieldset>


			</div>
EOF;

			}

$output .=<<<EOF

			 <!-- Column 3 -->
			<div class="col-xl-4 col-md-6 col-sm-3 col-xs-12">	
EOF;

			if($CMS->vars['enabled_commission'])
            {
                $output .= <<<EOF
			    <div class="row">
                    <div class="form-control-label2 row">
                     <div class="title_label2 col-xl-4 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['gcommission']}</div>
                      <div class="title_label2 col-xl-8 col-lg-12 col-md-12 col-sm-12 col-xs-12">   
                      </div>
                      <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$CMS->class->input->currency($data['ord_commission'])} ({$CMS->lang['rate']}: {$data['ord_commission_rating']})</p>
                    </div>
			    </div>
EOF;
            }


			$output .= <<<EOF
				<div class="row">
                    <div class="form-control-label2 row">
                     <div class="title_label2 col-xl-4 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['order_total']}
                             <div class="dropdown dropdown-typical" style="float:left;margin-right:5px">
                                    <a class=" dropdown-toggle-txt"  data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <span ><i class="fa fa-info-circle"></i></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-left"  >
                                        <span  class="dropdown-item">{$CMS->lang['ord_amount']}: {$data['ord_amount_n']} </span>
                                        <span  class="dropdown-item">{$CMS->lang['ord_discount']}: {$data['ord_discount_n']} </span>
                                        <span  class="dropdown-item">{$CMS->lang['ord_tax']}: {$data['ord_tax_n']} </span>

                                         
                                    </div>
                                </div>	
                     </div>
                      <div class="title_label2 col-xl-8 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            
                      </div>
                      <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['ord_total_n']}
                            
                      </p>
                      <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">	{$data['ord_status_n']}</p>
                        <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">		{$data['payment_status_bk']}</p>

                    </div>
			    </div>
			</div>		
	 </div>
 	</figure>	
</section>
{$order_item}
{$list_transaction} 
EOF;

        // SHipping
		if($data['is_shipping']  == 1 AND $data['service_type'] == 0)
		{

			$ship_deliver_fee = $CMS->class->input->currency($data['ship_deliver_fee']);

			$output .=<<<EOF
<div class="add_table">
	<section  class="add_form main_form" id="container_shipping_info">
	<h4 class="heading"><i class="fa fa-caret-down"></i><span>{$CMS->lang['ship_header']}</span></h4>	
	<figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			 

			<div class="col-xl-4 col-md-6 col-sm-12 col-xs-12">
					
					<fieldset class="form-group row">
					 			<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_receive_name']}:</label>
								 <div class="col-xl-7 form-control-span2"> {$data['ship_receive_name']}</div>
						 
					 </fieldset>	
					 <fieldset class="form-group row">
					 			<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_phone']}:</label>
								  <div class="col-xl-7 form-control-span2"> {$data['ship_phone']}</div> 
					 </fieldset>	
					 <fieldset class="form-group row">
					 			<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_address']}:</label>
								 
								<div class="col-xl-7 form-control-span2">{$data['ship_address']}</div>
					 </fieldset>	
					  <fieldset class="form-group row">
					 			<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_location']}:</label>
								 
								<div class="col-xl-7 form-control-span2">{$data['ship_location']}</div>
					 </fieldset>	

			</div>

			<div class="col-xl-4 col-md-6 col-sm-12 col-xs-12">
					 <fieldset class="form-group row">
					 			<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_code']}:</label>
								 
								<div class="col-xl-7 form-control-span2">{$data['ship_code']}</div>
					 </fieldset>
					 <fieldset class="form-group row">
					 			<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_weight']}:</label>
								 
								<div class="col-xl-7 form-control-span2">{$data['ship_weight']}</div>
					 </fieldset>
					 <div class="row">
							<div class="col-lg-4">
								 <fieldset class="form-group row">
								 		<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_long']}:</label>
											 
										<div class="col-xl-7 form-control-span2">{$data['ship_long']}</div>
								 </fieldset>
							</div>
							<div class="col-lg-4">
								 <fieldset class="form-group row">
								 		<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_wide']}:</label>
											 
									<div class="col-xl-7 form-control-span2">{$data['ship_wide']}</div>
								 </fieldset>
							</div>
							<div class="col-lg-4">
								 <fieldset class="form-group row">
								 		<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_height']}:</label>
											 
										<div class="col-xl-7 form-control-span2">{$data['ship_height']}</div>
								 </fieldset>
							</div>
					 </div>	
					  <fieldset class="form-group row">
					 			<label class="col-xl-5 form-control-label2">{$CMS->lang['ship_service_type']}:</label>
								 
								<div class="col-xl-7 form-control-span2">{$data['ship_service_type_bk']}</div>
					 </fieldset>				
			</div>

			<div class="col-xl-4 col-md-6 col-sm-12 col-xs-12">
					<fieldset class="form-group row">
					 			<label class="form-label pull-left" >{$CMS->lang['ship_deliver']}:</label>
								<div class="col-xl-7 form-control-span2">{$data['ship_deliver_bk']}</div>	 
					 </fieldset>

					<fieldset class="form-group row">
					 			<label class="form-label pull-left" >{$CMS->lang['ship_deliver_fee']}:</label>
								<div class="col-xl-7 form-control-span2">{$ship_deliver_fee}</div>   
					 </fieldset>	
			</div>
		</div>
	</figure>					

  </section><!-- Shipping-->
 </div>
EOF;

		}

		$output .=<<<EOF
		<section class="add_cart_footer">
EOF;
		$url_back['list'] =	"{$CMS->vars['root_domain']}/?site=order";
 
		$output .= $CMS->global->footer_back($url_back);


	 	 if($_SESSION['is_mobile'] == true)
         {
            $output .=<<<EOF
     

               <div class="btn-group dropup pull-right hidden-xl-up">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa"></i>{$CMS->lang['save_option']}
                  </button>
                  <div class="dropdown-menu">
                    <ul>
EOF;
						if($CMS->permit['order_delete'] == 1  )
						{
							$output .=<<<EOF
                         <li><a onclick="delete_confirm_order('{$CMS->vars['root_domain']}/?site=order&act=delete&id={$data['ord_id']}');" ><i class="fa fa-trash-o"></i>{$CMS->lang['act_delete_order']}</a></li>
EOF;
						}
					    if($CMS->permit['order_add'] == 1)
						{
							 
								$output .=<<<EOF
				 
						  <li><a  href="{$CMS->vars['root_domain']}/?site=order&act=add&sub_act=copy&id={$data['ord_id']}"  ><i class="fa  fa-copy"></i> {$CMS->lang['act_copy_order']}</a></li>	
EOF;
						 


						}
							
						if($CMS->permit['order_edit'] == 1)
						{
 
							
							if( $data['ord_status'] != 2)
							{
								if($data['ord_status'] != 3)
								{
									$output .=<<<EOF
								 <li><a href="{$CMS->vars['root_domain']}/?site=order&act=edit&id={$data['ord_id']}"><i class="fa  fa-edit"></i> {$CMS->lang['act_edit_order']}</a></li>
								<li><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=cancel&id={$data['ord_id']}');" ><i class="fa  fa-stop-circle-o"></i> {$CMS->lang['act_cancel_order']}</a></li>
EOF;
								}

							}


							if( $data['ord_status'] == 0)
							{
								$title_button = $data['service_type'] == 1 ? $CMS->lang['act_process_booking'] : $CMS->lang['act_process_order'];
								// Duyet don hang
								$output .=<<<EOF
								 
								<li><a  onclick="alert_confirm_custom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=process&id={$data['ord_id']}', '{$CMS->lang['confirm_process_order']}');"><i class="fa fa-play-circle"></i> {$title_button}</a></li>

EOF;
							}

							if($data['payment_status'] == 0 AND $data['ord_status'] == 0)
							{
								// Duyet don hang
								$output .=<<<EOF
						 
								<li><a  onclick="alert_confirm_custom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=approve_unpaid&id={$data['ord_id']}');"><i class="fa fa-credit-card-alt"></i> {$CMS->lang['act_mark_as_debit']}</a></li>
EOF;
							}
							
							if(   in_array($data['ord_status'], array(2,3)) == false AND $data['payment_status'] == 1) 
							{

								if($CMS->vars['negative_sale'] == 1 AND $stock > 0 )// Chan ban âm
								{
								// Duyet don hang
								$output .=<<<EOF
					 
								<li><a  onclick="alertText('{$CMS->lang['store_not_enought']}','warning');"   ><i class="fa  fa-chevron-down"></i>{$CMS->lang['update_status_done']}</a></li>
EOF;
								}
								elseif($CMS->vars['negative_sale'] == 0 AND $stock == 0 )
								{

								// Duyet don hang
								$output .=<<<EOF
								<li><a  onclick="alert_confirm_stockcustom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=success&id={$data['ord_id']}&type=all&stock={$stock}', {$stock} ,'{$CMS->lang['confirm_complete_order']}</p>');"   ><i class="fa  fa-chevron-down"></i>{$CMS->lang['update_status_done']}</a></li>
EOF;
								}else
								{
									// Duyet don hang
								$output .=<<<EOF
								<li><a  onclick="alert_confirm_stockcustom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=success&id={$data['ord_id']}&type=all&stock={$stock}', {$stock} ,'{$CMS->lang['store_not_enought']} <p>{$CMS->lang['confirm_complete_order']}</p>');"   ><i class="fa  fa-chevron-down"></i>{$CMS->lang['update_status_done']}</a></li>
EOF;
								}
							}
							if($data['payment_status'] != 1 )
							{
								if( $data['ord_status'] != 3)
								{

								// Duyet don hang
								$output .=<<<EOF
								<li><a  onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=approve_paid&id={$data['ord_id']}');"><i class="fa fa-credit-card"></i> {$CMS->lang['act_mark_as_paid']}</a></li>
EOF;
								}
							}

						}
							

                       $output .=<<<EOF
                        <li><a href="{$CMS->vars['root_domain']}/?site=order&act=rating&id={$data['ord_id']}"><i class="fa  fa-star"></i>{$CMS->lang['order_commission_rating']}</a></li>
                    </ul>
                  </div>
                </div>
EOF;

		}
		else
		{
						if($CMS->permit['order_delete'] == 1  )
						{
							$output .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=order&act=delete&id={$data['ord_id']}');"  class="pull-right add_cart_2"><i class="fa fa-trash-o"></i> {$CMS->lang['act_delete_order']}</a>
EOF;

						}

						// Chuc nang sao chep
 						if($CMS->permit['order_add'] == 1)
						{
							 
								$output .=<<<EOF
							<a   href="{$CMS->vars['root_domain']}/?site=order&act=add&sub_act=copy&id={$data['ord_id']}" class="pull-right add_cart_2"><i class="fa fa-copy"></i> {$CMS->lang['act_copy_order']}</a>
EOF;
						}
							
						if($CMS->permit['order_edit'] == 1)
						{
							if( $data['ord_status'] != 2) // Chua hoan thanh van cho sua
							{
							 
								if($data['ord_status'] != 3)
								{
									$output .=<<<EOF
						 	<a  href="{$CMS->vars['root_domain']}/?site=order&act=edit&id={$data['ord_id']}" class="pull-right add_cart_2"><i class="fa fa-edit"></i> {$CMS->lang['act_edit_order']}</a>
							<a   onclick="alert_confirm_custom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=cancel&id={$data['ord_id']}');"   class="pull-right add_cart_2"><i class="fa fa-stop-circle-o"></i> {$CMS->lang['act_cancel_order']}</a>
EOF;
								}
							}
							
							if($data['ord_status'] == 0)
							{
								// Proccessing
								$title_button = $data['service_type'] == 1 ? $CMS->lang['act_process_booking'] : $CMS->lang['act_process_order'];
								$output .=<<<EOF
								<a  onclick="alert_confirm_custom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=process&id={$data['ord_id']}','{$CMS->lang['confirm_process_order']}');"   class="pull-right add_cart_2"><i class="fa fa-play-circle"></i> {$title_button}</a>
EOF;
							}

							if($data['payment_status'] == 0 AND $data['ord_status'] == 0)
							{

								// Duyet don hang
								$output .=<<<EOF
								<a  onclick="alert_confirm_custom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=approve_unpaid&id={$data['ord_id']}', '{$CMS->lang['confirm_command']}');"   class="pull-right add_cart_2"><i class="fa fa-credit-card-alt"></i> {$CMS->lang['act_mark_as_debit']}</a>
EOF;
							}
 
							if($data['payment_status'] != 1)
							{

								if( $data['ord_status'] != 3)
								{
									// Duyet don hang
									$output .=<<<EOF
									<a  onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=approve_paid&id={$data['ord_id']}');"   class="pull-right add_cart_2"><i class="fa fa-credit-card"></i> {$CMS->lang['act_mark_as_paid']}</a>
EOF;
								}
								
							}

							if( in_array($data['ord_status'], array(2,3)) == false AND $data['payment_status'] == 1) 
							//Neu dơn hang da hoan thanh hoac da huy thi k dc complete order
							{	 
								if($CMS->vars['negative_sale'] == 1 AND $stock > 0)// Chan ban âm
								{
								// Duyet don hang
								$output .=<<<EOF
								<a  onclick="alertText('{$CMS->lang['store_not_enought']}','warning');"   class="pull-right add_cart_2">{$CMS->lang['update_status_done']}</a>
EOF;
								}
								elseif($CMS->vars['negative_sale'] == 0 AND $stock == 0)// Chan ban âm
								{
								// Duyet don hang
								$output .=<<<EOF
								<a  onclick="alert_confirm_stockcustom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=success&id={$data['ord_id']}&type=all&stock={$stock}', {$stock}, '{$CMS->lang['confirm_complete_order']}</p>');"   class="pull-right add_cart_2">{$CMS->lang['update_status_done']}</a>
EOF;
								}
								else
								{
									// Duyet don hang
								$output .=<<<EOF
								<a  onclick="alert_confirm_stockcustom('{$CMS->vars['root_domain']}/?site=order&act=edit_do&sub_act=success&id={$data['ord_id']}&type=all&stock={$stock}', {$stock}, '{$CMS->lang['store_not_enought']} <p>{$CMS->lang['confirm_complete_order']}</p>');"   class="pull-right add_cart_2">{$CMS->lang['update_status_done']}</a>
EOF;
								}
								
							}

						}

						$output .= <<<EOF
						<a class="pull-right add_cart_2" href="{$CMS->vars['root_domain']}/?site=order&act=rating&id={$data['ord_id']}"><i class="fa  fa-star"></i>{$CMS->lang['order_commission_rating']}</a>
EOF;


		}

					$output .=<<<EOF
		</section>

 <div id="box_check_stock" class="popup_check_stock mfp-hide" style="clear: both; overflow: hidden;">
            <p class="" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['stock_info']}</p>
               
            	<section class="add_table">		
	                <div class="table_cus" style="overflow-x: initial;">
							<div class="table-responsive">
								<table width="100%">
									<thead>
										<tr>
										
											 
							 				<th scope="col" width="2%">{$CMS->lang['store_name']}</th>
											<th scope="col" width="15%">{$CMS->lang['inventory']}</th>
											<th scope="col" width="15%">{$CMS->lang['link']}</th>
										</tr>
									</thead>
									 <tbody id="tbody_order_stock">

	                  				  </tbody>

								</table>
						</div>
					</div>	
				</section>						
                <div class="row" id="btn_boxstock_detail" style="text-align:center">
                </div>
 </div>    

{$this->preview_popup()}
{$this->getExportFiles()}
{$CMS->global->sendEmailPopup(1)}

<script type="text/javascript" src="/acp/jsacp/custom_transaction.js?20180312"></script>
<script src="{$CMS->vars['js_acp']}/order_stock.js"></script>

EOF;

		return $output;
	}

    
    /**
     * Refund order
     * @param array $data
     * @return string
     */

	public function refund_order($data=array())
	{
		global $CMS;
		
		$order_total_show = "$ ".number_format($data['ord_total'],2);
		$order_name = "<a href='{$CMS->vars[root_domain]}/?site=order&act=show&id={$CMS->input[id]}'>{$data['ord_name']}</a>";
	$output =<<<EOF
	
	<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>{$CMS->lang['order_refund']}</h3>             
            </div>
          </div>
        </div>
    </header>
      
<form method="post" id="refund_order" name="refund_order" action="{$CMS->vars['root_domain']}/?site=order&act=refund_do&id={$CMS->input['id']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

 <section class="card">
      <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3></h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=order{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
      </section>

        <div class="card-block">
          <h5 class="with-border">{$CMS->lang['required_info']}</h5>
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['order_id']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                #{$data['ord_id']}
              </p>
              </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['order_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  {$order_name}
                </p>
                </div>
            </div>  
            
            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['order_total']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  {$order_total_show}
                </p>
                </div>
            </div>
            <div class="form-group row" >
                <label class="col-sm-3 form-control-label">{$CMS->lang['title_enter_money_refund']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                	{$order_total_show}
                  	<!--input type="text" class="form-control" maxlength="6" onkeypress="return check_enter_number(event,this);" name="refund_amount" value="{$data['refund_amount']}" emsg="{$CMS->lang['note_amount_refund']}" placeholder="{$CMS->lang['note_amount_refund']}" /-->
                </p>
                </div>
              </div>

              <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['title_reason_note']}</label>
                  <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                    <textarea name="reason_refund" rows="5" emsg="{$CMS->lang['note_reason_refund']}" placeholder="{$CMS->lang['note_reason_refund']}" class="form-control">{$data['reason_refund']}</textarea>
                  </p>
                  </div>
              </div>

              <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                 <input class="btn btn-rounded" type="submit" name="btn_submit_refund" value=" {$CMS->lang['refund_submit']} "> 
                </p>
              </div>
            </div>

        </div><!-- end card-block -->

  </section><!-- end section card -->
   
</form>
<script language="javascript">rebuild_form("refund_order",1);</script>

EOF;

		return $output;		
	}

    /**
     * Choose product
     * @return string
     */
  
	function chooseProductType()
    {
        $output = <<<EOF
        <div id="choose_product_type" class="popup_edit_product mfp-hide" style="clear: both; overflow: hidden;">
        	<p class="title_add" style="font-weight: bold; text-transform: uppercase; font-size: 18px; text-align: center;">Chọn loại sản phẩm/ dịch vụ</p>

        	<div class="col-md-4">
        	</div>	
        	<div class="col-md-4">
        		<button onclick="changeProductType(0)" class="btn btn-inline btn-primary ladda-button" data-style="expand-right" data-size="xs"><span class="ladda-label">Sản phẩm</span><span class="ladda-spinner"></span><div class="ladda-progress" style="width: 0px;"></div></button>

        		<button onclick="changeProductType(1)" class="btn btn-inline btn-primary ladda-button" data-style="expand-right" data-size="xs"><span class="ladda-label">Dịch vụ</span><span class="ladda-spinner"></span><div class="ladda-progress" style="width: 0px;"></div></button>

        	</div>	
        	<div class="col-md-4">
        		
        	</div>	
        	<div class="col-md-4">
        	</div>	

            
        </div>
EOF;
        return $output;
    }

    /**
     * Invoice preview
     * @param string $data
     * @return string
     */

    public function preview($data = "" ) {
		global $CMS, $DB, $member;
		
		$customer = $CMS->customer->getInfo($data['cus_id']);
		$allItem = $CMS->transactions->getItemAll($data['trx_id'],"all");

		if($data['trx_payment_method'] == 0)
		{
			$trx_payment_method = "Tiền mặt";
		}
		else
		{
			$trx_payment_method = "Ngân hàng";
		}
		$output =<<<EOF
		 <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <style>
    	.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #5D6975;
  text-decoration: underline;
}

body {
  position: relative;
  width: 21cm;  
  height: 29.7cm; 
  margin: 0 auto; 
  color: #001028;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 12px; 
  font-family: Arial;
}

header {
  padding: 10px 0;
  margin-bottom: 30px;
}

#logo {
  text-align: center;
  margin-bottom: 10px;
}

#logo img {
  width: 90px;
}

h1 {
  border-top: 1px solid  #5D6975;
  border-bottom: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 2.4em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 20px 0;
  background: url(dimension.png);
}

#project {
  float: left;
}

#project span {
  
  text-align: left;
  width: 52px;
  margin-right: 35px;
  display: inline-block;
  font-size:13px;
}

#company {
  float: right;
  text-align: right;
}

#project div,
#company div {
  white-space: nowrap;        
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

table tr:nth-child(2n-1) td {
  background: #F5F5F5;
}

table th,
table td {
  text-align: center;
}

table th {
  padding: 10px 20px;
  color: #5D6975;
  border-bottom: 1px solid #C1CED9;
  white-space: nowrap;        
  font-weight: normal;
}

table .service,
table .desc {
  text-align: left;
}

table td {
  padding: 10px;
  text-align: left;
}

table td.service,
table td.desc {
  vertical-align: top;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table td.grand {
  border-top: 1px solid #5D6975;;
}

#notices .notice {
  color: #5D6975;
  font-size: 1.2em;
}

footer {
  color: #5D6975;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 8px 0;
  text-align: center;
}
#container {
  height: 500px;
  width: 600px;
  position: relative;
}
#image {
  position: absolute;
  left: 0;
  top: 0;
}
#text {
  z-index: 100;
  position: absolute;
 
  font-size: 30px;
  font-weight: bold;
  left: 250px;
  top: -50px;
   
  /* Safari */
-webkit-transform: rotate(-50deg);

/* Firefox */
-moz-transform: rotate(-50deg);

/* IE */
-ms-transform: rotate(-50deg);

/* Opera */
-o-transform: rotate(-50deg);

/* Internet Explorer */
filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}
    </style>
  </head>
  <body>
    <header class="clearfix" style="border:1px splid">



     	<div class="card-block invoice clearfix" >

     		<div class="col-lg-6 company-info" style="float:left;width:50%">
							<h2>{$CMS->vars['print_company_name']}</h2>
							<p>{$CMS->vars['print_website']}<br>
								{$CMS->vars['print_company_address']}<br>
								{$CMS->vars['print_company_phone']}
							</p>
							 
						 

			  </div>

			  <div class="text-lg-right clearfix"  style="float:right;  width:50%;">
							<div  >
									<h5 style="text-align:right; font-weight:bold;font-size:14px">Số hóa đơn #{$data['trx_invoice_no_c']}</h5>
									<div style="text-align:right; ;font-size:13px;margin-top:-15px">Ngày lập: {$data['trx_time_c']}</div>
							 </div>


								 
			 	</div>
     	</div>
     	<div class="card-block invoice clearfix" >
     		 <div id="project" style="float:left;width:50%">
	     		 <h4>Khách hàng:</h4>
			        <div><span>Họ tên:</span> {$data['cus_id_c']}</div>
			        <div><span>Địa chỉ: </span> {$customer['cus_address']}</div>
			        <div><span>Số điện thoại:</span> {$customer['cus_phone']}</div>
			        <div><span>Công ty:</span> {$customer['cus_company']}</div>
			       
			      </div>
			      <div id="company" class="clearfix" style="float:right;  width:50%;">
			      	<h4>Thông tin chi tiết</h4>
			  
			        <div>Tổng tiền: <span style="font-weight:bold">{$data['trx_total_c']}</span> </div>
			        <div>Hình thức thanh toán:{$trx_payment_method}</div>
EOF;
			if($data['trx_status'] == 0)
			{
				
				$output .=<<<EOF
				<div style="color:#ff0000;font-weight:bold">
				    Chưa thanh toán
				  </div>

EOF;
			}
			elseif($data['trx_status'] == 1)
			{
				
				$output .=<<<EOF
				<div style="color:#66ff33;font-weight:bold ">
				    Đã thanh toán
				  </div>

EOF;
			}		 
		
				$output .=<<<EOF
			      </div>						 
     	</div>
    </header>
 
     <main>
	      <table style="width: 100%; max-width: 100%;" width="100%" cellspacing="0" cellpadding="55%">
	        <thead>
	         <tr style="background-color: #f8f8f8;padding:10px 5px;">
					<th>STT</th>
					<th>Hàng hóa & dịch vụ</th>
					<th>Mô tả</th>
					<th>Kỳ hạn</th>
				 	<th>Đơn giá</th>
				 	<th>Số lượng</th>
				 	<th>Thuế</th>
					<th>Thành tiền</th>
				 </tr>
	        </thead>
	        <tbody>
EOF;

					$data_trx_amount = $CMS->class->input->currency($data['trx_amount']);		
					$data_trx_total = $CMS->class->input->currency($data['trx_total']);							
	
									$i = 1;
									foreach ($allItem as $key => $value) {
										# code...
										if($value['tri_cycle_type'] == 0)
										{
											$tri_cycle_type = $CMS->lang['gonce'];

										}
										elseif($value['tri_cycle_type'] == 1)
										{
											$tri_cycle_type = "{$value['tri_cycle']} {$CMS->lang['gmonth']}";

										}elseif($value['tri_cycle_type'] == 2)
										{
											$tri_cycle_type = "{$value['tri_cycle']} {$CMS->lang['gyear']}";

										}
										$tri_amount = $CMS->class->input->currency($value['tri_total']);
										$tri_total =  $CMS->class->input->currency($value['tri_total']+ ($value['tri_total'] * $value['tri_tax'] /100 ));
										$output .=<<<EOF

										<tr style="padding-top:5px">
											<td >{$i}</td>
											<td>{$value['tri_name']}</td>
											<td>{$value['tri_description']}</td>
											<td>{$tri_cycle_type}</td>
											<td>{$tri_amount}</td>
											<td>{$value['tri_quantity']}</td>
											<td>{$value['tri_tax']} %</td>
											<td>{$tri_total}</td>
										</tr>
EOF;
										 $i++;
									}
									$output .=<<<EOF
	          <tr>
	            <td colspan="7" style="text-align:right">Thành tiền:</td>
	            <td class="total">{$data_trx_amount}</td>	 
	          </tr> 
EOF;
				if($data['trx_discount_value'] > 0)
				{
					$output .=<<<EOF
	           <tr>
	            <td colspan="7" style="text-align:right">Chiết khấu</td>
	            <td class="total">{$data['trx_discount_value_c']}</td>
	          </tr>
EOF;
				}
				$output .=<<<EOF
	        
	          <tr>
	            <td colspan="7" style="text-align:right">Thuế: </td>
	            <td class=" total">{$data['trx_tax_c']}</td>
	          </tr>
	           <tr>
	            <td colspan="7" style="text-align:right">Tổng tiền: </td>
	            <td class=" total">{$data_trx_total}</td>
	          </tr>
	        </tbody>
	      </table>
	      
	      <div id="notices">
	        <div>NOTICE:</div>
	        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
	      </div>
	    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>
EOF;
		return $output;
	}

	   function attachFilesShow($data = [])
    {
        global $CMS;

        $icons = $CMS->transactions->file_icons;

        $files = $CMS->attach->get_array("transaction", $data['trx_id']);


        if(!$files) return "";

        $output = <<<EOF
                <div class="box-typical box-typical-padding border" style="padding: 0px 15px !important;">
                    <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['attach_files']}</label>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <div class="box-typical-upload box-typical-upload-in">
                            <ul class="uploading-list list_upload">
EOF;



        foreach ($files as $file)
        {
            $file['attach_ext'] = strtolower($file['attach_ext']);
            $icon = isset($icons[$file['attach_ext']]) ? $icons[$file['attach_ext']] : $icons['default'];

            $output .= <<<EOF
            <li>
                <a href="{$file['attach_location']}" target="_blank">
                    <div class="uploading-list-item-wrapper">
                        <div class="uploading-list-item-name">
                            <i class="{$icon}"></i>
                            {$file['attach_name']}
                        </div>
                        <div class="uploading-list-item-size">{$CMS->class->input->formatSizeUnits($file['attach_size'])}</div>
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </div>
                </a>
            </li>
EOF;

        }

        $output .= <<<EOF
                            </ul>
                        </div>
                     </div>
                </div>
            </div>
EOF;
        return $output;
    }
	 function preview_popup()
    {
        global $CMS;

        $output  = <<<EOF
        <!-- POPUP preview -->
        <div id="box_preview_invoice" class="mfp-hide" style="clear: both; overflow: hidden;height:50%"  >
  <p class="title_add title_change_cus" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['preview_invoice']}</p>
  	<div class="pdfContent" data-dojo-attach-point="_pdfContent" style="width:100%;" height="100%">
  		<iframe src="" width="100%" height="400px" frameborder="0"></iframe>
    </div>

  		<ul class="list_field_supplier">
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <fieldset class="form-group">
                            <div class="typeahead-field"> 
                                <span class="typeahead-query change_action_cus" style="float:right"><a target="_blank" id="print_trx" href="" width="100%" height="400px" frameborder="0" class="btn btn_add_cus" type="button" style="float:right" >{$CMS->lang['print_invoice']}</a></span>
                            </div>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query change_action_cus" style="float:right"><a target="_blank" id="print_trx" href="" width="100%" height="400px" frameborder="0" class="btn btn_add_cus" type="button" style="float:right" >{$CMS->lang['print_invoice']}</a></span>
                            </div>
                        </fieldset>
                    </li>
                
                </ul>
  </div>


<!-- POPUP preview model-->
<div id="box_preview_model" class="modal fade">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 style="float:left" class="modal-title">Preview</h5>
        <button style="float:right" type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="pdfContent" data-dojo-attach-point="_pdfContent" style="width:100%;" height="100%">
            <iframe src="" width="100%" height="400px" frameborder="0"></iframe>
        </div>
      </div>
      <div class="modal-footer">
        <div class="rows">
            <div class="col-md-6 col-sm-6">
                <form method="post" id="sendEmailInv">
                    <div class="form-group">
                        <div class="input-group">
                            <input id="email_receive" class="form-control " type="text" name="email_receive"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_cus_email_err']}" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}" value="">
                            <input type="hidden" id="trx_id" value="">
                            <input type="hidden" id="preview_type" value="default">
                            <div id="sendEmailInvBtn" style="cursor: pointer" class="input-group-addon">
                                Send email <img style="display:none; height: 60%" id="sendEmailLoading" src="/acp/images/fb-loading.gif">
                        </div>
                        </div>
                    </div>
                </form>
                <script>
                     $(document).ready(function(){
                        validate_form_custom2("#sendEmailInv", function(){return sendEmailInv()},"#sendEmailInvBtn");
                    });
                </script>
            </div>
        </div>
        
        <button type="button" class="btn btn-primary" onclick="printTrxPopup()">Print</button>
        <button type="button" class="btn btn-warning" onclick="backToPreviewTrxPopup()">Back</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- POPUP compose -->
<div id="box_compose_invoice" class="modal fade">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 style="float:left" class="modal-title">{$CMS->lang['compose']}</h5>
        <button style="float:right" type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <section class="tabs-section" id="previewTabs">
				<div class="tabs-section-nav">
					<div class="tbl">
						<ul class="nav" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" href="#tabs-2-tab-1" role="tab" data-toggle="tab" onclick="changeComposeTrxMode('default')">
									<span class="nav-link-in">
										{$CMS->lang['default_invoice']}
									</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#tabs-2-tab-2" role="tab" data-toggle="tab" onclick="changeComposeTrxMode('commercial')">
									<span class="nav-link-in">
										{$CMS->lang['commercial_invoice']}
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div><!--.tabs-section-nav-->
			</section>
        <textarea class="editor_texarea" id="invoice_content"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="previewTrxPopup()">{$CMS->lang['create_pdf']} & {$CMS->lang['preview']}<img style="display:none" id="previewTrxPopupLoading" src="/acp/images/fb-loading.gif"></button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$CMS->lang['close']}</button>
      </div>
    </div>
  </div>
</div>
EOF;

        return $output;

    }

    public function getExportFiles()
    {
        $output = <<<EOF
<!-- Modal -->
<div class="modal fade" id="getExportFiles" tabindex="-1" role="dialog" aria-labelledby="getExportFilesTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="getExportFiles" style="float:left">Attachments</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float:right">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <table id="getExportFilesTable" class="table table-striped table-bordered dt-responsive nowrap rows" cellspacing="0" width="100%">
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
EOF;
        return $output;
    }
}