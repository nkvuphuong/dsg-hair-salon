<?php

namespace models;
use core\ezy;
use lib\date;
use \lib\input;
use \lib\db;
use \lib\image;

class dashboard {

	/**
     * @var string
     *      Use for thumbnail image
     */
    public $thumb_folder = "thumbnail";

    /**
     * @var array
     *      Define size for images thumb (Width)
     */
    public $thumb_size = [
        'L' => 600,
        'M' => 400,
        'S' => 200
    ];

    static public function init()
    {
        global $tpl, $CMS;

//        $tpl->order = self::getOrderData();
 
        if(input::get('data_type') == 'json')
        {
            switch (input::get('mod'))
            {
                case 'trx':
                    $response = self::getTrxData();
                    break;
                case 'order':
                    $response = self::getOrdData();
                    break;
                default:
                    $response = [];
            }
            header('Content-Type: application/json');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        else
        {
            $tpl->trx = self::getTrxData();
            // 4 Block dashboard left
            $tpl->report['customer'] = self::getDataReport(3,"customer", "cus_time");
            $tpl->report['order'] = self::getDataReport(3,"order", "ord_time");
            $tpl->report['revenue'] = self::getDataReport(3,"revenue", "trx_time");
            $tpl->report['costs'] = self::getDataReport(3,"costs", "trx_time");

            // Order chart
            $tpl->chart['status_order'] = self::getDataChart(2,"status_order",'ord_time');
//            $tpl->chart['revenue_costs'] = self::getDataChart(3,"revenue_costs","trx_time");

            // Activities
            $tpl->getList['activity'] = self::getList(2,"activity","logs_time");
            $tpl->getList['issues'] = self::getList(2,"issues","iss_time");
            $tpl->getList['order'] = self::getList(2,"order","ord_time");
            $tpl->getList['customer'] = self::getList(2,"customer","cus_time");
        }

        // Default color website
        $_SESSION['color_theme'] = isset($_SESSION['color_theme']) ? $_SESSION['color_theme'] : "default";
    }

    /**
     * Get Order Data chart
     * @return array(chart, income, sources, chart_y, chart_stick)
     */

    static public function getOrderData()
    {
        global $CMS, $DB, $tpl;

        $report = array();

        // Temporary model, after test, it'll be move to model folder.
        list($first, $last) = $CMS->class->date->getFisrtLastInCurrentWeek();

        $first -= 24*3600; // Back to 1 day

        $DB->query("SELECT ord_id AS id, payment_status AS status, sum(ord_total) AS price, DATE_FORMAT(FROM_UNIXTIME(ord_time),'%d') AS day FROM ".root_table."order WHERE ord_time BETWEEN {$first} AND {$last} AND ord_deleted=0 GROUP BY day, status");

        $chart = array();
        $chart_highest = 0;

        // Overview
        $income = 0;
        $unpaid = 0;
        $paid = 0;
        $debt = 0;

        while ( $data = $DB->fetch_array() )
        {
            // Higest
            if ( $chart_highest < $data['price'] )
            {
                $chart_highest = $data['price'];
            }

            // Set price for that day
            $chart[$data['day']] += $data['price'];

            // Update overview
            $income += $data['price'];

            // Unpaid, Paid, Debt
            if ( $data['status'] == 0 )
            {
                $unpaid += $data['price'];
            } else if ( $data['status'] == 1 )
            {
                $paid += $data['price'];
            } else if ( $data['status'] == 2 )
            {
                $debt += $data['price'];
            }
        }

        $report['chart'] = "";

        // Start from sunday-monday to sunday-monday
        $dayi = $first;

        for ( $i = 1; $i <= 8; $i++)
        {
            $ii = $CMS->class->date->date_get( "d", $dayi );
            $dayi += 86400;

            //Output: $report['chart'] .= ['SUN', 123, '123'],
            $report['chart'] .= isset($chart[$ii]) ? "['{$ii}', ".intval($chart[$ii]).", '".$CMS->class->input->currency($chart[$ii])."'],".PHP_EOL : "['{$ii}', 0, '0'],".PHP_EOL;
        }

        // Detect currency type $ or d
        // Overview incomes
        if($CMS->vars['currency_type'] == "$")
        {
        	$report['income'] = $CMS->class->input->currency($income);
       		$report['sources'] = array(
            array("yellow", $CMS->class->input->currency($unpaid), $CMS->lang['dashboard_unpaid']),
            array("purple", $CMS->class->input->currency($debt), $CMS->lang['dashboard_debt']),
            array("lime", $CMS->class->input->currency($paid), $CMS->lang['dashboard_paid']),
        	);
        }
        else
        {
        	$report['income'] = $CMS->class->input->number($income, ".", 2);
       		$report['sources'] = array(
            array("yellow", round($unpaid/1000000, 2)."tr", $CMS->lang['dashboard_unpaid']),
            array("purple", round($debt/1000000, 2)."tr", $CMS->lang['dashboard_debt']),
            array("lime", round($paid/1000000, 2)."tr", $CMS->lang['dashboard_paid']),
        	);
        }
        

        // Create Y-column
        $chart_highest = input::ceiling($chart_highest, 1000000)*1.4;
        $chart_data = "";
        for ( $i = 6; $i > 0; $i-- )
        {
            $chart_data .= "<div class=\"item\">".round($chart_highest/6*$i/1000000, 0)." tr</div>
                        <div class=\"item\"></div>";
        }
        $report['chart_y'] = $chart_data;

        // Create ticks data
        $chart_highest = $chart_highest*1.16;
        $chart_data2 = "0";
        for ( $i = 1; $i <= 14; $i++ )
        {
            $chart_data2 .= ",".input::ceiling($chart_highest/14*$i, 100000);
        }
        $report['chart_stick'] = $chart_data2;

        return $report;
    }

    /**
     * @param int $type
     * + 1: day
     * + 2: week
     * + 3: month
     * + 4: year
     * @return array
     */
    static public function getTrxData($type = 2)
    {
        global $CMS, $DB, $tpl;

        $report = [];

        $chart = [];

        $type = intval(input::get('time_type')) ? intval(input::get('time_type')) : $type;

        $chartDateFormat = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];

        if($type == 4) //year
        {
            $chartDateFormat = str_replace(['d/','D/','/d','/D'],'',$chartDateFormat);
        }

        $sqlDateFormat = str_replace(['d','D','m','M','y','Y'],['%d','%D','%m','%M','%y','%Y'],$chartDateFormat);

        list($time_from, $time_to, $time_ago_from, $time_ago_to) = self::getTimeByType($type,1);

        //Backup input
        $input_bk = $CMS->input;

        $CMS->input = [];

        $CMS->input['date_from'] = date::format($time_from);
        $CMS->input['date_to'] = date::format($time_to);
        $CMS->input['type'] = 1;

        if(!empty($input_bk['mod_type']) && $input_bk['mod_type'] == 'revenue_costs')
        {
            $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(trx_time),'{$sqlDateFormat}') AS day, trx_type, SUM(trx_total) total FROM ".root_table."transaction WHERE trx_time BETWEEN {$time_from} AND {$time_to} AND trx_deleted=0 AND trx_subtype IN (2,3,6,7) GROUP BY day, trx_type";

            $sql = $DB->query($sql);

            $tmp = [];

            while($result = $DB->fetch_assoc($sql))
            {
                $day_income = $result['total']*1;
                $report['income'] = input::arrayValue($report, 'income') + $day_income;
                $tmp[$result['day']][$result['trx_type']] = input::arrayValue($tmp[$result['day']], $result['trx_type']) + $day_income;
            }

            $preDay = null;
            for($time = $time_from; $time <= $time_to; $time += 3600*24)
            {
                $day = date($chartDateFormat,$time);

                if($day != $preDay)
                {
                    $date_from = $day;
                    $date_to = $day;
                    $chart[] = [$day, input::arrayValue($tmp[$day],1)*1, input::arrayValue($tmp[$day],2)*1];
                    $preDay = $day;
                }
            }

            $report['chart'] = json_encode($chart);
        }
        else
        {

            $stats = $CMS->transactions->stats_default_page();

            $statsSort = [
                'unbill' => [
                    'color' => 'orange',
                    'data' => $stats['unbill']
                ],
                'estimate' => [
                    'color' => 'yellow',
                    'data' => $stats['estimate']
                ],
                'overdue' => [
                    'color' => 'red',
                    'data' => $stats['overdue']
                ],
                'invoice' => [
                    'color' => 'green',
                    'data' => $stats['invoice']
                ],
                'paid' => [
                    'color' => 'blue',
                    'data' => $stats['paid']
                ],
            ];

            $report['income'] = $stats['paid']['total'];
            $report['income_f'] = $stats['paid']['total_f'];
            $report['stats'] = $statsSort;
        }

