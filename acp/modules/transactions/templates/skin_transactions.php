<?php

use lib\date;

class skin_transactions {

    /**
     * Transaction header
     * @return string
     */

    public function head() {
        global $CMS, $DB, $member;

        // Check enable kho hang
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

        // Array to check define
        $input_filter = ["sub", "trx_status", "trx_method", "trx_reference_no", "trx_invoice_no", "cus_id", "trx_total_from", "trx_total_to", "sort", "order_by", "order", "by", "stats"];

        foreach ( $input_filter as $key )
        {
            $CMS->input[$key] = isset($CMS->input[$key]) ? $CMS->input[$key] : "";
        }

        // Stats to check define
        $stats_filter = ["estimate", "unbill", "overdue", "inv", "paid"];
        $stats_active = [];

        foreach ( $stats_filter as $key )
        {
            $stats_active[$key] = isset($stats_active[$key]) ? $stats_active[$key] : "";
        }

        // Trx_method
        $method_selected[0] = "";
        $method_selected[1] = "";

        // Check selected
        $CMS->input['trx_status'] ? $stt_selected[$CMS->input['trx_status']] = 'selected' : "";
        $CMS->input['sub'] ? $subtype_selected[$CMS->input['sub']] = "selected" : "";
        $CMS->input['trx_method'] == 1 ? $method_selected[$CMS->input['trx_method']] = "selected" : "";


        // Continue
        $option_trx_s_list = "<option value='1-0'>{$CMS->lang['select']}</option>";
        for ($i = 1; $i <= 4; $i++) {
            if ( $i == $CMS->input['sub']) {
                $option_trx_s_list .= "<option value='1-{$i}' selected >{$CMS->lang['trx_subtype_0'.$i]}</option>";
            } else {
                $option_trx_s_list .= "<option value='1-{$i}' >{$CMS->lang['trx_subtype_0'.$i]}</option>";
            }
        }
        $out = '';

        if (!empty($_SESSION['trx_error'])) {
            $out .= <<<EOF
<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
	<strong>{$CMS->lang['error']}</strong><br>
EOF;
            if (is_array($_SESSION['trx_error'])) {
                $out .= <<<EOF
		<ul>
EOF;
                foreach ($_SESSION['trx_error'] as $trx) {
                    $out .= <<<EOF
			<li>{$trx}</li>
EOF;
                }
                $out .= <<<EOF
		</ul>
EOF;
            } else {
                $out .= <<<EOF
			<span>{$_SESSION['trx_error']}</span>
EOF;
            }
            $out .= <<<EOF
</div>
EOF;
            unset($_SESSION['trx_error']);
        }

        if (isset($_SESSION['trx_success'])) {
            $out .= <<<EOF
<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
	<strong>{$CMS->lang['success']}</strong><br>
EOF;
            if (is_array($_SESSION['trx_success'])) {
                $out .= <<<EOF
		<ul>
EOF;
                foreach ($_SESSION['trx_success'] as $trx) {
                    $out .= <<<EOF
			<li>{$trx}</li>
EOF;
                }
                $out .= <<<EOF
		</ul>
EOF;
            } else {
                $out .= <<<EOF
			<span>{$_SESSION['trx_success']}</span>
EOF;
            }
            $out .= <<<EOF
</div>
EOF;
            unset($_SESSION['trx_success']);
        }

        $CMS->input['date_from'] = isset($CMS->input['date_from']) ? urldecode($CMS->input['date_from']) : "";
        $CMS->input['date_to'] = isset($CMS->input['date_to']) ? urldecode($CMS->input['date_to']) : "";

        $stats_filter = ['all', 'estimate', 'unbill', 'overdue', 'inv', 'paid'];

        if(isset($CMS->input['stats']) && in_array($CMS->input['stats'], $stats_filter))
        {
            $stats_active[$CMS->input['stats']] = 'active';
        }
        else
        {
            $stats_active['all'] = 'active';
        }

        $out .= <<<EOF
<script type='text/javascript' src='{$CMS->vars['js_acp']}/custom_transaction.js?20180312'></script>
<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['trx_type_'.$CMS->input['type']]}</h3>
		<figure class="pull-right right">
			<div class="search">
				<form method="get"  action="{$CMS->vars['root_domain']}/" style="display:inline-block">
						<input type="submit" class="fa-input" value="&#xf002;">
						<input type="hidden" value="transactions" name="site">
						<input type="hidden" value="search" name="act">
						<input type="hidden" value="{$CMS->input['type']}" name="type">
						<input type="text" name="keyword" id="p_quick_search" placeholder="{$CMS->lang['gsearch_quick']}" autocomplete="off">
						<div id="suggesstion-box" class="box_result_find" style="display:none"></div>
                        <script>autocomplete_quick_search("{$CMS->input['type']}")</script>
				</form>
				<a onclick="$('#formsearch_adv').toggle()" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>

			 </div>
			 {$CMS->global->importExportData('transactions', "&type={$CMS->input['type']}", 0,"{$CMS->vars['root_domain']}/?subact=download&mod=export_sample&module={$CMS->input['site']}")}
	 		<div class="btn-group">
				<button type="button"
						class="btn  dropdown-toggle"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
					{$CMS->lang['create_transaction_'.intval($CMS->input['type'])]}
				</button>
				<div class="dropdown-menu">
EOF;

        foreach ($CMS->transactions->action_list_by_type[intval($CMS->input['type'])] as $type_action => $sub_action)
        {
            $out .= <<<EOF
					<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}&sub={$sub_action}&act=add"><span class="font-icon"></span>{$CMS->lang['trx_subtype_0'.$sub_action]}</a>
EOF;
        }

        $out .= <<<EOF
				</div>
			</div>
		</figure>
	</figure>

   <section class="search_adv" >
		<form method="get" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/" >
		    <input type="hidden" name="type" value="{$CMS->input['type']}">
		    <input type="hidden" name="site" value="transactions">
			<figure class="box-typical box-typical box-typical-padding border">
					<h5>{$CMS->lang['gsearch_advance']}</h5>
					<ul class="input_li row match-height">
EOF;

        if(\lib\security::checkPermission(\lib\input::get('site'), 'all_branches')) {
            $out .= <<<EOF
                        <li class="col-xl-3 col-md-3 col-sm-6" style="display:{$addon_goods_enable}">
                            <select name="store_id" id="store_id" class="form-control select2" defaultvalue="{$CMS->input['store_id']}">
                                {$CMS->store->get_list_store($CMS->input['store_id'],1)}
                            </select>
                        </li>
EOF;
        }

        $out .= <<<EOF
						<li class="col-xl-3 col-md-3 col-sm-6">
                            <select name="sub" id="sub" class="form-control select2" defaultvalue="{$CMS->input['sub']}">
                                <option value="">-- Chọn loại --</option>
EOF;

                            foreach ($CMS->transactions->action_list_by_type[$CMS->input['type']] as $subtype)
                            {
                                $out .= "<option value='{$subtype}' ".(isset($subtype_selected[$subtype]) ? $subtype_selected[$subtype] : "").">{$CMS->lang['trx_subtype_0'.$subtype]}</option>";
                            }

        $out .= <<<EOF
                            </select>
						</li>
						<li class="col-xl-3 col-md-3 col-sm-6">
							
                            <select name="trx_status" id="trx_status" class="form-control select2" defaultvalue="{$CMS->input['trx_status']}">
                                <option value="">-- Trạng thái --</option>
EOF;

                    foreach ($CMS->transactions->list_status as $stt)
                    {
                        $out .= "<option value='{$stt}'".(isset($stt_selected[$stt]) ? $stt_selected[$stt] : "").">{$CMS->lang['trx_status_0'.$stt]}</option>";
                    }

        $out .= <<<EOF
                                </select>
                            </li>

                        <li class="col-xl-3 col-md-3 col-sm-6">
								<select name="trx_method"  class="form-control select2">
									<option value="">-- Thanh toán --</option>
									<option $method_selected[0] value="0">Tiền mặt</option>
									<option $method_selected[1] value="1">Chuyển khoản</option>
								</select>
						</li>


						<li class="col-xl-3 col-md-3 col-sm-6">
								<input  name="trx_reference_no" id="trx_reference_no" type="text" class="form-control" placeholder="{$CMS->lang['trx_reference_no']}" value="{$CMS->input['trx_reference_no']}" > 
						</li>
						
						<li class="col-xl-3 col-md-3 col-sm-6">
								<input  name="trx_invoice_no" id="trx_invoice_no" type="text" class="form-control" placeholder="{$CMS->lang['table_inv_code']}" value="{$CMS->input['trx_invoice_no']}" > 
						</li>
						
						<li class="col-xl-3 col-md-3 col-sm-6">
							<input  name="cus_id" id="cus_id" type="text" class="form-control" placeholder="{$CMS->lang['trx_cus_id']}" value="{$CMS->input['cus_id']}" > 
						</li>

					    
						<li class="col-xl-3 col-md-3 col-sm-6">
								<input  name="trx_total_from" id="trx_total_from" type="text" class="form-control" placeholder="{$CMS->lang['title_total_from']}" value="{$CMS->input['trx_total_from']}" > 
						</li>
						
						<li class="col-xl-3 col-md-3 col-sm-6">
								<input  name="trx_total_to" id="trx_total_to" type="text" class="form-control" placeholder="{$CMS->lang['title_total_to']}" value="{$CMS->input['trx_total_to']}" > 
						</li>
						
						
						 
						<li class="col-xl-3 col-md-3 col-sm-6">
							 
								<div class="input-group date datetimepicker-1">
									<input type="text" name="date_from" placeholder="{$CMS->lang['title_date_from']}" value="{$CMS->input['date_from']}" class="form-control">
								<span class="input-group-addon">
									<i class="font-icon font-icon-calend"></i>
								</span>
							 
						</li>

						 <li class="col-xl-3 col-md-3 col-sm-6">
							 
								<div class="input-group date datetimepicker-1">
									<input type="text" name="date_to" placeholder="{$CMS->lang['title_date_to']}" class="form-control" value="{$CMS->input['date_to']}">
								<span class="input-group-addon">
									<i class="font-icon font-icon-calend"></i>
								</span>
							 
						</li>

						 <li class="col-xl-4 col-md-4 col-sm-6">
							 
								<input type="submit" value="{$CMS->lang['comment_filter']}" name="is_submit">
								<!--<input class="btn btn-success" type="button" value="{$CMS->lang['export_data']}">-->
								<input type="reset" class="btn btn-danger" value="{$CMS->lang['clear_btn']}" onclick="refreshForm($('#formsearch_adv'));">
								<input type="hidden" value="{$CMS->input['sort']}" name="sort" />
								<input type="hidden" value="{$CMS->input['order_by']}" name="order_by" /> 	
						 
						</li>

					</ul>
			</figure>	
		 </form>	
		  
    </section>
EOF;

        if(isset($CMS->input['is_submit']))
        {
            $out .= <<<EOF
        <script>
            $(document).ready(function() {
                $("#formsearch_adv").show();
            })
        </script>
EOF;

        }

        $stats = $CMS->transactions->stats_default_page();

        $estimate_tab_hide = $CMS->input['type']==1 ? "" : "display: none";

        $url_params = ['date_from', 'date_to', 'keyword'];

        $href_options = "";

        foreach ($CMS->input as $k => $v)
        {
            if($v!="")
            {
                if(in_array($k, $url_params))
                {
                    $href_options .= "&{$k}=".urldecode($v);
                }
            }
        }

        $out.= <<<EOF
