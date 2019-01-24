<?php
// Prevent cronjob run
if ( !in_array($_SERVER['REMOTE_ADDR'], array("42.119.151.7")) )
{
    // ALways comment below line in production mode
    //return false;
}

// Init
include_once("../init.cron.php");
include_once("../acp/models/fanpage.php");
include_once("../kernel/api/facebook.php");

global $CMS, $DB;
\models\fanpage::init();
$api = $CMS->api->facebook->serverConnection();
if ($api['status'] === true) {
	$api = \models\fanpage::getListFanpage();
	if ($api['status'] === true) {
		\models\fanpage::saveTokenFanpage($api['messages']);
		foreach ($api['messages'] as $id) {
			$CMS->input['page_id'] = $id['id'];
			\models\fanpage::fanpageMessage();
		}
		exit('Success! Get all the messages from facebook page inbox');
	}
}
var_dump($api);
?>