<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->report = new class_report; 

class class_report {
	
	public $html;

	public $report = "";
	public $report_display = 2;
	public $report_date = array(); 

	public $sheet_data = array();	
	public $sheet_data_album = array();	
	public $sheet_data_video = array();	
	public $sheet_date = ""; // Use for sheet, save
	public $sheet_date_display = ""; // Use for sheet, live, load directly from each row
	public $sheet_now = ""; // Use for row of sheet
	public $sheet_cnt = 0;
	public $sheet_row = 2;
	public $total = 0;
	
	public $download = array("excel", "vnnic");
	public $mod_name = "";
	
	public $is_default=0;
	public $dataExcel = "";
	
	public function loadhtml()
	{
		global $CMS, $DB, $member;

		if ( !isset($this->html) )
		{	
			if ( $CMS->vars['is_admin_module'] )
			{
				$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/report/templates/skin_report.php");
			}
			else
			{
				$this->html = $CMS->class->template->load_template("skin_report");
			}
		}
	}
	
	//===========================================================================
	//  CUSTOMER DATA
	//===========================================================================

	public function defaultvalue( $data = array() )
	{
		global $CMS, $member, $DB;

		$data['report_start'] = $CMS->input['report_start'];
		$data['report_end'] = $CMS->input['report_end'];
		return $data;
	}
	
	
	
	
	
	//===========================================================================
	//  BUILD EXCEL
	//===========================================================================
	
	public function build_excel_header()
	{
		global $CMS, $DB;
		
		// Remove permission
		unset($CMS->permit);
		
		// Build
		$CMS->class->excel->build();
	}
	
	public function build_excel_footer( $name = "Report_service" )
	{
		global $CMS, $DB;

		// Set first sheet as default when open
		$CMS->class->excel->set_sheet($this->sheet_cnt-1);
		
		// Download
		$CMS->class->excel->download($name);
	}
	
	//===========================================================================
	//  BUILD SHEET
	//===========================================================================
	
	public function build_sheet( $input )
	{
		global $CMS, $DB;
		
		if ( $this->report_display == 1 )
		{
			$output = $input;
		}
		else if ( $this->report_display == 2 )
		{
			$output = substr($input, -7);
		}
		else if ( $this->report_display == 3 )
		{
			$output = substr($input, -4);
		}
		
		return $output;
	}
	
	//===========================================================================
	//  SET HEADER INPUT
	//===========================================================================
	
	public function set_header_input($is_sub="")
	{
		global $CMS, $DB;
		
		// Default value
		$CMS->input['is_sub'] = $is_sub;
		$CMS->input = ($_SESSION['input'] && $is_sub == 1) ? $_SESSION['input'] : $CMS->input;
		$CMS->input = $this->defaultvalue($CMS->input);
		
		//$CMS->input['report_start'] = $_POST['report_start'];
	//	$CMS->input['report_end'] = $_POST['report_end'];
		// Order input
		$this->report['service_type'] = preg_match("/(t)/", $CMS->input['report_service_group']) == true ? 1 : 0;
		$this->report['service_group'] = intval(str_replace("t", "", $CMS->input['report_service_group']));
		$this->report['payment_status'] = $CMS->input['report_payment_status'];
		$this->report['ord_status'] = $CMS->input['ord_status'];

		// Exactly report
		$this->report['employee'] = $CMS->input['report_employee'];
		$this->report['customer'] = $CMS->input['report_customer'];
		$this->report['member'] = $CMS->input['report_member'];
		$this->report['reseller_payment'] = $CMS->input['report_reseller_payment'];
		
		// Transaction input
		$this->report['transaction_type'] = $CMS->input['report_transaction_type'];
		$this->report['acc1'] = $CMS->input['report_acc1'];
		$this->report['acc2'] = $CMS->input['report_acc2'];
		$this->report['transaction_debt'] = $CMS->input['report_transaction_debt'];
		$this->report['transaction_bill'] = $CMS->input['report_transaction_bill'];

		// Payment input
		$this->report['payment_receiver'] = $CMS->input['report_payment_receiver'];
		$this->report['payment_type'] = $CMS->input['report_payment_type'];
		$this->report['payment_type2'] = $CMS->input['report_payment_type2'];
		
		// General input
		$this->report['start'] = $CMS->input['report_start'] ? $CMS->input['report_start'] : "";
		$this->report['end'] = $CMS->input['report_end'] ? $CMS->input['report_end'] : "";
		$this->report['report_start'] = $CMS->input['report_start'] ? $CMS->input['report_start'] : "";
		$this->report['report_end'] =  $CMS->input['report_end'] ? $CMS->input['report_end'] : "";
	
		$this->report['per_page'] = intval($CMS->input['report_per_page']);
		$this->report['location'] = $CMS->input['report_location'];
		$this->report['type'] = $CMS->input['report_type'];
		$this->report['display'] = intval($CMS->input['report_display']);
		$this->report['full'] = intval($CMS->input['report_full']);
		$this->report['payment_method'] = $CMS->input['report_payment_method'];
		$this->report['format'] = $CMS->input['report_format'];
		
		$this->report['ord_member_status'] = $CMS->input['ord_member_status'];
		
		// Report total
		$this->report['svctype_1'] = $CMS->input['report_svctype_1'];
		$this->report['svctype_2'] = $CMS->input['report_svctype_2'];
		$this->report['svctype_3'] = $CMS->input['report_svctype_3'];
		$this->report['svctype_4'] = $CMS->input['report_svctype_4'];
		$this->report['svctype_5'] = $CMS->input['report_svctype_5'];
		$this->report['svctype_6'] = $CMS->input['report_svctype_6'];
		
		$this->report['service'] = $CMS->input['report_service'];
		
		$this->report['service_list'] = $CMS->input['service_list'];
		
		// Get service type
		$this->report['type_1'] = $CMS->input['report_type_1'];
		$this->report['type_2'] = $CMS->input['report_type_2'];
		$this->report['type_3'] = $CMS->input['report_type_3'];
		
		// Report total method
		$this->report['total_method'] = $CMS->input['report_total_method'];
		
		// Total cycle
		$this->report['report_cycle_1'] = $CMS->input['report_cycle_1'];
		$this->report['report_cycle_2'] = $CMS->input['report_cycle_2'];
		$this->report['report_cycle_3'] = $CMS->input['report_cycle_3'];
		$this->report['report_cycle_4'] = $CMS->input['report_cycle_4'];
		$this->report['report_cycle_5'] = $CMS->input['report_cycle_5'];
		$this->report['report_cycle_6'] = $CMS->input['report_cycle_6'];
		
		// Total payment status
		$this->report['total_payment_status_1'] = $CMS->input['total_payment_status_1'];
		$this->report['total_payment_status_2'] = $CMS->input['total_payment_status_2'];
		$this->report['total_payment_status_3'] = $CMS->input['total_payment_status_3'];
		
		// Total payment status
		$this->report['total_payment_method_1'] = $CMS->input['total_payment_method_1'];
		$this->report['total_payment_method_2'] = $CMS->input['total_payment_method_2'];
		$this->report['total_payment_method_3'] = $CMS->input['total_payment_method_3'];
		
		// Total year
		$this->report['total_year'] = $CMS->input['report_total_year'];
		
		// Set value again for report all
		if($this->mod_name == "report_all")
		{
			//$temp = explode("/",$this->report['start']); 
			//$this->report['start'] = !$this->report['total_year'] ? "01/{$temp[1]}/{$temp[2]}" : "01/{$temp[1]}/{$this->report['total_year']}";
			
			//$temp = explode("/",$this->report['end']);
			//$end = $CMS->class->date->get_dayofmonth($temp[1],$this->report['total_year']);
			//$this->report['end'] = !$this->report['total_year'] ? "{$end}/{$temp[1]}/{$temp[2]}" : "{$end}/{$temp[1]}/{$this->report['total_year']}";
			
			$this->report['display'] = 2; 
			$this->report['full'] = 2;
		}
	}
	
	//===========================================================================
	//  SET FOOTER INPUT
	//===========================================================================
	
	public function set_footer_input()
	{
		global $CMS, $DB;
		
		// Search
		$CMS->class->search->url_return = "&report_service_group=".($this->report['service_type'] == 0 ? "" : "t")."{$this->report['service_group']}";
		$CMS->class->search->url_return .= "&report_start={$this->report['start']}";
		$CMS->class->search->url_return .= "&report_end={$this->report['end']}";
		$CMS->class->search->url_return .= "&report_per_page={$this->report['per_page']}";
		$CMS->class->search->url_return .= "&report_location={$this->report['location']}";
		$CMS->class->search->url_return .= "&report_type={$this->report['type']}";
		$CMS->class->search->url_return .= "&report_display={$this->report['display']}";
		$CMS->class->search->url_return .= "&report_full={$this->report['full']}";
		$CMS->class->search->url_return .= "&report_payment_method={$this->report['payment_method']}";
		$CMS->class->search->url_return .= "&report_format={$this->report['format']}";
		
		// Transaction
		if ( $CMS->input['act'] == "report_transaction" )
		{
			$CMS->class->search->url_return .= "&report_transaction_type={$this->report['transaction_type']}";
			$CMS->class->search->url_return .= "&report_acc1={$this->report['report_acc1']}";
			$CMS->class->search->url_return .= "&report_acc2={$this->report['report_acc2']}";
			$CMS->class->search->url_return .= "&report_transaction_debt={$this->report['transaction_debt']}";
		}
		
		// Payment
		if ( $CMS->input['act'] == "report_payment" )
		{
			$CMS->class->search->url_return .= "&report_payment_receiver={$this->report['report_payment_receiver']}";
			$CMS->class->search->url_return .= "&report_payment_type={$this->report['report_payment_type']}";
			$CMS->class->search->url_return .= "&report_payment_type2={$this->report['report_payment_type2']}";
		}
		
		// Exactly report
		if ( $CMS->input['act'] == "report_sales" )
		{
			$CMS->class->search->url_return .= "&report_employee={$this->report['employee']}";
			$CMS->class->search->url_return .= "&report_reseller_payment={$this->report['reseller_payment']}";
			$CMS->class->search->url_return .= "&report_customer={$this->report['customer']}";
		}
		
		// Location
		$CMS->input['location'] = $this->report['location'];
	}
	
	//===========================================================================
	//  SET HEADER
	//===========================================================================
	
	public function set_excel_header()
	{
		global $CMS, $DB;
		
		// Set sheet
		$CMS->class->excel->set_sheet($this->sheet_cnt);
		// $CMS->class->excel->set_title(str_replace("/","-", $this->sheet_date_display));

		// // Set style for header
		// $CMS->class->excel->set_style('A1:Z1', 'header');
		
		// // Style column
		// $col = str_split($CMS->class->excel->col);
		
		// for ( $i = 0; $i < count($this->sheet_data); $i++ )
		// {
		// 	if ( $this->sheet_data[$i] )
		// 	{
		// 		// Set column
		// 		$CMS->class->excel->set_column($col[$i], $this->sheet_data[$i][0]);
				
		// 		// Set header
		// 		$CMS->class->excel->set_value($col[$i].'1', $CMS->lang[$this->sheet_data[$i][1]], $this->sheet_data[$i][2]);
		// 	}
		// }
		
		// // Reset sheet
		// $this->sheet_date = $this->sheet_date_display;
		// $this->sheet_row = 2;
		// $this->total = 0;
		// $this->sheet_cnt++;
	}
	
	//===========================================================================
	//  SET CONTENT
	//===========================================================================
	
	public function set_excel_content( $data )
	{
		global $CMS, $DB;
		
		// Check for mark transaction is out of date
		if($data['transaction_is_debt'] > 0)
		{
			$day_1 = substr($data['transaction_debt_time'],0,2);
			$day_2 = substr($data['transaction_confirm_time'],0,2);
			
			$date_1 = substr($data['transaction_debt_time'],3,2);
			$date_2 = substr($data['transaction_confirm_time'],3,2);
		
			$is_mark = (($date_1 == $date_2) && ($day_1 == $day_2)) ? "" : "red";
		}
		else if($data['transaction_confirm_time'])
		{
			$start_time = $data['transaction_time_short'];
			$confirm_time = $data['transaction_confirm_time_short'];
			
			$day_1 = substr($data['transaction_time_short'],0,2);
			$day_2 = substr($data['transaction_confirm_time_short'],0,2);
			
			$date_1 = substr($data['transaction_time_short'],3,2);
			$date_2 = substr($data['transaction_confirm_time_short'],3,2);
		
			$is_mark = (($date_1 == $date_2) && ($day_1 == $day_2)) ? "" : "red";
		}
			
		// Style column
		$col = str_split($CMS->class->excel->col);
		
		for ( $i = 0; $i < count($this->sheet_data); $i++ )
		{
			if ( $this->sheet_data[$i] )
			{
				// Set value
				$CMS->class->excel->set_value($col[$i].$this->sheet_row, $data[$this->sheet_data[$i][1]], $is_mark ? $is_mark :$this->sheet_data[$i][2], $this->sheet_data[$i][3]);
			}
		}
		
		$this->sheet_row++;
	}
	//===========================================================================
	//  SET CONTENT
	//===========================================================================
	
	public function set_excel_content_all( $data )
	{
		global $CMS, $DB;
		
		
		// Style column
		$col = str_split($CMS->class->excel->col);
		
	
				for ( $i = 0; $i < count($this->sheet_data); $i++ )
				{
					if ( $this->sheet_data[$i] )
					{
						// Set value
						$CMS->class->excel->set_value($col[$i].$this->sheet_row, $data[$this->sheet_data[$i][1]], $is_mark ? $is_mark :$this->sheet_data[$i][2], $this->sheet_data[$i][3]);
					}
				}
			
			$this->sheet_row++;
		
		
	}
	
	
	
	//===========================================================================
	//  SET TITLE
	//===========================================================================
	
	public function set_excel_title( $title_name )
	{
		global $CMS, $DB;

		// Style column
		$col = str_split($CMS->class->excel->col);

		$CMS->class->excel->set_value($col[0].$this->sheet_row, $title_name);
		$CMS->class->excel->merge_cell($col[0].$this->sheet_row.':'.$col[count($this->sheet_data)-1].$this->sheet_row);
		$CMS->class->excel->set_style($col[0].$this->sheet_row.':'.$col[count($this->sheet_data)-1].$this->sheet_row,'header');
		$this->sheet_now = $title_name;
		$this->sheet_row++;
	}
	
	//===========================================================================
	//  BUILD LAST LINE
	//===========================================================================
	
	public function build_last_line( $lang_total = "Total" )
	{
		global $CMS;
		
		if ( $this->report['format'] == "vnnic" )
		{
			return false;	
		}
		
		// Style column
		$col = str_split($CMS->class->excel->col);

		$this->sheet_row++;
		$CMS->class->excel->merge_cell('A'.$this->sheet_row.':'.$col[count($this->sheet_data)-2].$this->sheet_row);
		$CMS->class->excel->set_value('A'.$this->sheet_row, $lang_total, 'align_right');
		$CMS->class->excel->set_value($col[count($this->sheet_data)-1].$this->sheet_row, $this->total, 'align_right');	// , 'currency'
	}
	
			
	//===========================================================================
	//  ORDER
	//===========================================================================
	
	public function order()
	{
		global $CMS, $DB;
		
		// Header input
		$this->set_header_input();

		// Load language
		$CMS->class->language->load("transaction");

		$this->comnbina_order_listing();
		
		// Statistics
		$data['report_name'][0] = $CMS->lang['report_chart_order'] ." ". $service_name ." {$CMS->lang['report_by']} ".$CMS->lang["report_display_{$this->report['display']}"];
		$data['report_name'][1] = $this->report['start'] ."   &rarr;   ". $this->report['end'];
		$data['report_display'] = $this->report['display'];
		
		//print "SELECT COUNT(ord_id) AS number,SUM(O.ord_amount) as count, DATE_FORMAT(FROM_UNIXTIME(IF(O.ord_payment_confirm_time > 0,O.ord_payment_confirm_time,O.ord_time)), '{$this->report_date[$data['report_display']]}') AS date {$sql_select} FROM ".root_table."order {$CMS->order->sql_table} WHERE {$CMS->order->sql_add} 1=1 GROUP BY date";exit;
		$data['report_sql'] = "SELECT COUNT(ord_id) AS number,SUM(IF(O.ord_is_vat,O.ord_total,O.ord_amount)) as count, DATE_FORMAT(FROM_UNIXTIME(IF('{$this->report['reseller_payment']}'=1,O.ord_time,O.ord_payment_confirm_time)), '{$this->report_date[$data['report_display']]}') AS date {$sql_select} FROM ".root_table."order {$CMS->order->sql_table} WHERE {$CMS->order->sql_add} 1=1 GROUP BY date";
		$data['report_start'] = $this->report['start'];
		$data['report_end'] = $this->report['end'];
		
		if ( in_array($this->report['format'], $this->download) == true )
		{
			$this->report_display = 2;
			$this->build_excel_header();
			$this->build_excel_order();
			$this->build_excel_footer();
		}
		else
		{		
			return $data;
		}
	}

