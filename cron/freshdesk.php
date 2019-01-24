<?php
// Prevent cronjob run
if ( !in_array($_SERVER['REMOTE_ADDR'], array("42.119.151.7")) )
{
    // ALways comment below line in production mode
    //return false;
}

// Init
include_once "../init.cron.php";
include_once "../kernel/api/freshdesk.php";

//Continue
if ( $cron_sites > 0 AND $cron_sites > (time() - 60 ) ) {
    echo "<PRE>freshdesk is being loaded.</PRE>\n";
} else {
	global $CMS;
	if ($CMS->vars['fd_sync_daily'] == 1) {
		$customers_to = $customers_from = $customers_fd = $customers_log_to = $customers_log_from =  array();
		$CMS->api->freshdesk->connect();
		$customers_fd = $CMS->api->freshdesk->contactsAll();
		foreach ($customers_fd as $customer_fd) {
			if (! $CMS->customer->cusCheckExist($customer_fd['email'])) {
				$data = array(
					'name' => $customer_fd['name'],
					'contact_email' => $customer_fd['email'],
					'address' => $customer_fd['address'],
					'phone' => $customer_fd['phone'],
				);
				if ($CMS->customer->social_add($data)) {
					$customers_log_from[] = $customer_fd['email'];
				}
			}
			$customers_from[] = $customer_fd['email'];
		}
		$customers_deleted = $CMS->api->freshdesk->contactsAllDeleted();
		foreach ($customers_deleted as $customer) {
			$CMS->customer->deleted($customer['email']);
		}
		$customers_to = $CMS->customer->getEmailNotInArray($customers_from);
		foreach ($customers_to as $customer) {
			$data['email'] = $customer;
			$data['name'] = $CMS->customer->getInfo($customer, 'cus_full_name');
			if (empty($CMS->api->freshdesk->contactsCreate($data)['code'])) {
				$customers_log_to[] = $customer;
			}
		}
		$CMS->class->logs->keep_content = 1;
		$CMS->class->logs->key = "freshdesk_sync_customer";
		$CMS->class->logs->insert("freshdesk_sync_customer", json_encode(array('form' => $customers_log_from, 'to' => $customers_log_to)));
		exit("Fresh Desk sync done");
	} else {
		exit("Fresh Desk not sync daily");
	}
}
?>