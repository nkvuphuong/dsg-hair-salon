<?php

use \models\sites;

// Prevent cronjob run
if ( !in_array($_SERVER['REMOTE_ADDR'], array("42.119.151.7")) )
{
    // ALways comment below line in production mode
    //return false;
}



// Init
include_once "../init.cron.php";
include_once root_path."whm/models/sites.php";
include_once root_path."whm/models/server.php";
// Load Cron
$cron_sites = $CMS->class->cache->load("cron_sites");
 

//Continue
if ( $cron_sites > 0 AND $cron_sites > (time() - 60 ) )
{
    echo "<PRE>sites is being loaded.</PRE>\n";
}
else
{
    // Load language
    $CMS->vars['is_admin_module']=true;
    $CMS->class->language->load("sites");
 
    // Update Cron
    $CMS->class->cache->save("cron_sites", time());
    //Delete hosting \
   

    print "Start load sites<br />";
    flush();

    //===========================================================================
    //  START CRON
    //===========================================================================
    /**
     * Get site : time expired dung thu
     */
    $time = time();
    $sql_pending = $DB->query("SELECT * FROM ".root_table."sites WHERE  site_created = 1 AND site_regtype = 1 AND site_license_expired <= '{$time}' AND is_suspend = 0  AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status= 1  <br />";
    flush();
 
    if ( $DB->num_rows($sql_pending) == 0 )
    {
        print "Have no sites to status : Time Expired<br />";
    }
    else
    {   
    
        $site_ex = $DB->fetch_array($sql_pending);
        print "Suspend  sites Expired: {$site_ex['site_id']} <br />";
        flush();
        $CMS->input['id'] = $site_ex['site_id'];
        if(sites::suspend() == true)
        {
            // Update time suspend
            $DB->query("UPDATE ".root_table."sites SET site_time_suspend = '{$time}', is_suspend = 1  WHERE site_id='{$site_ex['site_id']}'");

            print "Time Expired -> Suspend site  : {$site_ex['site_domainname']} success!<br />";
        }   
        else
        {
            print "Time Expired ->  Suspend site : {$site_ex['site_domainname']} fail!<br />";
        }
       
    }
    
   
    //Delete hosting \
    $time_delete = time() - (15 * 24 * 3600);
    $sql_delete= $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=5 AND is_suspend = 1     AND ( site_time_suspend <> 0 AND site_time_suspend <= '{$time_delete}' )  AND site_deleted = 0 LIMIT 1");
 
    print "Loaded sites delete hosting  <br />";
    flush();
 
    if ( $DB->num_rows($sql_delete) == 0 )
    {
        print "Have no sites to delete hosting <br />";
    }
    else
    {
        $site_delete = $DB->fetch_array($sql_delete);
   
        // Request delete hosting  domain site
        if(sites::delete_hosting($site_delete) == true)
        {
            $DB->query("UPDATE ".root_table."sites SET site_deleted = '1'  WHERE site_id='{$site_delete['site_id']}'");
            print "Delete hosting- domain site : {$site_delete['site_domainname']} success!<br />";
        }   
        else
        {
            print "Delete hosting- domain site : {$site_delete['site_domainname']} fail!<br />";
        }
    }


    //===========================================================================
    //  END CRON
    //===========================================================================
    print "End cron<br />";
    flush();
    $CMS->class->cache->save("monitor_cron_sites", time());

    // Update Cron
    $CMS->class->cache->save("cron_sites", 0);
}

?>