<?php

// Init, LHL-2017-09-29-Fixed for check alive module
$init_path = "../init.cron.php";
file_exists($init_path) ? include_once $init_path : "";

// Load Cron
$cron_email = $CMS->class->cache->load("cron_email");

// Continue
if ( $cron_email > 0 AND $cron_email > (time() - 120 ) )
{
	echo "<PRE>Email is being loaded.</PRE>\n";	
}
else
{
	// Load language
	$CMS->class->language->load("_admin_email");
	
	// Update Cron
	$CMS->class->cache->save("cron_email", time());
	
	print "Start load email<br />";
	flush();
	
	//===========================================================================
	//  START CRON
	//===========================================================================
	// Load for update

    $limit = isset($CMS->vars['cron_default_limit']) ? $CMS->vars['cron_default_limit'] : 10;
    $sql = $DB->query("SELECT * FROM ".root_table."email WHERE email_deleted=0 AND email_status=0 ORDER BY email_priority ASC, email_id ASC LIMIT {$limit}");
    print "Loaded email<br />";
    flush();

    if ( $DB->num_rows($sql) == 0 )
    {
        print "No more email to send<br />";
        $CMS->class->cache->save("email_updated", 0, 1);
    }
    else
    {
        print "Start sending email...<br />";
        flush();

        while ( $email = $DB->fetch_array($sql) )
        {
            if ( $CMS->class->mail->sendmail($email['email_to'], $email['email_toname'], $email['email_from'], $email['email_fromname'], $email['email_title'], $email['email_content'], $email['email_cc'], $email['email_bcc']) )
            {
                $DB->query("UPDATE ".root_table."email SET email_status=1 WHERE email_id={$email['email_id']}");
            }
            else
            {
                $DB->query("UPDATE ".root_table."email SET email_status=2 WHERE email_id={$email['email_id']}");
            }

            print "Email has been sent: <strong>{$email['email_title']}</strong> ({$email['email_id']})<br />";
            flush();
        }
    }

	//===========================================================================
	//  END CRON
	//===========================================================================

	$CMS->class->cache->save("monitor_cron_email", time());

	// Update Cron
	$CMS->class->cache->save("cron_email", 0);
}

?>