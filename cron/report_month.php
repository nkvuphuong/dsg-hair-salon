<?php

use \api\nexmo\sms;

// Prevent cronjob run
if ( !in_array($_SERVER['REMOTE_ADDR'], array("42.119.151.7")) )
{
    // ALways comment below line in production mode
    //return false;
}

// Init
include_once "../init.cron.php";

// Load Cron
$cron_report_month = $CMS->class->cache->load("cron_report_month");

// Continue 
if ( $cron_report_month > 0 AND $cron_report_month > (time() - 60 ) )
{
    echo "<PRE>report_month is being loaded.</PRE>\n";  
}
else
{


    
    $current_month = date('M, Y');
   
    $date_start = date('d-m-Y', strtotime('first day of this month'));
    $date_end = date('d-m-Y', strtotime('last day of this month'));
    
    $date_start_1 = date('Y-m-d', strtotime('first day of this month'));
    // Get info site
    $zd_contact_id = $CMS->vars['zd_contact_id'];
 
    $domain = $CMS->vars['root_domain']; 

    // 
 //echo $query_tickets = 'type:ticket subject:ID#'.$zd_contact_id.' created_at>'.$date_start_1;exit;
     // Get tickets
     if($zd_contact_id != "")
     {  
       $query_tickets = 'type:ticket subject:ID#'.$zd_contact_id.' created_at>'.$date_start_1;
        $res_tic = $CMS->api->zendesk->search_ticket($query_tickets);
        if($res_tic['status'] == "success")
        {
              $tickets =  (array) $res_tic['data'];
              $report_ticket = $tickets['count'];
        }
     }
     else
     {
        $report_ticket = 0;
     }
       $zendesk =<<<EOF
              <td valign="middle" align="left" class="MsoNormal" style="font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif; font-size:16px; color:#436fd1;line-height:20px;font-weight:bold;">
                                                    
                                                Zendesk tickets
                                                <tr>
                                                    <td>Tickets: {$report_ticket}</td>
                                                   
                                                </tr>                            
              </td>
EOF;
    // Get rating place
    $json = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid=ChIJ2euS9xqFbIcRuMbarlRUCWk&key=AIzaSyC-9nfJsHkqE7e-5UcyecnrMqgdpxsf38Q');
    $data = json_decode($json, true);
    $gg_place_reviews = count($data['result']['reviews']);
   // print_r ($data['result']['rating']);exit; //reviews
     $gg_place =<<<EOF
              <td valign="middle" align="left" class="MsoNormal" style="font-family:'Segoe UI',sans-serif,Arial, Helvetica, sans-serif; font-size:16px; color:#436fd1;line-height:20px;font-weight:bold">
                                                    
                                                Google Place
                                                <tr>
                                                    <td>Ratings: {$data['result']['rating']}</td>
                                                    <td>Reviews: {$gg_place_reviews}</td>
                                                   
                                                </tr>                            
              </td>
EOF;

    //
    $google_analytics = $CMS->report->google_getreport();
    $yelp_rp = $CMS->report->yelp_api();

   // $email_tpl = $CMS->emailtpl->get_info("report_marketing");
    $CMS->email->email_template = "report_marketing";
    $CMS->email->email_to = $customer['cus_email'];
    $CMS->email->email_toname = $customer['cus_full_name'];

    $CMS->email->email_cc = $user['user_email'];


    // email_from
    $CMS->email->data['img_logo_fb'] =  $CMS->vars['root_domain']."/acp/images/email/logo_fb2.png";
    $CMS->email->data['img_header_fb'] =  $CMS->vars['root_domain']."/acp/images/email/header_fb.jpg";
    $CMS->email->data['current_month'] =  $current_month;
    $CMS->email->data['date_start'] =  $date_start;
    $CMS->email->data['date_end'] =  $date_end;
    $CMS->email->data['account_id'] =  $CMS->vars['zd_contact_id'];
 
    $CMS->email->data['shop_name'] = $CMS->vars['website_title'];
    $CMS->email->data['domain_name'] = $domain;
    $CMS->email->data['zendesk_tickets'] = $zendesk;
    $CMS->email->data['google_place'] = $gg_place;
    $CMS->email->data['google_analytics'] = $google_analytics;
    $CMS->email->data['facebook_rp'] = $facebook_rp;
    $CMS->email->data['yelp_rp'] = $yelp_rp;
    
    $CMS->email->quick_send(0,0);
    echo "Done";exit;

    //===========================================================================
    //  END CRON
    //===========================================================================

    $CMS->class->cache->save("monitor_report_month", time());

    // Update Cron
    $CMS->class->cache->save("cron_report_month", 0);
}

?>