<div class="row">
				<div class="col-lg-12">
					<section class="tabs-section">
						<div class="tabs-section-nav tabs-section-nav-data">
							<div class="tbl">
								<ul class="nav" role="tablist">
									<li class="nav-item">
										<a class="nav-link {$stats_active['all']}" href="{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}&sub={$CMS->input['sub']}&stats=all{$href_options}" role="tab">
											<span class="nav-link-in">
												<span class="number">{$CMS->lang['trx_tab_all']}</span>
												<span class="percent color-blue">&nbsp;</span>
												<span class="title">{$CMS->class->input->number($stats['all']['cnt'])}</span>
											</span>
										</a>
									</li>
									<li class="nav-item" style="{$estimate_tab_hide}">
										<a class="nav-link {$stats_active['estimate']}" href="{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}&sub={$CMS->input['sub']}&stats=estimate{$href_options}" role="tab">
											<span class="nav-link-in">
												<span class="number">{$CMS->lang['trx_tab_estimate']}</span>
												<span class="percent">{$CMS->class->input->currency($stats['estimate']['total'])}</span>
												<span class="title">{$CMS->class->input->number($stats['estimate']['cnt'])}</span>
											</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link {$stats_active['unbill']}" href="{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}&sub={$CMS->input['sub']}&stats=unbill{$href_options}" role="tab">
											<span class="nav-link-in">
												<span class="number">{$CMS->lang['trx_tab_unpaid']}</span>
												<span class="percent color-yellow">{$CMS->class->input->currency($stats['unbill']['total'])}</span>
												<span class="title">{$CMS->class->input->number($stats['unbill']['cnt'])}</span>
											</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link {$stats_active['overdue']}" href="{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}&sub={$CMS->input['sub']}&stats=overdue{$href_options}" role="tab">
											<span class="nav-link-in">
												<span class="number">{$CMS->lang['trx_tab_overdue']}</span>
												<span class="percent color-red">{$CMS->class->input->currency($stats['overdue']['total'])}</span>
												<span class="title">{$CMS->class->input->number($stats['overdue']['cnt'])}</span>
											</span>
										</a>
									</li>
									
									<!--
									<li class="nav-item">
										<a class="nav-link {$stats_active['inv']}" href="{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}&sub={$CMS->input['sub']}&stats=inv{$href_options}" role="tab">
											<span class="nav-link-in">
												<span class="number">{$CMS->lang['trx_tab_invoice']}</span>
												<span class="percent color-blue-grey">{$CMS->class->input->currency($stats['invoice']['total'])}</span>
												<span class="title">{$CMS->class->input->number($stats['invoice']['cnt'])}</span>
											</span>
										</a>
									</li>
									-->
									
									<li class="nav-item">
										<a class="nav-link  {$stats_active['paid']}" href="{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}&sub={$CMS->input['sub']}&stats=paid{$href_options}" role="tab">
											<span class="nav-link-in">
												<span class="number">{$CMS->lang['trx_tab_paid']}</span>
												<span class="percent color-green">{$CMS->class->input->currency($stats['paid']['total'])}</span>
												<span class="title">{$CMS->class->input->number($stats['paid']['cnt'])}</span>
											</span>
										</a>
									</li>
								</ul>
							</div>
						</div><!--.tabs-section-nav-->
					</section><!--.tabs-section-->
				</div><!--.col-lg-6-->
			</div>

        <section class="add_table">
			<div class="data_table">
                <div class="table table_cus table-responsive">
EOF;

        if($_SESSION['is_mobile'] == true)
        {
            $out .= <<<EOF
            <table id="example" cellspacing="0" width="100%">		
			 	  <thead>
					<tr>
                        <th width="2%" data-orderable="false" data-sortable="false"></th>
EOF;

        }
        else
        {
            $out .= <<<EOF
            <table cellspacing="0" width="100%">		
			 	  <thead>
					<tr>
EOF;
        }

        $out.= <<<EOF
						<th width="5%" data-orderable="true">ID</th>
						<th width="15%" data-orderable="true">{$CMS->lang['trx_items']}</th>
						<th width="9%" data-orderable="false">{$CMS->lang['trx_type']}</th>
						<th width="9%" data-orderable="false">{$CMS->lang['trx_no']}</th>
						<th width="9%" data-orderable="false">{$CMS->lang['cus_type']}</th>
						<th width="9%" data-orderable="true">{$CMS->lang['trx_due']}</th>
						<th width="9%" data-orderable="true">{$CMS->lang['trx_remain']}</th>
						<th width="9%" data-orderable="true">{$CMS->lang['trx_total_tax']}</th>
						<th width="9%" data-orderable="false">{$CMS->lang['trx_status']}</th>
						<th width="5%" data-orderable="false"></th>
					</tr>
				</thead>
				<tbody>
EOF;
        return $out;
    }
    public function foot() {
        global $CMS, $DB, $member;

        $this->html_form = $CMS->class->template->load_template("skin_transactions_form");

        $out = <<<EOF
                        </tbody>
                    </table>
				</div>
			</div>
		</section>
		<div class="block_bottom pagination pagination-sm">
			{$CMS->transactions->show_page}
		</div>
		 
</section>		

{$this->html_form->preview_popup()}
{$this->html_form->getExportFiles()}

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
        {$CMS->global->sendEmailPopup()}
  <script src="{$CMS->vars['js_acp']}/transaction.js"></script>
<script language="javascript">arrange_setup("{$CMS->transactions->arrange_data}");</script>
<script>
/*
$('.table-responsive').on('show.bs.dropdown', function () {
     $('.table-responsive').css( "overflow", "inherit" );
});

$('.table-responsive').on('hide.bs.dropdown', function () {
     $('.table-responsive').css( "overflow", "auto" );
})
*/
</script>
EOF;
        return $out;
    }
    public function mid($data = null) {
        global $CMS, $DB, $member;
 
        $link_add_sub_2 = $show = $link_edit = $url_delete = '';

        switch ($data['trx_subtype']) {
            case 1:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                break;
            case 2:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "	
<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                break;
            case 3:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                break;
            case 4:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>
";
                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                break;
            case 5:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                if ($data['trx_status'] ==0) {
                    $link_add_sub_2 = " <a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=add&invoice={$data['trx_id']}'>{$CMS->lang['trx_add_7']}</a>";
                }
                break;
            case 6:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                break;
            case 7:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                break;
            case 8:
                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=8&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
                $link_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=8&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=8&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
                break;
            default:
                $show = "";
                $url_edit = "<a href='{$CMS->vars['root_domain']}/?site=transactions&act=sales'   class='edit'><i class='fa fa-edit'></i></a>";
                $url_delete = "{$CMS->vars['root_domain']}/?site=transactions&act=sales";
                break;
        }

        $highlight = isset($_SESSION['highlight']['trx']) && $_SESSION['highlight']['trx'] == $data['trx_id'] ? "highlight_row" : "";

        unset( $_SESSION['highlight']['trx']);

        if($data['cus_type'] == 1)
        {
            $cus_info = $CMS->customer->getInfo($data['cus_id']);

            $cus_type_name = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$cus_info['cus_id']}'>{$cus_info['cus_full_name']}</a>";
        }
        else if($data['cus_type'] == 2)
        {
            $supplier_info = $CMS->supplier->get_info($data['supplier_id']);
            $cus_type_name = "<a href='{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$supplier_info['supplier_id']}'>{$supplier_info['supplier_name']}</a>";
        }
        else if($data['cus_type'] == 3)
        {
            $assign_info = $CMS->user->get_info($data['user_assign']);
            $cus_type_name = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$assign_info['user_id']}'>{$assign_info['user_display_name']}</a>";
        }

        $out = <<<EOF
					<tr class="{$highlight}">
EOF;

        if($_SESSION['is_mobile'] == true)
        {
            $out .=<<<EOF
				<td></td>
EOF;
        }

        $out .= <<<EOF
						<td>
							{$show}
						</td>
						<td>
							{$CMS->transactions->showItemsListing($data)}
						</td>
						<td>
							{$data['trx_subtype_c']}
						</td>
						<td>
EOF;

        if($data['invoiceInfo'])
        {
            foreach($data['invoiceInfo'] as $invoiceInfo)
            {
                $invoiceInfo = $CMS->transactions->convertvalue($invoiceInfo);
                $out .= "<div><a href='{$CMS->vars['root_domain']}/?site=transactions&act=show&type={$invoiceInfo['trx_type']}&id={$invoiceInfo['trx_id']}'>{$invoiceInfo['trx_invoice_no_c']}</a></div>";
            }
        }
        else
        {
            $out .= $data['trx_invoice_no_c'];
        }

        $out .= <<<EOF
						</td>
						<td>
							{$cus_type_name}
						</td>
						<td style="{$data['style_color']}">
							{$data['trx_due_c']}
						</td>
						<td>
							{$data['trx_remain_c']}
						</td>
						<td>
							{$data['trx_total_c']}
						</td>
						<td>
							{$data['trx_status_c']}
						</td>
						<td align="center">
							{$CMS->transactions->action_html($data)}
EOF;



//        if($CMS->permit['transactions_edit'] == 1 && $data['trx_status']<=2)
        {
            $out .=<<<EOF

            {$link_edit}
EOF;

        }
        /*if($CMS->permit['transactions_edit'] == 1)
        {
            $out .=<<<EOF

            {$url_delete}
EOF;

        }*/
        $out .=<<<EOF

						 
						</td>
					</tr>
