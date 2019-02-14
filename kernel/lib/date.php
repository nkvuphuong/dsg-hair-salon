<?php

namespace lib;

$CMS->class->date = new date;

class date {

	/**
	 * @param $today
	 */
	 
	public $today = "";
	
	/**
	 * @param $day
	 */
	 
	public $day = "";
	
	/**
	 * @param $month
	 */
	 
	public $month = "";
	
	/**
	 * @param $year
	 */
	 
	public $year = "";
	
	/**
	 * @param $theday
	 */
	 
	public $theday = "";
	
	/**
	 * @param $today
	 */
	 
	public $dayofweek = "";
	
	/**
	 * @param $now
	 */
	 
	public $now = "";

	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		global $CMS;
 
		$realtime = time();
		$this->today = date("d/m/Y", $realtime);
		
		$this->dayofmonth = date("t", $realtime); // Return 28-31
		$this->dayofweek = date("N", $realtime);
		$this->day = date("d", $realtime);
		$this->month = date("m", $realtime);
		$this->year = date("Y", $realtime);
		$this->cycle = $this->day > 15 ? 2 : 1;
		$this->theday = date("w", $realtime);

		$CMS->vars['today'] = $this->today;
		$CMS->vars['this_dayofweek'] = $this->dayofweek;
		$CMS->vars['this_day'] = $this->day;
		$CMS->vars['this_month'] = $this->month;
		$CMS->vars['this_year'] = $this->year;
	}

	public function date_format( $input, $type = 0 )
	{
		global $CMS, $DB;
		
		if ( $type == 1 )
		{
			// $type = $CMS->vars['long_time'];
			$subfix_hours = $CMS->vars['hours_time_format'] == 24 ? "A" : "a";
			$format_hours = $CMS->vars['hours_time_format'] == 24 ? "H" : "h";
			$type = $CMS->vars['dateformat_php'][$CMS->vars['date_format']]." {$format_hours}:i {$subfix_hours}";
		}else if ( !is_numeric($type) )
		{
			$type = $type;
		}
		else
		{
			$type = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];//$CMS->vars['short_time'];
		}
		// Remove ham gmdate thay bang date
	 
//		$output = date( $type, $input + $CMS->vars['timezone']*3600 );
		$output = date( $type, $input);

		return $output;
	}

    /**
     * LHL-27/04/2017
     * New version of $CMS->class->date->date_format()
     * Use: \lib\date::format()
     * @param $input
     * @param int $type
     * @return string
     */

	static public function format( $input, $type = "short" )
    {
        global $CMS;

        if ($type == "long") {
            $subfix_hours = $CMS->vars['hours_time_format'] == 24 ? "A" : "a";
			$format_hours = $CMS->vars['hours_time_format'] == 24 ? "H" : "h";
			$type = $CMS->vars['dateformat_php'][$CMS->vars['date_format']]." {$format_hours}:i {$subfix_hours}";
        } else if ( $type == "short") {
            $type = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];
        } else if ( $type == "full") {
            $type = $CMS->vars['dateformat_php'][$CMS->vars['date_format']] . " H:i:s";
        } else {
            $type = $type;
        }