	public function build_excel_module()
	{
		global $CMS, $DB;
	
		// Sheet data
		/*$this->sheet_data = array(
			array(15, "news_id"),
			array("auto", "news_name"),
			array("auto", "parent_cat_id_bk", "wrap"),
			array("auto", "cat_id_bk"),
			array("auto", "news_time"),
			array("auto", "user_id", "align_right"),
			array(15, "news_royalty"),
		);*/
		
		// Vnnic
		if ( $this->report['format'] == "news" OR $this->report['module_id'] == 0 )
		{
			$this->sheet_data = array(
			array(15, "empty"),
			array("auto", "news_name_bk"),
			array("auto", "parent_cat_id_bk2", "wrap"),
			array("auto", "cat_id_bk2"),
			array("auto", "news_active_bk", "align_right"),
			array("auto", "news_time"),
			array("auto", "user_name", "align_right"),
			array(15, "news_royalty"),
			);	
		}
		if ( $this->report['format'] == "album" OR $this->report['module_id'] == 1)
		{
			$this->sheet_data = array(
			array(15, "empty"),
			array("auto", "album_name"),
			array("auto", "cat_id_bk"),
			array("auto", "album_active_bk", "align_right"),
			array("auto", "album_time"),
			array("auto", "user_name", "align_right"),
			array(15, "album_royalty"),
			);	
		}
		
		if ( $this->report['format'] == "video" OR $this->report['module_id'] == 2)
		{
			$this->sheet_data = array(
			array(15, "empty"),
			array("auto", "video_name"),
			array("auto", "cat_id_bk"),
			array("auto", "video_active_bk", "align_right"),
			array("auto", "video_time"),
			array("auto", "user_name", "align_right"),
			array(15, "video_royalty"),
			);	
		}
		
		
		
		// News header
		if ( $this->report['format'] == "news" OR $this->report['module_id'] == 0)
		{
			$col = str_split($CMS->class->excel->col);
			$i = 1;
			$itable = 0;
			
			$CMS->class->excel->set_column($col[0], 3 );
			$CMS->class->excel->set_column($col[1], 40 );
			$CMS->class->excel->set_column($col[2], 30 );
			$CMS->class->excel->set_column($col[3], 24 );
			$CMS->class->excel->set_column($col[4], 24 );
			$CMS->class->excel->set_column($col[5], 25 );
			$CMS->class->excel->set_column($col[6], 15 );
			$CMS->class->excel->set_column($col[7], 30 );
			
			$CMS->class->excel->set_value($col[1].$i, "Tên bài viết", "bold");
			$CMS->class->excel->set_value($col[2].$i, "Danh mục cha", "bold");
			$CMS->class->excel->set_value($col[3].$i, "Danh mục con", "bold");
			$CMS->class->excel->set_value($col[4].$i, "Tình trạng", "bold");
			$CMS->class->excel->set_value($col[5].$i, "Ngày post", "bold");

			$CMS->class->excel->set_value($col[6].$i, "Tên thành viên", "bold");
			$CMS->class->excel->set_value($col[7].$i, "Nhuận bút", "bold");
	
			$itable = $i;
			$i++;

			$this->sheet_row = $i;
		}
		
		// Album header
		if ( $this->report['format'] == "album" OR $this->report['module_id'] == 1)
		{
			$col = str_split($CMS->class->excel->col);
			$i = 1;
			$itable = 0;
			
			$CMS->class->excel->set_column($col[0], 3 );
			$CMS->class->excel->set_column($col[1], 45 );
			$CMS->class->excel->set_column($col[2], 30 );
			$CMS->class->excel->set_column($col[4], 24 );
			$CMS->class->excel->set_column($col[5], 15 );
			$CMS->class->excel->set_column($col[6], 15 );
			$CMS->class->excel->set_column($col[7], 30 );
			
			$CMS->class->excel->set_value($col[1].$i, "Tên bài viết", "bold");
			$CMS->class->excel->set_value($col[2].$i, "Danh mục", "bold");
			$CMS->class->excel->set_value($col[3].$i, "Ngày post", "bold");
			$CMS->class->excel->set_value($col[4].$i, "Tình trạng", "bold");
			$CMS->class->excel->set_value($col[5].$i, "Tên thành viên", "bold");
			$CMS->class->excel->set_value($col[6].$i, "Nhuận bút", "bold");
	
			$itable = $i;
			$i++;

			$this->sheet_row = $i;
		}
		
		
		// News header
		if ( $this->report['format'] == "video" OR $this->report['module_id'] == 2)
		{
			$col = str_split($CMS->class->excel->col);
			$i = 1;
			$itable = 0;
			
			$CMS->class->excel->set_column($col[0], 3 );
			$CMS->class->excel->set_column($col[1], 55 );
			$CMS->class->excel->set_column($col[2], 30 );
			$CMS->class->excel->set_column($col[3], 24 );
			$CMS->class->excel->set_column($col[4], 24 );
			$CMS->class->excel->set_column($col[5], 15 );
			$CMS->class->excel->set_column($col[6], 15 );
			$CMS->class->excel->set_column($col[7], 30 );
			
			$CMS->class->excel->set_value($col[1].$i, "Tên bài viết", "bold");
			$CMS->class->excel->set_value($col[2].$i, "Danh mục", "bold");
			$CMS->class->excel->set_value($col[3].$i, "Ngày post", "bold");
			$CMS->class->excel->set_value($col[4].$i, "Tình trạng", "bold");
			$CMS->class->excel->set_value($col[5].$i, "Tên thành viên", "bold");
			$CMS->class->excel->set_value($col[6].$i, "Nhuận bút", "bold");
	
			$itable = $i;
			$i++;

			$this->sheet_row = $i;
		}
		
		if ( $this->report['format'] == "news" OR $this->report['module_id'] == 0)
		{
			// Initialize
			while( $data = $DB->fetch_array( $CMS->news->sql_query ) )
			{
				$data = $CMS->news->convertvalue($data);
				// Content
				$this->set_excel_content($data);
				// Write last line
				$this->total += $data['news_royalty'];
			}
		}
		elseif ( $this->report['format'] == "album" OR $this->report['module_id'] == 1)
		{
			// Initialize
			while( $data = $DB->fetch_array( $CMS->album->sql_query ) )
			{
				$data = $CMS->album->convertvalue($data);
				// Content
				$this->set_excel_content($data);
				// Write last line
				$this->total += $data['album_royalty'];
			}
		}
		
		elseif ( $this->report['format'] == "video" OR $this->report['module_id'] == 2)
		{
			// Initialize
			while( $data = $DB->fetch_array( $CMS->video->sql_query ) )
			{
				$data = $CMS->video->convertvalue($data);
				// Content
				$this->set_excel_content($data);
				// Write last line
				$this->total += $data['video_royalty'];
			}
		}
		
			
		// Write last line
		$this->build_last_line($CMS->lang['ord_total']);
	}


	public function build_excel_module_all($data)
	{
		global $CMS, $DB;
	
			$this->sheet_data = array(
			array(15, "empty"),
			array("auto", "name"),
			array("auto", "cat_id"),
			array("auto", "active", "align_right"),
			array("auto", "time_bk"),
			array("auto", "user_name", "align_right"),
			array(15, "royalty"),
			);	
	
		// News header

			$col = str_split($CMS->class->excel->col);
			$i = 1;
			$itable = 0;
			
			$CMS->class->excel->set_column($col[0], 3 );
			$CMS->class->excel->set_column($col[1], 55 );
			$CMS->class->excel->set_column($col[2], 30 );
			$CMS->class->excel->set_column($col[3], 24 );
			$CMS->class->excel->set_column($col[4], 24 );
			$CMS->class->excel->set_column($col[5], 15 );
			$CMS->class->excel->set_column($col[6], 15 );
			$CMS->class->excel->set_column($col[7], 30 );
			
			$CMS->class->excel->set_value($col[1].$i, "Tên bài viết", "bold");
			$CMS->class->excel->set_value($col[2].$i, "Danh mục", "bold");
			$CMS->class->excel->set_value($col[3].$i, "Tình trạng", "bold");
			$CMS->class->excel->set_value($col[4].$i, "Ngày post", "bold");
			$CMS->class->excel->set_value($col[5].$i, "Tên thành viên", "bold");
			$CMS->class->excel->set_value($col[6].$i, "Nhuận bút", "bold");
	
			$itable = $i;
			$i++;

			$this->sheet_row = $i;
		
		// lap du lieu
		for($i = 0; $i <= count($data);$i++)
		{
			// Content
				$this->total += $data[$i]['royalty'];
			$this->set_excel_content_all($data[$i]);
		
		}
			
		// Write last line
		$this->build_last_line("Tổng tiền: ");
	}
	
	//===========================================================================
	//  TRANSACTION
	//===========================================================================
	
	public function transaction()
	{
		global $CMS, $DB;

		// Header input
		$this->set_header_input();
		
		// Load language
		$CMS->class->language->load("transaction");

		// Payment method
		if ( ! empty($this->report['payment_method']) ) { $CMS->transaction->sql_add .= " transaction_payment_method='{$this->report['payment_method']}' AND "; }
		else {$CMS->transaction->sql_add .= " transaction_payment_method!=4 AND ";}

		// Type
		if ( ! empty($this->report['transaction_type']) ) { $CMS->transaction->sql_add .= " transaction_type='{$this->report['transaction_type']}' AND "; }
		else {$CMS->transaction->sql_add .= " (transaction_type!=3) AND ";}
		
		// Is debt
		if ( ! empty($this->report['transaction_debt']) ) { $CMS->transaction->sql_add .= " transaction_is_debt='{$this->report['transaction_debt']}' AND transaction_status='0' AND "; }
		else 
		{
			// Status
			$CMS->transaction->sql_add .= " transaction_status=1 AND ";
		}

		// Start time, end time
		$CMS->transaction->sql_add .= $this->report['start'] ? " transaction_confirm_time >= ".$CMS->class->date->date2time($this->report['start'],1)." AND " : "";
		$CMS->transaction->sql_add .= $this->report['end'] ? " transaction_confirm_time <= ".($CMS->class->date->date2time($this->report['end'],1)+24*3600)." AND " : "";
		
		// Accountnat
		$CMS->transaction->sql_add .= $this->report['acc1'] ? " transaction_acc='{$this->report['acc1']}' AND " : "";
		$CMS->transaction->sql_add .= $this->report['acc2'] ? " transaction_acc2='{$this->report['acc2']}' AND " : "";

		// Sort
		$CMS->transaction->order_extend = "";
		$CMS->transaction->order_field = "transaction_confirm_time";
		$CMS->transaction->order_by = "ASC";
		
		// Footer input
		$this->set_footer_input();
	
		// Page
		$CMS->transaction->per_page = in_array($this->report['format'], $this->download) == true ? 999999 : $this->report['per_page'];
			
		// Select group_concat, 16-05-2012, HLoi
		$CMS->transaction->sql_select = "O.contract_id, COUNT(O.ord_id) as transaction_ordercnt, GROUP_CONCAT(O.ord_name,if(O.ord_domain!='',concat(' - ',O.ord_domain),''),' - ',S.service_display_name,'|') as transaction_orderlist, T.*";
		$CMS->transaction->sql_table = "AS T LEFT JOIN nh_invoice AS I ON I.inv_id=T.inv_id LEFT JOIN nh_order AS O ON O.inv_id=I.inv_id LEFT JOIN nh_service AS S ON S.service_id=O.service_id";
		$CMS->transaction->sql_group = "GROUP BY T.transaction_id";
		//$CMS->transaction->sql_extend = "transaction_ordercnt DESC,";
		$CMS->transaction->is_report = 1;
		
		if ( in_array($this->report['format'], $this->download) == true )
		{
			$CMS->transaction->is_report = 2;
		}
		
		// Get List
		$CMS->transaction->listing();
		
		// Statistics
		$data['report_name'][0] = $CMS->lang['report_chart_transaction'] ." {$CMS->lang['report_by']} ".$CMS->lang["report_display_{$this->report['display']}"];
		$data['report_name'][1] = $this->report['start'] ."   &rarr;   ". $this->report['end'];
		$data['report_display'] = $this->report['display'];
		
		//print "SELECT COUNT(transaction_id) AS number, SUM(transaction_total) as count, DATE_FORMAT(FROM_UNIXTIME(transaction_confirm_time), '{$this->report_date[$data['report_display']]}') AS date FROM ".root_table."transaction {$CMS->transaction->sql_table} WHERE {$CMS->transaction->sql_add} 1=1 GROUP BY date";exit;
		$data['report_sql'] = "SELECT COUNT(transaction_id) AS number, SUM(IF(transaction_is_bill, transaction_balance,transaction_total)) as count, DATE_FORMAT(FROM_UNIXTIME(transaction_confirm_time), '{$this->report_date[$data['report_display']]}') AS date FROM ".root_table."transaction WHERE {$CMS->transaction->sql_add} 1=1 GROUP BY date";
;
		$data['count'] = 0;
		$sql = $DB->query("SELECT * FROM ".root_table."transaction WHERE {$CMS->transaction->sql_add} 1=1");
		while($total = $DB->fetch_array($sql))
		{
			$data['count'] += $total['transaction_total'];	
		}
		
		$data['report_end'] = $this->report['end'];
		
		if ( in_array($this->report['format'], $this->download) == true )
		{
			$this->report_display = 2;
			$this->build_excel_header();
			$this->build_excel_transaction();
			$this->build_excel_footer();
		}
		else
		{
			return $data;
		}
	}

	public function build_excel_transaction()
	{
		global $CMS, $DB;
	
		// Sheet data
		$this->sheet_data = array(
			array(15, "transaction_name", "valign_top"),  
			array("auto","inv_id", "valign_top"),
			array("auto","contract_name", "valign_top"),
			array("auto","transaction_orderlist", "wrap"),
			//array(15, "inv_id"),
			array("auto", "cus_name", "valign_top"),
			//array("auto", "transaction_sender_user"),
			//array("auto", "transaction_sender_provider"),  
			//array("auto", "transaction_sender_other"),
			array("auto", "transaction_acc", "valign_top"),
			array("auto", "transaction_acc2", "valign_top"),
			array("auto", "transaction_type", "valign_top"),
			array("auto", "transaction_payment_method", "valign_top"),
			array("auto", "transaction_status_exel"),
			array("auto","bank_id","valign_top"),
			array("auto", "transaction_confirm_by", "valign_top"),
			array("auto", "transaction_time_short", "valign_top"),
			array("auto", "transaction_confirm_time_short", "valign_top"),
			array(15, "transaction_real_bk", array("align_right", "valign_top")), // , "currency"
		);

		// Initialize
		while( $data = $DB->fetch_array( $CMS->transaction->sql_query ) )
		{
			// Get order
			//$order = $CMS->order->get_info($data['inv_id']);
			//$data['ord_name'] = $order['ord_name'];
			//$data['ord_cycle'] = $order['ord_cycle'];
			//$data['ord_cycle_type'] = $order['ord_cycle_type'];
				
			// Get service
			//$sv_id = $CMS->order->get_info($data['inv_id'],"service_id");
			//$data['service_id'] = $CMS->service->get_info($sv_id,"service_name");
				
			//$data['contract_id'] = $CMS->order->get_info($data['inv_id'],"contract_id");
			//$data['contract_id'] = $CMS->contract->get_info($data['contract_id'],"contract_name");
			//$temp = $data;
			
			$data = $CMS->transaction->convertvalue($data);
			$data['transaction_orderlist'] = str_replace("<br />"," - ",$data['transaction_orderlist']);

			// Report display
			$this->sheet_date_display = $this->build_sheet($data['transaction_confirm_time_short']); 

			// Set sheet
			if ( $this->sheet_date_display != $this->sheet_date )
			{				
				if ( $this->sheet_cnt != 0 )
				{
					// Write last line
					$this->build_last_line($CMS->lang['transaction_total']);
					
					// Continue open new sheet		
					$CMS->class->excel->data->createSheet();	
				}
				
				// Reset sheet
				$this->set_excel_header();
			}
			
			// Set date row
			if ( $data['transaction_confirm_time_short'] != $this->sheet_now )
			{
				$this->set_excel_title($data['transaction_confirm_time_short']);
			}
			
			// Content
			$this->set_excel_content($data);
			
			// Write last line
			$this->total += $data['transaction_real_bk'];
		}
		
		// Write last line
		$this->build_last_line($CMS->lang['transaction_total']);
	}

