<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->accounts=new ClassAccounts;
class ClassAccounts {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";
	public $cache_prefix = "accounts";

	public function listing() {
		global $CMS, $DB, $member;

		foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = urldecode($v);
        }

		$this->arrange_data = trim("accounts_id,accounts_name,accounts_bank,accounts_number,accounts_holder,accounts_branch,accounts_status,accounts_time");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "accounts_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		$where = '';
		if (!empty($CMS->input['a_id']) && Validate::isNum($CMS->input['a_id'])) {
			$where .= " AND `accounts_id`='{$CMS->input['a_id']}' ";
		}
		if (!empty($CMS->input['a_account'])) {
			$where .= " AND `accounts_name` LIKE '%{$CMS->input['a_account']}%' ";
		}
		if (!empty($CMS->input['a_bank'])) {
			$where .= " AND `accounts_bank` LIKE '%{$CMS->input['a_bank']}%' ";
		}
		if (!empty($CMS->input['a_number'])) {
			$where .= " AND `accounts_number` LIKE '%{$CMS->input['a_number']}%' ";
		}
		if (isset($CMS->input['a_status']) && $CMS->input['a_status']!='' && in_array($CMS->input['a_status'], array(0,1))) {
			$where .= " AND `accounts_status` = '{$CMS->input['a_status']}' ";
		}

		$sql = "SELECT * FROM `".root_table."accounts` WHERE `accounts_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

        list($this->show_page, $results) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

		return $results;
	}

	public function convertvalue($data=null) {
		global $CMS, $DB, $member;
		switch ($data['accounts_status']) {
			case 1:
				$data['accounts_status_c'] = $CMS->lang['a_status_01'];
				break;
			default:
				$data['accounts_status_c'] = $CMS->lang['a_status_00'];
				break;
		}
		$data['accounts_time_c'] = $CMS->class->date->date_format($data['accounts_time'],1);
		return $data;
	}

	public function getInfo($record_id = null,$field_name = '*') {
        global $CMS, $DB, $member;
        $sql_add = "";
        if(is_numeric($record_id))
        {
            $record_id = intval($record_id);
            $sql_add .= "`accounts_id`='{$record_id}' AND";
        }
        else
        {
            $record_id = trim($record_id);
            $sql_add .= "`accounts_name`='{$record_id}' AND";
        }

        $sql = "SELECT {$field_name} FROM `".root_table."accounts` WHERE {$sql_add} `accounts_deleted`=0 LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if ($field_name !== '*') {
            return $data[$field_name];
        }

        return $data;
	}

	public function getAll($sql_add = '') {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."accounts` WHERE {$sql_add} `accounts_deleted`=0 ORDER BY `accounts_id` DESC";

		return $DB->fetch_data($sql, $this->cache_prefix);
	}

	public function add($a_account = null, $a_bank = null, $a_number = null, $a_holder = null, $a_branch = null, $a_status = null) {
		if (!empty($a_account)) {
			global $CMS, $DB, $member;
			$DB->query("INSERT INTO `".root_table."accounts` (`accounts_name`, `accounts_bank`, `accounts_number`, `accounts_holder`, `accounts_branch`, `accounts_status`, `user_id`, `accounts_time`) VALUES ('{$a_account}', '{$a_bank}', '{$a_number}', '{$a_holder}', '{$a_branch}', '{$a_status}', '{$member['user_id']}','".time()."')");

			//Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$id = $DB->last_insert_id();
			$CMS->class->logs->insert("Add_accounts_{$id}");
			return $id;
		}
		return false;
	}
	public function edit($data_info = null, $a_account = null, $a_bank = null, $a_number = null, $a_holder = null, $a_branch = null, $a_status = null) {
		if (!empty($data_info) && !empty($a_account)) {
			global $CMS, $DB, $member;
			$key = "Edit_accounts_{$data_info['accounts_id']}";
			$CMS->class->logs->key = $key;
			$CMS->class->logs->old_data = $data_info;
			$CMS->class->logs->insert($key);
			$DB->query("UPDATE `".root_table."accounts` SET `accounts_name`='{$a_account}', `accounts_bank`='{$a_bank}', `accounts_number`='{$a_number}', `accounts_holder`='{$a_holder}', `accounts_branch`='{$a_branch}', `accounts_status`='{$a_status}' WHERE `accounts_deleted`='0' AND `accounts_id`='{$data_info['accounts_id']}'");

            //Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->key = $key;
			$CMS->class->logs->save_detail("accounts",$data_info['accounts_id'],$this->getInfo($data_info['accounts_id']));
			return true;
		}
		return false;
	}
	public function deleted($id = null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."accounts` SET `accounts_deleted`=1 WHERE `accounts_id`='{$id}'");

            //Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("Deleted_accounts_{$id}");
		}
		return false;
	}

	function update_balance($id = 0)
    {
        global $DB, $CMS;

        $id = intval($id);

        if(!$id) return false;

        $balance = $this->calculate_balance($id);

        $sql = "UPDATE ".root_table."accounts SET accounts_balance = {$balance} WHERE accounts_id = {$id}";

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        return $DB->query($sql);
    }

    function calculate_balance($id = 0)
    {
        global $CMS, $DB;

        $balance = 0;

        if(!$id) return $balance;

        $sql = "SELECT SUM(IF(trx_subtype IN (2,3), trx_total, trx_total * -1)) balance FROM ".root_table."transaction WHERE trx_subtype IN (2,3,6,7) AND trx_status IN (1,3) AND trx_account={$id}";

        $result = $DB->fetch_assoc($DB->query($sql));

        return $result['balance'] * 1;
    }
}
?>