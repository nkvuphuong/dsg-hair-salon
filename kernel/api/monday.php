<?php

use allejo\DaPulse\Exceptions\ArgumentMismatchException;
use allejo\DaPulse\Exceptions\InvalidArraySizeException;
use allejo\DaPulse\Exceptions\InvalidColumnException;
use allejo\DaPulse\Exceptions\InvalidObjectException;
use allejo\DaPulse\Objects\PulseColumnStatusValue;
use allejo\DaPulse\Pulse;
use allejo\DaPulse\PulseBoard;
use allejo\DaPulse\PulseColumn;
use allejo\DaPulse\PulseGroup;
use allejo\DaPulse\PulseNote;


 

$CMS->api->monday = new api_monday;
class api_monday {

    public $api_key = '8d5b8dfa2c8c0e9ce88f171e98988c91';
    public $board_id = '104361622';
    public $user_id = 4178820;//ST
    public $user_id_sms = 4160475;//PL

    public function auto($query = "")
    {
        global $CMS;
        require_once root_path."vendor/autoload.php";
        $title = 'Task 18';
 
        PulseBoard::setApiKey($this->api_key);
        $value = PulseColumnStatusValue::Gold;
        $pulse=  new Pulse(106232937);
        $column = $pulse->getStatusColumn('status');

        $column->updateValue($value);
     
 
      
    }

  

    public function create_pulse($param = array())
    {
      //https://api.monday.com/v1/boards/109386379.json?api_key=8d5b8dfa2c8c0e9ce88f171e98988c91
 
        PulseBoard::setApiKey($this->api_key);
        $board = new PulseBoard($this->board_id);
        $pulse = $board->createPulse($param['title'], $this->user_id,null);
        
        if(isset($param['color_status']))
        {
          $column_status = $pulse->getStatusColumn('status');

          $column_status->updateValue($param['color_status']);
        }
        
        if(isset($param['whmcs_id']))
        {
          $column_wid = $pulse->getTextColumn('text');
 
          $column_wid->updateValue("{$param['whmcs_id']}");
        }

        if(isset($param['assignee_id']) AND $param['assignee_id'] != "")
        {
          $assignee_id = $param['assignee_id'] != "" ?  $param['assignee_id'] : $this->user_id;
          $column = $pulse->getPersonColumn('person');
          $column->updateValue($assignee_id);
        }

        if(isset($param['issue_type']) AND $param['issue_type'] != "" )
        {
          $column_type = $pulse->getStatusColumn('status7');

          $column_type->updateValue($param['issue_type']);
        }

        if(isset($param['issue_created']) AND $param['issue_created'] != "" )
        {
          $issue_created = $pulse->getDateColumn('date4');
          $timeSplit     = new \DateTime("{$param['issue_created']}");
          $issue_created->updateValue($timeSplit); 
        }
        
        if(isset($param['issue_updated']) AND $param['issue_updated'] != "" )
        {
          $issue_updated = $pulse->getDateColumn('date3');
          $timedate3     = new \DateTime("{$param['issue_updated']}");
          $issue_updated->updateValue($timedate3); 
        }
      
        if(isset($param['note_content']) AND $param['note_content'] != "" )
        {

          $pulse->createUpdate($assignee_id, "{$param['note_content']}");

        }

        if(isset($param['issue_description']) AND $param['issue_description'] != "" )
        {
           $pulse->addNote('Issue Description', $param['issue_description']);

        }

        $a = (array) $pulse;
        $pulse_output = $this->convert_array($a);
        
        if(isset($pulse_output['id']) AND $pulse_output['id'] != "")
        {
          $output['status'] = "success";
          $output['msg'] = "Create Pulse Success";
          $output['pulse_id'] = $pulse_output['id'];
          $output['board_id'] = $pulse_output['board_id'];
          
        }
        else
        {
          $output['status'] = "error";
          $output['msg'] = "Create Pulse Faild";
   
        }
        return $output;
 

    }

