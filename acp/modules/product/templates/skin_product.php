<?php

class skin_product
{

    /**
     * Product
     * @return string
     */

    public function head()
    {
        global $CMS, $DB, $member;

        $p_id_search = isset($CMS->input['p_id']) ? $CMS->input['p_id'] : '';
        $p_type_search = isset($CMS->input['p_type']) ? $CMS->input['p_type'] : '';

        // Check type
        if (isset($CMS->input['p_type']) AND $CMS->input['p_type'] == 0) {
            $active_0 = " active ";
            $active_all = " ";
            $active_1 = " ";
        } elseif (isset($CMS->input['p_type']) AND $CMS->input['p_type'] == 1) {
            $active_1 = " active ";
            $active_all = " ";
            $active_0 = " ";
        } else {
            $active_1 = " ";
            $active_all = " active ";
            $active_0 = " ";
        }


        $p_statup_search = isset($CMS->input['p_status']) ? $CMS->input['p_status'] : '';
        $p_group_search = isset($CMS->input['p_group']) ? $CMS->input['p_group'] : '';
        $p_manufacture_search = isset($CMS->input['p_manufacture']) ? $CMS->input['p_manufacture'] : '';

        $p_supplier_search = isset($CMS->input['p_supplier']) ? $CMS->input['p_supplier'] : '';

        $option_p_supplier = "<option value=''>{$CMS->lang['select_supplier']}</option>";
        $supplier = $CMS->supplier->get_list_supplier(1);

        foreach ($supplier as $s) {

            if ($s['supplier_id'] == $p_supplier_search AND $p_supplier_search != "") {
                $option_p_supplier .= "<option value='{$s['supplier_id']}' selected>{$s['supplier_name']}</option>";
            } else {

                $option_p_supplier .= "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
            }
        }


        $option_p_type_search = "<option value=''>{$CMS->lang['select']}</option>";
        for ($i = 0; $i <= 1; $i++) {
            if ($i == $p_type_search && $p_type_search != '') {
                $option_p_type_search .= "<option value='{$i}' selected>{$CMS->lang['p_type_0'.$i]}</option>";
            } else {
                $option_p_type_search .= "<option value='{$i}'>{$CMS->lang['p_type_0'.$i]}</option>";
            }
        }
        $option_p_statup_search = "<option value=''>-- {$CMS->lang['p_status']} --</option>";
        for ($i = 0; $i <= 2; $i++) {
            if ($i == $p_statup_search && $p_statup_search != '') {
                $option_p_statup_search .= "<option value='{$i}' selected>{$CMS->lang['p_statup_0'.$i]}</option>";
            } else {
                $option_p_statup_search .= "<option value='{$i}'>{$CMS->lang['p_statup_0'.$i]}</option>";
            }
        }
       
     
        $option_p_group_search = "<option value=''>-- {$CMS->lang['p_product_group']} --</option>";
        $group = $CMS->product_group->getAllFull($CMS->input['p_type']);

        // Level 1
        foreach ($group as $g) 
        {
            $selected = $p_group_search == $g['product_group_id'] ? 'selected' : "";
            $option_p_group_search .= "<option value='{$g['product_group_id']}' {$selected}>{$g['product_group_name']}</option>";

            // Level 2
            if (count($g['data_item']) > 0) 
            {
                foreach ($g['data_item'] as $g2) 
                {
                    $selected2 = $p_group_search == $g2['product_group_id'] ? 'selected' : "";
                    $option_p_group_search .= "<option value='{$g2['product_group_id']}' {$selected2}> |__{$g2['product_group_name']}</option>";

                    // Level 3
                    if (count($g2['data_item']) > 0) 
                    {
                        foreach ($g2['data_item'] as $g3) 
                        {
                            $selected3 = $p_group_search == $g3['product_group_id'] ? 'selected' : "";
                            $option_p_group_search .= "<option value='{$g3['product_group_id']}' {$selected3}> &nbsp;&nbsp;&nbsp;|__{$g3['product_group_name']}</option>";

                            // Level 4
                            if (count($g3['data_item']) > 0) 
                            {
                                foreach ($g3['data_item'] as $g4) 
                                {
                                    $selected4 = $p_group_search == $g4['product_group_id'] ? 'selected' : "";
                                    $option_p_group_search .= "<option value='{$g4['product_group_id']}' {$selected4}> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__{$g4['product_group_name']}</option>";
                                }
                            }
                        }
                    }
                }
            }
        }
 
        $option_p_manufacture_search = "<option value=''>--{$CMS->lang['p_manufacture']}--</option>";
        $manufacture = $CMS->manufacture->getAll();
        foreach ($manufacture as $m) {
            if ($m['manufacture_id'] == $p_manufacture_search && $p_manufacture_search != '') {
                $option_p_manufacture_search .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
            } else {
                $option_p_manufacture_search .= "<option value='{$m['manufacture_id']}'>{$m['manufacture_name']}</option>";
            }
        }

        $out = '';

        $p_name_convert = urldecode($CMS->input['p_name']);

        $export_type = $CMS->input['site'] == 'product' ? 0 : 1;

        $out .= <<<EOF
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_product.js?07092014"></script>
<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['p_title']}</h3>
		<figure class="pull-right right">
			<div class="search">
				<form method="post" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=search" style="display:inline-block">

					<input type="submit" class="fa-input" value="&#xf002;">
					<input type="text" name="p_quick_search" autocomplete="off" minlength="2" maxlength="64" id="p_quick_search" p_type="{$CMS->input['p_type']}" style="position: :relative;" placeholder="{$CMS->lang['gsearch_quick']}" value="{$p_name_convert}">
					<div id="suggesstion-box" class="box_result_find" style="display:none"></div>
				</form>
				<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
			</div>
			
			{$CMS->global->importExportData('product', "&export_type={$export_type}", 1)}
			
			<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=add" title="" class="add_bill">{$CMS->lang['p_add_button']}</a>
			<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
		</figure>
	</figure>

<section class="search_adv" >
<form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=search" >
	<figure class="box-typical box-typical box-typical-padding border">
			<h5>{$CMS->lang['gsearch_advance']}</h5>
			<ul class="input_li row match-height">
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input   name="p_id_search" id="p_id_search" type="number" class="form-control" placeholder="{$CMS->lang['p_id']}" value="{$p_id_search}" >
					</p>
				</li>

				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<input  name="p_name_search" id="p_name_search" type="text" class="form-control" placeholder="{$CMS->lang['p_name']}" value="{$p_name_search}" >
					</p>
				</li>
 
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<select class="form-control select2" name="p_group_search" id="p_group_search" >
						{$option_p_group_search}
						</select>
					</p>
				</li>
				
				<li class="col-xl-3 col-md-3 col-sm-6">
					<p class="form-control-static">
						<select class="form-control select2" name="p_supplier_search" id="p_supplier_search" >
						{$option_p_supplier}
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
 </form>	
  
</section>
 
EOF;
        $curTab = "";
        if ($p_type_search == 1) // Check loai dịc vụ thi hien thi tabs
        {
            if ($CMS->input['site'] == "product_group") {
                $curTab = 'product_group';
            }
            else if(($CMS->input['commission'] == 1))
            {
                $curTab = 'commission';
            }
            else {
                $curTab = 'service';
            }

            $tab_active[$curTab] = 'active';

            $out .= <<<EOF
		<section class="tabs-section">
		    <div class="tabs-section-nav tabs-section-nav-inline">
		        <ul class="nav" role="tablist">
		            <li class="nav-item">
		                <a class="nav-link {$tab_active['service']}" href="{$CMS->vars['root_domain']}/?site=service" >
		                    {$tpl->lang_list_service}
		                </a>
		            </li>
		            <li class="nav-item">
		                <a class="nav-link {$tab_active['product_group']}" href="{$CMS->vars['root_domain']}/?site=product_group&pg_type=1">
		                    {$tpl->lang_list_category_service}
		                </a>
		            </li>
EOF;

            if($CMS->vars['enabled_commission'])
            {
                $out .= <<<EOF
		            <li class="nav-item">
		                <a class="nav-link {$tab_active['commission']}" href="{$CMS->vars['root_domain']}/?site=service&commission=1">
		                    {$CMS->lang['gcommission']}
		                </a>
		            </li>
EOF;
            }


            $out .= <<<EOF
		        </ul>
		    </div><!--.tabs-section-nav-->
		</section><!--.tabs-section-->
	 
EOF;

        } else {

            if ($CMS->input['site'] == "product_group") {
                $curTab = 'product_group';
            }
            else if(($CMS->input['commission'] == 1))
            {
                $curTab = 'commission';
            }
            else {
                $curTab = 'product';
            }

            $tab_active[$curTab] = 'active';

            $out .= <<<EOF

		<section class="tabs-section">
		    <div class="tabs-section-nav tabs-section-nav-inline">
		        <ul class="nav" role="tablist">
		            <li class="nav-item">
		                <a class="nav-link {$tab_active['product']}" href="{$CMS->vars['root_domain']}/?site=product" >
		                    {$CMS->lang['list_product']}
		                </a>
		            </li>
		            <li class="nav-item">
		                <a class="nav-link {$tab_active['product_group']}" href="{$CMS->vars['root_domain']}/?site=product_group&pg_type=0">
		                    {$CMS->lang['list_category_product']}
		                </a>
		            </li>
EOF;

            if($CMS->vars['enabled_commission'])
            {
                $out .= <<<EOF
		            <li class="nav-item">
		                <a class="nav-link {$tab_active['commission']}" href="{$CMS->vars['root_domain']}/?site=product&commission=1">
		                    {$CMS->lang['gcommission']}
		                </a>
		            </li>
EOF;
            }


            $out .= <<<EOF
		        </ul>
		    </div><!--.tabs-section-nav-->
		</section><!--.tabs-section-->
	 
EOF;
        }

        $out .= <<<EOF
{$CMS->global->languageTab('langTab')}
<form name="form_product" id="form_product" method="POST" action="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"  >
<section class="add_table">
 
			<div class="data_table">
EOF;

        if ($_SESSION['is_mobile'] == true) {
            $out .= <<<EOF
            	<div>
					<table id="example" class="display table table_cus" cellspacing="0" width="100%">
						<thead>
						<tr>
								<th data-orderable="false" data-sortable="false" width="5%"></th>
								 <th data-orderable="false" data-sortable="false" width="1%" >
				                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_product');" id="checkall">
				                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
				                   <label></label>
				                    </div>
				                  </th>
								<th  width="15%" data-sortable="false" >{$CMS->lang['p_name']}</th>
EOF;

            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
								<th width="10%"  >{$CMS->lang['p_barcode']}</th>
EOF;
            }
            $out .= <<<EOF
								<th data-orderable="false" width="3%">{$CMS->lang['p_code_short']}</th>
								<th width="10%" data-sortable="true">{$CMS->lang['p_category']}</th>
								<th width="10%" data-sortable="true">{$CMS->lang['p_price']}</th>
								<th width="10%" data-sortable="true">{$CMS->lang['p_price_sell']}</th>
EOF;

            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
									<th width="10%" data-orderable="false" >{$CMS->lang['p_inventory']}</th>	
EOF;
            }
            $out .= <<<EOF
							 
								<th data-orderable="false" width="6%"></th>
						</tr>
						</thead>
						<tbody>
EOF;
        } else {
            $out .= <<<EOF
            <div class="table-responsive">
					<table id="example" class="display table table_cus" cellspacing="0" width="100%">
						<thead>
						<tr>
						    <th data-orderable="false" data-sortable="false" width="3%">
				                    <div style="margin-right: 10px;" class="checkbox checkbox-only" onclick="javascript:form_checkall('form_product');" id="checkall">
				                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
				                   <label></label>
				                    </div>
				            </th>
								<th width="3%">{$CMS->lang['p_code_short']}</th>
								<th width="15%" data-orderable="true" >{$CMS->lang['p_name']}</th>
EOF;

            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
								<th width="10%"  >{$CMS->lang['p_barcode']}</th>
EOF;
            }
            $out .= <<<EOF
								<th width="10%" data-sortable="true">{$CMS->lang['p_category']}</th>
								<th width="10%"  >{$CMS->lang['p_price']}</th>
									<th width="10%" data-sortable="false" data-orderable="false">{$CMS->lang['p_price_sell']}</th>
EOF;

            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
									<th width="10%" data-orderable="false" >{$CMS->lang['p_inventory']}</th>	
EOF;
            }
            $out .= <<<EOF
							 
						 		
								<th data-orderable="false" width="6%"></th>
						</tr>
						</thead>
						<tbody>