	//===========================================================================
	//  PAYMENT
	//===========================================================================
	
	public function all()
	{
		global $CMS, $DB;

		// Header input
		$this->set_header_input();

		$sql_add = $this->convert_input($CMS->input);
		$this->report['per_page'] = $CMS->input['report_per_page'];
		$this->report['format']= $CMS->input['report_format'];
		$this->report['module_id'] = $CMS->input['module_id'];
		$this->report['report_royalty'] = $CMS->input['royalty'];
		$this->report['report_active'] = $CMS->input['active'];	
		$this->report['sub_cate_news'] = $CMS->input['sub_cate_news'];	
		$this->report['sub_cate_album'] = $CMS->input['sub_cate_album'];	
		$this->report['sub_cate_video'] = $CMS->input['sub_cate_video'];	
		$this->report['user_id'] = $CMS->input['user_id'];	
	
	//	print_r ($this->report );exit;
		//print_r ($this->report['report_start']);exit;
		// Load language
		$CMS->class->language->load("news");
		if($CMS->input['module_id'] == 0 )
		{
			// Status
			$CMS->news->sql_add .= $sql_add;
			// Sort Nhuan but
			if($this->report['report_royalty'] != "" )
			{	
				if($this->report['report_royalty'] == 0 )
				{
					$CMS->news->sql_add .= "news_royalty = 0 AND " ;
				}
				elseif($this->report['report_royalty'] == 1 )
				{
					$CMS->news->sql_add .= "news_royalty > 0 AND " ;
				}
			}
			// Sort tinh trang bai viet
			if($this->report['report_active'] != "" )
			{	
					$CMS->news->sql_add .= "news_active = {$this->report['report_active']} AND " ;
			}
			

			// Start time, end time
			$CMS->news->sql_add .= $this->report['report_start'] ? " news_time > ".$CMS->class->date->date2time($this->report['report_start'],1)." AND " : "";
			$CMS->news->sql_add .= $this->report['report_end'] ? " news_time < ".($CMS->class->date->date2time($this->report['report_end'],1)+24*3600)." AND " : "";

			// Sort
			$CMS->news->order_extend = "";
			$CMS->news->order_field = "news_time";
			$CMS->news->order_by = "ASC";
			// Page
			$CMS->news->per_page =  $this->report['per_page'];
			$CMS->news->suffix_html = "&act={$this->mod_name}&user_id={$this->report['user_id']}&module_id={$this->report['module_id']}&sub_cate_news={$this->report['sub_cate_news']}&royalty={$this->report['report_royalty']}&active={$this->report['report_active']}&report_per_page={$this->report['report_per_page']}&report_start={$this->report['report_start']}&report_end={$this->report['report_end']}";
			// Get List
			$CMS->news->listing_report();
		}
		elseif($CMS->input['module_id'] == 1 )
		{
			// Status
			$CMS->album->sql_add .= $sql_add;
			// Sort Nhuan but
			if($this->report['report_royalty'])
			{	
				if($this->report['report_royalty'] == 0 )
				{
					$CMS->album->sql_add .= "album_royalty = 0 AND " ;
				}
				elseif($this->report['report_royalty'] == 1 )
				{
					$CMS->album->sql_add .= "album_royalty > 0 AND " ;
				}
			}
			// Sort tinh trang bai viet
			if($this->report['report_active'] != "" )
			{	
					$CMS->album->sql_add .= "album_active = {$this->report['report_active']} AND " ;
			}
			
			// Start time, end time
			$CMS->album->sql_add .= $CMS->input['report_start'] ? " album_time > ".$CMS->class->date->date2time($CMS->input['report_start'],1)." AND " : "";
			$CMS->album->sql_add .= $this->input['report_end'] ? " album_time < ".($CMS->class->date->date2time($CMS->input['report_start'],1)+24*3600)." AND " : "";
			// Sort
			$CMS->album->order_extend = "";
			$CMS->album->order_field = "album_time";
			$CMS->album->order_by = "ASC";
			// Page
			$CMS->album->per_page = in_array($this->report['format'], $this->download) == true ? 999999 : $this->report['per_page'];
			$CMS->album->suffix_html = "&act={$this->mod_name}&user_id={$this->report['user_id']}&module_id={$this->report['module_id']}&sub_cate_album={$this->report['sub_cate_album']}&royalty={$this->report['report_royalty']}&active={$this->report['report_active']}&report_per_page={$this->report['report_per_page']}&report_start={$this->report['report_start']}&report_end={$this->report['report_end']}";
			// Get List
			$CMS->album->listing();
		}
		elseif($CMS->input['module_id'] == 2 )
		{
			// Status
			$CMS->video->sql_add .= $sql_add;
			// Sort Nhuan but
			if($this->report['report_royalty'])
			{	
				if($this->report['report_royalty'] == 0 )
				{
					$CMS->video->sql_add .= "video_royalty = 0 AND " ;
				}
				elseif($this->report['report_royalty'] == 1 )
				{
					$CMS->video->sql_add .= "video_royalty > 0 AND " ;
				}
			}
			// Sort tinh trang bai viet
			if($this->report['report_active'] != "" )
			{	
					$CMS->video->sql_add .= "video_active = {$this->report['report_active']} AND " ;
			}
			// Start time, end time
			$CMS->video->sql_add .= $CMS->input['report_start'] ? " video_time > ".$CMS->class->date->date2time($CMS->input['report_start'],1)." AND " : "";
			$CMS->video->sql_add .= $this->input['report_end'] ? " video_time < ".($CMS->class->date->date2time($CMS->input['report_start'],1)+24*3600)." AND " : "";
			// Sort
			$CMS->video->order_extend = "";
			$CMS->video->order_field = "video_time";
			$CMS->video->order_by = "ASC";
			// Page
			$CMS->video->per_page = in_array($this->report['format'], $this->download) == true ? 999999 : $this->report['per_page'];
			$CMS->video->suffix_html = "&act={$this->mod_name}&user_id={$this->report['user_id']}&module_id={$this->report['module_id']}&sub_cate_video={$this->report['sub_cate_video']}&royalty={$this->report['report_royalty']}&active={$this->report['report_active']}&report_per_page={$this->report['report_per_page']}&report_start={$this->report['report_start']}&report_end={$this->report['report_end']}";
			// Get List
			$CMS->video->listing();
		}

		//print_r ()
		
		// Footer input
		$this->set_footer_input();
	
		
		if ( $this->report['report_format'] == "excel" AND $CMS->input['module_id'] == 3)
		{
			
			list($array,$data) = $CMS->report->convert_input_all();
			$this->report_display = 2;
			$this->build_excel_header();
			$this->build_excel_module_all($array);
			$this->build_excel_footer();
		}
		elseif ( $this->report['report_format'] == "excel" )
		{
			$this->report_display = 2;
			$this->build_excel_header();
			$this->build_excel_module();
			$this->build_excel_footer();
		}
		else
		{		
			return $data;
		}
	}


	
	//===========================================================================
	//  REPORT GRAPH
	//===========================================================================

	public function convert_input($data)
	{
		global $CMS, $DB;
		
	
		$sql_add .=  $data['user_id'] != '' ? "user_id = '{$data['user_id']}' AND " : "1=1 AND ";
	
		if($data['module_id'] == 0)
		{
			if($data['sub_cate_news'] != "")
			{
				$sql_add .= "parent_cat_id LIKE '%|{$data['sub_cate_news']}|%' AND "; 
			}
			
		}
		elseif($data['module_id'] == 1)
		{
			if($data['sub_cate_album'] != ""  )
			{
				$sql_add .= "cat_id = '{$data['sub_cate_album']}' AND "; 
			}
		}
		elseif($data['module_id'] == 2)
		{
			if($data['sub_cate_video'] != ""  )
			{
				$sql_add .= "cat_id = '{$data['sub_cate_video']}' AND "; 
			}
		}		
		if($CMS->input['module_id'] == 0){ $table = "news";$sql_add .= "news_deleted = 0 AND ";}
		if($CMS->input['module_id'] == 1){ $table = "album";$sql_add .= "album_deleted = 0 AND ";}
		if($CMS->input['module_id'] == 2){ $table = "video";$sql_add .= "video_deleted = 0  AND ";}
		return $sql_add;
		
	}
	
	public function build_excel_payment()
	{
		global $CMS, $DB;
	
		// Sheet data
		$this->sheet_data = array(
			array(15, "pay_name"),
			array(15, "pay_real_receiver_bk"),
			array("auto", "pay_location"),
			array("auto", "pay_type"),
			array("auto", "pay_type2"),
			array("auto", "pay_confirm_by"),
			array("auto", "pay_time"),
			array(15, "pay_total_vat_bk", "align_right"), // , "currency"
			array("auto", "pay_content"),
		);

		// Initialize
		while( $data = $DB->fetch_array( $CMS->transactionout->sql_query ) )
		{
			$data = $CMS->transactionout->convertvalue($data);

			// Report display
			$this->sheet_date_display = $this->build_sheet($data['pay_time_short']);

			// Set sheet
			if ( $this->sheet_date_display != $this->sheet_date )
			{				
				if ( $this->sheet_cnt != 0 )
				{
					// Write last line
					$this->build_last_line($CMS->lang['pay_total']);
					
					// Continue open new sheet		
					$CMS->class->excel->data->createSheet();	
				}
				
				// Reset sheet
				$this->set_excel_header();
			}
			
			// Set date row
			if ( $data['pay_time_short'] != $this->sheet_now )
			{
				$this->set_excel_title($data['pay_time_short']);
			}
			
			// Content
			$this->set_excel_content($data);
			
			// Write last line
			$this->total += $data['pay_total_bk'];
		}
		
		// Write last line
		$this->build_last_line($CMS->lang['pay_total']);
	}
	
	public function build_excel_password()
	{
		global $CMS, $DB;
	
		// Sheet data
		$this->sheet_data = array(
			array(25, "password_name"),
			array(15, "password_type"),
			array("auto", "user_name"),
			array("auto", "password_userg_id"),
			array("auto", "password_os"),
			array("auto", "password_server"),
			array(15, "password_ip"),
			array("auto", "password_ssh"),
			array("auto", "password_cp"),
			array("auto", "password_root"),
			array("auto", "password_sa"),
			array("auto", "password_content"),
			array("auto", "password_time"),
			array("auto", "password_time_update"),
			array(15, "password_ip_address"),
			array("15","password_port"),
			array("auto","password_own"),
		);
		
		// Initialize
		while( $data = $DB->fetch_array( $CMS->password->sql_query ) )
		{
			
			$data = $CMS->password->convertvalue($data);

			// Report display
			$this->sheet_date_display = $this->build_sheet($data['password_time_short']);

			// Set sheet
			if ( $this->sheet_date_display != $this->sheet_date )
			{				
				if ( $this->sheet_cnt != 0 )
				{
					// Continue open new sheet		
					$CMS->class->excel->data->createSheet();	
				}
					
				// Reset sheet
				$this->set_excel_header();
			}

			// Set date row
			if ( $data['password_time_short'] != $this->sheet_now )
			{
				$this->set_excel_title($data['password_time_short']);
			}
			// Content
			$this->set_excel_content($data);
		}
	}
	
	//===================================================================================
	// List filter service group
	//===================================================================================
	public function filter_list_svcgroup($svctype)
	{
		global $CMS, $DB;
		
		$output ="";
		
		$sql = $DB->query("SELECT * FROM ".root_table."service_group WHERE svctype_id = '{$svctype}' AND svcgroup_deleted = 0 ");	
		while($svcgroup = $DB->fetch_array($sql))
		{
			$output .= "<div ><ul class='check_co'>
							<li class='titile_check'><input type='checkbox' id='svcgroup_{$svcgroup['svcgroup_id']}' onclick='check_svcgroup(\"{$svcgroup['svcgroup_id']}\")' name='svcgroup_{$svcgroup['svcgroup_id']}' value='{$svcgroup['svcgroup_id']}'>{$svcgroup['svcgroup_name']}</li>";
							
			$sql_2 = $DB->query("SELECT * FROM ".root_table."service WHERE svcgroup_id = '{$svcgroup['svcgroup_id']}' AND service_deleted = 0 ");
			while($service = $DB->fetch_array($sql_2))
			{
				if(substr(strtolower($service['service_code']),0,2)=="dk" || substr(strtoupper($service['service_code']),0,3)=="ĐK")
				{ 
					$key = "dk"; 
				} 
				else if(substr(strtolower($service['service_code']),0,2)=="dt")
				{ 
					$key = "dt"; 
				}
				else if(substr(strtolower($service['service_code']),0,2)=="tf")
				{ 
					$key = "tf"; 
				}
				else
				{ 
					$key = ""; 
				}
				
				$checked = $_SESSION['service_list'] ? (in_array($service['service_id'],$_SESSION['service_list']) ? "checked='checked'" : "") : "";
				$service_name = $service['service_display_name'] ? $service['service_display_name'] : $service['service_name'];
				$output .= "<li >
								<input type='checkbox' key='{$key}' {$checked} id='sv_{$svcgroup['svcgroup_id']}_{$service['service_id']}' name='sv_{$svcgroup['svcgroup_id']}_{$service['service_id']}' value='{$service['service_id']}' >{$service_name}
							</li>";
				
			}
			$output .= "</ul></div>";
		}
		return $output;
	}
	//===========================================================================
	// transactionout_all
	//===========================================================================
	
