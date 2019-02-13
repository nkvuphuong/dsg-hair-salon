<?php
class skin_customer
{
	public function head()
	{
		global $CMS, $DB, $member;

        $cus_id_search = $CMS->input['cus_id'];
        $cus_group_search = $CMS->input['group'];
        $cus_full_name_search = $CMS->input['full_name'];
        $cus_cus_search = $CMS->input['cus'];
        $cus_type_search = $CMS->input['type'];
        $cus_birthday_search = $CMS->input['birthday'];
        $cus_sex_search = $CMS->input['sex'];
        $cus_country_search = intval($CMS->input['country']) ? intval($CMS->input['country']) : -1;

        $cus_city_search = $CMS->input['city'];
        $cus_district_search = $CMS->input['district'];

        $group = $CMS->group_customer->getAll(' gc_status=1 AND ');

        $option_cus_group_search = "<option value=''>{$CMS->lang['select']}</option>";
        foreach ($group as $g)
        {
            if ($g['gc_id'] == $cus_group_search)
            {
                $option_cus_group_search .= "<option value='{$g['gc_id']}' selected>{$g['gc_name']}</option>";
            }
            else
            {
                $option_cus_group_search .= "<option value='{$g['gc_id']}'>{$g['gc_name']}</option>";
            }
        }

        $option_cus_sex_search = "<option value=''>{$CMS->lang['select']}</option>";
        for ($i = 1; $i <= 2; $i++)
        {
            if ($i == $cus_sex_search)
            {
                $option_cus_sex_search .= "<option value='{$i}' selected>{$CMS->lang['cus_sex_0'.$i]}</option>";
            }
            else
            {
                $option_cus_sex_search .= "<option value='{$i}'>{$CMS->lang['cus_sex_0'.$i]}</option>";
            }
        }

        $country = $CMS->country->country();
        $option_cus_country_search = "<option value=''>{$CMS->lang['select']}</option>";
        foreach ($country as $countryId => $countryName)
        {
            $countrySelected = $cus_country_search == $countryId ? 'selected' : '';
            $option_cus_country_search .= "<option $countrySelected value='{$countryId}'>{$countryName}</option>";
        }

        $city = $CMS->country->city($cus_country_search ? $cus_country_search : -1);
        $option_cus_city_search = "<option value=''>{$CMS->lang['select']}</option>";

        // LHL-2018-06-07: Invalid argument supplied for foreach(
        if ( is_array($city) ) {
            foreach ($city as $cityId => $cityName)
            {
                $citySelected = $cus_city_search == $cityId ? 'selected' : '';
                $option_cus_city_search .= "<option $citySelected value='{$cityId}'>{$cityName}</option>";
            }
        }

        $option_cus_district_search = "<option value=''>{$CMS->lang['select']}</option>";
        if (isset($cus_city_search))
        {
            $district = $CMS->country->district($cus_city_search ? $cus_city_search : -1);
            foreach ($district as $districtId => $districtName)
            {
                $districtSelected = $cus_district_search == $districtId ? 'selected' : '';
                $option_cus_district_search .= "<option {$districtSelected} value='{$districtId}'>{$districtName}</option>";
            }
        }

        $option_cus = "<option value=''>{$CMS->lang['select']}</option>";
        $cus = $CMS->user->getAllUser();
        foreach ($cus as $c)
        {
            if ($c['user_id'] == $cus_cus)
            {
                $option_cus .= "<option value='{$c['user_id']}' selected>{$c['user_name']}</option>";
            }
            else
            {
                $option_cus .= "<option value='{$c['user_id']}' >{$c['user_name']}</option>";
            }
        }

        $gcActive[trim($CMS->input['group']) == '' ? -1 : intval($CMS->input['group'])] = 'active';

		$out .= <<<EOF
        <script type='text/javascript' src='{$CMS->vars['js_acp']}/customer.js'></script>
		<section class="add_table main_form">
			<figure class="heading">
				<h3>{$CMS->lang['cus_title']}</h3>
				<figure class="pull-right right">
					<div class="search">
						<form method="get" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/" style="display:inline-block">
						    <input type="hidden" name="site" value="customer">
							<input type="submit" class="fa-input" value="&#xf002;">
							<input type="text" name="keyword" autocomplete="off" minlength="2" maxlength="64" id="p_quick_search" style="position: :relative;" placeholder="{$CMS->lang['gsearch_quick']}" value="">
					        <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
						</form>
						<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
					</div>
					{$CMS->global->importExportData($CMS->input['site'],'',1)}
					<a href="{$CMS->vars['root_domain']}/?site=customer&act=add" title="" class="add_bill">{$CMS->lang['cus_add']}</a>
					<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
				</figure>
				<section class="search_adv" >
					<form method="get" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/" >
					    <input name="site" type="hidden" value="{$CMS->input['site']}"/>
						<figure class="box-typical box-typical box-typical-padding border">
								<h5>{$CMS->lang['gsearch_advance']}</h5>
								<ul class="input_li row match-height">
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									    <label class="form-label pull-left " for="cus_id_search">ID</label>
										<input type="number" name="cus_id" id="cus_id_search" value="{$cus_id_search}" placeholder="ID" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									    <label class="form-label pull-left " for="cus_group_search">{$CMS->lang['cus_group']}</label>
									    <select class="form-control" name="group" id="cus_group_search">
									        <option value=''>{$CMS->lang['select']}</option>
EOF;

        foreach ($group as $gc)
        {
            // LHL-2018-06-07: in_array() expects parameter 2 to be array
            $gcSelected = $gc['gc_id'] == \lib\input::get('group') ? "selected" : "";

            $out.= <<<EOF
                                            <option value="{$gc['gc_id']}" {$gcSelected}>{$gc['gc_name']}</option>
EOF;
        }


        $out.= <<<EOF
                                        </select>
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									    <label class="form-label pull-left " for="cus_full_name_search">{$CMS->lang['cus_full_name']}</label>
										<input type="text" name="full_name" id="cus_full_name_search" value="{$cus_full_name_search}" placeholder="{$CMS->lang['cus_full_name']}" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<label class="form-label pull-left " for="cus_sex_search">{$CMS->lang['cus_sex']}</label>
										<select name="sex" id="cus_sex_search" class="form-control select2">
											{$option_cus_sex_search}
										</select>
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<label class="form-label pull-left " for="cus_country_search">{$CMS->lang['cus_country']}</label>
										<select name="country" id="cus_country_search" class="form-control country_dependency select2">
											{$option_cus_country_search}
										</select>
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<label class="form-label pull-left " for="cus_city_search">{$CMS->lang['cus_city']}</label>
										<select name="city" id="cus_city_search" class="form-control city_dependency select2">
											{$option_cus_city_search}
										</select>
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<label class="form-label pull-left " for="cus_district_search">{$CMS->lang['cus_district']}</label>
										<select name="district" id="cus_district_search" class="form-control district_dependency select2">
											{$option_cus_district_search}
										</select>
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									    <label class="form-label pull-left " for="" style="width: 100%">&nbsp;</label>
										<input class="btn" type="submit" value="{$CMS->lang['comment_filter']}">
									</li>
                                </ul>
							</figure>
						 </form>
				</section>

				<script>
					function inputLoading(e)
					{
						var str  = "<div class=\"cssload-container\" id=\"input_loading\">";
							str += "<div class=\"cssload-progress cssload-float cssload-shadow\">";
							str += "<div class=\"cssload-progress-item\"></div>";
							str += "</div>";
							str += "</div>";
						e.parent().append(str);
					}
					function changeSCCity()
					{
						$("#cus_district_search").prop('disabled', true);
						inputLoading($('#cus_district_search'));
						$.get( 
							"{$CMS->vars['root_domain']}/?site=customer&act=get_district", 
							{ id: $('#cus_city_search').val() } 
						) .done( function(html) {
							$("#cus_district_search").html(html);
							$("#cus_district_search").prop('disabled', false);
							$('#input_loading').remove();
						} );
					}
				</script>
			</figure>
			
			<section class="tabs-section">
			    <div class="tabs-section-nav tabs-section-nav-inline">
			        <ul class="nav" role="tablist">
			            <li class="nav-item">
			                <a class="nav-link active" href="{$CMS->vars['root_domain']}/?site=customer" >
			                    {$CMS->lang['cus_list']}
			                </a>
			            </li>
			            <li class="nav-item">
			                <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=group_customer">
			                    {$CMS->lang['cus_category_list']}
			                </a>
			            </li>
			        </ul>
			    </div><!--.tabs-section-nav-->
			</section><!--.tabs-section-->		
			
			<section class="tabs-section" style="margin-top: 12px">
				<div class="tabs-section-nav tabs-section-nav-icons">
					<div class="tbl">
						<ul class="nav">
							<li class="nav-item">
								<a href="{$CMS->vars['root_domain']}/?site=customer" class="nav-link {$gcActive[-1]}">
									<span class="nav-link-in">
										{$CMS->lang['all']}
									</span>
								</a>
							</li>
EOF;

		foreach($group as $gData)
        {
            $out .= <<<EOF
							<li class="nav-item">
								<a class="nav-link {$gcActive[$gData['gc_id']]}" href="{$CMS->vars['root_domain']}/?site=customer&group={$gData['gc_id']}">
									<span class="nav-link-in">
										{$gData['gc_name']}
									</span>
								</a>
							</li>
EOF;
        }


		$out .= <<<EOF
		                    <li class="nav-item">
								<a href="{$CMS->vars['root_domain']}/?site=customer&group=0" class="nav-link {$gcActive[0]}">
									<span class="nav-link-in">
										{$CMS->lang['unclassified']}
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div><!--.tabs-section-nav-->

				<div class="tab-content">
					<section class="add_table">
                        <div class="data_table" style="overflow-x:auto">
                            <div class="table table_cus table-responsive" style="border-top: none;">
                                <table id="customer_example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                                    <thead>
                                        <tr role="row">
                                            <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                                            <th>ID</th>
                                            <th>{$CMS->lang['cus_full_name']}</th>
                                            <!--<th>{$CMS->lang['cus_type']}</th>-->
                                            <th>{$CMS->lang['cus_phone']}</th>
                                            <th>{$CMS->lang['cus_email']}</th>
                                            <!--th>{$CMS->lang['cus_group']}</th-->
                                            <th>{$CMS->lang['cus_order_number']}</th>
                                            <th>{$CMS->lang['cus_last_order']}</th>
                                            <th>{$CMS->lang['cus_total_order']}</th>
                                            <!--<th>{$CMS->lang['cus_time']}</th>-->
                                            <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="data_table" class="ui-sortable">
EOF;

		return $out;
	}