EOF;

        }


        return $out;
    }

    public function foot()
    {
        global $CMS, $DB, $member;
        $out = <<<EOF

							 
					</tbody>
				</table>
			</div>
		</div><!--.box-typical-body-->
		<div class="fuction_table">
				<div class="pull-left">

					<p class="form-control-static ">
						{$CMS->product->action_control}

					</p>
					 

				</div>
				<nav class="pull-right">
				   {$CMS->product->show_page}

				</nav>
		 </div>

 
	 </section>
	 
<input type="hidden" name="data_cnt" value="{$CMS->product->record_cnt}">

</form>	
<style type="text/css">.table-responsive { overflow-x: initial; }</style>
	 
</section>
{$CMS->global->print_barcode_popup()}
{$CMS->global->preview_barcode_popup()}
EOF;

        if ($_SESSION['is_mobile'] == true) {
            $out .= <<<EOF
				 <script>
						$(function() {
							$('#example').DataTable({
								 order: [],
							language: {
								      emptyTable: '{$CMS->lang['no_result']}: {$p_name_convert}'
							 },		 
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

        } else {

            if ($CMS->input['p_name'] != "") {
                $p_name_convert = urldecode($CMS->input['p_name']);
                $out .= <<<EOF
							
							 <script>
								$(function() {
								$('#example').DataTable({
								language: {
								      emptyTable: '{$CMS->lang['no_result']}: {$p_name_convert}'
								 },
	 							 order: [],
						 		  paging: false,
									  searching: false,
									  info: false
								});
							});
							</script>

EOF;

            } else {
                $out .= <<<EOF
							 
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


            $out .= <<<EOF
EOF;
        }
        $out .= <<<EOF
 <script src="{$CMS->vars['js_acp']}/product.js"></script>
<script language="javascript">arrange_setup("{$CMS->product->arrange_data}");</script>
EOF;
        return $out;
    }

    public function mid($data = null)
    {
        global $CMS, $DB, $member;
        $data['product_name_bk'] = $CMS->class->editor->substr($data['product_name'], 0, 22);

        $out .= <<<EOF
			<tr>
EOF;
        $price_tr = $data['product_price_sell'] ? $data['product_price_sell'] : $data['product_price'];

        if ($_SESSION['is_mobile'] == true) {
            $out .= <<<EOF
						<td></td>
						<td>
						    <div class="checkbox checkbox-only">
		                      <input type="checkbox"  name="id_{$data['record_cnt']}" id="id_{$data['record_cnt']}" value="{$data['product_id']}"/>
		                      <input type="hidden" name="name[_{$data['record_cnt']}]" value="{$data['product_name']}" />

                        	<input type="hidden" name="barcode[_{$data['record_cnt']}]" value="{$data['product_barcode']}" />
                        	<input type="hidden" name="price[_{$data['record_cnt']}]" value="{$price_tr}" />
						                      <label for="id_{$data['record_cnt']}"></label>
						     </div>
						  </td>
						<td class="short_info_td">
 
							<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$data['product_id']}" title="{$data['product_name']}">
									{$data['product_name_bk']}
		 
						</td>
EOF;

            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
							<td>
								 {$data['product_barcode']}	 
							</td>
EOF;
            }
            $out .= <<<EOF
		 				<td>
							 {$data['product_code']}
							 
						</td>
				 		<td>
							 {$data['product_group_c']}
							 
						</td>
						<td>
							{$data['product_price_c']}
						</td>
						<td>
							{$data['product_price_sell_c']}
						</td>

EOF;

            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
						<td>
							{$data['ass_inventory']}
			               </td>	
EOF;
            }
            $out .= <<<EOF
 						
						
EOF;

        } else {
        	$name_show = is_array($data['product_name']) ? $data['product_name'][$CMS->vars['default_language']] :$data['product_name']; 
            $out .= <<<EOF

				
					<td>
					    <div style="margin-right: 18px;" class="checkbox checkbox-only">
	                      <input type="checkbox"  name="id_{$data['record_cnt']}" id="id_{$data['record_cnt']}" value="{$data['product_id']}"/>
	                      <label for="id_{$data['record_cnt']}"></label>
					    </div>
					    <input type="text" maxlength="3" style="width:30px;border: solid 1px rgba(197,214,222,.7);box-shadow: none;border-radius: .2rem;text-align:center" name="order_{$data['product_id']}" id="order_{$data['product_id']}" value="{$data['product_order']}"/>          
					     <input type="hidden" name="name[_{$data['record_cnt']}]" value="{$name_show}" />
                        <input type="hidden" name="barcode[_{$data['record_cnt']}]" value="{$data['product_barcode']}" />
                        <input type="hidden" name="price[_{$data['record_cnt']}]" value="{$price_tr}" />
					</td>
					<td>
							 {$data['product_code']}
					</td>
					<td style="white-space: normal;">
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF
                
                    <span class="langTab" lang="{$langCode}">
                        <a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$data['product_id']}"  title="{$data['product_name'][$langCode]}">
                                {$data['product_name'][$langCode]}
                        </a>
					</span>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
               
					<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$data['product_id']}"  title="{$data['product_name']}">
							{$data['product_name_bk']}
					</a>
EOF;
        }

$out .=<<<EOF
				<span class="btn-inline btn-outline" style="float: left; margin: 0 5px">{$data['product_type_c']}</span>
				<span class="btn-inline btn-outline" style="float: left; margin: 0 5px">{$data['product_show_bk']}</span>
			</td>
EOF;


            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
							<td>
								 {$data['product_barcode']}	 
							</td>
EOF;
            }
            $out .= <<<EOF
					 <td>
							 {$data['product_group_c']}	 
					 </td>					 
EOF;
        }
        $out .= <<<EOF
						 
EOF;
        if ($_SESSION['is_mobile'] == false) {
            $out .= <<<EOF
						 
						<td>
							{$data['product_price_c']} 
						</td>
						<td>
							{$data['product_price_sell_c']}
						</td>
EOF;

            if ($CMS->input['site'] == "product") {
                $out .= <<<EOF
									<td>
										{$data['ass_inventory']}
		 			               </td>	
EOF;
            }
        }

     
        $out .= <<<EOF

					<td align="center">
						<div class="dropdown">
                            <button class="btn dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dd-header-add" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 30px, 0px); top: 0px; right: 0px; will-change: transform;">
EOF;
        if (!defined("is_web_us")) {
            if ($CMS->permit['product_is_root'] || $CMS->permit['product_preview_barcode']) {
                $out .= <<<EOF
                    <a onclick="print_barcode_popup('{$data['product_id']}')"  class="dropdown-item"><i class="fa fa-barcode"></i> {$CMS->lang['title_print_barcode']}</a>
EOF;

            }
        }


        if ($CMS->permit['order_add'] == 1) {
            $out .= <<<EOF
							<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=order_add&p_id={$data['product_id']}" title="{$CMS->lang['title_add_order']}" class="dropdown-item">
                            <i class="fa fa-cart-plus"></i> {$CMS->lang['title_add_order']}</a>
EOF;
        }

        if ($CMS->permit['product_edit'] == 1) {
            $out .= <<<EOF
							<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$data['product_id']}" title="" class="dropdown-item"><i class="fa fa-edit"></i> {$CMS->lang['title_edit_ps']}</a>
EOF;
        }

        if ($CMS->permit['product_delete'] == 1) {
            $out .= <<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=delete&id={$data['product_id']}');" class="dropdown-item"><i class="fa fa-trash-o"></i> {$CMS->lang['title_delete_product']}</a>		 
EOF;
        }
        $out .= <<<EOF


                            </div>
                        </div>
					</td>
				</tr>
EOF;
        return $out;
    }

    public function none()
    {
        global $CMS, $DB, $member;

    }

    public function add()
    {
        global $CMS, $DB, $member;
 

        $product_commission_type = $CMS->input['product_commission_type']*1;
        $product_commission_value = $CMS->input['product_commission_value']*1;

 
        //Check is web USA -> hide field
        $hide_field = "style='' ";
        if (defined("is_web_us") == true) {
            $hide_field = " style='display:none' ";
            $hide_field2 = "";
        } else {
            $hide_field2 = " style='display:none' ";
            $hide_field = "";
        }

        // Dùng cho bên service
        if ($CMS->input['site'] == "product") {
            $hide_field2 = " style='display:none' ";
        }
 
        $p_type = $CMS->input['p_type'];

		if($CMS->vars['addon_goods_enable'] == 1 AND $p_type == 0)
		{
        	list($row,$list) = $CMS->store->get_list_store($data['store_id']);
        }
 
        $user = $CMS->user->get_info($member['user_id']);
        $draft_product = json_decode($user['user_draft_product'], true);

        if($CMS->vars['translations'])
        {
            $p_name = isset($CMS->input['p_name']) ? $CMS->input['p_name'] : [];
            $p_description = isset($CMS->input['p_description']) ? $CMS->input['p_description'] : [];
            $p_information_1 = isset($CMS->input['p_information_1']) ? $CMS->input['p_description'] :[];
            $p_information_2 = isset($CMS->input['p_information_1']) ? $CMS->input['p_description'] : [];
        }
        else
        {
            $p_name = isset($CMS->input['p_name']) ? $CMS->input['p_name'] : '';
            $p_description = isset($CMS->input['p_description']) ? $CMS->input['p_description'] : "";
            $p_information_1 = isset($CMS->input['p_information_1']) ? $CMS->input['p_description'] : "";
            $p_information_2 = isset($CMS->input['p_information_1']) ? $CMS->input['p_description'] : "";
        }

        $p_sku = isset($CMS->input['p_sku']) ? $CMS->input['p_sku'] : '';

        $p_barcode = isset($CMS->input['p_barcode']) ? $CMS->input['p_barcode'] : '';
        $p_img_alt = isset($CMS->input['p_img_alt']) ? $CMS->input['p_img_alt'] : '';

        $p_status = isset($CMS->input['p_status']) ? $CMS->input['p_status'] : 0;
        $p_show = isset($CMS->input['p_show']) ? $CMS->input['p_show'] : 1;
        $p_stock_available = isset($CMS->input['p_stock_available']) ? $CMS->input['p_stock_available'] : 1;
        $p_show_instock = isset($CMS->input['p_show_instock']) ? $CMS->input['p_show'] : 0;
        $p_first_remain = isset($CMS->input['p_first_remain']) ? $CMS->input['p_first_remain'] : 0;

        $p_manufacture = isset($CMS->input['p_manufacture']) ? $CMS->input['p_manufacture'] : $draft_product['product_manufacture'];

        $p_supplier = isset($CMS->input['p_supplier']) ? $CMS->input['p_supplier'] : $draft_product['product_supplier'];

        $p_product_group = isset($CMS->input['p_product_group']) ? $CMS->input['p_product_group'] : $draft_product['product_group'] != "" ? $draft_product['product_group'] : $CMS->input['p_product_group'];

        $p_product_option = isset($CMS->input['p_product_option']) ? $CMS->input['p_product_option'] : $draft_product['product_option'] != "" ? $draft_product['product_option'] : $CMS->input['p_product_option'];




        $p_cycle = isset($CMS->input['p_cycle']) ? $CMS->input['p_cycle'] : 0;
        $p_tax = isset($CMS->input['p_tax']) ? $CMS->input['p_tax'] : 0;
        $p_price = isset($CMS->input['p_price']) ? $CMS->input['p_price'] : 0;
        $p_price_sell = isset($CMS->input['p_price_sell']) ? $CMS->input['p_price_sell'] : 0;
        $p_price_old = isset($CMS->input['p_price_old']) ? $CMS->input['p_price_old'] : 0;
        $p_price_original = isset($CMS->input['p_price_original']) ? $CMS->input['p_price_original'] : 0;

        $p_guarantee = isset($CMS->input['p_guarantee']) ? $CMS->input['p_guarantee'] : "";
        $p_order = isset($CMS->input['p_order']) ? intval($CMS->input['p_order']) : "";
        $p_up = isset($CMS->input['p_up']) ? $CMS->input['p_up'] : "";


        // Check hinh upload
        if (isset($_FILES['p_image'])) {
            $base64_image = $src_image_upload = $CMS->input['base64_image'];
            $style_display = " style='display:block' ";

        }

        $option_p_made_in = "<option value=''>{$CMS->lang['select']}</option>";
        $country = $CMS->country->country();
        foreach ($country as $k => $v) {
            if ($k == $p_made_in) {
                $option_p_made_in .= "<option value='{$k}' selected>{$v}</option>";
            } else {
                $option_p_made_in .= "<option value='{$k}'>{$v}</option>";
            }
        }
        $option_p_status = "";
        for ($i = 0; $i <= 2; $i++) {
            if ($i == $p_status) {
                $option_p_status .= " <div class='radio w25'><input type='radio' checked  name='p_status' id='radio-stt-{$i}' value='{$i}'><label for='radio-stt-{$i}'>{$CMS->lang['p_statup_0'.$i]}</label></div>";
            } else {
                $option_p_status .= " <div class='radio w25'><input type='radio'    name='p_status' id='radio-stt-{$i}' value='{$i}'><label for='radio-stt-{$i}'>{$CMS->lang['p_statup_0'.$i]}</label></div>";
            }
        }


        $option_p_show = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $p_show) {
                $option_p_show .= "   <div class='radio w25'><input type='radio' checked  name='p_show' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            } else {
                $option_p_show .= "   <div class='radio w25'><input type='radio'    name='p_show' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            }
        }

        $option_p_show_instock = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $p_show_instock) {
                $option_p_show_instock .= "   <div class='radio w25'><input type='radio' checked  name='p_show_instock' id='radio-p-show-instock-{$i}' value='{$i}'><label for='radio-p-show-instock-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            } else {
                $option_p_show_instock .= "   <div class='radio w25'><input type='radio'    name='p_show_instock' id='radio-p-show-instock-{$i}' value='{$i}'><label for='radio-p-show-instock-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            }
        }

        $option_p_stock_available = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $p_stock_available) {
                $option_p_stock_available .= "   <div class='radio w25'><input type='radio' checked  name='p_stock_available' id='radio-p-stock-available-{$i}' value='{$i}'><label for='radio-p-stock-available-{$i}'>{$CMS->lang['p_stock_available_'.$i]}</label></div>";
            } else {
                $option_p_stock_available .= "   <div class='radio w25'><input type='radio'    name='p_stock_available' id='radio-p-stock-available-{$i}' value='{$i}'><label for='radio-p-stock-available-{$i}'>{$CMS->lang['p_stock_available_'.$i]}</label></div>";
            }
        }

        $option_p_supplier = "<option value=''>{$CMS->lang['select']}</option>";
        $supplier = $CMS->supplier->get_list_supplier(1);

        foreach ($supplier as $s) {

            if ($s['supplier_id'] == $p_supplier) {
                $option_p_supplier .= "<option value='{$s['supplier_id']}' selected>{$s['supplier_name']}</option>";
            } else {

                $option_p_supplier .= "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
            }
        }



		$option_p_manufacture ="<option value=''>{$CMS->lang['select']}</option>";
		$manufacture = $CMS->manufacture->getAll();
		foreach ($manufacture as $m) {
			if ($m['manufacture_id'] == $p_manufacture) {
				$option_p_manufacture .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
			} else {
				$option_p_manufacture .= "<option value='{$m['manufacture_id']}'>{$m['manufacture_name']}</option>";
			}
		}



        $option_p_product_group = "<option value=''>{$CMS->lang['select']}</option>";
        $group = $CMS->product_group->getAll($p_type);

        foreach ($group as $g) {

            $option_p_product_group .= "<option value='{$g['product_group_id']}'>{$g['product_group_name']}</option>";
            if (count($g['data_item']) > 0) {
                foreach ($g['data_item'] as $key => $value) {
                    $option_p_product_group .= "<option value='{$value['product_group_id']}'> |__{$value['product_group_name']}</option>";
                }
            }
        }


        $option_p_product_option = "";
    
        for ($i = 1; $i <= 5; $i++) {
            if ($i == $p_product_option) {
                $option_p_product_option .= "
                	<div class='checkbox'>
								<input type='checkbox' name='p_product_option[]' value='{$i}' id='check-{$i}' checked=''>
								<label for='check-{$i}'>{$CMS->lang['product_option_'.$i]}</label>
							</div>
				";
            } else {
                $option_p_product_option .= "
   					 <div class='checkbox'>
								<input type='checkbox' name='p_product_option[]' value='{$i}' id='check-{$i}'  >
								<label for='check-{$i}'>{$CMS->lang['product_option_'.$i]}</label>
					 </div>
				";
            }
        }


        $option_p_cycle = "";
        if ($p_type != 0) {
            for ($i = 0; $i <= 2; $i++) {
                if ($i == $p_cycle) {
                    $option_p_cycle .= "<option value='{$i}' selected>{$CMS->lang['p_cycle_0'.$i]}</option>";
                } else {
                    $option_p_cycle .= "<option value='{$i}'>{$CMS->lang['p_cycle_0'.$i]}</option>";
                }
            }
        } else {
            $option_p_cycle .= "<option value='0' selected>{$CMS->lang['p_cycle_00']}</option>";
        }
        $out = '';

        $out .= <<<EOF