    public function create_pulse_survey($param = array())
    {
  
        $field_arr = array("1" => "text", "2" => "text0", "3" => "text2","4" => "text8" );
        PulseBoard::setApiKey($this->api_key);
        $board = new PulseBoard(109386379);
        $pulse = $board->createPulse($param['title'], $this->user_id_sms,null);
        
        if(isset($param['color_status']))
        {
          $column_status = $pulse->getStatusColumn('status7');

          $column_status->updateValue($param['color_status']);
        }
        
        if(isset($param['whmcs_id']))
        {
          $column_wid = $pulse->getTextColumn('text1');
 
          $column_wid->updateValue("{$param['whmcs_id']}");
        }
 
        if(isset($param['content']) AND count($param['content']) > 0)
        {
        
          for($i ==1; $i <= 4; $i++) {
            $key = $field_arr[$i];
            if(isset($param['content']['question_'.$i]) AND $param['content']['question_'.$i] != "" )
            {
              $column_type = $pulse->getTextColumn($key);

              $column_type->updateValue($param['content']['question_'.$i]);
            }
          }
        }
        if(isset($param['created']) AND $param['created'] != "" )
        {
          $issue_created = $pulse->getDateColumn('due_date');
          $timeSplit     = new \DateTime("{$param['created']}");
          $issue_created->updateValue($timeSplit); 
        }


        if(isset($param['cellphone']) AND $param['cellphone'] != "" )
        {
          $column_phone = $pulse->getTextColumn('text6');
          $column_phone->updateValue("{$param['cellphone']}");
        }

        if(isset($param['whmcs_username']) AND $param['whmcs_username'] != "" )
        {
          $column_whmcs_u = $pulse->getTextColumn('text28');
          $column_whmcs_u->updateValue("{$param['whmcs_username']}");
        }
    
        if(isset($param['answer_time']) AND $param['answer_time'] != "" )
        { 
          $answer_time = $pulse->getDateColumn('date');
          $timedate3     = new \DateTime("{$param['answer_time']}");
          $answer_time->updateValue($timedate3);  
        }

        if(isset($param['content']['morefeedback']) AND $param['content']['morefeedback'] != "" )
        {
           $pulse->addNote('More feedback', $param['content']['morefeedback']);
        }

        if(isset($param['content']['morefeedback']) AND $param['content']['morefeedback'] != "" )
        {
          $pulse->createUpdate($this->user_id_sms, "{$param['content']['morefeedback']}");
        }

 
        $a = (array) $pulse;
        $pulse_output = $this->convert_array($a);
        
        if(isset($pulse_output['id']) AND $pulse_output['id'] != "")
        {
          $output['status'] = "success";
          $output['msg'] = "Create Pulse Success";
          $output['pulse_id'] = $pulse_output['id'];
          $output['board_id'] = $pulse_output['board_id'];
          
        }
        else
        {
          $output['status'] = "error";
          $output['msg'] = "Create Pulse Faild";
   
        }
        return $output;
 
    }


    public function archive_pulse($pulse_id = "")
    {
      PulseBoard::setApiKey($this->api_key);

      $pulse = new Pulse($pulse_id);
      $pulse->archivePulse();
      $a = (array) $pulse;
      $pulse_output = $this->convert_array($a);
     
      if(isset($pulse_output['id']) AND $pulse_output['id'] != "")
      {
        $output['status'] = "success";
        $output['msg'] = "Archive Pulse Success";
        $output['pulse_id'] = $pulse_output['id'];
        $output['board_id'] = $pulse_output['board_id'];
        
      }
      else
      {
        $output['status'] = "error";
        $output['msg'] = "Archive Pulse Faild";
 
      }
      return $output;

    }

    public function update_pulse($param = array())
    {

        if(!isset($param['pulse_id'] ) AND $param['pulse_id'] =="")
        {
            $output['status'] = "error";
            $output['msg'] = "Pulse Not Found";
            return $output;
        }
        PulseBoard::setApiKey($this->api_key);
   
        $pulse=  new Pulse($param['pulse_id']);
        
        if($param['type_logs'] == "web_progress")
        {
          if(isset($param['note_content']) AND $param['note_content'] != "" )
          {
            $assignee_id = $param['assignee_id'] != "" ?  $param['assignee_id'] : $this->user_id;
            $pulse->createUpdate($assignee_id, "{$param['note_content']}");

          }

        }
        
        if($param['type_logs'] == "description")
        {
          if(isset($param['issue_description']) AND $param['issue_description'] != "" )
          {
              $pulse->addNote('Issue Description', $param['issue_description']);
          }
        }

        if($param['type_logs'] == "assignee")
        {
          if(isset($param['assignee_id']) AND $param['assignee_id'] != "")
          {
            $assignee_id = $param['assignee_id'] != "" ?  $param['assignee_id'] : $this->user_id;
            $column = $pulse->getPersonColumn('person');
            $column->updateValue($assignee_id);
          }
        }

        if(isset($param['color_status']))
        {
          $column_status = $pulse->getStatusColumn('status');
          $column_status->updateValue($param['color_status']);
        }

        if(isset($param['issue_updated']) AND $param['issue_updated'] != "" )
        { 
          $issue_updated = $pulse->getDateColumn('date3');
          $timedate3     = new \DateTime("{$param['issue_updated']}");
          $issue_updated->updateValue($timedate3);  
        }

        if(isset($param['issue_type']) AND $param['issue_type'] != "" )
        {
          $column_type = $pulse->getStatusColumn('status7');

          $column_type->updateValue($param['issue_type']);
        }

        $column_title = $pulse->editName("{$param['title']}");

        $a = (array) $pulse;
        $pulse_output = $this->convert_array($a);
 
        return $pulse_output;

    }