EOF;
        return $out;
    }
    public function none() {
        global $CMS, $DB, $member;
        $out = <<<EOF
					<tr>
						<td colspan="11">
							<center><h5>{$CMS->lang['not_data']}</h5></center>
						</td>
					</tr>
EOF;
        return $out;
    }

    public function add($form_html) {
        global $CMS, $DB, $member;
        switch ($CMS->input['sub']) {
            case 1:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_1'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_1'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 2:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_2'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_2'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&invoice={$CMS->input['invoice']}&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 3:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_3'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_3'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 4:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_4'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_4'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 5:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_5'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_5'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 6:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_6'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_6'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 7:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_7'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_7'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&invoice={$CMS->input['invoice']}&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 8:
                $data['add_text'] = $add_text = $CMS->lang['trx_add_8'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_8'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=8&act=add";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            default:
                break;
        }
        $data_info = array();
        if (isset($CMS->input['invoice']) && Validate::isNum($CMS->input['invoice'])) {
            $data['data_info'] = $data_info = $CMS->transactions->getInfo($CMS->input['invoice']);
//            print_r($data_info); exit;
            $CMS->input['cus_type'] = $data_info ? $data_info['cus_type'] : 1;
            $CMS->input['trx_email'] = $CMS->input['trx_email'] ? $CMS->input['trx_email'] : $data_info['cus_email'];

            if($CMS->input['cus_type'] == 1) //KH
            {
                $CMS->input['cus_id'] = $CMS->input['cus_id'] ? $CMS->input['cus_id'] : $data_info['cus_id'];

                $cus_info = $CMS->customer->getInfo($CMS->input['cus_id']);

                if($cus_info)
                {
                    $CMS->input['trx_cus'] = $CMS->input['trx_cus'] ? $CMS->input['trx_cus'] : $cus_info['cus_full_name'];

                    $data['cus_info'] = $cus_info;
                }
            }
            elseif ($CMS->input['cus_type'] == 2) //NCC
            {
                $CMS->input['supplier_id'] = $CMS->input['supplier_id'] ? $CMS->input['supplier_id'] : $data_info['supplier_id'];

                $supplier_info = $CMS->supplier->get_info($CMS->input['supplier_id']);

                if($supplier_info)
                {
                    $data['supplier_info'] = $supplier_info;
                }
            }
            elseif ($CMS->input['cus_type'] == 3) //NV
            {
                $CMS->input['user_assign'] = $CMS->input['user_assign'] ? $CMS->input['user_assign'] : $data_info['user_assign'];

                $assign_info = $CMS->user->get_info($CMS->input['user_assign']);

                if($assign_info)
                {
                    $CMS->input['user_name_assign'] = $CMS->input['user_name_assign'] ? $CMS->input['user_name_assign'] : $assign_info['user_display_name'];

                    $data['assign_info'] = $assign_info;
                }
            }

        }

        $dataFormat = str_replace(['YYYY','MM','DD'],['Y','m','d'],$CMS->vars['date_format']);

        $data['data_info']['cus'] = $data_info['cus'] = $CMS->customer->getInfo($CMS->input['cus_id']);
        $data['cus_id'] =  $trx_cus = isset($CMS->input['cus_id']) ? $CMS->input['cus_id'] : $data_info['cus']['cus_id'];
        $data['trx_cus'] = $trx_cus = isset($CMS->input['trx_cus']) ? $CMS->input['trx_cus'] : $data_info['cus']['cus_full_name'];
        $data['trx_email'] = $trx_email = isset($CMS->input['trx_email']) ? $CMS->input['trx_email'] : $data_info['cus']['cus_email'];
        $data['trx_address'] = $trx_address = isset($CMS->input['trx_address']) ? $CMS->input['trx_address'] : $data_info['cus']['cus_address'];
        $data['trx_method'] = $trx_method = isset($CMS->input['trx_method']) ? $CMS->input['trx_method'] : 0;
        $data['trx_estimate_status'] = $trx_estimate_status = isset($CMS->input['trx_estimate_status']) ? $CMS->input['trx_estimate_status'] : 0;
        $data['trx_status'] = $trx_status = isset($CMS->input['trx_status']) ? $CMS->input['trx_status'] : 0;
        $data['trx_account'] = $trx_account = isset($CMS->input['trx_account']) ? $CMS->input['trx_account'] : '';
        $data['trx_terms'] = $trx_terms = isset($CMS->input['trx_terms']) ? $CMS->input['trx_terms'] : 0;
        $data['trx_payment_date'] = $trx_payment_date = isset($CMS->input['trx_payment_date']) ? $CMS->input['trx_payment_date'] : date($dataFormat);
        $data['trx_accepted_date'] = $trx_payment_date = isset($CMS->input['trx_accepted_date']) ? $CMS->input['trx_paytrx_accepted_datement_date'] : date($dataFormat);
        $data['trx_total'] = $trx_total = isset($CMS->input['trx_total']) ? $CMS->input['trx_total'] : '';
        $data['trx_contract_code'] = $trx_contract_code = isset($CMS->input['trx_contract_code']) ? $CMS->input['trx_contract_code'] : '';
        $data['trx_note'] = $trx_note = isset($CMS->input['trx_note']) ? $CMS->input['trx_note'] : '';
        $data['trx_msg'] = $trx_msg = isset($CMS->input['trx_msg']) ? $CMS->input['trx_msg'] : '';
        $data['trx_reference_no'] = $trx_reference_no = isset($CMS->input['trx_reference_no']) ? $CMS->input['trx_reference_no'] : '';
        $data['trx_expiration_date'] = $trx_expiration_date = isset($CMS->input['trx_expiration_date']) ? $CMS->input['trx_expiration_date'] : date($dataFormat);
        $data['trx_estimate_date'] = $trx_estimate_date = isset($CMS->input['trx_estimate_date']) ? $CMS->input['trx_estimate_date'] : date($dataFormat);
        $data['trx_due_date'] = $trx_estimate_date = isset($CMS->input['trx_due_date']) ? $CMS->input['trx_due_date'] : date($dataFormat);
        $data['trx_credit_memo_date'] = $trx_credit_memo_date = isset($CMS->input['trx_credit_memo_date']) ? $CMS->input['trx_credit_memo_date'] : date($dataFormat);
        $data['cus_type'] = $cus_type = isset($CMS->input['cus_type']) ? $CMS->input['cus_type'] : 1;
        $data['store_id'] = \lib\input::get('store_id') * 1;
        $option_trx_method = "";
        for ($i = 0; $i <= 5; $i++) {
            if ($i == $trx_method) {
                $option_trx_method .= "<option value='{$i}' selected>{$CMS->lang['trx_method_0'.$i]}</option>";
            } else {
                $option_trx_method .= "<option value='{$i}'>{$CMS->lang['trx_method_0'.$i]}</option>";
            }
        }
        $data['option_trx_method'] = $option_trx_method;

        $est_status = [0,5,3,4];

        $option_trx_estimate_status = "";
        foreach ($est_status as $i) {
            if ($i == $trx_status) {
                $option_trx_estimate_status .= "<option value='{$i}' selected>{$CMS->lang['trx_status_0'.$i]}</option>";
            } else {
                $option_trx_estimate_status .= "<option value='{$i}'>{$CMS->lang['trx_status_0'.$i]}</option>";
            }
        }
        $data['option_trx_estimate_status'] = $option_trx_estimate_status;


        $option_trx_status = "";
        for ($i = 0; $i <= 3; $i++) {
            if ($i == $trx_status) {
                $option_trx_status .= "<option value='{$i}' selected>{$CMS->lang['trx_status_0'.$i]}</option>";
            } else {
                $option_trx_status .= "<option value='{$i}'>{$CMS->lang['trx_status_0'.$i]}</option>";
            }
        }
        $data['option_trx_status'] = $option_trx_status;


        $account = $CMS->accounts->getAll(' accounts_status=1 AND ');
        $option_trx_account = "<option balance='{$CMS->class->input->currency(0)}' value='0'>{$CMS->lang['select']}</option>";
        foreach ($account as $a) {
            if ($a['accounts_id'] == $trx_account) {
                $option_trx_account .= "<option balance='{$CMS->class->input->currency($a['accounts_balance'])}' value='{$a['accounts_id']}' selected>{$a['accounts_name']}</option>";
            } else {
                $option_trx_account .= "<option balance='{$CMS->class->input->currency($a['accounts_balance'])}' value='{$a['accounts_id']}' >{$a['accounts_name']}</option>";
            }
        }
        $data['option_trx_account'] = $option_trx_account;

        $out = '';
        if (!empty($_SESSION['trx_error'])) {
            $out .= <<<EOF
<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
	<strong>{$CMS->lang['error']}</strong><br>
EOF;
            if (is_array($_SESSION['trx_error'])) {
                $out .= <<<EOF
		<ul>
EOF;
                foreach ($_SESSION['trx_error'] as $trx) {
                    $out .= <<<EOF
			<li>{$trx}</li>
EOF;
                }
                $out .= <<<EOF
		</ul>
EOF;
            } else {
                $out .= <<<EOF
			<span>{$_SESSION['trx_error']}</span>
EOF;
            }
            $out .= <<<EOF
</div>
EOF;
            unset($_SESSION['trx_error']);
        }
        if (isset($_SESSION['trx_success'])) {
            $out .= <<<EOF
<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
	<strong>{$CMS->lang['success']}</strong><br>
EOF;
            if (is_array($_SESSION['trx_success'])) {
                $out .= <<<EOF
		<ul>
EOF;
                foreach ($_SESSION['trx_success'] as $trx) {
                    $out .= <<<EOF
			<li>{$trx}</li>
EOF;
                }
                $out .= <<<EOF
		</ul>
EOF;
            } else {
                $out .= <<<EOF
			<span>{$_SESSION['trx_success']}</span>
EOF;
            }
            $out .= <<<EOF
</div>
EOF;
            unset($_SESSION['trx_success']);
        }


        if($CMS->permit['customer_add'] == 1)
        {
            $btn_add_cus =<<<EOF
						<a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

        }else
        {
            $btn_add_cus = "";
        }

        $data['btn_add_cus'] = $btn_add_cus;

        $btn_edit_cus = '';
        if($CMS->permit['customer_edit'] == 1)
        {
            if($data['data_info']['cus']['cus_id'])
            {
                $btn_edit_cus =<<<EOF
             <span class="box_edit_cus pull-right" style="margin-left: 10px;"><a id="{$data['data_info']['cus']['cus_id']}" onclick="call_form_edit_customer(this);" class="btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
EOF;
            }

        }

        $data['btn_edit_cus'] = $btn_edit_cus;

        if($CMS->permit['transaction_terms_add'] == 1)
        {
            $btn_add_term =<<<EOF
						<a class="btn_gen add_new_term pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline" onclick="return add_term();"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

        }else
        {
            $btn_add_term = "";
        }
        $data['btn_add_term'] = $btn_add_term;

        if($CMS->permit['transaction_terms_edit'] == 1)
        {
            $btn_edit_term =<<<EOF
							 
EOF;

        }else
        {
            $btn_edit_term = "";
        }
        $data['btn_edit_term'] = $btn_edit_term;

        $form_body = "form{$CMS->input['sub']}";

        $out .= <<<EOF
		<script type='text/javascript' src='{$CMS->vars['js_acp']}/custom_transaction.js?20180312'></script>
		<script type='text/javascript' src='{$CMS->vars['js_acp']}/transaction_terms.js'></script>
		<script type='text/javascript' src='{$CMS->vars['js_url']}/acp_assets.js'></script>
		<form class="transactionForm" id="transactionForm" name="form-signin_v1" action="{$data['form_action']}" method="POST" enctype="multipart/form-data">		   
            {$form_html->$form_body($data)}
        </form>
EOF;

        $option_category = $CMS->product_group->getOptionCategory();
        $option_supplier = $CMS->supplier->get_list_supplier();
        $out .= <<<EOF
<div id="box_add_product" class="popup_add_product mfp-hide">
	<p class="title_add">{$CMS->lang['title_product_add']}</p>
	<form id="add_product_form" name="add_product_form">
		<ul class="list_field">
			<li>
				<input class="" type="radio" name="product_type" value="0" checked='checked' />{$CMS->lang['title_product_type_0']}
				<input class="" type="radio" name="product_type" value="1" />{$CMS->lang['title_product_type_1']}

			</li>
			<li><input class="form-control" name="product_name" placeholder="{$CMS->lang['title_product_name']}" /></li>
			<li><input class="form-control" name="product_code" placeholder="{$CMS->lang['title_product_code']}" /></li>
			<li>
				<select name="sup_id" class="form-control select2">
					{$option_supplier}
				</select>
				{$CMS->lang['title_product_supplier']}
			</li>
			<li>
				<select name="product_group" class="form-control select2">
					{$option_category}
				</select>
				{$CMS->lang['title_product_group']}
			</li>
			<li>
				<div class="table-responsive" style="overflow-x: initial;">
					<table class="table">
						<tr>
							<th>#</th>
							<th>{$CMS->lang['table_product_service']}</th>
							<th>{$CMS->lang['table_description']}</th>
							<th>{$CMS->lang['table_quantity']}</th>
							<th>{$CMS->lang['table_price']}</th>
							<th>{$CMS->lang['table_tax']}</th>
							<th></th>
						</tr>
						<tbody id="data_subitem">
							<tr class="row-item" rowtr="last-row">
								<td class="item-td" for="item-1" check="chtd"><span class="number">1</span></td>
								<td class="item-td" for="item-2">
									<input type="text" for="item-2" check="chtd" name="sub_product_name[]" class="form-control find_product hidden_border" autocomplete="off"/>
									<input type="hidden" class="product_id" name="sub_product_id[]" value=""/>
									<div class="box_result_find" style="position: absolute; background: #ccc;"></div>
								</td>

								<td class="item-td" for="item-3" check="chtd"><textarea for="item-3" name="sub_product_description[]" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>

								<td class="item-td" for="item-4" check="chtd"><input type="text" for="item-4" name="sub_product_quantity[]" check="chtd" class="form-control hidden_border" onkeypress="return check_enter_number(event,this);"/></td>

								<td class="item-td" for="item-5" check="chtd"><input type="text" for="item-5" name="sub_product_price[]" check="chtd" onkeypress="return check_enter_number(event,this);" class="form-control hidden_border"/></td>

								<td class="item-td" for="item-6" check="chtd"><input type="text" for="item-6" check="chtd" onkeypress="return check_enter_number(event,this);" autocomplete="off" name="sub_product_tax[]" class="form-control search_tax hidden_border"/></td>

								<td class="trash" for="item-7"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</li>
			<li><input class="form-control" type="file" name="product_image" /></li>
			<li><textarea class="form-control" rows="7" cols="30" name="product_description" placeholder="{$CMS->lang['title_product_description']}" ></textarea></li>
			<li><input class="form-control" name="product_price" onkeypress="return check_enter_number(event,this)" placeholder="{$CMS->lang['title_product_price']}" /></li>
			<li><input class="" type="checkbox" name="inclusive_of_tax" value="1" />{$CMS->lang['title_product_is_tax']}</li>
			<li><input class="form-control" name="product_tax" onkeypress="return check_enter_number(event,this)" placeholder="{$CMS->lang['title_product_tax']}" /></li>
			<li><input class="btn btn_add_product" type="button" value="Thêm"/></li>
			<p class="error_msg" style="display:none;"></p>
		</ul>
	</form>
EOF;


        $out .= <<<EOF
</div>

<script>
    var pFrmType = 'transaction';
</script>
{$CMS->global->fullFormHtml()}
{$CMS->global->assetFullFormHtml()}
{$this->chooseProductType()}
{$CMS->transaction_terms->formAddTransactionTerm()} 
<script>
$(document).ready(function() {
	$('#trx_cus_ajax').hide();
	$("#trx_payment_date, #trx_expiration_date, #trx_estimate_date, #trx_accepted_date, #trx_due_date, #trx_credit_memo_date").datetimepicker({ format:'{$CMS->vars['date_format']}', });

//	hideShowAccount();
	if ($('#cus_id').val() > 0) {
		$('#item_line > thead > tr > td > button').prop('disabled', false);
	} else {
		$('#item_line > thead > tr > td > button').prop('disabled', true);
	}
	
	validate_form_custom("#transactionForm", "[type='submit']", "attachFiles");
});




$('#trx_payment_date').change( function() {
	trDue('{$dataFormat}');
});
$('#trx_terms').change( function() {
	trDue('{$dataFormat}');
});
$('.transactionForm #trx_term_name').change(function() {
	$(".transactionForm #trx_terms").val("0");
}).blur(function() {
    if($(this).val()=="")
    {
        $(".transactionForm #trx_terms").val("0");
    }
});

</script>
EOF;
        return $out;
    }

    public function edit($form_html, $data = null) {
        global $CMS, $DB, $member;

        $convertvalue = $CMS->transactions->convertvalue($data);

        switch ($CMS->input['sub']) {
            case 1:
                $data['add_text'] = $add_text = $CMS->lang['trx_edit_1'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_1'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=edit&id={$data['trx_id']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 2:
                $data['add_text'] =$add_text = $CMS->lang['trx_edit_2'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_2'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=edit&id={$data['trx_id']}&invoice={$CMS->input['invoice']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 3:
                $data['add_text'] =$add_text = $CMS->lang['trx_edit_3'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_3'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=edit&id={$data['trx_id']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 4:
                $data['add_text'] =$add_text = $CMS->lang['trx_edit_4'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_4'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=edit&id={$data['trx_id']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 5:
                $data['add_text'] = $add_text = $CMS->lang['trx_edit_5'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_5'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=edit&id={$data['trx_id']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 6:
                $data['add_text'] =$add_text = $CMS->lang['trx_edit_6'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_6'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=edit&id={$data['trx_id']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 7:
                $data['add_text'] = $add_text = $CMS->lang['trx_edit_7'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_7'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=edit&id={$data['trx_id']}&invoice={$CMS->input['invoice']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 8:
                $data['add_text'] = $add_text = $CMS->lang['trx_edit_8'];
                $data['info_text'] = $info_text = $CMS->lang['trx_info_8'];
                $data['form_action'] = $form_action = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=8&act=edit&id={$data['trx_id']}";
                $data['back_link'] = $back_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            default:
                break;
        }

        $data = array_merge($data, $CMS->input);

        if($data['cus_type'] == 1) //KH
        {
            $data['cus'] = $CMS->customer->getInfo($data['cus_id']);

            if($data['cus'])
            {
                $data['cus_id'] = $data['cus']['cus_id'];
                $data['trx_cus'] = $data['cus']['cus_full_name'];
            }
        }
        else if($data['cus_type'] == 2) //NCC
        {
            $data['supplier'] = $CMS->supplier->get_info($data['supplier_id']);

            if($data['supplier'])
            {
                $data['supplier_id'] = $data['supplier']['supplier_id'];
            }
        }
        else if($data['cus_type'] == 3) //NV
        {
            $data['assign'] = $CMS->user->get_info($data['user_assign']);

            if($data['user_assign'])
            {
                $data['user_assign'] = $data['assign']['user_id'];
                $data['user_name_assign'] = $data['assign']['user_display_name'];
            }
        }

        $data['item']['product'] = $CMS->transactions->getItemAll($data['trx_id']);
        $data['item']['asset'] = $CMS->transactions->getItemAll($data['trx_id'], 'asset');

        $data['cus_id'] = $cus_id = isset($CMS->input['cus_id']) ? $CMS->input['cus_id'] : $data['cus']['cus_id'];
        $data['trx_cus'] = $trx_cus = isset($CMS->input['trx_cus']) ? $CMS->input['trx_cus'] : $data['cus']['cus_full_name'];
        $data['trx_email'] = $trx_email = isset($CMS->input['trx_email']) ? $CMS->input['trx_email'] : $data['cus_email'];
        $data['trx_address'] = $trx_address = isset($CMS->input['trx_address']) ? $CMS->input['trx_address'] : $data['trx_billing_address'];
        $data['trx_method'] = $trx_method = isset($CMS->input['trx_method']) ? $CMS->input['trx_method'] : $data['trx_payment_method'];
        $data['trx_account'] = $trx_account = isset($CMS->input['trx_account']) ? $CMS->input['trx_account'] : $data['trx_account'];
        $data['trx_terms'] = $trx_terms = isset($CMS->input['trx_terms']) ? $CMS->input['trx_terms'] : $data['trx_terms'];
        $data['trx_payment_date'] = $trx_payment_date = isset($CMS->input['trx_payment_date']) ? $CMS->input['trx_payment_date'] : $CMS->class->date->date_format($data['trx_payment_date']);
        $data['trx_total'] = $trx_total = isset($CMS->input['trx_total']) ? $CMS->input['trx_total'] : $data['trx_total'];
        $data['trx_note'] = $trx_note = isset($CMS->input['trx_note']) ? $CMS->input['trx_note'] : $data['trx_note'];
        $data['trx_msg'] = $trx_msg = isset($CMS->input['trx_msg']) ? $CMS->input['trx_msg'] : $data['trx_msg'];
        $data['trx_reference_no'] = $trx_reference_no = isset($CMS->input['trx_reference_no']) ? $CMS->input['trx_reference_no'] : $data['trx_reference_no'];
        $data['trx_expiration_date'] = $trx_expiration_date = isset($CMS->input['trx_expiration_date']) ? $CMS->input['trx_expiration_date'] : $CMS->class->date->date_format($data['trx_expiration_date']);
        $data['trx_estimate_date'] = $trx_estimate_date = isset($CMS->input['trx_estimate_date']) ? $CMS->input['trx_estimate_date'] : $CMS->class->date->date_format($data['trx_estimate_date']);
        $data['trx_accepted_date'] = $trx_estimate_date = isset($CMS->input['trx_accepted_date']) ? $CMS->input['trx_accepted_date'] : $CMS->class->date->date_format($data['trx_accepted_date']);
        $data['trx_due_date'] = $trx_estimate_date = isset($CMS->input['trx_due_date']) ? $CMS->input['trx_due_date'] : $CMS->class->date->date_format($data['trx_due_date']);
        $data['trx_credit_memo_date'] = $trx_credit_memo_date = isset($CMS->input['trx_credit_memo_date']) ? $CMS->input['trx_credit_memo_date'] : $CMS->class->date->date_format($data['trx_credit_memo_date']);
        $data['trx_contract_code'] = $trx_contract_code = isset($CMS->input['trx_contract_code']) ? $CMS->input['trx_contract_code'] : $data['trx_contract_code'];
        $data['trx_estimate_status'] = $trx_estimate_status = isset($CMS->input['trx_estimate_status']) ? $CMS->input['trx_estimate_status'] : $data['trx_estimate_status'];
        $data['trx_status'] = $trx_status = isset($CMS->input['trx_status']) ? $CMS->input['trx_status'] : $data['trx_status'];
        $data['store_id'] = \lib\input::get('store_id') ? \lib\input::get('store_id') * 1 : $data['store_id'];

        $dataFormat = str_replace(['YYYY','MM','DD'],['Y','m','d'],$CMS->vars['date_format']);

        $est_status = [0,5,3,4];

        $option_trx_estimate_status = "";
        foreach ($est_status as $i) {
            if ($i == $trx_status) {
                $option_trx_estimate_status .= "<option value='{$i}' selected>{$CMS->lang['trx_status_0'.$i]}</option>";
            } else {
                $option_trx_estimate_status .= "<option value='{$i}'>{$CMS->lang['trx_status_0'.$i]}</option>";
            }
        }
        $data['option_trx_estimate_status'] = $option_trx_estimate_status;

        $option_trx_method = "";
        for ($i = 0; $i <= 1; $i++) {
            if ($i == $trx_method) {
                $option_trx_method .= "<option value='{$i}' selected>{$CMS->lang['trx_method_0'.$i]}</option>";
            } else {
                $option_trx_method .= "<option value='{$i}'>{$CMS->lang['trx_method_0'.$i]}</option>";
            }
        }
        $data['option_trx_method'] = $option_trx_method;


        $option_trx_status = "";
        for ($i = 0; $i <= 3; $i++) {
            if ($i == $trx_status) {
                $option_trx_status .= "<option value='{$i}' selected>{$CMS->lang['trx_status_0'.$i]}</option>";
            } else {
                $option_trx_status .= "<option value='{$i}'>{$CMS->lang['trx_status_0'.$i]}</option>";
            }
        }
        $data['option_trx_status'] = $option_trx_status;

        $account = $CMS->accounts->getAll(' accounts_status=1 AND ');
        $option_trx_account = "<option balance='{$CMS->class->input->currency(0)}' value='0'>{$CMS->lang['select']}</option>";
        foreach ($account as $a) {
            if ($a['accounts_id'] == $trx_account) {
                $option_trx_account .= "<option  balance='{$CMS->class->input->currency($a['accounts_balance'])}' value='{$a['accounts_id']}' selected>{$a['accounts_name']}</option>";
            } else {
                $option_trx_account .= "<option balance='{$CMS->class->input->currency($a['accounts_balance'])}' value='{$a['accounts_id']}' >{$a['accounts_name']}</option>";
            }
        }
        $data['option_trx_account'] = $option_trx_account;

        $out = '';
        if (!empty($_SESSION['trx_error'])) {
            $out .= <<<EOF
<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
	<strong>{$CMS->lang['error']}</strong><br>
EOF;
            if (is_array($_SESSION['trx_error'])) {
                $out .= <<<EOF
		<ul>
EOF;
                foreach ($_SESSION['trx_error'] as $trx) {
                    $out .= <<<EOF
			<li>{$trx}</li>
EOF;
                }
                $out .= <<<EOF
		</ul>
EOF;
            } else {
                $out .= <<<EOF
			<span>{$_SESSION['trx_error']}</span>
EOF;
            }
            $out .= <<<EOF
</div>
EOF;
            unset($_SESSION['trx_error']);
        }
        if (isset($_SESSION['trx_success'])) {
            $out .= <<<EOF
<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
	<strong>{$CMS->lang['success']}</strong><br>
EOF;
            if (is_array($_SESSION['trx_success'])) {
                $out .= <<<EOF
		<ul>
EOF;
                foreach ($_SESSION['trx_success'] as $trx) {
                    $out .= <<<EOF
			<li>{$trx}</li>
EOF;
                }
                $out .= <<<EOF
		</ul>
EOF;
            } else {
                $out .= <<<EOF
			<span>{$_SESSION['trx_success']}</span>
EOF;
            }
            $out .= <<<EOF
</div>
EOF;
            unset($_SESSION['trx_success']);
        }

        if($CMS->permit['customer_add'] == 1)
        {
            $btn_add_cus =<<<EOF
            <a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

        }else
        {
            $btn_add_cus = "";
        }

        $data['btn_add_cus'] = $btn_add_cus;

        $btn_edit_cus = "";
        if($CMS->permit['customer_edit'] == 1)
        {
            if($data['cus']['cus_id'])
            {
                $btn_edit_cus =<<<EOF
             <span class="box_edit_cus pull-right" style="margin-left: 10px;"><a id="{$data['cus']['cus_id']}" onclick="call_form_edit_customer(this);" class="btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
EOF;
            }
        }

        $data['btn_edit_cus'] = $btn_edit_cus;

        if($CMS->permit['transaction_terms_add'] == 1)
        {
            $btn_add_term =<<<EOF
						<a class="btn_gen add_new_term pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline" onclick="return add_term();"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

        }else
        {
            $btn_add_term = "";
        }

        $data['btn_add_term'] = $btn_add_term;

        if($CMS->permit['transaction_terms_edit'] == 1)
        {
            $btn_edit_term =<<<EOF
							 
EOF;

        }else
        {
            $btn_edit_term = "";
        }

        $data['btn_edit_term'] = $btn_edit_term;

        $data['status_display'] = $CMS->input['sub']!=4 ? $convertvalue['trx_status_c'] : "";

        $form_body = "form{$CMS->input['sub']}";
        
        $out .= <<<EOF
		<script type='text/javascript' src='{$CMS->vars['js_acp']}/custom_transaction.js?20180312'></script>
		<script type='text/javascript' src='{$CMS->vars['js_acp']}/transaction_terms.js'></script>
		<script type='text/javascript' src='{$CMS->vars['js_url']}/acp_assets.js'></script>
<form id="transactionForm" action="{$form_action}" method="POST" enctype="multipart/form-data" class="transactionForm">
        {$form_html->$form_body($data)}
	</form>
</div>
<script>
    var pFrmType = 'transaction';
</script>
{$CMS->global->fullFormHtml()}
{$CMS->global->assetFullFormHtml()}
{$this->chooseProductType()}
{$CMS->transaction_terms->formAddTransactionTerm()}
<script>
$(document).ready(function() {
	$('#trx_cus_ajax').hide();
	$("#trx_payment_date, #trx_expiration_date, #trx_estimate_date, #trx_accepted_date, #trx_due_date, #trx_credit_memo_date").datetimepicker({ format:'{$CMS->vars['date_format']}', });
//	hideShowAccount();
	hideQuantityCol();
	if ($('#cus_id').val() > 0) {
		$('#item_line > thead > tr > td > button').prop('disabled', false);
	} else {
		$('#item_line > thead > tr > td > button').prop('disabled', true);
	}
	
	validate_form_custom("#transactionForm", "[type='submit']", "attachFiles");
});




$('#trx_payment_date').change( function() {
	trDue('{$dataFormat}');
});
$('#trx_terms').change( function() {
	trDue('{$dataFormat}');
});
$('.transactionForm #trx_term_name').change(function() {
	$(".transactionForm #trx_terms").val("0");
}).blur(function() {

    if($(this).val()=="")
    {
        $(".transactionForm #trx_terms").val("0");
    }
});
</script>
EOF;
        return $out;
    }

    public function show($data = null, $template) {
        global $CMS, $DB, $member;
        switch ($data['trx_subtype']) {
            case 1:
                $data[info_text] = $CMS->lang['trx_info_1'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 2:
                $data[info_text] = $CMS->lang['trx_info_2'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 3:
                $data[info_text] = $CMS->lang['trx_info_3'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 4:
                $data[info_text] = $CMS->lang['trx_info_4'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            case 5:
                $data[info_text] = $CMS->lang['trx_info_5'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 6:
                $data[info_text] = $CMS->lang['trx_info_6'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 7:
                $data[info_text] = $CMS->lang['trx_info_7'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=2";
                break;
            case 8:
                $data[info_text] = $CMS->lang['trx_info_8'];
                $data[back_link] = "{$CMS->vars['root_domain']}/?site=transactions&type=1";
                break;
            default:
                break;
        }

        $data['item']['product'] = $CMS->transactions->getItemAll($data['trx_id']);
        $data['item']['asset'] = $CMS->transactions->getItemAll($data['trx_id'], 'asset');

        $data['cus_info'] = $CMS->customer->getInfo($data['cus_id']);
        $data['supplier_info'] = $CMS->supplier->get_info($data['supplier_id']);
        $data['assign_info'] = $CMS->user->get_info($data['user_assign']);

        $data['user_info'] = $CMS->user->get_info($data['user_id']);

        $data['account_info'] = $CMS->accounts->getInfo($data['trx_account']);

        $data['accounts_type_info'] = $CMS->accounts_type->getInfo($data['at_id']);

        if($accounts_type_name = @json_decode($data['accounts_type_info']['accounts_type_name'], true))
        {
            $data['accounts_type_info']['accounts_type_name'] =  $accounts_type_name[$CMS->vars['default_language']];
        }


        $show_tmp = 'show_'.$data['trx_subtype'];

        $out = $template->$show_tmp($data);

        return $out;
    }

    function chooseProductType()
    {
        global $CMS;
        $output = <<<EOF
<div id="choose_product_type" class="popup_edit_product mfp-hide" style="clear: both; overflow: hidden;">
    <p class="title_add" style="font-weight: bold; text-transform: uppercase; font-size: 18px; text-align: center;">{$CMS->lang['title_product_service_type']}</p>

    <div class="col-md-4">
    </div>
    <div class="col-md-4">
        <button onclick="changeProductType(0)" class="btn btn-inline btn-primary ladda-button" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['product_service_type_0']}</span><span class="ladda-spinner"></span><div class="ladda-progress" style="width: 0px;"></div></button>
        <button onclick="changeProductType(1)" class="btn btn-inline btn-primary ladda-button" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['product_service_type_1']}</span><span class="ladda-spinner"></span><div class="ladda-progress" style="width: 0px;"></div></button>
    </div>
    <div class="col-md-4">

    </div>
    <div class="col-md-4">
    </div>
</div>
EOF;
        return $output;
    }

    public function preview($data = "")
    {
        global $CMS, $DB, $member;

        $allItem = $CMS->transactions->getItemAll($data['trx_id'], "all",1);

        $onload_print = $CMS->input['act'] == 'print' ? 'onload="window.print()"' : '';

        $output = <<<EOF
		 <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
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


.trx_status {
	position: absolute;
    top: 25%;
    left: 50%;
    /* Rotate div */
    -ms-transform: rotate(-45deg); /* IE 9 */
    -webkit-transform: rotate(-45deg); /* Chrome, Safari, Opera */
    transform: rotate(-45deg);
    z-indez: 100;
    font-size: 50px;
    color: black;
   -webkit-text-fill-color: white; /* Will override color (regardless of order) */
   -webkit-text-stroke-width: 1px;
   -webkit-text-stroke-color: black;
}
    </style>
  </head>
  <body {$onload_print}>
    <header class="clearfix">
        <style>
            a{color:red}
        </style>
     	<div class="card-block invoice clearfix" >
     		<div class="col-lg-6 company-info" style="float:left;width:50%">
                <h2>{$CMS->vars['print_company_name']}</h2>
                <p>{$CMS->vars['print_website']}<br>
                    {$CMS->vars['print_company_address']}<br>
                    {$CMS->vars['print_company_phone']}
                </p>
			  </div>

			  <div class="text-lg-right clearfix"  style="float:right;  width:50%;">
                <div>
EOF;
        if($data['trx_subtype'] == 1  || $data['trx_subtype'] == 5)
        {
            $output .= <<<EOF
                        <h5 style="text-align:right; font-weight:bold;font-size:14px">{$CMS->lang['invoice_number']} #{$data['trx_invoice_no_c']}</h5>
EOF;
        }
        elseif($data['trx_subtype'] == 2  || $data['trx_subtype'] == 7)
        {
            $output .= <<<EOF
                        <h5 style="text-align:right; font-weight:bold;font-size:14px">{$CMS->lang['payment1']} #{$data['trx_code']}</h5>
EOF;
        }
        elseif($data['trx_subtype'] == 3)
        {
            $output .= <<<EOF
                        <h5 style="text-align:right; font-weight:bold;font-size:14px">{$CMS->lang['receipt']} #{$data['trx_code']}</h5>
EOF;
        }
        elseif($data['trx_subtype'] == 4)
        {
            $output .= <<<EOF
                        <h5 style="text-align:right; font-weight:bold;font-size:14px">{$CMS->lang['estimate']} #{$data['trx_code']}</h5>
EOF;
        }
        elseif($data['trx_subtype'] == 6)
        {
            $output .= <<<EOF
                        <h5 style="text-align:right; font-weight:bold;font-size:14px">{$CMS->lang['expense']} #{$data['trx_code']}</h5>
EOF;
        }
        elseif($data['trx_subtype'] == 8)
        {
            $output .= <<<EOF
                        <h5 style="text-align:right; font-weight:bold;font-size:14px">{$CMS->lang['credit_memo']} #{$data['trx_code']}</h5>
EOF;
        }


        $output .= <<<EOF
                        <div style="text-align:right; ;font-size:13px;margin-top:-15px">{$CMS->lang['trx_payment_date']}: {$data['trx_time_c']}</div>
                 </div>			 
			 	</div>
     	</div>
     	<div class="card-block invoice clearfix" >
     		 <div id="project" style="float:left;width:50%">
EOF;

        if($data['cus_type'] == 1) //KH
        {
            $customer = $CMS->customer->getInfo($data['cus_id']);

            $output .= <<<EOF
	     		 <h4>{$CMS->lang['cus_type_1']}</h4>
			        <div><span>{$CMS->lang['fullname']}:</span> {$customer['cus_full_name']}</div>
			        <div><span>{$CMS->lang['address']}: </span> {$customer['cus_address']}</div>
			        <div><span>{$CMS->lang['phone']}:</span> {$customer['cus_phone']}</div>
			        <div><span>{$CMS->lang['company']}:</span> {$customer['cus_company']}</div>
			       
			      </div>
EOF;
        }
        else if($data['cus_type'] == 2) //Supplier
        {
            $supplier = $CMS->supplier->get_info($data['supplier_id']);

            $output .= <<<EOF
	     		 <h4>{$CMS->lang['supplier']}:</h4>
			        <div><span>{$CMS->lang['supplier_name']}:</span> {$supplier['supplier_name']}</div>
			        <div><span>{$CMS->lang['address']}: </span> {$supplier['supplier_address']}</div>
			        <div><span>{$CMS->lang['phone']}:</span> {$supplier['supplier_phone']}</div>			       
			      </div>
EOF;
        }
        else if($data['cus_type'] == 3) //Assign
        {
            $user = $CMS->user->get_info($data['user_assign']);

            $output .= <<<EOF
	     		 <h4>{$CMS->lang['staff']}:</h4>
			        <div><span>{$CMS->lang['fullname']}:</span> {$user['user_display_name']}</div>
			        <div><span>{$CMS->lang['address']}: </span> {$user['user_address']}</div>
			        <div><span>{$CMS->lang['phone']}:</span> {$user['user_phone']}</div>	
			      </div>
EOF;
        }

        $class_status = $CMS->input['act'] == 'print' ? 'trx_status' : '';

        $output .= <<<EOF
			      <div id="company" class="clearfix" style="float:right;  width:50%;">
			      	<h4>{$CMS->lang['details']}</h4>
			  
			        <div>{$CMS->lang['total1']}: <span style="font-weight:bold">{$data['trx_total_c']}</span></div>
			        <div>{$CMS->lang['trx_amount_received']}: <span style="font-weight:bold">{$data['trx_receive_payment_c']}</span></div>
EOF;
        if($data['trx_subtype'] != 8) // không phải loại credit memo
        {
            $output .= <<<EOF
                    <div>{$CMS->lang['trx_remain']}: <span style="font-weight:bold">{$data['trx_remain_c']}</span></div>
			        <div>{$CMS->lang['payment_method']}:{$data['trx_payment_method_c']}</div>
EOF;
        }
        else
        {
            $output .= <<<EOF
			        <div>{$CMS->lang['amount_to_refund']}: <span style="font-weight:bold">{$data['amount_to_refund_c']}</span></div>
EOF;
        }

        $output .= <<<EOF
			        <div class="{$class_status}">
                        {$data['trx_status_c']}
                    </div>
			      </div>			 
     	</div>
    </header>
     <main>
EOF;

        //Invoice transactions
        $trx_invoice_info = @json_decode($data['trx_invoice_info'], true);
        $total = 0;
        $sum_total = 0;
        $stt=0;

        if($trx_invoice_info)
        {
            $code_text = $data['trx_type'] == 1 ? $CMS->lang['table_inv_code'] : $CMS->lang['table_pay_code'];
            $output .= <<<EOF
              <table style="width: 100%; max-width: 100%;" width="100%" cellspacing="0" cellpadding="55%">
                <thead>
                 <tr style="background-color: #f8f8f8;padding:10px 5px;">
                        <th>#</th>
                        <th>$code_text</th>
                        <th>{$CMS->lang['trx_due_2']}</th>
                        <th>{$CMS->lang['trx_original_2']}</th>
                        <th>{$CMS->lang['trx_payment_2']}</th>
                     </tr>
                </thead>
                <tbody>
EOF;

            foreach($trx_invoice_info as $invoice_id => $invoice_total)
            {
                $stt++;
                $invoice_info = $CMS->transactions->getInfo($invoice_id);
                $invoice_info['trx_expiration_date'] = $CMS->class->date->date_format($invoice_info['trx_expiration_date']);
                $total += $invoice_info['trx_receive_payment'];
                $sum_total += $invoice_info['trx_total'];

                $prefix_trx = $data['trx_type'] == 2 ? 'PAY' : 'INV';
                $inv_no = $prefix_trx.$invoice_info['trx_invoice_no'];

                $output .= <<<EOF
                    <tr>
                        <td>{$stt}</td>
                        <td>{$inv_no}</td>
                        <td>{$invoice_info['trx_expiration_date']}</td>
                        <td class="text-right" style="text-align: right">{$CMS->class->input->currency($invoice_info['trx_total'])}</td>
                        <td class="text-right" style="text-align: right">{$CMS->class->input->currency($invoice_info['trx_receive_payment'])}</td>
                    </tr>
EOF;

            }

            $output .= <<<EOF
                  <tr>
                    <td colspan="2"></td>
                    <td style="text-align: right">{$CMS->lang['table_total']}</td>
                    <td class="text-right" style="text-align: right">{$CMS->class->input->currency($sum_total)}</td>
                    <td class="text-right" style="text-align: right">{$CMS->class->input->currency($total)}</td>
                </tr>
                </tbody>
              </table>
EOF;
        }
        //End - Invoice transactions

        // Receive transactions
        $trx_receive_info = $CMS->transactions->convertReceiveInvoiceInfo($data['trx_receive_info']);

        $total = 0;
        $sum_total = 0;
        $stt=0;

        if($trx_receive_info)
            {
                $code_text = $CMS->lang['table_receive_code'];
                $output .= <<<EOF
              <table style="width: 100%; max-width: 100%;" width="100%" cellspacing="0" cellpadding="55%">
                <thead>
                 <tr style="background-color: #f8f8f8;padding:10px 5px;">
                        <th>#</th>
                        <th>$code_text</th>
                        <th>{$CMS->lang['created_at']}</th>
                        <th>{$CMS->lang['updated_at']}</th>
                        <th>{$CMS->lang['trx_status']}</th>
                        <th>{$CMS->lang['trx_original_2']}</th>
                        <th>{$CMS->lang['trx_receive_amount']}</th>
                     </tr>
                </thead>
                <tbody>
EOF;

                foreach($trx_receive_info as $receive_info)
                {
                    $stt++;

                    $receive_info = $CMS->transactions->convertvalue($receive_info);

                    $total += $receive_info['receive_amount'];
                    $sum_total += $receive_info['trx_total'];

                    $output .= <<<EOF
                    <tr>
                        <td>{$stt}</td>
                        <td>{$receive_info['trx_code']}</td>
                        <td>{$receive_info['trx_time_c']}</td>
                        <td>{$receive_info['trx_time_update_c']}</td>
                        <td>{$receive_info['trx_status_c']}</td>
                        <td class="text-right" style="text-align: right">{$receive_info['trx_total_tax_c']}</td>
                        <td class="text-right" style="text-align: right">{$CMS->class->input->currency($receive_info['data_bk']['receive_amount'])}</td>
                    </tr>
EOF;

                }

                $output .= <<<EOF
                  <tr>
                    <td colspan="4"></td>
                    <td style="text-align: right">{$CMS->lang['table_total']}</td>
                    <td class="text-right" style="text-align: right">{$CMS->class->input->currency($sum_total)}</td>
                    <td class="text-right" style="text-align: right">{$CMS->class->input->currency($total)}</td>
                </tr>
                </tbody>
              </table>
EOF;
        }
        // END - Receive transactions



    if($allItem)
    {
        $output .= <<<EOF
	      <table style="width: 100%; max-width: 100%;" width="100%" cellspacing="0" cellpadding="55%">
	        <thead>
	         <tr style="background-color: #f8f8f8;padding:10px 5px;">
					<th>#</th>
					<th>{$CMS->lang['product']}/{$CMS->lang['service']}</th>
					<th>{$CMS->lang['description']}</th>
					<th>{$CMS->lang['period']}</th>
				 	<th>{$CMS->lang['price']}</th>
				 	<th>{$CMS->lang['quantity']}</th>
				 	<th>{$CMS->lang['tax']}</th>
					<th>{$CMS->lang['total_price']}</th>
				 </tr>
	        </thead>
	        <tbody>
EOF;

        $data_trx_amount = $CMS->class->input->currency($data['trx_amount']);
        $data_trx_total = $CMS->class->input->currency($data['trx_total']);

        $i = 1;
        foreach ($allItem as $key => $value) {
            # code...
            if ($value['tri_cycle_type'] == 0) {
                $tri_cycle_type = $CMS->lang['gonce'];

            } elseif ($value['tri_cycle_type'] == 1) {
                $tri_cycle_type = "{$value['tri_cycle']} {$CMS->lang['gmonth']}";

            } elseif ($value['tri_cycle_type'] == 2) {
                $tri_cycle_type = "{$value['tri_cycle']} {$CMS->lang['gyear']}";

            }


            $tri_price = $CMS->class->input->currency($value['tri_price']);
            $tri_total = $CMS->class->input->currency($value['tri_price']*$value['tri_cycle']*$value['tri_quantity'] + ($value['tri_price']*$value['tri_cycle']*$value['tri_quantity'] * $value['tri_tax'] / 100));
            $output .= <<<EOF

										<tr style="padding-top:5px">
											<td >{$i}</td>
											<td>{$value['tri_name']}</td>
											<td>{$value['tri_description']}</td>
											<td>{$tri_cycle_type}</td>
											<td>{$tri_price}</td>
											<td>{$value['tri_quantity']}</td>
											<td>{$value['tri_tax']} %</td>
											<td>{$tri_total}</td>
										</tr>
EOF;
            $i++;
        }
        $output .= <<<EOF
	          <tr>
	            <td colspan="7" style="text-align:right">{$CMS->lang['total']}:</td>
	            <td class="total">{$data_trx_amount}</td>	 
	          </tr> 
EOF;
        if ($data['trx_discount_value'] > 0) {
            $output .= <<<EOF
	           <tr>
	            <td colspan="7" style="text-align:right">{$CMS->lang['discount']}</td>
	            <td class="total">{$data['trx_discount_value_c']}</td>
	          </tr>
EOF;
        }
        $output .= <<<EOF
              <tr>
	            <td colspan="7" style="text-align:right">{$CMS->lang['discount']}: </td>
	            <td class=" total">{$data['trx_discount_value_c']}</td>
	          </tr>
	          <tr>
	            <td colspan="7" style="text-align:right">{$CMS->lang['tax']}: </td>
	            <td class=" total">{$data['trx_tax_c']}</td>
	          </tr>
	           <tr>
	            <td colspan="7" style="text-align:right">{$CMS->lang['total_money']}: </td>
	            <td class=" total">{$data_trx_total}</td>
	          </tr>
	        </tbody>
	      </table>
EOF;
    }

        $output .= <<<EOF
	      <div id="notices">
	        <div>NOTICE:</div>
	        <div class="notice">{$data['trx_note']}</div>
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

    public function preview2($data = "")
    {
        global $CMS, $DB, $member;

        $dateFormat = 'M,jS Y';

        //created date field
        $createdDate = [
            1 => 'trx_payment_date',
            2 => 'trx_time',
            3 => 'trx_time',
            4 => 'trx_time',
            5 => 'trx_payment_date',
            6 => 'trx_time',
            7 => 'trx_time',
        ];

        $createdDate = $data[$createdDate[$data['data_bk']['trx_subtype']]] ? $data[$createdDate[$data['data_bk']['trx_subtype']]] : $data['data_bk']['trx_time'];
        $createdDate = $createdDate ? $createdDate : time();

        $createdYear =  \lib\date::format($createdDate, 'Y');
        $createdDate = strtoupper(\lib\date::format($createdDate, $dateFormat));

        //Due date
        $dueDate = [
            1 => 'trx_due_date',
            2 => 'trx_payment_date',
            3 => 'trx_payment_date',
            4 => 'trx_estimate_date',
            5 => 'trx_due_date',
            6 => 'trx_payment_date',
            7 => 'trx_payment_date',
        ];

        $dueDate = $data[$dueDate[$data['data_bk']['trx_subtype']]] ? $data[$dueDate[$data['data_bk']['trx_subtype']]] : $data['data_bk']['trx_time'];
        $dueDate = $dueDate ? $dueDate : time();

        $dueDate = strtoupper(\lib\date::format($dueDate, $dateFormat));

        $currencyShort = [
            '$' => 'USD',
            'đ' => 'VND',
        ];

        $currencyLong = [
            '$' => 'United States Dollars',
            'đ' => 'Vietnamese Dong',
        ];

        $cus_type = intval($data['data_bk']['cus_type']);

        if($cus_type == 1)
        {
            $buyerInfo = $CMS->customer->getInfo($data['data_bk']['cus_id']);

            $buyer['name'] = $buyerInfo['cus_company'] ? $buyerInfo['cus_company'] : $buyerInfo['cus_full_name'];

            $buyer['address'] = $buyerInfo['cus_company_address'] ? $buyerInfo['cus_company_address'] : $buyerInfo['cus_address'];

            $buyer['taxCode'] = $buyerInfo['cus_tax_code'];
        }
        elseif ($cus_type == 2)
        {
            $buyerInfo = $CMS->supplier->get_info($data['data_bk']['supplier_id']);

            $buyer['name'] = $buyerInfo['supplier_name'];

            $buyer['address'] = $buyerInfo['supplier_address'];

            $buyer['taxCode'] = $buyerInfo['supplier_taxcode'];
        }
        elseif ($cus_type == 3)
        {
            $buyerInfo = $CMS->user->get_info($data['data_bk']['user_assign']);

            $buyer['name'] = $buyerInfo['user_display_name'];

            $buyer['address'] = $buyerInfo['user_address'];

            $buyer['taxCode'] = '';
        }

        $seller['name'] = mb_strtoupper($CMS->vars['print_company_name'],'UTF-8');
        $seller['address'] = mb_strtoupper($CMS->vars['print_company_address'],'UTF-8');
        $seller['taxCode'] = mb_strtoupper($CMS->vars['print_company_tax'],'UTF-8');

        $buyer['name'] = mb_strtoupper($buyer['name'],'UTF-8');
        $buyer['address'] = mb_strtoupper($buyer['address'],'UTF-8');
        $buyer['taxCode'] = mb_strtoupper($buyer['taxCode'],'UTF-8');

        $allItem = $CMS->transactions->getItemAll($data['trx_id'], "all", 1);
        $onload_print = $CMS->input['act'] == 'print' ? 'onload="window.print()"' : '';

        $output = <<<EOF
 <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <style>
        table#detail {
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table#detail, table#detail th, table#detail td, table#detail th {
           border: 1px solid black;
        }
        
        table#detail td {
            padding-left: 2px;
            padding-right: 2px;
        }
        
        table#main td {
            padding-left:15px;
        }
        
        table#main {
            margin-bottom: 20px;
        }
    </style>
  </head>
  <body {$onload_print} style="width: 21cm">
    <table id="main" autosize="1.6" border="0" width="100%">
        <tr>
            <td colspan="2" style="text-align:center"><h1>COMMERCIAL INVOICE</h1></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:right;">
                NO.{$data['trx_id']}/{$createdYear}<br />
                DATE {$createdDate}
            </td>
        </tr>
        <tr>
            <td width="50%" style="font-weight:bold; vertical-align: top">THE SELLER:</td>
            <td width="50%" style="font-weight:bold; word-wrap: break-word">
                {$seller['name']}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold; vertical-align: top">ADDRESS:</td>
            <td style="font-weight:bold; word-wrap: break-word">
                {$seller['address']}<br/>
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold; vertical-align: top">TAX CODE:</td>
            <td style="font-weight:bold; word-wrap: break-word">
                {$seller['taxCode']}<br/>
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr  style="vertical-align: top">
            <td style="font-weight:bold; vertical-align: top">THE BUYER:</td>
            <td style="font-weight:bold; word-wrap: break-word">
                {$buyer['name']}<br/>
            </td>
        </tr>
        <tr  style="vertical-align: top">
            <td style="font-weight:bold; vertical-align: top">ADDRESS:</td>
            <td style="font-weight:bold; word-wrap: break-word">
                {$buyer['address']}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold; vertical-align: top">TAX CODE:</td>
            <td style="font-weight:bold; word-wrap: break-word">
                {$buyer['taxCode']}
            </td>
        </tr>
        <tr  style="vertical-align: top">
            <td style="font-weight:bold;">SALE CONTRACT NO:</td>
            <td style="font-weight:bold; word-wrap: break-word">
                 {$data['trx_contract_code']}
            </td>
        </tr>
        <tr  style="vertical-align: top">
            <td style="font-weight:bold;">DUE DATE:</td>
            <td style="padding-left:120px">
                 DATED {$dueDate}
            </td>
        </tr>
    </table>
    <table id="detail" autosize="1.6" style="text-align:center;" width="100%">
        <tr>
            <th width="5%">NO</th>
            <th width="15%">ITEM NUMBER</th>
            <th width="20%">
                DESCRIPTION OF GOODS
            </th>
            <th width="15%">
                QUANTITY
            </th>
            <th width="15%">
                CYCLE
            </th>
            <th width="15%">
                PRICE<br>
                ({$currencyShort[$CMS->vars['currency_type']]})
            </th>
            <th width="15%">
                AMOUNT<br/>
                ({$currencyShort[$CMS->vars['currency_type']]})
            </th>
        </tr>
EOF;
        $sumTotal = 0;
        $index=0;

        foreach($allItem as $no => $item)
        {
            $index ++;

            $amount = $item['tri_quantity'] * $item['tri_price'] * $item['tri_cycle'];
            $sumQuantity += $item['tri_quantity'];

            $amount = number_format($amount,2);
            $item['tri_price'] =  number_format($item['tri_price'],2);
            $item['tri_quantity'] =  number_format($item['tri_quantity']);
            $item['tri_cycle'] =  number_format($item['tri_cycle']);

            $output.= <<<EOF
        <tr>
            <td>{$index}</td>
            <td>{$item['tri_name']}</td>
            <td style="text-align:left; word-wrap: break-word">{$item['tri_description']}</td>
            <td>{$item['tri_quantity']}</td>
            <td>{$item['tri_cycle']}</td>
            <td>{$item['tri_price']}</td>
            <td  style="text-align:right">{$amount}</td>
        </tr>
EOF;
        }

        $paymentAmount = $data['trx_receive_payment'];
        $balanceDue = $data['trx_remain'];
        $sumTotal = $data['trx_total'];

        if($CMS->vars['currency_type'] == '$')
        {
            $numberToWords = \lib\input::usd_to_words($sumTotal);
        }
        else
        {
            $numberToWords = $CMS->class->input->read_words($sumTotal);
        }

        $subTotal = number_format($data['trx_amount'],2);
        $discount = number_format($data['trx_total_discount'],2);
        $tax = number_format($data['trx_tax'],2);
        $sumTotal = number_format($sumTotal,2);
        $paymentAmount = number_format($paymentAmount,2);
        $balanceDue = number_format($balanceDue,2);
        $sumQuantity = number_format($sumQuantity);

        $output .= <<<EOF
        <tr style="font-weight: bold">
            <td colspan="3">SUB-TOTAL</td>
            <td>{$sumQuantity}</td>
            <td colspan="2" style="text-align:right; border-right-width:0">{$currencyShort[$CMS->vars['currency_type']]}</td>
            <td style="text-align:right; border-left-width:0">{$subTotal}</td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="3">DISCOUNT</td>
            <td></td>
            <td colspan="2" style="text-align:right; border-right-width:0">{$currencyShort[$CMS->vars['currency_type']]}</td>
            <td style="text-align:right; border-left-width:0">{$discount}</td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="3">TAX</td>
            <td></td>
            <td colspan="2" style="text-align:right; border-right-width:0">{$currencyShort[$CMS->vars['currency_type']]}</td>
            <td style="text-align:right; border-left-width:0">{$tax}</td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="3">TOTAL</td>
            <td></td>
            <td colspan="2" style="text-align:right; border-right-width:0">{$currencyShort[$CMS->vars['currency_type']]}</td>
            <td style="text-align:right; border-left-width:0">{$sumTotal}</td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="3">PAYMENT</td>
            <td></td>
            <td colspan="2" style="text-align:right; border-right-width:0">{$currencyShort[$CMS->vars['currency_type']]}</td>
            <td style="text-align:right; border-left-width:0">{$paymentAmount}</td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="3">BALANCE DUE</td>
            <td></td>
            <td colspan="2" style="text-align:right; border-right-width:0">{$currencyShort[$CMS->vars['currency_type']]}</td>
            <td style="text-align:right; border-left-width:0">{$balanceDue}</td>
        </tr>
    </table>
    <p style="width: 100%; font-style: italic">Say: {$currencyLong[$CMS->vars['currency_type']]} {$numberToWords}.</p>
    <p style="width: 100%;">NOTICE: {$data['trx_note']}</p>
    <p style="width: 100%;">Payment method: </p>
    <p style="width: 100%;">Account number: </p>
    <p style="width: 100%;">Bank branch: </p>
    <p style="width: 100%; text-align:center; margin-bottom:5px"><span style="font-weight: bold; ">FOR AND ON BEHALF OF {$seller['name']}</span></p>
  </body>
</html>
EOF;
        return $output;
    }

    public function email_content_1($data)
    {
        global $CMS;

        $output = <<<EOF
<html>
<head>
    <title>Fwd: You have authorized a payment to Invision Power Services</title>
    <link rel="important stylesheet" href="chrome://messagebody/skin/messageBody.css">
</head>
<body>
<div style="padding:0;margin:0;background:#f2f2f2;min-width:500px">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="m_1313588667216914166marginFix">
        <tbody>
        <tr>
            <td bgcolor="#f2f2f2" class="m_1313588667216914166mobMargin" style="font-size:0px"></td>
            <td bgcolor="#ffffff" width="660" align="center" class="m_1313588667216914166mobContent">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" width="600" valign="top">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tbody>
                                <tr class="m_1313588667216914166no_mobile_phone">
                                    <td bgcolor="#f2f2f2" style="padding-top:10px"></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#f2f2f2" style="padding-top:10px"></td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" bgcolor="#ffffff">
                                        <table border="0" cellpadding="0" cellspacing="0" style="margin-bottom:10px"
                                               width="100%">
                                            <tbody>
                                            <tr valign="bottom">
                                                <td width="20" align="center" valign="top"></td>
                                                <td align="left" height="64">
                                                    <img alt="paypal" style="width:113px;height:46px" width="113"
                                                         height="46" border="0"
                                                         src="{$CMS->vars['upload_url']}/attach/{$CMS->vars['logo_website']}">
                                                </td>
                                                <td width="40" align="center" valign="top"></td>
                                                <td align="right">
                                                    <span>
                                                        <span style="display:inline">{$data['trx_time_c']}</span>
                                                        <span style="display:inline">
                                                            <span style="display:inline">
                                                            <br>Transaction ID: {$data['trx_code']}
                                                            </span>
                                                        </span>
                                                        <span style="display:inline">
                                                            <span style="display:inline">
                                                            <br>Status: {$data['trx_status_c']}
                                                            </span>
                                                        </span>
                                                    </span>
                                                </td>
                                                <td width="20" align="center" valign="top"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:10px;padding-top:10px;margin-bottom:20px" width="100%">
                                            <tbody>
                                            <tr valign="bottom">
                                                <td width="20" align="center" valign="top"></td>
                                                <td valign="top" style="font-family:Calibri,Trebuchet,Arial,sans serif;font-size:15px;line-height:22px;color:#333333" class="m_1313588667216914166ppsans">
                                                    <div style="margin-top:30px;color:#333!important;font-family:arial,helvetica,sans-serif;font-size:12px">
                                                        <span style="color:#333333!important;font-weight:bold;font-family:arial,helvetica,sans-serif">Hello Loi Ly,</span>
                                                        <table cellpadding="5">
                                                            <tbody>
                                                            <tr>
                                                                <td valign="top"><span style="display:inline">
																	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin porta elementum mi non rhoncus. Etiam risus odio, viverra vitae mattis id, aliquet eleifend urna. Aliquam eget lobortis nibh. Ut dui odio, convallis egestas purus non, tristique hendrerit elit. Sed nunc sem, suscipit eget finibus id, bibendum eget tortor. Aliquam erat volutpat. Fusce non condimentum ipsum, in eleifend metus. Proin eget posuere nisi. Nunc orci lorem, semper nec risus ac, vestibulum condimentum tortor. Nunc congue sodales eros, eget elementum mauris malesuada id. Nullam at ullamcorper purus.
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <span style="display:inline"><br></span>
                                                        <div style="margin-top:5px;clear:both">
                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" style="color:#666666!important;font-family:arial,helvetica,sans-serif;font-size:11px;margin-bottom:20px;clear:both" width="100%">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="padding-top:15px;padding-right:10px" valign="top" width="50%">
                                                                        <span style="color:#333333;font-weight:bold">Seller</span>
                                                                        <br>
                                                                        <span style="display:inline">{$CMS->vars['company_name']}<br></span>
                                                                        <span style="display:inline">{$CMS->vars['company_phone']} - {$CMS->vars['company_phone2']}<br></span>
                                                                        <span style="display:inline">{$CMS->vars['company_fax']}<br></span>
                                                                        <span style="display:inline">{$CMS->vars['company_address']}<br></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <span style="display:inline"></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-top:15px;padding-right:10px"
                                                                        valign="top" width="50%">
                                                                            <span style="display:inline">
                                                                                <span style="display:inline">
                                                                                    {$this->show_person_email($data)}
                                                                                </span>
                                                                            </span>
                                                                        </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                            {$this->show_receive_info_email($data['trx_receive_info'])}
                                                            {$this->show_items_email($data['trx_id'])}
                                                            <table border="0" cellpadding="0" cellspacing="0" style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;clear:both;color:#666666!important;font-family:arial,helvetica,sans-serif;font-size:11px" width="100%">
                                                                <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <span style="display:inline"></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table align="right" border="0" cellpadding="0" cellspacing="0" style="color:#666666!important;font-family:arial,helvetica,sans-serif;font-size:11px;margin-top:20px;clear:both;width:100%">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td style="width:75%;text-align:right;padding:0 10px 0 0"><strong>{$CMS->lang['table_amount']}</strong></td>
                                                                                <td style="width:25%;text-align:right;padding:0 10px 0 0">{$data['trx_amount_c']}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width:75%;text-align:right;padding:0 10px 0 0"><strong>{$CMS->lang['discount']}</strong></td>
                                                                                <td style="width:25%;text-align:right;padding:0 10px 0 0">{$data['trx_discount_value_c']}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width:75%;text-align:right;padding:0 10px 0 0"><strong>{$CMS->lang['tax']}</strong></td>
                                                                                <td style="width:25%;text-align:right;padding:0 10px 0 0">{$data['trx_tax_c']}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width:75%;text-align:right;padding:0 10px 0 0"><strong>{$CMS->lang['trx_sum']}</strong></td>
                                                                                <td style="width:25%;text-align:right;padding:0 10px 0 0">{$data['trx_total_c']}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="width:100%;text-align:right;padding:10px 10px 10px 0"></td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color:#757575;padding-bottom:20px;padding-left:10px">
                                                                        <br>
                                                                        <span style="padding-left:10px">Invoice ID: {$data['trx_invoice_no_c']}</span>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <span style="font-weight:bold;color:#444"></span>
                                                        <span></span>
                                                    </div>
                                                </td>
                                                <td width="20" align="center" valign="top"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" width="600" valign="top">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tbody>
                                <tr>
                                    <td bgcolor="#f2f2f2" style="padding-top:20px"></td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" bgcolor="#f2f2f2">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody>
                                            <tr valign="bottom">
                                                <td>
                                                    <table align="left" class="m_1313588667216914166mobile_table_width_utility_nav" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                        <tr>
                                                            <td class="m_1313588667216914166ultility_nav_padding" style="font-family:Calibri,Trebuchet,Arial,sans serif;font-size:13px;color:#666;font-weight:bold">
                                                                <span id="m_1313588667216914166bottomLinks"></span>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td width="20" align="center" valign="top"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td bgcolor="#f2f2f2" class="m_1313588667216914166mobMargin" style="font-size:0px"></td>
        </tr>
        </tbody>
    </table>
</div>


</body>
</html>
EOF;
        return $output;
    }

    function show_product_items_email($trx_id = 0)
    {
        global $CMS, $DB;

        $output = "";

        $data['item']['product'] = $CMS->transactions->getItemAll($trx_id);

        if($data['item']['product'])
        {
            $output .= <<<EOF
            
            <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0"
       style="width:100% !important;font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif;color:#656565; ">
            <tbody>
            <tr style="font-weight: bold">
                <th width="25%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_product_service']}</th>
                <th width="25%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_price']}</th>
                <th width="10%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_quantity']}</th>
                <th width="15%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_discount_value']}</th>
                <th width="10%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_tax']}</th>
                <th width="15%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_amount']}</th>
            </tr>
EOF;

            $product_total = 0;

            foreach($data['item']['product'] as $k => $product)
            {
                $product_total += $product['tri_total'];

                $cycle_type = $product['tri_cycle_type'] == 0 ? $CMS->lang['cycle_type_0'] : "{$product['tri_cycle']} {$CMS->lang['cycle_type_'.$product['tri_cycle_type']]}";

                $index = $k+1;

                $output .= <<<EOF
            <tr>
                <td style="font-size:12px;height:28px;text-align:left;">{$product['tri_name']}</td>
                <td style="font-size:12px;height:28px;text-align:left;">
                    <span style="display:inline">{$CMS->class->input->currency($product['tri_price'])} x {$cycle_type}</span>
                </td>
                <td style="font-size:12px;height:28px;text-align:left;">{$product['tri_quantity']}</td>
                <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->currency($product['tri_total_discount'])}</td>
                <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->number($product['tri_tax'])}</td>
                <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->currency($product['tri_total'])}</td>
            </tr>
EOF;
                }


                $output .= <<<EOF
            </tbody>
        </table>
EOF;
        }

        return $output;
    }

    function show_asset_items_email($trx_id = 0)
    {
        global $CMS, $DB;


        $output = "";

        $data['item']['asset'] = $CMS->transactions->getItemAll($trx_id, 'asset');

        if($CMS->vars['addon_goods_enable'] == 0)
        {
            return $output;
        }

        if($data['item']['asset'])
        {
            //asset
            $output .= <<<EOF
<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0"
       style="width:100% !important;font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif;color:#656565; ">
            <tbody>
            <tr style="font-weight: bold">
                <th width="25%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_asset']}</th>
                <th width="25%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_price']}</th>
                <th width="10%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_quantity']}</th>
                <th width="15%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_discount_value']}</th>
                <th width="10%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_tax']}</th>
                <th width="15%" style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">{$CMS->lang['table_amount']}</th>
            </tr>
EOF;

            $asset_total = 0;

            foreach($data['item']['asset'] as $k => $asset)
            {
                $asset_total += $asset['tri_total'];

                $index = $k+1;

                $output .= <<<EOF
                <tr>
                    <td style="font-size:12px;height:28px;text-align:left;">{$asset['tri_name']}</td>
                    <td style="font-size:12px;height:28px;text-align:left;">
                        <span style="display:inline">{$CMS->class->input->currency($asset['tri_price'])}</span>
                    </td>
                    <td style="font-size:12px;height:28px;text-align:left;">{$asset['tri_quantity']}</td>
                     <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->currency($asset['tri_total_discount'])}</td>
                    <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->number($asset['tri_tax'])}</td>
                    <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->currency($asset['tri_total'])}</td>
                </tr>
EOF;
            }


            $output .= <<<EOF
            </tbody>
        </table>
EOF;
        }

        return $output;
    }

    function show_person_email($data=[])
    {
        global $CMS;

        if($data['cus_type'] == 1)
        {
            $output = <<<EOF
            <span style="color:#333333;font-weight:bold">{$CMS->lang['cus_type_1']}</span>
            <br>
            <span style="display:inline">{$data['cus_info']['cus_full_name']}<br></span>
            <span style="display:inline">{$data['trx_billing_address']}<br></span>
EOF;
        }
        else if($data['cus_type'] == 2)
        {
            $output = <<<EOF
            <span style="color:#333333;font-weight:bold">{$CMS->lang['cus_type_2']}</span>
            <br>
            <span style="display:inline">{$data['supplier_info']['supplier_name']}<br></span>
            <span style="display:inline">{$data['trx_billing_address']}<br></span>
EOF;
        }
        else if($data['cus_type'] == 3)
        {
            $output = <<<EOF
            <span style="color:#333333;font-weight:bold">{$CMS->lang['cus_type_3']}</span>
            <br>
            <span style="display:inline">{$data['assign_info']['user_display_name']}<br></span>
            <span style="display:inline">{$data['trx_billing_address']}<br></span>
EOF;
        }

        return $output;
    }

    function show_receive_info_email($data=[])
    {
        global $CMS, $DB;

        $data = $CMS->transactions->convertReceiveInvoiceInfo($data);

        if(!$data) return false;

        $subtype = $CMS->input['type'] == 1 ? 2 : 7;

        $output = <<<EOF
<table width="100%" style="border: #eaeced solid thin; padding: 5px; margin-bottom: 5px; font-family: 'Segoe UI',sans-serif,Arial, Helvetica, sans-serif; color: #656565;">
    <tbody>
        <tr>
            <th style="color: #58b3fe; font-size: 14px; text-align: left;">Related transactions</th>
        </tr>
        <tr>
            <td>   
            <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0"
       style="width:100% !important;font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif;color:#656565; ">
                <tbody>
                <tr>
                    <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">
                        {$CMS->lang['trx_subtype_0'.$subtype]}
                    </th>
                    <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">
                        {$CMS->lang['created_at']}
                    </th>
                    <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">
                        {$CMS->lang['trx_original_2']}
                    </th>
                    <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">
                        {$CMS->lang['trx_receive_amount']}
                    </th>
                    <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b">
                        {$CMS->lang['trx_status']}
                    </th>
                </tr>
EOF;
                    $total = 0;
                    $sum_total = 0;

                    foreach($data as $invoice_info)
                    {
                        $invoice_info = $CMS->transactions->convertvalue($invoice_info);

                        $total += $invoice_info['receive_amount'];
                        $sum_total += $invoice_info['trx_total'];

                        $output .= <<<EOF
                            <tr>
                                <td style="font-size:12px;height:28px;text-align:left;">{$invoice_info['trx_code']}</td>
                                <td style="font-size:12px;height:28px;text-align:left;">
                                    <span style="display:inline">{$invoice_info['trx_time_c']}</span>
                                </td>
                                <td style="font-size:12px;height:28px;text-align:left;">{$invoice_info['trx_total_tax_c']}</td>
                                <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->currency($invoice_info['data_bk']['receive_amount'])}</td>
                                <td style="font-size:12px;height:28px;text-align:left;">{$invoice_info['trx_status_c']}</td>
                            </tr>
EOF;

                    }

                    $output .= <<<EOF
                </tbody>
            </table>
            </td>
        </tr>
    </tbody>
</table>
EOF;

        return $output;
    }

    function show_invoices_info_email($data=[])
    {
        global $CMS, $DB;

        $data = $CMS->transactions->convertReceiveInvoiceInfo($data);

        if(!$data) return false;

        $subtype = $CMS->input['sub'] == 7 ? 5 : 1;

        $output = <<<EOF
                    <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0"
       style="width:100% !important;font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif;color:#656565; ">
                        <thead>
                        <tr>
                            <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b" width="10%">{$CMS->lang['trx_subtype_0'.$subtype]}</th>
                            <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b" width="20%">{$CMS->lang['created_at']}</th>
                            <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b" width="5%">{$CMS->lang['trx_status']}</th>
                            <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b" width="10%">{$CMS->lang['trx_original_2']}</th>
                            <th style="color:#414c4b;font-size:10px;height:28px;border-top:1px solid #eaeced;text-align:left;border-bottom:1px solid #414c4b" width="10%">{$CMS->lang['trx_payment_2']}</th>
                        </tr>
                        </thead>
                        <tbody>
EOF;

        $total = 0;
        $sum_total = 0;

        foreach($data as $invoice_info)
        {
            $invoice_info = $CMS->transactions->convertvalue($invoice_info);
            $total += $invoice_info['receive_amount'];
            $sum_total += $invoice_info['trx_total'];

            $output .= <<<EOF
                    <tr>
                        <td style="font-size:12px;height:28px;text-align:left;">{$invoice_info['trx_invoice_no_c']}</td>
                        <td style="font-size:12px;height:28px;text-align:left;">{$invoice_info['trx_time_c']}</td>
                        <td style="font-size:12px;height:28px;text-align:left;">{$invoice_info['trx_status_c']}</td>
                        <td style="font-size:12px;height:28px;text-align:left;">{$invoice_info['trx_total_tax_c']}</td>
                        <td style="font-size:12px;height:28px;text-align:left;">{$CMS->class->input->currency($invoice_info['data_bk']['receive_amount'])}</td>
                    </tr>
EOF;

        }


        $output .= <<<EOF
                        </tbody>
                    </table>
EOF;

        return $output;
    }
}
?>