<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=add_do" method="POST" enctype="multipart/form-data">
<input type="hidden" name="p_type" value="{$CMS->input['p_type']}">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['p_add']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
{$CMS->global->languageTab('langTab')}
 <figure class="box-typical box-typical box-typical-padding border">
 	<div class="row">
		  <div class="col-md-6">
 
		  	<div class="row">
EOF;
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF

                <div class="col-xl-6">
				  	<fieldset class="form-group langTab" lang="{$langCode}">
						<label class="form-label" >{$CMS->lang['p_name']} <span style="color:red">(*)</span></label>
				 		 <div class="form-control-wrapper">
							<input class="form-control " type="text" name="p_name[{$langCode}]" id="p_name[{$langCode}]" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['p_name_err']}" value="{$p_name[$langCode]}">
					 	</div>
				  	</fieldset>
				</div>
        
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
            
                <div class="col-xl-6">
				  	<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['p_name']} <span style="color:red">(*)</span></label>
				 		 <div class="form-control-wrapper">
							<input class="form-control " type="text" name="p_name" id="p_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['p_name_err']}" value="{$p_name}">
					 	</div>
				  	</fieldset>
				</div>
EOF;
        }


    $out .= <<<EOF

				<div class="col-xl-6">
					 <fieldset class="form-group" {$hide_field} >
						<label class="form-label" >{$CMS->lang['p_sku']}</label>
						<input class="form-control " type="text" name="p_sku" id="p_sku" value="{$p_sku}">
					</fieldset>
			  
			  	</div>
			  	
			  	<div class="col-xl-6">
						 <fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['p_img_alt']}</label>
                            <input class="form-control " type="text" name="p_img_alt" id="p_img_alt" value="{$p_img_alt}">
						</fieldset>
			 		   
			 	</div>

			 	<div class="col-xl-6" {$hide_field}>
					 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_barcode']}</label>
						<input class="form-control " type="text" name="p_barcode" id="p_barcode" value="{$p_barcode}">
					</fieldset>
			 	</div>
			</div>
			<div class="row">
			 	<div class="col-xl-6">
			  		<fieldset class="form-group">
						<label class="form-label pull-left" >{$CMS->lang['p_avartar']}</label>
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
										<input  type="hidden" id="ufile_output_b64" name="base64_image" value="{$base64_image}" />
									  </div>
							 
								 <div class="drop-zone fileinput-button" style="height: 110px !important">
			                             
								 		   <img id="upload_img_show"  width="205" src="{$src_image_upload}" {$style_display}  />
			                              <i class="font-icon font-icon-cloud-upload-2"></i>
			                               <div class="drop-zone-caption">Drag file to upload</div>

			                                 <input type="file"  name="p_image" id="ufile" accept="image/*">
			                     </div><!--.drop-zone-->
			                  		
							 
							 
								<img class="img-responsive" src="{$pg_avartar}" style="max-width: 100%;" alt="{$data['product_group_name']}"> 
							
					</fieldset>

			  	</div>
			
			 	
			 	<div class="col-xl-6">
				 	 <fieldset class="form-group">

						<label class="form-label pull-left" >{$CMS->lang['p_product_group']} <span style="color:red">(*)</span></label>
						
				
EOF;
        if ($CMS->permit["product_group_add"] == 1) {
            $out .= <<<EOF
					 
							<a   data-size="s" class="add_new_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline">
								<i class="fa fa-plus" aria-hidden="true"></i>	
							</a>
EOF;

        }
        if ($CMS->permit["product_group_edit"] == 1) {
            $out .= <<<EOF
						<span class="box_product_group pull-right" style="margin-left:10px"></span>

							

EOF;
        }


        $out .= <<<EOF
						<div class="form-control-wrapper">
							<select class="form-control select2 select_product_group" name="p_product_group" defaultvalue="{$p_product_group}" id="p_product_group" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['p_product_group_err']}"  >
								{$option_p_product_group}
							</select>
						</div>
			
					</fieldset>
EOF;

        if (!defined("is_web_us"))
        {
        	if($CMS->input['p_type'] == 0)
        	{
        		$out .= <<<EOF
					 <fieldset class="form-group">

						<label class="form-label pull-left" >{$CMS->lang['p_product_option']}</label>
		 
						<div class="form-control-wrapper">
						 
								{$option_p_product_option}
							 
						</div>
			
					</fieldset>
EOF;
        	}
            
        }

        $out .= <<<EOF

			 	</div>
			</div>
			<div class="row match-height">
					<div class="col-xl-12"><h5 class="m-t-lg with-border">{$CMS->lang['title_info_transaction']}</h5></div>
					<div class="col-xl-6" {$hide_field}>
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_cycle']}</label>
							<select class="form-control" name="p_cycle" id="p_cycle" >
									{$option_p_cycle}
								</select>
						</fieldset>		

					</div>
					 
					<div class="col-xl-6" {$hide_field}>
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_price']}</label>

							<input class="form-control " type="text" name="p_price" id="p_price" value="{$p_price}"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
						</fieldset>			
					</div>

					<div class="col-xl-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['p_price_old']}</label>
							<input class="form-control " type="text" name="p_price_old" id="p_price_old" value="{$p_price}"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
						</fieldset>			
					</div>

					<div class="col-xl-6">
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_price_original']}</label>
							<input class="form-control " type="text" name="p_price_original" id="p_price_original" value="{$p_price_original}"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
						</fieldset>				

					</div>

					<div class="col-xl-6" {$hide_field}>
						<fieldset class="form-group mb0">
							<label class="form-label" >{$CMS->lang['p_tax']}</label>
								<select name="p_tax" class="form-control bootstrap-select">
									<option value="0">0%</option>
									<option value="10" selected>10%</option>
								</select>
						</fieldset>						
					</div>
					<div class="col-xl-6">
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_price_sell']}</label>
							<input class="form-control " type="text" name="p_price_sell" id="p_price_sell" value="{$p_price_sell}"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
						</fieldset>				

					</div>
					<div class="col-xl-6" {$hide_field2}>
						<fieldset class="form-group mb0">
							<label class="form-label" >{$CMS->lang['title_suffix_price']}</label>
							<input name="p_up" id='p_up' class="form-control" value="{$p_up}" />
						</fieldset>
					</div>
EOF;

        //Huê hồng
        if($CMS->vars['enabled_commission'])
        {
            $out .= <<<EOF
					<div class="col-xl-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['gcommission']}</label>
							 <div class="input-group">
                              <input value="{$product_commission_value}" id="product_commission_value" name="product_commission_value" type="number" step="0.01" class="form-control" aria-label="Text input with dropdown button">
                              <input value="{$product_commission_type}" id="product_commission_type" name="product_commission_type" value="0" type="hidden">
                              <div id="product_commission_dropdown" class="input-group-btn">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  %
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                  <a class="dropdown-item" value="0">%</a>
                                  <a class="dropdown-item" value="1">{$CMS->vars['currency_type']}</a>
                                </div>
                              </div>
                                <script>
                                    dropdownInput($('#product_commission_dropdown'), $('#product_commission_type'));
                                </script>
                                </div>
						</fieldset>	
					</div>
					
EOF;
        }

        if ($p_type == 1) {

            $out .= <<<EOF
					<div class="col-xl-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['staff_id']}</label>
							 <select class="select2" multiple="multiple" name="staff_id[]">
	                             {$CMS->user->load_list_user()}
	                        </select>
						</fieldset>	
					</div>
					<div class="col-xl-6" {$hide_field2}>
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_order']}</label>
							<input class="form-control" min="0" type="number" name="p_order" id="p_order" value="{$p_order}" />
						</fieldset>			
					</div>		
EOF;
    }


$out .= <<<EOF
			</div>
	  </div><!-- col-md-6 -->
		
		<div class="col-md-6" >
			 <fieldset class="form-group">
			 	<div class="row" {$hide_field}>
			 		<div class="col-lg-6">

						<label class="form-label pull-left" >{$CMS->lang['p_manufacture']}</label> 
				
				
EOF;
 					if($CMS->permit["manufacture_add"] == 1)
					{
						$out .=<<<EOF
					 
							<a   data-size="s" class="add_new_manufacture pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline">
								<i class="fa fa-plus" aria-hidden="true"></i>	
							</a>
EOF;

					}	
					if($CMS->permit["manufacture_edit"] == 1)
					{
						$out .=<<<EOF
						<span class="box_edit_manufacture pull-right" style="margin-left:10px"></span>

							

EOF;
					}

 
									
					$out .=<<<EOF


							<select class="form-control select_manufacture select2" name="p_manufacture" id="p_manufacture" >
								{$option_p_manufacture}
							</select>
							
			 		</div><!-- col-lg-6-->
			 		<div class="col-xl-6 col-lg-12">

						<label class="form-label pull-left" >{$CMS->lang['p_supplier']}</label>
				
EOF;
        if ($CMS->permit["supplier_add"] == 1) {
            $out .= <<<EOF
										 
						 
						<a   data-size="s" class="add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>	</a>
EOF;

        }

        if ($CMS->permit["supplier_edit"] == 1) {
            $out .= <<<EOF
						<span class="box_edit_supplier pull-right" style="margin-left:10px"></span>

							

EOF;
        }


        $out .= <<<EOF
					
						<select class="form-control select_supplier select2" name="p_supplier" id="p_supplier" for="change" >
								{$option_p_supplier}
							</select>	

			 		</div>	<!-- col-lg-6-->

			 		 <div class="col-xl-6 col-lg-12 mb1rem">

							
			 		</div><!-- col-lg-6-->


			 	</div><!-- class row-->
					
							
				</fieldset>
				<fieldset class="form-group">

						<label class="form-label" >{$CMS->lang['p_guarantee']}</label>
			 			<div class="form-control-wrapper">	
			 				<input class="form-control" maxlength="3" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" type="text" name="p_guarantee" id="p_guarantee" value="{$p_guarantee}"    />
			 			</div> 
				</fieldset>	
EOF;
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF

                <fieldset class="form-group langTab" lang="{$langCode}">
					<label class="form-label" >{$CMS->lang['p_description']}</label>
						<textarea name="p_description[{$langCode}]" rows="5" class="form-control">{$p_description[$langCode]}</textarea>
				</fieldset>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
            
                <fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['p_description']}</label>
						<textarea name="p_description" rows="5" class="form-control">{$p_description}</textarea>
				</fieldset>
EOF;
        }
 
        if (!defined("is_web_us"))
        {
            $out .= <<<EOF
        <fieldset class="form-group">
            <label class="form-label" >{$CMS->lang['p_gallery']}</label>
                <div class="box-typical-upload box-typical-upload-in">
                        <div class="drop-zone fileinput-button" style="width: 100%">
                            <i class="font-icon font-icon-cloud-upload-2"></i>
                            <div class="drop-zone-caption">Drag file to upload</div>
                            <input type="file" multiple name="p_gallery[]" id="list_image" class="multiple_upload" accept="image/*">
                        </div><!--.drop-zone-->
                    <p class="box_error" style="display: none;"></p>
                    <h6 class="uploading-list-title title_upload" style="display: none;">{$CMS->lang['title_note_uploading']}</h6>
                    <ul class="uploading-list list_upload">
                        
                    </ul>
                </div>
        </fieldset>
EOF;
        }


        $out .=<<<EOF
			 <fieldset class="form-group mb0">
			 	<div class="row">
			 		 
			 		<div class="col-lg-6">
			 			<label class="form-label" >{$CMS->lang['p_show']}</label>
						{$option_p_show}
			 		</div>

			 	</div><!-- row -->
			 
			</fieldset>		


EOF;

		if($CMS->vars['addon_goods_enable'] == 1 AND $p_type == 0)
		{

		  $out .= <<<EOF

			 <fieldset class="form-group mb0">
			 	<div class="row">
			    	<div class="col-xl-12"><h5 class="m-t-lg with-border">{$CMS->lang['p_store_addons']}</h5></div>
				 	<div class="col-lg-6">
				     <fieldset class="form-group">
						<label class="form-label" for="store_id">{$CMS->lang['store_id']}</label>
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
					   <select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control " data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_store']}">						
                       		{$list}
                        </select>
EOF;
                    }
                    $out .=<<<EOF
					   </fieldset>
					</div>
			 		<div class="col-lg-6">
				 		<fieldset class="form-group">
							<label class="form-label">{$CMS->lang['p_first_remain']}</label>
							<input class="form-control " type="text" name="p_first_remain" id="p_first_remain" value="{$p_first_remain}">
						</fieldset>
			 		</div>

			 	</div><!-- row --> 
			</fieldset>
			 <fieldset class="form-group mb0">
			 	<div class="row">
			 		<div class="col-lg-6">
							<label class="form-label" >{$CMS->lang['p_stock_available']}</label>
							 
							{$option_p_stock_available}
					 </div>	 
			 		<div class="col-lg-6">
							<label class="form-label" >{$CMS->lang['p_show_instock']}</label>
						 	{$option_p_show_instock}
					 </div>	 
			 	</div><!-- row -->
			</fieldset>	
