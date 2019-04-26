<?php
if (!defined('IN_ROOT')) exit();
//tesst sync 001
$CMS->store = new ModStore;
class ModStore{
    public $show_page= "";
    public $CMS = "";
    public $sql_query = "";
    public $sql_query_bk = "";
    public $arrange_data = "";
    public $record_cnt = 0;
    public $control = 0;
    public $total = 0;
    public $sql_add = "";
    public $action_control = "";
    public $news_project = "";
    public $data_array = array();
    public $per_page = 10;
    public $prefix_html = "";
    public $suffix_html = "";
    public $cache_prefix = 'store';

    public $html;


    public function acp_add()
    {
        global $CMS, $DB, $member;

        $store_name = trim($CMS->input['store_name']);
        $store_phone = trim($CMS->input['store_phone']);
        $store_address = trim($CMS->input['store_address']);
        $store_slogan = trim($CMS->input['store_slogan']);
        $store_type = intval($CMS->input['store_type']);
        $city_id = intval($CMS->input['city_id']);
        $googlemap_code = $CMS->input['googlemap_code'];
        $user_id = $member['user_id'];
        $store_time = time();

        $store_backend_url = trim($CMS->input['store_backend_url']);
        $store_backend_type = trim($CMS->input['store_backend_type']);
        $store_backend_key = trim($CMS->input['store_backend_key']);
        $store_backend_secret = trim($CMS->input['store_backend_secret']);
        $store_backend_sync = intval($CMS->input['store_backend_sync']);
        $store_display = intval($CMS->input['store_display']);

        if($store_backend_sync)
        {
            if(!$store_backend_key) { $_SESSION['msg'] .= $CMS->errormsg .= $CMS->lang['invalid_key']; return false;}
            if(!$store_backend_secret) { $_SESSION['msg'] .= $CMS->errormsg .= $CMS->lang['invalid_secret']; return false;}
        }

        if(!$store_name)
        {
            $_SESSION['msg'] .= $CMS->errormsg .= $CMS->lang['store_empty_name'];
            return false;
        }

        // Check input
        /*if (!$store_type)
        {
            $_SESSION['msg'] .= $CMS->errormsg .= $CMS->lang['store_empty_type'];
            return false;
        }*/

        // Check exist
        if($this->check_exist("store_name",$store_name))
        {
            $_SESSION['msg'] .= $CMS->errormsg .= $CMS->lang['store_is_exist'];
            return false;
        }

        //Upload file.
        if ($_FILES['store_avatar']) {
            $uploadImg = $CMS->class->image->uploadImage($_FILES['store_avatar'], 'store');
            if ($uploadImg['status'] != 'success') {
                $_SESSION['msg'] .= "Upload avatar failed: " . $uploadImg['msg'];
                return false;
            } else {
                $store_avatar = $uploadImg['file'];
            }
        } else {
            $store_avatar = '';
        }

        // Insert data
        $DB->query("INSERT INTO ".root_table."store (store_time, user_id, store_name, store_type, store_phone, store_address, city_id, googlemap_code, store_backend_url, store_backend_type, store_backend_key, store_backend_secret, store_backend_sync, store_display, store_avatar, store_slogan) VALUES ('{$store_time}', '{$user_id}', '{$store_name}', '{$store_type}', '{$store_phone}', '{$store_address}', '{$city_id}', '{$googlemap_code}', '{$store_backend_url}', '{$store_backend_type}', '{$store_backend_key}', '{$store_backend_secret}', '{$store_backend_sync}', '{$store_display}', '{$store_avatar}', '{$store_slogan}')");

        $CMS->class->cache->mdelete($this->cache_prefix);

        $store_id = $DB->last_insert_id();

        //Cache
        $insertedRecord = $this->get_info($store_id);

        $CMS->class->logs->key= "store_{$store_id}";
        $_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} create <b>store {$store_name}</b>");