    public function update_pulse_survey($param = array())
    {
        $field_arr = array("1" => "text", "2" => "text0", "3" => "text2","4" => "text8" );
        if(!isset($param['pulse_id'] ) AND $param['pulse_id'] =="")
        {
            $output['status'] = "error";
            $output['msg'] = "Pulse Not Found";
            return $output;
        }
        PulseBoard::setApiKey($this->api_key);
   
        $pulse=  new Pulse($param['pulse_id']);
        
        

        if(isset($param['color_status']))
        {
          $column_status = $pulse->getStatusColumn('status7');

          $column_status->updateValue($param['color_status']);
        }
        
        if(isset($param['whmcs_id']))
        {
          $column_wid = $pulse->getTextColumn('text1');
 
          $column_wid->updateValue("{$param['whmcs_id']}");
        }
        

        if(isset($param['cellphone']) AND $param['cellphone'] != "" )
        {
          $column_phone = $pulse->getTextColumn('text6');
          $column_phone->updateValue("{$param['cellphone']}");
        }

        if(isset($param['whmcs_username']) AND $param['whmcs_username'] != "" )
        {
          $column_whmcs_u = $pulse->getTextColumn('text28');
          $column_whmcs_u->updateValue("{$param['whmcs_username']}");
        }
    
    
        if(isset($param['content']) AND count($param['content']) > 0)
        {
        
          for($i ==1; $i <= 4; $i++) {
            $key = $field_arr[$i];
            if(isset($param['content']['question_'.$i]) AND $param['content']['question_'.$i] != "" )
            {
              $column_type = $pulse->getTextColumn($key);

              $column_type->updateValue($param['content']['question_'.$i]);
            }
          }
        }
        
  
        // if(isset($param['content']['morefeedback']) AND $param['content']['morefeedback'] != "" )
        // {
        //    $pulse->addNote('More feedback', $param['content']['morefeedback']);
        // }

        if(isset($param['answer_time']) AND $param['answer_time'] != "" )
        { 
          $answer_time = $pulse->getDateColumn('date');
          $timedate3     = new \DateTime("{$param['answer_time']}");
          $answer_time->updateValue($timedate3);  
        }

        if(isset($param['content']['morefeedback']) AND $param['content']['morefeedback'] != "" )
        {
          $pulse->createUpdate($this->user_id_sms, "{$param['content']['morefeedback']}");
        }

        $a = (array) $pulse;
        $pulse_output = $this->convert_array($a);
 
        return $pulse_output;

    }

    public function comment_pulse($param = array())
    {
        if(!isset($param['pulse_id'] ) AND $param['pulse_id'] =="")
        {
            $output['status'] = "error";
            $output['msg'] = "Pulse Not Found";
            return $output;
        }
        PulseBoard::setApiKey($this->api_key);
   
        $pulse=  new Pulse($param['pulse_id']);
        

        if(isset($param['comment_content']) AND $param['comment_content'] != "" )
        {
          $comment_author_name = $param['comment_author_name'] != "" ?  $param['comment_author_name'] : $this->user_id;
          $pulse->createUpdate($comment_author_name, "{$param['comment_content']}");
        }
 
        $a = (array) $pulse;
        $pulse_output = $this->convert_array($a);
 
        return $pulse_output;

    }

    public function get_groups()
    {
       require_once root_path."vendor/autoload.php";
         PulseBoard::setApiKey($this->api_key);
         $boards = new PulseBoard($this->board_id);
         $a = $boards->getGroups(true);
         //$a = $boards->getGroups();
    
        // $boards = PulseBoard::getBoards();
 
         print_r ($a);exit;

    }
    public function get_board_info($board_id = "")
    {

       $ch = curl_init(); 
 
        curl_setopt($ch, CURLOPT_URL, "https://api.monday.com/v1/boards/".$board_id.".json/?api_key=".$this->api_key); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
       
        if (curl_errno($ch)) {
          $response= curl_error($ch);
        } else {
          
          $response= curl_exec($ch);
        }
        print_r (json_decode($response,true));exit;
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
}