EOF;

		}
		  $out .= <<<EOF

		</div><!-- col-md-6 -->
	</div><!-- row -->
EOF;

        if (!defined("is_web_us"))
        {

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF
			<div class="row langTab" lang="{$langCode}">                
            	<section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#p_information_1_{$langCode}" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_1']}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2_{$langCode}" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_2']}
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1_{$langCode}">
                        <textarea name="p_information_1[{$langCode}]" rows="5" class="editor_texarea">{$p_information_1[$langCode]}</textarea>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="p_information_2_{$langCode}">
                        <textarea name="p_information_2[{$langCode}]" rows="5" class="editor_texarea">{$p_information_2[$langCode]}</textarea>
                    </div><!--.tab-pane-->
                </div><!--.tab-content-->
			</section>
		</div>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
            	<section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#p_information_1" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_1']}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_2']}
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->
            
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1">
                        <textarea name="p_information_1" rows="5" class="editor_texarea">{$p_information_1}</textarea>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="p_information_2">
                        <textarea name="p_information_2" rows="5" class="editor_texarea">{$p_information_2}</textarea>
                    </div><!--.tab-pane-->
                </div><!--.tab-content-->
            </section>
        </div>
EOF;
        }

        }


        $out .= <<<EOF
  </figure>
</section>
			 
 <section class="add_table">
	 	 <!-- responsive-table -->

EOF;
        if ($_SESSION['is_mobile'] == true) {
            $out .= <<<EOF
				<table id="table_item" class="table_cus">
EOF;
        } else {
            $out .= <<<EOF
				<table class="responsive-table table_cus">
EOF;
        }
        $out .= <<<EOF
								<thead>
									<tr>
										<th  scope="col" width="2%"></th>
EOF;
        if ($_SESSION['is_mobile'] == false) {
            $out .= <<<EOF

										<th  scope="col" width="3%">{$CMS->lang['stt']}</th>
EOF;
        }
        $out .= <<<EOF
										
										<th  scope="col" width="15%">{$CMS->lang['product_service']}</th>
										<th  scope="col" width="12%">{$CMS->lang['p_sku']}</th>
										
										<th  scope="col" width="25%">{$CMS->lang['pg_description']}</th>
										<th  scope="col" width="7%">{$CMS->lang['stock_store_quantity']}</th>
										<th  scope="col" width="10%">{$CMS->lang['price_no_vat']}</th>
										<th  scope="col" width="7%">{$CMS->lang['vat_percent']}</th>
										<th  scope="col" width="3%"></th>
									</tr>
								</thead>	
								<tbody id="data_table">
 
								<tr class="row-grid" rowtr="last-row">
									<td scope="row"  ><i class="hidden-sm-down fa fa-th handle"></i></td>
EOF;
        if ($_SESSION['is_mobile'] == false) {
            $out .= <<<EOF

										<td data-title="{$CMS->lang['stt']}" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#1</span></td>
EOF;
        }
        $out .= <<<EOF
								 
									<td  data-title="{$CMS->lang['product_service']}" class="grid-td  " for="dgrid-2"> 
										 
										 <div class="box_container">
											<figure class="text_r"><input type="text" for="dgrid-2" check="chtd"  class="form-control find_product" autocomplete="off"  name ="sub_product_name[]"  onfocusout="return convert_sku_code(this);" /> </figure>	
						 		
											<div class="box_result_find" style="display:none" ></div>
										 </div>	
											<input type="hidden" class="product_id" name="sub_product_id[]" value=""/>

									</td>
									<td  data-title="{$CMS->lang['p_sku']}" class="grid-td" for="dgrid-3" check="chtd" >

											<figure class="text_r">  <input type="text"  for="dgrid-3" name ="sub_product_sku[]"  class="form-control" placeholder="SKU" > 
											</figure>
</td>

									<td  data-title="{$CMS->lang['pg_description']}" class="grid-td" for="dgrid-4" check="chtd" >

											<figure class="text_r">  <textarea  for="dgrid-4" name ="sub_product_description[]"  class="form-control" placeholder="{$CMS->lang['pg_description']}" style="width:250px">{$value['product_description']}</textarea>
											</figure>
</td>
									<td data-title="{$CMS->lang['stock_store_quantity']}" class="grid-td" for="dgrid-5"> 
										<figure class="text_r">
											<input for="dgrid-5"  name ="sub_product_quantity[]" type="number"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,1);" value="1" onkeyup="calculate_money_subitem_product();"  class="form-control quan_list" style="cursor:pointer;" /> 
										</figure>
</td>
									<td data-title="{$CMS->lang['price_no_vat']}" class="grid-td" for="dgrid-6">
										<figure class="text_r">
											 <input for="dgrid-6"  name ="sub_product_price[]"  onkeyup="calculate_money_subitem_product(); "   onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" value="0" class="form-control" /> 
										</figure>	 
</td>
									<td data-title="{$CMS->lang['vat_percent']}" class="grid-td" for="dgrid-7"> 
											<figure class="text_r"> 	
												<select for="dgrid-7"  class="form-control  tax_list"  name ="sub_product_tax[]"  onchange="calculate_money_subitem_product();"  >
														<option value="0">0%</option>
														<option value="10" selected="selected">10%</option>
													</select>
											</figure>	 

 
</td>
									<td class="trash" for="dgrid-8"><span class="del_row_invoid text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
								</tr>
 
								</tbody>
					</table>	 
	 </section>	
	 
 
 

 




 
		 
		<section class="add_cart_footer">

EOF;


        $url_back['list'] = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";
        $out .= $CMS->global->footer_back($url_back);


        $url_save[1]['key'] = "save_and_saveoption";
        $url_save[1]['icon'] = " fa-life-saver";
        $url_save[1]['js'] = " onclick='product_submit_saveoption();' ";
        $url_save[1]['redirect'] = "0";

        $out .= $CMS->global->footer_save("{$CMS->input['site']}", $url_save);


        $out .= <<<EOF
			

		</section>
 
		<input type="hidden" value="0" name="add_product_option"  />
		
 </form>
 


EOF;
        if ($_SESSION['is_mobile'] == true) {
            $out .= <<<EOF
					 
    <script>


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



       $('#table_item tbody').on('keyup change', '.child input, .child select, .child textarea', function(){
           var el = $(this);
           var rowIdx = el.closest('ul').data('dtr-index');
           var colIdx = el.closest('li').data('dtr-index');
           var cell = table_item.cell({ row: rowIdx, column: colIdx }).node();
          //   $('input, select, textarea', cell).val(el.val());
           $(cell).children().children().val(el.val());
           if(el.is(':selected')){ $ (cell).children().children().find("option[value='"+el.val()+"']").prop('selected', true); }

             calculate_money_subitem_product();


       });

</script>
EOF;
        }

        $out .= <<<EOF
<script>	
$(document).ready(function(){

	validate_form_custom("#form-signin_v1",".act_submit_save");
});


var module_name = "product_module";	
 // $("#form_submit, #form_submit_mobile").click(function(){
 	  
 // 	 	$("tbody#data_table tr.child").remove();
 // 	 	$("#trigger_submit").trigger("click");
 	 
 // });

 // $("#form_submit_option, #form_submit_option_mobile").click(function(){
  
 // 	$("#form-signin_v1 input[name='add_product_option']").attr('value','1');
 // 	$("#trigger_submit").trigger("click");
 // });

 
	var lang_supplier_add = "{$CMS->lang['supplier_add']}";
	var lang_supplier_edit = "{$CMS->lang['supplier_edit']}";

	var lang_manufacture_edit  = "{$CMS->lang['manufacture_edit']}";
	var lang_manufacture_add = "{$CMS->lang['manufacture_add']}";

</script>
 <script src="{$CMS->vars['js_acp']}/product.js"></script>
 
		{$this->formAddSupplier()}
 
		{$this->formAddManufacture()}
 		{$this->formAddproductgroup()}

EOF;
        return $out;
    }

    public function edit($data = null)
    {
        global $CMS, $DB, $member;

        $product_commission_type = isset($CMS->input['product_commission_type']) ? $CMS->input['product_commission_type']*1 : $data['product_commission_type'];
        $product_commission_value = isset($CMS->input['product_commission_value']) ? $CMS->input['product_commission_value']*1 : $data['product_commission_value'];

        //Check is web USA -> hide field
        $hide_field = "style='' ";
        if (defined("is_web_us") == true) {
            $hide_field = " style='display:none' ";
            $hide_field2 = "";
        } else {
            $hide_field2 = " style='display:none' ";
            $hide_field = "";
        }

        // Dùng cho bên service
        if ($CMS->input['site'] == "product") {
            $hide_field2 = " style='display:none' ";
        }


        $p_name = isset($CMS->input['p_name']) ? $CMS->input['p_name'] : $data['product_name'];

        $p_sku = isset($CMS->input['p_sku']) ? $CMS->input['p_sku'] : $data['product_sku'];
        $p_barcode = isset($CMS->input['p_barcode']) ? $CMS->input['p_barcode'] : $data['product_barcode'];

        $p_img_alt = isset($CMS->input['p_img_alt']) ? $CMS->input['p_img_alt'] : $data['product_image_alt'];

        $p_status = isset($CMS->input['p_status']) ? $CMS->input['p_status'] : $data['product_status'];


        $p_manufacture = isset($CMS->input['p_manufacture']) ? $CMS->input['p_manufacture'] : $data['product_manufacture'];

        $p_supplier = isset($CMS->input['p_supplier']) ? $CMS->input['p_supplier'] : $data['sup_id'];


        $p_product_group = isset($CMS->input['p_product_group']) ? $CMS->input['p_product_group'] : $data['product_group'];

        $p_product_option = isset($CMS->input['p_product_option']) ? $CMS->input['p_product_option'] : $data['product_option'];


        $p_type = $CMS->input['p_type'];

        $p_cycle = isset($CMS->input['p_cycle']) ? $CMS->input['p_cycle'] : $data['product_cycle'];
        $p_tax = isset($CMS->input['p_tax']) ? $CMS->input['p_tax'] : $data['product_tax'];
        $p_price = isset($CMS->input['p_price']) ? $CMS->input['p_price'] : $data['product_price'];
        $p_price_original = isset($CMS->input['p_price_original']) ? $CMS->input['p_price_original'] : $data['product_price_original'];
        $p_price_sell = isset($CMS->input['p_price_sell']) ? $CMS->input['p_price_sell'] : $data['product_price_sell'];
        $p_price_old = isset($CMS->input['p_price_old']) ? $CMS->input['p_price_old'] : $data['product_price_old'];
        $p_order = isset($CMS->input['p_order']) ? intval($CMS->input['p_order']) : $data['product_order'];
        $p_up = isset($CMS->input['p_up']) ? $CMS->input['p_up'] : $data['product_up'];


        $p_show = isset($CMS->input['p_show']) ? $CMS->input['p_show'] : $data['product_show'];
        $p_show_instock = isset($CMS->input['p_show_instock']) ? $CMS->input['p_show_instock'] : $data['product_show_instock'];
        $p_stock_available = isset($CMS->input['p_stock_available']) ? $CMS->input['p_stock_available'] : $data['product_stock_available'];

        $p_description = isset($CMS->input['p_description']) ? $CMS->input['p_description'] : $data['product_description'];

        $p_information_1 = isset($CMS->input['p_information_1']) ? $CMS->input['p_information_1'] : $data['product_information_1'];
        $p_information_2 = isset($CMS->input['p_information_2']) ? $CMS->input['p_information_2'] : $data['product_information_2'];

        $p_guarantee = isset($CMS->input['p_guarantee']) ? $CMS->input['p_guarantee'] : $data['product_guarantee_default'];


        // Check hinh upload
        if (isset($_FILES['p_image']) || $data['product_image_c']) {
            $data['base64_string'] = $CMS->input['base64_image'];
            $style_display = " style='display:block' ";

        }

        $option_p_show = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $p_show) {
                $option_p_show .= "   <div class='radio w25'><input type='radio' checked  name='p_show' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            } else {
                $option_p_show .= "   <div class='radio w25'><input type='radio'    name='p_show' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            }
        }



        $option_p_show_instock = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $p_show_instock) {
                $option_p_show_instock .= "   <div class='radio w25'><input type='radio' checked  name='p_show_instock' id='radio-p-show-instock-{$i}' value='{$i}'><label for='radio-p-show-instock-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            } else {
                $option_p_show_instock .= "   <div class='radio w25'><input type='radio'    name='p_show_instock' id='radio-p-show-instock-{$i}' value='{$i}'><label for='radio-p-show-instock-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            }
        }

        $option_p_stock_available = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $p_stock_available) {
                $option_p_stock_available .= "   <div class='radio w25'><input type='radio' checked  name='p_stock_available' id='radio-p-stock-available-{$i}' value='{$i}'><label for='radio-p-stock-available-{$i}'>{$CMS->lang['p_stock_available_'.$i]}</label></div>";
            } else {
                $option_p_stock_available .= "   <div class='radio w25'><input type='radio'    name='p_stock_available' id='radio-p-stock-available-{$i}' value='{$i}'><label for='radio-p-stock-available-{$i}'>{$CMS->lang['p_stock_available_'.$i]}</label></div>";
            }
        }


        $option_p_made_in = "<option value=''>{$CMS->lang['select']}</option>";
        $country = $CMS->country->country();
        foreach ($country as $k => $v) {
            if ($k == $p_made_in) {
                $option_p_made_in .= "<option value='{$k}' selected>{$v}</option>";
            } else {
                $option_p_made_in .= "<option value='{$k}'>{$v}</option>";
            }
        }
        $option_p_status = "";


        for ($i = 0; $i <= 2; $i++) {
            if ($i == $p_status) {

                $option_p_status .= " <div class='radio w25'><input type='radio' checked  name='p_status' id='radio-stt-{$i}' value='{$i}'><label for='radio-stt-{$i}'>{$CMS->lang['p_statup_0'.$i]}</label></div>";
            } else {
                $option_p_status .= " <div class='radio w25'><input type='radio'    name='p_status' id='radio-stt-{$i}' value='{$i}'><label for='radio-stt-{$i}'>{$CMS->lang['p_statup_0'.$i]}</label></div>";
            }
        }


        $p_type = $data['product_type'];

        $option_supplier = "<option value=''>{$CMS->lang['select']}</option>";
        $supplier = $CMS->supplier->get_list_supplier(1);

        foreach ($supplier as $s) {

            if ($s['supplier_id'] == $p_supplier) {
                $option_supplier .= "<option value='{$s['supplier_id']}' selected>{$s['supplier_name']}</option>";
            } else {

                $option_supplier .= "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
            }
        }

		$option_p_manufacture ="<option value=''>{$CMS->lang['select']}</option>";
		$manufacture = $CMS->manufacture->getAll();
		foreach ($manufacture as $m) {
			if ($m['manufacture_id'] == $p_manufacture) {
				$option_p_manufacture .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
			} else {
				$option_p_manufacture .= "<option value='{$m['manufacture_id']}'>{$m['manufacture_name']}</option>";
			}
		}

        $option_p_product_group = "<option value=''>{$CMS->lang['select']}</option>";


        $group = $CMS->product_group->getAll($p_type);

        foreach ($group as $g) {

            $pgroup_selected = $p_product_group == $g['product_group_id'] ? "selected" : "";

            $option_p_product_group .= "<option value='{$g['product_group_id']}' {$pgroup_selected}>{$g['product_group_name']}</option>";
            if (count($g['data_item']) > 0) {
                foreach ($g['data_item'] as $key => $value) {

                    $pgroup_selected = $p_product_group == $value['product_group_id'] ? "selected" : "";

                    $option_p_product_group .= "<option value='{$value['product_group_id']}' {$pgroup_selected}> |__{$value['product_group_name']}</option>";
                }
            }
        }

        $option_p_type = "";
        for ($i = 0; $i <= 1; $i++) {
            if ($i == $p_type) {
                $option_p_type .= "
   					 <option selected value='{$i}'>{$CMS->lang['p_type_0'.$i]}</option>

				";
            } else {
                $option_p_type .= "   <option   value='{$i}'>{$CMS->lang['p_type_0'.$i]}</option>";
            }
        }

        $option_p_product_option = "";
        $option_p_product_option_bk = explode(",", $p_product_option);
         for ($i = 1; $i <= 5; $i++) {
            if (in_array($i, $option_p_product_option_bk)) {
                $option_p_product_option .= "
                	<div class='checkbox'>
								<input type='checkbox' name='p_product_option[]' value='{$i}' id='check-{$i}' checked >
								<label for='check-{$i}'>{$CMS->lang['product_option_'.$i]}</label>
							</div>
				";
            } else {
                $option_p_product_option .= "
   					 <div class='checkbox'>
								<input type='checkbox' name='p_product_option[]' value='{$i}' id='check-{$i}'  >
								<label for='check-{$i}'>{$CMS->lang['product_option_'.$i]}</label>
					 </div>
				";
            }
        }


        $option_p_cycle = "";
        if ($p_type != 0) {
            for ($i = 0; $i <= 2; $i++) {
                if ($i == $p_cycle) {
                    $option_p_cycle .= "<option value='{$i}' selected>{$CMS->lang['p_cycle_0'.$i]}</option>";
                } else {
                    $option_p_cycle .= "<option value='{$i}'>{$CMS->lang['p_cycle_0'.$i]}</option>";
                }
            }
        } else {
            $option_p_cycle .= "<option value='0' selected>{$CMS->lang['p_cycle_00']}</option>";
        }
        $out = '';
