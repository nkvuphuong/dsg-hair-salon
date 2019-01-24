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
$cron_subscription = $CMS->class->cache->load("cron_subscription");

//Continue
if ( $cron_subscription > 0 AND $cron_subscription > (time() - 60 ) )
{
    echo "<PRE>subscription is being loaded.</PRE>\n";
}
else
{
    // Load language
    $CMS->class->language->load("_admin_subscription");

    // Update Cron
    $CMS->class->cache->save("cron_subscription", time());

    print "Start load subscription<br />";
    flush();

    //===========================================================================
    //  START CRON
    //===========================================================================
    // Load for update

    $limit = isset($CMS->vars['cron_default_limit']) ? $CMS->vars['cron_default_limit'] : 5;
    $sql = $DB->query("SELECT * FROM ".root_table."subscription WHERE sub_is_clone=0 AND payment_status!=0 LIMIT {$limit}");

    print "Loaded subscription<br />";
    flush();

    if ( $DB->num_rows($sql) == 0 )
    {
        print "Have no subscription to clone<br />";
        $CMS->class->cache->save("subscription_updated", 0, 1);
        exit;
    }
    else
    {

        //Lay danh sach cot trong table
        $cols = $DB->get_column_names("subscription");
        //Bo 2 cot sub_id, sub_is_clone
        $cols = array_diff($cols, ['sub_id', 'sub_is_clone']);

        $sql_insert = "INSERT INTO ".root_table."subscription (" . implode(',', $cols) . ") VALUES ";

        $sql_update = "UPDATE ".root_table."subscription SET sub_is_clone=1 WHERE sub_id IN ";

        $update_id = [];

        $msg = [];

        while ($data = $DB->fetch_assoc($sql))
        {
            $values = [];

            $update_id[] = $data['sub_id'];

            $msg[] = $data['sub_name'];

            foreach ($cols as $field)
            {
                $values[] = "'{$data[$field]}'";
            }

            $sql_insert .= '('.implode(',', $values).'),';
        }

        $sql_update .= '(' . implode(',', $update_id) . ')';

        $sql_insert = trim($sql_insert,',');

        //insert vào database system
        $DBS->query($sql_insert);

        //update lại trạng thái cho các record đã clone
        $DB->query($sql_update);

        echo "Cloned :<hr />" . implode('<br />', $msg);
    }

    //===========================================================================
    //  END CRON
    //===========================================================================

    $CMS->class->cache->save("monitor_cron_subscription", time());

    // Update Cron
    $CMS->class->cache->save("cron_subscription", 0);
}

?>