	public function foot()
	{
		global $CMS, $DB, $member;

		$out = <<<EOF
                                </tbody>
                            </table>
                        </div>
                        <div class="fuction_table">
                            <div class="pull-left">
                                <p class="form-control-static"></p>
                            </div>
                            <nav class="pull-right">
                                <div class="block_bottom pagination pagination-sm">{$CMS->customer->show_page}</div>
                            </nav>
                        </div>
                    </div>
                </section>
            </div><!--.tab-content-->
        </section>
	</section>
<script type="text/javascript" src="/acp/jsacp/customer.js"></script>
EOF;
	
		$out .= <<<EOF
		<script>
		    locationDependency();
		    $(function() {
		      	$('#customer_example').DataTable({
		        	language: {
		            	emptyTable: 'Không tìm thấy dữ liệu!'
		          	},
		        	order: [[ 1,"desc"]],
		        	paging: false,
		        	searching: false,
		        	info: false,
EOF;
  
  	if ( $_SESSION['is_mobile'] == true )
  	{
    	$out .= "responsive: { details: true},";
  	}

		$out .= <<<EOF
		    });
		});
	</script>
	<style>
		.table-responsive {
		  overflow-x: visible !important;
		  overflow-y: visible !important;
		}
	</style>
	
	<script>
	    $('html').on('click', function(e) {  
		    $('.popover').remove();
		    if($(e.target).hasClass('popOver'))
            {
                $(e.target).popover({
                    html : true, 
                    content: function() {
                        let contentObj = $($(this).attr('href'));
                        return contentObj.html();
                    } 
                });
                $(e.target).popover('show');
            }
        });
	</script>
EOF;

		return $out;
	}