// print "<pre>"; print_r($data);exit;
        $out .= <<<EOF
<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit_do&id={$data['product_id']}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="p_type" value="{$CMS->input['p_type']}">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['p_edit']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
{$CMS->global->languageTab('langTab')}
 <figure class="box-typical box-typical box-typical-padding border">
 
 	<div class="row">
		<div class="col-md-6">
			<div class="row">
EOF;
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF

                <div class="col-xl-6">
				  	<fieldset class="form-group langTab" lang="{$langCode}">
						<label class="form-label" >{$CMS->lang['p_name']} <span style="color:red">(*)</span></label>
				 		 <div class="form-control-wrapper">
							<input class="form-control " type="text" name="p_name[{$langCode}]" id="p_name[{$langCode}]" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['p_name_err']}" value="{$p_name[$langCode]}">
					 	</div>
				  	</fieldset>
				</div>
        
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
            
                <div class="col-xl-6">
				  	<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['p_name']} <span style="color:red">(*)</span></label>
				 		 <div class="form-control-wrapper">
							<input class="form-control " type="text" name="p_name" id="p_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['p_name_err']}" value="{$p_name}">
					 	</div>
				  	</fieldset>
				</div>
EOF;
        }


    $out .= <<<EOF

				<div class="col-xl-6">
					<fieldset class="form-group" {$hide_field} >
						<label class="form-label" >{$CMS->lang['p_sku']}</label>

								<input class="form-control " type="text" name="p_sku" id="p_sku" value="{$p_sku}">
					</fieldset>
			 		
				</div> <!-- col 6 -->
				<div class="col-xl-6">
					 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_img_alt']}</label>
                        <input class="form-control " type="text" name="p_img_alt" id="p_img_alt" value="{$p_img_alt}">
					</fieldset>
			 	</div>

			 	<div class="col-xl-6" {$hide_field}>
					 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_barcode']}</label>
						<input class="form-control " type="text" name="p_barcode" id="p_barcode" value="{$p_barcode}">
					</fieldset>
			 	</div>
			</div>
			<div class="row">
				<div class="col-xl-6">
						 <label class="form-label pull-left" >{$CMS->lang['p_avartar']}</label>
					
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
										<input  type="hidden" id="ufile_output_b64" value="{$data['base64_string']}" name="base64_image" />
									  </div>
							 
								 <div class="drop-zone fileinput-button" style="    height: 120px !important">
			                              <img id="upload_img_show" src="{$data['product_image_c']}" width="205" {$style_display} />

			                              <i class="font-icon font-icon-cloud-upload-2"></i>
			                               <div class="drop-zone-caption">Drag file to upload</div>
			                                 <input type="file"  name="p_image" id="ufile" accept="image/*">
			                         </div><!--.drop-zone-->
			                  		
							
					</fieldset>
				</div><!-- col xl 6-->
				
			 	<div class="col-xl-6">
			 		   <fieldset class="form-group" {$hide_field}>
							<label class="form-label pull-left" >{$CMS->lang['p_type']} <span style="color:red">(*)</span></label>
							<select class="form-control select_p_type" name="p_type" id="p_type"   >
								{$option_p_type}
							</select>
								
					  </fieldset>		
					   <fieldset class="form-group ">
							<label class="form-label pull-left" >{$CMS->lang['p_product_group']} <span style="color:red">(*)</span></label>
										
				
EOF;
        if ($CMS->permit["product_group_add"] == 1) {
            $out .= <<<EOF
					 
							<a   data-size="s" class="add_new_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline">
								<i class="fa fa-plus" aria-hidden="true"></i>	
							</a>
EOF;

        }
        if ($CMS->permit["product_group_edit"] == 1) {


            if ($p_product_group > 0) {
                $out .= <<<EOF
					 	<span class="box_product_group pull-right" style="margin-left:10px"> <a id="{$p_product_group}" class="btn_gen btn_edit_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
 
EOF;


            } else {
                $out .= <<<EOF
						<span class="box_product_group pull-right" style="margin-left:10px"></span>

							

EOF;
            }

        }


        $out .= <<<EOF
							 <div class="form-control-wrapper">
									<select class="form-control select_product_group select2" name="p_product_group" id="p_product_group" class="select_product_group"   defaultvalue="{$p_product_group}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['p_product_group_err']}" >
										{$option_p_product_group}
									</select>
								</div>	
								
						</fieldset>
EOF;
        if (!defined("is_web_us"))
        {
        	if($CMS->input['p_type'] == 0)
        	{
            $out .= <<<EOF
						 <fieldset class="form-group ">
							<label class="form-label pull-left" >{$CMS->lang['p_product_option']} </label>
							 <div class="form-control-wrapper">
									 
										{$option_p_product_option}
								 
								</div>	
								
						</fieldset>
EOF;
        	}
        }


        $out .= <<<EOF
				</div>	
			</div>
			<div class="row match-height">
					<div class="col-xl-12"><h5 class="m-t-lg with-border">{$CMS->lang['title_info_transaction']}</h5></div>
					<div class="col-xl-6" {$hide_field}>
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_cycle']}</label>
							<select class="form-control" name="p_cycle" id="p_cycle" >
									{$option_p_cycle}
								</select>
						</fieldset>		

					</div>
				 
					<div class="col-xl-6">
						<fieldset class="form-group" {$hide_field}>
						<label class="form-label" >{$CMS->lang['p_price']}</label>

							
							<input class="form-control " type="text" name="p_price" id="p_price" value="{$p_price}" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
						</fieldset>			
					</div>
					<div class="col-xl-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['p_price_old']}</label>
							<input class="form-control " type="text" name="p_price_old" id="p_price_old" value="{$p_price_old}"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
						</fieldset>			
					</div>
					<div class="col-xl-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['p_price_original']}</label>
							<input class="form-control " type="text" name="p_price_original" id="p_price_original" value="{$p_price_original}"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
						</fieldset>				

					</div>

					<div class="col-xl-6" {$hide_field}>
						<fieldset class="form-group mb0">
							<label class="form-label" >{$CMS->lang['p_tax']}</label>
								<select name="p_tax" id="p_tax" class=" form-control" value="{$p_tax}">

EOF;

        if ($p_tax == 0) {
            $out .= <<<EOF
									<option value="0" selected="selected">0%</option>
									<option value="10"  >10%</option>
EOF;
        } else {
            $out .= <<<EOF
									<option value="0" >0%</option>
									<option value="10" selected="selected" >10%</option>
EOF;
        }


        $out .= <<<EOF
								 
										</select>
						</fieldset>						
					</div>
					<div class="col-xl-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['p_price_sell']}</label>
							<input class="form-control " type="text" name="p_price_sell" id="p_price_sell" value="{$p_price_sell}"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
						</fieldset>				

					</div>
					<div class="col-xl-6" {$hide_field2}>
						<fieldset class="form-group mb0">
							<label class="form-label" >{$CMS->lang['title_suffix_price']}</label>
							<input name="p_up" id='p_up' class="form-control" value="{$p_up}" />
						</fieldset>
					</div>
EOF;

        if($CMS->vars['enabled_commission'])
        {
            $out .= <<<EOF
					<div class="col-xl-6">
                        <fieldset class="form-group">
                            <label class="form-label" >{$CMS->lang['gcommission']}</label>
                             <div class="input-group">
                              <input value="{$product_commission_value}" id="product_commission_value" name="product_commission_value" type="number" step="0.01" class="form-control" aria-label="Text input with dropdown button">
                              <input value="{$product_commission_type}" id="product_commission_type" name="product_commission_type" value="0" type="hidden">
                              <div id="product_commission_dropdown" class="input-group-btn">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  %
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                  <a class="dropdown-item" value="0">%</a>
                                  <a class="dropdown-item" value="1">{$CMS->vars['currency_type']}</a>
                                </div>
                              </div>
                                <script>
        dropdownInput($('#product_commission_dropdown'), $('#product_commission_type'));
                                </script>
                                </div>
                        </fieldset>	
                    </div>
EOF;
        }


        if ($p_type == 1) {

            $out .= <<<EOF
                        <div class="col-xl-6">
                            <fieldset class="form-group">
                            <label class="form-label" >{$CMS->lang['staff_id']}</label>
                                 <select class="select2" multiple="multiple" name="staff_id[]">
                                     {$CMS->user->load_list_user_2($data['staff_id'])}
                                </select>
                            </fieldset>			
                        </div>
                        <div class="col-xl-6" {$hide_field2}>
                            <fieldset class="form-group">
                            <label class="form-label" >{$CMS->lang['p_order']}</label>
                                <input class="form-control" min="0" type="number" name="p_order" id="p_order" value="{$p_order}" />
                            </fieldset>			
                        </div>	
EOF;
        }


        $out .= <<<EOF
			  </div><!-- row-->
		</div>
 	
		<div class="col-md-6">
			 
		
EOF;

        $option_category = $CMS->product_group->getOptionCategory();
        
        $out .= <<<EOF
		   
		 <fieldset class="form-group">
			 	<div class="row" {$hide_field}>
			 	
			 	   <div class="col-xl-6 col-lg-12 mb1rem">
 					

EOF;
				 	$out .=<<<EOF
				 	 
					<label class="form-label pull-left" >{$CMS->lang['p_manufacture']}</label>
			
				
				
EOF;
 
					if($CMS->permit["manufacture_add"] == 1)
					{
						$out .=<<<EOF
						 	<a   data-size="s" class="add_new_manufacture pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>	</a>


EOF;

					}	
				  if ($CMS->permit["manufacture_edit"] == 1) {
           			 if ($p_manufacture > 0) {
               		 $out .= <<<EOF
					 
						<span class="box_edit_manufacture pull-right" style="margin-left:10px"> <a id="{$p_manufacture}" class="box_edit_manufacture btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>

							

EOF;


		            } else {
		                $out .= <<<EOF
								<span class="box_edit_manufacture pull-right" style="margin-left:10px"></span>
EOF;
           			 }

        }
				
					$out .=<<<EOF

 
							<select class="form-control select_manufacture select2" name="p_manufacture" id="p_manufacture" >
								{$option_p_manufacture}
							</select>
									 
			 		</div><!-- col-lg-6-->


			 		<div class="col-xl-6 col-lg-12">

						<label class="form-label pull-left" >{$CMS->lang['p_supplier']}</label>
				
