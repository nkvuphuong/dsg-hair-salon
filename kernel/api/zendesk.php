<?php

use Zendesk\API\HttpClient as ZendeskAPI;
$CMS->api->zendesk = new api_zendesk;
class api_zendesk {

    public function search_ticket($ticket_id = "")
    {
        global $CMS;

        //example of sending an sms using an API key / secret
        require_once root_path."vendor/autoload.php";


        
        $subdomain = $CMS->vars['zdesk_subdomain'];
        $username = $CMS->vars['zdesk_username'];
        $token = $CMS->vars['zdesk_token'];


        $client = new ZendeskAPI($subdomain);
        $client->setAuth('basic', ['username' => $username, 'token' => $token]);
    
       
      
        try {
          // Get all tickets
          //$tickets = $client->users()->findAll(['per_page' => 100, 'page' => 30]);
          //  $params = ['query=type:ticket' => 'status:open'];
           // $params = ['query=id:27965667867'];

          // $tickets = $client->search()->find($query); //'type:ticket subject:ID#2164 created_at>2017-04-11'
          $tickets = $client->tickets()->find($ticket_id);
          $data_tic = (array) $tickets;
          $data_tic = $this->object_to_array($data_tic);
        
          $tic =  (array) $data_tic['ticket'];
  
          //$tickets = $client->users()->find(27965667867);
          // Show the results
           return array("status" => "success", "data" => $tic);
        } catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
           return array("status" => "error", "data" => "");
        }
    }

    public function convert_array($arr = array())
    {
      $arr_output = array();
      foreach ($arr as $key => $value) {
        # code...
        $key = str_replace(" ", "", $key);
        $key = preg_replace('/\s+/', '', $key);
        $key = str_replace("*", "", $key);
        $key = trim($key);
        $arr_output[$key] = $value;
      }
      return $arr_output;
    }
    public function object_to_array($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->object_to_array($value);
            }
            return $result;
        }
        return $data;
    }

}