	public function mid($data=null)
	{
		global $CMS, $DB, $member;

	  	$style = $_SESSION['is_mobile'] == true ? "padding-right:27px;" : "";

		$out = <<<EOF
		<tr>
			<td style="margin:0px;padding:0px;{$style}"></td>
            <td ><span class="number">#{$data['cus_id']}</span></td>
            <td class="mwr" style="position: relative;"><a data-placement="bottom" title="{$CMS->lang['cus_title_info']}" class="popOver" href="#popOver{$data['cus_id']}">{$data['cus_full_name']}</a>
				<div class="box_info_customer" id="popOver{$data['cus_id']}" style="display: none;">
					<ul class="list_info_cus">
						<li><b>{$CMS->lang['cus_name']}</b>: {$data['cus_full_name']}</a></li>
						<li><b>{$CMS->lang['cus_email']}</b>: {$data['cus_email']}</li>
						<li><b>{$CMS->lang['cus_phone']}</b>: {$data['cus_phone']}</li>
						<li><b>{$CMS->lang['cus_address']}</b>: {$data['cus_address']}</li>
						<li><b>{$CMS->lang['cus_country']}</b>: {$data['cus_country_c']}</li>
						<li><b>{$CMS->lang['cus_district']}</b>: {$data['cus_district_c']}</li>
						<li><b>{$CMS->lang['cus_city']}</b>: {$data['cus_city_c']}</li>
						<li>
							<a class="pull-left" href="{$CMS->vars['root_domain']}/?site=customer&act=show&id={$data['cus_id']}">{$CMS->lang['cus_detail']}</a>
EOF;
    
	    if ( $CMS->permit['user_edit'] )
	  	{
	    	$out .= <<<EOF
	    	<a class="pull-right" href="{$CMS->vars['root_domain']}/?site=customer&act=edit&id={$data['cus_id']}" class="edit">{$CMS->lang['cus_edit']}</a>
EOF;
  		}

    		$out .= <<<EOF
						</li>
					</ul>
				</div>
            </td>
			<!--<td>{$data['cus_type']}</td>-->
			<td>{$data['cus_phone']}</td>
			<td>{$data['cus_email']}</td>
			<!--td>{$data['cus_group_c']}</td-->
			<td><a href='{$CMS->vars['root_domain']}/?site=order&cus_id={$data['data_bk']['cus_id']}' target='_blank'>{$data['cus_number_order']} <i class="fa fa-external-link" aria-hidden="true"></i></a></td>
			<td>{$data['cus_last_order']}</td>
			<td>{$data['cus_total_order']}</td>
			<!---<td>{$data['cus_time_c']}</td>-->
			<td >
EOF;
    
	    if ( $CMS->permit['user_edit'] )
	  	{
	    	$out .= <<<EOF
	    	<a href="{$CMS->vars['root_domain']}/?site=customer&act=edit&id={$data['cus_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
  		}

  		if ( $CMS->permit['user_delete'] )
  		{
    		$out .= <<<EOF
    		<a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=customer&act=delete&id={$data['cus_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
EOF;
  		}

  		$out .= <<<EOF
			</td>
        </tr>
EOF;

		return $out;
	}
	public function none() {
		global $CMS, $DB, $member;
		$out = <<<EOF
EOF;
		return $out;
	}

	public function show($data=null) 
	{
		global $CMS, $DB, $member;

		$bill = $CMS->transactions->getNumberTotalByCusID($CMS->input['id'],0,5);
		$invoice = $CMS->transactions->getNumberTotalByCusID($CMS->input['id'],0,1);
		$email = $CMS->email->count('',$data['cus_email']);
		$src_image_upload = $CMS->vars['upload_url']."/customer/{$data['cus_image']}";
		$tags = @json_decode($data['cus_tags'], true);

		$out = '';

		if (isset($_SESSION['error_msg'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['error_msg']}
			</div>
EOF;
			unset($_SESSION['error_msg']);
		}

		if (isset($_SESSION['msg'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['msg']}
			</div>
EOF;
			unset($_SESSION['msg']);
		}

		$out = <<<EOF
		<section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['cus_info']}: {$data['cus_full_name']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=customer&group={$data['cus_group']}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>

		 

			<figure class="box-typical box-typical box-typical-padding border">

			
			<div class="row">
			    <div class="col-md-6">
			        <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
                            <h5 class="m-t-lg with-border">{$CMS->lang['cus_basic_info']}</h5>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_full_name']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_full_name']}
                                    </div>	
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_email']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_email']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_sex']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_sex_c']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_birthday']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_birthday']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_group']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_group_c']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_note']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_note']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_image']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        <img style="width:30%" src="{$src_image_upload}" />
                                    </div>
                                </div>
                            </fieldset>
                            
                            <h5 class="m-t-lg with-border">{$CMS->lang['cus_contact_info']}</h5>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_phone']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_phone']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_address']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_address']}
                                    </div>	
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_address']} 2</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_address2']}
                                    </div>	
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_country']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_country_c']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_city']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_city_c']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_district']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_district_c']}
                                    </div>
                                </div>
                            </fieldset>
                            
                            <h5 class="m-t-lg with-border">{$CMS->lang['cus_other_info']}</h5>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_company']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_company']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_tax_code']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_tax_code']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_company_address']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_company_address']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_company_email']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_company_email']}
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group row">
                                <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_company_phone']}</label>
                                <div class="col-xl-8 form-control-span2"> 
                                    <div class="form-label semibold">
                                        {$data['cus_company_phone']}
                                    </div>
                                </div>
                            </fieldset>
                        </div>
				    </div>
                </div>
                <div class="col-xl-6">
	                <div class="row">
	                    <div class="col-sm-6">
	                        <a href="{$CMS->vars['root_domain']}/?site=order&cus_id={$CMS->input['id']}" target='_blank'>
	                            <article class="statistic-box red">
                                    <div>
                                        <div class="number">{$data['cus_total_order']}</div>
                                        <div class="caption"><div>{$data['cus_number_order']} {$CMS->lang['orders']}</div></div>
                                    </div>
                                </article>
	                        </a>
	                    </div><!--.col-->
	                    <div class="col-sm-6">
	                        <a href="{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&cus_id={$CMS->input['id']}" target='_blank'>
	                            <article class="statistic-box green">
	                            <div>
	                                <div class="number">{$CMS->class->input->currency($invoice['total'])}</div>
	                                <div class="caption"><div>{$invoice['cnt']} {$CMS->lang['invoices']}</div></div>
	                            </div>
	                        </article>
	                        </a>
	                    </div><!--.col-->	 
	                    <div class="col-sm-6">
	                        <a href="{$CMS->vars['root_domain']}/?site=email&email_to={$data['cus_email']}" target='_blank'>
	                            <article class="statistic-box purple">
                                    <div>
                                        <div class="number">{$email}</div>
                                        <div class="caption"><div>Email(s)</div></div>
                                    </div>
                                </article>
	                        </a>
	                    </div><!--.col-->
	                    <div class="col-sm-6">
	                        <a href="{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&cus_id={$CMS->input['id']}" target='_blank'>
	                            <article class="statistic-box yellow">
                                    <div>
                                        <div class="number">{$CMS->class->input->currency($bill['total'])}</div>
                                        <div class="caption"><div>{$bill['cnt']} {$CMS->lang['bills']}</div></div>
                                    </div>
                                </article>
	                        </a>
	                    </div><!--.col-->                                      
	                </div><!--.row-->
	                <div class="row">
	                    <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
	                     <h5 class="m-t-lg with-border">Tags</h5>
                            <fieldset class="form-group row">