EOF;
        if ($CMS->permit["supplier_add"] == 1) {
            $out .= <<<EOF
										 
						 
						<a   data-size="s" class="add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>	</a>
EOF;

        }

        if ($CMS->permit["supplier_edit"] == 1) {
            if ($p_supplier > 0) {
                $out .= <<<EOF
					 
						<span class="box_edit_supplier pull-right" style="margin-left:10px"> <a id="{$p_supplier}" class="btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>

							

EOF;


            } else {
                $out .= <<<EOF
						<span class="box_edit_supplier pull-right" style="margin-left:10px"></span>

							

EOF;
            }

        }


        $out .= <<<EOF
					
						<select class="form-control select_supplier select2" name="p_supplier" id="p_supplier" for="change">
								{$option_supplier}
							</select>	

			 		</div>	<!-- col-lg-6-->

			 	</div><!-- class row-->
					
							
				</fieldset>
  				<fieldset class="form-group">
  				
		 		
		 				<label class="form-label" >{$CMS->lang['p_guarantee']}</label>
				 				<input class="form-control" maxlength="3" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" type="text" name="p_guarantee" id="p_guarantee"  value="{$p_guarantee}" />
				 		
				</fieldset> 
EOF;
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF

                <fieldset class="form-group langTab" lang="{$langCode}">
					<label class="form-label" >{$CMS->lang['p_description']}</label>
						<textarea name="p_description[{$langCode}]" rows="5" class="form-control">{$p_description[$langCode]}</textarea>
				</fieldset>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
            
                <fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['p_description']}</label>
						<textarea name="p_description" rows="5" class="form-control">{$p_description}</textarea>
				</fieldset>
EOF;
        }								

        if (!defined("is_web_us"))
        {
            $out .= <<<EOF
            <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['p_gallery']}</label>
					<div class="box-typical-upload box-typical-upload-in">
                            <div class="drop-zone fileinput-button" style="width: 100%">
                                <i class="font-icon font-icon-cloud-upload-2"></i>
                                <div class="drop-zone-caption">Drag file to upload</div>
                                <input type="file" multiple name="p_gallery[]" id="list_image" class="multiple_upload" accept="image/*">
                            </div><!--.drop-zone-->
                        <p class="box_error" style="display: none;"></p>
                        <h6 class="uploading-list-title title_upload" style="display: none;">{$CMS->lang['title_note_uploading']}</h6>
                        <!-- OLD FILE -->
                        <ul class="uploading-list list_upload">
EOF;

            $old_product_gallery = @json_decode($data['product_gallery'],true);

            // LHL-2018-06-06: Fix error Invalid argument supplied for foreach()
            if ( count($old_product_gallery) > 0 ) {
                foreach ($old_product_gallery as $old_gallery_img)
                {
                    $old_gallery_path = "{$CMS->vars['upload_dir']}/{$old_gallery_img}";
                    $old_gallery_src = "{$CMS->vars['upload_url']}/{$old_gallery_img}";
                    if(is_file($old_gallery_path))
                    {
                        $out .= <<<EOF
                            <li class="uploading-list-item">
                                  <div class="uploading-list-item-wrapper">
                                      <div class="uploading-list-item-name">
                                          <i class="font-icon font-icon-cam-photo"></i>
                                          <a href="{$old_gallery_src}">{$old_gallery_img}</a>
                                          <input type="hidden" name="old_gallery[]" value="{$old_gallery_img}">
                                      </div>
                                      <button type="button" class="uploading-list-item-close" onclick="removeItemGallery(this,3)">
                                          <i class="font-icon-close-2"></i>
                                      </button>
                                  </div>
                              </li>
EOF;
                    }
                }
            }


            $out .= <<<EOF
                        </ul>
                        <script>
                            $('.list_upload').magnificPopup({
                                  delegate: 'a', // child items selector, by clicking on it popup will open
                                  type: 'image'
                                });
                        </script>
                    </div>
			</fieldset>
EOF;
        }


        $out .= <<<EOF

				<!--fieldset class="form-group" {$hide_field2}>
					<label class="form-label" >{$CMS->lang['p_order']}</label>
					<input class="form-control" min="0" type="number" name="p_order" id="p_order" value="{$p_order}" />
				</fieldset-->			



				<fieldset class="form-group mb0">
					<div class="row">
						<div class="col-lg-6">
							<label class="form-label" >{$CMS->lang['p_show']}</label>
							 
							{$option_p_show}
						</div>	
					</div>		
				</fieldset>	 

EOF;
			if($CMS->vars['addon_goods_enable'] == 1 AND $p_type == 0)
			{
				$out .=<<<EOF
				<fieldset class="form-group mb0">
					<div class="row">
				 		<div class="col-lg-6">
							<label class="form-label" >{$CMS->lang['p_stock_available']}</label>
							{$option_p_stock_available}
						</div>	
						<div class="col-lg-6">
							<label class="form-label" >{$CMS->lang['p_show_instock']}</label>
							 
							{$option_p_show_instock}
						</div>	
					</div>
							
				</fieldset>

EOF;
			}
			$out .=<<<EOF
 
				
				
		
		</div>
	</div>
EOF;

        if (!defined("is_web_us"))
        {
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF
			<div class="row langTab" lang="{$langCode}">                
            	<section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#p_information_1_{$langCode}" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_1']}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2_{$langCode}" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_2']}
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1_{$langCode}">
                        <textarea name="p_information_1[{$langCode}]" rows="5" class="editor_texarea">{$p_information_1[$langCode]}</textarea>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="p_information_2_{$langCode}">
                        <textarea name="p_information_2[{$langCode}]" rows="5" class="editor_texarea">{$p_information_2[$langCode]}</textarea>
                    </div><!--.tab-pane-->
                </div><!--.tab-content-->
			</section>
		</div>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
            	<section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#p_information_1" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_1']}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2" role="tab" data-toggle="tab">
                                    {$CMS->lang['p_information_2']}
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->
            
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1">
                        <textarea name="p_information_1" rows="5" class="editor_texarea">{$p_information_1}</textarea>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="p_information_2">
                        <textarea name="p_information_2" rows="5" class="editor_texarea">{$p_information_2}</textarea>
                    </div><!--.tab-pane-->
                </div><!--.tab-content-->
            </section>
        </div>
EOF;
        }

        }


        $out .= <<<EOF

 
	</figure>	
</section>
 <section class="add_table">	 

EOF;

        //$product_subitem = base64_decode($data['product_subitem']);
        $un_product_subitem = json_decode($data['product_subitem'], true);


        $out .= <<<EOF
					 	

				 
				 

EOF;
        if ($_SESSION['is_mobile'] == true) {
            $out .= <<<EOF

							<table  id="table_item" class="table_cus">
EOF;
        } else {
            $out .= <<<EOF

							<table class="responsive-table table_cus">
EOF;
        }
        $out .= <<<EOF
								<thead>
									<tr>
									
										<th  scope="col" width="2%"></th>
EOF;
        if ($_SESSION['is_mobile'] == false) {
            $out .= <<<EOF

										<th  scope="col" width="3%">{$CMS->lang['stt']}</th>
EOF;
        }
        $out .= <<<EOF
										
									<th  scope="col" width="15%">{$CMS->lang['product_service']}</th>
									<th  scope="col" width="12%">{$CMS->lang['p_sku']}</th>

									<th  scope="col" width="25%">{$CMS->lang['pg_description']}</th>
									<th  scope="col" width="7%">{$CMS->lang['stock_store_quantity']}</th>
									<th  scope="col" width="10%">{$CMS->lang['price_no_vat']}</th>
									<th  scope="col" width="7%">{$CMS->lang['vat_percent']}</th>
									<th  scope="col" width="3%"></th>

									</tr>
								</thead>	
								<tbody id="data_table">
EOF;

        $total_row = count($un_product_subitem);

        if ($total_row > 0) {
            foreach ($un_product_subitem as $key => $value) {

                $key = $key + 1;
                $out .= <<<EOF
								 <tr class="row-grid" rowtr="">
									 <td scope="row"  ><i class="hidden-sm-down fa fa-th handle"></i></td>	
EOF;
                if ($_SESSION['is_mobile'] == false) {
                    $out .= <<<EOF

										 <td scope="row" data-title="{$CMS->lang['stt']}" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#{$key}</span></td>
EOF;
                }
                $out .= <<<EOF
								 
									<td data-title="{$CMS->lang['product_service']}"  class="grid-td" for="dgrid-2"> 
										 <div class="box_container">	
											<figure class="text_r">	
												<input type="text"  for="dgrid-2"  check="chtd"  class="form-control find_product " autocomplete="off"  name ="sub_product_name[]" value="{$value['product_name']}" onfocusout="return convert_sku_code(this);" /> 
											</figure>	
												<div class="box_result_find" style="display:none" ></div>
										 </div>	

											<input type="hidden" class="product_id" name="sub_product_id[]" value=""/>


									</td>
									<td data-title="{$CMS->lang['p_sku']}"  class="grid-td" for="dgrid-3" >
										<figure class="text_r">
											 <input name ="sub_product_sku[]" value="{$value['product_sku']}" class="form-control"    for="dgrid-3" /> 
										</figure>	 
</td>

									<td  data-title="{$CMS->lang['pg_description']}" class="grid-td" for="dgrid-4" check="chtd" >
										<figure class="text_r">
											 <textarea rows="4" for="dgrid-4" name ="sub_product_description[]" class="form-control" placeholder="{$CMS->lang['pg_description']}">{$value['product_description']}</textarea>
										</figure>	 
</td>
									<td data-title="{$CMS->lang['stock_store_quantity']}"  class="grid-td" for="dgrid-5" >
										<figure class="text_r">
											 <input name ="sub_product_quantity[]" value="{$value['product_quantity']}"   onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this);"  onkeyup="calculate_money_subitem_product();"  class="form-control " type="number" for="dgrid-5" /> 
										</figure>	 
</td>
									<td data-title="{$CMS->lang['price_no_vat']}"  class="grid-td" for="dgrid-6" >
										<figure class="text_r">
											 <input name ="sub_product_price[]" value="{$value['product_price']}" class="form-control"  onkeyup="calculate_money_subitem_product(); " onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" for="dgrid-6" /> 
										</figure>	 
</td>
									<td data-title="{$CMS->lang['vat_percent']}" class="grid-td" for="dgrid-7" >  
										<figure class="text_r"> 
											<select for="dgrid-7"  class="form-control"  name ="sub_product_tax[]"  class="bootstrap-select">

EOF;

                if ($value['product_tax'] == 0) {
                    $out .= <<<EOF
									<option value="0" selected="selected">0%</option>
									<option value="10"  >10%</option>
EOF;
                } else {
                    $out .= <<<EOF
									<option value="0" >0%</option>
									<option value="10" selected="selected" >10%</option>
EOF;
                }


                $out .= <<<EOF
									 	</select>
									 </figure>	

</td>
									<td class="trash"   for="dgrid-8"><span class="del_row_invoid text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
								</tr>
EOF;

                if ($key == $total_row) {
                    $key = $total_row + 1;

                    $out .= <<<EOF
								 <tr class="row-grid" rowtr="last-row">
										<td scope="row"><i class="hidden-sm-down fa fa-th handle"></i></td> 
EOF;
                    if ($_SESSION['is_mobile'] == false) {
                        $out .= <<<EOF

										 <td scope="row" data-title="{$CMS->lang['stt']}" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#{$key}</span></td>
EOF;
                    }
                    $out .= <<<EOF
								 
									<td data-title="{$CMS->lang['product_service']}" class="grid-td" for="dgrid-2">
										 <div class="box_container">	
										 	<figure class="text_r">
										     	<input type="text"  for="dgrid-2"  check="chtd"  class="form-control find_product " autocomplete="off"  name ="sub_product_name[]" onfocusout="return convert_sku_code(this);" value=""/> 
										     </figure>	
										      <div class="box_result_find" style="display:none" ></div>
										   </div><!-- box_container-->   

										  <input type="hidden" name="sub_product_id[]"  />
												

									</td>
									<td data-title="{$CMS->lang['p_sku']}"  class="grid-td" for="dgrid-3" >
										<figure class="text_r">
											 <input name ="sub_product_sku[]"  class="form-control"    for="dgrid-3" /> 
										</figure>	 
</td>
									<td  data-title="{$CMS->lang['pg_description']}" class="grid-td" for="dgrid-4" check="chtd" >
										<figure class="text_r">
											 <textarea rows="4" for="dgrid-4" name ="sub_product_description[]" class="form-control" placeholder="{$CMS->lang['pg_description']}"> </textarea>
											 
										</figure>	 
</td>
									<td data-title="{$CMS->lang['stock_store_quantity']}"  class="grid-td" for="dgrid-5" > <input name ="sub_product_quantity[]"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this);" value="1" onkeyup="calculate_money_subitem_product();"  class="form-control quan_list" type="number" for="dgrid-5" /> 
</td>
									<td data-title="{$CMS->lang['price_no_vat']}" class="grid-td" for="dgrid-6" > 
										<figure class="text_r">
											<input name ="sub_product_price[]"  onkeyup="calculate_money_subitem_product(); " onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"  value="" class="form-control" for="dgrid-6" /> 
										   </figure>	
</td>
									<td data-title="{$CMS->lang['vat_percent']}" class="grid-td" for="dgrid-7" >  
										<figure class="text_r">
											<select for="dgrid-7"  class="form-control tax_list"  name ="sub_product_tax[]" >
 												<option value="0" >0%</option>
												<option value="10" selected="selected" >10%</option>
											 </select>
										</figure>		 
</td>
									<td class="trash"   for="dgrid-8"><span class="del_row_invoid text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
								</tr>
EOF;
                }//End if check last row

            }//End for

        }//End if check row
        else {
            $key = 1;
            $out .= <<<EOF
								 <tr class="row-grid" rowtr="last-row">
								    <td scope="row"><i class="hidden-sm-down fa fa-th handle"></i></td>
EOF;
            if ($_SESSION['is_mobile'] == false) {
                $out .= <<<EOF

										<td   data-title="{$CMS->lang['stt']}" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#{$key}</span></td>
EOF;
            }
            $out .= <<<EOF
									
								 
									<td data-title="{$CMS->lang['product_service']}" class="grid-td" for="dgrid-2">
										<div class="box_container">	
											<figure class="text_r">
										    	<input type="text"  for="dgrid-2"  check="chtd"  class="form-control find_product " autocomplete="off"  name ="sub_product_name[]" onfocusout="return convert_sku_code(this);" value=""/> 
										    </figure>	
										    	<div class="box_result_find"  style="display:none" ></div>
										</div><!-- box_container-->   
											<input type="hidden" name="sub_product_id[]"  />
											
									</td>
									<td data-title="{$CMS->lang['p_sku']}"  class="grid-td" for="dgrid-3" >
										<figure class="text_r">
											 <input name ="sub_product_sku[]" value="{$value['product_sku']}" class="form-control"    for="dgrid-3" /> 
										</figure>	 
</td>

									<td data-title="{$CMS->lang['pg_description']}" class="grid-td" for="dgrid-4" check="chtd" > 
										<figure class="text_r"> 
									 
											 <textarea rows="4" for="dgrid-4" name ="sub_product_description[]" class="form-control" placeholder="{$CMS->lang['pg_description']}"></textarea>
										</figure>	
</td>
									<td data-title="{$CMS->lang['stock_store_quantity']}" class="grid-td" for="dgrid-5" >
										<figure class="text_r"> 
											 <input name ="sub_product_quantity[]"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this);" value="1" onkeyup="calculate_money_subitem_product();"    class="form-control quan_list" type="number" for="dgrid-5" /> 
										</figure>	 
