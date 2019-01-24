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

    print "Start load sites<br />";
    flush();
    
    //===========================================================================
    //  START CRON
    //===========================================================================
    /**
     * Get site : status : pending active - status code: 1  , site_created = 0
     */

    $sql_pending = $DB->query("SELECT * FROM ".root_table."sites WHERE site_status = 1 AND site_created = 0  AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status= 1  <br />";
    flush();
 
    if ( $DB->num_rows($sql_pending) == 0 )
    {
        print "Have no sites to status : pending<br />";
    }
    else
    {   
    
        $site_pending = $DB->fetch_array($sql_pending);
        print "Creating sites : {$site_pending['site_id']} <br />";
        flush();
        // Update this site is creating
        $DB->query("UPDATE ".root_table."sites SET site_created = 2 WHERE site_id='{$site_pending['site_id']}'");
         // Request Active site
    
        if(sites::active($site_pending) == true)
        {
            print "Active site : {$site['site_domainname']} success!<br />";
        }   
        else
        {
            print "Active site : {$site['site_domainname']} fail!<br />";
        }
 
       
    }
    
    
     /**
     * Get site : status : pending active - status code: 1 0
     */

    $sql_change_domain = $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=10 AND site_created = 1  AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status=10  <br />";
    flush();
 
    if ( $DB->num_rows($sql_change_domain) == 0 )
    {
        print "Have no sites to change_domain<br />";
    }
    else
    {

        $site_change_domain = $DB->fetch_array($sql_change_domain);
        // Update this site is rsyncing
        $DB->query("UPDATE ".root_table."sites SET site_created = 2 WHERE site_id='{$site_change_domain['site_id']}'");

        // Request Change domain site
        if(sites::change_domain($site_change_domain) == true)
        {
            print "Change domain site : {$site_change_domain['site_domainname']} success!<br />";
        }   
        else
        {
            print "Change domain site : {$site_change_domain['site_domainname']} fail!<br />";
        }
    }

    /**
     * Get site : status : change theme active - status code: 14
     */
 
    $sql_change_theme = $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=14 AND site_created = 1  AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status=14  <br />";
    flush();
 
    if ( $DB->num_rows($sql_change_theme) == 0 )
    {
        print "Have no sites to change_theme<br />";
    }
    else
    {

        $site_change_theme = $DB->fetch_array($sql_change_theme);
        // Update this site is rsyncing
        $DB->query("UPDATE ".root_table."sites SET site_created = 2 WHERE site_id='{$site_change_theme['site_id']}'");

        // Request Change domain site
        if(sites::change_theme($site_change_theme) == true)
        {
            print "Change theme site : {$site_change_theme['site_domainname']} success!<br />";
        }   
        else
        {
            print "Change theme site : {$site_change_theme['site_domainname']} fail!<br />";
        }
    }
 
     /**
     * Get site : status : change pass acp - status code: 16
     */

    $sql_change_pass = $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=16 AND site_created = 1  AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status=16  <br />";
    flush();
 
    if ( $DB->num_rows($sql_change_pass) == 0 )
    {
        print "Have no sites to change_pass ACP<br />";
    }
    else
    {

        $site_change_pass = $DB->fetch_array($sql_change_pass);
        

        // Request Change domain site
        if(sites::change_password($site_change_pass) == true)
        {
            print "Change pass ACP site : {$site_change_pass['site_domainname']} success!<br />";
        }   
        else
        {
            print "Change  pass ACP site : {$site_change_pass['site_domainname']} fail!<br />";
        }
    }


 
     /**
     * Get site : status : pending Rsync - status code: 12
     */

    $sql_rsync_domain = $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=12 AND site_created = 1  AND site_deleted =0 AND is_suspend = 0 LIMIT 3");
 
    print "Loaded sites status=12  <br />";
    flush();
 
    if ( $DB->num_rows($sql_rsync_domain) == 0 )
    {
        print "Have no sites to Rsync domain<br />";
    }
    else
    {
        $site_rsync_domain = $DB->fetch_array($sql_rsync_domain);
   
        // Request Rsync domain site
        if(sites::rsync($site_rsync_domain) == true)
        {
            print "Rsync domain site : {$site_rsync_domain['site_domainname']} success!<br />";
        }   
        else
        {
            print "Rsync domain site : {$site_rsync_domain['site_domainname']} fail!<br />";
        }
    }

 
     /**
     * Get site : status : pending Production - status code: 18
     */

    $sql_pro_domain = $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=18 AND site_created = 1  AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status= 18  <br />";
    flush();
 
    if ( $DB->num_rows($sql_pro_domain) == 0 )
    {
        print "Have no sites to Change debug mode Production<br />";
    }
    else
    {
        $site_pro = $DB->fetch_array($sql_pro_domain);
   
        // Request Chagne mode production domain site
        if(sites::mode_production($site_pro) == true)
        {
            print "Change debug mode Production domain site : {$site_pro['site_domainname']} success!<br />";
        }   
        else
        {
            print "Change debug mode Production domain site : {$site_pro['site_domainname']} fail!<br />";
        }
    }

 
     /**
     * Get site : status : pending Development - status code: 20
     */

    $sql_dev_domain = $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=20 AND site_created = 1  AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status= 20  <br />";
    flush();
 
    if ( $DB->num_rows($sql_dev_domain) == 0 )
    {
        print "Have no sites to Change debug mode Development<br />";
    }
    else
    {
        $site_dev = $DB->fetch_array($sql_dev_domain);
   
        // Request Chagne mode development domain site
        if(sites::mode_development($site_dev) == true)
        {
            print "Change debug mode Development domain site : {$site_dev['site_domainname']} success!<br />";
        }   
        else
        {
            print "Change debug mode Development domain site : {$site_dev['site_domainname']} fail!<br />";
        }
    }
 
   
    /**
     * Get site : status : pending move server real- status code: 22
     */

    $sql_move= $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=22 AND site_created = 1 AND site_moved = 0 AND site_deleted =0 LIMIT 1");
 
    print "Loaded sites status= 22  <br />";
    flush();
 
    if ( $DB->num_rows($sql_move) == 0 )
    {
        print "Have no sites to move server real<br />";
    }
    else
    {
        $site_move = $DB->fetch_array($sql_move);
        // Update this site is rsyncing
        $DB->query("UPDATE ".root_table."sites SET site_moved = 2 WHERE site_id='{$site_move['site_id']}'");


        // Request Chagne mode development domain site
        if(sites::move($site_move) == true)
        {
            print "Move server real- domain site : {$site_move['site_domainname']} success!<br />";
        }   
        else
        {
            print "Move server real domain site : {$site_move['site_domainname']} fail!<br />";
        }
    }

   
    //Delete hosting \
    // $time_delete = time - (15 * 24 * 3600);
    // $sql_delete= $DB->query("SELECT * FROM ".root_table."sites WHERE site_status=2 AND site_created = 1 AND site_moved = 1  AND site_deleted =0  AND ( site_time_moved <> 0 AND site_time_moved <= '{$time_delete}' ) LIMIT 1");
 
    // print "Loaded sites delete hosting  <br />";
    // flush();
 
    // if ( $DB->num_rows($sql_delete) == 0 )
    // {
    //     print "Have no sites to delete hosting <br />";
    // }
    // else
    // {
    //     $site_delete = $DB->fetch_array($sql_delete);
   
    //     // Request delete hosting  domain site
    //     if(sites::delete_hosting($site_delete) == true)
    //     {
    //         print "Delete hosting- domain site : {$site_delete['site_domainname']} success!<br />";
    //     }   
    //     else
    //     {
    //         print "Delete hosting- domain site : {$site_delete['site_domainname']} fail!<br />";
    //     }
    // }



    /**
     * Get site : status : created for update https domain
     */
    // $n_time = time() - (2 * 24 * 3600);

    // $sql_ssl= $DB->query("SELECT * FROM ".root_table."sites WHERE  site_created = 1 AND site_time <= '{$n_time}' AND site_deleted =0 AND is_ssl = 0 AND is_suspend=0  LIMIT 1");
 
    // print "Loaded sites status= 2 - update ssl  <br />";
    // flush();
 
    // if ( $DB->num_rows($sql_ssl) == 0 )
    // {
    //     print "Have no sites to update ssl<br />";
    // }
    // else
    // {
    //     $site_ssl = $DB->fetch_array($sql_ssl);
        

    //     // Request Chagne mode development domain site
    //     if(sites::act_rsync_cron($site_ssl,1) == true)
    //     {
    //         print "Update sync web has ssl : {$site_ssl['site_domainname']} success!<br />";
    //     }   
    //     else
    //     {
    //         print "Update sync web has ssl : {$site_ssl['site_domainname']} fail!<br />";
    //     }
    // }


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