EOF;

                foreach ($tags as $tag)
                {
                    $out .= <<<EOF
                                <a href="{$CMS->vars['root_domain']}/?site=customer&tag={$tag}" target="_self" class="btn btn-inline btn-primary">{$tag}</a>
EOF;

                }

                    $out .= <<<EOF
                            </fieldset>
                        </div>
	                </div>
	            </div>
			</div>
			</figure>
		</section>		
        {$CMS->global->logs("customer_{$CMS->input['id']}","Edit_customer_{$CMS->input['id']}")}
EOF;
		$footer_details = array(
				'type' => 'show',
				'module' => 'customer',
				'detail_id' => $data['cus_id'],
			);
		$out .= $CMS->global->footer_details($footer_details);

		return $out;
	}

    public function form($data  )
    {
        global $CMS, $DB, $member;
 
        $cus_full_name = $data['cus_full_name'];
        $cus_address = $data['cus_address'];
        $cus_address2 = $data['cus_address2'];
        $cus_type = $data['cus_type'];
        $cus_phone = $data['cus_phone'];
        $cus_company = $data['cus_company'];
        $cus_company_phone = $data['cus_company_phone'];
        $cus_company_address = $data['cus_company_address'];
        $cus_company_email = $data['cus_company_email'];
        $cus_email_invoice = $data['cus_email_invoice'];
        $cus_tax_code = $data['cus_tax_code'];
        $cus_group = $data['cus_group'];
        $cus_sex = $data['cus_sex'];
        $cus_birthday = \lib\date::format($data['cus_birthday']);

        $maskFormat = preg_replace('/[^\/]/','0',$CMS->vars['date_format']) ;
        $maskPlaceHolder = preg_replace('/[^\/]/','_',$CMS->vars['date_format']);

        if(isset($data['cus_country']))
        {
            $cus_country = $data['cus_country'];
        }
        else
        {
            if(defined("is_web_us") == true)
            {
                $cus_country = 231;
            }
            else
            {
                $cus_country = 238;
            }
        }

//        $cus_country = $data['cus_country'];
        $cus_city = $data['cus_city'];
        $cus_district = $data['cus_district'];
        $cus_email = $data['cus_email'];
        $cus_cus = $data['cus_cus'];
        $cus_note = $data['cus_note'];

        $group = $CMS->group_customer->getAll(' gc_status=1 AND ');
        $option_group = "";
        foreach ($group as $g)
        {
            if (in_array($g['gc_id'], $cus_group))
            {
                $option_group .= "<option value='{$g['gc_id']}' selected>{$g['gc_name']}</option>";
            }
            else
            {
                $option_group .= "<option value='{$g['gc_id']}'>{$g['gc_name']}</option>";
            }
        }

        $sexChecked[!in_array($cus_sex,[1,2]) ? 1 : $cus_sex] = "Checked";

        $option_country = $CMS->country->get_country_option($cus_country);

        $option_city = $CMS->country->getOptionCity($cus_country, $cus_city);

        $option_district = $CMS->country->getOptionDistrict($cus_city, $cus_district);

        $out = '';
        $src_image_upload = $CMS->vars['upload_url']."/customer/{$data['cus_image']}";
        $style_display = '';
        if($src_image_upload != "") { $style_display = "display:block"; }
        
        $out .= <<<EOF
		<form id="form-signin_v1" name="form-customer_v1" action="{$CMS->vars['root_domain']}/?site=customer&act={$CMS->input['act']}&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">
		<section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['cus_'.$CMS->input['act']]}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=customer&group={$cus_group}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
 			<figure class="box-customer box-typical box-typical-padding border" style="display: inline-block;
    width: 100%;">
                <div class="col-lg-4">
                    <h5 class="m-t-lg with-border">{$CMS->lang['cus_basic_info']}</h5>
                    <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_full_name']} <span style="color:red">(*)</span></label>
				 		<input class="form-control " type="text" name="cus_full_name" id="cus_full_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['cus_full_name_err']}" value="{$cus_full_name}"> 
					</fieldset>
					<fieldset class="form-group row">
					    <div class="col-xl-7 col-lg-8 col-md-3 col-sm-4 col-xs-8">
					        <label class="form-label" >{$CMS->lang['cus_email']} <span style="color:red">(*)</span></label>
				 		<input class="form-control " type="text" name="cus_email" id="cus_email" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['cus_email_err']}" data-validation-regex="/^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}" value="{$cus_email}" placeholder="example@email.com">
					    </div>
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_sex']}</label>
						<div class="radio">
                            <input type="radio" name="cus_sex" id="cus_sex_1" value="1" $sexChecked[1]>
                            <label for="cus_sex_1">{$CMS->lang['cus_sex_01']}</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="cus_sex" id="cus_sex_2" value="2" $sexChecked[2]>
                            <label for="cus_sex_2">{$CMS->lang['cus_sex_02']}</label>
                        </div>
					</fieldset>
					<fieldset class="form-group row">
					    <div class="col-xl-6 col-lg-8 col-md-3 col-sm-4 col-xs-8">
					        <label class="form-label" >{$CMS->lang['cus_birthday']}</label>
					        <div class="form-control-wrapper form-control-icon-right">
					            <input class="form-control " type="text" name="cus_birthday" id="cus_birthday" value="{$cus_birthday}">
					            <i class="font-icon font-icon-calend"></i>
					        </div>
					    </div>
                    </fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_group']}</label>
				 		<select class="form-control select2" multiple name="cus_group[]" id="cus_group">
							{$option_group}
						</select>
					</fieldset>	
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_note']}</label>
				 		<textarea class="form-control" name="cus_note" id="cus_note" rows="3">{$cus_note}</textarea>
					</fieldset>
                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['cus_tags']}</label>
                        <select class="form-control select2" name="cus_tags[]" multiple="multiple" id="tagSelector">
                        </select>
                    </fieldset>
 					 <fieldset class="form-group">
			              <label class="form-label pull-left" >{$CMS->lang['cus_image']}</label>
			                  <div class="actionButtons pull-right">
			                      <ul>
			                          <li onclick="return delete_fileToAttach();">
			                              <i class="fa fa-trash-o"></i>
			                          </li>
			                      </ul>
			                      <input  type="hidden" id="ufile_output_b64" name="base64_image" value="" />
			                  </div>
			                 
			                  <div class="drop-zone fileinput-button" style="height: 150px !important">
			                      <img id="upload_img_show" width="205" src="{$src_image_upload}"  style="margin: auto;max-height: 130px;max-width: 200px;{$style_display}" />
			                      <i class="font-icon font-icon-cloud-upload-2"></i>
			                      <div class="drop-zone-caption">Drag file to upload</div>
			                          <input type="file" name="cus_image" id="ufile" accept="image/*">
			                  </div><!--.drop-zone-->
			       </fieldset>

                </div>
                
                
                <div class="col-lg-4">
                    <h5 class="m-t-lg with-border">{$CMS->lang['cus_contact_info']}</h5>
                    <fieldset class="form-group row">
                        <div class="col-xl-6 col-lg-8 col-md-3 col-sm-4 col-xs-8">
                            <label class="form-label" >{$CMS->lang['cus_phone']}</label>
				 		    <input class="form-control" type="text" name="cus_phone" id="cus_phone" value="{$cus_phone}" c_type="phone">
                        </div>				
					</fieldset>
					<fieldset class="form-group row">
					    <div class="col-xl-7 col-lg-8 col-md-3 col-sm-4 col-xs-8">
					        <label class="form-label" >{$CMS->lang['cus_email_invoice']}</label>
				 		    <input class="form-control" data-validation-regex="/(^$|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$)/" data-validation-regex-message="{$CMS->lang['invalid_email']}" type="text" name="cus_email_invoice" id="cus_email_invoice" value="{$cus_email_invoice}" placeholder="example@email.com">
					    </div>
                    </fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_address']}</label>
				 		<input class="form-control " type="text" name="cus_address" id="cus_address" value="{$cus_address}">
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_address']} 2</label>
				 		<input class="form-control " type="text" name="cus_address2" id="cus_address2" value="{$cus_address2}">
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_country']}</label>
				 		<select class="form-control country_dependency select2" name="cus_country" id="cus_country">
							{$option_country}
						</select>
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_city']}</label>
				 		<select class="form-control city_dependency select2" name="cus_city" id="cus_city">
							{$option_city}
						</select>
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_district']}</label>
				 		<select class="form-control district_dependency select2" name="cus_district" id="cus_district">
							{$option_district}
						</select>
					</fieldset>
                </div>
                
                <div class="col-lg-4">
                    <h5 class="m-t-lg with-border">{$CMS->lang['cus_other_info']}</h5>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_company']}</label>
				 		<input class="form-control " type="text" name="cus_company" id="cus_company" value="{$cus_company}">
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_tax_code']}</label>
				 		<input class="form-control " type="text" name="cus_tax_code" id="cus_tax_code" value="{$cus_tax_code}">
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['cus_company_address']}</label>
				 		<input class="form-control " type="text" name="cus_company_address" id="cus_company_address" value="{$cus_company_address}">
					</fieldset>
					<fieldset class="form-group row">
					    <div class="col-xl-7 col-lg-8 col-md-3 col-sm-4 col-xs-8">
					        <label class="form-label" >{$CMS->lang['cus_company_email']}</label>
				 		    <input class="form-control" data-validation-regex="/(^$|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$)/" data-validation-regex-message="{$CMS->lang['invalid_email']}" type="text" name="cus_company_email" id="cus_company_email" value="{$cus_company_email}" placeholder="example@email.com">
					    </div>
                    </fieldset>             
                    <fieldset class="form-group row">
					    <div class="col-xl-6 col-lg-8 col-md-3 col-sm-4 col-xs-8">
					        <label class="form-label" >{$CMS->lang['cus_company_phone']}</label>
				 		    <input class="form-control" type="text" name="cus_company_phone" id="cus_company_phone" value="{$cus_company_phone}" c_type="phone">
					    </div>
					</fieldset>
                </div>
			</figure>
		</section>
		{$CMS->global->logs("{$CMS->input['site']}_{$CMS->input['id']}")}
        <script type="text/javascript" src="/acp/jsacp/customer.js"></script>
