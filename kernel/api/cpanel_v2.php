<?php
$CMS->api->cpanel_v2 = new api_cpanel_v2;

class api_cpanel_v2
{
	public $server_info = array();
    
    public $is_ssl = 0;
    
    public $pass_is_hash = false;
    
    public $data_result = ""; // Use for debug
        
    public $url_request = ""; // Use for debug
    
    public $timeout = 0;
	public $cpanel; 

    public function execute($page = "")
    {
        global $CMS;
        require_once root_path."vendor/autoload.php";
        define("CPANEL_API_2", 2);
        $this->cpanel = new \Gufy\CpanelPhp\Cpanel([
            'host'        =>  "https://{$this->server_info['ip']}:2087",
            'username'    =>  "{$this->server_info['user']}",
            'auth_type'   =>  'hash', // there is also an option to use "hash"
            'password'    =>  "{$this->server_info['hash']}", // if you use hash, get the value from WHM's Remote Access Key if not use the root password here
        ]);
        return true;
    }

    public function create_email($param = "")
    {

        $this->execute();
        // Create the user@example.com email address.
 
        $cresult = $this->cpanel->execute_action(
        CPANEL_API_2, 'Email', 'addpop', $param['hosting_username'] ,
        array(
            'domain'          => $param['hosting_domain'] , 
            'email'           => $param['email_user'] , 
            'password'        => $param['email_password'] ,
             
        ));
        $cpanelresult = json_decode($cresult,true);
 
        if($cpanelresult['cpanelresult']['event']['result'] == 1)
        {
            $result['msg'] = "Create Email hosting success!";
            $result['status'] = 1;
            return $result;
        }
        else
        {
            $result['msg'] =  "Create Email hosting fail ." .$cpanelresult['cpanelresult']['event']['reason'];
            $result['status'] = 0;
            return $result;
        }
    }

    public function createdbuser($param = "")
    {

        $this->execute();
  
        $cresult = $this->cpanel->execute_action(
        CPANEL_API_2, 'MysqlFE', 'createdbuser', $param['hosting_username'] ,
        array(
            'dbuser'   => $param['db_username'],
            'password' => $param['db_password'],
        ));
        $cpanelresult = json_decode($cresult,true);
 
        if($cpanelresult['cpanelresult']['event']['result'] == 1)
        {
            // Create DB
            $db_return = $this->cpanel->execute_action(
            CPANEL_API_2, 'MysqlFE', 'createdb', $param['hosting_username'] ,
            array(
                'db'   => $param['db_name'] 
            ));

            $db_result = json_decode($db_return,true);
            if($db_result['cpanelresult']['event']['result'] == 1)
            {
             
                // This function grants privileges to a user on a database
                $pri_return = $this->cpanel->execute_action(
                CPANEL_API_2, 'MysqlFE', 'setdbuserprivileges',$param['hosting_username'] ,
                array(
                    'privileges' => 'ALL PRIVILEGES',
                    'db' =>  $param['db_name'] ,
                    'dbuser' => $param['db_username'],
                ));
                //
                $pri_result = json_decode($pri_return,true);
                if($pri_result['cpanelresult']['event']['result'] == 1)
                {
                    $result['msg'] = "Create DB and User success!";
                    $result['status'] = 1;
                    return $result;
                }
                else
                {
                    $result['msg'] = "Add user to DB faild!" .$pri_result['cpanelresult']['event']['reason'];
                    $result['status'] = 0;
                    return $result;
                }

            }
            else
            {
                $result['msg'] = "Create DB  faild!".$db_result['cpanelresult']['event']['reason'];
                $result['status'] = 0;
                return $result;
            }
        }
        else
        {
            $result['msg'] =  "Create User for DB  faild!"  .$cpanelresult['cpanelresult']['event']['reason'];
            $result['status'] = 0;
            return $result;
        }
     

      
    }


    public function add_cronjob($param = "")
    {

        $this->execute();
  
        $cresult = $this->cpanel->execute_action(
        CPANEL_API_2, 'Cron', 'add_line', $param['hosting_username'] ,
        array(
            'command'        => $param['command_cronjob'],
            'day'            => '*',
            'hour'           => '*',
            'minute'         => '*',
            'month'          => '*',
            'weekday'        => '*',
             
        ));
        $cpanelresult = json_decode($cresult,true);
  
        if($cpanelresult['cpanelresult']['event']['result'] == 1)
        {
            $result['msg'] = "Add cron jobs success!";
            $result['status'] = 1;
            return $result;
        }
        else
        {
            $result['msg'] =  "Add cron jobs fail ." .$cpanelresult['cpanelresult']['event']['reason'];
            $result['status'] = 0;
            return $result;
        }
    }


    public function parkdomain($param = "")
    {

        $this->execute();
        // Create the user@example.com email address.
 
        $cresult = $this->cpanel->execute_action(
        CPANEL_API_2, 'Park', 'park', $param['hosting_username'] ,
        array(
            'domain'          => $param['extra_domain'] ,        
        ));
        $cpanelresult = json_decode($cresult,true);
 
        if($cpanelresult['cpanelresult']['event']['result'] == 1)
        {
            $result['msg'] = "Create parks a domain success!";
            $result['status'] = 1;
            return $result;
        }
        else
        {
            $result['msg'] =  "Create E parks a domain fail ." .$cpanelresult['cpanelresult']['event']['reason'];
            $result['status'] = 0;
            return $result;
        }
    }


    public function listparkeddomains($param = "")
    {

        $this->execute();
        // Create the user@example.com email address.
 
        $cresult = $this->cpanel->execute_action(
        CPANEL_API_2, 'Park', 'listparkeddomains', $param['hosting_username'] ,
        array(
        ));
        $cpanelresult = json_decode($cresult,true);
 
        if($cpanelresult['cpanelresult']['event']['result'] == 1)
        {
            $result['msg'] = "Get list parks a domain success!";
            $result['status'] = 1;
            $result['data'] = $cpanelresult['cpanelresult']['data'];
            return $result;
        }
        else
        {
            $result['msg'] =  "Get list  parks a domain fail ." .$cpanelresult['cpanelresult']['event']['reason'];
            $result['status'] = 0;
            return $result;
        }
    }


    public function changepassword_hosting( $username = "", $old_password = "", $password = "")
    {

        $this->execute();
       // echo $username."--".$old_password."--".$password; 
        $cresult = $this->cpanel->execute_action(CPANEL_API_2, 'Passwd', 'change_password', $username,
        array(
            'user'   =>  $username,
            'oldpass' => $old_password,
            'newpass' => $password,
        ));
        $cpanelresult = json_decode($cresult,true);
 
        if($cpanelresult['cpanelresult']['event']['result'] == 1 AND $cpanelresult['cpanelresult']['data']['0']['status'] = 1)
        {
            $result['msg'] = "Change password hosting success!";
            $result['status'] = 1;
            return $result;

        }elseif($cpanelresult['cpanelresult']['event']['result'] == 1 AND $cpanelresult['cpanelresult']['data']['0']['status'] = 0)
        {
            $result['msg'] = $cpanelresult['cpanelresult']['data']['0']['statustxt'];
            $result['status'] = 0;
            return $result;

        }
        else
        {
            $result['msg'] = $cpanelresult['cpanelresult']['event']['reason'];
            $result['status'] = 0;
            return $result;
        }
     

      
    }




	 
}

?>