//        $output = gmdate($type, $input + $CMS->vars['timezone'] * 3600);
        $output = date($type, $input);

        return $output;
    }
	
	public function gmt( $day, $month, $year, $removezone = 0 )
	{
		global $CMS, $DB;

 
//		$output = @mktime(0, 0, 0, $month, $day, $year ) + ($removezone == 0 ? $CMS->vars['timezone']*3600 : 0);
		$output = @mktime(0, 0, 0, $month, $day, $year );

		return $output;
	}
	
	public function date_get( $type, $time )
	{
		global $CMS;

		$data = date( $type, $time ); //  + $CMS->vars['timezone']*3600 
		
		return $data;
	}

    /**
     * Clone date_get function
     * @param $type
     * @param $time
     * @return false|string
     */
    static public function dateGet( $type, $time )
    {
        global $CMS;

        $data = date( $type, $time ); //  + $CMS->vars['timezone']*3600

        return $data;
    }

	/**
	 * @param $input
	 *		Date to time, dd/mm/yyyy -> timestamp
	 */
	
	public function date2time( $input, $remove_timezone = 0)
	{
		global $CMS;
		
		// Backup time zone
		if ( $remove_timezone == 1 )
		{
			$timezone = 0; //$CMS->vars['timezone'];
			$CMS->vars['timezone'] = 0;
		}
	 
		// Convert
		$input = str_replace("-", "/", $input);
		$input = explode("/", $input);
 
		$pos_date = explode(",", $CMS->vars['position_date_format'][$CMS->vars['date_format']]);

        $pos_date[0] = isset($pos_date[0]) ? $pos_date[0] : null;
        $pos_date[1] = isset($pos_date[1]) ? $pos_date[1] : null;
        $pos_date[2] = isset($pos_date[2]) ? $pos_date[2] : null;

        $input[0] = isset($input[0]) ? $input[0] : null;
        $input[1] = isset($input[1]) ? $input[1] : null;
        $input[2] = isset($input[2]) ? $input[2] : null;

		$day   = $pos_date[0] == 0 ? $input[0] : ($pos_date[1] == 0 ? $input[1] : $input[2]);
		$month = $pos_date[0] == 1 ? $input[0] : ($pos_date[1] == 1 ? $input[1] : $input[2]);
		$year  = $pos_date[0] == 2 ? $input[0] : ($pos_date[1] == 2 ? $input[1] : $input[2]);
 
		$output = $this->gmt($day, $month, $year);
	 
		// Restore time zone
		if ( $remove_timezone == 1 )
		{
			$CMS->vars['timezone'] = $timezone;
		}
		
		return $output;
	}

	public function clear_time( $input, $removezone = 0 )
	{
		global $CMS;
	
		// Backup time zone
		//$timezone = $CMS->vars['timezone'];
		//$CMS->vars['timezone'] = 0;
		
		// Convert
		$input = $this->date_format( $input, 0 );
		$input = explode("/", $input);

		$dateExplode = explode(',',$CMS->vars['position_date_format'][$CMS->vars['date_format']]) ;

		$tmp = [];
		foreach ($dateExplode as $replace => $find)
        {
            $tmp[$replace] = $input[$find];
        }

		$output = $this->gmt($tmp[0], $tmp[1], $tmp[2], $removezone);
		
		// Restore time zone
		//$CMS->vars['timezone'] = $timezone;

		return $output;
	}
	
	public function count( $input )
	{
		$time = intval($input);
	
		// Days
		$days = floor($time / (3600*24));
		
		// Hours
		$hours = $time;
		
		if ( $days > 0 )
		{
			$hours = $hours - $days*3600*24;
		}
	
		$hours = floor($hours / 3600);
		
		// Minutes
		$minutes = $time;
		
		if ( $days > 0 )
		{
			$minutes = $minutes - $days*3600*24;
		}
		
		if ( $hours > 0 )
		{
			$minutes = $minutes - $hours*3600;
		}
		
		$minutes = floor($minutes / 60);
		
		// Minutes
		$seconds = $time;
		
		if ( $days > 0 )
		{
			$seconds = $seconds - $days*3600*24;
		}
		
		if ( $hours > 0 )
		{
			$seconds = $seconds - $hours*3600;
		}
		
		if ( $minutes > 0 )
		{
			$seconds = $seconds - $minutes*60;
		}
		
		$count = array();
		$count[0] = $days;
		$count[1] = $hours;
		$count[2] = $minutes;
		$count[3] = $seconds;
		$count["d"] = $days;
		$count["h"] = $hours;
		$count["m"] = $minutes;
		$count["s"] = $seconds;
		
		return $count;
	}
	
	public function add_month( $input, $month = 0 )
	{
		if ( ! $month )
		{
			return $input;
		}
		
		$end_cycle = $this->date_format( $input, 0 );
		$end_cycle = explode("/", $end_cycle);
			
		$end_day = $end_cycle[0];
		$end_month = $end_cycle[1] + $month;
	
		// Check the value if the month more than 12
		if ( $end_month > 12 )
		{
			$sodu = floor($end_month / 12);
			$end_month = $end_month - ($sodu * 12 );					
			$end_year = $end_cycle[2] + $sodu;
		}
		else
		{
			$end_year = $end_cycle[2];
		}
					
		// Return the month to 2 numbers (x -> xx, ex: 2 -> 02)
		$end_month = (strlen( $end_month ) < 2) ? "0".$end_month : $end_month;	
		
		$output = $this->gmt($end_day, $end_month, $end_year);
		
		return $output;
	}
	
	/**
	 * @param $input
	 *		Timestamp
	 */
	
	public function add_hour( $input, $hour = 0 )
	{
		$output = $input + ($hour * 3600);
		
		return $output;
	}
	
	/**
	 * @param $input
	 *		Timestamp
	 */
	
	public function add_minute( $input, $minute = 0 )
	{
		$output = $input + ($minute * 60);
		
		return $output;
	}
	
	/**
	 * @param $get_dayofmonth
	 *		Timestamp
	 */
	
	public function get_dayofmonth( $month, $year )
	{
		$output = date("t", mktime(0, 0, 0, $month, 1, $year));
		
		return $output;
	}
	
	/**
	 * @param $get_dayofweek
	 *		Timestamp
	 */
	
	public function get_dayofweek( $day, $month, $year )
	{
		$output = date("N", mktime(0, 0, 0, $month, $day, $year));
		
		return $output;
	}
	
	/****************************************************************************
	* FUNCTION : time_since
	* $time_start : time begin ( in UNIX time )
	* return format time like:  1 minute and 5 seconds ago
	****************************************************************************/
	
	public function time_since($time_start)
	{
		global $CMS, $DB, $member;
		
		if ( ! $time_start )
		{
			return "<small>...</small>";
		}
		
		$now = time();
		$diff = $now - $time_start;
		
		switch (1) 
		{
			case ( $diff < 60 ):
				$count = $diff;
				if ( $count == 0 ) $count = $CMS->lang['timer_moment'];
				else if ( $count == 1 ) $suffix = $CMS->lang['timer_second'];
				else $suffix = $CMS->lang['timer_seconds'];
			break;
			
			case ( $diff > 60 && $diff < 3600 ):
				$count = floor($diff/60);
				if ( $count == 1 ) $suffix = $CMS->lang['timer_minute'];
				else $suffix = $CMS->lang['timer_minutes'];
			break;
			
			case ( $diff > 3600 && $diff < 86400 ):
				$count = floor($diff/3600);
				if ( $count == 1 ) $suffix = $CMS->lang['timer_hour'];
				else $suffix = $CMS->lang['timer_hours'];
			break;
			
			case ( $diff > 86400 && $diff < 2629743 ):
				$count = floor($diff/86400);
				if ( $count == 1 ) $suffix = $CMS->lang['timer_day'];
				else $suffix = $CMS->lang['timer_days'];
			break;
			
			case ( $diff > 2629743 && $diff < 31556926 ):
				$count = floor($diff/2629743);
				if ( $count == 1 ) $suffix = $CMS->lang['timer_month'];
				else $suffix = $CMS->lang['timer_month'];
			break;
			
			case ( $diff > 31556926 ):
				$count = floor($diff/31556926);
				if ( $count == 1 ) $suffix = $CMS->lang['timer_year'];
				else $suffix = $CMS->lang['timer_years'];
			break;
		}
		
		return $count." ".$suffix.$CMS->lang['timer_ago'];
	}
	
	//===========================================
	// TIMER IN SECOND (60 = 1 phút, 180 = 3 phút)
	//===========================================

    public function timer_second($time_start, $is_ago = 1)
    {
        return $this->convert_estimate_time_text($time_start, $is_ago, "text");
    }

    public function convert_to_days($time)
    {
        if($time > 0)
        {
            $days = $time /(60*60*24);

            return floor($days);
        }
    }

	public function normal_time_news($input = "", $type = 0, $style="/")
	{
		global $CMS;
		$str_search = array ( "Mon", 
					"Tue", 
					"Wed", 
					"Thu", 
					"Fri", 
					"Sat", 
					"Sun",
					"am", 
					"pm",
					":" );
					
		$str_replace = array ("Thứ hai", 
					"Thứ ba", 
					"Thứ tư",
					"Thứ năm", 
					"Thứ sáu",  
					"Thứ bảy", 
					"Chủ nhật", 
					"Sáng", 
					"Chiều",
					":" );
		
		
		if($type == 1)
		{
//			$output = gmdate("d{$style}m{$style}Y", $input + $CMS->vars['timezone']*3600);
			$output = gmdate("d{$style}m{$style}Y", $input);
		}
		else
		{
//			$time = gmdate("d{$style}m{$style}Y | H:i", $input + $CMS->vars['timezone']*3600);
			$time = gmdate("d{$style}m{$style}Y | H:i", $input);

			if($CMS->vars['default_language'] == "vn")
			{
				$output = str_replace($str_search, $str_replace, $time);
			}else
			{
				$output = $time;
			}
		}

		return $output;
	}

    /*
     * LHL - Estimate time
     * Example input: 3w 4d 12h
     */

    public function convert_estimate_time( $data = "" )
    {
        // Max length is 32
        if ( ! $data OR strlen($data)> 32 ) { return false; }

        // Init
        $week = 0;
        $day = 0;
        $hour = 0;
        $minute = 0;

        // Get week, day, hour
        $time = explode(" ", strtolower(trim($data)));

        for ( $i = 0; $i < count($time); $i++ )
        {
            $timevalue = substr($time[$i], 0, strlen($time[$i])-1);
            $timeunit = substr($time[$i], -1);

            if ( $timeunit == "w" ) {
                $week =  $timevalue;
            } elseif ( $timeunit == "d" ) {
                $day =  $timevalue;
            } elseif ( $timeunit == "h" ) {
                $hour =  $timevalue;
            } elseif ( $timeunit == "m" ) {
                $minute =  $timevalue;
            }
        }

        // Clear invalid info, convert them into absolute number
        $week = abs(intval($week));
        $day = abs(intval($day));
        $hour = abs(intval($hour));
        $minute = abs(intval($minute));

        // Check max value
        //$week = $week > 48 ? 48 : $week;
        //$day = $day > 365 ? 365 : $day;
        //$hour = $hour > 24 ? 24 : $hour;
        //$minute = $minute > 60 ? 60 : $minute;

        $data = ($week*7*3600*24) + ($day*3600*24) + ($hour*3600) + ($minute*60);

        //print $data;exit;

        return $data;
    }

    /*
     * Estimate time in text
     */

    public function convert_estimate_time_text($time_start, $is_ago = 1, $is_text = "text")
    {
        // Check for text or key
        if ( $is_text == "text" )
        {
            global $CMS;
        }
        else
        {
            $CMS->lang['timer_second'] = $CMS->lang['timer_seconds'] = "s";
            $CMS->lang['timer_minute'] = $CMS->lang['timer_minutes'] = "m";
            $CMS->lang['timer_hour'] = $CMS->lang['timer_hours'] = "h";
            $CMS->lang['timer_day'] = $CMS->lang['timer_days'] = "d";
            $CMS->lang['timer_week'] = $CMS->lang['timer_weeks'] = "w";
            $CMS->lang['timer_month'] = $CMS->lang['timer_months'] = "m";
            $CMS->lang['timer_year'] = $CMS->lang['timer_years'] = "y";
        }

        $diff = $time_start;

        // Use for create a string of time, ex: 1 hours 30 minutes
        $count_remain = 0;

        switch (1)
        {
            /*case ( $diff < 60 ):
                $count = $diff;
                if ( $count == 0 ) $count = $CMS->lang['timer_moment'];
                else if ( $count == 1 ) $suffix = $CMS->lang['timer_second'];
                else $suffix = $CMS->lang['timer_seconds'];
                break;
            */

            case ( $diff >= 60 && $diff < 3600 ):
                $count = floor($diff/60);
                $count_remain = $diff - ($count*60);
                if ( $count == 1 ) $suffix = $CMS->lang['timer_minute'];
                else $suffix = $CMS->lang['timer_minutes'];
                break;

            case ( $diff >= 3600 && $diff < 86400 ):
                $count = floor($diff/3600);
                $count_remain = $diff - ($count*3600);
                if ( $count == 1 ) $suffix = $CMS->lang['timer_hour'];
                else $suffix = $CMS->lang['timer_hours'];
                break;

            case ( $diff >= 86400 && $diff < 604800 ):
                $count = floor($diff/86400);
                $count_remain = $diff - ($count*86400);
                if ( $count == 1 ) $suffix = $CMS->lang['timer_day'];
                else $suffix = $CMS->lang['timer_days'];
                break;

            case ( $diff >= 604800 && $diff < 2629743 ):
                $count = floor($diff/604800);
                $count_remain = $diff - ($count*604800);
                if ( $count == 1 ) $suffix = $CMS->lang['timer_week'];
                else $suffix = $CMS->lang['timer_weeks'];
                break;

            case ( $diff >= 2629743 && $diff < 31556926 ):
                $count = floor($diff/2629743);
                $count_remain = $diff - ($count*2629743);
                if ( $count == 1 ) $suffix = $CMS->lang['timer_month'];
                else $suffix = $CMS->lang['timer_month'];
                break;

            case ( $diff >= 31556926 ):
                $count = floor($diff/31556926);
                $count_remain = $diff - ($count*31556926);
                if ( $count == 1 ) $suffix = $CMS->lang['timer_year'];
                else $suffix = $CMS->lang['timer_years'];
                break;
        }

        if ( $is_text == "text" )
        {
            // First value
            $result = $count." ".$suffix;

            // Continue convert to timer if value is more than 0
            $result .= ($count_remain > 0 ? $this->convert_estimate_time_text($count_remain, 0, $is_text) : "");

            // Output "ago" in suffix
            $result .= ($is_ago == 1 ? " ".$CMS->lang['timer_ago'] : "");
        }
        else{
            // First value
            $result = $count.$suffix." ";

            // Continue convert to timer if value is more than 0
            $result .= ($count_remain > 0 ? $this->convert_estimate_time_text($count_remain, 0, $is_text) : "");
        }

        return $result;
    }

    /**
     * @param string $type: unix_timestamp | timestamp
     * @return array
     */
	function getFisrtLastInCurrentWeek($type = 'unix_timestamp')
    {
        $start = strtotime('this week');
        $end = $start + (6*24*3600);

        if ($type == 'timestamp') {
            $start = $this->date_format($start);
            $end = $this->date_format($end);
        }

        return [$start, $end];
    }

    /**
     * @param string $type: unix_timestamp | timestamp
     * @return array
     */
    function getFisrtLastInCurrentMonth($type = 'unix_timestamp')
    {
        $start = strtotime(date('Y-m-01'));
        $end = strtotime(date('Y-m-t'));

        if ($type == 'timestamp') {
            $start = $this->date_format($start);
            $end = $this->date_format($end);
        }

        return [$start, $end];
    }

    /**
     * @param string $type: unix_timestamp | timestamp
     * @return array
     */
    function getFisrtLastInLastWeek($type = 'unix_timestamp')
    {
        $start = strtotime('last week');
        $end = $start + (6*24*3600);

        if ($type == 'timestamp') {
            $start = $this->date_format($start);
            $end = $this->date_format($end);
        }

        return [$start, $end];
    }

    /**
     * @param string $type: unix_timestamp | timestamp
     * @return array
     */
    function getFisrtLastInLastMonth($type = 'unix_timestamp')
    {
        $time = strtotime('last month');
        $start = strtotime(date('Y-m-01', $time));
        $end = strtotime(date('Y-m-t', $time));

        if ($type == 'timestamp') {
            $start = $this->date_format($start);
            $end = $this->date_format($end);
        }

        return [$start, $end];
    }

    /**
     * @param string $type: unix_timestamp | timestamp
     * @return array
     */
    function getFisrtLastInThisYear($type = 'unix_timestamp')
    {
        $time = strtotime('this year');
        $start = strtotime(date('Y-01-01', $time));
        $end = strtotime(date('Y-12-31', $time));

        if ($type == 'timestamp') {
            $start = $this->date_format($start);
            $end = $this->date_format($end);
        }

        return [$start, $end];
    }

    /**
     * @param string $type: unix_timestamp | timestamp
     * @return array
     */
    function getFisrtLastInLastYear($type = 'unix_timestamp')
    {
        $time = strtotime('last year');
        $start = strtotime(date('Y-01-01', $time));
        $end = strtotime(date('Y-12-31', $time));

        if ($type == 'timestamp') {
            $start = $this->date_format($start);
            $end = $this->date_format($end);
        }

        return [$start, $end];
    }

    static function getTimezone($timezone_id)
    {
        foreach(\DateTimeZone::listAbbreviations() as $items)
        {
            foreach($items as $item)
            {
               if($timezone_id == $item['timezone_id'])
               {
                   $offset = $item['offset']/3600;
                   $item['hour'] = floor($offset/1);
                   $item['min'] = round(($offset-$item['hour'])*60) ;
                   $item['timezone'] = $item['hour'] <0 ? "{$item['hour']}:{$item['min']}" : "+{$item['hour']}:{$item['min']}";
                   return $item;
               }
            }

        }
        return false;
    }

    static function getTimezoneByOffet($hours = 0, $min = 0)
    {
        foreach(\DateTimeZone::listAbbreviations() as $items)
        {
            foreach($items as $item)
            {
                $offset = $item['offset']/3600;
                $item['hour'] = floor($offset/1);
                $item['min'] = round(($offset-$item['hour'])*60);
$item['timezone'] = $item['hour'] <0 ? "{$item['hour']}:{$item['min']}" : "+{$item['hour']}:{$item['min']}";

                if($item['hour'] == $hours && $item['min'] == $min)
                {
                    return $item;
                }
            }

        }
        return false;
    }

    static function hour2Sec($time)
    {
        $arr = explode(":", $time);

        $h = trim($arr[0]) * 1;
        $m = trim($arr[1]) * 1;

        return (($h*60)+$m)*60;
    }

    static function sec2Hour($time)
    {
        $m = floor($time/60);
        $h = floor($m/60);
        $m = $m-($h*60);

        $h = substr("0$h", -2);
        $m = substr("0$m", -2);
        return "$h : $m";
    }

    /*
    * get the days ex: monday, tuesday ...
    */
    function timeToTheDay( $time = 0 )
    {
    	global $CMS;

    	$theday = date("w", $time);

    	$Titleday = [];
    	$Titleday['en'] = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    	$Titleday['vn'] = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
    	$Languageday = isset($Titleday[$CMS->vars['default_language']]) ? $CMS->vars['default_language'] : 'en';

    	return isset($Titleday[$Languageday][$theday]) ? $Titleday[$Languageday][$theday] : "";
    }

    function dateToTheDay( $date = 0 )
    {
    	return $this->timeToTheDay($this->date2time($date));
    }

    /**
     * Get Unix timestamp with date format
     * @author Vu Phuong
     * @since 2018-09-13
     * @see tests/kernel/lib/date/GetUnixTimestampTest.php
     * @param $date
     * @param $format
     * @return bool|false|int
     */
    static function getUnixTimestamp($date, $format)
    {
        if(!$date || !$format) {
            return false;
        }

        $dateParse = date_parse_from_format($format, $date);

        if($dateParse['error_count']) {
            return false;
        }

        //Convert to standard
        $year = $dateParse['year'] * 1;
        $month = substr("0" . $dateParse['month'] * 1, -2);
        $day = substr("0" . $dateParse['day'] * 1, -2);
        $hour = substr("0" . $dateParse['hour'] * 1, -2);
        $minute = substr("0" . $dateParse['minute'] * 1, -2);
        $second = substr("0" . $dateParse['second'] * 1, -2);

        $standard = "{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}";

        return strtotime($standard);
    }

    /**
     * Quick convert current format to another format
     * @author : Vu Phuong
     * @since : 2018-09-13
     * @see: tests/kernel/lib/date/ConvertFormatTest.php
     * @param $date: string
     * @param $currentFormat: string
     * @param $newFormat: string
     * @return false|string
     * @example :
     * date::convertFormat('20/09/1989', 'd/m/Y', 'Y-m-d') => '1989-09-20'
     * date::convertFormat('20/09/1989', 'd/m/Y', 'd-m-Y') => '20-09-1989'
     * date::convertFormat('20/09/1989', 'd/m/Y', 'd.m.Y') => '20.09.1989'
     * date::convertFormat('20.09.1989', 'd.m.Y', 'd/m/Y') => '20/09/1989'
     * date::convertFormat('20/09/1989 18:00', 'd/m/Y H:i', 'd-m-Y h:i a') => '20-09-1989 06:00 pm'
     * date::convertFormat('xxx', 'd/m/Y H:i', 'd-m-Y h:i a') => false
     * date::convertFormat(null, 'd/m/Y H:i', 'd-m-Y h:i a') => false
     * date::convertFormat('20/09/1989 18:00', null, 'd-m-Y h:i a') => false
     * date::convertFormat('20/09/1989 18:00', 'xxx', 'd-m-Y h:i a') => false
     * date::convertFormat('20/09/1989 18:00', 'd/m/Y H:i', null) => false
     */
    static function convertFormat($date, $currentFormat, $newFormat)
    {
        if(!$date || !$currentFormat || !$newFormat) return false;

        $timestamp = self::getUnixTimestamp($date, $currentFormat);

        if($timestamp === false) return false;

        $return = date($newFormat, $timestamp);
        return $return;
    }
}

?>