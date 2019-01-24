<?php
use core\ezy;
use lib\input;

//Load models
use models\shipping_services;
ezy::load_model("shipping_services");

$config_general = new config_general;
$config_general->auto_run();

class config_general {

	//===========================================================================
	//  AUTO RUN //
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['config_general_header']}";

		// Load the models
		$CMS->config_general->auto_run();
		$this->html = $CMS->config_general->html;
		// Switch
		switch( $CMS->input["act"] )
		{
			case "edit_do":
				$this->edit_do();
			break;

            case "import":
                $this->import();
                break;
            case "export":
                $this->export();
                break;
            case "color":
                $this->color();
                break;
            case "update_color":
                $this->update_color();
                break;
            case "shipping_fee":
                $this->shippingFee();
            break;

            default:
                switch( \lib\input::get('subact') )
                {
                    case "update_timezone":
                        $this->update_timezone();
                        break;
                    case "clear_cache":
                        $this->clear_cache();
                        break;
                    case "del_favicon":
                        $this->del_favicon();
                        break;
                    default:
                        $this->page_default();
                        break;
                }
                break;

			
		}
	}
    public function update_color(){
        global $CMS, $DB, $member;
        $color_1= $CMS->input['free_theme_color_1'];
        $color_2= $CMS->input['free_theme_color_2'];
        $color_3= $CMS->input['free_theme_color_3'];
        $color_4= $CMS->input['free_theme_color_4'];
        if(!empty($color_1) and !empty($color_2) and !empty($color_3)){
            $CMS->config_general->update_config_general('free_theme_color_1',$color_1);
            $CMS->config_general->update_config_general('free_theme_color_2',$color_2);
            $CMS->config_general->update_config_general('free_theme_color_3',$color_3);
            $CMS->config_general->update_config_general('free_theme_color_4',$color_4);
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general");
        }else{
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=color");
        }

    }
    public function color(){
        global $CMS, $DB, $member;
        $CMS->output .= ezy::html("get_html_color");
    }

    public function edit_do()
	{
		global $CMS, $DB, $member;
       
		if(\lib\input::get('subact') == "del_key_gg_analytics")
		{
			// Get info key
			$sql = $DB->query("SELECT * FROM ".root_table."conf_settings  WHERE  conf_key = 'gg_analytics_account' AND conf_value <> '' ORDER BY conf_id DESC LIMIT 1");
 
			if($DB->num_rows($sql) > 0)
			{
				$info = $DB->fetch_array($sql);
				$filename = root_path."db/googleauth/".$info['conf_value'];
			 	unlink($filename);
			 	$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '' WHERE conf_key = 'gg_analytics_account'");
			 	 //del cache
         		$DB->query("TRUNCATE ".root_table."cache");
			 	$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&tab={$activetab}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&tab={$activetab}");
		}
		else
		{  
			$CMS->config_general->edit();
			$activetab = $CMS->input['active_tab'] ? "{$CMS->input['active_tab']}" : "tabs-4-tab-1";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&tab={$activetab}");
		}
		
	}
	

	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;

		// Write data
		$CMS->output .= $this->html->header();
	}

    /**
     * Import excel
     */
    public function import()
    {
        global $CMS;
        ezy::load_model("report");
        $CMS->config_general->importFromExcel();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general");
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = $CMS->config_general->exportToExcel();
//        header("location: {$link}");
        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    public function update_timezone()
    {
        global $CMS;
        $CMS->config_general->updateTimezoneId();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&tab={$CMS->input['tab']}");
    }

    public function clear_cache()
    {
        global $CMS;

        $CMS->class->cache->mdelete('');

        $_SESSION['msg'] = $CMS->lang['cleared_cache_all'];

        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&tab=tabs-config-cache");
    }

    public function del_favicon()
    {
        global $CMS;

        $CMS->class->cache->deletesql("config");
        $CMS->config_general->del_favicon();
        print 1; exit;
    }

    public function shippingFee()
    {
        global $CMS, $tpl;

        // load language
        $CMS->class->language->load("shipping_fee");

        switch( \lib\input::get('subact') )
        {
            case 'quicksearch';
                $this->shippingFeeQuickSearch();
                exit;
            break;

            case 'shipping_default':
                if ( $CMS->input['request_method'] == 'post' ) 
                {
                    $CMS->config_general->updateShippingDefault($CMS->input);
                }
                $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
            break;

            case "delete_all":
                \models\shipping_services::delete_all();
                $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
            break;

            case "delete":
                $shipping_fee = \models\shipping_services::getInfo($CMS->input['id']);
                if( !$shipping_fee )
                {
                    $_SESSION['error_msg'] = $CMS->lang["not_found_shipping_fee"];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
                }

                \models\shipping_services::delete($shipping_fee['ship_id']);
                $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
            break;

            case "edit":
                $tpl->render = 'shipping_fee_edit';

                $shipping_fee = \models\shipping_services::getInfo($CMS->input['id']);
                if( !$shipping_fee )
                {
                    $_SESSION['error_msg'] = $CMS->lang["not_found_shipping_fee"];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
                }

                if ( $CMS->input['request_method'] == 'post' ) 
                {
                    if( \models\shipping_services::edit($shipping_fee, $CMS->input) )
                    {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
                    }

                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee&subact=edit&id={$shipping_fee['ship_id']}");
                }

                $tpl->data = \models\shipping_services::convertValue($shipping_fee);
                $tpl->logs = $CMS->global->logs(\models\shipping_services::$cache_prefix.'_'.$shipping_fee['ship_id']);
            break;

            case "add":
                if ( $CMS->input['request_method'] == 'post' ) 
                {
                    if( \models\shipping_services::add($CMS->input) )
                    {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
                    }

                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee&subact=add");
                }

                $tpl->render = 'shipping_fee_add';
                $tpl->option_product_list = $CMS->product->getOptionProduct(0, true, false, true);

                $tpl->option_shipping_service = '';
                $shipping_type_service = \models\shipping_services::$shipping_type_service;
                foreach( $shipping_type_service as $key => $value )
                {
                    $tpl->option_shipping_service .= "<option value='{$key}'>{$value}</option>";
                }
            break;

            case "clear_cache":
                $CMS->class->cache->mdelete(\models\shipping_services::$cache_prefix);
                $_SESSION['msg'] .= $CMS->lang['ship_fee_cleared_cache_all'];
                $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee");
            break;

            default:
                $tpl->render = 'shipping_fee';
                $tpl->data = \models\shipping_services::listing();
                $tpl->show_page = $CMS->show_page;

                $country = $CMS->country->getInfoCountry($CMS->vars['default_shiping_location']);
                $tpl->option_location = $CMS->country->get_country_option((isset($country['country_iso_code']) ? $country['country_iso_code'] : 0), 1);
                $tpl->logs = $CMS->global->logs('default_shiping');

                // Search
                $tpl->keywords = isset($CMS->input['keywords']) ? urldecode($CMS->input['keywords']) : "";
                $tpl->price = isset($CMS->input['price']) ? urldecode($CMS->input['price']) : "";
                $tpl->price_extra = isset($CMS->input['price_extra']) ? urldecode($CMS->input['price_extra']) : "";

                $ship_type_service = isset($CMS->input['ship_type_service']) ? $CMS->input['ship_type_service'] : '';
                $shipping_type_service = \models\shipping_services::$shipping_type_service;
                $tpl->option_shipping_service = "<option value=''>-----</option>";
                foreach( $shipping_type_service as $key => $value )
                {
                    $selected = "";
                    if( $ship_type_service == $key AND is_numeric($ship_type_service) )
                    {
                        $selected = "selected='selected'";
                    }
                    $tpl->option_shipping_service .= "<option value='{$key}' {$selected}>{$value}</option>";
                }

                $ship_location = isset($CMS->input['ship_location']) ? $CMS->input['ship_location'] : '';
                $shipping_location = \models\shipping_services::$shipping_location;
                $tpl->option_ship_location = "<option value=''>-----</option>";
                foreach( $shipping_location as $key => $value )
                {
                    $selected = "";
                    if( $ship_location == $key AND is_numeric($ship_location) )
                    {
                        $selected = "selected='selected'";
                    }
                    $tpl->option_ship_location .= "<option value='{$key}' {$selected}>{$value}</option>";
                }
            break;
        }

        // Output
        $CMS->output .= ezy::html($tpl->render, 'config_general');
    }

    public function shippingFeeQuickSearch() 
    {
        global $CMS;

        // Data
        $limit = \models\shipping_services::$maxPage = 4;
        $dataListShippingFee = \models\shipping_services::listing( "", 1 );
        $dataTotalShippingFee = \models\shipping_services::listing( "", 1, 'cnt' );

        $output = '<ul class="list-results">';

        if( is_array($dataListShippingFee) AND $dataTotalShippingFee > 0 )
        {
            $cnt = count($dataListShippingFee);
            $i = 0;
            foreach( $dataListShippingFee as $data ) 
            {
                $i++;
                $last = ($cnt == $i) ? 'last' : '';
                $output .= <<<EOF
                <li>
                    <div class="clearfix item {$last}">
                        <a class='pointer inline-block' href='{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee&subact=edit&id={$data['ship_id']}'>
                        <h5>{$data['product_name']}</h5>
EOF;
                        if( $data['product_barcode'] )
                        {
                            $output .= <<<EOF
                            <div><small class="text-small"><i class="fa fa-barcode"></i> {$data['product_barcode']}</small></div>
EOF;
                        }

                    $output .= <<<EOF
                            <div>
                                <small class="text-small"><i class="fa fa-truck rtl"></i> {$data['shipping_type']} - {$data['shipping_location']}/ </small>
                                <small class="text-small">{$CMS->lang['ship_fee_price']}: {$data['us_shipping_c']} </small>
                                <small class="text-small">{$CMS->lang['ship_fee_price_extra_short']}: {$data['us_extra_c']}</small>
                            </div>
                        </a>
                    </div>
                </li>
EOF;
            }

            if( $dataTotalShippingFee > $limit )
            {
                $remain = $dataTotalShippingFee - $limit;
                $output .= "<li class='more-results'><a href='{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee&keywords={$CMS->input['keywords']}'>{$CMS->lang['view_more']} ({$remain}) {$CMS->lang['result']}</a></li>";
            }
        }
        else
        {
            $output .= "<li>No data...</li>";
        }

        $output .= '</ul>';

        print \lib\input::jsonEncode(array('status' => 'success', 'data' => $output), 0); exit;
    }
}

?>