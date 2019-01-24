<?php

use \api\nexmo\sms;

// Prevent cronjob run
if ( !in_array($_SERVER['REMOTE_ADDR'], array("42.119.151.7")) )
{
    // ALways comment below line in production mode
    //return false;
}
 
// Init, LHL-2017-09-29-Fixed for check alive module
$init_path = "../init.cron.php";
file_exists($init_path) ? include_once $init_path : "";

// Load Cron
$cron_sms = $CMS->class->cache->load("cron_sms");
 
// Continue	
if ( $cron_sms > 0 AND $cron_sms > (time() - 60 ) )
{
	echo "<PRE>SMS is being loaded.</PRE>\n";	
}
else
{
	// Load language
	$CMS->class->language->load("_admin_sms");
	
	// Update Cron
	$CMS->class->cache->save("cron_sms", time());
 
	print "Start load SMS<br />";
	flush();
	
	//===========================================================================
	//  START CRON
	//===========================================================================
	// Load for update
    $time_org  = 1508124146; // gửi lại các sms fail sau ngày 15/10

    $limit = isset($CMS->vars['cron_default_limit']) ? $CMS->vars['cron_default_limit'] : 1;
//    $limit = 1; //test
    // Sms pending và fail
    $sql = $DB->query("SELECT * FROM ".root_table."sms WHERE sms_deleted=0 AND ( sms_status=0 OR sms_status =2 ) AND sms_time >= '{$time_org}' ORDER BY sms_priority ASC, sms_id ASC LIMIT {$limit}");

    print "Loaded sms<br />";
    flush();

    if ( $DB->num_rows($sql) == 0 )
    {
        print "No more sms to send<br />";
        $CMS->class->cache->save("sms_updated", 0, 1);
    }
    else
    {
        print "Start sending sms...<br />";
        flush();

        while ( $sms = $DB->fetch_array($sql) )
        {
            //$sms['sms_from'] = "84922422456";
            //$sms['sms_to'] = "84922422456";

            $result = sms::send($sms['sms_from'], $sms['sms_to'], $sms['sms_content']);
            if ( $result['status'] == 'success' )
            {
                $DB->query("UPDATE ".root_table."sms SET sms_status=1, sms_api_response='{$result['response']}', sms_api_method='{$CMS->vars['sms_method']}' WHERE sms_id={$sms['sms_id']}");
            }
            else
            {
                $DB->query("UPDATE ".root_table."sms SET sms_status=2, sms_api_response='{$result['response']}', sms_api_method='{$CMS->vars['sms_method']}' WHERE sms_id={$sms['sms_id']}");
            }

            print "SMS has been sent: <strong>{$sms['sms_title']}</strong> ({$sms['sms_id']})<br />";
            flush();
        }
    }

	//===========================================================================
	//  END CRON
	//===========================================================================

	$CMS->class->cache->save("monitor_cron_sms", time());

	// Update Cron
	//$CMS->class->cache->save("cron_sms", 0);
}

?>