</td>
									<td data-title="{$CMS->lang['price_no_vat']}" class="grid-td" for="dgrid-6" > 
										<figure class="text_r">
											<input name ="sub_product_price[]"  onkeyup="calculate_money_subitem_product(); " onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"  value="" class="form-control" for="dgrid-6" /> 
										</figure>		
</td>
									<td data-title="{$CMS->lang['vat_percent']}" class="grid-td" for="dgrid-7" >  
										<figure class="text_r">
											<select for="dgrid-7"  class="form-control tax_list"  name ="sub_product_tax[]"   >
 												<option value="0" >0%</option>
												 <option value="10" selected="selected" >10%</option>
											 </select>
										</figure>		 
</td>
									<td class="trash"   for="dgrid-8"><span class="del_row_invoid text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
								</tr>
EOF;
        }
        $out .= <<<EOF
								</tbody>
							</table>
					 

					 
				 
EOF;


        $out .= <<<EOF
 	
	 </section>	
	 
 
 

	


 
		 
		<section class="add_cart_footer">

EOF;
        $url_back['list'] = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";
        $url_back['detail_id'] = "{$data['product_id']}";
        $out .= $CMS->global->footer_back($url_back);

        $out .= $CMS->global->footer_edit("{$CMS->input['site']}");
        $out .= <<<EOF
  
		</section>
 
</form>

EOF;
        if ($_SESSION['is_mobile'] == true) {
            $out .= <<<EOF
					 
    <script>


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



       $('#table_item tbody').on('keyup change', '.child input, .child select, .child textarea', function(){
           var el = $(this);
           var rowIdx = el.closest('ul').data('dtr-index');
           var colIdx = el.closest('li').data('dtr-index');
           var cell = table_item.cell({ row: rowIdx, column: colIdx }).node();
          //   $('input, select, textarea', cell).val(el.val());
           $(cell).children().children().val(el.val());
           if(el.is(':selected')){ $ (cell).children().children().find("option[value='"+el.val()+"']").prop('selected', true); }

             calculate_money_subitem_product();


       });

</script>
EOF;
        }

        $out .= <<<EOF
 <script>

 
$(document).ready(function(){

	
	validate_form_custom("#form-signin_v1",".act_submit_save");
});

var module_name = "product_module";	
 $("#form_submit, #form_submit_mobile").click(function(){
 	 
 	 	$("tbody#data_table tr.child").remove();
 	 	$("#trigger_submit").trigger("click");
 
 });
 </script>
<script>
$(document).ready(function() {
	$("#p_type").change(function() {
		if ($("#p_type").val() != 0) {
			$($("#p_cycle")).empty();
			$($("#p_cycle")).html("<option value='1'>{$CMS->lang['p_cycle_01']}</option><option value='2'>{$CMS->lang['p_cycle_02']}</option>");
		} else {
			$($("#p_cycle")).empty();
			$($("#p_cycle")).html("<option value='0' selected>{$CMS->lang['p_cycle_00']}</option>");
		}
	});
});
</script>
   <script>
	var lang_supplier_add = "{$CMS->lang['supplier_add']}";
	var lang_supplier_edit = "{$CMS->lang['supplier_edit']}";

	var lang_manufacture_edit  = "{$CMS->lang['manufacture_edit']}";
	var lang_manufacture_add = "{$CMS->lang['manufacture_add']}";

</script>
 		 <script src="{$CMS->vars['js_acp']}/product.js"></script>
 
		{$this->formAddSupplier()}
 
		{$this->formAddManufacture()}
 
		{$this->formAddproductgroup()}
EOF;
        return $out;
    }

    public function show($data = null)
    {
        global $CMS, $DB, $member;

        //Check is web USA -> hide field
        $hide_field = "style='' ";
        if (defined("is_web_us") == true) {
            $hide_field = " style='display:none' ";
        }


        $out = <<<EOF

<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['p_info']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>
{$CMS->global->languageTab('langTab')}
 <figure class="box-typical box-typical box-typical-padding border">
 	<div class="row">
		<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
		
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF
                <fieldset class="form-group row langTab" lang="{$langCode}">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_name']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 <div class="form-label semibold">
							{$data['product_name'][$langCode]}  {$data['search_p_name']}
						</div>	
					</div>
				</fieldset>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
               	<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_name']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 <div class="form-label semibold">
							{$data['product_name']}  {$data['search_p_name']}
						</div>	
					</div>
				</fieldset>
EOF;
        }

        $out .=<<<EOF

				
				 <fieldset class="form-group row" {$hide_field} >
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_code']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_code']}
					 
					</div>
				</fieldset>
				<fieldset class="form-group row" {$hide_field} >
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_sku']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_sku']}
					 
					</div>
				</fieldset>
			 <fieldset class="form-group row" {$hide_field}>
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_barcode']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_barcode']}
					 
					</div>
				</fieldset>
			 <fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_product_group']}</label>
					<div class="col-xl-9 form-control-span2"> 
					 
							{$data['product_group_bk']} {$data['search_pg_name']}
					 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_product_option']}</label>
					<div class="col-xl-9 form-control-span2"> 
			 
							{$data['product_option_c']}
				 
					</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_type']}</label>
					<div class="col-xl-9 form-control-span2"> 
			 
							{$data['product_type_c']}
				 
					</div>
				</fieldset>
			
				
				<fieldset class="form-group row" {$hide_field}>
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_cycle']}</label>
					<div class="col-xl-9 form-control-span2"> 
	 
							{$data['product_cycle_c']}
				 
					</div>
				</fieldset>
				<fieldset class="form-group row"  {$hide_field} >
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_price']}</label>
					<div class="col-xl-9 form-control-span2"> 
					 
							{$data['product_price_c']}
					 
					</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_price_old']}</label>
					<div class="col-xl-9 form-control-span2"> 
						{$data['product_price_old_c']}
					</div>
				</fieldset>
				 <fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_price_original']}</label>
					<div class="col-xl-9 form-control-span2"> 
					 
							{$data['product_price_original_c']}
					 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_price_sell']}</label>
					<div class="col-xl-9 form-control-span2"> 
					 
							{$data['product_price_sell_c']}
					 
					</div>
				</fieldset>
				<fieldset class="form-group row" {$hide_field} >
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_tax']}</label>
					<div class="col-xl-9 form-control-span2"> 
							{$data['product_tax']}%
					</div>
				</fieldset>
EOF;

        if($CMS->vars['enabled_commission'])
        {
            $out .= <<<EOF
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['gcommission']}</label>
					<div class="col-xl-9 form-control-span2"> 
							{$data['product_commission']}
					</div>
				</fieldset>
EOF;
        }


        if ($data['product_type'] == 1) {
            $out .= <<<EOF
					<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['staff_id']}</label>
					<div class="col-xl-9 form-control-span2"> 
					 
							{$data['staff_id_bk']} 
				 
					</div>
				</fieldset>
 
EOF;

        }
        $out .= <<<EOF
		</div>


		<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
	 			
	 			<fieldset class="form-group row" {$hide_field}>
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_manufacture']}</label>
					<div class="col-xl-9 form-control-span2"> 
	 
							{$data['product_manufacture_c']}
			 
					</div>
				</fieldset>

				<fieldset class="form-group row" {$hide_field} >
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_supplier']}</label>
					<div class="col-xl-9 form-control-span2"> 
			 
							{$data['supplier_name_bk']} {$data['search_ncc_name']}
					 
					</div>
				</fieldset>

				<fieldset class="form-group row"  {$hide_field} >
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_guarantee']}</label>
					<div class="col-xl-9 form-control-span2"> 
			 
							{$data['product_guarantee_default_c']}
					 
					</div>
				</fieldset>
  

			<fieldset class="form-group row">
				<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_show']}</label>
				<div class="col-xl-9 form-control-span2"> 
		 
						{$data['product_show_c']}
				 
				</div>
			</fieldset>
EOF;

		if($CMS->vars['addon_goods_enable'] == 1)
		{
			$out .=<<<EOF
			<fieldset class="form-group row">
				<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_first_remain']}</label>
				<div class="col-xl-9 form-control-span2"> 
						{$data['product_first_remain']} 
EOF;
				if($CMS->permit['inventory_add'] == true)
				{
					$out .=<<<EOF
					<a href='{$CMS->vars['root_domain']}/?site=inventory'>{$CMS->lang['ajust_inventory']}</a>
EOF;
				}

			$out .=<<<EOF
				</div>
			</fieldset>
			<fieldset class="form-group row">
				<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_stock_available']}</label>
				<div class="col-xl-9 form-control-span2"> 
						{$data['product_stock_available_c']} 
				</div>
			</fieldset>
			<fieldset class="form-group row">
				<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_show_instock']}</label>
				<div class="col-xl-9 form-control-span2"> 
						{$data['product_show_instock_c']} 
				</div>
			</fieldset>
EOF;
		}




        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF

                <fieldset class="form-group row langTab" lang="{$langCode}">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_description']}</label>
					<div class="col-xl-9 form-control-span2"> 
							{$data['product_description'][$langCode]}
					</div>
				</fieldset>
				
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
               	<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_description']}</label>
					<div class="col-xl-9 form-control-span2"> 
							{$data['product_description']}
					</div>
				</fieldset>
EOF;
        }

        $out .=<<<EOF

			
			<fieldset class="form-group row">
				<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_avartar']}</label>
				<div class="col-xl-9 form-control-span2"> 
						 <img src="{$data['product_image_c']}" width="150" />
				</div>
			</fieldset>


            <div class="form-group row">
				<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_img_alt']}</label>
				<div class="col-xl-9 form-control-span2"> 
						{$data['product_image_alt']}
 				</div>
			</div>
	 
			 <div class="form-group row">
				<label class="col-xl-3 form-control-label2" >{$CMS->lang['user_name']}</label>
				<div class="col-xl-9 form-control-span2"> 
					 
						{$data['user_name_bk']} {$data['search_user_name']}
 				</div>
			</div>
			 


				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_time']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['product_time_c']}
						  
					</div>
				</fieldset>
		 


		</div>
	 
	</div>


 
 	</figure>	
</section>
 <section class="add_table">				 
EOF;
        if ($data['product_type'] == 0)// Hàng hóa
        {
            //	$product_subitem = base64_decode($data['product_subitem']);
            $un_product_subitem = json_decode($data['product_subitem'], true);

            if (count($un_product_subitem) > 0) {
                $out .= <<<EOF
			 

				  
						 
							<div class="table-responsive" style="overflow-x: initial;">
							<table class="table_cus" width="100%">
								<thead>
									<tr>
									
										 
										<th  scope="col" width="3%">{$CMS->lang['stt']}</th>
					
										<th  scope="col" width="15%">{$CMS->lang['product_service']}</th>
										<th  scope="col" width="12%">{$CMS->lang['p_sku']}</th>

										<th  scope="col" width="25%">{$CMS->lang['pg_description']}</th>
										<th  scope="col" width="7%">{$CMS->lang['stock_store_quantity']}</th>
										<th  scope="col" width="10%">{$CMS->lang['price_no_vat']}</th>
										<th  scope="col" width="7%">{$CMS->lang['vat_percent']}</th>
 
									</tr>
								</thead>	
								<tbody id="data_subitem">
EOF;
                foreach ($un_product_subitem as $key => $value) {
                    $key = $key + 1;
                    $out .= <<<EOF
								<tr class="row-item" rowtr="last-row">
									 	<td class="item-td" for="item-1" check="chtd"><span class="number">#{$key}</span></td>
								 
									<td class="item-td" for="item-2"> <span>{$value['product_name']} </span>
									</td>
									<td class="item-td" for="item-3"> <span>{$value['product_sku']} </span>
									</td>
									<td  class="item-td" for="item-4" check="chtd" <span>{$value['product_description']}</span>
</td>
									<td> <span>{$value['product_quantity']}</span>
</td>
									<td> <span>{$CMS->class->input->currency($value['product_price'])}</span>
</td>
									<td> <span>{$value['product_tax']}%</span>
</td>
								 
								</tr>
EOF;

                }
                $out .= <<<EOF
							</tbody>
							</table>
						</div>		


			 
	 
	 

EOF;

            }
        }


        $out .= <<<EOF