        return TRUE;
    }

    public function check_exist( $field, $value = "", $except_value = "" )
    {
        global $CMS, $DB, $member;

        if ( ! $field )
        {
            return true;
        }

        if ( $except_value )
        {
            $DB->query("SELECT store_id FROM ".root_table."store WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND store_deleted=0");
        }
        else
        {
            $DB->query("SELECT store_id FROM ".root_table."store WHERE {$field}='{$value}' AND store_deleted=0");
        }

        if ( $DB->num_rows() == 0 )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function acp_listing()
    {
        global $CMS,$DB,$member;

        if (!isset($this->html))
        {
            $this->html = $CMS->class->template->load_template("skin_store");
        }

        $this->arrange_data = trim("store_id,store_name,store_time,store_type,user_id");
        $default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "store_id";
        $default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";

        $where='';
        if (($CMS->input['sname'])) {
            $where.=" AND `store_name` LIKE '%{$CMS->input['sname']}%'";
            $this->prefix_html.="&sname={$CMS->input['sname']}";
        }
        if (($CMS->input['sid'])) {
            $where.=" AND store_id='{$CMS->input['sid']}'";
            $this->prefix_html.="&sid={$CMS->input['sid']}";
        }

        $this->prefix_html=empty($this->prefix_html)?'':'?site=store'.$this->prefix_html.'&page=';

        $sql = "SELECT * FROM `".root_table."store` WHERE `store_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

        list($this->show_page, $results) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

        if ($results)
        {
            foreach ($results as $data)
            {
                $data = $this->convertvalue($data);
                $output .= $this->html->mid($data);
            }
        }
        else
        {
            //$output .= $this->html->none();
        }
        return $output;
    }

    public function info($id=0){
        global $CMS, $DB, $member;
        $id = intval($id);
        if(!$id) return false;

        $sql = "SELECT * FROM `".root_table."store` WHERE `store_deleted`=0 AND `cus_id`='{$member['cus_id']}' AND `store_id`={$id} LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        return $data;
    }

    public function acp_info($id=0){

        global $CMS, $DB, $member;
        $id = intval($id);
        if(!$id) return false;

        $sql = "SELECT * FROM `".root_table."store` WHERE `store_deleted`=0 AND `store_id`={$id} LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        return $data;
    }

    public function del(){
        global $CMS, $DB, $member;
        $DB->query("UPDATE `".root_table."store` SET `store_deleted`=1 WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$CMS->input['id']}");
        $_SESSION["msg"]=$CMS->lang['store_del_success'];

        //Del cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        return TRUE;
    }

    public function acp_del()
    {
        global $CMS, $DB;

        // Get info
        $data = $this->get_info();

        // Check existing
        if ( ! $data ) { return false; }

        // Update info
        $DB->query("UPDATE ".root_table."store SET store_deleted=1 WHERE store_id='{$data['store_id']}'");

        //Del cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        // Create log
        $CMS->class->logs->key = "store_{$data['store_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['store_deleted']} <b>{$data['store_name']}</b>");

        // Redirect
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store&page={$CMS->input['page']}");

        return true;
    }
    public function del_all(){
        global $CMS, $DB, $member;
        if(is_array($CMS->input['id_del'])) {
            foreach ($CMS->input['id_del'] as $id){
                $DB->query("UPDATE `".root_table."store` SET `store_deleted`=1 WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id}");
            }
        }

        //Del cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        die ('success');
    }

    public function acp_del_all(){
        global $CMS, $DB, $member;
        if(is_array($CMS->input['id_del'])) {
            foreach ($CMS->input['id_del'] as $id){
                $DB->query("UPDATE `".root_table."store` SET `store_deleted`=1 WHERE `store_id` = {$id}");
                $CMS->class->logs->insert("{$member['cus_username']} deleted <b>store {$id}</b>");
            }
        }

        //Del cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        die ('success');
    }
    public function edit($id=null) {
        if(!is_null($id)) {
            global $CMS, $DB, $member;
            $DB->query("SELECT `store_act` FROM `".root_table."store` WHERE `cus_id`={$member['cus_id']} AND `store_id`={$id}");
            if ($DB->num_rows()>0) {
                if ($DB->fetch_array()['store_act']==2) {
                    $CMS->errormsg="{$CMS->lang['store_lock_err']}";
                    return false;
                }
            } else return false;

            $store_title=$CMS->input['store_title'];
            $store_des=substr($CMS->input['store_des'],0,1000);
            $free_sub=$CMS->input['free_sub']=='sub'?1:0;
            if ($free_sub==1) {
                $store_url='';
                $store_url_sub=rtrim($CMS->input['store_url_sub'],'/');
            } else {
                $store_url=rtrim($CMS->input['store_url'],'/');
                $store_url_sub='';
            }
            if (empty($store_url) AND empty($store_url_sub)) {
                return false;
            }
            $store_cam_pub=is_array($CMS->input['store_cam_pub'])?$CMS->input['store_cam_pub']:array();
            foreach ($CMS->input['store_cam'] as $cam) {
                array_push($store_cam_pub,$cam);
            }
            $store_cam_pri=is_array($CMS->input['store_cam_pri'])?$CMS->input['store_cam_pri']:array();
            $store_cam=implode(",", $CMS->input['store_cam']);
            $store_cam = ",".$store_cam.",";
            // $store_tags=implode(",", $CMS->input['store_tags']);
            // $store_tags = ",".$store_tags.",";
            $store_ban=isset($_FILES['store_ban'])?$_FILES['store_ban']['name']:'';
            $store_logo=isset($_FILES['store_logo'])?$_FILES['store_logo']['name']:'';
            $store_pri=(isset($CMS->input['store_pri'])&&$CMS->input['store_pri']=='on')?1:0;
            $store_act=(isset($CMS->input['store_act'])&&$CMS->input['store_act']=='on')?1:0;
            $store_hide=(isset($CMS->input['store_hide'])&&$CMS->input['store_hide']=='on')?1:0;
            // Check input
            if (strlen($store_title)<3||strlen($store_title)>40) {
                $CMS->errormsg="{$CMS->lang['store_err_title']}";
                return false;
            }
            if (!empty($store_url)) {
                if (!(bool)preg_match("/^[0-9a-zA-Z-_]+$/",$store_url)||strlen($store_url)<3||strlen($store_url)>20) {
                    $CMS->errormsg="{$CMS->lang['store_err_url']}";
                    return false;
                }
                $DB->query("SELECT `store_id` FROM `".root_table."store` WHERE `store_url`='{$store_url}' AND `store_id`!={$id}");
                if($DB->num_rows()>0) {
                    $CMS->errormsg="{$CMS->lang['store_exist_url']}";
                    return FALSE;
                }
            }
            // Check upload
            if(!empty($store_ban)) {
                if (!$CMS->class->attachment->check_is_image($_FILES['store_ban']['tmp_name'])) {
                    $CMS->errormsg="{$CMS->lang['store_war_ban']}";
                    return false;
                }
                if ($_FILES['store_ban']['size']>(5*1024*1024)) {
                    $CMS->errormsg="{$CMS->lang['store_war_ban']}";
                    return false;
                }
                // $image_info=getimagesize($_FILES['store_ban']['tmp_name']);
                // if ($image_info[0]>1080 || $image_info[1]>230) {
                // $CMS->errormsg="{$CMS->lang['store_war_ban']}";
                // return false;
                // }
                $tmp=empty($store_url)?$store_url_sub:$store_url;
                $tmp=preg_replace('/[^a-zA-Z0-9]/','',$tmp);
                $new_image="/store/{$CMS->class->image->check_folder_img("store","",1,"")}/{$tmp}-ban.{$imageFileType}";
                if (!move_uploaded_file($_FILES['store_ban']['tmp_name'], $CMS->vars['upload_dir'].$new_image)) {
                    return FALSE;
                } else {
                    $store_ban=$new_image;
                    $DB->query("SELECT `store_ban` FROM `".root_table."store` WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id} AND `store_ban` <>'{$store_ban}'");
                    if ($DB->num_rows()>0) {
                        while ($data=$DB->fetch_array()) {
                            unlink($CMS->vars['upload_dir'].$data['store_ban']);
                        }
                    }
                }
            }
            if(!empty($store_logo)) {
                if (!$CMS->class->attachment->check_is_image($_FILES['store_logo']['tmp_name'])) {
                    $CMS->errormsg="{$CMS->lang['store_war_ban']}";
                    return false;
                }
                if ($_FILES['store_logo']['size']>(5*1024*1024)) {
                    $CMS->errormsg="{$CMS->lang['store_war_ban']}";
                    return false;
                }
                // $image_info=getimagesize($_FILES['store_ban']['tmp_name']);
                // if ($image_info[0]>1080 || $image_info[1]>230) {
                // $CMS->errormsg="{$CMS->lang['store_war_ban']}";
                // return false;
                // }
                $tmp=empty($store_url)?$store_url_sub:$store_url;
                $tmp=preg_replace('/[^a-zA-Z0-9]/','',$tmp);
                $new_image="/store/{$CMS->class->image->check_folder_img("store","",1,"")}/{$tmp}-logo.{$imageFileType}";
                if (!move_uploaded_file($_FILES['store_logo']['tmp_name'], $CMS->vars['upload_dir'].$new_image)) {
                    return FALSE;
                } else {
                    $store_logo=$new_image;
                    $DB->query("SELECT `store_logo` FROM `".root_table."store` WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id} AND `store_logo` <>'{$store_logo}'");
                    if ($DB->num_rows()>0) {
                        while ($data=$DB->fetch_array()) {
                            unlink($CMS->vars['upload_dir'].$data['store_logo']);
                        }
                    }
                }
            }
            if (!empty($store_url_sub)) {
                if (!preg_match("/^$|(http(s)?:\/\/)(www\.)?(.)*[\.](.)*$/i",$store_url_sub)) {
                    $CMS->errormsg="{$CMS->lang['store_url_err']}";
                    return false;
                }
                $DB->query("SELECT `store_id` FROM `".root_table."store` WHERE `store_url_sub`='{$store_url_sub}' AND `store_id`!={$id}");
                if($DB->num_rows()>0) {
                    $CMS->errormsg="{$CMS->lang['store_exist_url_sub']}";
                    return FALSE;
                }
            }
            /*
            $DB->query("SELECT `store_id`,`store_url_sub` FROM `".root_table."store` WHERE `store_id`={$id} ");
            if ($DB->num_rows()>0) {
                $data=$DB->fetch_array();
                if ($data['store_url_sub']!=$store_url_sub) {
                    $data['str_store_url_sub']=rtrim(preg_replace("/(http(s)?:\/\/)(www\.)?/","",$data['store_url_sub']),'/');
                    $str=file_get_contents(root_path.'zone/vhost.conf');
                    $start=strpos($str,'#start'.$data['str_store_url_sub']);
                    $end=strpos($str,'#end'.$data['str_store_url_sub'])+strlen('#end'.$data['str_store_url_sub']);
                    $str1=substr($str,0,$start);
                    $str2=substr($str,$end);
                    file_put_contents(root_path.'zone/vhost.conf',$str1.$str2);
                }
            }
            if (!empty($store_url_sub)&&$data['store_url_sub']!=$store_url_sub) {
                $DB->query("SELECT `store_id` FROM `".root_table."store` WHERE `store_url_sub`='{$store_url_sub}'");
                if ($DB->num_rows()>0) {
                    $CMS->errormsg="{$CMS->lang['store_exist_url_sub']}";
                    return FALSE;
                }
                $str_store_url_sub=rtrim(preg_replace("/(http(s)?:\/\/)(www\.)?/","",$store_url_sub),'/');
                $str=<<<EOF
\n
#start{$str_store_url_sub}
<VirtualHost *:80>
    DocumentRoot "{$CMS->vars['root_document']}/cus_store"
    ServerName {$str_store_url_sub}
    ErrorLog "logs/{$str_store_url_sub}.log"
    CustomLog "logs/{$str_store_url_sub}-access.log" common
</VirtualHost>
#end{$str_store_url_sub}
EOF;
                $fp=fopen(root_path.'zone/vhost.conf','a') or exit('not open file httpd.conf');
                fwrite($fp, $str);
                fclose($fp);
            }
            */
            // update data

            $store_ban=empty($store_ban)?"":"`store_ban`='{$store_ban}',";
            $store_logo=empty($store_logo)?"":"`store_logo`='{$store_logo}',";
            $DB->query("UPDATE `".root_table."store` SET `store_time_up`=".time().",`store_title`='{$store_title}',`store_des`='{$store_des}',`store_url`='{$store_url}',`store_cam`='{$store_cam}', {$store_ban} {$store_logo} `store_pri`={$store_pri},`store_act`={$store_act},`store_hide`={$store_hide},`store_url_sub`='{$store_url_sub}',`store_url_default`={$free_sub} WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id}");

            $CMS->class->cache->mdelete($this->cache_prefix);

            $cam_id=array();
            // $tags_id=array();
            $DB->query("DELETE FROM `".root_table."scampaigns` WHERE `store_id` = {$id}");
            // $DB->query("DELETE FROM `".root_table."link_tags_store` WHERE `store_id` = {$id}");
            foreach ($CMS->input['store_cam'] as $value) {
                $DB->query("INSERT INTO ".root_table."scampaigns (cam_id, store_id) VALUES ('{$value}', '{$id}')");
                array_push($cam_id,$value);
                // $DB->query("INSERT INTO `".root_table."link_tags_store` (`tags_id`, `store_id`) VALUES ('{$value}', '{$id}')");
                // array_push($tags_id,$value);
            }
            $cam_id=json_encode($cam_id);
            // $tags_id=json_encode($tags_id);
            $DB->query("UPDATE `".root_table."domain_store` SET `do_store_domain`='{$store_url_sub}',`do_store_cam_id`='{$cam_id}',`do_store_cus_id`='{$member['cus_id']}' WHERE `do_store_store_id`='{$id}'");

            //Del cache
            $CMS->class->cache->mdelete('domain_store');
            $CMS->class->cache->mdelete('scampaigns');


            if (isset($_SESSION['is_mobile'])&&$_SESSION['is_mobile']==0) {
                if (count($store_cam_pub)) {
                    foreach ($store_cam_pub as $cam) {
                        $DB->query("UPDATE `".root_table."campaigns` SET `cam_is_private`=0 WHERE `cam_id`={$cam}");
                    }
                }
                if (count($store_cam_pri)) {
                    foreach ($store_cam_pri as $cam) {
                        $DB->query("UPDATE `".root_table."campaigns` SET `cam_is_private`=1 WHERE `cam_id`={$cam}");
                    }
                }

                $CMS->class->cache->mdelete('campaigns');
            }
            // Create log
            $this->vhost();
            $_SESSION["msg"]=$CMS->lang['emsg_update_success_store'].$store_title;

            return true;
        }
        return FALSE;
    }

    public function acp_edit()
    {
        global $CMS, $DB, $member;

        $oldData = $store = $this->get_info();

        $key = "store_{$store['store_id']}";
        $CMS->class->logs->key = $key;
        $CMS->class->logs->old_data = $store;

        $store_name = trim($CMS->input['store_name']);
        $store_phone = trim($CMS->input['store_phone']);
        $store_address = trim($CMS->input['store_address']);
        $store_type = intval($CMS->input['store_type']);
        $city_id = intval($CMS->input['city_id']);
        $googlemap_code = $CMS->input['googlemap_code'];

        $store_backend_url = trim($CMS->input['store_backend_url']);
        $store_backend_type = trim($CMS->input['store_backend_type']);
        $store_backend_key = trim($CMS->input['store_backend_key']);
        $store_backend_secret = trim($CMS->input['store_backend_secret']);
        $store_backend_sync = intval($CMS->input['store_backend_sync']);
        $store_display = intval($CMS->input['store_display']);

        if($store_backend_sync)
        {
            if(!$store_backend_key) { $CMS->errormsg .= $CMS->lang['invalid_key']; return false;}
            if(!$store_backend_secret) { $CMS->errormsg .= $CMS->lang['invalid_secret']; return false;}
        }



        if(!$store_name)
        {
            $_SESSION['msg'] = $CMS->lang['store_empty_name'];
            return false;
        }

        // Check input
        if (!$store_type)
        {
            $_SESSION['msg'] = $CMS->lang['store_empty_type'];
            return false;
        }

        // Check exist
        if($this->check_exist("store_name",$store_name,$store['store_name']))
        {
            $_SESSION['msg'] = $CMS->lang['store_is_exist'];
            return false;
        }

        // Insert data
        $DB->query("UPDATE ".root_table."store SET store_name='{$store_name}',store_type='{$store_type}', store_phone='{$store_phone}',store_address='{$store_address}', city_id='{$city_id}', googlemap_code='{$googlemap_code}', store_backend_url='{$store_backend_url}', store_backend_type='{$store_backend_type}', store_backend_key='{$store_backend_key}', store_backend_secret='{$store_backend_secret}', store_backend_sync='{$store_backend_sync}', store_display='{$store_display}' WHERE store_id='{$store['store_id']}'");

        $CMS->class->cache->mdelete($this->cache_prefix);
        $_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} edited <b>store {$store_name}</b>");

        $data_new = $this->get_info($store['store_id']);
        $CMS->class->logs->key= "store_{$store['store_id']}";
        $CMS->class->logs->save_detail("store",$store['store_id'],$data_new);


        return TRUE;
    }

    public function auto_run() {
        global $CMS, $DB, $member;

        if (!isset($this->html)) {
            $this->html = $CMS->class->template->load_template("skin_store");
        }

        if ($CMS->class->cache->check("user_{$member['user_id']}_store_{$CMS->vars['default_language']}")) {
            $CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_store_{$CMS->vars['default_language']}");
        } else {
            $data = "";
            if ($CMS->permit["store_search"]) {
                $data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" >
	<a href="{$CMS->vars['root_domain']}/?site=store&act=search" title="{$CMS->lang['title_search_store']}">
	<button type="button" class="action-btn"><i class="fa fa-search"></i></button>
  </a>
</div>
EOF;

                $this->control = 1;
            }
            if ($CMS->permit["pcategory_arrange"]) {
                $data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="arrange" id="glyphicon-sort" >
	<i class="fa fa-refresh" title="{$CMS->lang['title_arrange_store']}"></i>
</div>
EOF;
                $this->control = 1;
            }
            if ($CMS->permit["pcategory_delete"]) {
                $data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="delete_all" id="font-icon-trash">
	<i class="fa fa-trash-o" title="{$CMS->lang['title_delete_all_store']}"></i>
</div>
EOF;
                $this->control = 1;
            }
            if ($CMS->permit["pcategory_add"]) {
                $data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered">
	<a href="{$CMS->vars['root_domain']}/?site=store&act=add" title="{$CMS->lang['title_add_pcategory']}">
		<button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>
	</a>
</div>
EOF;
                $this->control = 1;
            }
            $data = $this->control == 1 ?  $data : "";
            $CMS->class->cache->save("user_{$member['user_id']}_store_{$CMS->vars['default_language']}", $data);
            $CMS->vars['action_controller'] = $data;
        }

        $this->action_control = $CMS->vars['action_controller'];
    }
    public function search(){
        global $CMS, $DB, $member;
        $str='';
        if ($CMS->input['quick_search']) {
            $str.='&sname='.trim($CMS->input['quick_search']);
        }
        if (intval($CMS->input['sid'])) {
            $str.='&sid='.intval($CMS->input['sid']);
        }
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store{$str}");
    }



    public function get_option_store($type="")
    {
        global $CMS, $DB;

        $output = "<option value=''>-- None --</option>";
        $sql = $DB->query("SELECT * FROM ".root_table."store WHERE store_deleted = 0 ORDER BY store_time DESC");

        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql))
            {
                $output_list .= "\"{$result['store_id']}\",";
                $output .=<<<EOF
					<option value='{$result['store_id']}'>{$result['store_name']}</option>
EOF;
            }

            $output_list = rtrim($output_list,",");
        }

        if($type)
        {
            return array($output, $output_list);
        }else
        {
            return $output;
        }
    }

    public function get_info( $record_id = 0, $field_name = "" )
    {
        global $CMS, $DB, $member;

        if ( ! $record_id AND $CMS->input['site'] == "store" )
        {
            $record_id = intval($CMS->input['id']);
        }

        // Clear record
        $record_id = strip_tags($record_id);

        // Check record
        if ( ! $record_id )
        {
            return false;
        }

        $sql = "SELECT * FROM ".root_table."store WHERE (store_id='{$record_id}' OR store_name='{$record_id}') AND store_deleted = 0 ORDER BY store_id DESC LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if ( $field_name )
        {
            if ( $data[$field_name] )
            {
                return $data[$field_name];
            }
            else
            {
                return false;
            }
        }

        return $data;
    }

    public function convertvalue($data)
    {
        global $CMS;

        $data['store_type'] = $CMS->lang["store_type_{$data['store_type']}"];
        $data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
        $data['store_time'] = $CMS->class->date->date_format($data['store_time'],1);
        $data['assets_count'] = $this->count_product_byStore($data['store_id']);

        $data['store_backend_url_c'] = $data['store_backend_url'] ? '<a target="_blank" href="'.$data['store_backend_url'].'">'.$data['store_backend_url'].'<a>' : '...';
        $data['store_backend_type_c'] = $data['store_backend_type'] ? $data['store_backend_type'] : '...';

        return $data;
    }

    public function action($id=null) {
        if (!is_null($id)) {
            global $CMS, $DB, $member;
            $DB->query("SELECT `store_act`, store_title FROM `".root_table."store` WHERE `cus_id`={$member['cus_id']} AND `store_id`={$id}");
            $data = $DB->fetch_array();
            if ($data['store_act']==2) {
                return FALSE;
            }
            $count = $DB->query("UPDATE `".root_table."store` SET `{$CMS->input['action']}`='{$CMS->input['value']}' WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id}");

            //delete cache
            $CMS->class->cache->delete('store_'.$id);

            if($count)
            {
                if($CMS->input['action'] == "store_deleted")
                {
                    $_SESSION['msg'] = $CMS->lang['emsg_deleted_success_store']. $data['store_title'];
                }else
                {
                    $_SESSION['msg'] = $CMS->lang['emsg_update_success_store']. $data['store_title'];
                }
            }else
            {
                $_SESSION['msg'] = $CMS->lang['emsg_update_error_store']. $data['store_title'];
            }
            return true;
        }
        return false;
    }



    public function get_list_store($defaultvalue="", $type = 0, $default_select = 1, $disable_title=0)
    {
        global $CMS, $DB;

        //if($CMS->class->cache->check("store_list"))
        //{
        //	$data = $CMS->class->cache->load("store_list");
        //	return $data;
        //}
        $output = "";

        $CMS->class->language->load("store");

        $sql = "SELECT * FROM ".root_table."store WHERE store_deleted=0";

        $results = $DB->fetch_data($sql, $this->cache_prefix);

        $row = count($results);

        if($row >= 3 OR $type == 1)
        {
            if($type!=2 AND $type!=3)
            {

                $output = $disable_title ? "" : "<option value=''>{$CMS->lang['select_store']}</option>";
            }
        }

        $i = 1;
        $id_checked = 0;

        foreach($results as $data)
        {
            $count = "";
            $count_asset = $this->count_product_byStore($data['store_id']);
            if($type == 1)
            {
                $selected = ($defaultvalue AND $defaultvalue == $data['store_id']) ? "selected" : "";
                $output .= "<option value='{$data['store_id']}' {$selected}>{$data['store_name']} ({$count_asset})</option>";
            }
            else if($type == 2) // Get outout data is array
            {
                $output[] = array('id' => $data['store_id'], 'name' => $data['store_name']);
            }
            else if($type == 3) // Get outout data is string
            {
                $output .= $output ? ",".$data['store_id'] : $data['store_id'];
            }
            else
            {
                if($row >= 3 )
                {
                    if($defaultvalue)
                    {
                        if( $defaultvalue == $data['store_id'])
                        {
                            $selected = "selected";
                            $id_checked = $data['store_id'];

                        }else
                        {
                            $selected = "";
                        }
                    }else
                    {
                        if($i == 1 and $default_select)
                        {
                            $selected = "selected";
                            $id_checked = $data['store_id'];
                        }else
                        {
                            $selected = "";
                        }

                    }

                    $output .= "<option value='{$data['store_id']}' {$selected}>{$data['store_name']} ({$count_asset})</option>";

                }
                else
                {
                    if($defaultvalue)
                    {
                        $checked = ($defaultvalue AND $defaultvalue == $data['store_id']) ? "checked" : "";
                        $id_checked = $checked ? $data['store_id'] : 0;
                    }else
                    {
                        if($i == 1 and $default_select)
                        {
                            $checked = "checked";
                            $id_checked = $data['store_id'];

                        }else
                        {
                            $checked = "";
                        }

                    }

                    if($row == 1)
                    {
                        $output .=<<<EOF
						<div class="radio w25">
							<input type="radio" checked name="store_id" id="radio-{$data['store_id']}" value="{$data['store_id']}">
							<label for="radio-{$data['store_id']}">{$data['store_name']} ({$count_asset})</label>
						</div>
EOF;
                    }
                    else
                    {
                        $output .=<<<EOF
						<div class="radio w25">
							<input type="radio" {$checked} name="store_id" id="radio-{$data['store_id']}" value="{$data['store_id']}">
							<label for="radio-{$data['store_id']}">{$data['store_name']} ({$count_asset})</label>
						</div>
EOF;
                    }


                }
            }

            $i++;
        }

        if($type == 0)
        {
            //$CMS->class->cache->save("store_list",$output);
            $data = array($row, $output, $id_checked);

            return $data;
        }
        else
        {

            return $output;
        }


    }

    public function count_product_byStore($store_id = "")
    {
        global $CMS, $DB;

        if($store_id != "")
        {
            $sql = "SELECT count(0) cnt FROM ".root_table."assets WHERE store_id  = '{$store_id}' AND parent_id=0 AND ass_deleted = 0  AND is_available=1 ";

            $data = $DB->fetch_data($sql, 'assets');

            return $data[0]['cnt'];
        }else
        {
            return 0;
        }
    }

    public function getListOptionStore($no_accept_id=0)
    {
        global $CMS, $DB;

        $output = "";
        $clause = "";
        if($no_accept_id)
        {
            $clause = " AND store_id != '{$no_accept_id}' ";
        }

        $sql = "SELECT * FROM ".root_table."store WHERE store_deleted = 0 {$clause} ORDER BY store_name ASC";

        $results = $DB->fetch_data($sql, $this->cache_prefix);

        if($results)
        {
            foreach($results as $result)
            {
                $output .= "<option value='{$result['store_id']}'>{$result['store_name']}</option>";

            }
        }

        return $output;
    }

    public function getListStore_by_cityid($city_id = "", $sqlAdd = "")
    {
        global $CMS, $DB;

        $output = "";
        $clause = $sqlAdd;
        if($city_id != "")
        {
            $clause .= " AND city_id = '{$city_id}' ";
        }

        $sql = "SELECT * FROM ".root_table."store WHERE store_deleted = 0 {$clause} ORDER BY store_name ASC";

        $results = $DB->fetch_data($sql, $this->cache_prefix);


        return $results;
    }

    public function getAll()
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM ".root_table."store WHERE store_deleted = 0 ORDER BY store_name ASC";

        return $DB->fetch_data($sql, $this->cache_prefix);
    }

    public function acp_addAjax()
    {
        global $CMS, $DB, $member;

        $store_name = trim($CMS->input['store_name']);
        $store_phone = trim($CMS->input['store_phone']);
        $store_address = trim($CMS->input['store_address']);
        $store_type = intval($CMS->input['store_type']);
        $city_id = intval($CMS->input['city_id']);
        $googlemap_code = $CMS->input['googlemap_code'];
        $user_id = $member['user_id'];
        $store_time = time();

        $store_backend_url = trim($CMS->input['store_backend_url']);
        $store_backend_type = trim($CMS->input['store_backend_type']);
        $store_backend_key = trim($CMS->input['store_backend_key']);
        $store_backend_secret = trim($CMS->input['store_backend_secret']);
        $store_backend_sync = intval($CMS->input['store_backend_sync']);

        if($store_backend_sync)
        {
            if( ! $store_backend_key )
            {
                print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_key']}"));exit;
            }

            if( ! $store_backend_secret )
            {
                print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_secret']}"));exit;
            }
        }

        if( ! $store_name )
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['store_empty_name']}"));exit;
        }

        if ( ! $store_type )
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['store_empty_type']}"));exit;
        }

        // Check exist
        if( $this->check_exist("store_name",$store_name) )
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['store_is_exist']}"));exit;
        }

        // Insert data
        $sql = "INSERT INTO ".root_table."store (store_time, user_id, store_name, store_type, store_phone, store_address, city_id, googlemap_code, store_backend_url, store_backend_type, store_backend_key, store_backend_secret, store_backend_sync) VALUES ('{$store_time}', '{$user_id}', '{$store_name}', '{$store_type}', '{$store_phone}', '{$store_address}', '{$city_id}', '{$googlemap_code}', '{$store_backend_url}', '{$store_backend_type}', '{$store_backend_key}', '{$store_backend_secret}', '{$store_backend_sync}')";
        // print $sql; exit;

        $sql = $DB->query($sql);
        $store_id = $DB->last_insert_id();

        // clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        //Cache
        $insertedRecord = $this->get_info($store_id);

        $CMS->class->logs->key= "store_{$store_id}";
        $_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} create <b>store {$store_name}</b> via quickadd");

        return $store_id;
    }

    public function acp_editAjax()
    {
        global $CMS, $DB, $member;

        $store_id = trim($CMS->input['store_id']);
        $store_name = trim($CMS->input['store_name']);
        $store_phone = trim($CMS->input['store_phone']);
        $store_address = trim($CMS->input['store_address']);
        $store_type = intval($CMS->input['store_type']);
        $city_id = intval($CMS->input['city_id']);
        $googlemap_code = $CMS->input['googlemap_code'];
        $user_id = $member['user_id'];
        $store_time = time();

        $store_backend_url = trim($CMS->input['store_backend_url']);
        $store_backend_type = trim($CMS->input['store_backend_type']);
        $store_backend_key = trim($CMS->input['store_backend_key']);
        $store_backend_secret = trim($CMS->input['store_backend_secret']);
        $store_backend_sync = intval($CMS->input['store_backend_sync']);

        $store = $this->get_info($store_id);

        if($store_backend_sync)
        {
            if( ! $store_backend_key )
            {
                print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_key']}"));exit;
            }

            if( ! $store_backend_secret )
            {
                print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_secret']}"));exit;
            }
        }

        if( ! $store_name )
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['store_empty_name']}"));exit;
        }

        if ( ! $store_type )
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['store_empty_type']}"));exit;
        }

        // Check exist
        if( $this->check_exist("store_name", $store_name, $store['store_name']) )
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['store_is_exist']}"));exit;
        }

        // Update data
        $sql = "UPDATE ".root_table."store SET store_name='{$store_name}',store_type='{$store_type}', store_phone='{$store_phone}',store_address='{$store_address}', city_id='{$city_id}', googlemap_code='{$googlemap_code}', store_backend_url='{$store_backend_url}', store_backend_type='{$store_backend_type}', store_backend_key='{$store_backend_key}', store_backend_secret='{$store_backend_secret}', store_backend_sync='{$store_backend_sync}' WHERE store_id='{$store['store_id']}'";
        $count = $DB->query($sql);

        // Clearcache
        $CMS->class->cache->mdelete($this->cache_prefix);

        if( $count )
        {
            $data_new = $this->get_info($store['store_id']);

            // Addlogs
            $CMS->class->logs->key= "store_{$store['store_id']}";
            $CMS->class->logs->insert("{$member['cus_username']} edited <b>store {$store_name}</b>");

            $CMS->class->logs->key= "store_{$store['store_id']}";
            $CMS->class->logs->old_data = $store;
            $CMS->class->logs->save_detail("store",$store['store_id'],$data_new);

            return $data_new;
        }
        else
        {
            return false;
        }
    }
}
?>