	public function transactionout_all()
	{
		global $CMS, $DB;
		
		$transactionout = 0;
		
		$_SESSION["msg"] .= "";
		
		$pay_total = 0;
		
		$list_ord = "";
		
		$pay_content = "Chi huê hồng: ";
		
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
				$data = $CMS->order->get_info($id);
				
				$data['ord_member_revenue_bk'] = $data['ord_member_revenue'];
				
				$data['ord_member_revenue'] = $CMS->class->input->currency($data['ord_member_revenue']);
				
				if(($data['ord_payment_status'] == 1) && ($data['ord_member_status'] == 0))
				{
					$pay_content = $pay_content.$data['ord_name'].": ".$data['ord_member_revenue']."; ";
					
					$pay_total += $data['ord_member_revenue_bk'];
					
					$list_ord = $list_ord.$data['ord_name'].",";
				
					$DB->query("UPDATE ".root_table."order SET  ord_member_status = 1 WHERE ord_id = '{$data['ord_id']}'");	
					
					$transactionout = 1;
					
					$member_id = $data['member_id'];
				}
			}
		}
		
		if ( $transactionout == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['ord_transactionout_failed']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=report&act=report_member");
		}
		else
		{
			
			$ctv = $CMS->customer->getInfo($member_id);
			
			$CMS->input['cus_id'] = 0;
			$CMS->input['member_id'] = $ctv['cus_id'];
			$CMS->input['pay_receiver_address'] = $ctv['cus_address'];
			$CMS->input['pay_type'] = 1111;
			$CMS->input['pay_type2'] = 1111;
			$CMS->input['pay_is_vat'] = 0;
			$CMS->input['pay_total'] = $pay_total;
			$CMS->input['pay_receiver_type'] = 4;
			$CMS->input['pay_list_ord'] = $list_ord;
			$CMS->input['pay_content'] = $pay_content;
			
			if($data = $CMS->transactionout->add())
			{
				
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactionout&act=show&id={$data['pay_id']}");
				
			}
			else
			{
				$_SESSION["msg"] .= "{$CMS->lang['ord_transactionout_failed']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=report&act=report_member");
			}
		}
	}
	
	//==============================================================
	// Get list service group
	//==============================================================
	public function get_group_list()
	{
		global $CMS, $DB;
		
		$output = "";
		
		$svctype = $CMS->input['svctype'];	
		$sql = $DB->query("SELECT * FROM ".root_table."service_group WHERE svctype_id='{$svctype}' AND svcgroup_deleted = 0");
		
		while($data = $DB->fetch_array())
		{
			$output .= $data['svcgroup_id']."|";
		}
		print $output;exit;	
	}
	
	//===========================================================================
	//  Report total
	//===========================================================================
	
	public function total($is_sub = 0)
	{
		global $CMS, $DB;
		
		$CMS->input['report_total_method'] == 1 ? $this->comnbina_order_listing($is_sub) : $this->combina_transaction($is_sub);
		
		/*$CMS->order->sql_add = "";
		$CMS->order->sql_table = "";
		
		$_SESSION['input'] = $is_sub == 0 ? $CMS->input : $_SESSION['input'];
		if($CMS->input['date'])
		{
			$_SESSION['input']['date'] = $CMS->input['date'];	
		}
		
		// Header input
		$this->set_header_input($is_sub);
		
		// Load language
		$CMS->class->language->load("transaction");

		// Define Condition for Get List
		$base_sql_table = $CMS->order->sql_table .= " AS O LEFT JOIN ".root_table."service AS S ON S.service_id=O.service_id LEFT JOIN ".root_table."service_group AS G ON G.svcgroup_id=S.svcgroup_id "; 

		$sql_select = ""; // Addition select
	
		// Select transaction, 22-05-2012, Hloi
		$CMS->order->sql_table .= " LEFT JOIN nh_invoice AS I ON I.inv_id=O.inv_id LEFT JOIN nh_transaction AS T ON T.inv_id=I.inv_id ";
		$CMS->order->sql_select .= ", GROUP_CONCAT(T.transaction_name) as ord_transactionlist ";
		$CMS->order->sql_group = " GROUP BY ord_id ";
		
		//********************** Start time, end time following total method 
		if($is_sub == 1)
		{
			if($CMS->input['date'])
			{
				$now = explode("/",$CMS->class->date->today);
				$this->report['start'] = !$this->report['total_year'] ? "01/{$CMS->input['date']}/{$now[2]}" : "01/{$CMS->input['date']}/{$this->report['total_year']}";
				
				$end = $CMS->class->date->get_dayofmonth($CMS->input['date'],$this->report['total_year']);
				$this->report['end'] = !$this->report['total_year'] ? "{$end}/{$CMS->input['date']}/{$now[2]}" : "{$end}/{$CMS->input['date']}/{$this->report['total_year']}";
			}
			else
			{
				$temp = explode("/",$CMS->class->date->today);
				$this->report['start'] = !$this->report['total_year'] ? "01/{$temp[1]}/{$temp[2]}" : "01/{$temp[1]}/{$this->report['total_year']}";
				
				$day = $CMS->class->date->get_dayofmonth($temp[1],$this->report['total_year']);
				$this->report['end'] = !$this->report['total_year'] ? "{$day}/{$temp[1]}/{$temp[2]}" : "{$day}/{$temp[1]}/{$this->report['total_year']}"; 
			}
		}
		
		if($this->report['total_method']==1)
		{
			$CMS->order->sql_add .= $this->report['start'] ? " O.ord_time > ".$CMS->class->date->date2time($this->report['start'],1)." AND " : "";
			$CMS->order->sql_add .= $this->report['end'] ? " O.ord_time < ".($CMS->class->date->date2time($this->report['end'],1)+24*3600)." AND " : "";
			
			// Group as date
			$this->report['type_group'] = "ord_time";
		}
		else
		{
			$CMS->order->sql_add .= $this->report['start'] ? " IF(T.transaction_confirm_time > 0,T.transaction_confirm_time,T.transaction_time) > ".$CMS->class->date->date2time($this->report['start'],1)." AND " : "";
			$CMS->order->sql_add .= $this->report['end'] ? " IF(T.transaction_confirm_time > 0,T.transaction_confirm_time,T.transaction_time) < ".($CMS->class->date->date2time($this->report['end'],1)+24*3600)." AND " : "";
			
			// Get condition for transaction
			$CMS->order->sql_add .= " T.transaction_type!='3' AND T.transaction_status=1 AND ";
			
			// Group as date
			$this->report['type_group'] = "transaction_confirm_time";
		}
		// ** End total method
		
		//*************************** Service type
		
		$this->report['list_group'] = "";
		
		if(($this->report['svctype_1'] && $this->report['svctype_2'] && $this->report['svctype_3'] && $this->report['svctype_4'] && $this->report['svctype_5'] && $this->report['svctype_6']) || $this->is_default == 1)
		{
			$this->report['list_group'] = $CMS->lang['svctype_1'].", ".$CMS->lang['svctype_2'].", ".$CMS->lang['svctype_3'].", ".$CMS->lang['svctype_4'].", ".$CMS->lang['svctype_5'].", ".$CMS->lang['svctype_6'];
		}
		else
		{
			$CMS->order->sql_add .= "(";
			for($i=1;$i<=6;$i++)
			{
				if($this->report["svctype_{$i}"] > 0) 
				{
					$j = $i+1;
					$CMS->order->sql_add .= " G.svctype_id = '".$this->report["svctype_{$i}"]."'";
					
					$svctype = $this->report["svctype_{$i}"];
					
					$this->report['list_group'] .= $CMS->lang["svctype_{$svctype}"];
					$this->report['list_group'] .= $this->report["svctype_{$j}"] ? "," : "";
					
					$CMS->order->sql_add .= $this->report["svctype_{$j}"] ? " OR " : ($this->report['service_list'][0] ? " OR " : " AND " );
				}
			}
		}
		
		// Get service list
		if(!$this->report['svctype_1'] || !$this->report['svctype_2'] || !$this->report['svctype_3'] || !$this->report['svctype_4'] || !$this->report['svctype_5'] || !$this->report['svctype_6'])
		{
			if($this->report['service_list'][0])
			{
				$CMS->order->sql_add .= " S.service_id NOT IN (";
				for($i=0;$i<count($this->report['service_list']);$i++)
				{
					$CMS->order->sql_add .= $this->report['service_list'][$i];
					$CMS->order->sql_add .= $this->report['service_list'][$i+1] ? "," : "";
				}
				$CMS->order->sql_add .= ") AND ";
			}
			
			$CMS->order->sql_add .= $this->is_default == 0 ? " 1=1 ) AND " : "";
		}
		//****** End service
		
		// ************ Total cycle
		if((!$this->report['report_cycle_1'] || !$this->report['report_cycle_2'] || !$this->report['report_cycle_3'] || !$this->report['report_cycle_4'] || !$this->report['report_cycle_5'] || !$this->report['report_cycle_6']) && $this->is_default == 0)
		{
			$CMS->order->sql_add .= "( ";
			if($this->report['report_cycle_1'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 3 ) ";
				if($this->report['report_cycle_2'] || $this->report['report_cycle_3'] || $this->report['report_cycle_4'] || $this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_2'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 3 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 6 ) ";
				if($this->report['report_cycle_3'] || $this->report['report_cycle_4'] || $this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_3'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 6 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 12 ) ";
				if($this->report['report_cycle_4'] || $this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_4'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 12 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 24 ) ";
				if($this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_5'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 24 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 36 ) ";
				if($this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_6'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 36 ) ";
			}
			$CMS->order->sql_add .= " ) AND ";
		}
		//**End
		
		//******* Total Payment Status
		if((!$this->report['total_payment_status_1'] || !$this->report['total_payment_status_2'] || !$this->report['total_payment_status_3']) && $this->is_default == 0)
		{
			$CMS->order->sql_add .= "(";
			if ( $this->report['total_payment_status_1'])
			{
				$CMS->order->sql_add .= " (O.ord_payment_status=1) "; 
				if($this->report['total_payment_status_2'] || $this->report['total_payment_status_3'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if ( $this->report['total_payment_status_2'])
			{
				$CMS->order->sql_add .= " (O.ord_payment_status=4) ";
				if($this->report['total_payment_status_3'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if ( $this->report['total_payment_status_3']) // 3 is Payment Yet
			{
				$CMS->order->sql_add .= " (O.ord_payment_status=0) "; 
			}
			$CMS->order->sql_add .= " ) AND ";
		}
		//** End 
		
		//************ Total payment method
		if(!$this->report['total_payment_method_1'] || !$this->report['total_payment_method_2'] || !$this->report['total_payment_method_3'])
		{
			$CMS->order->sql_add .= "(";
			if ( $this->report['total_payment_method_1']) 
			{ 
				$CMS->order->sql_add .= " O.ord_payment_method=0 "; 
				$CMS->order->sql_add .= $this->report['total_payment_method_2'] || $this->report['total_payment_method_3'] ? " OR " : " AND ";
			}
			if ( $this->report['total_payment_method_2']) 
			{ 
				$CMS->order->sql_add .= " O.ord_payment_method=1 "; 
				$CMS->order->sql_add .= $this->report['total_payment_method_3'] ? " OR " : " AND ";
			}
			if ( $this->report['total_payment_method_3']) 
			{ 
				$CMS->order->sql_add .= " O.ord_payment_method=3 AND "; 
			}
			$CMS->order->sql_add .= "1=1) AND";

		}
		// ** End
		
		//*********** Get report type
		
		$this->report['list_type'] = "";
		
		if($this->is_default == 1)
		{
			$this->report['list_type'] = $CMS->lang['report_type_1'].",".$CMS->lang['report_type_2'].",".$CMS->lang['report_type_3'];	
		}
		else
		{
			if($this->report['type_1'])
			{
				$type = $this->report['type_1'];
				$this->report['list_type'] .= $CMS->lang["report_type_{$type}"];
				
				$this->report['list_type'] .= ($this->report['type_2'] || $this->report['type_3']) ? ", " : "."; 
				
			}
			if($this->report['type_2'])
			{
				$type = $this->report['type_2'];
				$this->report['list_type'] .= $CMS->lang["report_type_{$type}"];
				
				$this->report['list_type'] .= ($this->report['type_3']) ? ", " : "."; 
				
			}
			if($this->report['type_3'])
			{
				$type = $this->report['type_3'];
				$this->report['list_type'] .= $CMS->lang["report_type_{$type}"].".";
			}
		}
		
		//****** End type
		
		// Sort
		$CMS->order->order_extend = "";
		$CMS->order->order_field = "ord_time";
		$CMS->order->order_by = "ASC";
		
		// Footer input
		$this->set_footer_input();
	
		// Page
		$CMS->order->per_page = (in_array($this->report['format'], $this->download) == true && !$CMS->input['date']) ? 999999 : $this->report['per_page'];

		$sql_add_more = "";
		
		$sql_add_more .= $this->report['payment_status'] ? "&report_payment_status={$this->report['payment_status']}" : "";

		// Get List
		$CMS->order->listing($sql_add_more);*/

		// Statistics
		$data['report_name'][0] = $CMS->lang['report_chart_order'] ." ". $service_name ;
		$data['report_name'][1] = $this->report['start'] ."   &rarr;   ". $this->report['end'];
		
		// Get service group
		$data['report_name'][2] = ($this->report['total_method'] == 1 ? $CMS->lang['report_customer_1'] : $CMS->lang['report_customer_2']).": ".$this->report['list_group'];
		$data['report_name'][3] = $CMS->lang['report_type'].": ".$this->report['list_type'];
		
		$data['report_display'] = $this->report['display'];
		$data['report_sql'] = "SELECT COUNT(ord_id) AS number,SUM(IF(O.ord_is_vat > 0, O.ord_total, O.ord_amount)) as count, DATE_FORMAT(FROM_UNIXTIME({$this->report['type_group']}), '{$this->report_date[4]}') AS date {$sql_select} FROM ".root_table."order {$CMS->order->sql_table} WHERE {$CMS->order->sql_add} 1=1 GROUP BY date";
		
		$data['report_start'] = $this->report['start'];
		$data['report_end'] = $this->report['end'];
		
		// Staticstics for sub month - 6.12.2012 hvu
		if($is_sub == 1)
		{
			$data['report_name'][0] = $CMS->lang['report_chart_order'] ." ". $service_name ;
			$data['report_name'][1] = $this->report['start'] ."   &rarr;   ". $this->report['end'];
			$data['report_display'] = 1;
			$data['report_sql'] = "SELECT COUNT(ord_id) AS number,SUM(IF(O.ord_is_vat > 0, O.ord_total, O.ord_amount)) as count, DATE_FORMAT(FROM_UNIXTIME({$this->report['type_group']}), '{$this->report_date[5]}') AS date {$sql_select} FROM ".root_table."order {$CMS->order->sql_table} WHERE {$CMS->order->sql_add} 1=1 GROUP BY date";
			
			$_SESSION['report_sql_bk'] = $data['report_sql'];
			
			$data['report_start'] = $this->report['start'];
			$data['report_end'] = $this->report['end'];
		}
 
		if ( in_array($this->report['format'], $this->download) == true )
		{
			$this->report_display = 2;
			$this->build_excel_header();
			$this->build_excel_order();
			$this->build_excel_footer();
		}
		else
		{		
			return $data;
		}
	}
	
	//=====================================================================
	// Check exist group in svctype
	//=====================================================================
	public function checkexist()
	{
		global $CMS, $DB;
		
		$svctype = $CMS->input['svctype'];
		$group = $CMS->input['group'];
		
		$is_in_array = 0;
		
		for($i=0;$i<count($svctype);$i++)
		{
			$sql = $DB->query("SELECT * FROM ".root_table."service_group WHERE svcgroup_id = '{$group}' AND svctype_id = '{$svctype[$i]}'");
			if($DB->num_rows() > 0)
			{
				$is_in_array = 1;
				break;
			}
		}
		
		if($is_in_array == 0)
		{
			print 0;exit;	
		}
		else
		{
			print 1;exit;	
		}
	}
	
	public function get_total_year($curr_year=0)
	{
		global $CMS, $DB;
		
		$output = "";
		
		$now = $CMS->class->date->year;
		
		for($i=0;$i<3;$i++)
		{
			$data = $now - $i;
			$curr_year = $curr_year > 0 ? $curr_year : $CMS->class->date->year;
			$output .= "<input type='radio' name='report_total_year' value='{$data}' defaultvalue='{$curr_year}' onclick=\"update_total_year(this.value)\"/>{$data}";	
		}
		
		return $output;
	}
	
	public function load_user_list( $group=0, $location="")
	{
		global $CMS, $DB, $member;
		
		$output = "";
		$add_sql = "";
		$add_sql2 = "";
		
		$output .= "<option value=''>------------------</option>";
		
		// Select only sale group
		if($group)
		{
			$add_sql .= " userg_id = {$group} AND ";	
		}
		
		if($location && $location!='all')
		{
			$add_sql2 .= " user_location='{$location}' AND ";
		}
		
		$sql = $DB->query("SELECT * FROM ".root_table."user_group WHERE {$add_sql} userg_deleted=0 ORDER BY userg_is_root ASC, userg_is_admin ASC, userg_title ASC");

		while ( $group = $DB->fetch_array( $sql ) )
		{
			$output .= "<optgroup label='{$group['userg_title']}'>";	

			$sql2 = $DB->query("SELECT * FROM ".root_table."user WHERE {$add_sql2} userg_id='{$group['userg_id']}' AND user_deleted=0 AND user_status=1 ORDER BY user_name ASC");
			
			while ( $user = $DB->fetch_array( $sql2 ) )
			{
				$output .= "<option value='{$user['user_id']}' style='font-weight: ".($user['user_is_leader'] == true ? "normal" : "normal").";'>----- ".($user['user_is_leader'] == true ? "" : "")." {$user['user_display_name']} ". ($user['user_last_visit'] >= $CMS->core->time_out ? "[Online]" : "") ."</option>";
			}
		}
		
		return $output;
	}
	
	public function combina_transaction($is_sub = 0)
	{
		global $CMS, $DB;
		
		// Load language
		$CMS->class->language->load("transaction");

		$this->report['type_group'] = $this->mod_name == "report_all" ? "O.ord_time" : "T.transaction_confirm_time";
		
		$CMS->order->sql_add = "";
		$CMS->order->sql_table = "";
		
		$_SESSION['input'] = $is_sub == 0 ? $CMS->input : $_SESSION['input'];
		if($CMS->input['date'])
		{
			$_SESSION['input']['date'] = $CMS->input['date'];	
		}
		
		// Header input
		$this->set_header_input($is_sub);
		
		// Remove payment method = payment to reseller, except payment online
		$CMS->order->sql_add .= " (T.transaction_type!=3) AND ";
		
		//********************** Start time, end time following total method 
		if($is_sub == 1)
		{
			if($CMS->input['date'])
			{
				$now = explode("/",$CMS->class->date->today);
				$this->report['start'] = !$this->report['total_year'] ? "01/{$CMS->input['date']}/{$now[2]}" : "01/{$CMS->input['date']}/{$this->report['total_year']}";
				
				$end = $CMS->class->date->get_dayofmonth($CMS->input['date'],$this->report['total_year']);
				$this->report['end'] = !$this->report['total_year'] ? "{$end}/{$CMS->input['date']}/{$now[2]}" : "{$end}/{$CMS->input['date']}/{$this->report['total_year']}";
			}
			else
			{
				$temp = explode("/",$CMS->class->date->today);
				$this->report['start'] = !$this->report['total_year'] ? "01/{$temp[1]}/{$temp[2]}" : "01/{$temp[1]}/{$this->report['total_year']}";
				
				$day = $CMS->class->date->get_dayofmonth($temp[1],$this->report['total_year']);
				$this->report['end'] = !$this->report['total_year'] ? "{$day}/{$temp[1]}/{$temp[2]}" : "{$day}/{$temp[1]}/{$this->report['total_year']}"; 
			}
		}

		//========================================================================================= Payment method
		if ( ! empty($this->report['payment_method']) ) 
		{
			$CMS->order->sql_add .= " T.transaction_payment_method='{$this->report['payment_method']}' AND "; 
		}
		else if($this->mod_name == "report_all")
		{
			//************ Total payment method
			if(!$this->report['total_payment_method_1'] || !$this->report['total_payment_method_2'] || !$this->report['total_payment_method_3'])
			{
				$CMS->order->sql_add .= "(";
				if ( $this->report['total_payment_method_1']) 
				{ 
					$CMS->order->sql_add .= " O.ord_payment_method=0 "; 
					$CMS->order->sql_add .= $this->report['total_payment_method_2'] || $this->report['total_payment_method_3'] ? " OR " : " AND ";
				}
				if ( $this->report['total_payment_method_2']) 
				{ 
					$CMS->order->sql_add .= " O.ord_payment_method=1 "; 
					$CMS->order->sql_add .= $this->report['total_payment_method_3'] ? " OR " : " AND ";
				}
				if ( $this->report['total_payment_method_3']) 
				{ 
					$CMS->order->sql_add .= " O.ord_payment_method=3 AND "; 
				}
				$CMS->order->sql_add .= "1=1) AND";
	
			}
			// ** End
		}
		//========================================================================================= End Payment method
		
		//========================================================================================= Service 
		if($this->mod_name == "report_all")
		{
			$this->report['list_group'] = "";
			
			if(($this->report['svctype_1'] && $this->report['svctype_2'] && $this->report['svctype_3'] && $this->report['svctype_4'] && $this->report['svctype_5'] && $this->report['svctype_6']) || $this->is_default == 1)
			{
				$this->report['list_group'] = $CMS->lang['svctype_1'].", ".$CMS->lang['svctype_2'].", ".$CMS->lang['svctype_3'].", ".$CMS->lang['svctype_4'].", ".$CMS->lang['svctype_5'].", ".$CMS->lang['svctype_6'];
			}
			else
			{
				$CMS->order->sql_add .= "(";
				for($i=1;$i<=6;$i++)
				{
					if($this->report["svctype_{$i}"] > 0) 
					{
						$j = $i+1;
						$CMS->order->sql_add .= " G.svctype_id = '".$this->report["svctype_{$i}"]."'";
						
						$svctype = $this->report["svctype_{$i}"];
						
						$this->report['list_group'] .= $CMS->lang["svctype_{$svctype}"];
						$this->report['list_group'] .= $this->report["svctype_{$j}"] ? "," : "";
						
						$CMS->order->sql_add .= $this->report["svctype_{$j}"] ? " OR " : ($this->report['service_list'][0] ? " OR " : " AND " );
					}
				}
			}
			
			// Get service list
			if(!$this->report['svctype_1'] || !$this->report['svctype_2'] || !$this->report['svctype_3'] || !$this->report['svctype_4'] || !$this->report['svctype_5'] || !$this->report['svctype_6'])
			{
				if($this->report['service_list'][0])
				{
					$CMS->order->sql_add .= " S.service_id NOT IN (";
					for($i=0;$i<count($this->report['service_list']);$i++)
					{
						$CMS->order->sql_add .= $this->report['service_list'][$i];
						$CMS->order->sql_add .= $this->report['service_list'][$i+1] ? "," : "";	
					}
					$CMS->order->sql_add .= ") AND ";
				}
				
				$CMS->order->sql_add .= $this->is_default == 0 ? " 1=1 ) AND " : "";
			}
		}
		//========================================================================================= End Service

		//========================================================================================= Type
		if ( ! empty($this->report['transaction_type']) ) { $CMS->order->sql_add .= " T.transaction_type='{$this->report['transaction_type']}' AND "; }
		else if($this->mod_name == "report_all")
		{
			if ( $this->report['type_1'])
			{
				$CMS->order->sql_add .= $CMS->mod_report->sql_service_register;
			}
			else if ( $this->report['type_2'])
			{
				$CMS->order->sql_add .= $CMS->mod_report->sql_service_renew;
			}
			else if ( $this->report['type_3'])
			{
				$CMS->order->sql_add .= $CMS->mod_report->sql_service_transfer;
			}
		}

		//========================================================================================= End Type
		
		// Is debt
		if ( ! empty($this->report['transaction_debt']) ) { $CMS->order->sql_add .= " T.transaction_is_debt='{$this->report['transaction_debt']}' AND T.transaction_status='0' AND "; }
		else 
		{
			// Status
			$CMS->order->sql_add .= " T.transaction_status=1 AND ";
		}

		// Start time, end time
		$CMS->order->sql_add .= $this->report['start'] ? " IF(T.transaction_confirm_time > 0,T.transaction_confirm_time,T.transaction_time) > ".$CMS->class->date->date2time($this->report['start'],1)." AND " : "";
		$CMS->order->sql_add .= $this->report['end'] ? " IF(T.transaction_confirm_time > 0,T.transaction_confirm_time,T.transaction_time) < ".($CMS->class->date->date2time($this->report['end'],1)+24*3600)." AND " : "";
		
		// Accountnat
		$CMS->order->sql_add .= $this->report['acc1'] ? " T.transaction_acc='{$this->report['acc1']}' AND " : "";
		$CMS->order->sql_add .= $this->report['acc2'] ? " T.transaction_acc2='{$this->report['acc2']}' AND " : "";
		
		//============================================= Add for total path
		//**************************** User
		if ( preg_match("/(g)/", $this->report['employee']) == true )
		{
			$this->report['employee'] = str_replace("g", "", $this->report['employee']);
			
			$CMS->order->sql_add .= " ( ";
			
			$sql = $DB->query("SELECT * FROM ".root_table."user WHERE userg_id='{$this->report['employee']}'");	

			$i = 0;
			while ( $userlist = $DB->fetch_array( $sql ) )
			{
				$i++;
				$CMS->order->sql_add .=  " T.transaction_confirm_by='{$userlist['user_id']}' ";
				$CMS->order->sql_add .= $i==$DB->num_rows($sql) ? "" : " OR ";
			}
			
			$CMS->order->sql_add .= " ) AND ";
		}
		else if ( $this->report['employee'] )
		{
			$CMS->order->sql_add .= " T.transaction_confirm_by='{$this->report['employee']}' AND ";
		}
		// **** End user
		// End path
		
		//*********** Get report type
		
		if($this->mod_name == "report_all")
		{
			$this->report['list_type'] = "";
			
			if($this->is_default == 1)
			{
				$this->report['list_type'] = $CMS->lang['report_type_1'].",".$CMS->lang['report_type_2'].",".$CMS->lang['report_type_3'];	
			}
			else
			{
				if($this->report['type_1'])
				{
					$type = $this->report['type_1'];
					$this->report['list_type'] .= $CMS->lang["report_type_{$type}"];
					
					$this->report['list_type'] .= ($this->report['type_2'] || $this->report['type_3']) ? ", " : "."; 
					
				}
				if($this->report['type_2'])
				{
					$type = $this->report['type_2'];
					$this->report['list_type'] .= $CMS->lang["report_type_{$type}"];
					
					$this->report['list_type'] .= ($this->report['type_3']) ? ", " : "."; 
					
				}
				if($this->report['type_3'])
				{
					$type = $this->report['type_3'];
					$this->report['list_type'] .= $CMS->lang["report_type_{$type}"].".";
				}
			}
		}
		
		// Sort
		$CMS->order->order_extend = "";
		$CMS->order->order_field = "T.transaction_confirm_time";
		$CMS->order->order_by = "ASC";
		
		// Footer input
		$this->set_footer_input();
	
		// Page
		$CMS->order->per_page = in_array($this->report['format'], $this->download) == true ? 999999 : $this->report['per_page'];
			
		// Select group_concat, 16-05-2012, HLoi
		$CMS->order->sql_select = "O.contract_id, COUNT(O.ord_id) as transaction_ordercnt, GROUP_CONCAT(O.ord_name,if(O.ord_domain!='',concat(' - ',O.ord_domain),''),' - ',S.service_display_name,'|') as transaction_orderlist, T.*";
		$CMS->order->sql_table = "AS O LEFT JOIN nh_invoice AS I ON I.inv_id=O.inv_id LEFT JOIN nh_transaction AS T ON I.inv_id=T.inv_id LEFT JOIN nh_service AS S ON S.service_id=O.service_id left join ".root_table."service_group as G on G.svcgroup_id=S.svcgroup_id LEFT JOIN ".root_table."bank as B on B.bank_id=T.bank_id ";
		$CMS->order->sql_group = "GROUP BY T.transaction_id";
		
		$CMS->transaction->is_report = 2;
		
		if ( in_array($this->report['format'], $this->download) == true )
		{
			$CMS->transaction->is_report = 2;
		}
		
		// Get List
		$CMS->order->listing();
	}
	
	public function get_ajax_chart()
	{
		global $CMS, $DB;
		
		$this->loadhtml();
		
		$is_show_curyear = $CMS->input['show_chart_1'] ? $CMS->input['show_chart_1'] : "";	
		$is_show_preyear = $CMS->input['show_chart_2'] ? $CMS->input['show_chart_2'] : "";
		$is_show_preyear2 = $CMS->input['show_chart_3'] ? $CMS->input['show_chart_3'] : "";
		
		$show_detail_chart = $CMS->input['show_detail_chart'] ? $CMS->input['show_detail_chart'] : "";
		
		$data = $this->total();	// Get data follow current year
		
		$sub_data = $show_detail_chart ? $this->total(1) : "";  // Get data follow date of current year
		
		// Get info to replace for Pre data
		$start = $CMS->class->date->date2time($data['report_start'],1);
		$end = $CMS->class->date->date2time($data['report_end'],1) + 24*3600;
		
		// Date for Pre SQL
		$temp_start = explode("/",$data['report_start']);
		$temp_end = explode("/",$data['report_end']);
		
		// Create Pre SQL
		if($is_show_preyear)
		{
			$pre_start = $CMS->class->date->date2time(str_replace($temp_start[2],$temp_start[2]-1,$data['report_start']));
			$pre_end = $CMS->class->date->date2time("31/{$temp_end[1]}/".($temp_end[2]-1)) + 24*3600;
		
			$pre_sql = str_replace($start,$pre_start,$data['report_sql']);
			$pre_sql = str_replace($end,$pre_end,$pre_sql);
		}
		// End Pre SQL
		
		// Create Pre 2 SQL
		if($is_show_preyear2)
		{
			$pre_start_2 = $CMS->class->date->date2time(str_replace($temp_start[2],$temp_start[2]-2,$data['report_start']));
			$pre_end_2 = $CMS->class->date->date2time("31/{$temp_end[1]}/".($temp_end[2]-2)) + 24*3600;
			
			$pre_sql_2 = str_replace($start,$pre_start_2,$data['report_sql']);
			$pre_sql_2 = str_replace($end,$pre_end_2,$pre_sql_2); 
		}
		// End Pre 2 SQL
		
		$chart .= $this->html->order_multi_chart($data['report_name'], $data['report_sql'], $pre_sql, $pre_sql_2,$data['report_end'],1,1,$is_show_curyear,$is_show_preyear,$is_show_preyear2);
		$chart = str_replace("\\n","\n",$chart);
		$chart .= "|||".($sub_data ? $this->html->sub_report($sub_data) : "");
		
		return $chart;
		
	}
	
	public function combina_order($current_year = "",$show_chart_1 = 1, $show_chart_2 = 1, $show_chart_3 = 1, $is_show_detail = 1)
	{
		global $CMS, $DB; 
		
		include_once(root_path."/tools/FusionCharts/Includes/FusionCharts.php");
		
		// Load language
		$CMS->class->language->load("transaction");
		$CMS->class->language->load("report");
		
		// Group as date
		$this->report['type_group'] = "ord_time";
		
		// Get default
		$this->is_default = 1;
		$this->mod_name="report_all";
		$report_width = 700;
		
		// Set value
		$CMS->input['report_total_year'] = $current_year ? $current_year : $CMS->class->date->year;
		$CMS->input['show_chart_1'] = $show_chart_1;
		$CMS->input['show_chart_2'] = $show_chart_2;
		$CMS->input['show_chart_3'] = $show_chart_3;
		$CMS->input['show_detail_chart'] = $is_show_detail;
		$CMS->input['report_total_method'] = 1;
		
		// Get data
		$data = $this->get_ajax_chart();
		
		$data = str_replace("\n","\\n",$data);
		$data = explode("|||",$data);
		
		// Get java
		$output = "<script src='{$CMS->vars['root_domain']}/tools/FusionCharts/JSClass/FusionCharts.js' type='text/javascript'></script>";
		$output .= "<script type='text/javascript' src='{$CMS->vars['root_domain']}/javascript/acp_report.js'></script>";
		
		$output .= renderChart("{$CMS->vars['root_domain']}/tools/FusionCharts/Charts/MSColumnLine3D.swf", "", $data[0], "report_result", $report_width, 300, false, true);
		$output .= $is_show_detail == 1 ? renderChart("{$CMS->vars['root_domain']}/tools/FusionCharts/Charts/Column3D.swf", "", $data[1], "report_sub", $report_width, 300, false, true) : "";
			
		return $output;
	}
	
	public function comnbina_order_listing($is_sub = 0)
	{
		global $CMS, $DB;
		
		$this->report['type_group'] = "ord_time";
		
		$CMS->order->sql_add = "";
		$CMS->order->sql_table = "";		
		//========================================================================================= Define Condition for Get List
		
		$base_sql_table = $CMS->order->sql_table .= " AS O LEFT JOIN ".root_table."service AS S ON S.service_id=O.service_id LEFT JOIN ".root_table."service_group AS G ON G.svcgroup_id=S.svcgroup_id "; 

		$sql_select = ""; // Addition select
		
		$_SESSION['input'] = $is_sub == 0 ? $CMS->input : $_SESSION['input'];
		if($CMS->input['date'])
		{
			$_SESSION['input']['date'] = $CMS->input['date'];	
		}
		
		// Header input
		$this->set_header_input($is_sub);
		
		// Load language
		$CMS->class->language->load("transaction");
	
		// Select transaction, 22-05-2012, Hloi
		$CMS->order->sql_table .= " LEFT JOIN nh_invoice AS I ON I.inv_id=O.inv_id LEFT JOIN nh_transaction AS T ON T.inv_id=I.inv_id ";
		$CMS->order->sql_select .= ", GROUP_CONCAT(T.transaction_name) as ord_transactionlist ";
		$CMS->order->sql_group = " GROUP BY ord_id ";
		
		//========================================================================================= End Condition
		
		//********************** Start time, end time following total method 
		if($is_sub == 1)
		{
			if($CMS->input['date'])
			{
				$now = explode("/",$CMS->class->date->today);
				$this->report['start'] = !$this->report['total_year'] ? "01/{$CMS->input['date']}/{$now[2]}" : "01/{$CMS->input['date']}/{$this->report['total_year']}";
				
				$end = $CMS->class->date->get_dayofmonth($CMS->input['date'],$this->report['total_year']);
				$this->report['end'] = !$this->report['total_year'] ? "{$end}/{$CMS->input['date']}/{$now[2]}" : "{$end}/{$CMS->input['date']}/{$this->report['total_year']}";
			}
			else
			{
				$temp = explode("/",$CMS->class->date->today);
				$this->report['start'] = !$this->report['total_year'] ? "01/{$temp[1]}/{$temp[2]}" : "01/{$temp[1]}/{$this->report['total_year']}";
				
				$day = $CMS->class->date->get_dayofmonth($temp[1],$this->report['total_year']);
				$this->report['end'] = !$this->report['total_year'] ? "{$day}/{$temp[1]}/{$temp[2]}" : "{$day}/{$temp[1]}/{$this->report['total_year']}"; 
			}
		}
		
		//========================================================================================= Service
		
		if($this->report['service_group'])
		{
			if ( $this->report['service_type'] == 1 )
			{
				// Check for hosting
				if ( $this->report['service_group'] == 2 )
				{
					$this->report['service_groupname'] = "hosting";
					$sql_select .= " , SUM(S.service_domains) AS domain_cnt ";
				}
				// End check
				
				$CMS->order->sql_add .= " G.svctype_id='{$this->report['service_group']}' AND ";
				$service_name = $CMS->config_servicetype->get_info($this->report['service_group'],"svctype_name");
			}
			else
			{
				$CMS->order->sql_add .= $this->report['service_group'] ? " G.svcgroup_id='{$this->report['service_group']}' AND " : "";
				$service_name = ($this->report['service_group'] ? " ". $CMS->config_servicegroup->get_info($this->report['service_group'],"svcgroup_name") : "");
			}
		}
		
		
		//*************************** Service type : Report Total
		else if($this->mod_name == "report_all")
		{
			$this->report['list_group'] = "";
			
			if(($this->report['svctype_1'] && $this->report['svctype_2'] && $this->report['svctype_3'] && $this->report['svctype_4'] && $this->report['svctype_5'] && $this->report['svctype_6']) || $this->is_default == 1)
			{
				$this->report['list_group'] = $CMS->lang['svctype_1'].", ".$CMS->lang['svctype_2'].", ".$CMS->lang['svctype_3'].", ".$CMS->lang['svctype_4'].", ".$CMS->lang['svctype_5'].", ".$CMS->lang['svctype_6'];
			}
			else
			{
				$CMS->order->sql_add .= "(";
				for($i=1;$i<=6;$i++)
				{
					if($this->report["svctype_{$i}"] > 0) 
					{
						$j = $i+1;
						$CMS->order->sql_add .= " G.svctype_id = '".$this->report["svctype_{$i}"]."'";
						
						$svctype = $this->report["svctype_{$i}"];
						
						$this->report['list_group'] .= $CMS->lang["svctype_{$svctype}"];
						$this->report['list_group'] .= $this->report["svctype_{$j}"] ? "," : "";
						
						$CMS->order->sql_add .= $this->report["svctype_{$j}"] ? " OR " : ($this->report['service_list'][0] ? " OR " : " AND " );
					}
				}
			}
			
			// Get service list
			if(!$this->report['svctype_1'] || !$this->report['svctype_2'] || !$this->report['svctype_3'] || !$this->report['svctype_4'] || !$this->report['svctype_5'] || !$this->report['svctype_6'])
			{
				if($this->report['service_list'][0])
				{
					$CMS->order->sql_add .= " S.service_id NOT IN (";
					for($i=0;$i<count($this->report['service_list']);$i++)
					{
						$CMS->order->sql_add .= $this->report['service_list'][$i];
						$CMS->order->sql_add .= $this->report['service_list'][$i+1] ? "," : "";	
					}
					$CMS->order->sql_add .= ") AND ";
				}
				
				$CMS->order->sql_add .= $this->is_default == 0 ? " 1=1 ) AND " : "";
			}
		}
		//========================================================================================= End Service
		
		//========================================================================================= Payment Status
		if($this->mod_name == "report_all")
		{
			//******* Total Payment Status
			if((!$this->report['total_payment_status_1'] || !$this->report['total_payment_status_2'] || !$this->report['total_payment_status_3']) && $this->is_default == 0)
			{
				$CMS->order->sql_add .= "(";
				if ( $this->report['total_payment_status_1'])
				{
					$CMS->order->sql_add .= " (O.ord_payment_status=1) "; 
					if($this->report['total_payment_status_2'] || $this->report['total_payment_status_3'])
					{
						$CMS->order->sql_add .= " OR ";	
					}
				}
				if ( $this->report['total_payment_status_2'])
				{
					$CMS->order->sql_add .= " (O.ord_payment_status=4) ";
					if($this->report['total_payment_status_3'])
					{
						$CMS->order->sql_add .= " OR ";	
					}
				}
				if ( $this->report['total_payment_status_3']) // 3 is Payment Yet
				{
					$CMS->order->sql_add .= " (O.ord_payment_status=0) "; 
				}
				$CMS->order->sql_add .= " ) AND ";
			}
			//** End 
		}
		else
		{
			if ( $this->report['payment_status'] == 1 )
			{
				$CMS->order->sql_add .= " (O.ord_payment_status=1) AND "; 
			}
			else if ( $this->report['payment_status'] == 2 )
			{
				$CMS->order->sql_add .= " (O.ord_payment_status=4) AND "; 
			}
			// Add 21.4.2012
			else if ( $this->report['payment_status'] == 3 ) // 3 is Payment Yet
			{
				$CMS->order->sql_add .= " (O.ord_payment_status=0) AND "; 
			}
			else
			{
				$CMS->order->sql_add .= " (O.ord_payment_status=1) AND ";
			}
		}
		//========================================================================================= End Payment
		
		//========================================================================================= Ord status
		$CMS->order->sql_add .= !empty($this->report['ord_status'])  ? " O.ord_status='{$this->report['ord_status']}' AND " : " O.ord_status NOT IN (3) AND ";
		//========================================================================================= End status
		
		//========================================================================================= Payment method 
		if($this->mod_name == "report_all")
		{
			//************ Total payment method
			if(!$this->report['total_payment_method_1'] || !$this->report['total_payment_method_2'] || !$this->report['total_payment_method_3'])
			{
				$CMS->order->sql_add .= "(";
				if ( $this->report['total_payment_method_1']) 
				{ 
					$CMS->order->sql_add .= " O.ord_payment_method=0 "; 
					$CMS->order->sql_add .= $this->report['total_payment_method_2'] || $this->report['total_payment_method_3'] ? " OR " : " AND ";
				}
				if ( $this->report['total_payment_method_2']) 
				{ 
					$CMS->order->sql_add .= " O.ord_payment_method=1 "; 
					$CMS->order->sql_add .= $this->report['total_payment_method_3'] ? " OR " : " AND ";
				}
				if ( $this->report['total_payment_method_3']) 
				{ 
					$CMS->order->sql_add .= " O.ord_payment_method=3 AND "; 
				}
				$CMS->order->sql_add .= "1=1) AND";
	
			}
			// ** End
		}
		else
		{
			if ( $this->report['payment_method'] == "0" ) { $CMS->order->sql_add .= " O.ord_payment_method=0 AND "; }
			else if ( $this->report['payment_method'] == "1" ) { $CMS->order->sql_add .= " O.ord_payment_method=1 AND "; }
			else if ( $this->report['payment_method'] == "3" ) { $CMS->order->sql_add .= " O.ord_payment_method=3 AND "; }
			else {  $CMS->order->sql_add .= " O.ord_payment_method!=4 AND "; } // Prevent stats from order 119996, LHL 04/04/2013
		}
		//========================================================================================= End Payment method
		
		// Customer
		if ( $this->report['customer'] )
		{
			$customer = $CMS->customer->getInfo($this->report['customer']);
			$sql_customer = $CMS->mod_customer->load_profile_list("O.", $customer );
			$CMS->order->sql_add .= " ({$sql_customer} O.cus_id='{$customer['cus_id']}') AND ";
		}
		
		// Member
		if ( $this->report['member'] )
		{
			$member_id = $CMS->customer->getInfo($this->report['member']);
			//$sql_customer = $CMS->mod_customer->load_profile_list("O.", $customer );
			$CMS->order->sql_add .= " ({$sql_customer} O.member_id='{$member_id['cus_id']}') AND ";
			$CMS->order->sql_add .= " ord_payment_status = 1 AND ";
			
			if($this->report['ord_member_status'] == 0)
			{
				$CMS->order->sql_add .= " ord_member_status = 0 AND ";	
			}
			else if($this->report['ord_member_status'] == 1)
			{
				$CMS->order->sql_add .= " ord_member_status = 1 AND ";	
			}
		}
		
		//========================================================================================= User
		if ( preg_match("/(g)/", $this->report['employee']) == true )
		{
			$this->report['employee'] = str_replace("g", "", $this->report['employee']);
			
			$CMS->order->sql_add .= " ( ";
			
			$sql = $DB->query("SELECT * FROM ".root_table."user WHERE userg_id='{$this->report['employee']}'");

			$i = 0;
			while ( $userlist = $DB->fetch_array( $sql ) )
			{
				$i++;
				$CMS->order->sql_add .=  " T.transaction_confirm_by='{$userlist['user_id']}' ";
				$CMS->order->sql_add .= $i==$DB->num_rows($sql) ? "" : " OR ";
			}
			
			$CMS->order->sql_add .= " ) AND ";
		}
		else if ( $this->report['employee'] )
		{
			$CMS->order->sql_add .= " T.transaction_confirm_by='{$this->report['employee']}' AND ";
		}
		//========================================================================================= End User

		// Stats all orders
		if ( $this->report['format'] == "vnnic" )
		{
			//$this->report['reseller_payment'] = 1;
		}

		// Except reseller order
		if(!$this->report['member'] && $this->mod_name != "report_all")
		{
			if ( $this->report['reseller_payment'] == 0 || $this->report['reseller_payment'] == 2)
			{
				$CMS->order->sql_add .= " O.ord_payment_type!=3 AND ";	 
			}
			else if ( $this->report['reseller_payment'] == 1 )
			{
				$CMS->order->sql_add .= " O.ord_payment_type=3 AND ";
			}
		}
		
		// Start time, end time
		$CMS->order->sql_add .= $this->report['start'] ? " IF('{$this->report['reseller_payment']}'=1 OR '{$this->report['payment_status']}' = 2,O.ord_time,O.ord_payment_confirm_time) >= ".$CMS->class->date->date2time($this->report['start'],1)." AND " : "";
		$CMS->order->sql_add .= $this->report['end'] ? " IF('{$this->report['reseller_payment']}'=1 OR '{$this->report['payment_status']}' = 2,O.ord_time,O.ord_payment_confirm_time) <= ".($CMS->class->date->date2time($this->report['end'],1)+24*3600)." AND " : "";

		//========================================================================================= All, Register, renew
		if ( $this->report['type'] == 3 ) // Transfer
		{
			$CMS->order->sql_add .= " S.service_code LIKE ('TF%') AND ";
		}
		else if ( $this->report['type'] == 2 ) // Renew
		{
			$CMS->order->sql_add .= " S.service_code LIKE ('DT%') AND ";
		}
		else if ( $this->report['type'] == 1 ) // Register
		{
			$CMS->order->sql_add .= " S.service_code NOT LIKE ('TF%') AND S.service_code NOT LIKE ('DT%') AND "; 
		}
		//========================================================================================= End Type
		
		// ************ Total cycle
		if((!$this->report['report_cycle_1'] || !$this->report['report_cycle_2'] || !$this->report['report_cycle_3'] || !$this->report['report_cycle_4'] || !$this->report['report_cycle_5'] || !$this->report['report_cycle_6']) && $this->is_default == 0 && $this->mod_name == "report_all")
		{
			$CMS->order->sql_add .= "( ";
			if($this->report['report_cycle_1'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 3 ) ";
				if($this->report['report_cycle_2'] || $this->report['report_cycle_3'] || $this->report['report_cycle_4'] || $this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";
				}
			}
			if($this->report['report_cycle_2'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 3 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 6 ) ";
				if($this->report['report_cycle_3'] || $this->report['report_cycle_4'] || $this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_3'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 6 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 12 ) ";
				if($this->report['report_cycle_4'] || $this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_4'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 12 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 24 ) ";
				if($this->report['report_cycle_5'] || $this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_5'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 24 AND IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) <= 36 ) ";
				if($this->report['report_cycle_6'])
				{
					$CMS->order->sql_add .= " OR ";	
				}
			}
			if($this->report['report_cycle_6'])
			{
				$CMS->order->sql_add .= "(IF(O.ord_cycle_type=2,O.ord_cycle*12,O.ord_cycle) >= 36 ) ";
			}
			$CMS->order->sql_add .= " ) AND ";
		}
		//**End
		
		//*********** Get report type
		
		if($this->mod_name == "report_all")
		{
			$this->report['list_type'] = "";
			
			if($this->is_default == 1)
			{
				$this->report['list_type'] = $CMS->lang['report_type_1'].",".$CMS->lang['report_type_2'].",".$CMS->lang['report_type_3'];	
			}
			else
			{
				if($this->report['type_1'])
				{
					$type = $this->report['type_1'];
					$this->report['list_type'] .= $CMS->lang["report_type_{$type}"];
					
					$this->report['list_type'] .= ($this->report['type_2'] || $this->report['type_3']) ? ", " : "."; 
					
				}
				if($this->report['type_2'])
				{
					$type = $this->report['type_2'];
					$this->report['list_type'] .= $CMS->lang["report_type_{$type}"];
					
					$this->report['list_type'] .= ($this->report['type_3']) ? ", " : "."; 
					
				}
				if($this->report['type_3'])
				{
					$type = $this->report['type_3'];
					$this->report['list_type'] .= $CMS->lang["report_type_{$type}"].".";
				}
			}
		}
		
		// Sort
		$CMS->order->order_extend = "";
		$CMS->order->order_field = "ord_time";
		$CMS->order->order_by = "ASC";
		
		// Footer input
		$this->set_footer_input();
	
		// Page
		$CMS->order->per_page = in_array($this->report['format'], $this->download) == true ? 999999 : $this->report['per_page'];

		$sql_add_more = "";
		
		$sql_add_more .= $this->report['payment_status'] ? "&report_payment_status={$this->report['payment_status']}" : "";
		$sql_add_more .= $this->report['ord_status'] ? "&ord_status={$this->report['ord_status']}" : "";

		// Get List
		$CMS->order->listing($sql_add_more);
	}
	
	public function build_excel_customer()
	{
		global $CMS, $DB;
		
		// Sheet data
		$this->sheet_data = array(
			array(10, "cus_id","align_left"),
			array(13,"cus_code","align_left"),
			array("auto","cus_username","align_left"),
			array("auto","cus_own_type","align_left"),
			array("auto","cus_email","align_left"),
			array("auto","cus_contract_name","align_left"),
			array("auto","cus_contract_phone","align_left"),
			array("auto","cus_contract_address","align_left"),
			array("auto","cus_total_balance"),
			array("auto","cus_balance"),
			array("auto","cus_debt_balance"),
		);
		
		// Create sheet name
		$location = $CMS->input['location'];
		$this->sheet_date_display = "Reseller_".$location."_".$CMS->class->date->date_format(time());
		
		// Sheet header
		$this->set_excel_header();
		
		// Initialize
		while( $data = $DB->fetch_array( $CMS->customer->sql_query ) )
		{
			$data = $CMS->customer->convertvalue($data);
			
			// Content
			$this->set_excel_content($data);
		}
	}
	
	
	public function convert_input_all()
	{
		global $CMS, $DB;
		// Start time, end time
			$array = array();
			$row = array();
			$row1 = array();
			$row2 = array();
			//$report_start = $CMS->class->date->date2time($CMS->input['report_start'],1);
			//$report_end = ($CMS->class->date->date2time($CMS->input['report_start'],1)+24*3600);
			
			$royalty = $CMS->input['royalty'];
			
			$sql_add_news .= $this->report['report_start'] ? " news_time > ".$CMS->class->date->date2time($this->report['report_start'],1)." AND " : "";
			$sql_add_album .= $this->report['report_start'] ? " album_time > ".$CMS->class->date->date2time($this->report['report_start'],1)." AND " : "";
			$sql_add_video .= $this->report['report_start'] ? " video_time > ".$CMS->class->date->date2time($this->report['report_start'],1)." AND " : "";

			$sql_add_news .= $this->report['report_end'] ? " news_time < ".($CMS->class->date->date2time($this->report['report_end'],1)+24*3600)." AND " : "";
			$sql_add_album .= $this->report['report_end'] ? " album_time < ".($CMS->class->date->date2time($this->report['report_end'],1)+24*3600)." AND " : "";
			$sql_add_video .= $this->report['report_end'] ? " video_time < ".($CMS->class->date->date2time($this->report['report_end'],1)+24*3600)." AND " : "";



			if($royalty != "" AND $royalty == 1)
			{
				$sql_royalty_news = "news_royalty > 0 AND ";
				$sql_royalty_album = "album_royalty > 0 AND ";
				$sql_royalty_video = "video_royalty > 0 AND ";
				
			}
			elseif($royalty != "" AND  $royalty == 0)
			{
				$sql_royalty_news = "news_royalty = 0 AND ";
				$sql_royalty_album = "album_royalty = 0 AND ";
				$sql_royalty_video = "video_royalty = 0 AND ";	
			}
			
			$active = $CMS->input['active'];
			if($active != "" AND $active == 1)
			{
				$sql_active_news = "news_active =1  AND ";
				$sql_active_album = "album_active =1 AND ";
				$sql_active_video = "video_active =1 AND ";
				
			}
			elseif($active != "" AND  $active == 0)
			{
				$sql_active_news = "news_active = 0 AND ";
				$sql_active_album = "album_active = 0 AND ";
				$sql_active_video = "video_active = 0 AND ";	
			}
			elseif($active != "" AND  $active == 2)
			{
				$sql_active_news = "news_active = 2 AND ";
				$sql_active_album = "album_active = 2 AND ";
				$sql_active_video = "video_active = 2 AND ";	
			}
			
			$user_id = $CMS->input['user_id'];
			if($user_id != "" AND $user_id != "all")
			{
				$sql_user = "user_id = '{$user_id}' AND ";
			}
			if($user_id == "" OR $user_id == "all")
			{
				$user_id = "all";
			}
			// Get Rows News
			$sql_news = $DB->query("SELECT * FROM ".root_table."news WHERE {$sql_royalty_news} {$sql_active_news} {$sql_user} {$sql_add_news} 1 =1 AND news_deleted = 0 ORDER BY news_id DESC");
			$total = "";
			//print_r ("SELECT * FROM ".root_table."news WHERE {$sql_royalty_news} {$sql_active_news} {$sql_user} 1 =1 ORDER BY news_id DESC");exit;
			if ( $DB->num_rows( $sql_news ) > 0 )
			{
				$i = 0;
				while( $result = $DB->fetch_array($sql_news))
				{			
					// Convert info
					
					$data = $CMS->news->convertvalue($result);
					$data['time_bkk'] = $data['news_time_bkk'];
					$data['time_bk'] = $data['news_time'];
					$data['name'] = $data['news_name'];
					$data['cat_id'] = $data['cat_id_bk2'];
					$data['active'] = $data['news_active_bk'];
					
					$data['user_name'] = $data['user_name'];
					$data['royalty'] = $data['news_royalty'];
					
					
					$total += $data['news_royalty'];
			
					$data['type'] = "news";
					$array = array_merge($array,array($data));
					$row[$i] = $data;
					$i++;
				}
			}
			$data['total_news'] = $DB->num_rows( $sql_news ) ;
		
			// Get Rows Album
			$sql_album = $DB->query("SELECT * FROM ".root_table."album WHERE {$sql_royalty_album} {$sql_active_album} {$sql_user} {$sql_add_album} 1 =1 AND album_deleted = 0  ORDER BY album_id DESC");
			
			if ( $DB->num_rows( $sql_album) > 0 )
			{
				$j = intval(0+count($row));
				while( $result_1 = $DB->fetch_array($sql_album) )
				{
					// Convert info
				
					$data_album = $CMS->album->convertvalue($result_1);
					$data_album['time_bkk'] = $data_album['album_time_bkk'];
					$data_album['time_bk'] = $data_album['album_time'];
					$data_album['name'] = $data_album['album_name'];
					$data_album['cat_id'] = $data_album['cat_id_bk'];
					$data_album['active'] = $data_album['album_active_bk'];
					
					$data_album['user_name'] = $data_album['user_name'];
					$data_album['royalty'] = $data_album['album_royalty'];
					$total += $data_album['album_royalty'];
					
					$data_album['type'] = "album";
					$array = array_merge($array,array($data_album));
					$row1[$j] = $data_album;
					$j++;
				}
			}
			
				$data['total_album'] = $DB->num_rows( $sql_album ) ;
				
			// Get Rows Video
			$sql_video= $DB->query("SELECT * FROM ".root_table."video WHERE {$sql_royalty_video} {$sql_active_video} {$sql_user} {$sql_add_video} 1 =1 AND video_deleted = 0 ORDER BY video_id DESC");
			if ( $DB->num_rows( $sql_video ) > 0 )
			{
				$k =  intval(count($row)+count($row1));
				while( $result_2 = $DB->fetch_array($sql_video ) )
				{
					// Convert info

					$data_video = $CMS->video->convertvalue($result_2);
					$data_video['time_bkk'] = $data_video['video_time_bkk'];
					$data_video['time_bk'] = $data_video['video_time'];
					$data_video['name'] = $data_video['video_name'];
					$data_video['cat_id'] = $data_video['cat_id_bk'];
					$data_video['active'] = $data_video['video_active_bk'];
					
					$data_video['user_name'] = $data_video['user_name'];
					$data_video['royalty'] = $data_video['video_royalty'];
					
				
					$total += $data_video['video_royalty'];
					$data_video['type'] = "video";
					$array = array_merge($array,array($data_video));
					$row2[$k] = $data_video;
					$k++;
				}
			}
			
			$data['total_video'] = $DB->num_rows( $sql_video ) ;
			$data['total_royalty'] = $total;
				//$array = array_merge($row,$row1);
			
				//$array =  array_merge($array,$row2);
				$temp = array();
			/*$me = 0;
			foreach ($array as $key=>$value) {
				$temp[$me] = $value;
				$me++;
			}*/
		
			$array = $CMS->class->input->array_sort($array, "time_bkk", "desc", 0);
			if($_SERVER['REMOTE_ADDR'] == "42.119.93.153")
				{
					
				}
			$lala= array();
			$just=0;
				foreach ($array as $key=>$value) {
				$lala[$just] = $value;
				$just++;
			}
			
				if($_SERVER['REMOTE_ADDR'] == "118.69.66.146")
				{
					//print_r ($array);exit;
				}
				
			$return = array($lala,$data);
			return $return;
	}


	public function report_all($time_type='alltime')
	{
		global $CMS, $DB;

		$clause = $this->setting_time($time_type, "ord_time");

		$sql = $DB->query("SELECT SUM(ord_total) as total, COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_payment_status = 1 AND ord_deleted = 0 AND (ord_status = 0 OR ord_status = 1) {$clause}");
		//DATE_FORMAT(FROM_UNIXTIME(ord_time),'%m-%d-%Y') as date_view, 
		$number_order = 0;
		$total_order = 0;
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$number_order += $result['number_order']; // Tổng đơn hàng đã thanh toán
				$total_order += $result['total'];
			}

		}


		// Tổng số đơn hàng 
		$number_total_order = $this->get_number_order("", $clause);
		$number_order_pending = $this->get_number_order(0, $clause);
		$number_order_cancer = $this->get_number_order(2, $clause);
		$number_order_refund = $this->get_number_order(4, $clause);
// print $number_total_order;exit;
		// Về sản phẩm	
		// $product_sales_most = $this->get_number_product_sales($clause);

		$data = array(
				"number_order" => $number_order,
				"total_order" => $total_order,
				"number_total_order" => $number_total_order,
				"number_order_cancer" => $number_order_cancer,
				"number_order_refund" => $number_order_refund,
				"number_order_pending" => $number_order_pending,
			);
		return $data;
	}

	public function convertdate($date='')
	{
		global $CMS;

		// date_format dd/mm/YY convert to YY/mm/dd
		$listdate = explode("/", $date);
		$new_date = $listdate[2]."/".$listdate[1]."/".$listdate[0];
		return $new_date;
	}

	public function setting_time($time_type='alltime', $table_time="ord_time")
	{
		global $CMS;

		if($CMS->input['report_start'] or $CMS->input['report_end'])
		{
			$time_from = $CMS->input['report_start'] ? strtotime($this->convertdate($CMS->input['report_start'])) : "";
			$time_to = $CMS->input['report_end'] ? strtotime($this->convertdate($CMS->input['report_end'])) + 3600*24 - 1: "";
			if($time_from and $time_to)
			{
				$clause = " AND {$table_time} BETWEEN '{$time_from}' AND '{$time_to}' ";
			}else
			{
				if($time_from)
				{
					$clause = " AND {$table_time} >= '{$time_from}' ";
				}elseif($time_to)
				{
					$clause = " AND {$table_time} <= '{$time_to}' ";
				}
			}

		}else
		{
			if($time_type == "today")
			{
				$today = date("Y/m/d",strtotime("today"));
				$time_from = strtotime($today);
				$time_to = $time_from + 3600*24 - 1;
				$clause = " AND {$table_time} BETWEEN '{$time_from}' AND '{$time_to}' ";
			}elseif($time_type == "yesterday")
			{
				$time_check = date("Y/m/d",strtotime("yesterday"));
				$time_from = strtotime($time_check);
				$time_to = $time_from + 3600*24 - 1;
				$clause = " AND {$table_time} BETWEEN '{$time_from}' AND '{$time_to}' ";
			}elseif($time_type == "thisweek")
			{
				$time_check_1 = date("Y/m/d", strtotime('monday this week', strtotime('last sunday')));
				$time_check_2 = date("Y/m/d", strtotime('sunday this week', strtotime('last sunday')));

				$time_from = strtotime($time_check_1);
				$time_to = strtotime($time_check_2) + 3600*24 - 1;
				$clause = " AND {$table_time} BETWEEN '{$time_from}' AND '{$time_to}' ";
			}elseif($time_type == "lastweek")
			{
				$time_check_1 = date("Y/m/d", strtotime('monday last week', strtotime('last sunday')));
				$time_check_2 = date("Y/m/d", strtotime('sunday last week', strtotime('last sunday')));

				$time_from = strtotime($time_check_1);
				$time_to = strtotime($time_check_2) + 3600*24 - 1;
				$clause = " AND {$table_time} BETWEEN '{$time_from}' AND '{$time_to}' ";
			}elseif($time_type == "thismonth")
			{
				$time_check_1 = date("Y/m/d", strtotime('first day of this month'));
				$time_check_2 = date("Y/m/d", strtotime('last day of this month'));

				$time_from = strtotime($time_check_1);
				$time_to = strtotime($time_check_2) + 3600*24 - 1;
				$clause = " AND {$table_time} BETWEEN '{$time_from}' AND '{$time_to}' ";
			}elseif($time_type == "lastmonth")
			{
				$time_check_1 = date("Y/m/d", strtotime('first day of last month'));
				$time_check_2 = date("Y/m/d", strtotime('last day of last month'));

				$time_from = strtotime($time_check_1);
				$time_to = strtotime($time_check_2) + 3600*24 - 1;
				$clause = " AND {$table_time} BETWEEN '{$time_from}' AND '{$time_to}' ";
			}elseif($time_type == "alltime")
			{
				$clause = "";
			}
		}// End if

		return $clause;
	}

	public function get_number_order($type='', $clause="")
	{
		global $CMS, $DB;

		/*******************
		*   Type = 0: Đơn hàng đang chờ
		*   Type = 1: Đơn hàng Thành công
		*   Type = 2: Đơn hàng bị huỷ
		*   Type = 3: Đơn hàng đang chờ huỷ
		*   Type = 4: Đơn hàng hoàn tiền
		*   clause : điều kiện về thời gian
		********************/
		// Tổng số đơn hàng
		// print $type;
		if($type === "active")
		{
			$clause .= " AND (ord_status = 0 OR ord_status = 1) ";
		}elseif($type==="")
		{
			$clause .= "";
		}else
		{
			$type = intval($type);
			$clause .= " AND ord_status = '{$type}' ";
		}
		// print "SELECT COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_deleted = 0 AND ord_payment_status = 1 {$clause} GROUP BY date_view";exit;
		$sql_num_order = $DB->query("SELECT COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_deleted = 0 AND ord_payment_status = 1 {$clause}");//DATE_FORMAT(FROM_UNIXTIME(ord_time),'%m-%d-%Y') as date_view, 
		$number = intval($DB->fetch_array($sql_num_order)['number_order']);

		return $number;
	}

	public function get_number_product_sales($clause='')
	{
		global $CMS, $DB;

		// $clause = str_replace("ord_time", "O.ord_time", $clause);
		// print "SELECT SUM(OC.ordc_quantity) as total_quantity, OC.exp_id FROM ".root_table."order as O LEFT JOIN ".root_table."order_content as OC ON O.ord_id = OC.ord_id WHERE O.ord_deleted = 0 AND O.ord_payment_status=1 AND (O.ord_status = 0 or O.ord_status = 1) {$clause} GROUP BY OC.exp_id";exit;
		// // clause : điều kiện về thời gian
		// $sql = $DB->query("SELECT SUM(OC.ordc_quantity) FROM ".root_table."order as O LEFT JOIN ".root_table."order_content as OC ON O.ord_id = OC.ord_id WHERE O.ord_deleted = 0 AND O.ord_payment_status=1 AND (O.ord_status = 0 or O.ord_status = 1) {$clause}");

	}

	public function get_data_chart($time_type='', $cus_id=0)
	{
		global $CMS, $DB;

		if($time_type == "thisweek")
		{
			$time_check_1 = date("Y/m/d", strtotime('monday this week', strtotime('last sunday')));
			$time_check_2 = date("Y/m/d", strtotime('sunday this week', strtotime('last sunday')));

			$time_from = strtotime($time_check_1);
			$time_to = strtotime($time_check_2) + 3600*24 - 1;
			$clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
		}elseif($time_type == "lastweek")
		{
			$time_check_1 = date("Y/m/d", strtotime('monday last week', strtotime('last sunday')));
			$time_check_2 = date("Y/m/d", strtotime('sunday last week', strtotime('last sunday')));

			$time_from = strtotime($time_check_1);
			$time_to = strtotime($time_check_2) + 3600*24 - 1;
			$clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
		}elseif($time_type == "thismonth")
		{
			$time_check_1 = date("Y/m/d", strtotime('first day of this month'));
			$time_check_2 = date("Y/m/d", strtotime('last day of this month'));

			$time_from = strtotime($time_check_1);
			$time_to = strtotime($time_check_2) + 3600*24 - 1;
			$clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
		}elseif($time_type == "lastmonth")
		{
			$time_check_1 = date("Y/m/d", strtotime('first day of last month'));
			$time_check_2 = date("Y/m/d", strtotime('last day of last month'));

			$time_from = strtotime($time_check_1);
			$time_to = strtotime($time_check_2) + 3600*24 - 1;
			$clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
		}

		if($cus_id)
		{
			$clause2 = " AND cus_id = '{$cus_id}' ";
		}else
		{
			$clause2 = "";
		}

		$sql = $DB->query("SELECT DATE_FORMAT(FROM_UNIXTIME(ord_time),'%Y-%m-%d') as date_view, COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_payment_status = 1 AND ord_deleted = 0 {$clause} {$clause2} GROUP BY date_view ORDER BY date_view ASC");
		$number_order = array();
		$count_number = $DB->num_rows($sql);
		if($count_number > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$number_order[$result['date_view']] = $result['number_order'];
			}
		}

		$sql_pending = $DB->query("SELECT DATE_FORMAT(FROM_UNIXTIME(ord_time),'%Y-%m-%d') as date_view, COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_payment_status = 1 AND ord_deleted = 0 AND ord_status = 0 {$clause} {$clause2} GROUP BY date_view ORDER BY date_view ASC");
		$pending = array();
		$count_pending = $DB->num_rows($sql_pending);
		if($count_pending > 0)
		{
			while ($result = $DB->fetch_array($sql_pending)) 
			{
				$pending[$result['date_view']] = $result['number_order'];
			}
		}

		$sql_refund = $DB->query("SELECT DATE_FORMAT(FROM_UNIXTIME(ord_time),'%Y-%m-%d') as date_view, COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_payment_status = 1 AND ord_deleted = 0 AND ord_status = 4 {$clause} {$clause2} GROUP BY date_view ORDER BY date_view ASC");
		$refund = array();
		$count_refund = $DB->num_rows($sql_refund);
		if($count_refund > 0)
		{
			while ($result = $DB->fetch_array($sql_refund)) 
			{
				$refund[$result['date_view']] = $result['number_order'];
			}
		}

		$num_date = ($time_to - $time_from)/(86400);
		for($i = 0; $i<=$num_date; $i++)
		{
			$date = date("Y/m/d", $time_from);
			$date = date("Y-m-d",strtotime("{$date} + {$i} day"));
			if(!$number_order[$date])
			{
				$data_number_order[$date] = 0;
			}else
			{
				$data_number_order[$date] = $number_order[$date];
			}

			if(!$pending[$date])
			{
				$data_pending[$date] = 0;
			}else
			{
				$data_pending[$date] = $pending[$date];
			}

			if(!$refund[$date])
			{
				$data_refund[$date] = 0;
			}else
			{
				$data_refund[$date] = $refund[$date];
			}
		}

		return array($data_number_order, $data_pending, $data_refund);
	}



	public function report_sales($time_type = "")
	{
		global $CMS, $DB;

		$clause = $this->setting_time($time_type, "O.ord_time");
		// print "SELECT SUM(OC.ordc_total) as total_money, OC.exp_id FROM ".root_table."order as O LEFT JOIN ".root_table."order_content as OC ON O.ord_id = OC.ord_id WHERE O.ord_deleted = 0 AND O.ord_payment_status=1 AND (O.ord_status = 0 or O.ord_status = 1) {$clause} GROUP BY OC.exp_id ORDER BY total_money DESC LIMIT 10";exit;
		// clause : điều kiện về thời gian
		$sql = $DB->query("SELECT SUM(OC.ordc_total) as total_money, OC.exp_id FROM ".root_table."order as O LEFT JOIN ".root_table."order_content as OC ON O.ord_id = OC.ord_id WHERE O.ord_deleted = 0 AND O.ord_payment_status=1 AND (O.ord_status = 0 or O.ord_status = 1) {$clause} GROUP BY OC.exp_id ORDER BY total_money DESC LIMIT 10");

		$data = array();
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$result['name_product'] = $CMS->exproduct->get_info($result['exp_id'], "exp_name");
				$data[] = $result;
			}
		}

		return $data;

	}

	public function report_sales_cus($time_type = "")
	{
		global $CMS, $DB;

		$clause = $this->setting_time($time_type, "ord_time");
		
		$sql = $DB->query("SELECT SUM(ord_total) as total_money, cus_id FROM ".root_table."order WHERE cus_id != 0 AND ord_deleted = 0 AND ord_payment_status=1 AND (ord_status = 0 or ord_status = 1) {$clause} GROUP BY cus_id ORDER BY total_money DESC LIMIT 10");
		$data = array();
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$result['name_cus'] = $CMS->customer->getInfo($result['cus_id'], "cus_username");
				$data[] = $result;
			}
		}

		return $data;


	}

	public function report_product($time_type = "")
	{
		global $CMS, $DB;

		$clause = $this->setting_time($time_type, "O.ord_time");
		// clause : điều kiện về thời gian
		$sql = $DB->query("SELECT SUM(OC.ordc_quantity) as quantity, OC.exp_id FROM ".root_table."order as O LEFT JOIN ".root_table."order_content as OC ON O.ord_id = OC.ord_id WHERE O.ord_deleted = 0 AND O.ord_payment_status=1 AND (O.ord_status = 0 or O.ord_status = 1) {$clause} GROUP BY OC.exp_id ORDER BY quantity DESC LIMIT 10");
		$data = array();
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$result['name_product'] = $CMS->exproduct->get_info($result['exp_id'], "exp_name");
				$data[] = $result;
			}
		}

		return $data;
	}

	public function report_campaigns($time_type = "")
	{
		global $CMS, $DB;

		$clause = $this->setting_time($time_type, "ord_time");
		
		$sql = $DB->query("SELECT SUM(ord_subtotal) as total_money, cam_id FROM ".root_table."order WHERE ord_deleted = 0 AND ord_payment_status=1 AND (ord_status = 0 or ord_status = 1) {$clause} GROUP BY cam_id ORDER BY total_money DESC LIMIT 10");
		$data = array();
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$result['name_campaigns'] = $CMS->campaigns->get_info($result['cam_id'], "cam_name");
				$data[] = $result;
			}
		}

		return $data;
	}

	public function get_data_chart_product()
	{
		global $CMS, $DB;

		// $clause = $this->setting_time("alltime", "ord_time");
		
		$sql = $DB->query("SELECT SUM(OC.ordc_quantity) as quantity, OC.exp_id FROM ".root_table."order as O LEFT JOIN ".root_table."order_content as OC ON O.ord_id = OC.ord_id WHERE O.ord_deleted = 0 AND O.ord_status IN (0,1,2,3,4) AND ordc_deleted = 0 GROUP BY OC.exp_id ORDER BY quantity");
		$data = array();
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$result['name_product'] = $CMS->exproduct->get_info($result['exp_id'], "exp_name");
				$data[] = $result;
			}
		}

		return $data;
	}

	public function get_list_profit_by_cus($time_type = "alltime")
	{
		global $CMS, $DB;

		$clause = $this->setting_time($time_type, "O.ord_time");
		$data = array();
		
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT SUM(OC.ordc_profit) as ordc_profit, OC.cus_id FROM ".root_table."order AS O LEFT JOIN ".root_table."order_content AS OC ON O.ord_id = OC.ord_id WHERE O.ord_payment_status = 1 AND (O.ord_status = 0 OR O.ord_status = 1) AND ord_deleted = 0 {$clause} GROUP BY cus_id", 20);

		if($DB->num_rows($this->sql_query) > 0)
		{
			while ($result = $DB->fetch_array($this->sql_query)) 
			{
				$data[] = $result;
			}
		}

		return $data;
	}

	/***************************************************************************************/
	function excel_header()
	{
		global $CMS;
		// $CMS->report->test_report();
    	require_once(root_path."acp/tools/excelphp/PHPExcel.php");
		
		// Create new PHPExcel object
		$this->dataExcel = new PHPExcel();

		// Set document properties
		$this->dataExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");

		// Set default font
		$this->dataExcel->getDefaultStyle()->getFont()->setName('Arial')
		                                          ->setSize(10);
	}

	function test_report()
	{
		global $CMS, $DB;

		$this->excel_header();

		// Set style
		$style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

		// Set row title
		$this->dataExcel->getActiveSheet()->setCellValue('A1', 'STT')
		                              ->setCellValue('B1', 'NAME')
		                              ->setCellValue('C1', 'EMAIL')
		                              ->setCellValue('D1', 'SUBJECT')
		                              ->setCellValue('E1', 'CONTENT')
		                              ->setCellValue('F1', 'TIME CREATE');
		// Format cell title
		$this->dataExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true)->setItalic(true)->setColor(new PHPExcel_Style_Color("#FFFFFF"));
		$this->dataExcel->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray($style);

		$this->dataExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true)->setItalic(true)->setColor(new PHPExcel_Style_Color("#FFFFFF"));
		$this->dataExcel->getActiveSheet()->getStyle("B1")->getAlignment()->applyFromArray($style);

		$this->dataExcel->getActiveSheet()->getStyle("C1")->getFont()->setBold(true)->setItalic(true)->setColor(new PHPExcel_Style_Color("#FFFFFF"));
		$this->dataExcel->getActiveSheet()->getStyle("C1")->getAlignment()->applyFromArray($style);

		$this->dataExcel->getActiveSheet()->getStyle("D1")->getFont()->setBold(true)->setItalic(true)->setColor(new PHPExcel_Style_Color("#FFFFFF"));
		$this->dataExcel->getActiveSheet()->getStyle("D1")->getAlignment()->applyFromArray($style);

		$this->dataExcel->getActiveSheet()->getStyle("E1")->getFont()->setBold(true)->setItalic(true)->setColor(new PHPExcel_Style_Color("#FFFFFF"));
		$this->dataExcel->getActiveSheet()->getStyle("E1")->getAlignment()->applyFromArray($style);

		$this->dataExcel->getActiveSheet()->getStyle("F1")->getFont()->setBold(true)->setItalic(true)->setColor(new PHPExcel_Style_Color("#FFFFFF"));
		$this->dataExcel->getActiveSheet()->getStyle("F1")->getAlignment()->applyFromArray($style);

		$DB->query("SELECT * FROM ".root_table."contact");
		$i = 2;
		$count_row = $DB->num_rows() + 1;
		while ($result = $DB->fetch_array()) 
		{
			$this->dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
		                              ->setCellValue('B'.$i, $result['con_name'])
		                              ->setCellValue('C'.$i, $result['con_email'])
		                              ->setCellValue('D'.$i, $result['con_subject'])
		                              ->setCellValue('E'.$i, $result['con_content'])
		                              ->setCellValue('F'.$i, PHPExcel_Shared_Date::PHPToExcel( $result['con_time']));
		    $this->dataExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
		    $this->dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);

		    $i++;
		}
		
		// Set size column
		$this->dataExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->dataExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$this->dataExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$this->dataExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$this->dataExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

		// Set height 
		$this->dataExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
		$this->dataExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray(
		        array(
		            'fill' => array(
		                'type' => PHPExcel_Style_Fill::FILL_SOLID,
		                'color' => array('rgb' => '000000')
		            )
		        )
		    );
		$this->dataExcel->getActiveSheet()->getStyle('A1:F'.$count_row)->getBorders()->applyFromArray(
              array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array(
                          'rgb' => '808080'
                      )
                  )
              )
      );
		// Rename worksheet
		$this->dataExcel->getActiveSheet()->setTitle('Danh sách liên hệ');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->dataExcel->setActiveSheetIndex(0);

