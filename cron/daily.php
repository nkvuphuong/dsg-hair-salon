<?php

// Prevent cronjob run
if ( !in_array($_SERVER['REMOTE_ADDR'], array("42.119.151.7")) )
{
    // ALways comment below line in production mode
    //return false;
}

// Init
include_once "../init.cron.php";

// Load Cron
$cron_daily = $CMS->class->cache->load("cron_daily");

//Continue
/*if ( $cron_daily > 0 AND $cron_daily > (time() - 60*60*24 ) )
{
    echo "<PRE>sites is being loaded.</PRE>\n";
}
else*/
{
    // Load language
    $CMS->class->language->load("_admin_sites");

    // Update Cron
    $CMS->class->cache->save("cron_daily", time());

    print "Start processing<br />";
    flush();

    //===========================================================================
    //  START CRON
    //===========================================================================

    // CHECK EXPIRE SITE
    if ( isset($DBS) ) {

        //$basicPackage = $CMS->subscription->getPackageList()[0];

        //$DBS->query(" UPDATE ".root_table."sites SET site_license_package='{$basicPackage['package_name']}' WHERE site_license_package!='{$basicPackage['package_name']}' AND site_deleted=0 AND site_license_expired <= ".time());
        //echo "Reset package ".$DBS->affected_rows()." site(s)<br />";
        //flush();
    }

    // Clear config
   // $DB->query("DELETE FROM `nh_cache` WHERE cache_name = 'config'");
    $time_ss =  time()-(30*24*3600);
    // Clear sessions
    $DB->query("DELETE FROM ".root_table."user_sessions WHERE session_time < '{$time_ss}' ");
 
    if(defined("is_web_us") == 1 )
    { 
        // Canh bao group slack content ve deadline nhạp content
        $time_expired = time() - (3*24*3600);// 
        $sql = $DB->query("SELECT * FROM ".root_table."sites WHERE  site_created = 1 AND site_regtype = 2 AND site_deleted = 0 AND change_domain_time = 0 AND site_editor_finish = 0 AND site_time <= '{$time_expired}'  ORDER BY site_id ASC ");

        $msg_content ="";
        if($DB->num_rows($sql) > 0)
        {
            while ($site = $DB->fetch_array($sql)) {
                # code... 
                $content = ""; 
                $content_staff = "";
                $content = $CMS->user->get_info($site['site_editor'], 'user_display_name');
                $site_start_time_bk = $site['site_start_time'] ? $CMS->class->date->date_format($site['site_start_time'],1) : 'N/A';
                if($content != "")
                {      
                    $content_staff = " Content: ".$content;
                }
                $msg_content .= "ID #{$site['site_id']}- CusID #{$site['cusweb_id']} - {$site['site_domainname']} - {$site['site_domain_extra']}. Date create: " .$site_start_time_bk . $content_staff ."\n";
            } 
            $CMS->api->slack->sendMessage_2("<!here>: [{$_SERVER['SERVER_NAME']}] DANH SÁCH WEBSITE CHƯA HOÀN THÀNH VIỆC NHẬP CONTENT. TEAM @CONTENT, VUI LÒNG KIỂM TRA LẠI TIẾN ĐỘ CÁC WEBSITE TRÊN!","{$msg_content}");
        }
    }
    

    if(defined("is_web_us") == 1 )
    { 
        // Canh bao group slack content ve deadline nhạp content
        $time_expired_change_domain = time() - (15*24*3600);// 
        $sql_2 = $DB->query("SELECT * FROM ".root_table."sites WHERE  site_created = 1 AND site_regtype = 2 AND site_deleted = 0 AND change_domain_time = 0 AND site_time <= '{$time_expired_change_domain}'  ORDER BY site_id ASC ");

        $msg_content ="";
        if($DB->num_rows($sql_2) > 0)
        {
            while ($site_2 = $DB->fetch_array($sql_2)) {
                # code... 
                $content = ""; 
                $content_staff = "";
                $content = $CMS->user->get_info($site_2['site_editor'], 'user_display_name');
                $site_start_time_bk = $site_2['site_start_time'] ? $CMS->class->date->date_format($site_2['site_start_time'],1) : 'N/A';
                if($content != "")
                {      
                    $content_staff = " Content: ".$content;
                }
                $msg_content_2 .= "ID #{$site_2['site_id']}- CusID #{$site_2['cusweb_id']} - {$site_2['site_domainname']} - {$site_2['site_domain_extra']}. Date create: " .$site_start_time_bk . $content_staff ."\n";
            } 
            $CMS->api->slack->sendMessage_2("<!here>: [{$_SERVER['SERVER_NAME']}] DANH SÁCH WEBSITE CHƯA CHUYỂN LÊN DOMAIN CHÍNH(SAU 15 NGÀY NHẬN WEBSITE). TEAM @CONTENT, VUI LÒNG KIỂM TRA LẠI TIẾN ĐỘ CÁC WEBSITE TRÊN!","{$msg_content_2}");
        }
    }

    //===========================================================================
    //  END CRON
    //===========================================================================

    $CMS->class->cache->save("monitor_cron_daily", time());

    // Update Cron
    $CMS->class->cache->save("cron_daily", 0);

    echo "End process";
}

?>