</section>	
EOF;
        //Check stock
        $stock = $CMS->assets->check_stockproduct_allstore($data['product_id']);

        if (count($stock) > 0) {


            $out .= <<<EOF

<section class="add_table">				 			 
		 
		  <h4 class="heading"><i class="fa fa-caret-down"></i><span>{$CMS->lang['check_store_title']}</span></h4>
				 
			 <div class="table_cus" style="overflow-x: initial;">
				<dic class="table-responsive">
					<table width="100%">
						<thead>
							<tr>
 
				 			 
								<th scope="col" width="25%">{$CMS->lang['stock_store_name']}</th>
								<th scope="col" width="25%">{$CMS->lang['stock_store_quantity']}</th>
								<th scope="col" width="25%">{$CMS->lang['stock_store_url']}</th>
								 
					 		 
							</tr>
						</thead>	
						<tbody>
EOF;

            foreach ($stock as $key => $value) {
                # code...

                $out .= <<<EOF
				<tr>
					 
					<td>{$value['store_name']}</td>
					<td>{$value['stock']}</td>
					<td><a href="{$CMS->vars['root_domain']}/?site=assets&store_id={$value['store_id']}">{$CMS->lang['detail_store']}</a></td> 
				</tr>	
EOF;

            }
            $out .= <<<EOF

										
			</tbody>
		</table>
	</div></div>
 
		
</section>
EOF;


        }

        $out .= <<<EOF
		<section class="add_cart_footer">
 
			 
EOF;
        $url_back['list'] = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";

        $out .= $CMS->global->footer_back($url_back);


        if ($CMS->permit['product_delete'] == 1) {
            $out .= <<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=delete&id={$data['product_id']}');"    class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['gdelete']}</a>
					 
EOF;

        }

        if ($CMS->permit['product_edit'] == 1) {
            $out .= <<<EOF
							<a  href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$data['product_id']}"   class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['gedit']}</a>
EOF;

        }


        $out .= <<<EOF


					<div class="btn-group dropup pull-right hidden-sm-up">
					  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-save"></i>{$CMS->lang['gaction']}
					  </button>
					  <div class="dropdown-menu">
					  	<ul>
									 
EOF;

        if ($CMS->permit['product_delete'] == 1) {
            $out .= <<<EOF

							<li class="hidden-sm-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=delete&id={$data['product_id']}');"  id="form_submit_mobile"  title=""><i class="fa fa-trash-o"></i>{$CMS->lang['gdelete']}</a></li>
EOF;
        }

        if ($CMS->permit['product_edit'] == 1) {
            $out .= <<<EOF
							<li class="hidden-sm-up"><a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$data['product_id']}"  id="form_submit_option_mobile" title=""><i class="fa fa-edit"></i>Sửa</a></li>
EOF;

        }


        $out .= <<<EOF
	
						</ul>
					  </div>
					</div>


		</section>
 

EOF;
        return $out;
    }


    public function formAddSupplier()
    {
        global $CMS;

        $output = <<<EOF
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
									<select name="supplier_get_invoice" id="supplier_get_invoice" class="form-control" defaultvalue="{$data['supplier_get_invoice']}" style="width: 100%">
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
									<select name="supplier_type" id="supplier_type" class="form-control" onchange="change_supplier_type(this.value)" defaultvalue="{$data['supplier_type']}" style="width: 100%">
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
									<select name="supplier_status" id="supplier_status" class="form-control" defaultvalue="{$data['supplier_status']}" style="width: 100%">
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
				  <a class="btn_add_supplier" style="display:none">btn_add_supplier</a>
                  <a class="btn_edit_do_supplier" style="display:none">btn_edit_do_supplier</a>
			</form>
		</div>
 		<script>
                 $(document).ready(function(){

                    validate_form_custom("#box_add_supplier",".act_popup_btn_validate","box_custom");
                });

          </script>  
EOF;

        return $output;
    }


    public function formAddManufacture()
    {
        global $CMS;
        $option_m_manufacture_parent = "<option value=''>{$CMS->lang['select']}</option>";
        $manufacture = $CMS->manufacture->getParent();
        foreach ($manufacture as $m) {
            if ($m['manufacture_id'] == $m_manufacture_parent) {
                $option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
            } else {
                $option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' >{$m['manufacture_name']}</option>";
            }
        }
        $option_m_status = "";
        $m_status = 1;
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $m_status) {
                $option_m_status .= "<option value='{$i}' selected>{$CMS->lang['m_status_0'.$i]}</option>";
            } else {
                $option_m_status .= "<option value='{$i}'>{$CMS->lang['m_status_0'.$i]}</option>";
            }
        }
        $output = <<<EOF
		<div id="box_add_manufacture" class="popup_add_manufacture mfp-hide">
			<p class="title_add_manufacture roboto_bold" style="font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['manufacture_add']}</p>
			<form id="add_manufacture_form" name="add_manufacture_form">
				<ul class="list_field_supplier">
				    <li style="min-height: 0px;"><p class="manu_error_msg" style="display:none;"></p></li>
			 
      		 	<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
				 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['m_manufacture_parent']} </label>
							<div class="typeahead-field"> 
					 			<span class="typeahead-query">
								 
								<select class="form-control select2" name="m_manufacture_parent" id="m_manufacture_parent" >
									{$option_m_manufacture_parent}
								</select>
							</span>
						</div>
					</fieldset>
	         	</li>

				<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
					<fieldset class="form-group">
						<label class="form-label"  >{$CMS->lang['m_name']} <span style="color:red">(*)</span></label>
						 <div class="typeahead-field"> 
					 			<span class="typeahead-query">
								 
								<input class="form-control " type="text" name="m_name" id="m_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_name_err']}" value="{$m_name}">
								<input type="hidden" name="m_manufacture_id" id="m_manufacture_id" />
							</span>
						</div>
					</fieldset>
				</li>

				<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
					<fieldset class="form-group ">
						<label class="form-label"   >{$CMS->lang['m_code']}</label>
						 <div class="typeahead-field"> 
					 			<span class="typeahead-query">
								<input class="form-control " type="text" name="m_code" id="m_code"   value="{$m_code}">
							</span>
						</div>
					</fieldset>

				</li>
			 
				<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
					<fieldset class="form-group ">
						<label class="form-label"  >{$CMS->lang['m_status']}</label>
						 <div class="typeahead-field"> 
					 		 <span class="typeahead-query">
								<select class="form-control" name="m_status" id="m_status" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_status_err']}">
									{$option_m_status}
								</select>
							</span>
						</div>
					</fieldset>
				</li>
				<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
					<fieldset class="form-group ">
						<label class="form-label" >{$CMS->lang['m_description']} <span style="color:red">(*)</span></label>
							 <div class="typeahead-field"> 
					 		 <span class="typeahead-query">
								<textarea rows="3" class="form-control" name="m_description" id="m_description">{$m_description}</textarea> 
							
						</div>
					</fieldset>
				</li>	

				<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
					<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
						<fieldset class="form-group">
							<div class="typeahead-field"> 
								<span class="typeahead-query change_action_manufacture">
									<input class="btn btn_add_manufacture" type="button" value="{$CMS->lang['manufacture_add']}"/>
								</span>
							</div>
						</fieldset>
					</li>
				
				</ul>
				  <a class="btn_add_manufacture" style="display:none">btn_add_manufacture</a>
                  <a class="btn_edit_do_manufacture" style="display:none">btn_edit_do_manufacture</a>
          
			</form>
		</div>
		 <script>
                 $(document).ready(function(){

                    validate_form_custom("#box_add_manufacture",".act_popup_btn_validate","box_custom");
                });

          </script>  


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
            if ($i == $pg_type) {
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
        $output = <<<EOF
		<div id="box_add_group_product" class="popup_add_group_product mfp-hide">
			<p class="title_group_product  " style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['group_product_add']}...</p>
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

<select class="form-control" name="subact" onchange="return check_submit_form('{$CMS->lang['gnotice_confirm_action']}', 'form_product', this);" defaultvalue="delete_all" emsg="{$CMS->lang['incomplete_action']}" ehide="1">
	<option value="">{$CMS->lang['choose_action']}</option>
	{$CMS->vars['action_controller']}

</select>

 
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

    function preview_barcode1()
    {
        global $CMS;

        $output = '';

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

            $barcode_bk = $barcode;
            $barcode = lib\Barcode::generatorJPG($barcode);

            //create folder for qrcode temporary
            $CMS->class->image->check_folder_img('qrcode','',0);

            if(lib\Barcode::$error)
            {
                echo $barcode; exit;
            }

            $barcode = base64_encode($barcode);
            $barcode = "<img height='100px' src=\"data:image/jpeg;base64,'{$barcode}'\">";
            $CMS->input['name'] = urldecode($CMS->input['name']);
            $CMS->input['skucode'] = urldecode($CMS->input['skucode']);
            $CMS->input['group'] = urldecode($CMS->input['group']);
            $price = strip_tags($CMS->class->input->currency($CMS->input['price']));

            $qrcodeContent = '';
            $qrcodeContent .=  "Name: {$CMS->input['name']}\n";
            $qrcodeContent .=  "SKU: {$CMS->input['skucode']}\n";
            $qrcodeContent .=  "Category: {$CMS->input['group']}\n";
            $qrcodeContent .=  "Barcode: {$barcode_bk}\n";
            $qrcodeContent = stripslashes($qrcodeContent);

            \PHPQRCode\QRcode::png($qrcodeContent, "{$CMS->vars['upload_dir']}/qrcode/qrcode.png", 'L', 4, 2);

            for($i=1; $i<=2; $i++)
            {
                $output .= <<<EOF
            <div style="width: 300px; float:left; margin: 10px">
                <div style="width: 100%; clear: both; text-align: center">
                    <strong>{$CMS->input['name']}</strong>
                </div>
                <div style="width: 100%; clear: both;">
                    <div style="width: 35%; float: left">{$CMS->input['skucode']}</div>
                    <div style="width: 55%; float: right; text-align: right"><img src="{$CMS->vars['upload_url']}/qrcode/qrcode.png"/></div>
                </div>
                <div style="width: 100%; clear: both;">
                    <div  style="width: 35%; float: left">{$price}</div>
                    <div  style="width: 55%; float: right; text-align: right">{$barcode}</div>
                </div>
            </div>
EOF;
            }
        }

        return $output;
    }

    /**
     * Commission list html
     * @param array $data
     */
    function commission($data = [])
    {
        global $CMS;

        if( $CMS->input['site'] == 'service' )
        {
        	$product_service_title = $CMS->lang['list_service'];
        	$product_service_category_title = $CMS->lang['list_category_service'];

        	$totalproduct = $CMS->product->countProductByType(1);
    		$totalGroup = $CMS->product_group->countProductGroupByType(1);
    		$totalCommission = $CMS->product->countCommissionByType(1);
        }
        else
        {
        	$product_service_title = $CMS->lang['list_product'];
        	$product_service_category_title = $CMS->lang['list_category_product'];

        	$totalproduct = $CMS->product->countProductByType(0);
    		$totalGroup = $CMS->product_group->countProductGroupByType(0);
    		$totalCommission = $CMS->product->countCommissionByType(0);
        }

        $output = <<<EOF
<section class="main_form">
    <figure class="heading">
		<h3>Commission</h3>
		<figure class="pull-right right">
			<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&amp;act=add" title="" class="add_bill">Add service</a>
			<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&amp;subact=clear_cache">Clear cache</a>
		</figure>
	</figure>

	<!-- Tabs head -->
    <section class="box-heading box-status box-filternav" style="padding-bottom: 0;margin-bottom: 0;">
        <div class="box-heading-body">
            <ul class="nav-filter clearfix">
                <li class="">
                    <a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}">{$totalproduct}<span>{$product_service_title}</span></a>
                </li>
                <li class="">
                    <a href="{$CMS->vars['root_domain']}/?site=product_group&amp;pg_type={$CMS->input['p_type']}">{$totalGroup}<span>{$product_service_category_title}</span></a>
                </li>
                <li class="active">
                    <a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&amp;commission=1">{$totalCommission}<span>{$CMS->lang['gcommission']}</span></a>
                </li>
            </ul>
        </div>
    </section>

    <form name="form_product" id="form_product" method="POST" action="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&commission=1">
        <section class="add_table" style="margin-top: 15px !important;">
            <div class="data_table">
                <div class="table-responsive">
                    <table id="example" class="display table table_cus" cellspacing="0" width="100%">
						<thead>
						    <tr>
						        <th width="5%">ID</th>
						        <th>{$CMS->lang['product']}/{$CMS->lang['service']} <small style="font-size: 12px; font-weight: normal">({$CMS->lang['commission_note']})</small></th>
						        <th>{$CMS->lang['gcommission']} <input type="submit" value="update" class="btn btn-primary" style="float: right"></th>
						    </tr>
						</thead>
						<tbody>
EOF;

        foreach($data as $item)
        {
            $product_type = $item['product_type'] ? 'service' : 'product';

            $output .= <<<EOF
						    <tr>
						        <td>#{$item['product_id']}</td>
						        <td><a href="{$CMS->vars['root_domain']}/?site={$product_type}&act=show&id={$item['product_id']}">{$item['product_name']}</a></td>
						        <td>
						            <div class="input-group">
                                      <input value="{$item['product_commission_value']}" id="product_commission_value_{$item['product_id']}" name="product_commission_value[{$item['product_id']}]" type="number" step="0.01" class="form-control" aria-label="Text input with dropdown button">
                                      <input value="{$item['product_commission_type']}" id="product_commission_type_{$item['product_id']}" name="product_commission_type[{$item['product_id']}]" type="hidden">
                                      <div id="product_commission_dropdown_{$item['product_id']}" class="input-group-btn">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #bebebe; border-color: #bebebe; color: #6c7a86; padding: 7px 10px;">%</button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                          <a class="dropdown-item" value="0">%</a>
                                          <a class="dropdown-item" value="1">{$CMS->vars['currency_type']}</a>
                                        </div>
                                      </div>
                                        <script>
                                            dropdownInput($('#product_commission_dropdown_{$item['product_id']}'), $('#product_commission_type_{$item['product_id']}'));
                                        </script>
                                    </div>
                                </td>
                            </tr>
EOF;
        }


        $output .= <<<EOF
                            <tr>
                                <td colspan="3"><input type="submit" value="update" class="btn btn-primary" style="float: right"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>    
    </form>
</section>
<script>
$(".checkbox_product").trigger("change");
</script>
EOF;
        return $output;
    }
}

?>