// 		$objPHPExcel  = PHPExcel_IOFactory::load($filename);
// $objWorksheet = $objPHPExcel->getActiveSheet();
// For Saving The File

// header('Content-Type: application/vnd.ms-excel');
// header('Content-Disposition: attachment;filename="yourfile.xls"');
// header('Cache-Control: max-age=0');
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save('php://output');





		$objWriter = PHPExcel_IOFactory::createWriter($this->dataExcel, 'Excel5');
		$objWriter->save("D:/02simple.xls");
	}

	public function build_excel_test()
	{
		global $CMS, $DB;
	
		// Sheet data
		$this->sheet_data = array(
			array(15, "con_name", "valign_top"),  
			array("auto","con_email", "valign_top"),
			array("auto","con_subject", "valign_top"),
			array("auto","con_content", "wrap"),
			array("auto", "con_time", "valign_top"),
			// //array(15, "inv_id"),
			// array("auto", "cus_name", "valign_top"),
			// //array("auto", "transaction_sender_user"),
			// //array("auto", "transaction_sender_provider"),  
			// //array("auto", "transaction_sender_other"),
			// array("auto", "transaction_acc", "valign_top"),
			// array("auto", "transaction_acc2", "valign_top"),
			// array("auto", "transaction_type", "valign_top"),
			// array("auto", "transaction_payment_method", "valign_top"),
			// array("auto", "transaction_status_exel"),
			// array("auto","bank_id","valign_top"),
			// array("auto", "transaction_confirm_by", "valign_top"),
			// array("auto", "transaction_time_short", "valign_top"),
			// array("auto", "transaction_confirm_time_short", "valign_top"),
			// array(15, "transaction_real_bk", array("align_right", "valign_top")), // , "currency"
		);
		$sql = $DB->query("SELECT * FROM ".root_table."contact");
		// Initialize
		while( $data = $DB->fetch_array( $sql ) )
		{
			// Report display
			$this->sheet_date_display = $this->build_sheet(date("d/m/Y", $data['con_time'])); 

			// Set sheet
			if ( $this->sheet_date_display != $this->sheet_date )
			{				
				if ( $this->sheet_cnt != 0 )
				{
					print "adfa";exit;
					// Write last line
					$this->build_last_line("Tong cong");
					
					// Continue open new sheet		
					$CMS->class->excel->data->createSheet();	
				}
				
				// Reset sheet
				$this->set_excel_header();
			}
			
			// Set date row
			if ( $data['con_time'] != $this->sheet_now )
			{
				$this->set_excel_title("test excel");
			}
			
			// Content
			$this->set_excel_content($data);
			
			// Write last line
			$this->total += 120;
		}
		
		// Write last line
		$this->build_last_line("Tong63 cong");
	}


	// google api analytics
	public function google_getreport($is_json = "")
	{
		global $CMS, $DB;

		// data
		$data = array();

		$date_start = date('Y-m-d', strtotime('first day of this month'));
   		$date_end = date('Y-m-d', strtotime('last day of this month'));


		$type = $CMS->input['report_type'];
		$data['dateranges'] = array('type'=> $type,'start' => $date_start,'end'	=> $date_end);
 
		// converte
		$CMS->api->google_api_analytics->setqueryReset();
		$CMS->api->google_api_analytics->setqueryDateRanges($date_start, $date_end);

		// set Metric :Số phiên
		$CMS->api->google_api_analytics->setqueryMetric('ga:sessions','sessions');

		// // set Metric :Người dùng
		 $CMS->api->google_api_analytics->setqueryMetric('ga:Users','Users');

		// // set Metric :Số lần xem trang
		 $CMS->api->google_api_analytics->setqueryMetric('ga:pageviews','pageviews');

		// // set Metric :phiên trên trang
		// $CMS->api->google_api_analytics->setqueryMetric('ga:pageviewsPerSession','pageviewsPerSession');

		// // set Metric :Thời gian trung bình của phiên
		 $CMS->api->google_api_analytics->setqueryMetric('ga:avgSessionDuration','avgSessionDuration');

		// // set Metric :Tỉ lệ thoát
		$CMS->api->google_api_analytics->setqueryMetric('ga:bounceRate','bounceRate');
		
		// // set Metric :Phiên mới - Phiên củ (100 - Phiên mới)
		$CMS->api->google_api_analytics->setqueryMetric('ga:percentNewSessions','percentNewSessions');

		// set dimension
		$CMS->api->google_api_analytics->setqueryDimension('ga:date');

		// set orderbys
		$CMS->api->google_api_analytics->setqueryOrderBys('ga:date');

		// get report
		$getReport = $CMS->api->google_api_analytics->getReport();
		
		// Số phiên
		$data['totals_session'] = $getReport['modelData']['reports']['0']['data']['totals']['0']['values']['0'];

		// Người dùng
		$data['totals_users'] = $getReport['modelData']['reports']['0']['data']['totals']['0']['values']['1'];

		// Số lần xem trang
		$data['totals_pageviews'] = $getReport['modelData']['reports']['0']['data']['totals']['0']['values']['2'];
		
		// Số phiên trên trang
		$data['totals_pageviewsPerSession'] = $getReport['modelData']['reports']['0']['data']['totals']['0']['values']['3'];
		$data['totals_pageviewsPerSession'] = round($data['totals_pageviewsPerSession'],2);

		// Thời gian trung bình của phiên
		$data['totals_avgSessionDuration'] = $getReport['modelData']['reports']['0']['data']['totals']['0']['values']['4'];
	
		// Convert seconds to datetime
		// HH
		$totals_seconds = round($data['totals_avgSessionDuration']);
		$hh = floor($totals_seconds / (60*60));

		// MM
		$totals_seconds = $totals_seconds - ($hh*60*60);
		$mm = floor($totals_seconds / 60);

		// SS
		$totals_seconds = $totals_seconds - ($mm*60);
		$ss = floor($totals_seconds);

		$hh = ($hh<10)? "0{$hh}" : $hh;
		$mm = ($mm<10)? "0{$mm}" : $mm;
		$ss = ($ss<10)? "0{$ss}" : $ss;
		$data['totals_avgSessionDuration'] = "{$hh}:{$mm}:{$ss}";

		// Tỉ lệ thoát
		$data['totals_bounceRate'] = $getReport['modelData']['reports']['0']['data']['totals']['0']['values']['5'];
		$data['totals_bounceRate'] = round($data['totals_bounceRate'],2);
		
		// Phiên mới - Phiên củ (100 - Phiên mới)
		$data['totals_percentNewSessions'] = $getReport['modelData']['reports']['0']['data']['totals']['0']['values']['6'];
		$data['totals_percentNewSessions'] = round($data['totals_percentNewSessions'],2);

		// Phiên củ
		$data['totals_percentOldSessions'] = 100 - $data['totals_percentNewSessions'];

		 
		 
	    $gg_analytics =<<<EOF
              <td valign="middle" align="left" class="MsoNormal" style="font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif; font-size:16px; color:#436fd1;line-height:20px;font-weight:bold">
                                                    
                                                Google Analytics
                                                <tr>
                                                    <td>Session: {$data['totals_session']}</td>
                                                    <td>Users: {$data['totals_users']}</td>
                                                 </tr>  
                                                <tr>
                                                    <td>Page views: {$data['totals_pageviews']}</td>
                                                    <td>Page viewsPerSession: {$data['totals_pageviewsPerSession']}</td>
                                                </tr>  
                                                 <tr>
                                                    <td>Avg Session Duration: {$data['totals_avgSessionDuration']}</td>
                                                    <td>Bounce Rate: {$data['totals_bounceRate']}%</td>
                                                </tr> 
                                                 <tr>
                                                    <td>Percent NewSessions: {$data['totals_percentNewSessions']}%</td>
                                                    <td>Percent Old Sessions: {$data['totals_percentOldSessions']}%</td>
                                                </tr>                             
              </td>
EOF;
		return $gg_analytics;	
	}

	
	public function yelp_api()
	{

		$postData = "grant_type=client_credentials&".
		            "client_id=9r2eoOAcqMYKJ6V9W1ByeQ&".
		            "client_secret=2Lw6twXAEGXveNMameBDghBjCb95EKaEVEfsXUbyTzUf7TarIJIsSL6e4UaTy0uE";
		$ch = curl_init();

		//set the url
		curl_setopt($ch,CURLOPT_URL, "https://api.yelp.com/oauth2/token");
		//tell curl we are doing a post
		curl_setopt($ch,CURLOPT_POST, TRUE);
		//set post fields
		curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);
		//tell curl we want the returned data
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);

		if($result){
		   $data = json_decode($result);
		   //echo "Token: ".$data->access_token;
		}


		$cu = curl_init();

		//set the url
		curl_setopt($cu,CURLOPT_URL, 'https://api.yelp.com/v3/businesses/'. urlencode ( 'get-waxed-friendswood'));
		//tell curl we are doing a post
		curl_setopt($cu,CURLOPT_HTTPGET, 0);
		curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
		//set post fields
		//tell curl we want the returned data
		curl_setopt($cu,CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $data->access_token));
		$a = curl_exec($cu);
		$rs = json_decode($a,true); 
   		$yelp_analytics =<<<EOF
              <td valign="middle" align="left" class="MsoNormal" style="font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif; font-size:16px; color:#436fd1;line-height:20px;font-weight:bold">
                                                    
                                                Yelp Report
                                                <tr>
                                                    <td>Reviews count: {$rs['review_count']}</td>
                                                    <td>Rating: {$rs['rating']}</td>
                                                 </tr>  
                                                                             
              </td>
EOF;
		return $yelp_analytics;	
	}
}
?>