EOF;
        $footer_details = array(
            'type' => $CMS->input['act'],
            'module' => 'customer',
        );
        $out .= $CMS->global->footer_details($footer_details);
        $out .= <<<EOF
		</form>
		<style>
			.box-customer .form-group { position: relative;}
		</style>
		<script>
		locationDependency();
		
		function inputLoading(e) 
		{
			var str  = "<div class=\"cssload-container\" id=\"input_loading\">";
				str += "<div class=\"cssload-progress cssload-float cssload-shadow\">";
				str += "<div class=\"cssload-progress-item\"></div>";
				str += "</div>";
				str += "</div>";
			e.parent().append(str);
		}

		$("#cus_birthday").datetimepicker({format:'{$CMS->vars['date_format']}'});
		$("#cus_birthday").mask("{$maskFormat}", {placeholder: "{$maskPlaceHolder}"});
		$("[c_type=phone]").mask("{$CMS->vars['phone_format']}", {placeholder: "{$CMS->vars['phone_format']}"});
		</script>
		<script type='text/javascript' src='{$CMS->vars['js_url']}/acp_news.js'></script>
		<script>
		$(document).ready(function(){
			validate_form_custom("#form-signin_v1",".act_submit_save");
			select2Tags('{$data['cus_tags']}', "customer");
		});
		</script>
EOF;
        return $out;
    }


    public function loadform_dsg($src_image_upload = "")
    {
    	global $CMS;

    	$output .=<<<EOF
    	  <fieldset class="form-group">
              <label class="form-label pull-left" >{$CMS->lang['customer_image']}</label>
                  <div class="actionButtons pull-right">
                      <ul>
                          <li onclick="return delete_fileToAttach();">
                              <i class="fa fa-trash-o"></i>
                          </li>
                      </ul>
                      <input  type="hidden" id="ufile_output_b64" name="base64_image" value="" />
                  </div>
                 
                  <div class="drop-zone fileinput-button" style="height: 150px !important">
                      <img id="upload_img_show" width="205" src="{$src_image_upload}" {$style_display} style="margin: auto;max-height: 130px;max-width: 200px;" />
                      <i class="font-icon font-icon-cloud-upload-2"></i>
                      <div class="drop-zone-caption">Drag file to upload</div>
                          <input type="file" name="customer_image" id="ufile" accept="image/*">
                  </div><!--.drop-zone-->
       </fieldset>
EOF;
		return $output;
    }
}
?>