        $CMS->input = $input_bk;

        return $report;
    }

    /**
     * @param int $type
     * + 1: day
     * + 2: week
     * + 3: month
     * + 4: year
     * @return array
     */
    static public function getOrdData($type = 2)
    {
        global $CMS, $DB, $tpl;

        $report = [];

        $chart = [];

        $type = intval($CMS->input['time_type']) ? intval($CMS->input['time_type']) : $type;

        $chartDateFormat = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];

        if($type == 4) //year
        {
            $chartDateFormat = str_replace(['d/','D/','/d','/D'],'',$chartDateFormat);
        }

        $sqlDateFormat = str_replace(['d','D','m','M','y','Y'],['%d','%D','%m','%M','%y','%Y'],$chartDateFormat);

        list($time_from, $time_to, $time_ago_from, $time_ago_to) = self::getTimeByType($type,1);

        //Backup input
        $input_bk = $CMS->input;

        $CMS->input = [];

        $CMS->input['date_from'] = date::format($time_from);
        $CMS->input['date_to'] = date::format($time_to);


        $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$sqlDateFormat}') AS day, SUM(ord_total) total FROM ".root_table."order WHERE ord_time BETWEEN {$time_from} AND {$time_to} AND ord_deleted=0 GROUP BY day";

        $sql = $DB->query($sql);

        $tmp = [];

        $report['income'] = 0;
        while($result = $DB->fetch_assoc($sql))
        {
            $day_income = $result['total']*1;
            $report['income'] += $day_income;
            $tmp[$result['day']] = input::arrayValue($tmp, $result['day'], 0)  + $day_income;
        }

        $preDay = null;
        for($time = $time_from; $time <= $time_to; $time += 3600*24)
        {
            $day = date($chartDateFormat,$time);

            if($day != $preDay)
            {
                $chart[] = [$day, input::arrayValue($tmp, $day)*1];
                $preDay = $day;
            }
        }

        $report['chart'] = json_encode($chart);
        $CMS->input = $input_bk;

        return $report;
    }

    /**
     *
     * @param int $type
     * @param string $type_block
     * @param string $field
     * @return mixed
     */
	
	static public function getDataReport($type = 0, $type_block = "", $field="")
	{
		global $CMS, $DB;

		$data = array();

		list($time_from, $time_to, $time_ago_from, $time_ago_to) = self::getTimeByType($type,1);

		if($time_from and $time_to)
		{
			$clause = " AND {$field} BETWEEN '{$time_from}' AND '{$time_to}' ";
		}

		if($time_ago_from and $time_ago_to)
		{
			$clause_sub = " AND {$field} BETWEEN '{$time_ago_from}' AND '{$time_ago_to}' ";
		}

		if($type_block == "customer")
		{
			$sql = $DB->query("SELECT 0 FROM ".root_table."customer WHERE cus_deleted = 0 {$clause} ORDER BY cus_id DESC");
			$data[$type_block]['number_new'] = $DB->num_rows($sql);

			$sql_sub = $DB->query("SELECT 0 FROM ".root_table."customer WHERE cus_deleted = 0 {$clause_sub} ORDER BY cus_id DESC");
			// Đếm số customer
			$data[$type_block]['number_old'] = $DB->num_rows($sql_sub);
			
			// print $data."<br/>".$data_sub;exit;
		}elseif($type_block == "order")
		{
			// Đếm số order
			$sql = $DB->query("SELECT COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_deleted = 0 {$clause}");
			$data[$type_block]['number_new'] = $DB->fetch_array($sql)['number_order'];

			$sql_sub = $DB->query("SELECT COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_deleted = 0 {$clause_sub}");
			$data[$type_block]['number_old'] = $DB->fetch_array($sql_sub)['number_order'];
			
			// print "<pre>";print_r($data[$type_block]);exit;
		}elseif($type_block == "revenue")
		{
			// Thu trên thực tế lấy theo trx_subtype 2,3 (Loại hoá đơn và Nhận thanh toán) và trx_status 2,3  (Đã hoàn thành và đóng)
			$sql = $DB->query("SELECT SUM(trx_total) as total FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (2,3) AND trx_status IN (2,3) {$clause}");
			$data[$type_block]['number_new'] = intval($DB->fetch_array($sql)['total']);

			$sql_sub = $DB->query("SELECT SUM(trx_total) as total FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (2,3) AND trx_status IN (2,3) {$clause_sub}");
			$data[$type_block]['number_old'] = intval($DB->fetch_array($sql_sub)['total']);
			
			// print "<pre>";print_r($data[$type_block]);exit;
		}elseif($type_block == "costs")
		{
			// Chi trên thực tế lấy theo trx_subtype 6,7 (Loại chi phí và biên lai chi) và trx_status 2,3  (Đã hoàn thành và đóng)
			$sql = $DB->query("SELECT SUM(trx_total) as total FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (6,7) AND trx_status IN (2,3) {$clause}");
			$data[$type_block]['number_new'] = intval($DB->fetch_array($sql)['total']);

			$sql_sub = $DB->query("SELECT SUM(trx_total) as total FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (6,7) AND trx_status IN (2,3) {$clause_sub}");
			$data[$type_block]['number_old'] = intval($DB->fetch_array($sql_sub)['total']);
			
			// print "<pre>";print_r($data[$type_block]);exit;
		}

		// So sánh dữ liệu
		$compare = $data[$type_block]['number_new'] - $data[$type_block]['number_old'];
		if($compare > 0)
		{
			$data[$type_block]['icon'] = "<div class='arrow up'></div>";	
			if($data[$type_block]['number_old'] == 0)
			{
				$data[$type_block]['percent'] = "100%";
			}else
			{
				$data[$type_block]['percent'] = round(($compare/$data[$type_block]['number_old'])*100)."%";
			}
		}else
		{
			$data[$type_block]['icon'] = "<div class='arrow down'></div>";
			// LHL, Except != 0 to avoid division by zero error ($data[$type_block]['number_old'] = 0)
			$data[$type_block]['percent'] = $data[$type_block]['number_old'] != 0 ? round((abs($compare)/$data[$type_block]['number_old'])*100)."%" : "0%";
		}

		// Convert dữ liệu
		if($type_block == "revenue" or $type_block == "costs")
		{
			$data[$type_block]['title_number'] = $CMS->class->input->currency($data[$type_block]['number_new']);
			$data[$type_block]['number_new'] = self::convertMoneytoString($data[$type_block]['number_new']);
		}

		// Caption report
		$data[$type_block]['caption'] = $CMS->lang['title_report_'.$type_block];
		// Return data
		return $data[$type_block];
	}

    static public function convertMoneytoString($input="")
	{
		global $CMS;
		// Input : interger
		if($input AND $CMS->vars['currency_type'] == "đ")
		{
			$input_bk = $input;
			$input = floatval(round($input));
			$dau = 1;
			$diff = 1;
			$dv = "đ";
			if($input < 0)
			{
				$dau = -1;
			}

			$length = strlen(abs($input));

			if($length > 9)
			{
				$dv = "Tỷ";
				$diff = 1000000000;
			}elseif($length > 6)
			{
				$dv = "Tr";
				$diff = 1000000;
			}

			if($dv === "đ")
			{
				$input_new = $CMS->class->input->number($input_bk,".", 2)." {$dv}";
			}else
			{
				$input_new = "<span style='font-size: 32px;'>~ </span>".$CMS->class->input->number($dau*$input/$diff,".", 2)." {$dv}";
			}

			return $input_new;
		}
		else
		{
			$input_new = $CMS->class->input->currency($input, 2);
		}
		return $input_new;
	}

    static public function getTimeByType($type = 0, $get_old = 0)
	{
        global $CMS;

        $dateFormat = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];

		$timezone = 0; //$CMS->vars['timezone']*3600
		if($type == 1)
		{
			// Theo ngày
			$time_from = strtotime(date($dateFormat))+$timezone;
			$time_to = $time_from + 3600*24;

			$time_ago_from = strtotime('yesterday')+$timezone;
			$time_ago_to = $time_ago_from + 3600*24;
		}elseif($type == 2)
		{
			// Theo tuần
			$time_from = strtotime('monday this week')+$timezone;
			$time_to = strtotime('sunday this week') + $timezone;

			$time_ago_from = strtotime('monday previous week')+$timezone;
			$time_ago_to = strtotime('sunday previous week') + $timezone;

		}elseif($type == 3)
		{
			// Theo tháng
			$time_from = strtotime('first day of this month')+$timezone;
			$time_to = strtotime('last day of this month') + $timezone;

			$time_ago_from = strtotime('first day of previous month')+$timezone;
			$time_ago_to = strtotime('last day of previous month') + $timezone;

		}elseif($type == 4)
		{
			// Theo năm
			$time_from = strtotime('first day of January '.date('Y'))+$timezone;
			$time_to = strtotime('last day of December '.date('Y')) + $timezone;

			$time_ago_from = strtotime('first day of January '.date('Y',strtotime('previous year')))+$timezone;
			$time_ago_to = strtotime('last day of December '.date('Y',strtotime('previous year'))) + $timezone;
		}else
		{
			// Tất cả
			$time_from = "";
			$time_to = "";

			$time_ago_from = "";
			$time_ago_to = "";
		}

		if($get_old)
		{
			return array($time_from, $time_to, $time_ago_from, $time_ago_to);
		}else
		{
			return array($time_from, $time_to);
		}
	}


    static public function getDataChart($type=0,  $type_block = "", $field="")
	{
		global $CMS, $DB;

		list($time_from, $time_to) = self::getTimeByType($type);

		if($time_from and $time_to)
		{
			$clause = " AND {$field} BETWEEN '{$time_from}' AND '{$time_to}' ";
		}


		if($type_block == "status_order")
		{
			$sql = $DB->query("SELECT ord_status, COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_deleted = 0 {$clause} GROUP BY ord_status");
			if($DB->num_rows($sql) > 0)
			{
				$str_chart = '';
				while ($result = $DB->fetch_array($sql)) 
				{
					if($result['ord_status'] == 0)
					{
						$status_name = $CMS->lang['title_status_0'];
					}elseif($result['ord_status'] == 1)
					{
						$status_name = $CMS->lang['title_status_1'];
					}elseif($result['ord_status'] == 2)
					{
						$status_name = $CMS->lang['title_status_2'];
					}
					 
					$str_chart .= "['{$status_name}' , {$result['number_order']}],";
				}
				$str_chart = "[".rtrim($str_chart,",")."]";
				
				$data['str_chart'] = $str_chart;
				$data['chart_title'] = $CMS->lang['title_status_order'];
				
			}else
			{
				
				$data['str_chart'] = "[]";
				$data['chart_title'] = $CMS->lang['title_status_order'];
			}

			return $data;

		}elseif($type_block == "revenue_costs")
		{
			if($type == 4)
			{
				$str_date = "%m%";
			}else
			{
				$str_date = "%d%-%m";
			}

			$sql_revenue = $DB->query("SELECT SUM(trx_total) as total, DATE_FORMAT(FROM_UNIXTIME(trx_time),'{$str_date}') AS datefm FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (2,3) AND trx_status IN (2,3) {$clause} GROUP BY datefm");
			$arr_chart = array();
			if($DB->num_rows($sql_revenue) > 0)
			{
				while ($result = $DB->fetch_array($sql_revenue)) 
				{
					$arr_chart[$result['datefm']] = $result['total'];
				}
			}
			
			$sql_costs = $DB->query("SELECT SUM(trx_total) as total, DATE_FORMAT(FROM_UNIXTIME(trx_time),'{$str_date}') AS datefm FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (6,7) AND trx_status IN (2,3) {$clause} GROUP BY datefm");
			$arr_chart2= array();
			if($DB->num_rows($sql_costs) > 0)
			{
				while ($result2 = $DB->fetch_array($sql_costs)) 
				{
					$arr_chart2[$result2['datefm']] = $result2['total'];
				}
			}

			$month = date("m");
			$year = date("Y"); 
			$number_day = intval(cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y")));
			$chart1 = array();
			$chart2 = array(); 
			$cat_date = array();
			$chart1[0] = "['{$CMS->lang['title_time']}', '{$CMS->lang['title_report_revenue']}', '{$CMS->lang['title_report_costs']}']"; // Khai bao
			for($x=1;$x<=$number_day;$x++)
			{
				$day = $x < 10 ? "0{$x}" : $x;
				$str_day = "{$day}-{$month}";//-{$year}

				if(isset($arr_chart2[$str_day]) or isset($arr_chart[$str_day]))
				{
					$value_1 = isset($arr_chart[$str_day]) ? round(intval($arr_chart[$str_day])/1000000, 2) : 0;
					$value_2 = isset($arr_chart2[$str_day]) ? round(intval($arr_chart2[$str_day])/1000000, 2) : 0;
					$chart1[] = "['{$str_day}', {$value_1}, {$value_2}]";
				}
			}
			if(count($chart1) == 1)
			{
				$day_crr = date("d-m");
				$chart1[1] = "['{$day_crr}',0,0]";
			}
			$data['str_chart'] = "[".implode($chart1, ',')."]"; 
			$data['caption'] = "{$CMS->lang['title_report_revenue']} - {$CMS->lang['title_report_costs']}";
			$data['caption_revenue'] = $CMS->lang['title_report_revenue'];
			$data['caption_costs'] = $CMS->lang['title_report_costs'];
			$data['caption_chart'] = $CMS->lang['title_report_chart']. date("m/Y");
			$data['title_unit_x'] = $CMS->lang['title_unit_chart'];
			return $data; 
		}
	}


    static public function getList($type=0, $type_block="", $field = "")
	{
		global $CMS, $DB;

		list($time_from, $time_to) = self::getTimeByType($type);

		if($time_from and $time_to)
		{
			$clause = " AND {$field} BETWEEN '{$time_from}' AND '{$time_to}' ";
		}
		$output = "";
		$data = array();
		if($type_block == "activity")
		{
			$sql = $DB->query("SELECT * FROM ".root_table."logs WHERE log_key NOT LIKE 'sms_%' ORDER BY log_id DESC LIMIT 10  ");

			if($DB->num_rows($sql) > 0)
			{
				while ($result = $DB->fetch_array($sql)) 
				{
					$data_user = $CMS->user->get_info($result['user_id']);
					// print_r($data_user);exit;
					if($CMS->permit['user_read'])
					{
						$link_user = "href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data_user['user_id']}'";
					}else
					{
						$link_user = "";
					}
					$time_due = self::convertTimedue($result['log_time']);
					if(isset($result['user_avatar']))
					{
						$link_image = "{$CMS->vars['upload_url']}/avatar/{$result['user_avatar']}";
					}else
					{
						$link_image = "{$CMS->vars['img_url']}/no-img.jpg";
					}
					$output .=<<<EOF
						<div class="widget-activity-item">
		                    <div class="user-card-row">
		                        <div class="tbl-row">
		                            <div class="tbl-cell tbl-cell-photo">
		                                <a {$link_user}>
		                                    <img src="{$link_image}" alt="{$data_user['user_display_name']}">
		                                </a>
		                            </div>
		                            <div class="tbl-cell">
		                                <p class="row-p">
		                                    <a {$link_user} class="semibold">{$data_user['user_display_name']}</a>
		                                    {$result['log_name']}
		                                </p>
		                                <p>{$time_due}</p>
		                            </div>
		                        </div>
		                    </div>
		                </div>
EOF;

				}
			}

			$data['caption'] = $CMS->lang['title_activity'];
			$data['output'] = $output;
			return $data;
		}elseif($type_block == "issues")
		{
			$sql = $DB->query("SELECT * FROM ".root_table."issues ORDER BY iss_id DESC LIMIT 10  ");
			if($DB->num_rows($sql) > 0)
			{
				$action =<<<EOF
							<div class="btn-group widget-menu">
                                <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="font-icon glyphicon glyphicon-option-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Separated link</a>
                                </div>
                            </div>

EOF;

				while ($result = $DB->fetch_array($sql)) 
				{
					$data_user = $CMS->user->get_info($result['user_id']);
					// print_r($data_user);exit;
					if($CMS->permit['user_read'])
					{
						$link_user = "href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data_user['user_id']}'";
					}else
					{
						$link_user = "";
					}
					$time_due = self::convertTimedue($result['iss_time']);
					if(isset($result['user_avatar']))
					{
						$link_image = "{$CMS->vars['upload_url']}/avatar/{$result['user_avatar']}";
					}else
					{
						$link_image = "{$CMS->vars['img_url']}/no-img.jpg";
					}
					
					// Convert status
					if($result['iss_status'] == 0)
					{
						$btn_status = "<span class='label label-danger' style='font-size: 11px;'>{$CMS->lang['title_status_0']}</span>";
					}elseif($result['iss_status'] == 1)
					{
						$btn_status = "<span class='label label-warning' style='font-size: 11px;'>{$CMS->lang['title_status_1']}</span>";
					}elseif($result['iss_status'] == 2)
					{
						$btn_status = "<span class='label label-success' style='font-size: 11px;'>{$CMS->lang['title_status_2']}</span>";
					}

					$output .=<<<EOF
						<div class="widget-tasks-item">
                            <div class="user-card-row">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-photo">
                                        <a {$link_user}>
                                            <img src="{$link_image}" alt="Avatar">
                                        </a>
                                    </div>
                                    <div class="tbl-cell">
                                        <p class="user-card-row-name" style="display: inline-block;font-size: 14px;"><a {$link_user}>{$data_user['user_display_name']}</a></p>
                                        <p class="row-p" style="display: inline-block;">{$result['iss_name']}</p>
                                        <p class="color-blue-grey-lighter">{$time_due} {$btn_status}</p>
                                    </div>

                                </div>
                            </div>
                        </div>
EOF;

				}
			}

			// Count issues
			$sql_count = $DB->query("SELECT iss_status, COUNT(iss_id) as number_issue FROM ".root_table."issues WHERE iss_status IN (0,1) GROUP BY iss_status");
			if($DB->num_rows($sql_count) > 0)
			{
				while ($result= $DB->fetch_array($sql_count)) 
				{
					if($result['iss_status'] == 0)
					{
						$data['status'][0] = " <span class='label label-pill label-danger' style='font-size: 11px;'>{$result['number_issue']}</span>";
					}elseif($result['iss_status'] == 1)
					{
						$data['status'][1] = " <span class='label label-pill label-warning' style='font-size: 11px;'>{$result['number_issue']}</span>";
					}
				}
			}
			$data['caption'] = $CMS->lang['title_issues'];
			$data['output'] = $output;
			return $data;
		}elseif($type_block == "order")
		{
			$sql = $DB->query("SELECT * FROM ".root_table."order WHERE ord_deleted=0 ORDER BY ord_id DESC LIMIT 10  ");
			if($DB->num_rows($sql) > 0)
			{
				while ($result = $DB->fetch_array($sql)) 
				{
					
					// Convert status
					if($result['ord_status'] == 0)
					{
						$btn_status = "<span class='label label-warning' style='font-size: 11px;'>{$CMS->lang['order_status_0']}</span>";
					}elseif($result['ord_status'] == 1)
					{
						$btn_status = "<span class='label label-default' style='font-size: 11px;'>{$CMS->lang['order_status_1']}</span>";
					}elseif($result['ord_status'] == 2)
					{
						$btn_status = "<span class='label label-success' style='font-size: 11px;'>{$CMS->lang['order_status_2']}</span>";
					}
                    elseif($result['ord_status'] == 3)
                    {
                        $btn_status = "<span class='label label-default' style='font-size: 11px;'>{$CMS->lang['order_status_3']}</span>";
                    }
                    elseif($result['ord_status'] == 4)
                    {
                        $btn_status = "<span class='label label-success' style='font-size: 11px;'>{$CMS->lang['order_status_4']}</span>";
                    }

					if($result['payment_status'] == 0)
					{
						$payment_status = "<span class='label label-danger' style='font-size: 11px;'>{$CMS->lang['title_payment_status_0']}</span>";
					}elseif($result['payment_status'] == 1)
					{
						$payment_status = "<span class='label label-success' style='font-size: 11px;'>{$CMS->lang['title_payment_status_1']}</span>";
					}elseif($result['payment_status'] == 2)
					{
						$payment_status = "<span class='label label-danger' style='font-size: 11px;'>{$CMS->lang['title_payment_status_2']}</span>";
					}

					$link_order = "{$CMS->vars['root_domain']}/?site=order&act=show&id={$result['ord_id']}";
					$order_total = $CMS->class->input->currency($result['ord_total']);
					$time_show = self::convertTimedue($result['ord_time']);
					$output .=<<<EOF
						<tr>
	                        <td>
	                            {$payment_status}
	                            {$btn_status}
	                        </td>
	                        <td><a href="{$link_order}">{$result['ord_name']}</a></td>
	                        <td align="center">{$order_total}</td>
	                        <td class="color-blue-grey" nowrap="" align="center">{$time_show}</td>
	                    </tr>
EOF;

				}

				$data['caption'] = $CMS->lang['title_recent_order'];
				$data['output'] = $output;

				return $data;
			}
		}elseif($type_block == "customer")
		{
			$sql = $DB->query("SELECT * FROM ".root_table."customer WHERE cus_deleted=0 ORDER BY cus_id DESC LIMIT 10  ");
			if($DB->num_rows($sql) > 0)
			{
				while ($result = $DB->fetch_array($sql)) 
				{

					$link_cus = "{$CMS->vars['root_domain']}/?site=customer&act=show&id={$result['cus_id']}";
					$number_order = $CMS->order->count_order($result['cus_id']);
					
					$link_avatar = "{$CMS->vars['img_url']}/no-img.jpg";
					$output .=<<<EOF
						<article class="contact-row">
	                        <div class="user-card-row">
	                            <div class="tbl-row">
	                                <div class="tbl-cell tbl-cell-photo">
	                                    <a href="{$link_cus}">
	                                        <img src="{$link_avatar}" alt="{$result['cus_full_name']}">
	                                    </a>
	                                </div>
	                                <div class="tbl-cell">
	                                    <p class="user-card-row-name"><a href="{$link_cus}">{$result['cus_full_name']}</a></p>
	                                    <p class="user-card-row-mail"><a href="maito:{$result['cus_email']}">{$result['cus_email']}</a></p>
	                                </div>
	                                <div class="tbl-cell tbl-cell-status">{$CMS->lang['number_order']}: {$number_order}</div>
	                            </div>
	                        </div>
	                    </article>
EOF;

				}

				$data['caption'] = $CMS->lang['title_recent_customer'];
				$data['output'] = $output;

				return $data;
			}
		}
	}

    static public function convertTimedue($time=0)
	{
		global $CMS;

		$curr_time = time();
		$text = "";
		$number_time = "";
		// Số ngày trong tháng
		$number_day_of_month = intval(cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y")));
		if($time)
		{
			if($curr_time - $time < 60 )
			{
				$number_time = "";
				$text = "A moment";
			}elseif($curr_time - $time < 3600 )
			{
				$number_time = ceil(($curr_time - $time)/60);
				$text = " minute ago";
			}elseif($curr_time - $time < 24*3600 )
			{
				$number_time = ceil(($curr_time - $time)/3600);
				$text = " hour ago";
			}elseif($curr_time - $time < 7*24*3600 )
			{
				$number_time = ceil(($curr_time - $time)/(24*3600));
				$text = " day ago";
			}elseif($curr_time - $time < $number_day_of_month*24*3600 )
			{
				$number_time = ceil(($curr_time - $time)/(7*24*3600));
				$text = " week ago";
			}elseif($curr_time - $time < 365*24*3600 )
			{
				$number_time = ceil(($curr_time - $time)/($number_day_of_month*24*3600));
				$text = " month ago";
			}else
			{
				$number_time = ceil(($curr_time - $time)/(365*24*3600));
				$text = " year ago";
			}
		}

		return $number_time.$text;
	}

    /**
     * Assets
     */

    static public function search_asset()
    {
        global $CMS;

        $key_search = $CMS->input['key_search'];
        $store_id = intval($CMS->input['store_id']);
        $supplier_id = intval($CMS->input['supplier_id']);
        // print $store_id;exit;
        $data = $CMS->assets->searchKey($key_search, $store_id, $supplier_id);

        if($data)
        {
            print json_encode(array('status' => 'success' , 'data' => $data));exit;
        }else
        {
            print json_encode(array('status' => 'error' , 'msg' => "{$CMS->lang['title_no_data']}"));exit;
        }
    }

    /**
     * Assets
     */

    static public function update_asset_to_list()
    {
        global $CMS;

        $item_id = intval($CMS->input['item_id']);
        $quantiy = intval($CMS->input['quantity']);
        $price = intval($CMS->input['price']);
        $tax = intval($CMS->input['tax']);

        $_SESSION['list_product'][$item_id]['ass_price'] = $price;
        $_SESSION['list_product'][$item_id]['ass_quantity'] = $quantiy;
        $_SESSION['list_product'][$item_id]['ass_amount'] =  round(($quantiy * $price) + ($quantiy * $price * $tax)/100);
        $_SESSION['list_product'][$item_id]['ass_tax'] = $tax;

        print 1;exit;
    }

    /**
     * Assets
     */

    static public function save_to_list_asset()
    {
        global $CMS;
// print "adfads" ;exit;
        //asset_id  as ass_key được truyền qua
        $ass_id = $CMS->input['asset_id'];

        // $CMS->assets->sql_add = " AND is_available = 1 ";
        $data = $CMS->assets->get_info($ass_id);
        // Create SESSION TEMP SAVE PRODUCT
        if(!$_SESSION['list_product'])
        {
            $_SESSION['list_product'] == array();
        }
// print "<pre>";
// print_r($data);exit;
        if($data)
        {
            $index = intval($CMS->input['item_id']);

            // Bên tài sản truy xuất thông tin theo key
            // $_SESSION['list_product'][$index]['ass_id'] = $data['ass_id'];
            $ass_price = $data['ass_price'] > 0 ? $data['ass_price'] : $data['ass_purchase_price'];
            $_SESSION['list_product'][$index]['ass_name'] = $data['ass_name'];
            $_SESSION['list_product'][$index]['ass_code'] = $data['ass_code'];
            $_SESSION['list_product'][$index]['ass_key'] = $data['ass_key'];

            $_SESSION['list_product'][$index]['ass_quantity'] = 1;
            $_SESSION['list_product'][$index]['ass_subitem'] = $CMS->assets->get_list_item($data['ass_id']);
            $_SESSION['list_product'][$index]['ass_amount'] =  round($ass_price + ($ass_price*$data['ass_tax'])/100);
            $_SESSION['list_product'][$index]['ass_tax'] = $data['ass_tax'];
            $_SESSION['list_product'][$index]['ass_price'] = $ass_price;
            // $_SESSION['list_product'][$index]['store_id'] = $data['store_id'];
            // $_SESSION['list_product'][$index]['ass_subitem'] = json_decode($data['ass_subitem'], true);
            // $_SESSION['list_product'][$index]['product_id'] = $data['product_id'];
            // $_SESSION['list_product'][$index]['supplier_id'] = $data['supplier_id'];
            // $_SESSION['list_product'][$index]['user_id'] = $data['user_id'];
            // print "<pre>";
            // print_r($_SESSION['list_product']);exit;
            print 1;exit;
        }else
        {
            print 0;exit;
        }
    }

    static function uploadImageSummernote()
    {
    	global $CMS, $DB;


    	


    }
    

    static function fileManagement()
    {
    	global $CMS, $DB;
    	$site_url = $CMS->vars['upload_dir']; //edit path
    	$link_url = $CMS->vars['upload_url'];
		$directory = "/attach/"; //edit path
		$images = glob($site_url.$directory.'*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

    	$output =<<<EOF
<style>
.thumb > span:after {
     content: "\f00e"; 
	font-family: FontAwesome;
    position: absolute;
	font-size:22px;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: all .6s;
    -webkit-transition: all .6s;
    background: rgba(0,0,0,0.7);
    color: #FFF;
    text-align: center;
    padding:45px 0;
}
.thumb > span:hover:after {
    opacity:1;
}
.thumb img {
	width:100%
}
.thumb {
	border:1px solid #aaa;
	padding:5px;
	margin-bottom:15px;
	position:relative;
	box-shadow: 0 1px 3px rgba(0,0,0,0.9);
	overflow:hidden	;
	height:120px
}
.thumb img {
    -webkit-transition: all .6s ease; /* Safari and Chrome */
  	-moz-transition: all .6s ease; /* Firefox */
  	-o-transition: all .6s ease; /* IE 9 */
  	-ms-transition: all .6s ease; /* Opera */
  	transition: all .6s ease;
}
.thumb:hover img {
    -webkit-transform:scale(1.25); /* Safari and Chrome */
    -moz-transform:scale(1.25); /* Firefox */
    -ms-transform:scale(1.25); /* IE 9 */
    -o-transform:scale(1.25); /* Opera */
     transform:scale(1.25);
}
body.modal-open { overflow: hidden!important; }
</style>

<div class="modal-dialog modal-lg"   style="overflow:initial">
	<div class="modal-content">
		<div class="btn-info modal-header">
		<h4 class="modal-title"><i class="fa fa-image"></i>&nbsp;&nbsp;Image manager
		<button type="button" data-toggle="tooltip" title="" id="button-upload" class="btn btn-primary pull-right"><i class="fa fa-upload"></i>&nbsp;&nbsp;UPLOAD IMAGE</button>
		</h4>
		</div>
		<div class="modal-body"  style="height:400px;overflow-y:auto">
EOF;

		$i=0;	
		// print "<pre>";
		// print_r($images);exit;	
		foreach ($images as $image) 
		{ 
			$image = basename($image);
			$image = $link_url.$directory.$image;
			$basename = basename($image);
	$output .=<<<EOF

		<div id="image_{$i}" style="margin:5px;float:left;width:155px;height:145px;">
        <div class="thumb" data-image="{$image}"><span><img class="pop" style="" src="{$image}" /></span></div>
		<div style="margin:-10px 0 10px 0" class="pull-right">
		<a data-toggle="tooltip" class="delete-image" data-image_id="{$i}" data-image="{$basename}" href="javascript:;" title="Delete image"><i class="fa fa-trash-o fa-lg"></i></a>
		&nbsp;&nbsp;<a data-toggle="tooltip" class="insert-image" data-image="{$image}" title="insert image" href="javascript:;"><i class="fa fa-sign-in fa-lg"></i></a>
		</div>
		</div>
EOF;

			$i++;
		}
$output .=<<<EOF

		</div>
		<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button></div>
	</div>
</div>

<!-- show image popup -->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <img src="" id="imagepreview" style="width:100%;" >
      </div>
      <p style="text-align:right;padding-right:20px">
        <button type="button" class="btn btn-default close-modal">Close</button>
      </p>
    </div>
  </div>
</div>

<!-- delete image popup -->
<div class="modal fade" id="imagemodaldelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
	<div class="btn-warning modal-header">
		<h4 class="modal-title"><i class="fa fa-trash-o"></i>&nbsp;&nbsp;Delete Image</h4>
		</div>
      <div class="modal-body">
        Are you sure you want to delete the image?
      </div>
      <p style="text-align:right;padding-right:20px">
        <button type="button" id="delete_image" class="btn btn-primary close-modal">Yes</button>&nbsp;<button type="button" class="btn btn-default close-modal">No</button>
      </p>
    </div>
  </div>
</div>


EOF;

		print $output;exit;    	
    }


    static function saveFile()
    {
    	global $CMS;

    	$file_tmp = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : "";
		$file_name = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : "";
		$file_type = isset($_FILES['file']['type']) ? $_FILES['file']['type'] : "";
		$file_size = isset($_FILES['file']['size']) ? $_FILES['file']['size'] : "";
		$file_error = isset($_FILES['file']['error']) ? $_FILES['file']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );

		// Check dung luong file upload
		$max = 18;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
			return false;
		}

		
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		$product_image = "";
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
				return false;
			}

			$CMS->class->image->check_folder_img("attach","",0);
			$imgPath = "{$CMS->vars['upload_dir']}/attach/{$file_location}";
			$check = @copy($file_tmp, $imgPath);
			if(!$check)
			{
				$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
				return false;
			}

            //Create thumb
            // foreach ($this->thumb_size as $keySize => $valSize)
            // {
            //     $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
            // }

			$url_img = "{$CMS->vars['upload_url']}/attach/{$file_location}";
			$return_data=array('error'=>0,'content'=>$url_img); //edit path
			print json_encode($return_data);exit;
		}else
		{
			print "";exit;
		}    	
    }

    static function delFile()
    {
    	global $CMS;

    	$img = $_POST['image'];
		if (file_exists( "{$CMS->vars['upload_dir']}/attach/".$img)) 
			unlink("{$CMS->vars['upload_dir']}/attach/".$img);
    }
	   
    
    static function renderHTMLMenu($key_permit = "", $menu_name="", $link="", $icon_code = "fa fa-circle-thin")
    {
        global $CMS;

        // Check theo key permit
        // Check is_root
        $output = "";
        if($CMS->vars['is_root'] == 1)
        {
            $output = "<li><a href='{$link}'><span class='lbl'><i class='{$icon_code}'></i>{$menu_name}</span></a></li>";
        }else
        {
            if($CMS->permit[$key_permit] == 1)
            {
                $output = "<li><a href='{$link}'><span class='lbl'><i class='{$icon_code}'></i>{$menu_name}</span></a></li>";
            }
        }

        return $output;
    }   


    static function change_color_theme($color="")
    {
        global $CMS, $DB;

        $DB->query("SELECT conf_value FROM ".root_table."conf_settings WHERE conf_key='color_theme' LIMIT 1");
        $check = count($DB->fetch_array());
        $color = $color ? $color : "default";
        // print $check;exit;
        if($check)
        {
            $DB->query("UPDATE ".root_table."conf_settings SET conf_value='{$color}' WHERE conf_key='color_theme'");
        }else
        {
            $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_group, conf_type) VALUES ('Color theme', 'color_theme', '{$color}', 1, 'input')");
        }

        // Delete config
        $CMS->class->cache->deletesql("config");
    }
}

?>