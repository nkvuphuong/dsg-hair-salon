<?php

use \models\server;
 
// Prevent cronjob run
if ( !in_array($_SERVER['REMOTE_ADDR'], array("42.119.151.7")) )
{
    // ALways comment below line in production mode
    //return false;
}

// Init
include_once "../init.cron.php";
include_once root_path."whm/models/server.php";
 
// Load Cron
$cron_server = $CMS->class->cache->load("cron_server");
 

//Continue
if ( $cron_server > 0 AND $cron_server > (time() - 60 ) )
{
    echo "<PRE>server is being loaded.</PRE>\n";
}
else
{
    // Load language
    $CMS->vars['is_admin_module']=true;
    $CMS->class->language->load("server");
 
    // Update Cron
    $CMS->class->cache->save("cron_server", time());

    print "Start load server<br />";
    flush();
 
    //===========================================================================
    //  START CRON
    //===========================================================================
    /**
     * Get Server : status : pending active - status code: 1  , site_created = 0
     */

    $sql_pending = $DB->query("SELECT * FROM ".root_table."server WHERE sv_active = 1 AND sv_deploy = 1  AND sv_deleted =0 LIMIT 1");
 
    print "Loaded server sv_deploy = 1  <br />";
    flush();
 
    if ( $DB->num_rows($sql_pending) == 0 )
    {
        print "Have no server to status : pending<br />";
    }
    else
    {   
    
        $sv_pending = $DB->fetch_array($sql_pending);

        print "Creating server : {$sv_pending['sv_id']} <br />";
        flush();
        // Update this server is creating
        $DB->query("UPDATE ".root_table."server SET sv_deploy = 4 WHERE sv_id='{$sv_pending['sv_id']}'");
         // Request Deploy server
    
        if(server::deploy($sv_pending) == true)
        {
            print "Deploy server : {$sv_pending['sv_id']} success!<br />";
        }   
        else
        {
            print "Deploy server : {$sv_pending['sv_id']} fail!<br />";
        }
 
       
    }
    
 

    //===========================================================================
    //  END CRON
    //===========================================================================
    print "End cron<br />";
    flush();
    $CMS->class->cache->save("monitor_cron_server", time());

    // Update Cron
    $CMS->class->cache->save("cron_server", 0);
}

?>