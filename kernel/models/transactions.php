<?php

use core\ezy;
use lib\date;
use lib\input;
use models\download;

$CMS->transactions = new ClassTransactions;
class ClassTransactions {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";

	public $accepted_files = ['jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'pdf'];
	public $max_size = 25; //MB
	public $max_files = 5;

	public $status_font_size;

	public $file_icons = [
        'png' => 'fa fa-camera',
        'jpg' => 'fa fa-camera',
        'jpeg' => 'fa fa-camera',
        'gif' => 'fa fa-camera',
        'zip' => 'fa fa-file-archive-o',
        'rar' => 'fa fa-file-archive-o',
        'doc' => 'fa fa-file-text-o',
        'docx' => 'fa fa-file-text-o',
        'xls' => 'fa fa-file-excel-o',
        'xlsx' => 'fa fa-file-excel-o',
        'xlsx' => 'fa fa-file-excel-o',
        'pdf' => 'fa fa-file-pdf-o',
        'default' => 'fa fa fa-file',
    ];

	public $permit_group = [

        //Hóa đơn (thu)
        1 => [
            //pending
            0 => ['add_to_payment', 'preview', 'files', 'send', 'copy'],
            //paid
            1 => ['preview', 'files', 'send', 'copy'],
            //overdue
            2 => ['preview', 'files', 'print', 'send', 'copy'],
            3 => ['preview', 'files', 'print', 'send'],//closed
            4 => []//rejected
        ],

        //Thanh toán
        2 => [
            //pending
            0 => ['preview', 'files', 'send'],
            //paid
            1 => ['preview', 'files', 'send'],
            //overdue
            2 => ['preview', 'files', 'send'],
            3 => ['preview', 'files', 'send'],//closed
            4 => []//rejected
        ],

        //Phiếu thu
        3 => [
            //pending
            0 => ['preview', 'files', 'send', 'copy'],
            //paid
            1 => ['preview', 'files', 'send', 'copy'],
            //overdue
            2 => ['preview', 'files', 'send', 'copy'],
            3 => ['preview', 'files', 'send'],//closed
            4 => []//rejected
        ],

        //Dự kiến
        4 => [
            //pending
            0 => ['preview', 'send', 'copy'],
            //paid
            1 => ['preview', 'send', 'copy'],
            //overdue
            2 => ['preview', 'send', 'copy'],
            3 => ['preview', 'send'],//closed
            4 => []//rejected
        ],

        //Hóa đơn (chi)
        5 => [
            //pending
            0 => ['add_to_bill', 'preview', 'files', 'send', 'copy'],
            //paid
            1 => ['preview', 'files', 'send', 'copy'],
            //overdue
            2 => ['preview', 'files', 'send', 'copy'],
            3 => ['preview', 'files', 'send'],//closed
            4 => []//rejected
        ],

        //Chi phí
        6 => [
            //pending
            0 => ['preview', 'files', 'send', 'copy'],
            //paid
            1 => ['preview', 'files', 'send', 'copy'],
            //overdue
            2 => ['preview', 'files', 'send', 'copy'],
            3 => ['preview', 'files', 'send'],//closed
            4 => []//rejected
        ],

        //Bill payment
        7 => [
            //pending
            0 => ['preview', 'files', 'send'],
            //paid
            1 => ['preview', 'files', 'send'],
            //overdue
            2 => ['preview', 'files', 'send'],
            3 => ['preview', 'files', 'send'],//closed
            4 => []//rejected
        ],

        //Credit memo
        8 => [
            //pending
            0 => ['preview', 'files', 'send'],
            //paid
            1 => ['preview', 'files', 'send'],
            //overdue
            2 => ['preview', 'files', 'send'],
            3 => ['preview', 'files', 'send'],//closed
            4 => []//rejected
        ],
    ];

    public $action_list_by_type = [
        0 => [1,2,3,4,8,5,7,6],// all , hvu 17/08/2017, update new type (0) for show all when search all
        1 => [1,2,3,4,8], //thu
        2 => [5,7,6] //chi
    ];

    public $list_status = [0,1,2,3,4];

    public $status_color = [
        0 => "#969693", //watting
        1 => "#56dd2a", //Paid
        2 => "#dd0f0f", //Overdue
        3 => "#000000", //Close
        4 => "#f97c00", //Cancel
        5 => "#00dbf9", //Accept
    ];

    public $sql_add;

    public function action_list_by_status($data)
    {
        global $CMS;

        $permits = [
            'add_to_payment' => [
                'lang' => "<i class=\"fa fa-plus-circle\" aria-hidden=\"true\"></i> {$CMS->lang['trx_add_2']}",
                'url' => "{$CMS->vars['root_domain']}/?site=transactions&act=add&invoice={$data['trx_id']}&type={$data['trx_type']}&sub=2",
                'type' => "redirect",
            ],
            'add_to_bill' => [
                'lang' => "<i class=\"fa fa-plus-circle\" aria-hidden=\"true\"></i> {$CMS->lang['trx_add_2']}",
                'url' => "{$CMS->vars['root_domain']}/?site=transactions&act=add&invoice={$data['trx_id']}&type={$data['trx_type']}&sub=7",
                'type' => "redirect",
            ],
            'edit' => [
                'lang' => "<i class=\"fa fa-pencil-square\" aria-hidden=\"true\"></i> {$CMS->lang['act_edit']}",
                'url' => "{$CMS->vars['root_domain']}/?site=transactions&act=edit&id={$data['trx_id']}&type={$data['trx_type']}&sub={$data['trx_subtype']}",
                'type' => "redirect",
            ],
            /*'print' => [
                'lang' =>"<i class=\"fa fa-print\" aria-hidden=\"true\"></i> {$CMS->lang['act_print']}",
                'url' => "{$CMS->vars['root_domain']}/?site=transactions&act=print&id={$data['trx_id']}&type={$data['trx_type']}&sub={$data['trx_subtype']}",
                'type' => "windows_open",
            ],*/
            'print' => [
                'lang' =>"<i class=\"fa fa-print\" aria-hidden=\"true\"></i> {$CMS->lang['act_print']}",
                'url' => "{$CMS->vars['root_domain']}/?site=transactions&act=preview&id={$data['trx_id']}&type={$data['trx_type']}&sub={$data['trx_subtype']}",
                'type' => "redirect",
            ],
            'send' => [
                'lang' => "<i class=\"fa fa-share-square\" aria-hidden=\"true\"></i> {$CMS->lang['act_send']}",
                'type' => "onclick",
                'function' => "loadEmailTpl('{$data['trx_type']}','{$data['trx_id']}')",
            ],
            'copy' => [
                'lang' => "<i class=\"fa fa-clone\" aria-hidden=\"true\"></i> {$CMS->lang['act_copy']}",
                'url' => "{$CMS->vars['root_domain']}/?site=transactions&act=copy&id={$data['trx_id']}&type={$data['trx_type']}&sub={$data['trx_subtype']}",
                'type' => "redirect",
            ],
            'preview' => [
                'lang' => "<i class=\"fa fa-eye\" aria-hidden=\"true\"></i> {$CMS->lang['act_preview_and_print']}",
                'url' => "{$CMS->vars['root_domain']}/?site=transactions&act=preview&id={$data['trx_id']}&type={$data['trx_type']}&sub={$data['trx_subtype']}",
                'type' => "popup_iframe",
            ],
            'files' => [
                'lang' => "<i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i> {$data['trx_attachment_number']} Attachment(s)",
                'type' => "onclick",
                'function' => "getExportTrxFiles('{$data['trx_id']}')",
            ],
        ];

        $permits_group = $this->permit_group[$data['trx_subtype']][$data['trx_status']];

        $return = [];

        foreach($permits_group as $act)
        {
            if (defined("is_web_us")) {
                if(in_array($act, ['preview', 'print']))
                {
                    continue;
                }
            }

            if($CMS->permit['transactions_is_root'] || $CMS->permit['transactions_'.$act])
            {
                $return[$act] = $permits[$act];
            }
        }

        return $return;
    }

    function action_html($data)
    {
        global $CMS;

        $output = "";

        if($action_list_by_status = $CMS->transactions->action_list_by_status($data))
        {
            $output .= <<<EOF
            <div class="btn-group">
                    <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {$CMS->lang['gaction']}
                    </button>
                    <div class="dropdown-menu" style="right:inherit !important">
EOF;

            foreach($action_list_by_status as $permit)
            {
                
                if($permit['type'] == 'windows_open')
                {
                    $output .=<<<EOF
                        <a class="dropdown-item" onclick="window.open('{$permit['url']}', '', 'width=600, heigh=800')">{$permit['lang']}</a>
EOF;
                }
                else if($permit['type'] == 'popup_iframe')
                {
//                    $output .=<<<EOF
//                        <a class="dropdown-item" onclick="previewTrxPopup('{$permit['url']}')">{$permit['lang']}</a>
//EOF;
                    $output .=<<<EOF
                        <a class="dropdown-item" onclick="composeTrx('{$permit['url']}&mode=compose','{$data['trx_subtype']}','{$data['trx_id']}')">{$permit['lang']}</a>
EOF;
                }
                else if($permit['type'] == 'onclick')
                {
                    $output .=<<<EOF
                        <a class="dropdown-item" onclick="{$permit['function']}">{$permit['lang']}</a>
EOF;
                }
                else
                {
                    $output .=<<<EOF
                        <a class="dropdown-item" href='{$permit['url']}'>{$permit['lang']}</a>
EOF;
                }

            }

            if( isset($data['url_edit_link']) )
            {
                $output .=<<<EOF
                <a class="dropdown-item" href="{$data['url_edit_link']}"><i class='fa fa-edit'></i> {$CMS->lang['act_edit']}</a>
EOF;
            }

            $output .=<<<EOF
                    </div>
            </div>

EOF;
        }
        return $output;
    }

    function action_button_html($data, $style="")
    {
        global $CMS;

        $output = "";

        if($action_list_by_status = $CMS->transactions->action_list_by_status($data))
        {
            foreach($action_list_by_status as $permitKey => $permit)
            {

                if($style=='dropdown')
                {
                    $class="";

                    $output .= "<li class=\"hidden-xl-up\">";
                }
                else
                {
                    $class="pull-right add_cart_2 hidden-sm-down";
                }

                if($permit['type'] == 'windows_open')
                {
                    $output .=<<<EOF
                        <a class="{$class}" onclick="window.open('{$permit['url']}', '', 'width=600, heigh=800')">{$permit['lang']}</a>
EOF;
                }
                else if($permit['type'] == 'popup_iframe')
                {
//                    $output .=<<<EOF
//                        <a class="dropdown-item" onclick="previewTrxPopup('{$permit['url']}')">{$permit['lang']}</a>
//EOF;
                    $output .=<<<EOF
                        <a class="{$class}" onclick="composeTrx('{$permit['url']}&mode=compose','{$data['trx_subtype']}','{$data['trx_id']}')">{$permit['lang']}</a>
EOF;
                }
                else if($permit['type'] == 'onclick')
                {
                    $output .=<<<EOF
                        <a class="{$class}" onclick="{$permit['function']}">{$permit['lang']}</a>
EOF;
                }
                else
                {
                    $output .=<<<EOF
                        <a class="{$class}" href='{$permit['url']}'>{$permit['lang']}</a>
EOF;
                }

                if($style=='dropdown')
                {
                    $output .= "</li>";
                }

            }
        }
        return $output;
    }

	public function listing($export = 0) {
		global $CMS, $DB, $member;

		$exportTitle = [];
        $exportTitle[] = $CMS->lang['trx_type_'.$CMS->input['type']];

		$this->arrange_data = trim("trx_id,trx_payment_date,trx_subtype,trx_invoice_no,cus_id,trx_total,trx_tax,trx_status");
		$default_field = input::get('order') ? input::get('order') : 'trx_id';
		$default_order = input::get('by') ? input::get('by') : 'DESC';
		$where = "";

		$today = $CMS->class->date->gmt($CMS->vars['this_day'],$CMS->vars['this_month'],$CMS->vars['this_year']);

        $this->sql_add = "";

		if ($store_id = input::get('store_id') * 1)
		{
            $this->sql_add .= " AND store_id = '{$store_id}' ";
        }

		if($CMS->input['stats'])
        {

            if($CMS->input['stats'] == 'estimate') //Dự kiến
            {
                $exportTitle[] = $CMS->lang['trx_tab_estimate'];
                $this->sql_add .= " AND `trx_subtype`=4 ";
            }
            elseif ($CMS->input['stats'] == 'unbill') //chua thanh toán
            {
                $exportTitle[] = $CMS->lang['trx_tab_unpaid'];
                if($CMS->input['type'] == 1)
                {
                    $this->sql_add .= " AND `trx_subtype`=1 AND trx_receive_payment < trx_total 
                    AND trx_id NOT IN (SELECT trx_id FROM ".root_table."transaction WHERE trx_deleted=0 AND `trx_subtype`=1 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)))";
                }
                else
                {
                    $this->sql_add .= " AND `trx_subtype`=5 AND trx_receive_payment < trx_total 
                    AND trx_id NOT IN (SELECT trx_id FROM ".root_table."transaction WHERE trx_deleted=0 AND `trx_subtype`=5 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)))";
                }
            }
            elseif ($CMS->input['stats'] == 'overdue') //quá hạn
            {
                $exportTitle[] = $CMS->lang['trx_tab_overdue'];
                if($CMS->input['type'] == 1)
                {
                    $this->sql_add .= " AND `trx_subtype`=1 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)) ";
                }
                else
                {
                    $this->sql_add .= " AND `trx_subtype`=5 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)) ";
                }
            }
            elseif ($CMS->input['stats'] == 'inv')
            {
                $exportTitle[] = $CMS->lang['trx_tab_invoice'];
                if($CMS->input['type'] == 1)
                {
                    $this->sql_add .= " AND `trx_subtype`=1 ";
                }
                else
                {
                    $this->sql_add .= " AND `trx_subtype`=5 ";
                }
            }
            elseif ($CMS->input['stats'] == 'paid') //đã thanh toán
            {
                $exportTitle[] = $CMS->lang['trx_tab_paid'];
                if($CMS->input['type'] == 1)
                {
                    $this->sql_add .= " AND `trx_subtype` IN (2,3) ";
                }
                else
                {
                    $this->sql_add .= " AND `trx_subtype` IN (6,7) ";
                }
            }
            else
            {
                $this->sql_add .= " AND `trx_type`='{$CMS->input['type']}' ";
            }

            if($CMS->input['date_from'] and $CMS->input['date_to'])
            {
                $date_from = $CMS->class->date->date2time($CMS->input['date_from']);
                $date_to = $CMS->class->date->date2time($CMS->input['date_to']) + (3600*24) - 1;
                $this->sql_add .= " AND trx_time BETWEEN '{$date_from}' AND '{$date_to}' ";
            }elseif($CMS->input['date_from'])
            {
                $date_from = $CMS->class->date->date2time($CMS->input['date_from']);
                $this->sql_add .= " AND trx_time >= '{$date_from}' ";
            }elseif($CMS->input['date_to'])
            {
                $date_to = $CMS->class->date->date2time($CMS->input['date_to']) + (3600*24) - 1;
                $this->sql_add .= " AND trx_time <= '{$date_to}' ";
            }

            // print $this->sql_add;exit;
        }
        else
        {
            if(!empty($CMS->input['trx_status']))
            {
                if(preg_match("/(,)/",$CMS->input['trx_status']))
                {
                   $temp = explode(",",$CMS->input['trx_status']);
                   $sql_temp = "";
                   
                   for($i=0;$i<count($temp);$i++)
                   {
                       if($temp[$i] && in_array($CMS->input['trx_status'], $this->list_status))
                       {
                           $exportTitle[] = $CMS->lang['trx_status_0'.$temp[$i]];
                           $sql_temp .= $sql_temp ? ",{$temp[$i]}" : $temp[$i];
                       }
                   }
                   
                   $this->sql_add .= " AND `trx_status` IN ($sql_temp) ";
                }
                else
                {
                    if (in_array($CMS->input['trx_status'], $this->list_status))
                    {
                        $exportTitle[] = $CMS->lang['trx_status_0'.$CMS->input['trx_status']];
                        $this->sql_add .= " AND `trx_status`='{$CMS->input['trx_status']}' ";
                    } 
                }
            }
            
            

            if (!empty($CMS->input['trx_method'])) {
                $exportTitle[] = $CMS->lang['trx_method_0'.$CMS->input['trx_method']];
                $this->sql_add .= " AND `trx_payment_method`='{$CMS->input['trx_method']}' ";
            }

            if (!empty($CMS->input['trx_reference_no'])) {
                $exportTitle[] = $CMS->lang['trx_reference_no'].': '.$CMS->input['trx_reference_no'];
                $CMS->input['trx_reference_no'] = urldecode($CMS->input['trx_reference_no']);
                $this->sql_add .= " AND `trx_reference_no`='{$CMS->input['trx_reference_no']}' ";
            }

            if (!empty($CMS->input['trx_invoice_no'])) {
                $exportTitle[] = $CMS->lang['table_inv_code'].': '.$CMS->input['trx_invoice_no'];
                $CMS->input['trx_invoice_no'] = urldecode($CMS->input['trx_invoice_no']);
                $this->sql_add .= " AND `trx_invoice_no`='{$CMS->input['trx_invoice_no']}' ";
            }

            if (!empty($CMS->input['cus_id'])) {
                $customerInfo = $CMS->customer->getInfo($CMS->input['cus_id']);
                $exportTitle[] = "{$CMS->lang['trx_cus']}: {$customerInfo['cus_full_name']}";
                $this->sql_add .= " AND `cus_id`='{$CMS->input['cus_id']}' ";
            }

            if (!empty($CMS->input['trx_total_from'])) {
                $exportTitle[] = "{$CMS->lang['total']} {$CMS->lang['from']} {$CMS->class->input->currency($CMS->input['trx_total_from'])}";
                $this->sql_add .= " AND `trx_total` >= {$CMS->input['trx_total_from']} ";
            }

            if (!empty($CMS->input['trx_total_to'])) {
                $exportTitle[] = "{$CMS->lang['total']} {$CMS->lang['to']} {$CMS->class->input->currency($CMS->input['trx_total_to'])}";
                $this->sql_add .= " AND `trx_total` <= {$CMS->input['trx_total_to']} ";
            }

            if (!empty($CMS->input['date_from'])) {

                $CMS->input['date_from'] = urldecode($CMS->input['date_from']);

                $exportTitle[] = "{$CMS->lang['title_date_from']} {$CMS->input['date_from']}";

                $date_from = $CMS->class->date->date2time($CMS->input['date_from']);

                $this->sql_add .= " AND `trx_time` >= {$date_from} ";
            }

            if (!empty($CMS->input['date_to'])) {

                $CMS->input['date_to'] = urldecode($CMS->input['date_to']);

                $exportTitle[] = "{$CMS->lang['title_date_to']} {$CMS->input['date_to']}";

                $date_to = $CMS->class->date->date2time($CMS->input['date_to']) + (3600*24);

                $this->sql_add .= " AND `trx_time` < {$date_to} ";
            }
            
            if(!empty($CMS->input['sub']))
            {
                if(preg_match("/(,)/",$CMS->input['sub']))
                {
                   $temp = explode(",",$CMS->input['sub']);
                   $sql_temp = "";
                   
                   for($i=0;$i<count($temp);$i++)
                   {
                       if($temp[$i] && in_array($CMS->input['sub'], $this->list_status))
                       {
                           $exportTitle[] = $CMS->lang['trx_subtype_0'.$temp[$i]];
                           $sql_temp .= $sql_temp ? ",{$temp[$i]}" : $temp[$i];
                       }
                   }
                   
                   $this->sql_add .= " AND `trx_subtype` IN ($sql_temp) ";
                }
                else
                {
                    if (in_array($CMS->input['sub'], array(1, 2, 3, 4, 5, 6, 7))) 
                    {
                        $exportTitle[] = $CMS->lang['trx_subtype_0'.$CMS->input['sub']];
                        $this->sql_add .= " AND `trx_subtype`='{$CMS->input['sub']}' ";
                    }
                }
            }
            
            

            if (!empty($CMS->input['type']) && in_array($CMS->input['type'], array(1, 2))) {
                $this->sql_add .= " AND `trx_type`='{$CMS->input['type']}' ";
            }
        }

        $flag = false;

        if($CMS->input['keyword'])
        {
            $keyword = urldecode($CMS->input['keyword']);
            $exportTitle[] = "{$CMS->lang['keyword']}: {$keyword}";

            $exportTitle[] = $CMS->lang['trx_subtype_0'.$CMS->input['sub']];

            if(is_numeric($keyword))
            {
                $keyword = "TRX{$keyword}";

                $this->sql_add .= " AND trx_code='{$keyword}' ";

                $flag = true;
            }
            elseif(preg_match('/^TRX[0-9]+$/i', $keyword))
            {
                $this->sql_add .= " AND  trx_code='{$keyword}' ";

                $flag = true;
            }
            else if(preg_match('/^(INV|PAY)[0-9]+$/i', $keyword))
            {
                $keyword = strtoupper($keyword);

                if($CMS->input['type'] == 1)
                {
                    $inv_no = str_replace('INV','', $keyword);
                }
                else if ($CMS->input['type'] == 2)
                {
                    $inv_no = str_replace('PAY','', $keyword);
                }
                else
                {
                    $inv_no = "";
                }

                if(!$inv_no || $inv_no != str_split($keyword, 3)[1])
                {
                    $inv_no = "";
                }

                $inv_no = intval($inv_no);

                if($inv_no)
                {
                    $this->sql_add .= " AND  trx_invoice_no='{$inv_no}' ";

                    $flag = true;
                }
            }
            else if(Validate::isEmail($keyword))
            {
                $this->sql_add .= " AND cus_email = '{$keyword}' ";

                $flag = true;
            }

            if(!$flag)
            {
                $this->sql_add .= " AND (trx_contract_code LIKE '%{$keyword}%' OR trx_billing_address LIKE '%{$keyword}%' OR trx_reference_no LIKE '%{$keyword}%' OR cus_email LIKE '%{$keyword}%' OR trx_items LIKE '%{$keyword}%' ) ";
            }
        }

        if($export == 1) //For export data (Disabled paging)
        {
            if($CMS->input['is_template'])
            {
                $subtype = $CMS->input['type']==1 ? 3 : 6;
                $sql = "SELECT * FROM `".root_table."transaction` WHERE `trx_deleted`=0 AND trx_subtype={$subtype} ORDER BY {$default_field} {$default_order} LIMIT 10";
            }
            else
            {
                $sql = "SELECT * FROM `".root_table."transaction` WHERE `trx_deleted`=0 {$this->sql_add} ORDER BY {$default_field} {$default_order}";
            }

            $this->show_page = "";
            $this->sql_query = $DB->query($sql);
            $CMS->vars['exportTitle'] = implode(', ',$exportTitle);
        }
        else
        {
            $sql = "SELECT * FROM `".root_table."transaction` WHERE `trx_deleted`=0 {$this->sql_add} ORDER BY {$default_field} {$default_order}";

            list($this->show_page, $this->sql_query) = $CMS->class->page->create($sql, $this->per_page, $this->prefix_html, $this->suffix_html);
        }

		$arr = array();

		$this->num_rows = $DB->num_rows($this->sql_query);

		if ($this->num_rows>0) {
			while ($result = $DB->fetch_array($this->sql_query)) {
                if($export == 1) //For export data (Disabled paging)
                {
                    array_push($arr, $this->convertValueToExport($result));
                }
                else
                {
                    array_push($arr, $this->convertvalue($result));
                }
			}
		}

		return $arr;
	}

	function autocomplete()
    {
        global $CMS;

        $this->per_page = 3;
        $data = $this->listing();

        $li = "";

        $return = [
            "status" => "error",
            "msg" => $CMS->lang['data_not_found']
        ];

        if($CMS->class->page->total_row > 0)
        {
                foreach ($data as $trx)
                {
                    $trx = $this->convertvalue($trx);
                    $trx['trx_invoice_no_c'] = $trx['trx_invoice_no_c'] != '-' ? ' '.$trx['trx_invoice_no_c'] : '';

                    $trx['trx_receive_payment'] = $trx['trx_subtype'] == 1 || $trx['trx_subtype'] == 5 ? $CMS->class->input->currency($trx['trx_receive_payment']).'/' : '';

                    $li .= "<li><a href='{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$trx['trx_id']}'>{$trx['trx_code']}{$trx['trx_invoice_no_c']} {$trx['trx_receive_payment']}{$trx['trx_total_c']} ({$trx['trx_subtype_c']})</a></li>";
                }
                if($CMS->class->page->total_row > 3)
                {
                    $li .= "<li class='see_more'><a href='{$CMS->vars['root_domain']}/?site=transactions&act=search&type={$CMS->input['type']}&keyword={$CMS->input['keyword']}' >{$CMS->lang['read_more']} ({$CMS->class->page->total_row}) {$CMS->lang['result']}</a></li>";
                }

            $return = [
                "status" => "success",
                "data_option" => $li
            ];
        }

        echo @json_encode($return); exit;
    }

	public function convertvalue($data = null) {
		global $CMS, $DB, $member;

		$data['data_bk'] = $data_bk = $data;

		$data['trx_due_c'] = !in_array($data['trx_subtype'], [1,5]) || ! $data['trx_due_date'] ? '...' : $CMS->class->date->date_format($data['trx_due_date']);

		$data['trx_time_c'] = $CMS->class->date->date_format($data['trx_time']);
        $data['trx_time_update'] = $data['trx_time_update'] ? $data['trx_time_update'] : $data['trx_time'];
		$data['trx_time_update_c'] = !$data['trx_time_update'] ? '' : $CMS->class->date->date_format($data['trx_time_update']);
        // Get order code from module order
        $data_order = $CMS->order->get_order_payment($data['trx_id']);
 
        if($data_order != false)
        {
            $data['ord_link'] = $data_order;
        }
 
		$data['trx_subtype_c'] = $CMS->lang['trx_subtype_0'.$data['trx_subtype']];
		$prefix_trx = $data['trx_type'] == 2 ? 'PAY' : 'INV';
		$data['trx_invoice_no_c'] = !empty($data['trx_invoice_no']) && $data['trx_invoice_no'] > 0 ? $prefix_trx.$data['trx_invoice_no'] : '-';
		$data['cus_id_c'] = $CMS->customer->getInfo($data['cus_id'], 'cus_full_name');
		$data['store_id_c'] = $CMS->store->get_info($data['store_id'], 'store_name');
		$data['trx_payment_date_c'] = $CMS->class->date->date_format($data['trx_payment_date']);
		$data['trx_credit_memo_date_c'] = !empty($data['trx_credit_memo_date']) ? $CMS->class->date->date_format($data['trx_credit_memo_date']) : "-";

		$data['trx_tax_c'] =  $CMS->class->input->currency($data['trx_tax']);
		$data['trx_amount_c'] =  $CMS->class->input->currency($data['trx_amount']);
		$data['trx_total_c'] =  $CMS->class->input->currency($data['trx_total']);
//		$data['trx_discount_value_c'] = $data['trx_discount_type'] ?  $CMS->class->input->currency($data['trx_discount_value']) : $CMS->class->input->number($data['trx_discount_value']).'%';
        $data['trx_discount_value_c'] = $CMS->class->input->currency($data['trx_total_discount']);
		$data['trx_total_not_tax_c'] =  $CMS->class->input->currency($data['trx_total'] - $data['trx_tax']);
 
		$data['trx_estimate_date_c'] = $CMS->class->date->date_format($data['trx_estimate_date']);
		$data['trx_accepted_date_c'] = $CMS->class->date->date_format($data['trx_accepted_date']);

        $today = $CMS->class->date->gmt($CMS->vars['this_day'],$CMS->vars['this_month'],$CMS->vars['this_year']);

        if(!empty($this->status_font_size))
        {
            $status_font_size = "font-size:{$this->status_font_size}px !important;";
        }
        else
        {
            $status_font_size = "";
        }

        unset($this->status_font_size);

		if($data['trx_status'] == 0)//Dang cho
		{
				$data['trx_status_c'] = "<span style='{$status_font_size}' class=\"label label-default\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
		}
		elseif($data['trx_status'] == 1)//Hoan thanh
		{
			$data['trx_status_c'] = "<span style='{$status_font_size}' class=\"label label-success\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
		}
 		elseif($data['trx_status'] == 2)//Quá hạn
		{
			$data['trx_status_c'] = "<span style='{$status_font_size}' class=\"label label-warning\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
		}
		elseif($data['trx_status'] == 3)//Đã đóng
		{
			$data['trx_status_c'] = "<span style='{$status_font_size}' class=\"label label-success\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
		}
		elseif($data['trx_status'] == 4)//Hủy
		{
			$data['trx_status_c'] = "<span style='{$status_font_size}' class=\"label label-danger\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
		}

        if($data['trx_estimate_status'] == 0)//Dang cho
        {
            $data['trx_estimate_status_c'] = "<spa style='{$status_font_size}'n class=\"label label-default\">".$CMS->lang['trx_estimate_status_0'.$data['trx_estimate_status']]."</span>";
        }
        elseif($data['trx_estimate_status'] == 1)//Chap nhan
        {
            $data['trx_estimate_status_c'] = "<span style='{$status_font_size}' class=\"label label-success\">".$CMS->lang['trx_estimate_status_0'.$data['trx_estimate_status']]."</span>";
        }
        elseif($data['trx_estimate_status'] == 2)//Da dong
        {
            $data['trx_estimate_status_c'] = "<span style='{$status_font_size}' class=\"label label-warning\">".$CMS->lang['trx_estimate_status_0'.$data['trx_estimate_status']]."</span>";
        }
        elseif($data['trx_estimate_status'] == 3)//Tu choi
        {
            $data['trx_estimate_status_c'] = "<span style='{$status_font_size}' class=\"label label-danger\">".$CMS->lang['trx_estimate_status_0'.$data['trx_estimate_status']]."</span>";
        }


		$data['trx_payment_method_c'] = $CMS->lang['trx_method_0'.$data['trx_payment_method']];
		$data['trx_account_c'] = !empty($data['trx_account']) && intval($data['trx_account']) > 0 ? $CMS->accounts->getInfo($data['trx_account'], 'accounts_name') : '-';

		$data['trx_expiration_date_c'] = $data['trx_subtype'] != 4 || !$data['trx_expiration_date'] ? '...' : $CMS->class->date->date_format($data['trx_expiration_date']);

		$data['transaction_item'] = $this->getItemAll($data['trx_id']);

		/*if (!empty($data['transaction_item'])) {
			$total = 0;
			foreach ($data['transaction_item'] as $item) {
				$total += ($item['tri_quantity'] * $item['tri_total']) + ($item['tri_quantity'] * $item['tri_total'] * $item['tri_tax'] / 100);
			}
			$data['trx_total_tax_c'] =  $CMS->class->input->currency($total);
		} else {
			$data['trx_total_tax_c'] =  $CMS->class->input->currency($data['trx_total'] + ($data['trx_total'] * $data['trx_tax'] / 100));
		}*/

        $data['trx_total_tax_c'] =  $CMS->class->input->currency($data['trx_total']);

        $data['style_color'] = '';

		if(in_array($data['trx_subtype'],[1,5]))
        {
            if($data_bk['trx_status'] == 2) //Quá hạn
            {
                $data['style_color'] = "color: red";
            }
            else if($data['trx_due_date'] > 0 && $data['trx_due_date'] < $today && $data['trx_status'] == 0)
            {
                $data['style_color'] = "color: red";
            }
        }

        $data['trx_status_color'] = $this->status_color[$data_bk['trx_status']];

		//Số tiền hoàn lại (Số dư thanh toán) của giao dịch credit memo
        if($data_bk['trx_subtype'] == 8)
        {
            $data['amount_to_refund'] = $data_bk['trx_total'] - $data_bk['trx_receive_payment'];
            $data['amount_to_refund_c'] =  $CMS->class->input->currency($data['amount_to_refund']);
        }

        if($data_bk['trx_subtype'] == 2)
        {
            $data['trx_remain'] = $data_bk['trx_receive_payment'] - $data_bk['trx_total'];
            $data['trx_remain_c'] =  $CMS->class->input->currency($data['trx_remain']);
        }
        else
        {
            $data['trx_remain'] = $data_bk['trx_total'] - $data_bk['trx_receive_payment'];
            $data['trx_remain_c'] =  $CMS->class->input->currency($data['trx_remain']);
        }

        $data['trx_receive_payment_c'] = $CMS->class->input->currency($data['trx_receive_payment']);

		return $data;
	}

    public function convertValueToExport($data = null) {
        global $CMS, $DB, $member;

        list($langData,$currentLang) = $this->loadLangForImportExport();

        $data['data_bk'] = $data_bk = $data;

        $dateFormat = 'Y-m-d';

        $data['trx_due_date'] = !in_array($data_bk['trx_subtype'], [1,5]) || ! $data_bk['trx_due_date'] ? '' : date::format($data_bk['trx_due_date'],$dateFormat);

        $data['trx_time'] = $data_bk['trx_time'] ? date::format($data_bk['trx_time'],$dateFormat) : '';
        $data['trx_time_update'] = $data_bk['trx_time_update'] ? $data_bk['trx_time_update'] : $data_bk['trx_time'];
        $data['trx_time_update'] == $data['trx_time_update'] ? date::format($data['trx_time_update'],$dateFormat)  : '';
        $data['trx_payment_date'] = $data_bk['trx_payment_date'] ? date::format($data_bk['trx_payment_date'],$dateFormat)  : '';
        $data['trx_estimate_date'] = $data_bk['trx_estimate_date'] ? date::format($data_bk['trx_estimate_date'],$dateFormat)  : '';
        $data['trx_accepted_date'] = $data_bk['trx_accepted_date'] ? date::format($data_bk['trx_estimate_date'],$dateFormat)  : '';
        $data['trx_expiration_date'] = $data_bk['trx_subtype'] != 4 || !$data_bk['trx_expiration_date'] ? '' : date::format($data_bk['trx_expiration_date']);
        $data['trx_subtype'] = $currentLang['sub_type_'.$data_bk['trx_subtype']];
        $prefix_trx = $data_bk['trx_type'] == 2 ? 'PAY' : 'INV';
        $data['trx_invoice_no'] = !empty($data_bk['trx_invoice_no']) && $data_bk['trx_invoice_no'] > 0 ? $prefix_trx.$data_bk['trx_invoice_no'] : '';
        $data['cus_id'] = $CMS->customer->getInfo($data_bk['cus_id'], 'cus_full_name');

        $data['trx_total_not_tax'] =  $data_bk['trx_total'] - $data_bk['trx_tax'];

        $today = $CMS->class->date->gmt($CMS->vars['this_day'],$CMS->vars['this_month'],$CMS->vars['this_year']);

        $data['trx_status'] = $currentLang['trx_status_'.$data_bk['trx_status']];
        $data['trx_estimate_status']  = $currentLang['trx_estimate_status_'.$data_bk['trx_estimate_status']];
        $data['payment_method'] = $currentLang['trx_method_'.$data_bk['trx_payment_method']];
        $data['account_name'] = !empty($data_bk['trx_account']) && intval($data_bk['trx_account']) > 0 ? $CMS->accounts->getInfo($data_bk['trx_account'], 'accounts_name') : '';

        $data['accounts_type_info'] = $CMS->accounts_type->getInfo($data['at_id']);

        $data['transaction_item'] = $this->getItemAll($data['trx_id'],'all');
        if (!empty($data['transaction_item'])) {
            $total = 0;
            foreach ($data['transaction_item'] as $item) {
                $total += ($item['tri_quantity'] * $item['tri_total']) + ($item['tri_quantity'] * $item['tri_total'] * $item['tri_tax'] / 100);
            }
            $data['trx_total_tax'] =  $total;
        } else {
            $data['trx_total_tax'] =  $data_bk['trx_total'] + ($data_bk['trx_total'] * $data_bk['trx_tax'] / 100);
        }

        $data['cus_type'] = $currentLang['cus_type_'.$data_bk['cus_type']];
        $data['reference_number'] = $data['trx_reference_no'];
        $data['contract_code'] = $data['trx_contract_code'];
        $data['payment_address'] = $data['trx_billing_address'];
        $data['accounting_account'] = $data['accounts_type_info']['accounts_type_name'];
        return $data;
    }

	public function getInfo($trx_id = null, $trx_type = null, $trx_sub = null, $field = '*') {
                
		if (!empty($trx_id)) {
			global $CMS, $DB, $member;
			//AND `trx_type`='{$trx_type}' AND `trx_subtype`='{$trx_sub}'
			$DB->query("SELECT {$field} FROM `".root_table."transaction` WHERE `trx_id`='{$trx_id}' AND `trx_deleted`=0 LIMIT 1");
			if ($DB->num_rows() > 0) {
				$data = $DB->fetch_array();
				if ($field !== '*') {
					return $data[$field];
				}
				return $data;
			}
		}
		return false;
	}
	public function getInfoItem($tri_id = null, $field = '*') {
		if (!empty($tri_id)) {
			global $CMS, $DB, $member;
                        
			$DB->query("SELECT {$field} FROM `".root_table."transaction_item` WHERE `tri_id`='{$tri_id}' AND `tri_deleted`=0 LIMIT 1");
			if ($DB->num_rows() > 0) {
				$data = $DB->fetch_array();
				if ($field !== '*') {
					return $data[$field];
				}
				return $data;
			}
		}
		return false;
	}
	public function getInvoiceEstimate($id = 0, $code_id = 0, $trx_type=1, $cus_type=1) {
		if (!empty($code_id)) {
			global $CMS, $DB, $member;

            $trx_type = intval($trx_type);

            $trx_subtype = $trx_type == 1 ? 1 : 5;

            $sql_add = "";

            if($cus_type == 1)
            {
                $sql_add = " AND `cus_id`='{$id}'";
            }
            elseif ($cus_type == 2)
            {
                $sql_add = " AND `supplier_id`='{$id}'";
            }
            elseif ($cus_type == 3)
            {
                $sql_add = " AND `user_assign`='{$id}'";
            }

			$DB->query("SELECT * FROM `".root_table."transaction` WHERE `trx_type`={$trx_type} AND `trx_subtype`= {$trx_subtype} AND `trx_status`=0 AND `trx_invoice_no`='{$code_id}' {$sql_add} AND `trx_deleted`=0 ORDER BY `trx_id` DESC LIMIT 1");

			if ($DB->num_rows() > 0) {
				return  $DB->fetch_array();
			} else {
			    if($trx_type == 1)
                {
                    $DB->query("SELECT * FROM `".root_table."transaction` WHERE `trx_type`={$trx_type} AND `trx_subtype`=4 AND `trx_estimate_status`=1 AND `trx_id`='{$code_id}' {$sql_add} AND `trx_deleted`=0 ORDER BY `trx_id` DESC LIMIT 1");
                    if ($DB->num_rows() > 0) {
                        return $DB->fetch_array();
                    }
                }
			}
		}
		return false;
	}
	public function getInvoices($cus_id = null, $trx_parent_id = 0) {
		if (!empty($cus_id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `trx_id`,`trx_total`,`trx_invoice_no`,`trx_subtype` FROM `".root_table."transaction` WHERE ((`trx_type`=1 AND `trx_subtype`=1 AND `trx_parent_id`='{$trx_parent_id}') OR (`trx_type`=1 AND `trx_subtype`=4 AND `trx_estimate_status`=1)) AND `cus_id`='{$cus_id}' AND `trx_deleted`=0 ORDER BY `trx_id` DESC");
			$arr = array();
			if ($DB->num_rows() > 0) {
				while ($result = $DB->fetch_array()) {
					array_push($arr, $result);
				}
			}
			return $arr;
		}
		return false;
	}
	public function getNoInvoice($trx_sub = null) {
		if (!empty($trx_sub)) {
			global $CMS, $DB, $member;

			$DB->query("SELECT MAX(`trx_invoice_no`) trx_invoice_no FROM `".root_table."transaction` WHERE `trx_subtype`='{$trx_sub}' AND `trx_deleted`=0");

			$no = 0;
			if ($DB->num_rows() > 0) {
				$no = $DB->fetch_array()['trx_invoice_no'];

			}
			$no += 1;
		 
			return $no;
		}
		return false;
	}
    public function getExpenseBill($cus_id = null, $trx_parent_id = 0) {
        if (!empty($cus_id)) {
            global $CMS, $DB, $member;
            $DB->query("SELECT * FROM `".root_table."transaction` WHERE `trx_type`=2 AND `trx_subtype`=5 AND `trx_parent_id`='{$trx_parent_id}' AND `cus_id`='{$cus_id}' AND `trx_deleted`=0 ORDER BY `trx_id` DESC");
            $arr = array();
            if ($DB->num_rows() > 0) {
                while ($result = $DB->fetch_array()) {
                    array_push($arr, $result);
                }
            }
            return $arr;
        }
        return false;
    }
    public function getUnbilledByCustomer($id = null, $trx_parent_id = 0, $trx_subtype=0, $cus_type=1, $inv_id=0) {
        global $CMS, $DB, $member;

        $sql = "";

        if (!empty($id)) {

            if($cus_type == 1)
            {
                $sql = "SELECT * FROM `".root_table."transaction` WHERE `trx_subtype`='{$trx_subtype}' AND `trx_parent_id`='{$trx_parent_id}' AND `cus_id`='{$id}' AND `trx_deleted`=0 AND trx_status=0 ORDER BY `trx_id` DESC";
            }
            elseif ($cus_type == 2)
            {
                $sql = "SELECT * FROM `".root_table."transaction` WHERE `trx_subtype`='{$trx_subtype}' AND `trx_parent_id`='{$trx_parent_id}' AND `supplier_id`='{$id}' AND `trx_deleted`=0 AND trx_status=0 ORDER BY `trx_id` DESC";
            }
            elseif ($cus_type == 3)
            {
                $sql = "SELECT * FROM `".root_table."transaction` WHERE `trx_subtype`='{$trx_subtype}' AND `trx_parent_id`='{$trx_parent_id}' AND `user_assign`='{$id}' AND `trx_deleted`=0 AND trx_status=0 ORDER BY `trx_id` DESC";
            }
        } elseif ($inv_id) {
            $sql = "SELECT * FROM `".root_table."transaction` WHERE `trx_subtype`='{$trx_subtype}' AND `trx_parent_id`='{$trx_parent_id}' AND `trx_id`='{$inv_id}' AND `trx_deleted`=0 AND trx_status=0 ORDER BY `trx_id` DESC";
        }

        if ($sql) {
            $DB->query($sql);
            $arr = array();
            if ($DB->num_rows() > 0) {
                while ($result = $DB->fetch_array()) {
                    array_push($arr, $result);
                }
            }
            return $arr;
        }

        return false;
    }
	public function getItemAll($trx_id = null, $type = 'product', $group_by = 1) {
		if (!empty($trx_id)) {
			global $CMS, $DB, $member;

			if($type == 'all')
            {
                if($group_by == 1)
                {
                    $DB->query("
                      SELECT *, SUM(tri_quantity) tri_quantity FROM `".root_table."transaction_item` WHERE `tri_deleted`=0 AND `trx_id`='{$trx_id}' AND product_id != 0 GROUP BY tri_description, tri_cycle_type, tri_cycle, tri_total, tri_tax,product_id
                      UNION ALL
                      SELECT *, SUM(tri_quantity) tri_quantity FROM `".root_table."transaction_item` WHERE `tri_deleted`=0 AND `trx_id`='{$trx_id}'  AND ass_key != '' AND ass_key IS NOT NULL GROUP BY tri_description, tri_cycle_type, tri_cycle, tri_total, tri_tax, ass_key
                  ");
                }
                else
                {
                    $DB->query("SELECT * FROM `".root_table."transaction_item` WHERE `tri_deleted`=0 AND `trx_id`='{$trx_id}'");
                }
            }
			else if($type == 'product')
            {
                if($group_by == 1)
                {
                    $DB->query("SELECT *, SUM(tri_quantity) tri_quantity FROM `".root_table."transaction_item` WHERE `tri_deleted`=0 AND `trx_id`='{$trx_id}' AND product_id != 0 GROUP BY tri_description, tri_cycle_type, tri_cycle, tri_total, tri_tax,product_id ");
                }
                else
                {
                    $DB->query("SELECT * FROM `".root_table."transaction_item` WHERE `tri_deleted`=0 AND `trx_id`='{$trx_id}' AND product_id != 0");
                }
            }
            else
            {
                if($group_by == 1)
                {
                    $DB->query("SELECT *, SUM(tri_quantity) tri_quantity FROM `".root_table."transaction_item` WHERE `tri_deleted`=0 AND `trx_id`='{$trx_id}'  AND ass_key != '' AND ass_key IS NOT NULL GROUP BY tri_description, tri_cycle_type, tri_cycle, tri_total, tri_tax, ass_key");
                }
                else
                {
                    $DB->query("SELECT * FROM `".root_table."transaction_item` WHERE `tri_deleted`=0 AND `trx_id`='{$trx_id}'  AND ass_key != '' AND ass_key IS NOT NULL");
                }
            }

			$arr = array();
			if ($DB->num_rows() > 0) {
				while ($result = $DB->fetch_array()) {
					array_push($arr, $result);
				}
			}
			return $arr;
		}
		return false;
	}
	public function add($data = [], $type_return = 0) {
		global $CMS, $DB, $member;
                
		$CMS->input = $data ? array_merge($CMS->input, $data) : $CMS->input;

		$trx_status = !empty($CMS->input['trx_status']) ? $CMS->input['trx_status'] : '0';
		$trx_type = !empty($CMS->input['type']) ? $CMS->input['type'] : '0';
		$trx_subtype = !empty($CMS->input['sub']) ? $CMS->input['sub'] : '0';
		$trx_parent_id = !empty($CMS->input['trx_parent_id']) ? $CMS->input['trx_parent_id'] : '0';
		$trx_invoice_no = !empty($CMS->input['trx_invoice_no']) ? $CMS->input['trx_invoice_no'] : '0';
		$trx_contract_code = !empty($CMS->input['trx_contract_code']) ? $CMS->input['trx_contract_code'] : '';
		$trx_billing_address = !empty($CMS->input['trx_address']) ? $CMS->input['trx_address'] : '';
		$trx_terms = !empty($CMS->input['trx_terms']) ? $CMS->input['trx_terms'] : '0';
		$trx_estimate_status = !empty($CMS->input['trx_estimate_status']) ? $CMS->input['trx_estimate_status'] : '0';
		$trx_bill_no = !empty($CMS->input['trx_bill_no']) ? $CMS->input['trx_bill_no'] : '0';
		$trx_payment_date = !empty($CMS->input['trx_payment_date']) ? $CMS->input['trx_payment_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
        $trx_accepted_date = !empty($CMS->input['trx_accepted_date']) ? $CMS->input['trx_accepted_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
		$trx_payment_method = !empty($CMS->input['trx_method']) ? $CMS->input['trx_method'] : '0';
		$trx_account = !empty($CMS->input['trx_account']) ? $CMS->input['trx_account'] : '0';
		$trx_tax = !empty($CMS->input['trx_tax']) ? $CMS->input['trx_tax'] : '0';
		$trx_total = !empty($CMS->input['trx_total']) ? $CMS->input['trx_total'] : '0';
        $trx_amount = !empty($CMS->input['trx_amount']) ? $CMS->input['trx_amount'] : '0';
        $trx_receive_payment = !empty($CMS->input['trx_receive_payment']) ? $CMS->input['trx_receive_payment'] : '0';
        $trx_total_discount = !empty($CMS->input['trx_total_discount']) ? $CMS->input['trx_total_discount'] : '0';
        $trx_discount_type = !empty($CMS->input['trx_discount_type']) ? $CMS->input['trx_discount_type'] : '0';
        $trx_discount_value = !empty($CMS->input['trx_discount_value']) ? $CMS->input['trx_discount_value'] : '0';
		$trx_expiration_date = !empty($CMS->input['trx_expiration_date']) ? $CMS->input['trx_expiration_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
        $trx_estimate_date = !empty($CMS->input['trx_estimate_date']) ? $CMS->input['trx_estimate_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
        $trx_due_date = !empty($CMS->input['trx_due_date']) ? $CMS->input['trx_due_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
        $trx_credit_memo_date = !empty($CMS->input['trx_credit_memo_date']) ? $CMS->input['trx_credit_memo_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);

		$cus_id = !empty($CMS->input['cus_id']) ? $CMS->input['cus_id'] : '0';
		$cus_email = !empty($CMS->input['trx_email']) ? $CMS->input['trx_email'] : '';
		$cus_email_cc = !empty($CMS->input['cus_email_cc']) ? $CMS->input['cus_email_cc'] : '';
		$cus_email_bcc = !empty($CMS->input['cus_email_bcc']) ? $CMS->input['cus_email_bcc'] : '';
		$trx_note = !empty($CMS->input['trx_note']) ? $CMS->input['trx_note'] : '';
		$trx_msg = !empty($CMS->input['trx_msg']) ? $CMS->input['trx_msg'] : '';
		$trx_reference_no = !empty($CMS->input['trx_reference_no']) ? $CMS->input['trx_reference_no'] : '';
		$trx_attachments = !empty($CMS->input['trx_attachments']) ? $CMS->input['trx_attachments'] : '';
		$trx_accepted_by = !empty($CMS->input['trx_accepted_by']) ? $CMS->input['trx_accepted_by'] : '';
		$trx_term_name = !empty($CMS->input['trx_term_name']) ? $CMS->input['trx_term_name'] : '';
		$trx_payment_date = $trx_payment_date ? $CMS->class->date->date2time($trx_payment_date) : 0;
		$trx_expiration_date = $trx_expiration_date ?  $CMS->class->date->date2time($trx_expiration_date) : 0;
        $trx_estimate_date = $trx_estimate_date ? $CMS->class->date->date2time($trx_estimate_date) : 0;
        $trx_accepted_date = $trx_accepted_date ? $CMS->class->date->date2time($trx_accepted_date) : 0;
        $trx_due_date = $trx_due_date ? $CMS->class->date->date2time($trx_due_date) : 0;
        $trx_credit_memo_date = $trx_credit_memo_date ? $CMS->class->date->date2time($trx_credit_memo_date) : 0;

		$cus_type = isset($CMS->input['cus_type']) && intval($CMS->input['cus_type']) ? intval($CMS->input['cus_type']) : 1;
		$supplier_id = isset($CMS->input['supplier_id']) && intval($CMS->input['supplier_id']) ? intval($CMS->input['supplier_id']) : 0;
		$user_assign = isset($CMS->input['user_assign']) && intval($CMS->input['user_assign']) ? intval($CMS->input['user_assign']) : 0;
		$request_id = isset($CMS->input['request_id']) && intval($CMS->input['request_id']) ? intval($CMS->input['request_id']) : 0;
		$ret_id = isset($CMS->input['ret_id']) && intval($CMS->input['ret_id']) ? intval($CMS->input['ret_id']) : 0;

		$at_id = isset($CMS->input['at_id']) ? intval($CMS->input['at_id']) : 0;

		$user_id = isset($member['user_id']) ? $member['user_id'] : 0;

		$trx_invoice_info = '';

		if($trx_subtype == 2 || $trx_subtype == 7) //receive payment || bill payment
        {
            $trx_amount = $trx_total;
            $trx_invoice_info = !empty($CMS->input['tri_payment']) ? \lib\input::jsonEncode($CMS->input['tri_payment'],0) : '';
        }

        if($trx_subtype == 1)
        {
            $trx_excess_cash = !empty($CMS->input['excess_cash']) ? $CMS->input['excess_cash'] : '0';
        }
        else
        {
            $trx_excess_cash = 0;
        }


        $store_id = input::get('store_id') * 1;
        $trx_booking_phone = input::get('trx_booking_phone','');

		$trx_time_update =  time();

		$sql = "INSERT INTO `".root_table."transaction` (`trx_status`, `trx_type`, `trx_subtype`, `trx_parent_id`, `trx_invoice_no`, `trx_contract_code`, `trx_billing_address`, `trx_terms`, `trx_estimate_status`, `trx_bill_no`, `trx_payment_date`, `trx_payment_method`, `trx_account`, `trx_tax`, `trx_total`, `trx_expiration_date`, `cus_id`, `cus_email`, `cus_email_cc`, `cus_email_bcc`, `trx_note`, `trx_msg`, `trx_attachments`, `user_id`, `trx_time`, cus_type, supplier_id, trx_reference_no, trx_amount, trx_discount_type, trx_discount_value, user_assign, trx_estimate_date, trx_accepted_date,trx_accepted_by, request_id, ret_id, trx_due_date, trx_term_name, at_id, trx_time_update, trx_total_discount, trx_credit_memo_date, trx_receive_payment, trx_invoice_info,trx_excess_cash,store_id,trx_booking_phone) VALUES ('{$trx_status}','{$trx_type}','{$trx_subtype}','{$trx_parent_id}','{$trx_invoice_no}','{$trx_contract_code}','{$trx_billing_address}','{$trx_terms}','{$trx_estimate_status}','{$trx_bill_no}','{$trx_payment_date}','{$trx_payment_method}','{$trx_account}','{$trx_tax}','{$trx_total}','{$trx_expiration_date}','{$cus_id}','{$cus_email}','{$cus_email_cc}','{$cus_email_bcc}','{$trx_note}','{$trx_msg}','{$trx_attachments}','{$user_id}','".time()."', '{$cus_type}', '{$supplier_id}', '{$trx_reference_no}', '{$trx_amount}', '{$trx_discount_type}', '{$trx_discount_value}', '{$user_assign}', '{$trx_estimate_date}', '{$trx_accepted_date}', '{$trx_accepted_by}', '{$request_id}', '{$ret_id}', '{$trx_due_date}', '{$trx_term_name}', '{$at_id}', '{$trx_time_update}', '{$trx_total_discount}','{$trx_credit_memo_date}', '{$trx_receive_payment}', '{$trx_invoice_info}','{$trx_excess_cash}',{$store_id},'{$trx_booking_phone}')";

		$DB->query($sql);

		$id = $DB->last_insert_id();

		$_SESSION['highlight']['trx'] =  $id;

		$this->update_transaction_code();
		$CMS->customer->update_last_action_time($cus_id);

		$inserted_record = $this->getInfo($id);

		$this->updateInfoInvoice($inserted_record); //Check ràng buộc và cập nhật lại thông tin cho invoice
		$this->updateInfoReceive($inserted_record); //Check ràng buộc và cập nhật lại thông tin cho receive
		$this->updateInfoCreditMemo($inserted_record); //Check ràng buộc và cập nhật lại thông tin cho credit memo

		$CMS->class->logs->key = "transaction_{$id}";

        $CMS->lang['trx_add_success_'.$CMS->input['sub']] = isset($CMS->lang['trx_add_success_'.$CMS->input['sub']]) ? $CMS->lang['trx_add_success_'.$CMS->input['sub']] : '';
        $CMS->lang['trx_status_0'.$inserted_record['trx_status']] =  isset($CMS->lang['trx_status_0'.$inserted_record['trx_status']]) ? $CMS->lang['trx_status_0'.$inserted_record['trx_status']] : '';

        $CMS->class->logs->insert("{$CMS->lang['trx_add_success_'.$CMS->input['sub']]} - {$inserted_record['trx_code']} - {$CMS->lang['trx_status_0'.$inserted_record['trx_status']]}");

        $CMS->class->logs->key = "transaction_{$id}";

        if(isset($CMS->vars['trx_is_accepted']) && $CMS->vars['trx_is_accepted']) //Dự kiến accepted
        {
            $CMS->class->logs->insert("{$CMS->lang['accepted_success']} {$inserted_record['trx_accepted_by']} {$CMS->class->date->date_format($inserted_record['trx_accepted_date'])} - {$inserted_record['trx_code']} - {$CMS->lang['trx_status_0'.$inserted_record['trx_status']]}");
        }
		else if($trx_subtype == 4) //Dự kiến
        {
            if($trx_status == 3) //closed
            {
                $CMS->class->logs->insert("{$CMS->lang['closed_success']} {$inserted_record['trx_accepted_by']} {$CMS->class->date->date_format($inserted_record['trx_accepted_date'])} - {$inserted_record['trx_code']} - {$CMS->lang['trx_status_0'.$inserted_record['trx_status']]}");
            }
            else if($trx_status == 4) //rejected
            {
                $CMS->class->logs->insert("{$CMS->lang['rejected_success']} {$inserted_record['trx_accepted_by']} {$CMS->class->date->date_format($inserted_record['trx_accepted_date'])} - {$inserted_record['trx_code']} - {$CMS->lang['trx_status_0'.$inserted_record['trx_status']]}");
            }
        }
        
        // Update transaction balance - hvu 03/07/2017
        $this->transaction_balance($id);

        //UPDATE BALANCE ACCOUNT
        $CMS->accounts->update_balance($inserted_record['trx_account']);

		if($type_return)
		{
			return $id;
		}else
		{
			return $inserted_record;
		}
	}
        
	public function addItem($trx_id = null, $data=array(), $type = 0) {

		if (!empty($trx_id)) {
			global $CMS, $DB, $member;

			$CMS->input = $data ? array_merge($CMS->input, $data) : $CMS->input;

			switch ($CMS->input['sub']) {
                case 5:
                case 4:
                case 3:
                case 6:
				case 1:
				case 8:
					$tri_ordi_id = !empty($CMS->input['product_item_id']) ? $CMS->input['product_item_id'] : array();
					$tri_name = !empty($CMS->input['product_name']) ? $CMS->input['product_name'] : array();
					$tri_description = !empty($CMS->input['product_description']) ? $CMS->input['product_description'] : array();
					$tri_quantity = !empty($CMS->input['product_quantity']) ? $CMS->input['product_quantity'] : array();
					$tri_price = !empty($CMS->input['product_price']) ? $CMS->input['product_price'] : array();
					$tri_old_price = !empty($CMS->input['product_old_price']) ? $CMS->input['product_old_price'] : array();
					$tri_tax = !empty($CMS->input['product_tax']) ? $CMS->input['product_tax'] : array();
					$product_id = !empty($CMS->input['product_id']) ? $CMS->input['product_id'] : array();
                    $tri_cycle = !empty($CMS->input['product_cycle']) ? $CMS->input['product_cycle'] : array();
                    $tri_cycle_type = !empty($CMS->input['product_cycle_type']) ? $CMS->input['product_cycle_type'] : array();
                    $tri_type = !empty($CMS->input['product_type']) ? $CMS->input['product_type'] : array();
                    $tri_discount_type = !empty($CMS->input['product_discount_type']) ? $CMS->input['product_discount_type'] : array();
                    $tri_discount_value = !empty($CMS->input['product_discount_value']) ? $CMS->input['product_discount_value'] : array();
                    $tri_booking_time = !empty($CMS->input['product_booking_time']) ? $CMS->input['product_booking_time'] : array();
                    $tri_staff = !empty($CMS->input['product_staff']) ? $CMS->input['product_staff'] : array();


					//
					$ass_ordi_id = !empty($CMS->input['ass_item_id']) ? $CMS->input['ass_item_id'] : array();
                    $ass_name = !empty($CMS->input['ass_name']) ? $CMS->input['ass_name'] : array();
                    $ass_description = !empty($CMS->input['ass_description']) ? $CMS->input['ass_description'] : array();
                    $ass_quantity = !empty($CMS->input['ass_quantity']) ? $CMS->input['ass_quantity'] : array();
                    $ass_price = !empty($CMS->input['ass_price']) ? $CMS->input['ass_price'] : array();
                    $ass_tax = !empty($CMS->input['ass_tax']) ? $CMS->input['ass_tax'] : array();
                    $ass_key = !empty($CMS->input['ass_key']) ? $CMS->input['ass_key'] : array();
                    $ass_discount_type = !empty($CMS->input['ass_discount_type']) ? $CMS->input['ass_discount_type'] : array();
                    $ass_discount_value = !empty($CMS->input['ass_discount_value']) ? $CMS->input['ass_discount_value'] : array();
                    $ass_booking_time = !empty($CMS->input['ass_booking_time']) ? $CMS->input['ass_booking_time'] : array();
                    $ass_staff = !empty($CMS->input['ass_staff']) ? $CMS->input['ass_staff'] : array();
					break;
                case 7:
				case 2:
					$list_item = array();
					foreach ($_POST['item'] as $k => $v) {
						if (is_array($v)) {
							foreach ($v as $k1 => $v1) {
								if ($v1 == 'on') {
									$list_item[$k] = is_array($list_item[$k]) ? $list_item[$k] : array();
									array_push($list_item[$k], $k1);
								}
							}
						} else {
							if ($v == 'on') {
								$list_item[$k] = array();
							}
						}
					}
					foreach ($list_item as $k => $item) {
						$tri_name = is_array($tri_name) ? $tri_name : array();
						$tri_description = is_array($tri_description) ? $tri_description : array();
						$tri_quantity = is_array($tri_quantity) ? $tri_quantity : array();
						$tri_price = is_array($tri_price) ? $tri_price : array();
						$tri_old_price = is_array($tri_old_price) ? $tri_old_price : array();

						$tri_tax = is_array($tri_tax) ? $tri_tax : array();
						$product_id = is_array($product_id) ? $product_id : array();
						$tri_cycle = is_array($tri_cycle) ? $tri_cycle : array();
						$tri_cycle_type = is_array($tri_cycle_type) ? $tri_cycle_type : array();
						$tri_type = is_array($tri_type) ? $tri_type : array();
						$tri_invoice_no  = is_array($tri_invoice_no) ? $tri_invoice_no : array();
                        $tri_discount_type = is_array($tri_discount_type) ? $tri_discount_type : array();
                        $tri_discount_value = is_array($tri_discount_value) ? $tri_discount_value : array();
                        $tri_booking_time = is_array($tri_booking_time) ? $tri_booking_time : array();
                        $tri_staff = is_array($tri_staff) ? $tri_staff : array();

						if (is_array($item)) {
							foreach ($item as $i) {
								$tmp = $this->getInfoItem($i);
								array_push($tri_name, $tmp['tri_name']);
								array_push($tri_description, $tmp['tri_description']);
								array_push($tri_quantity, $tmp['tri_quantity']);
								array_push($tri_price, $tmp['tri_price']);
								array_push($tri_old_price, $tmp['tri_old_price']);

								array_push($tri_tax, $tmp['tri_tax']);
								array_push($product_id, $tmp['product_id']);
								array_push($tri_invoice_no, $tmp['tri_invoice_no']);
								array_push($tri_cycle, $tmp['tri_cycle']);
								array_push($tri_cycle_type, $tmp['tri_cycle_type']);
								array_push($tri_type, $tmp['tri_type']);

                                array_push($tri_discount_type, $tmp['tri_discount_type']);
                                array_push($tri_discount_value, $tmp['tri_discount_value']);

                                array_push($tri_booking_time, $tmp['tri_booking_time']);
                                array_push($tri_staff, $tmp['tri_staff']);
							}
						}
						$this->updateReceiveChildren($k, $item);
					}
					break;
				default:
					break;
			}

			$productsTmp  = [];

			foreach ($tri_name as $k => $v) {
				if (!empty($tri_name[$k])  ) {

					$tri_quantity[$k] = isset($tri_quantity[$k]) && $tri_quantity[$k] * 1 ? $tri_quantity[$k] * 1 : 1;
                    $tri_tax[$k] = isset($tri_tax[$k]) ? ($tri_tax[$k] * 1) : 0;
                    $product_id[$k] = isset($product_id[$k]) ? $product_id[$k] * 1 : 0;
                    $tri_cycle[$k] = isset($tri_cycle[$k]) && $tri_cycle[$k] * 1 ? $tri_cycle[$k] * 1 : 1;
                    $tri_type[$k] = isset($tri_type[$k]) ? $tri_type[$k] * 1 : 0;
                    if(!isset($productsTmp[$product_id[$k]]))
                    {
                        $productsTmp[$product_id[$k]] = $CMS->product->getInfo($product_id[$k]);
                    }
                    $tri_cycle_type[$k] = $productsTmp[$product_id[$k]]['product_cycle'];

                    $tri_price[$k] = isset($tri_price[$k]) ? $tri_price[$k] : 0;
                    $tri_old_price[$k] = isset($tri_old_price[$k]) ? $tri_old_price[$k] : 0;
                    $tri_discount_type[$k] = isset($tri_discount_type[$k]) ? $tri_discount_type[$k] : 0;
                    $tri_discount_value[$k] = isset($tri_discount_value[$k]) ? $tri_discount_value[$k] : 0;
                    $tri_description[$k] = isset($tri_description[$k]) ? $tri_description[$k] : '';

                    $tri_booking_time[$k] = isset($tri_booking_time[$k]) ? $tri_booking_time[$k] : 0;
                    $tri_staff[$k] = isset($tri_staff[$k]) ? $tri_staff[$k] : 0;

                    $calculateItem = $this->calculateItem($tri_old_price[$k] > $tri_price[$k] ? $tri_old_price[$k] : $tri_price[$k], $tri_discount_type[$k], $tri_discount_value[$k], $tri_tax[$k], $tri_quantity[$k], $tri_cycle[$k]);

                    $tri_total[$k] = $calculateItem['total'];
                    $tri_subtotal[$k] = $calculateItem['subTotal'];
                    $tri_total_tax[$k] = $calculateItem['totalTax'];
                    $tri_total_discount[$k] = $calculateItem['totalDiscount'];

                    //	$tri_invoice_no[$k] = Validate::isNum($tri_invoice_no[$k]) && $tri_invoice_no[$k] > 0 ? $tri_invoice_no[$k] : '0';

                    $member['user_id'] = isset($member['user_id']) ? $member['user_id'] : 0;

                    $DB->query("INSERT INTO `".root_table."transaction_item` (`trx_id`, `tri_name`, `tri_description`, `tri_quantity`, `tri_price`, `tri_tax`, `product_id`,   `user_id`, `tri_time`, `tri_cycle`, `tri_cycle_type`, `tri_type`, tri_discount_type, tri_discount_value, tri_total_discount, tri_subtotal, tri_total_tax, tri_total, tri_old_price, tri_booking_time, tri_staff) VALUES ('{$trx_id}', '{$tri_name[$k]}', '{$tri_description[$k]}', {$tri_quantity[$k]}, '{$tri_price[$k]}', '{$tri_tax[$k]}', '{$product_id[$k]}', '{$member['user_id']}', '".time()."', {$tri_cycle[$k]}, '{$tri_cycle_type[$k]}', '{$tri_type[$k]}', '{$tri_discount_type[$k]}', '{$tri_discount_value[$k]}', '{$tri_total_discount[$k]}', '{$tri_subtotal[$k]}', '{$tri_total_tax[$k]}', '{$tri_total[$k]}', '$tri_old_price[$k]', '{$tri_booking_time[$k]}', '{$tri_staff[$k]}')");


                    $id = $DB->last_insert_id();
                    //Check nếu có ordi_id (cua order item) thi update tri_id qua table order_item
                    if(isset($tri_ordi_id[$k]) AND $tri_ordi_id[$k] != "" AND $tri_ordi_id[$k] > 0)
                    {
                        $DB->query("UPDATE ".root_table."order_item SET tri_id = '{$id}' WHERE ordi_id='{$tri_ordi_id[$k]}'  ");
                    }
                    $CMS->class->logs->insert("Add_transactions_item_{$id}");
				}
			}

            foreach ($ass_name as $k => $v) {
                if (!empty($ass_name[$k])  ) {
                    $ass_quantity[$k] = Validate::isNum($ass_quantity[$k]) && $ass_quantity[$k] > 0 ? $ass_quantity[$k] : '1';
                    $ass_tax[$k] = Validate::isNum($ass_tax[$k]) && $ass_tax[$k] > 0 ? $ass_tax[$k] : '0';
//                    $ass_id[$k] = Validate::isNum($ass_id[$k]) && $ass_id[$k] > 0 ? $ass_id[$k] : '0';
                    $ass_key[$k] = trim($ass_key[$k]);
                    //  $tri_invoice_no[$k] = Validate::isNum($tri_invoice_no[$k]) && $tri_invoice_no[$k] > 0 ? $tri_invoice_no[$k] : '0';

                    $ass_booking_time[$k] = isset($ass_booking_time[$k]) ? $ass_booking_time[$k] : 0;
                    $ass_staff[$k] = isset($ass_staff[$k]) ? $ass_staff[$k] : 0;

                    $calculateItem = $this->calculateItem($ass_price[$k], $ass_discount_type[$k], $ass_discount_value[$k], $ass_tax[$k], $ass_quantity[$k], 1);

                    $ass_total[$k] = $calculateItem['total'];
                    $ass_subtotal[$k] = $calculateItem['subTotal'];
                    $ass_total_tax[$k] = $calculateItem['totalTax'];
                    $ass_total_discount[$k] = $calculateItem['totalDiscount'];

                    $DB->query("INSERT INTO `".root_table."transaction_item` (`trx_id`, `tri_name`, `tri_description`, `tri_quantity`, `tri_price`, `tri_tax`, `ass_key`,   `user_id`, `tri_time`, `tri_cycle`, `tri_cycle_type`, `tri_type`, tri_discount_type, tri_discount_value, tri_total_discount, tri_subtotal, tri_total_tax, tri_total, tri_booking_time, tri_staff) VALUES ('{$trx_id}', '{$ass_name[$k]}', '{$ass_description[$k]}', {$ass_quantity[$k]}, '{$ass_price[$k]}', '{$ass_tax[$k]}', '{$ass_key[$k]}', '{$member['user_id']}', '".time()."', 1, 0, 2, '{$ass_discount_type[$k]}', '{$ass_discount_value[$k]}', '{$ass_total_discount[$k]}', '{$ass_subtotal[$k]}', '{$ass_total_tax[$k]}', '{$ass_total[$k]}', '{$ass_booking_time[$k]}', '{$ass_staff[$k]}')");
                    $id = $DB->last_insert_id();
                    if($ass_ordi_id[$k] != "" AND $ass_ordi_id[$k] > 0)
                    {
                        $DB->query("UPDATE ".root_table."order_item SET tri_id = '{$id}' WHERE ordi_id='{$ass_ordi_id[$k]}'  ");
                    }
                    $CMS->class->logs->insert("Add_transactions_item_{$id}");
                }
            }

            // Neu type == 1 , tu ben order goi qua, thi k goi ham nay
          	if($type == 0)
          	{
          		 $CMS->order->add_ord_item($trx_id );
          	}

			//UPDATE trx_items
            $tri_name = array_unique(array_values($tri_name));
            $ass_name = array_unique(array_values($ass_name));

            $trx_items = array_merge($tri_name, $ass_name);
            $trx_items = json_encode($trx_items);
            $sql_update_trx_items = "UPDATE ".root_table."transaction SET trx_items='{$trx_items}' WHERE trx_id = {$trx_id}";

            $DB->query($sql_update_trx_items);
        
			return true;
		}
		return false;
	}
	public function clearItem($trx_id = null) {
		if (!empty($trx_id)) {
			global $CMS, $DB, $member;
			$DB->query("DELETE FROM `".root_table."transaction_item` WHERE `trx_id` = '{$trx_id}'");
			return true;
		}
		return false;
	}
	public function edit($data_info = null) {
            
		if (!empty($data_info)) {
			global $CMS, $DB, $member;

			$CMS->input = array_merge($data_info, $CMS->input);

			$old_data = $this->getInfo($data_info['trx_id']);

			$key = "transaction_{$data_info['trx_id']}";
			$CMS->class->logs->key = $key;
			$CMS->class->logs->old_data = $data_info;
			$trx_status = !empty($CMS->input['trx_status']) ? $CMS->input['trx_status'] : $data_info['trx_status'];
			$trx_type = !empty($CMS->input['type']) ? $CMS->input['type'] : '0';
			$trx_subtype = !empty($CMS->input['sub']) ? $CMS->input['sub'] : '0';
			$trx_parent_id = !empty($CMS->input['trx_parent_id']) ? $CMS->input['trx_parent_id'] : '0';
			$trx_invoice_no = !empty($CMS->input['trx_invoice_no']) ? $CMS->input['trx_invoice_no'] : $data_info['trx_invoice_no'];
			$trx_contract_code = !empty($CMS->input['trx_contract_code']) ? $CMS->input['trx_contract_code'] : '';
			$trx_billing_address = !empty($CMS->input['trx_address']) ? $CMS->input['trx_address'] : '';
			$trx_accepted_by = !empty($CMS->input['trx_accepted_by']) ? $CMS->input['trx_accepted_by'] : '';
			$trx_term_name = !empty($CMS->input['trx_term_name']) ? $CMS->input['trx_term_name'] : '';
			$trx_terms = !empty($CMS->input['trx_terms']) ? $CMS->input['trx_terms'] : '0';
			$trx_estimate_status = !empty($CMS->input['trx_estimate_status']) ? $CMS->input['trx_estimate_status'] : '0';
			$trx_bill_no = !empty($CMS->input['trx_bill_no']) ? $CMS->input['trx_bill_no'] : '0';
			$trx_payment_date = !empty($CMS->input['trx_payment_date']) ? $CMS->input['trx_payment_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);

            $trx_receive_payment = isset($CMS->input['trx_receive_payment']) ? $CMS->input['trx_receive_payment'] : $data_info['trx_receive_payment'];

			$trx_payment_method = !empty($CMS->input['trx_method']) ? $CMS->input['trx_method'] : '0';
			$trx_account = !empty($CMS->input['trx_account']) ? $CMS->input['trx_account'] : '0';
			$trx_tax = !empty($CMS->input['trx_tax']) ? $CMS->input['trx_tax'] : '0';
			$trx_total = !empty($CMS->input['trx_total']) ? $CMS->input['trx_total'] : '0';
            $trx_amount = !empty($CMS->input['trx_amount']) ? $CMS->input['trx_amount'] : '0';
            $trx_total_discount = !empty($CMS->input['trx_total_discount']) ? $CMS->input['trx_total_discount'] : '0';
            $trx_discount_type = !empty($CMS->input['trx_discount_type']) ? $CMS->input['trx_discount_type'] : '0';
            $trx_discount_value = !empty($CMS->input['trx_discount_value']) ? $CMS->input['trx_discount_value'] : '0';
			$trx_expiration_date = !empty($CMS->input['trx_expiration_date']) ? $CMS->input['trx_expiration_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
            $trx_estimate_date = !empty($CMS->input['trx_estimate_date']) ? $CMS->input['trx_estimate_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
            $trx_accepted_date = !empty($CMS->input['trx_accepted_date']) ? $CMS->input['trx_accepted_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
            $trx_due_date = !empty($CMS->input['trx_due_date']) ? $CMS->input['trx_due_date'] : date($CMS->vars['dateformat_php'][$CMS->vars['date_format']]);
			$cus_id = !empty($CMS->input['cus_id']) ? $CMS->input['cus_id'] : '0';
			$cus_email = !empty($CMS->input['trx_email']) ? $CMS->input['trx_email'] : '';
			$cus_email_cc = empty($CMS->input['cus_email_cc']) ? $CMS->input['cus_email_cc'] : '';
			$cus_email_bcc = !empty($CMS->input['cus_email_bcc']) ? $CMS->input['cus_email_bcc'] : '';
			$trx_note = !empty($CMS->input['trx_note']) ? $CMS->input['trx_note'] : '';
            $trx_msg = !empty($CMS->input['trx_msg']) ? $CMS->input['trx_msg'] : '';
			$trx_reference_no = !empty($CMS->input['trx_reference_no']) ? $CMS->input['trx_reference_no'] : '';
			$trx_attachments = !empty($CMS->input['trx_attachments']) ? $CMS->input['trx_attachments'] : '';
			$trx_payment_date = $trx_payment_date ? $CMS->class->date->date2time($trx_payment_date) : 0;
			$trx_expiration_date = $trx_expiration_date ? $CMS->class->date->date2time($trx_expiration_date) : 0;
            $trx_estimate_date = $trx_estimate_date ? $CMS->class->date->date2time($trx_estimate_date) : 0;
            $trx_accepted_date = $trx_accepted_date ? $CMS->class->date->date2time($trx_accepted_date) : 0;
            $trx_due_date = $trx_due_date ? $CMS->class->date->date2time($trx_due_date) : 0;
            $supplier_id = !empty($CMS->input['supplier_id']) ? $CMS->input['supplier_id'] : 0;
            $user_assign = !empty($CMS->input['user_assign']) ? $CMS->input['user_assign'] : 0;
            $cus_type = !empty($CMS->input['cus_type']) ? $CMS->input['cus_type'] : 0;
            $store_id = !empty($CMS->input['store_id']) ? $CMS->input['store_id'] : 0;
            $at_id = intval($CMS->input['at_id']);

            if($trx_subtype == 2) //receive payment
            {
                $trx_invoice_info = !empty($CMS->input['tri_payment']) ? \lib\input::jsonEncode($CMS->input['tri_payment'],0) : '';
            }

            $trx_time_update =  time();

			$DB->query("UPDATE `".root_table."transaction` SET  `trx_status`='{$trx_status}', `trx_type`='{$trx_type}', `trx_subtype`='{$trx_subtype}', `trx_parent_id`='{$trx_parent_id}', `trx_invoice_no`='{$trx_invoice_no}', `trx_contract_code`='{$trx_contract_code}', `trx_billing_address`='{$trx_billing_address}', `trx_terms`='{$trx_terms}', `trx_estimate_status`='{$trx_estimate_status}', `trx_bill_no`='{$trx_bill_no}', `trx_payment_date`='{$trx_payment_date}', `trx_payment_method`='{$trx_payment_method}', `trx_account`='{$trx_account}', `trx_tax`='{$trx_tax}', `trx_total`='{$trx_total}', `trx_expiration_date`='{$trx_expiration_date}', `cus_id`='{$cus_id}', `cus_email`='{$cus_email}', `cus_email_cc`='', `cus_email_bcc`='', `trx_note`='{$trx_note}', `trx_msg`='{$trx_msg}', `trx_attachments`='{$trx_attachments}', `trx_reference_no`='{$trx_reference_no}', trx_amount='{$trx_amount}', trx_discount_type='{$trx_discount_type}', trx_discount_value='{$trx_discount_value}', trx_estimate_date='{$trx_estimate_date}', trx_accepted_date='{$trx_accepted_date}', trx_accepted_by='{$trx_accepted_by}', trx_due_date='{$trx_due_date}', cus_type='{$cus_type}', supplier_id='{$supplier_id}', user_assign='{$user_assign}', trx_term_name='{$trx_term_name}', at_id='{$at_id}', trx_time_update='{$trx_time_update}', trx_total_discount='{$trx_total_discount}', trx_invoice_info='{$trx_invoice_info}', trx_receive_payment='{$trx_receive_payment}', store_id='{$store_id}'  WHERE `trx_id`='{$data_info['trx_id']}' AND `trx_deleted`=0");

            $CMS->customer->update_last_action_time($cus_id);

            $trx_info = $this->getInfo($data_info['trx_id'], $data_info['trx_type'], $data_info['trx_subtype']);

            $this->updateInfoInvoice($trx_info); //Check ràng buộc và cập nhật lại thông tin cho invoice
            $this->updateInfoReceive($trx_info, $old_data); //Check ràng buộc và cập nhật lại thông tin cho receive
            $this->updateInfoCreditMemo($trx_info); //Check ràng buộc và cập nhật lại thông tin cho credit memo

            //Cập nhật order
            $CMS->order->update_order($trx_info);
            
            // Update transaction balance - hvu 03/07/2017
            $this->transaction_balance($data_info['trx_id'],1,$old_data['trx_balance']);

            //UPDATE BALANCE ACCOUNT
            $CMS->accounts->update_balance($trx_info['trx_account']);

            if($old_data['trx_account'] != $trx_info['trx_account'])
            {
                $CMS->accounts->update_balance($old_data['trx_account']);
            }

			$CMS->class->logs->key = $key;

            if($CMS->vars['trx_is_accepted']) //Dự kiến accepted
            {
                $CMS->class->logs->insert("{$CMS->lang['accepted_success']} {$trx_info['trx_accepted_by']} {$CMS->class->date->date_format($trx_info['trx_accepted_date'])} - {$trx_info['trx_code']} - {$CMS->lang['trx_status_0'.$trx_info['trx_status']]}");
            }
            else if($trx_subtype == 4) //Dự kiến
            {
                if($trx_status == 3) //closed
                {
                    $CMS->class->logs->insert("{$CMS->lang['closed_success']} {$trx_info['trx_accepted_by']} {$CMS->class->date->date_format($trx_info['trx_accepted_date'])} - {$trx_info['trx_code']} - {$CMS->lang['trx_status_0'.$trx_info['trx_status']]}");
                }
                else if($trx_status == 4) //rejected
                {
                    $CMS->class->logs->insert("{$CMS->lang['rejected_success']} {$trx_info['trx_accepted_by']} {$CMS->class->date->date_format($trx_info['trx_accepted_date'])} - {$trx_info['trx_code']} - {$CMS->lang['trx_status_0'.$trx_info['trx_status']]}");
                }
                else
                {
                    $CMS->class->logs->insert("{$CMS->lang['trx_edit_success_'.$CMS->input['sub']]} - {$trx_info['trx_code']} - {$CMS->lang['trx_status_0'.$trx_info['trx_status']]}");
                }
            }
            else
            {
                $CMS->class->logs->insert("{$CMS->lang['trx_edit_success_'.$CMS->input['sub']]} - {$trx_info['trx_code']} - {$CMS->lang['trx_status_0'.$trx_info['trx_status']]}");
            }

			$CMS->class->logs->save_detail("transaction", $data_info['trx_id'], $trx_info);

            return $trx_info;
		}
		return false;
	}
	public function delete($trx_id = null) {
		if (!empty($trx_id)) {
			global $CMS, $DB, $member;

			$trx = $this->getInfo($trx_id);

			$DB->query("UPDATE `".root_table."transaction` SET `trx_deleted`=1 WHERE `trx_id`='{$trx_id}' ");

            $CMS->class->logs->key = "transaction_{$trx_id}";

			$CMS->class->logs->insert("đã xóa: <strong>{$trx['trx_code']}</strong>");
		}
		return false;
	}
	public function updateReceiveChildren($trx_id = null, $children = null) {
		if (!empty($trx_id)) {
			global $CMS, $DB, $member;
			$children = is_array($children) ? $children : array($children);
			$tmp = $this->getInfo($trx_id, 1, 1, 'trx_receive_children');
			$tmp = !empty($tmp) ? json_decode($tmp) : array();
			$children = json_encode(array_merge($children, $tmp));
			$DB->query("UPDATE `".root_table."transaction` SET `trx_receive_children`='{$children}' WHERE `trx_id`='{$trx_id}' AND `trx_deleted`=0");
			return true;
		}
		return false;
	}
	public function updateReceivePayment($trx_id = null, $receive_payment = null) {
		if (!empty($trx_id)) {
			global $CMS, $DB, $member;
			$receive_payment = !empty($receive_payment) ? $receive_payment : '0';
			$tmp = $this->getInfo($trx_id, 1, 1, 'trx_receive_payment');
			$tmp = !empty($tmp) ? $tmp : '0';
			$receive_payment += $tmp;
			$DB->query("UPDATE `".root_table."transaction` SET `trx_receive_payment`='{$receive_payment}' WHERE `trx_id`='{$trx_id}' AND `trx_deleted`=0");
			return true;
		}
		return false;
	}

	function updateInvoiceInfo($trx_id = 0, $invoiceInFo = [], $creditMemoInfo = [])
    {
        global $DB;

        if(!$trx_id) return false;

        $trx_total = 0;
        foreach ($invoiceInFo as $key => $value)
        {
            $trx_total += $value;
            $this->updateReceiveInfo($key, $trx_id, $value);
        }

        //Check xem invoice nào bị xóa
        $old = $this->getInfo($trx_id);
        $old_invoices = @json_decode($old['trx_invoice_info']);

        if($old_invoices) {
            foreach($old_invoices as $key => $value)
            {
                if(!isset($invoiceInFo[$key]))
                {
                    $this->updateReceiveInfo($key, $trx_id, $value, 1);
                }
            }
        }


        $trx_invoice_info = !empty($invoiceInFo) ? json_encode($invoiceInFo) : null;
        $trx_credit_memo_info = !empty($creditMemoInfo) ? json_encode($creditMemoInfo) : null;

        $sql = "UPDATE ".root_table."transaction SET trx_invoice_info='{$trx_invoice_info}', trx_credit_memo_info='{$trx_credit_memo_info}', trx_total='{$trx_total}' WHERE trx_id='{$trx_id}'";

        return $DB->query($sql);
    }

    function updateReceiveInfo($trx_id = 0, $receive_id = 0, $payment = 0, $is_delete = 0)
    {
        global $DB, $CMS;

        if($trx_id == 0) return false;

        $data = $this->getInfo($trx_id);

        $trx_receive_info = @json_decode($data['trx_receive_info'], true);

        if($is_delete == 1)
        {
            unset($trx_receive_info[$receive_id]);
        }
        else
        {
            $trx_receive_info[$receive_id] = $payment;
        }


        $trx_receive_payment = 0;

        foreach ($trx_receive_info as $k => $v)
        {
            $trx_receive_payment += $v;
        }

        $trx_receive_info = !empty($trx_receive_info) ? @json_encode($trx_receive_info) : null;

        $trx_status = $trx_receive_payment >= $data['trx_total'] ? 1 : 0;

        $sql = "UPDATE ".root_table."transaction SET trx_receive_info='{$trx_receive_info}', trx_receive_payment='{$trx_receive_payment}', trx_status='{$trx_status}' WHERE trx_id='{$trx_id}'";

        $CMS->order->update_payment_status($trx_id,$trx_status);

        return $DB->query($sql);
    }

	public function updateStatus($trx_id = null, $trx_status = null) {
		if (!empty($trx_id)) {
			global $CMS, $DB, $member;
			$trx_status = !empty($trx_status) && in_array($trx_status, array(0,1,2,3)) ? $trx_status : '0';
			$DB->query("UPDATE `".root_table."transaction` SET `trx_status`='{$trx_status}' WHERE `trx_id`='{$trx_id}' AND `trx_deleted`=0");

			return true;
		}
		return false;
	}


	public function add_transaction($type= "unpaid" ) {
            
		global $CMS, $DB, $member;

		$data['cus_id'] = isset($CMS->input['cus_id']) ? intval($CMS->input['cus_id']) : 0;
 		$data['user_id'] = isset($CMS->input['user_id']) ? intval($CMS->input['user_id']) : 0;
		$cus = $CMS->customer->getInfo($data['cus_id']); //$cus = $CMS->customer->getInfo($cus_id);
		$data['cus_email'] = isset($cus['cus_email']) ? $cus['cus_email'] : '';
		$data['trx_address'] = isset($cus['cus_address']) ? $cus['cus_address'] : '';
		$trx_time = time();
		
		$data['type'] = 1;
		$CMS->input['sub'] = $data['sub'] = 1;
		$data['trx_status'] = isset($CMS->input['trx_status']) ? $CMS->input['trx_status'] : 0 ;
		$data['trx_payment_method'] = isset($CMS->input['payment_method']) ? intval($CMS->input['payment_method']) : 0;
		$data['trx_account'] = isset($CMS->input['account_id']) ? intval($CMS->input['account_id']) : 0;
		if($type == "unpaid")
		{
			$data['trx_terms'] = 15;
			$trx_expiration_date = time() + ( $data['trx_terms'] * (24 * 60 * 60));

			$data['trx_expiration_date'] = $CMS->class->date->date_format($trx_expiration_date);

		}

		$data['trx_invoice_no'] = $this->getNoInvoice(1);
		$data['store_id'] = input::get('store_id');
		$data['trx_booking_phone'] = input::get('booking_phone');

 		$trx = $this->add($data);

 		$trx_id = $trx['trx_id'];
 		$this->addItem($trx_id,"",1 );


		return $trx_id;
	}

	public function add_transaction_item($trx_id = "") {
		global $CMS, $DB, $member;
 
		$cus_id = intval($CMS->input['cus_id']);
		$cus = $CMS->customer->getInfo($cus_id);
		$cus_email = $cus['cus_email'];
		$trx_billing_address = $cus['cus_address'];
		$trx_time = time();
		$trxtype = 1;
		$trx_subtype = 1;
		$trx_status = 0;
		$trx_payment_methdod =  $CMS->input['payment_method'];
		$trx_account = $CMS->input['account_id'];


 		foreach ($_SESSION['order_item'] as $key => $subitem) {

 			$tri_total = $subitem['item_price'] *  $subitem['item_cycle'];
 			$time = time();
 			$start_day_format = $CMS->class->date->date_format($time);
 			$cycle = $subitem['item_cycle'];
 			$service_type = $CMS->input['service_type'];
 			$cycle_type = $CMS->input['item_cycle_type'];
 			$tri_name = $subitem['item_name'];

 			if($service_type == 1)//Dich vu GTGT
 			{
 				$tri_cycle = $cycle;
 				$product_id = $subitem['item_id'];
 				if($cycle_type == 0)// Tháng
 				{
 					$end_day = time() + (86400 * 31 * $cycle);
 				}
 				else
 				{
 					$end_day = time() + (86400 * 365 * $cycle);
 				}
 				$tri_description = $tri_name . "(". $start_day_format ."-".$CMS->class->date->date_format($end_day).")";
 			 
 			}
 			else
 			{
 				$property_id = $subitem['item_id'];
 				$store_id = $subitem['item_store_id'];
 				$tri_quantity = $cycle;
 				$tri_description = $tri_name ;
 			}

 		 		
 			$tri_due_date = $end_day;

 			//
 			$DB->query("INSERT INTO `".root_table."transaction_item` (`trx_id`, `tri_name`, `tri_description`,     `tri_quantity` , `tri_cycle`, `cycle_type`, `tri_total`, `tri_tax`,   `service_type`, `tri_due_date` , `product_id`,   `property_id`, `store_id`, `user_id`, `tri_time`) VALUES ('{$trx_id}','{$tri_name}','{$tri_description}', '{$tri_quantity}', '{$tri_cycle}', '{$cycle_type}', '{$tri_total}', '{$tri_tax}', '{$service_type}', '{$tri_due_date}', '{$product_id}','{$property_id}','{$store_id}','{$member['user_id']}','".time()."')");
			$id = $DB->last_insert_id();
			$CMS->class->logs->insert("Add_transactions_item_{$id}");



 		}

  
		
		return true;
	}


	public function edit_transaction($trx_id = null) {
		
		global $CMS, $DB, $member;
 
		$data_info = $this->getInfo($trx_id);
		if (!empty($data_info)) {
			global $CMS, $DB, $member;
			$key = "transaction_{$data_info['trx_id']}";
			$CMS->class->logs->key = $key;
			$CMS->class->logs->old_data = $data_info;
			$CMS->class->logs->insert($key);
			$trx_status = !empty($CMS->input['trx_status']) ? $CMS->input['trx_status'] : '0';
		 	$cus_id = !empty($CMS->input['cus_id']) ? $CMS->input['cus_id'] : '0';
		 	$cus = $CMS->customer->getInfo($cus_id);
			$cus_email = $cus['cus_email'];

			$trx_payment_method =   $CMS->input['payment_method'];
			$trx_account =  $CMS->input['account_id'];
			$trx_tax = !empty($CMS->input['trx_tax']) ? $CMS->input['trx_tax'] : '0';
			$trx_total = !empty($CMS->input['trx_total']) ? $CMS->input['trx_total'] : '0';
            $trx_amount = !empty($CMS->input['trx_amount']) ? $CMS->input['trx_amount'] : '0';
            $trx_discount_type = !empty($CMS->input['trx_discount_type']) ? $CMS->input['trx_discount_type'] : '0';
            $trx_discount_value = !empty($CMS->input['trx_discount_value']) ? $CMS->input['trx_discount_value'] : '0';
		 
			$DB->query("UPDATE `".root_table."transaction` SET    `trx_payment_method`='{$trx_payment_method}', `trx_account`='{$trx_account}', `trx_tax`='{$trx_tax}', `trx_total`='{$trx_total}',  `cus_id`='{$cus_id}', `cus_email`='{$cus_email}', trx_amount='{$trx_amount}', trx_discount_type='{$trx_discount_type}', trx_discount_value='{$trx_discount_value}' WHERE `trx_id`='{$data_info['trx_id']}' AND `trx_deleted`=0");

            $CMS->customer->update_last_action_time($cus_id);

			$CMS->class->logs->key = $key;
			$CMS->class->logs->save_detail("transaction", $data_info['trx_id'], $this->getInfo($data_info['trx_id'], $data_info['trx_type'], $data_info['trx_subtype']));
			return true;
		}
		return false;
	}

	function update_transaction_code($prefix = "TRX")
    {
        global $DB;
        $sql = "UPDATE ".root_table."transaction SET trx_code = CONCAT('{$prefix}', trx_id) WHERE trx_code IS NULL OR trx_code=''";
        return $DB->query($sql);
    }

    function calculate($data = [])
    {
        global $CMS, $DB;

        // Backup inputs
        $input_bk = $CMS->input;
        $data_bk  = $data;

        if( $data )
        {
            $CMS->input = array_merge($CMS->input, $data);
        }

        $data = [
            'old_subtotal' => 0, // Gía gốc chưa giảm giá, thuế ...
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'total' => 0,
            'commission' => 0,
            'fee_shipping' => 0,
        ];

        $checkTax = true; //Kiểm tra tính đồng bộ của % thuế
        $checkTaxValue = -1;

        if(isset($CMS->input['product_quantity']) && $CMS->input['product_quantity'])
        {
            foreach ($CMS->input['product_quantity'] as $k => $product_quantity) {
                if ($product_quantity <= 0 || empty($CMS->input['product_name'][$k])) {
                    unset($CMS->input['product_name'][$k]);
                    unset($CMS->input['product_id'][$k]);
                    unset($CMS->input['product_description'][$k]);
                    unset($CMS->input['product_quantity'][$k]);
                    unset($CMS->input['product_price'][$k]);
                    unset($CMS->input['product_old_price'][$k]);
                    unset($CMS->input['product_price_add'][$k]);
                    unset($CMS->input['product_cycle'][$k]);
                    unset($CMS->input['product_amount'][$k]);
                    unset($CMS->input['product_tax'][$k]);
                    unset($CMS->input['product_discount_type'][$k]);
                    unset($CMS->input['product_discount_value'][$k]);
                    unset($CMS->input['product_commission_type'][$k]);
                    unset($CMS->input['product_commission_value'][$k]);
                } else {
                    $discountType = isset($CMS->input['product_discount_type'][$k]) ? intval($CMS->input['product_discount_type'][$k]) : 0;
                    $discountValue = isset($CMS->input['product_discount_value'][$k]) ? floatval($CMS->input['product_discount_value'][$k]) : 0;

                    $commissionType = isset($CMS->input['product_commission_type'][$k]) ? intval($CMS->input['product_commission_type'][$k]) : 0;
                    $commissionValue = isset($CMS->input['product_commission_value'][$k]) ? floatval($CMS->input['product_commission_value'][$k]) : 0;

                    $price = $CMS->input['product_price'][$k] = floatval( isset($CMS->input['product_price'][$k]) ? $CMS->input['product_price'][$k] : 0 ) + floatval( isset($CMS->input['product_price_add'][$k]) ? $CMS->input['product_price_add'][$k] : 0 );
                    $old_price = $CMS->input['product_old_price'][$k] = floatval( isset($CMS->input['product_old_price'][$k]) ? $CMS->input['product_old_price'][$k] : 0 ) + floatval( isset($CMS->input['product_price_add'][$k]) ? $CMS->input['product_price_add'][$k] : 0 );
                    $quantity = $CMS->input['product_quantity'][$k] = isset($CMS->input['product_quantity'][$k]) && intval($CMS->input['product_quantity'][$k]) ? intval($CMS->input['product_quantity'][$k]) : 1;
                    $cycle = $CMS->input['product_cycle'][$k] = isset($CMS->input['product_cycle'][$k]) && intval($CMS->input['product_cycle'][$k]) ? intval($CMS->input['product_cycle'][$k]) : 1;
                    $taxPercent = $CMS->input['product_tax'][$k] = isset($CMS->input['product_tax'][$k]) ? floatval($CMS->input['product_tax'][$k]) : 0;
                    $calculateItem = $this->calculateItem($old_price > $price ? $old_price : $price, $discountType, $discountValue, $taxPercent, $quantity, $cycle);

                    $data['tax'] += $calculateItem['totalTax'];
                    $data['old_subtotal'] += $calculateItem['subTotal'];
                    $data['subtotal'] += $calculateItem['subTotal'] - $calculateItem['totalDiscount'];
                    $data['commission'] += $commissionType ?  $commissionValue*$quantity : ($calculateItem['subTotal'] - $calculateItem['totalDiscount']) *  $commissionValue/100;

                    if($checkTaxValue == -1)
                    {
                        $checkTaxValue = $taxPercent;
                    }

                    if($checkTaxValue != $taxPercent)
                    {
                        $checkTax = false;
                    }
                }
            }
        }

        if(isset($CMS->input['ass_quantity']) && $CMS->input['ass_quantity'])
        {
            foreach ($CMS->input['ass_quantity'] as $k => $ass_quantity) {
                if ($ass_quantity <= 0 || empty($CMS->input['ass_name'][$k])) {
                    unset($CMS->input['ass_name'][$k]);
                    unset($CMS->input['ass_id'][$k]);
                    unset($CMS->input['ass_key'][$k]);
                    unset($CMS->input['ass_quantity'][$k]);
                    unset($CMS->input['ass_price'][$k]);
                    unset($CMS->input['ass_amount'][$k]);
                    unset($CMS->input['ass_tax'][$k]);
                    unset($CMS->input['ass_discount_type'][$k]);
                    unset($CMS->input['ass_discount_value'][$k]);
                } else {

                    $discountType = intval($CMS->input['ass_discount_type'][$k]);
                    $discountValue = floatval($CMS->input['ass_discount_value'][$k]);

                    $price = $CMS->input['ass_price'][$k] = floatval($CMS->input['ass_price'][$k]);
                    $quantity = $CMS->input['ass_quantity'][$k] = intval($CMS->input['ass_quantity'][$k]) ? intval($CMS->input['ass_quantity'][$k]) : 1;
                    $cycle = $CMS->input['ass_cycle'][$k] = intval($CMS->input['ass_cycle'][$k]) ? intval($CMS->input['ass_cycle'][$k]) : 1;
                    $taxPercent = $CMS->input['ass_tax'][$k] = floatval($CMS->input['ass_tax'][$k]);

                    $calculateItem = $this->calculateItem($price, $discountType, $discountValue, $taxPercent, $quantity, $cycle);

                    $data['old_subtotal'] += $calculateItem['subTotal'];
                    $data['subtotal'] += $calculateItem['subTotal'] - $calculateItem['totalDiscount'];
                    $data['tax'] += $calculateItem['totalTax'];

                    if($checkTaxValue == -1)
                    {
                        $checkTaxValue = $taxPercent;
                    }

                    if($checkTaxValue != $taxPercent)
                    {
                        $checkTax = false;
                    }
                }
            }
        }

        $total_discount_type = input::get('total_discount_type')*1;
        $total_discount_value = input::get('total_discount_value')*1;

        if($checkTax)
        {
            $data['discount'] = $total_discount_type == 0 ? ($data['subtotal'] * $total_discount_value) / 100 : $total_discount_value;
            $data['tax'] = (($data['subtotal'] - $data['discount']) * $checkTaxValue) / 100;
        }
        else
        {
            $data['discount'] = 0;
        }

        $data['total'] = $data['subtotal'] - $data['discount'] + $data['tax'];

        // Revere inputs
        $CMS->input = $input_bk;

        // ThamLV-Y2018M09D01: calculate fee shipping based on the original value of the order
        if( $fee_shipping = $this->calculateShippingFee($data_bk, $data['old_subtotal']) )
        {
            $data['fee_shipping'] = $fee_shipping;
            $data['total'] += $fee_shipping;
        }

        return $data;
    }


    function showItemsListing($trx=[])
    {
        global $CMS;

        if(!$trx) return false;

        $li = "";

        $items_text = "";

        if(in_array($trx['trx_subtype'], [1,3,4,5,6,8])) //get trx_item
        {
            $items = $this->getItemAll($trx['trx_id'], 'all');

            foreach ($items as $k => $item)
            {
                if($k<=1)
                {
                    if($item['product_id'])
                    {
                        $href = "{$CMS->vars['root_domain']}/?site=product&act=show&id={$item['product_id']}";
                    }
                    elseif ($item['ass_key'])
                    {
                        $href = "{$CMS->vars['root_domain']}/?site=assets&act=show&id={$item['ass_key']}";
                    }
                    else{
                        $href = "";
                    }

                    $tri_name = \lib\input::substr($item['tri_name'],0,22);

                    $li .= <<<EOF
                <li><a href="{$href}">- {$tri_name}</a></li>
EOF;
                }

                $items_text .= ",{$item['tri_name']} ";
            }
        }

        $items_text = trim($items_text, ',');

        $output = <<<EOF
        <ul data-toggle="tooltip" title="{$items_text}">{$li}</ul>
EOF;


        return $output;
    }

    function stats_default_page()
    {
        global $DB, $CMS;

        $return = [];

        $today = $CMS->class->date->gmt($CMS->vars['this_day'],$CMS->vars['this_month'],$CMS->vars['this_year']);

        $sql_add = "";

        if($CMS->input['date_from'] and $CMS->input['date_from'])
        {
            $date_from = $CMS->class->date->date2time($CMS->input['date_from']);
            $date_to = $CMS->class->date->date2time($CMS->input['date_to'])+(3600*24)-1;
            $sql_add .= " AND trx_time BETWEEN '{$date_from}' AND '{$date_to}' ";
        }elseif($CMS->input['date_from'])
        {
            $date_from = $CMS->class->date->date2time($CMS->input['date_from']);
            $sql_add .= " AND trx_time = '{$date_from}' ";
        }elseif($CMS->input['date_to'])
        {
            $date_to = $CMS->class->date->date2time($CMS->input['date_to'])+(3600*24)-1;
            $sql_add .= " AND trx_time = '{$date_to}' ";
        }

        if(input::get('keyword'))
        {
            $keyword = urldecode($CMS->input['keyword']);

            if(input::get('type') == 1)
            {
                $inv_no = str_replace('INV','', $keyword);
            }
            else if (input::get('type') == 2)
            {
                $inv_no = str_replace('PAY','', $keyword);
            }
            else
            {
                $inv_no = "";
            }

            if(!$inv_no || $inv_no != str_split($keyword, 3)[1])
            {
                $inv_no = "";
            }

            $inv_no = intval($inv_no);

            if($inv_no)
            {
                $sql_add .= " AND  trx_invoice_no='{$inv_no}' ";
            }
            else
            {
                $sql_add .= " AND (trx_code='{$keyword}' OR trx_billing_address LIKE '%{$keyword}%' OR cus_email LIKE '%{$keyword}%' ) ";
            }
        }

        if($store_id = input::get('store_id') * 1)
        {
            $sql_add .= " AND store_id={$store_id} ";
        }

        //Tất cả
        $all_sql = "SELECT COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_type='{$CMS->input['type']}' {$sql_add}";
        $all_sql = $DB->query($all_sql);
        $return['all'] = $DB->fetch_assoc($all_sql);

        //Dự kiến
        $estimate_sql = "SELECT SUM(trx_total) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype=4 {$sql_add}";
        $estimate_sql = $DB->query($estimate_sql);
        $return['estimate'] = $DB->fetch_assoc($estimate_sql);
        $return['estimate']['total'] = $return['estimate']['total']*1;
        $return['estimate']['total_f'] = $CMS->class->input->currency($return['estimate']['total']);


        //Chưa thanh toán
        if($CMS->input['type'] == 1)
        {
            $unbill_sql = "SELECT SUM(trx_total-trx_receive_payment) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype=1 AND trx_receive_payment < trx_total AND trx_id NOT IN (SELECT trx_id FROM ".root_table."transaction WHERE trx_deleted = 0 AND `trx_subtype`=1 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)) {$sql_add}) {$sql_add}";
        }
        else
        {
            $unbill_sql = "SELECT SUM(trx_total-trx_receive_payment) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype=5  AND trx_receive_payment < trx_total AND trx_id NOT IN (SELECT trx_id FROM ".root_table."transaction WHERE trx_deleted = 0 AND `trx_subtype`=5 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)) {$sql_add}) {$sql_add}";
        }


        $unbill_sql = $DB->query($unbill_sql);
        $return['unbill'] = $DB->fetch_assoc($unbill_sql);
        $return['unbill']['total'] = $return['unbill']['total']*1;
        $return['unbill']['total_f'] = $CMS->class->input->currency($return['unbill']['total']);

        //Quá hạn
        if($CMS->input['type'] == 1)
        {
            $overdue_sql = "SELECT SUM(trx_total - trx_receive_payment) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND `trx_subtype`=1 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)) {$sql_add}";
        }
        else
        {
            $overdue_sql = "SELECT SUM(trx_total - trx_receive_payment) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND `trx_subtype`=5 AND (trx_status = 2 OR (trx_due_date!=0 AND trx_due_date<$today AND trx_status=0)) {$sql_add}";
        }

        $overdue_sql = $DB->query($overdue_sql);
        $return['overdue'] = $DB->fetch_assoc($overdue_sql);
        $return['overdue']['total'] = $return['overdue']['total']*1;
        $return['overdue']['total_f'] = $CMS->class->input->currency($return['overdue']['total']);

        //Hóa đơn
        if($CMS->input['type'] == 1)
        {
            $invoice_sql = "SELECT SUM(trx_total) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype=1 {$sql_add}";
        }
        else
        {
            $invoice_sql = "SELECT SUM(trx_total) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype=5 {$sql_add}";
        }


        $invoice_sql = $DB->query($invoice_sql);
        $return['invoice'] = $DB->fetch_assoc($invoice_sql);
        $return['invoice']['total'] = $return['invoice']['total']*1;
        $return['invoice']['total_f'] = $CMS->class->input->currency($return['invoice']['total']);

        //Đã thanh toán

        if($CMS->input['type'] == 1)
        {
//            $paid_sql = "SELECT SUM(trx_total) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (2,3) {$sql_add}";
            $paid_sql = "SELECT SUM(trx_receive_payment) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype=1 AND trx_receive_payment !=0 {$sql_add}";
        }
        else
        {
//            $paid_sql = "SELECT SUM(trx_total) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (6,7) {$sql_add}";
            $paid_sql = "SELECT SUM(trx_receive_payment) total, COUNT(*) cnt FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype=5 AND trx_receive_payment !=0 {$sql_add}";
        }

        $paid_sql = $DB->query($paid_sql);
        $return['paid'] = $DB->fetch_assoc($paid_sql);
        $return['paid']['total_f'] = $CMS->class->input->currency($return['paid']['total']);

        return $return;
    }

    function copy($id = 0)
    {
        global $CMS;

        $id = intval($id);

        $data = $this->getInfo($id);

       $products = $CMS->transactions->getItemAll($data['trx_id'],'product',1);
       $assets = $CMS->transactions->getItemAll($data['trx_id'], 'asset',1);

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $CMS->input = $data;
        $CMS->input['type'] = $data['trx_type'];
        $CMS->input['sub'] = $data['trx_subtype'];
        $CMS->input['act'] = 'add';
        $CMS->input['trx_method'] = $data['trx_payment_method'];
        $CMS->input['trx_expiration_date'] = $CMS->class->date->date_format($data['trx_expiration_date']);
        $CMS->input['trx_estimate_date'] = $CMS->class->date->date_format($data['trx_estimate_date']);
        $CMS->input['trx_due_date'] = $CMS->class->date->date_format($data['trx_due_date']);
        $CMS->input['trx_accepted_date'] = $CMS->class->date->date_format($data['trx_accepted_date']);
        $CMS->input['trx_payment_date'] = $CMS->class->date->date_format($data['trx_payment_date']);

        foreach ($products as $p)
        {
            $CMS->input['product_name'][] = $p['tri_name'];
            $CMS->input['product_id'][] = $p['product_id'];
            $CMS->input['product_cycle_type'][] = $p['tri_cycle_type'];
            $CMS->input['product_description'][] = $p['tri_description'];
            $CMS->input['product_cycle'][] = $p['tri_cycle'];
            $CMS->input['product_quantity'][] = $p['tri_quantity'];
            $CMS->input['product_price'][] = $p['tri_total'];
            $CMS->input['product_tax'][] = $p['tri_tax'];
        }

        foreach ($assets as $a)
        {
            $CMS->input['ass_name'][] = $a['tri_name'];
            $CMS->input['ass_id'][] = $a['ass_key'];
            $CMS->input['ass_key'][] = $a['ass_key'];
            $CMS->input['ass_code'] [] = $a['tri_description'];
            $CMS->input['ass_quantity'][] = $a['tri_quantity'];
            $CMS->input['ass_price'][] = $a['tri_total'];
            $CMS->input['ass_tax'][] = $a['tri_tax'];
        }
    }

    function loadEmailTpl($id = 0)
    {
        global $CMS;

        $trx = $this->getInfo($id);

        $this->sendTrxEmail($id,0);

        $email_tpl_code = $CMS->email->email_template;
        $template = $CMS->emailtpl->get_info($email_tpl_code);

        if(!$trx || !$template)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $data = $CMS->email->data;

        $return['mail_to'] = $trx['cus_email'];
        $return['title'] = $CMS->email->convert_v2($template['emailtpl_title'], $data);
        $return['title'] = html_entity_decode($return['title'], ENT_QUOTES);
        $return['content'] = $CMS->email->convert_v2($template['emailtpl_content'], $data);
//        $return['content'] = str_replace('</p>',"</p>\n", $return['content']);
//        $return['content'] = strip_tags( $return['content']);
        $return['cus_id'] = $trx['cus_id'];
        $return['email_from'] = $template['emailtpl_from'];
        $return['email_from_name'] = $template['emailtpl_from_name'] ? $template['emailtpl_from_name'] : $template['emailtpl_from'];

        return $return;
    }

    function sendEmail($mail_to, $title, $content, $cus_id = 0, $email_from = "", $email_from_name="", $cc="", $bcc="", $email_attachment)
    {
        global $CMS;

        $content = str_replace( ["src=\"/uploads/", "src='/uploads/"],["src=\"{$CMS->vars['parent_domain']}/uploads/", "src=\'{$CMS->vars['parent_domain']}/uploads/"],$content);

        $email = $CMS->email->quick_add(3, $cus_id, $email_from, $email_from_name, $mail_to, $mail_to, $title, $content, 0, time(), 0, 0, $cc, $bcc, 1, "", $email_attachment);

        return $email;
    }

    function attachFiles($trx_id = 0)
    {
        global $CMS;

        $trx_id = intval($trx_id);

        if(!$trx_id) return false;

        $CMS->attach->mod_name = "transaction";
        $CMS->attach->mod_id = $trx_id;

        $CMS->attach->add();
    }

    function createReceivedPayment($trx_id = 0)
    {
        global $CMS;
 
        $trx_id = intval($trx_id);

        $trx = $this->getInfo($trx_id);

        if(!$trx)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        if($trx['trx_status'] != 0)
        {
            $_SESSION['msg'] = $CMS->lang['transaction_must_be_waiting'];
            return false;
        }

        $trx_receive_payment = $trx['trx_total'] - $trx['trx_receive_payment'];

        if($trx_receive_payment <= 0)
        {
            $_SESSION['msg'] = $CMS->lang['trx_receive_payment_invalid'];
            return false;
        }

        $trx_subtype = $trx['trx_type'] == 1 ? 2 : 7;

        $data = [
            'trx_status' => 3,  //Đã đóng
            'type' => $trx['trx_type'],
            'sub' => $trx_subtype,
            'trx_email' => $trx['trx_email'],
            'trx_payment_date' => date("d/m/Y"),
            'cus_type' => $trx['cus_type'],
            'cus_id' => $trx['cus_id'],
            'supplier_id' => $trx['supplier_id'],
            'user_assign' => $trx['user_assign'],
            'trx_account' => $trx['trx_account'],
            'trx_reference_no' => $trx['trx_reference_no'],
            'trx_total' => $trx_receive_payment,//$trx['trx_total'],
            'trx_receive_payment' => $trx_receive_payment,//$trx['trx_total'],
        ];

        unset($CMS->input['trx_invoice_no']);

        $inserted_trx = $this->add($data);

//        $CMS->transactions->updateInvoiceInfo($inserted_trx['trx_id'], [$trx_id => $trx['trx_total']]);
        $CMS->transactions->updateInvoiceInfo($inserted_trx['trx_id'], [$trx_id => $trx_receive_payment]);

        return $this->getInfo($inserted_trx['trx_id']);
    }

    public function loadAccountTypeOption($default = 0)
    {
        global $CMS;

        $CMS->class->language->load("accounts_type");

        $output = "";

        $selected[$default] = "selected";

        $groupIds = [0,1];

        foreach ($groupIds as $gId)
        {
            $data = $CMS->accounts_type->getData(" accounts_group={$gId} AND ");

            $output .= "<optgroup label=\"{$CMS->lang['at_group_'.$gId]}\">";

            foreach ($data as $result)
            {

                if($accounts_type_name = @json_decode($result['accounts_type_name'], true))
                {
                    $result['accounts_type_name'] = $accounts_type_name[$CMS->vars['default_language']];
                }

                $output .= <<<EOF
            <option {$selected[$result['accounts_type_id']]} value="{$result['accounts_type_id']}">{$result['accounts_type_name']}</option>
EOF;

            }

            $output .= "</optgroup>";
        }


        return $output;
    }
    
    public function transaction_balance($trx_id = 0, $type = 0, $old_balance = 0)
    {
        global $CMS,$DB;
        
        // Get info
        $transaction = $this->getInfo($trx_id);
        
        // Check data
        if(empty($transaction))
        {
            return false;
        }
        // Get total revenue(thu/chi) of all transaction before this transaction
        $sql = $DB->query("SELECT * FROM ".root_table."transaction WHERE trx_deleted=0 AND trx_status IN (1,3) AND trx_id <= '{$transaction['trx_id']}' ORDER BY trx_id ASC"); // 1: Paid, 3: Closed
                
        // Default
        $temp = 0;
                
        // Loop
        while($data = $DB->fetch_array($sql))
        {
            // Check for +
            if(in_array($data['trx_subtype'],array(2,3))) // Receive, Receipt
            {
                $temp += $data['trx_total'];     
            }
                
            // Check for -
            if(in_array($data['trx_subtype'],array(5,6))) // Bill payment, 
            {
                $temp -= $data['trx_total']; 
            }
        }
        
        // Log default
        $log = array();
            
        // Update balance this transaction
        $DB->query("UPDATE ".root_table."transaction SET trx_balance='{$temp}' WHERE trx_id='{$transaction['trx_id']}'");
        
        // Save log this transaction
        $log[$transaction['trx_id']] = "{$transaction['trx_balance']} -> {$temp}";
        
        // Check for type update transaction
        if($type == 1)
        {
           $amount = $temp - $old_balance;
           
           // Update all transaction after this
           $sql = $DB->query("SELECT * FROM ".root_table."transaction WHERE trx_id > '{$transaction['trx_id']}' AND trx_deleted=0 AND trx_subtype IN (2,3,5,6)");
           
           while($data = $DB->fetch_array($sql))
           {
               if($amount < 0)
                {
                    $new_balance = $data['transaction_balance'] - $amount;
                   
                    // Update transaction
                    $DB->query("UPDATE ".root_table."transaction SET trx_balance='{$new_balance}' WHERE trx_id='{$data['trx_id']}'");
                   
                    // Save logs
                    $log[$data['trx_id']] = "{$data['trx_balance']} -> {$new_balance}";
                }
           }
        }
            
        // Logs
        $CMS->class->logs->key = "transaction_{$transaction['trx_id']}";
        $CMS->lang['transaction_update_balance'] = isset($CMS->lang['transaction_update_balance']) ? $CMS->lang['transaction_update_balance'] : '';
        $CMS->class->logs->insert("{$CMS->lang['transaction_update_balance']}",serialize($log));
    }

    /**
     * Count number of transactions and get sum total by customer
     * @param $cus_id
     * @param int $trx_type
     * @param int $trx_subtype
     * @return mixed
     */
    function getNumberTotalByCusID($cus_id,$trx_type=0,$trx_subtype=0)
    {
        global $DB;

        $sql_add = '';

        if($trx_type)
        {
            $sql_add .= " trx_type='{$trx_type}' AND ";
        }

        if($trx_subtype)
        {
            $sql_add .= " trx_subtype='{$trx_subtype}' AND ";
        }

        $sql = "SELECT COUNT(trx_id) cnt, IFNULL(SUM(trx_total),0) total FROM ".root_table."transaction WHERE {$sql_add} trx_deleted=0 AND cus_id={$cus_id}";

        $data = $DB->fetch($sql);

        return $data;
    }

    /**
     * Count file and update number of files for transaction
     * @param $trx_id
     * @return mixed
     */
    function updateFileCount($trx_id)
    {
        global $CMS, $DB;
        $trx_id = intval($trx_id);
        $num = 0;

        if($trx_id)
        {
            $rootPath = "invoice_files/{$trx_id}";
            $pathUploads = "{$CMS->vars['upload_dir']}/{$rootPath}";

            if(is_dir($pathUploads))
            {
                $files = \lib\input::scandir($pathUploads,'pdf');
                $num = count($files);
            }
        }

        $sql = "UPDATE ".root_table."transaction SET trx_attachment_number={$num} WHERE trx_id='{$trx_id}'";

        return $DB->query($sql);
    }

    /**
     * List export file
     * @param $trx_id int
     * @return array
     */
    function getExportFiles($trx_id)
    {
        global $CMS;

        $trx_id = intval($trx_id);

        $return['status'] = 'fail';

        if(!$trx_id)
        {
            $return['msg'] = 'Not found transaction';
        }

        $rootPath = "invoice_files/{$trx_id}";

        $pathUploads = "{$CMS->vars['upload_dir']}/{$rootPath}";

        if(is_dir($pathUploads))
        {
            $files = \lib\input::scandir($pathUploads,'pdf');
            if(!$files)
            {
                $return['msg'] = 'There are no attachments to display for TRX'.$trx_id;
            }
            else
            {
                $return['status'] = 'success';

                foreach ($files as $fileName)
                {
                    $fileInfo = [];
                    $fileInfo[] = "<a href='{$CMS->vars['root_domain']}/billing/invoice/{$fileName}' target='_blank'>{$fileName}</a><a onclick='previewTrxPopup(\"{$trx_id}\",\"{$fileName}\")'><i style='float:right' class=\"fa fa-envelope\" aria-hidden=\"true\"></i></a>"; //name
                    $fileInfo[] = date ("Y-m-d H:i:s", filemtime("{$CMS->vars['upload_dir']}/{$rootPath}/{$fileName}")); //time
                    $return['files'][] = $fileInfo;
                }
            }
        }
        else
        {
            $return['msg'] = 'There are no attachments to display for TRX'.$trx_id;
        }

        return $return;
    }

    /**
     * Download file invoice
     */
    function downloadInvoice()
    {
        global $CMS;

        $root = "invoice_files";

        $fileName = urldecode($CMS->input['file']);

        $exp = explode('-',$fileName);

        $folder = $exp[1];

        $redirect = $CMS->vars['root_domain'];

        if(!$folder || !is_dir($uploadDir = "{$CMS->vars['upload_dir']}/{$root}/{$folder}"))
        {
            $_SESSION['msg'] = "Have no any attachment";
        }
        else
        {
            if(!is_file($filePath = "{$uploadDir}/{$fileName}"))
            {
                $_SESSION['msg'] = "Not found file";
            }
            else
            {
                ezy::load_model("download");
                download::downFile($filePath);
            }
        }

        $CMS->global->redirect($redirect);
    }

    /**
     * Extract and get info invoice or receive
     * @param $data array|object
     * @return array|bool
     */
    function convertReceiveInvoiceInfo($data)
    {
        if(!is_array($data))
        {
            if(!$data = @json_decode($data, true))
            {
                return false;
            }
        }

        if(!$data) return false;

        $return = [];

        foreach ($data as $trx_id => $trx_amount)
        {
            $trx = $this->getInfo($trx_id);
            if($trx)
            {
                $trx['receive_amount'] = $trx_amount;
                $return[] = $trx;
            }
        }
        return $return;
    }

    /**
     * Convert input or data for edit form
     * @param $data array
     * @return array
     */
    function editvalue($data)
    {
        $data['trx_payment_date'] = $data['trx_payment_date'] ? \lib\date::format($data['trx_payment_date']) : "";
        $data['trx_expiration_date'] = $data['trx_expiration_date'] ? \lib\date::format($data['trx_expiration_date']) : "";
        $data['trx_estimate_date'] = $data['trx_estimate_date'] ? \lib\date::format($data['trx_estimate_date']) : "";
        $data['trx_accepted_date'] = $data['trx_accepted_date'] ? \lib\date::format($data['trx_accepted_date']) : "";
        $data['trx_due_date'] = $data['trx_due_date'] ? \lib\date::format($data['trx_due_date']) : "";
        return $data;
    }

    public function loadLangForImportExport()
    {
        global $CMS;

        $langData = [
            'trx_id' => [
                'vn'=>'ID',
                'en'=>'ID'
            ],
            'transaction_item' => [
                'vn'=>'Mặt hàng',
                'en'=>'Items'
            ],
            'trx_subtype' => [
                'vn'=>'Loại',
                'en'=>'Type'
            ],
            'trx_invoice_no' => [
                'vn'=>'Mã',
                'en'=>'Code'
            ],
            'audience_type' => [
                'vn'=>'Khách hàng / Nhà cung cấp / Nhân viên phụ trách',
                'en'=>'Customer / Provider / Staff'
            ],
            'email' => [
                'vn'=>'Email',
                'en'=>'Email'
            ],
            'name' => [
                'vn'=>'Tên',
                'en'=>'Name'
            ],
            'payment_address' => [
                'vn'=>'Địa chỉ',
                'en'=>'Address',
            ],
            'payment_date' => [
                'vn'=>'Ngày thanh toán',
                'en'=>'Payment date',
            ],
            'trx_due_date' => [
                'vn'=>'Ngày đáo hạn',
                'en'=>'Due date'
            ],
            'trx_total' => [
                'vn'=>'Tổng cộng',
                'en'=>'Total'
            ],
            'trx_status' => [
                'vn'=>'Trạng thái',
                'en'=>'Status'
            ],
            'quantity' => [
                'vn'=>'Số lượng',
                'en'=>'Quantity'
            ],
            'cycle' => [
                'vn'=>'Chu kỳ thanh toán',
                'en'=>'Cycle'
            ],
            'cycle_type_0' => [
                'vn'=>'Một lần',
                'en'=>'Once'
            ],
            'cycle_type_1' => [
                'vn'=>'Tháng',
                'en'=>'Month'
            ],
            'cycle_type_2' => [
                'vn'=>'Năm',
                'en'=>'Year'
            ],
            'price' => [
                'vn'=>'Giá',
                'en'=>'Price'
            ],
            'tax' => [
                'vn'=>'Thuế',
                'en'=>'Tax'
            ],
            'sub_type_1' => [
                'vn' => 'Hóa đơn',
                'en' => 'Invoice',
            ],
            'sub_type_2' => [
                'vn' => 'Nhận thanh toán',
                'en' => 'Received payment',
            ],
            'sub_type_3' => [
                'vn' => 'Phiếu thu',
                'en' => 'Receipt',
            ],
            'sub_type_4' => [
                'vn' => 'Dự kiến',
                'en' => 'Estimate',
            ],
            'sub_type_5' => [
                'vn' => 'Hóa đơn',
                'en' => 'Bill',
            ],
            'sub_type_6' => [
                'vn' => 'Chi phí',
                'en' => 'Expense',
            ],
            'sub_type_7' => [
                'vn' => 'Thanh toán',
                'en' => 'Bill payment',
            ],
            'sub_type_8' => [
                'vn' => 'Credit memo',
                'en' => 'Credit memo',
            ],
            'cus_type_1' => [
                'vn' => 'Khách hàng',
                'en' => 'Customer',
            ],
            'cus_type_2' => [
                'vn' => 'Nhà cung cấp',
                'en' => 'Provider',
            ],
            'cus_type_3' => [
                'vn' => 'Nhân viên phụ trách',
                'en' => 'Staff',
            ],
            'trx_status_0' => [
                'vn' => 'Đang chờ',
                'en' => 'Waiting',
            ],
            'trx_status_1' => [
                'vn' => 'Đã thanh toán',
                'en' => 'Paid',
            ],
            'trx_status_2' => [
                'vn' => 'Quá hạn',
                'en' => 'Overdue',
            ],
            'trx_status_3' => [
                'vn' => 'Đã đóng',
                'en' => 'Closed',
            ],
            'trx_status_4' => [
                'vn' => 'Đã hủy',
                'en' => 'Canceled',
            ],
            'trx_status_5' => [
                'vn' => 'Chấp nhận',
                'en' => 'Accept',
            ],
            'trx_estimate_status_0' => [
                'vn' => 'Đang chờ',
                'en' => 'Waiting',
            ],
            'trx_estimate_status_1' => [
                'vn' => 'Chấp nhận',
                'en' => 'Accept',
            ],
            'trx_estimate_status_2' => [
                'vn' => 'Đã đóng',
                'en' => 'Closed',
            ],
            'trx_estimate_status_3' => [
                'vn' => 'Từ chối',
                'en' => 'Rejected',
            ],
            'trx_method_0' => [
                'vn' => 'Tiền mặt',
                'en' => 'Cash',
            ],
            'trx_method_1' => [
                'vn' => 'Chuyển khoản',
                'en' => 'Bank transfer',
            ],
            'tri_type_0' => [
                'vn' => 'Sản phẩm',
                'en' => 'Product',
            ],
            'tri_type_1' => [
                'vn' => 'Dịch vụ',
                'en' => 'Service',
            ],
            'tri_type_2' => [
                'vn' => 'Tài sản',
                'en' => 'Asset',
            ],
            'reference_number' => [
                'vn' => 'Số tham chiếu',
                'en' => 'Reference number',
            ],
            'account_name' => [
                'vn' => 'Tài khoản',
                'en' => 'Account',
            ],
            'accounting_account' => [
                'vn' => 'Định khoản',
                'en' => 'Record the transaction',
            ],
            'contract_code' => [
                'vn' => 'Mã hợp đồng',
                'en' => 'Contract code',
            ],
            'payment_method' => [
                'vn' => 'Hình thức thanh toán',
                'en' => 'Payment method',
            ],
            'payment_method_0' => [
                'vn' => 'Tiền mặt',
                'en' => 'Cash',
            ],
            'payment_method_1' => [
                'vn' => 'Chuyển khoản',
                'en' => 'Bank transfer',
            ],
            'trx_payment_date' => [
                'vn' => 'Ngày thanh toán',
                'en' => 'Payment date',
            ],
        ];

        $currentLang = [];

        foreach($langData as $langKey => $langItem)
        {
            $currentLang[$langKey] = $langItem[$CMS->vars['default_language']];
        }

        return [$langData,$currentLang];
    }

    /**
     * Export to excel file
     * @return string
     */
    public function exportToExcel($is_template = 0)
    {
        global $CMS, $DB, $member;

        list($langData,$currentLang) = $this->loadLangForImportExport();

        $transactions = $this->listing(1);

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */

        if($is_template == 1)
        {
            $tri_type = [0,1];

            $fields = 'transaction_item,trx_subtype,audience_type,email,name,payment_address,trx_payment_date,payment_method,account_name,reference_number,accounting_account,contract_code';
        }
        else
        {
            $tri_type = [0,1,2];
            $fields = 'trx_id,transaction_item,trx_subtype,trx_invoice_no,audience_type,email,name,trx_due_date,trx_total,trx_status';
        }


        //Range char A-Z
        $rangeChar = range('A', 'Z') ;
        $fields = explode(',', $fields);

        $setTitle = [];

        foreach($fields as $field)
        {
            $field = trim($field);
            $setTitle[] = $currentLang[$field];
        }

        \models\report::excel_header();

        // Set style
        $style = array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => TRUE
        );

        $count_row = count($transactions) + 2;// + 1 row title

        // Set style excel
        \models\report::excel_title($setTitle, $count_row, $style);

        // Loop data
        $i = 3;

        //Set filter description
        $lastChar = $rangeChar[count($fields)-1];
        \models\report::$dataExcel->getActiveSheet()->mergeCells("A2:{$lastChar}2");
        \models\report::$dataExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
        \models\report::$dataExcel->getActiveSheet()->setCellValue("A2", $CMS->vars['exportTitle']);

        if($count_row > 2)
        {
            foreach ( $transactions as $result)
            {
                $result['audience_type'] = $result['cus_type'];
                $result['name'] = "";
                $result['email'] = "";
                if($result['data_bk']['cus_type'] == 1)
                {
                    $cus_info = $CMS->customer->getInfo($result['data_bk']['cus_id']);
                    if($cus_info)
                    {
                        $result['name'] = $cus_info['cus_full_name'];
                        $result['email'] = $cus_info['cus_email'];
                    }
                }
                else if($result['data_bk']['cus_type'] == 2)
                {
                    $supplier_info = $CMS->supplier->get_info($result['data_bk']['supplier_id']);

                    if($supplier_info)
                    {
                        $result['name'] = $supplier_info['supplier_name'];
                        $result['email'] = $supplier_info['supplier_email'];
                    }
                }
                else if($result['data_bk']['cus_type'] == 3)
                {
                    $assign_info = $CMS->user->get_info($result['data_bk']['user_assign']);

                    if($assign_info)
                    {
                        $result['name'] = $assign_info['user_display_name'];
                        $result['email'] = $assign_info['user_email'];
                    }
                }


                $transaction_items = $result['transaction_item'];
                $result['transaction_item'] = [];

                foreach($transaction_items as $item)
                {
                    if(in_array($item['tri_type'],$tri_type))
                    {
                        $result['transaction_item'][] = "{$currentLang['tri_type_'.$item['tri_type']]}: {$item['tri_name']} || {$currentLang['quantity']}: {$item['tri_quantity']} || {$currentLang['cycle']}: {$item['tri_cycle']} x {$CMS->lang['cycle_type_'.$item['tri_cycle_type']]} || {$currentLang['price']}: {$item['tri_total']} || {$currentLang['tax']}: {$item['tri_tax']}%";
                    }
                }

                $result['transaction_item'] = implode(PHP_EOL,$result['transaction_item']);

                foreach ($rangeChar as $charKey => $char)
                {
                    /**
                     * Set values to cell by chars(A-Z) and fields from database
                     */
                    if(!isset($fields[$charKey])) continue;
                    $field = trim($fields[$charKey]);

                    $result[$field] = strip_tags(html_entity_decode($result[$field]));

                    if($field == 'trx_due_date' || $field == 'trx_payment_date')
                    {
                        $date = new DateTime($result[$field]);

                        \models\report::$dataExcel->getActiveSheet()->setCellValue($char.$i, PHPExcel_Shared_Date::PHPToExcel( $date ));
                    }
                    else
                    {
                        \models\report::$dataExcel->getActiveSheet()->setCellValue($char.$i, $result[$field]);
                    }

                    \models\report::$dataExcel->getActiveSheet()->getStyle($char.$i)->getAlignment()->setWrapText(true);

                    if($field == 'trx_total')
                    {
                        \models\report::$dataExcel->getActiveSheet()->getStyle($char.$i)->getNumberFormat()->setFormatCode("#,##0.00");
                    }
                    else if($field == 'trx_due_date' || $field == 'trx_payment_date')
                    {
                        \models\report::$dataExcel->getActiveSheet()->getStyle($char.$i)->getNumberFormat()->setFormatCode('mm/dd/yyyy');
                    }
                };
                $i++;
            }
        }


        // Set name file
        $file_name = "trx_{$CMS->vars['this_year']}{$CMS->vars['this_month']}{$CMS->vars['this_day']}.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
    }

    /**
     * Import data from excel file
     * @return bool
     */
    function importFromExcel()
    {
        global $CMS, $DB, $member;

        list($langData,$currentLang) = $this->loadLangForImportExport();

        // require file
        require_once root_path."vendor/autoload.php";

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;

        // Input
        $file_tmp = isset($_FILES['upload_file']['tmp_name']) ? $_FILES['upload_file']['tmp_name'] : "";
        $file_name = isset($_FILES['upload_file']['name']) ? $_FILES['upload_file']['name'] : "";

        // Check file allow
        $file_ext = $CMS->class->attachment->get_ext( $file_name );
        $arr_allow = array("xls","xlsx");

        if(!in_array($file_ext, $arr_allow))
        {
            $_SESSION['error_msg'] = $CMS->lang['error_ext_file_upload'];
            return false;
        }

        // Khoi tao
        $objPHPExcel = \PHPExcel_IOFactory::load($file_tmp);

        $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn(); //Cột cuối cùng
        $highestRow         = $objPHPExcel->getActiveSheet()->getHighestRow(); // e.g. 10

        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        //Reset style
        $styleDefault = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_NONE,
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'))
        );

        $objPHPExcel->getActiveSheet()->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray($styleDefault);

        //Danh sách thứ tự các field tương ứng với thứ tự cột từ file excel
        $fields_text = 'transaction_item,trx_subtype,audience_type,email,name,payment_address,payment_date,payment_method,reference_number,account_name,accounting_account,contract_code';
        $fields = explode(',', $fields_text);

        $errStyleCell = array(
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
//                    'color' => array('rgb' => 'FF0000')
//                )
//            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E0E0E0')
            )
        ); //Style đánh dấu ô có lỗi

        $errStyleRow = array(
            'font'  => array(
//                'bold'  => true,
                'color' => array('rgb' => 'FF0000'),
            )); //Style đánh dấu dòng có lỗi

        $validData = []; //Mang chua cac dong hop le

        /**
         * Marked error position
         */
        $errorPositions = [];


        $langDataRegex = [];

        foreach($langData as $key => $lang)
        {
            $langDataRegex[$key] = implode('|',$lang);
        }

        $audienceType = ['customer' => 1, 'provider' => 2, 'staff' => 3];
        $paymentMethod = ['cash' => 0, 'bank transfer' => 1];
        $productType = ['product' => 0, 'service' => 1];
        $tax = ['yes' => 10, 'no' => 0];

        /**
         * Loop to check required and set data by key
         */

        // Loop row
        for ($row = 1; $row <= $highestRow; ++$row)
        {
            if($row == 1) { continue;}
            $data = [];

            // Loop col
            for ($col = 0; $col < $highestColumnIndex; ++$col)
            {
                if($fields[$col])
                {
                    $cell =  $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $row);
                    if(\PHPExcel_Shared_Date::isDateTime($cell)) {
                        $data[$fields[$col]] = \PHPExcel_Shared_Date::ExcelToPHP($cell->getValue());
                    }
                    else
                    {
                        $data[$fields[$col]] = $CMS->class->editor->input($cell->getValue(), "text"); //Gan du lieu lai theo giong field trong DB
                    }
                }
            }
            if(!isset($errorPositions[$row]))
            {
                $validData[$row] = $data;
            }
        }

        /**
         * Loop again to check validate and convert key
         */

        foreach($validData as $row => $data)
        {
            /**
             * Check trx_subtype
             */

            for($i=1; $i<=7; $i++)
            {
                if(preg_match("/^{$langDataRegex['sub_type_'.$i]}$/i", $data['trx_subtype']))
                {
                    if(in_array($i,[3,6]))
                    {
                        $data['sub'] = $i;
                    }
                    break;
                }
            }

            if(!$data['sub'])
            {
                $errorPositions[$row][] = array_search('trx_subtype', $fields);
            }

            $data['type'] = $data['sub'] > 4 ? 2 : 1;

            /**
             * Check payment method
             */
            for($i=0; $i<=1; $i++)
            {
                if(preg_match("/^{$langDataRegex['payment_method_'.$i]}$/i", $data['payment_method']))
                {
                    $data['payment_method'] = $i;
                    break;
                }
            }
            $data['payment_method'] = intval($data['payment_method']);

            /**
             * Check audiencetype
             */
            $cus_type = null;
            for($i=1; $i<=3; $i++)
            {
                if(preg_match("/^{$langDataRegex['cus_type_'.$i]}$/i", $data['audience_type']))
                {
                    $cus_type = $i;
                    break;
                }
            }

            if(!$cus_type)
            {
                $errorPositions[$row][] = array_search('audience_type', $fields);
            }
            else
            {
                $data['cus_type'] = $cus_type;
            }

            if($cus_type == 1)//customer
            {
                if(!$data['email'])
                {
                    $errorPositions[$row][] = array_search('email', $fields);
                }
                else
                {
                    if(!$cusInfo = $CMS->customer->getInfo($data['email']))
                    {
                        //Add new customer
                        $cusData = [
                            'cus_full_name' => $data['name'],
                            'cus_email' => $data['email']
                        ];

                        $cusInfo = $CMS->customer->quickadd($cusData);
                        $CMS->input = [];
                        $data['cus_id'] = $cusInfo['cus_id'];
                    }
                    else
                    {
                        $data['cus_id'] = $cusInfo['cus_id'];
                    }
                }
            }
            elseif($cus_type == 2)//supplier
            {
                if(!$data['name'])
                {
                    $errorPositions[$row][] = array_search('name', $fields);
                }
                else
                {
                    if(!$supplierInfo = $CMS->supplier->get_info($data['name']))
                    {
                        //Add new supplier
                        $supplierData = [
                            'supplier_name' => $data['name'],
                            'supplier_email' => $data['email']
                        ];

                        $supplierInfo = $CMS->supplier->add($supplierData);
                        $CMS->input = [];
                        $data['supplier_id'] = $supplierInfo['supplier_id'];
                    }
                    else
                    {
                        $data['supplier_id'] = $supplierInfo['supplier_id'];
                    }
                }
            }
            elseif($cus_type == 3)//staff
            {
                if(!$data['email'])
                {
                    $errorPositions[$row][] = array_search('email', $fields);
                }
                else
                {
                    if(!$staffInfo = $CMS->user->get_info($data['email']))
                    {
                        //Add new user
                        $staffInfo = [
                            'user_name' => $data['email'],
                            'user_email' => $data['email'],
                            'user_display_name' => $data['name'],
                        ];

                        $staffInfo = $CMS->user->add($staffInfo,0);
                        $CMS->input = [];
                        $data['user_assign'] = $staffInfo['user_id'];
                    }
                    else
                    {
                        $data['user_assign'] = $staffInfo['user_id'];
                    }
                }
            }

            /**
             * Check account
             */

            if(trim($data['account_name']) != '')
            {
                if(!$accountInfo = $CMS->accounts->getInfo($data['account_name']))
                {
                    $account_id = $CMS->accounts->add(trim($data['account_name']),null, null, null, null, 1);
                    $data['trx_account'] = $account_id;
                }
                else
                {
                    $data['trx_account'] = $accountInfo['accounts_id'];
                }
            }

            /**
             * Check accounting account(định khoản)
             */
            if(trim($data['accounting_account']) != '')
            {
                if(!$accountsTypeInfo = $CMS->accounts_type->getInfo($data['accounting_account'],1))
                {
                    $accounts_type_id = $CMS->accounts_type->add(null,null,trim($data['accounting_account']),1);
                    $data['at_id'] = $accounts_type_id;
                }
                else
                {
                    $data['at_id'] = $accountsTypeInfo['accounts_type_id'];
                }
            }

            /**
             * Check item
             */
            if(!$data['transaction_item'])
            {
                $errorPositions[$row][] = array_search('transaction_item', $fields);
            }

            $CMS->vars['translations'] = [];
            $flagErrorCycle = false;

            $transaction_items = $data['transaction_item'];
            $transaction_items = preg_split('/\\r\\n|\\r|\\n/',$transaction_items);

            $data['trx_items'] = [];

            foreach($transaction_items as $trx_item)
            {
                $itemInfo = [];

                $explodeItem = explode("||",$trx_item);

                //get product type
                $productTypeData = explode(':',$explodeItem[0]);

                $productType = trim($productTypeData[0]);
                $productName = trim($productTypeData[1]);

                //Get quantity
                $productQuantityData = explode(':',$explodeItem[1]);
                $quantity = trim($productQuantityData[1]);

                //Get cycle
                $productCycleData = explode(':',$explodeItem[2]);
                $cycleInfo = explode("x", trim($productCycleData[1]));
                $cycle = trim($cycleInfo[0]);
                $cycleType = trim($cycleInfo[1]);

                //Get price
                $productPriceData = explode(':',$explodeItem[3]);
                $price = trim($productPriceData[1]);

                //Get tax
                $productTaxData = explode(':',$explodeItem[4]);
                $tax = trim($productTaxData[1]);
                $tax = str_replace('%','',$tax);

                if($productType=='' || $productName=='' || $quantity=='' || $cycleType=='' || $price=='' || $tax=='')
                {
                    $errorPositions[$row][] = array_search('transaction_item', $fields);
                }


                //Check product type
                $itemInfo['product_type'] = null;

                for($i=0; $i<=1; $i++)
                {
                    if(preg_match("/^{$langDataRegex['tri_type_'.$i]}$/i", $productTypeData[0]))
                    {
                        $itemInfo['product_type'] = $i;
                        break;
                    }
                }

                if($itemInfo['product_type'] !== null)
                {
                    if($itemInfo['product_type'] === 0)
                    {
                        $itemInfo['cycle_type']=0;
                        $itemInfo['cycle'] = 1;
                    }
                    else
                    {
                        if(!$cycleType)
                        {
                            $errorPositions[$row][] = array_search('transaction_item', $fields);
                        }
                        else
                        {
                            for($i=0; $i<=2; $i++)
                            {
                                if(preg_match("/^{$langDataRegex['cycle_type_'.$i]}$/i", $cycleType))
                                {
                                    $itemInfo['cycle_type'] = $i;
                                    break;
                                }
                            }

                            if($itemInfo['cycle_type'] === 0) //Once
                            {
                                $itemInfo['cycle'] = 1;
                            }
                            elseif($itemInfo['cycle_type'] == 1) //Month
                            {

                                $itemInfo['cycle'] = $cycle;
                            }
                            elseif($itemInfo['cycle_type'] == 2) //Yeasr
                            {
                                $itemInfo['cycle'] = $cycle;
                            }

                            if(!$itemInfo['cycle'])
                            {
                                $errorPositions[$row][] = array_search('transaction_item', $fields);
                            }
                        }
                    }

                    //Check product name
                    if(!$productInfo = $CMS->product->getInfo($productName))
                    {
                        $productData = [
                            'p_name' => $productName,
                            'p_show' => 1,
                            'p_type' => $itemInfo['product_type'],
                            'p_cycle' => $itemInfo['cycle_type'],
                            'p_price_sell' => floatval($price),
                            'p_tax' => floatval($tax),
                        ];
                        $productInfo = $CMS->product->add($productData,0);
                    }

                    $itemInfo['price'] = floatval($price);
                    $itemInfo['tax'] = floatval($tax);
                    $itemInfo['quantity'] = intval($quantity) ? intval($quantity) : 1;
                    $itemInfo['product'] = $productInfo;

                    $data['trx_items'][] = $itemInfo;
                }
            }

            /**
             * convert to insert
             */
            $itemData=[];

            if(!isset($errorPositions[$row]))
            {
                $itemData['type'] = $data['type'];
                $itemData['sub'] = $data['sub'];

                foreach($data['trx_items'] as $trx_item)
                {
                    $itemData['product_id'][] = $trx_item['product']['product_id'];
                    $itemData['product_name'][] = $trx_item['product']['product_name'];
                    $itemData['product_cycle_type'][] = $trx_item['cycle_type'];
                    $itemData['product_description'][] = $trx_item['product']['product_description'];
                    $itemData['product_cycle'][] = $trx_item['cycle'];
                    $itemData['product_quantity'][] = $trx_item['quantity'];
                    $itemData['product_price'][] = $trx_item['price'];
                    $itemData['product_tax'][] = $trx_item['tax'];
                }

                $calculate_result = $this->calculate($itemData);

                $CMS->input = [];

                $insertData = [
                    'type' => $data['type'],
                    'sub' => $data['sub'],
                    'trx_status' => 1, //paid
                    'cus_type' => $data['cus_type'],
                    'cus_id' => $data['cus_id'],
                    'supplier_id' => $data['supplier_id'],
                    'user_assign' => $data['user_assign'],
                    'trx_email' => $data['email'],
                    'trx_address' => $data['payment_address'],
                    'trx_payment_date' => date::format($data['payment_date']),
                    'trx_method' => $data['payment_method'],
                    'trx_reference_no' => $data['reference_number'],
                    'trx_account' => $data['trx_account'],
                    'at_id' => $data['at_id'],
                    'trx_contract_code' => $data['contract_code'],
                    'trx_total' => $calculate_result['total'],
                    'trx_amount' => $calculate_result['subtotal'],
                    'trx_tax' => $calculate_result['tax'],
                    'items' => $itemData,
                ];

                $validData[$row] = $insertData;
            }
            else
            {
                unset($validData[$row]);
            }
        }

        if($errorPositions)
        {
            foreach ($errorPositions as $errRow => $errCols)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension();

                //Đánh dấu dòng lỗi
                $objPHPExcel->getActiveSheet()->getStyle("A{$errRow}:{$highestColumn}{$errRow}")->applyFromArray($errStyleRow);

                foreach ($errCols as $errCol)
                {
                    //Đánh dấu các ô bị lỗi
                    $colName = $rangeChar[$errCol];
                    $objPHPExcel->getActiveSheet()->getStyle("{$colName}{$errRow}")->applyFromArray($errStyleCell);
                }
            }

            /**
             * Trả về file highlight các dòng bị lỡi cho khách sửa lại
             */
            //create excel folder
            if(!is_dir("{$CMS->vars['upload_dir']}/excel/"));
            {
                $CMS->class->image->check_folder_img('excel', "", 0);
            }

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $tmpFile = "import_trx_tpl_C{$member['user_id']}.xls";
            $objWriter->save("{$CMS->vars['upload_dir']}/excel/{$tmpFile}");
            $_SESSION['msg'] = "{$CMS->lang['invalid_import_data']} <a href='{$CMS->vars['upload_url']}/excel/{$tmpFile}'>Download file</a>";
            $CMS->global->redirectReferer();
        }

        /**
         * Loop dữ liệu để vào DB
         */


        foreach ($validData as $row => $data)
        {
            $trx = $CMS->transactions->add($data);
            $CMS->input = [];
            $this->addItem($trx['trx_id'],$data['items']);
            $CMS->input = [];
        }

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }

    function sendTrxEmail($trx_id = 0, $is_send = 1)
    {
        global $CMS;

        $data = $CMS->transactions->getInfo($trx_id);

        if(!$data) return false;

        //Send mail
        $lang_bk = $CMS->lang;
        $filename = root_path."language/en/admin_global.php";
        include($filename);
        $CMS->lang = $lang;
        $filename = root_path."language/en/admin_transactions.php";
        include($filename);
        $CMS->lang = array_merge($CMS->lang, $lang);

        $data = $CMS->transactions->convertvalue($data);

        $trx_status = strtoupper($CMS->lang['trx_status_0'.$data['data_bk']['trx_status']]);
        $trx_status = str_split($trx_status);
        $trx_status =  implode(" ",$trx_status);

        $CMS->email->data['trx_status'] = "<a style=\"background-color:{$data['trx_status_color']};color:#ffffff;padding:0 5px\">{$trx_status}</a>";

        $CMS->email->data['created_date'] = date::format($data['trx_time']);
        $CMS->email->data['trx_code'] = $data['trx_code'];

        $data['cus_info'] = $CMS->customer->getInfo($data['cus_id']);
        $data['supplier_info'] = $CMS->supplier->get_info($data['supplier_id']);
        $data['assign_info'] = $CMS->user->get_info($data['user_assign']);
        $CMS->email->data['object'] = $CMS->lang['cus_type_'.$data['cus_type'] ];

//        $CMS->email->data['website_logo'] = "http://3fs.vn/assets/images/logo.png";
        $CMS->email->data['website_logo'] = "<img src=\"{$CMS->vars['upload_url']}/attach/{$CMS->vars['logo_website']}\" alt=\"124x52\" width=\"124\" editable=\"true\" label=\"124x52\" />";

        $CMS->email->data['seller_name'] = $CMS->vars['print_company_name'] ? $CMS->vars['print_company_name'].'<br>':'';
        $CMS->email->data['seller_email'] = $CMS->vars['print_company_email'] ? $CMS->vars['print_company_email'].'<br>':'';
        $CMS->email->data['seller_phone'] = $CMS->vars['print_company_phone'] ? $CMS->vars['print_company_phone'].'<br>':'';
        $CMS->email->data['company_name'] = strip_tags($CMS->vars['print_company_name']);
        $CMS->email->data['copyright_name'] = strip_tags($CMS->vars['print_company_name']);

        if($data['cus_type'] == 1)
        {
            $CMS->email->data['name'] = $data['cus_info']['cus_full_name'];
            $CMS->email->data['email'] = $data['cus_info']['cus_email'];
            $CMS->email->data['phone'] = $data['cus_info']['cus_phone'];
        }
        else if($data['cus_type'] == 2)
        {
            $CMS->email->data['name'] = $data['supplier_info']['supplier_name'];
            $CMS->email->data['email'] = $data['supplier_info']['supplier_email'];
            $CMS->email->data['phone'] = $data['supplier_info']['supplier_phone'];
        }
        else if($data['cus_type'] == 3)
        {
            $CMS->email->data['name'] = $data['assign_info']['user_display_name'];
            $CMS->email->data['email'] = $data['assign_info']['user_email'];
            $CMS->email->data['phone'] = $data['assign_info']['user_phone'];
        }

        $CMS->email->email_to = $CMS->email->data['email'];
        $CMS->email->email_toname = $CMS->email->data['name'];

        $CMS->email->data['name'] = $CMS->email->data['name'] ? $CMS->email->data['name'].'<br>' : $CMS->email->data['name'];
        $CMS->email->data['email'] = $CMS->email->data['email'] ? $CMS->email->data['email'].'<br>' : $CMS->email->data['email'];
        $CMS->email->data['phone'] = $CMS->email->data['phone'] ? $CMS->email->data['phone'].'<br>' : $CMS->email->data['phone'];


        $this->html = $CMS->class->template->load_template("skin_transactions");

        if($data['trx_subtype'] == 1)
        {
            $this->sendInvoice($data);
        }
        else if($data['trx_subtype'] == 2)
        {
            $this->sendReceivedPayment($data);
        }
        else if($data['trx_subtype'] == 3)
        {
            $this->sendReceiptPayment($data);
        }
        else if($data['trx_subtype'] == 4)
        {
            $this->sendEstimate($data);
        }
        else if($data['trx_subtype'] == 5)
        {
            $this->sendBill($data);
        }
        else if($data['trx_subtype'] == 6)
        {
            $this->sendExpense($data);
        }
        else if($data['trx_subtype'] == 7)
        {
            $this->sendBillPayment($data);
        }
        else if($data['trx_subtype'] == 8)
        {
            $this->sendCreditMemo($data);
        }
        else
        {
            $is_send = 0;
        }

        if($is_send)
        {
            $CMS->email->quick_send(0,0);
        }

        $CMS->lang = $lang_bk;
    }

    function sendInvoice($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "invoice_info";

        $CMS->email->data['due_date'] = $data['trx_due_c'];
        $CMS->email->data['related_transactions'] = $this->html->show_receive_info_email($data['trx_receive_info']);
        $CMS->email->data['products_services'] = $this->html->show_product_items_email($data['trx_id']);
        $CMS->email->data['assets'] = $this->html->show_asset_items_email($data['trx_id']);
        $CMS->email->data['sub_total'] = $data['trx_amount_c'];
        $CMS->email->data['discount'] = $data['trx_discount_value_c'];
        $CMS->email->data['tax'] = $data['trx_tax_c'];
        $CMS->email->data['total'] = $data['trx_total_c'];
        $CMS->email->data['invoice_code'] = $data['trx_invoice_no_c'];
    }

    function sendReceivedPayment($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "received_payment_info";

        $CMS->email->data['related_transactions'] = $this->html->show_invoices_info_email($data['trx_invoice_info']);
        $CMS->email->data['total'] = $data['trx_total_c'];
    }

    function sendReceiptPayment($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "receipt_info";

        $CMS->email->data['products_services'] = $this->html->show_product_items_email($data['trx_id']);
        $CMS->email->data['assets'] = $this->html->show_asset_items_email($data['trx_id']);
        $CMS->email->data['sub_total'] = $data['trx_amount_c'];
        $CMS->email->data['discount'] = $data['trx_discount_value_c'];
        $CMS->email->data['tax'] = $data['trx_tax_c'];
        $CMS->email->data['total'] = $data['trx_total_c'];
    }

    function sendEstimate($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "estimate_info";

        $CMS->email->data['products_services'] = $this->html->show_product_items_email($data['trx_id']);
        $CMS->email->data['assets'] = $this->html->show_asset_items_email($data['trx_id']);
        $CMS->email->data['sub_total'] = $data['trx_amount_c'];
        $CMS->email->data['discount'] = $data['trx_discount_value_c'];
        $CMS->email->data['tax'] = $data['trx_tax_c'];
        $CMS->email->data['total'] = $data['trx_total_c'];
    }

    function sendBill($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "bill_info";

        $CMS->email->data['related_transactions'] = $this->html->show_receive_info_email($data['trx_receive_info']);
        $CMS->email->data['products_services'] = $this->html->show_product_items_email($data['trx_id']);
        $CMS->email->data['assets'] = $this->html->show_asset_items_email($data['trx_id']);
        $CMS->email->data['sub_total'] = $data['trx_amount_c'];
        $CMS->email->data['discount'] = $data['trx_discount_value_c'];
        $CMS->email->data['tax'] = $data['trx_tax_c'];
        $CMS->email->data['total'] = $data['trx_total_c'];
        $CMS->email->data['bill_code'] = $data['trx_invoice_no_c'];
    }

    function sendBillPayment($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "bill_payment_info";

        $CMS->email->data['related_transactions'] = $this->html->show_invoices_info_email($data['trx_invoice_info']);
        $CMS->email->data['total'] = $data['trx_total_c'];
    }

    function sendExpense($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "expense_info";

        $CMS->email->data['products_services'] = $this->html->show_product_items_email($data['trx_id']);
        $CMS->email->data['assets'] = $this->html->show_asset_items_email($data['trx_id']);
        $CMS->email->data['total'] = $data['trx_total_c'];
    }

    function sendCreditMemo($data)
    {
        global $CMS;

        if(!$data) return false;

        $CMS->email->email_template = "credit_memo_info";
        $CMS->email->data['credit_memo_date'] = $data['trx_credit_memo_date_c'];
        $CMS->email->data['related_transactions'] = $this->html->show_receive_info_email($data['trx_receive_info']);
        $CMS->email->data['products_services'] = $this->html->show_product_items_email($data['trx_id']);
        $CMS->email->data['assets'] = $this->html->show_asset_items_email($data['trx_id']);
        $CMS->email->data['sub_total'] = $data['trx_amount_c'];
        $CMS->email->data['discount'] = $data['trx_discount_value_c'];
        $CMS->email->data['tax'] = $data['trx_tax_c'];
        $CMS->email->data['total'] = $data['trx_total_c'];
        $CMS->email->data['amount_to_refund'] = $data['amount_to_refund_c'];
    }

    function calculateItem($price=0, $discountType=0, $discountValue=0, $taxPercent=0, $quantity=1, $cycle=1)
    {
        $quantity = $quantity ? $quantity : 1;
        $discountType = $discountType * 1;
        $discountValue = $discountValue * 1;
        $taxPercent = $taxPercent * 1;
        $cycle = $cycle ? $cycle : 1;
        $price = $price * 1;
        $subTotal = $price * $cycle * $quantity;
        $totalDiscount = $discountType == 0 ? ($subTotal * $discountValue) / 100 : $discountValue * $quantity;
        $total = $subTotal - $totalDiscount;
        $total = $total <= 0 ? 0 : $total;
        $totalTax = ($total * $taxPercent) / 100;
        $total = $total + $totalTax;

        $return = [
            'subTotal' => $subTotal*1,
            'totalDiscount' => $totalDiscount*1,
            'totalTax' => $totalTax*1,
            'total' => $total*1,
        ];

        return $return;
    }

    /**
     * Kiểm tra ràng buộc giữa các transaction khi edit
     * @param int $trx_id
     * @return bool
     */
    function checkLinked($trx_id = 0)
    {
        global $DB;

        $sql = "SELECT trx_id FROM ".root_table."transaction WHERE trx_id={$trx_id} AND ( (trx_receive_info IS NOT NULL AND trx_receive_info != '' AND trx_receive_info !='{}') OR (trx_invoice_info IS NOT NULL AND trx_invoice_info != '' AND trx_invoice_info !='{}') OR (trx_credit_memo_info IS NOT NULL AND trx_credit_memo_info != '' AND trx_credit_memo_info !='{}') )";

        $sql = $DB->query($sql);
        $num_rows = $DB->num_rows($sql);

        return $num_rows ? false : true;
    }


    /**
     * Check ràng buộc, cập nhật dữ liệu cho invoice và các receive liên quan
     * @param $data
     */
    function updateInfoInvoice($invoice = [])
    {
        global $CMS, $DB;

        if($invoice['trx_subtype'] != 1 && $invoice['trx_subtype'] != 5) return false;

        $unpaid_amount = $invoice['trx_total'] - $invoice['trx_receive_payment']; //Số tiền cần thanh toán

        $invoice['trx_receive_info'] = \lib\input::isJson($invoice['trx_receive_info']) ? \lib\input::jsonDecode($invoice['trx_receive_info']) : [];

        //Check receive và total
        if($unpaid_amount == 0) //Thanh toán đủ
        {
            $invoice['trx_status'] = 1; //paid
        }
        else if($unpaid_amount < 0) // Thanh toán thừa
        {
            $invoice['trx_status'] = 1; //paid

            $invoice['trx_receive_payment'] = $invoice['trx_total'];

            $temp_receive_payment = 0;

            $flag = 0;

            foreach($invoice['trx_receive_info'] as $receive_id => $receive_payment)
            {
                $temp_receive_payment += $receive_payment;

                if($temp_receive_payment > $invoice['trx_total'])
                {
                    $credit_amount = $flag ? $receive_payment : $temp_receive_payment - $invoice['trx_total'];//Số tiền cần hoàn lại vào receive
                    $debug[$receive_id] = $credit_amount;
                    $flag = 1; //đánh dấu khi lần đầu số tiền thanh toán vượt quá thành tiền

                    //Update lại receive info cho invoice hiện tại
                    if($flag > 1)
                    {
                        unset($invoice['trx_receive_info'][$receive_id]);
                    }
                    else
                    {
                        $invoice['trx_receive_info'][$receive_id] -= $credit_amount;
                    }

                    if(!$invoice['trx_receive_info'][$receive_id]) unset($invoice['trx_receive_info'][$receive_id]);

                    //Update lại thông tin cho các receive liên quan
                    $sql_receive = "SELECT trx_id, trx_invoice_info, trx_receive_payment, trx_total FROM ".root_table."transaction WHERE trx_subtype=2 AND trx_deleted=0 AND trx_id={$receive_id} LIMIT 1";
                    $sql_receive = $DB->query($sql_receive);

                    $receive = $sql_receive->fetch_assoc();

                    if($receive)
                    {
                        $receive['trx_invoice_info'] = \lib\input::isJson($receive['trx_invoice_info']) ? \lib\input::jsonDecode($receive['trx_invoice_info']) : [];

                        $receive['trx_invoice_info'][$invoice['trx_id']] -= $credit_amount;
                        if(!$receive['trx_invoice_info'][$invoice['trx_id']]) unset($receive['trx_invoice_info'][$invoice['trx_id']]);
                        $receive['trx_total'] -= $credit_amount;

                        $receive['trx_invoice_info'] = !empty($receive['trx_invoice_info']) && is_array($receive['trx_invoice_info']) ? \lib\input::jsonEncode($receive['trx_invoice_info'],0) : '';

                        $receive['trx_status'] = $receive['trx_receive_payment'] == $receive['trx_total'] ? 3 : 0;

                        //Update receive info
                        $sql_update = "UPDATE ".root_table."transaction SET trx_invoice_info='{$receive['trx_invoice_info']}', trx_status='{$receive['trx_status']}', trx_total='{$receive['trx_total']}' WHERE trx_id={$receive['trx_id']}";
                        $DB->query($sql_update);
                    }
                }
            }
        }
        else //thanh toán thiếu
        {
            $invoice['trx_status'] = 0; //waiting

            //Tìm receive còn khả dụng
            $sql_receive = "SELECT trx_id, trx_invoice_info, trx_receive_payment, trx_total, (trx_receive_payment - trx_total) AS balance FROM ".root_table."transaction WHERE trx_subtype=2 AND cus_type={$invoice['cus_type']} AND supplier_id={$invoice['supplier_id']} AND user_assign={$invoice['user_assign']} AND cus_id={$invoice['cus_id']} AND trx_deleted=0 HAVING balance>0 ORDER BY trx_id";

            $sql_receive = $DB->query($sql_receive);

            if($DB->num_rows($sql_receive))
            {
                while($receive = $sql_receive->fetch_assoc())
                {
                    if(!$unpaid_amount) return false;

                    $receive['trx_invoice_info'] = \lib\input::isJson($receive['trx_invoice_info']) ? \lib\input::jsonDecode($receive['trx_invoice_info']) : [];

                    if($receive['balance'] > $unpaid_amount)
                    {
                        $payment_amount = $unpaid_amount;
                        $unpaid_amount = 0;
                        $receive['trx_status'] = 0; //waiting
                    }
                    else
                    {
                        $payment_amount =  $receive['balance'];
                        $unpaid_amount -= $receive['balance'];
                        $receive['trx_status'] = 3; //Closed
                    }

                    $receive['trx_invoice_info'][$invoice['trx_id']] = (isset($receive['trx_invoice_info'][$invoice['trx_id']]) ? $receive['trx_invoice_info'][$invoice['trx_id']] : 0) + $payment_amount;
                    $receive['trx_total'] += $payment_amount;
                    $receive['trx_invoice_info'] = !empty($receive['trx_invoice_info']) && is_array($receive['trx_invoice_info']) ? \lib\input::jsonEncode($receive['trx_invoice_info'],0) : '';

                    //Update receive info
                    $sql_update = "UPDATE ".root_table."transaction SET trx_invoice_info='{$receive['trx_invoice_info']}', trx_status='{$receive['trx_status']}', trx_total='{$receive['trx_total']}' WHERE trx_id={$receive['trx_id']}";
                    $DB->query($sql_update);

                    $invoice['trx_receive_info'][$receive['trx_id']] = (isset($invoice['trx_receive_info'][$receive['trx_id']]) ? $invoice['trx_receive_info'][$receive['trx_id']] : 0) + $payment_amount;
                    $invoice['trx_receive_payment'] += $payment_amount;
                }
            }

            if($invoice['trx_total'] == $invoice['trx_receive_payment'])
            {
                $invoice['trx_status'] = 1; //paid
            }
        }

        //Update invoice
        $invoice['trx_receive_info'] = !empty($invoice['trx_receive_info']) && is_array($invoice['trx_receive_info']) ? \lib\input::jsonEncode($invoice['trx_receive_info'],0) : '';

        //Update lại thông tin cho invoice
        $sql_update = "UPDATE ".root_table."transaction SET trx_receive_payment='{$invoice['trx_receive_payment']}', trx_status='{$invoice['trx_status']}', trx_receive_info='{$invoice['trx_receive_info']}' WHERE trx_id={$invoice['trx_id']}";

        $DB->query($sql_update);

        if($unpaid_amount)
        {
            //Tìm credit memo còn khả dụng
            $sql_credit_memo = "SELECT trx_id, trx_invoice_info, trx_receive_payment, trx_total, (trx_total - trx_receive_payment) AS balance, trx_receive_info, cus_type, cus_id, supplier_id, user_assign, trx_subtype, trx_type FROM ".root_table."transaction WHERE trx_subtype=8 AND cus_type={$invoice['cus_type']} AND supplier_id={$invoice['supplier_id']} AND user_assign={$invoice['user_assign']} AND cus_id={$invoice['cus_id']} AND trx_deleted=0 HAVING balance>0 ORDER BY trx_id";

            $sql_credit_memo = $DB->query($sql_credit_memo);

            if($DB->num_rows($sql_credit_memo))
            {
                while($credit_memo = $sql_credit_memo->fetch_assoc())
                {
                    $this->updateInfoCreditMemo($credit_memo);
                }
            }

            //Do quy trình chuyển sang như add/update credit memo nên có thể stop ở chổ này
            return true;
        }
    }

    /**
     * Cập nhật lại thông tin của receive và các invoice liên quan khi add hoặc edit receive
     * @param $receive
     */
    function updateInfoReceive($receive = [], $oldReceive = [])
    {
        global $CMS, $DB;

        if($receive['trx_subtype'] != 2 && $receive['trx_subtype'] != 7) return false;

        $receive['trx_invoice_info'] = \lib\input::isJson($receive['trx_invoice_info']) ? \lib\input::jsonDecode($receive['trx_invoice_info']) : [];

        /*if(!$oldReceive) //Add news
        {
            $receive['trx_receive_payment'] = $receive['trx_total'];
        }*/

        $receive['trx_receive_payment'] = $receive['trx_receive_payment'] ? $receive['trx_receive_payment'] : 0;

        if(!$receive['trx_receive_payment'])
        {
            if(!empty($receive['trx_invoice_info']))
            {
                foreach ($receive['trx_invoice_info'] as $receive_payment)
                {
                    $receive['trx_receive_payment'] += $receive_payment;
                }
            }
        }


        $receive['trx_status'] = ($receive['trx_receive_payment'] == $receive['trx_total']) ? 3 : 0;

        //Update receive info
        $sql_update = "UPDATE ".root_table."transaction SET trx_receive_payment={$receive['trx_receive_payment']}, trx_status='{$receive['trx_status']}' WHERE trx_id='{$receive['trx_id']}'";
        $DB->query($sql_update);

        $related_id = is_array($receive['trx_invoice_info']) ? array_keys($receive['trx_invoice_info']) : [];

        $sql_add = '';

        if($related_id)
        {
            $related_id = implode(',',$related_id);

            $sql_add = " OR trx_id IN ($related_id) ";
        }

        if($receive['trx_subtype'] == 2)
        {
            //Lấy những invoice liên quan
            $sql_invoice = "SELECT trx_id, trx_receive_info, trx_total FROM ".root_table."transaction WHERE trx_deleted=0 AND trx_subtype=1 AND (trx_receive_info LIKE '%\"{$receive['trx_id']}\":%' {$sql_add}) ORDER BY trx_id";
        }
        else
        {
            //Lấy những bill liên quan
            $sql_invoice = "SELECT trx_id, trx_receive_info, trx_total FROM ".root_table."transaction WHERE trx_deleted=0 AND trx_subtype=5 AND (trx_receive_info LIKE '%\"{$receive['trx_id']}\":%' {$sql_add}) ORDER BY trx_id";
        }


        $sql_invoice = $DB->query($sql_invoice);

        if($DB->num_rows($sql_invoice))
        {
            while($invoice = $sql_invoice->fetch_assoc())
            {
                $invoice['trx_receive_info'] = \lib\input::isJson($invoice['trx_receive_info']) ? \lib\input::jsonDecode($invoice['trx_receive_info']) : [];

                if(!$receive['trx_invoice_info'][$invoice['trx_id']]) //invoice đã xóa khỏi receive
                {
                    unset($invoice['trx_receive_info'][$receive['trx_id']]);
                }
                else
                {
                    $invoice['trx_receive_info'][$receive['trx_id']] = $receive['trx_invoice_info'][$invoice['trx_id']];
                }

                $invoice['trx_receive_payment'] = 0;
                foreach($invoice['trx_receive_info'] as $receive_amount)
                {
                    $invoice['trx_receive_payment'] += $receive_amount;
                }

                $invoice['trx_status'] = ($invoice['trx_receive_payment'] == $invoice['trx_total']) ? 1 : 0;

                $invoice['trx_receive_info'] = (!empty($invoice['trx_receive_info']) && is_array($invoice['trx_receive_info'])) ? \lib\input::jsonEncode($invoice['trx_receive_info'],0) : '';

                //Update invoice info
                $sql_update = "UPDATE ".root_table."transaction SET trx_receive_info='{$invoice['trx_receive_info']}', trx_receive_payment='{$invoice['trx_receive_payment']}', trx_status='{$invoice['trx_status']}' WHERE trx_id='{$invoice['trx_id']}'";
                $DB->query($sql_update);
            }
        }
        return true;
    }

    /**
     * Cập nhật lại thông tin của credit memo và các receive payment liên quan khi add hoặc edit credit memo
     * @param $credit_memo
     */
    function updateInfoCreditMemo($credit_memo = [])
    {
        global $CMS, $DB;

        if($credit_memo['trx_subtype'] != 8) return false;

        //Backup input
        $backup_input = $CMS->input;

        $credit_amount = $credit_memo['trx_total'] - $credit_memo['trx_receive_payment']; //Số dư khả dụng

        $credit_memo['trx_receive_info'] = $old_trx_receive_info = \lib\input::isJson($credit_memo['trx_receive_info']) ? \lib\input::jsonDecode($credit_memo['trx_receive_info']) : [];

        if($credit_amount < 0)
        {
            $credit_amount = abs($credit_amount);
            krsort($credit_memo['trx_receive_info']); //Đảo mảng lại để duyệt từ sau lên trước

            //Duyệt qua các receive có liên quan
            foreach($credit_memo['trx_receive_info'] as $receive_id => $receive_amount)
            {
                if(!$credit_amount) break;

                $receive = $oldReceive = $this->getInfo($receive_id);

                $receive['trx_invoice_info'] = \lib\input::isJson($receive['trx_invoice_info']) ? \lib\input::jsonDecode($receive['trx_invoice_info']) : [];

                //Trừ tiền ở receive và cho cập nhật lại ở các invoice có liên quan
                if($credit_amount > $receive_amount)
                {
                    $payment_amount = $receive_amount;
                    $credit_amount -= $payment_amount;
                    $receive_amount = 0;
                }
                else
                {
                    $payment_amount = $credit_amount;
                    $receive_amount -= $payment_amount;
                    $credit_amount = 0;
                }

                $credit_memo['trx_receive_info'][$receive_id] -= $payment_amount;

                $receive['trx_amount'] -= $payment_amount;
                $receive['trx_receive_payment'] -= $payment_amount;
                $receive['trx_total'] -= $payment_amount;

                foreach($receive['trx_invoice_info'] as $inv_id => $inv_amount)
                {
                    if(!$payment_amount) break;

                    if($payment_amount > $inv_amount)
                    {
                        $payment_amount -= $inv_amount;
                        $inv_amount = 0;
                    }
                    else
                    {
                        $inv_amount -= $payment_amount;
                        $payment_amount = 0;
                    }

                    if(!$inv_amount) unset($receive['trx_invoice_info'][$inv_id]);

                    $receive['trx_invoice_info'][$inv_id] = $inv_amount;
                }

                if(!$credit_memo['trx_receive_info'][$receive_id]) unset($credit_memo['trx_receive_info'][$receive_id]);

                $receive['trx_invoice_info'] = !empty($receive['trx_invoice_info']) && is_array($receive['trx_invoice_info']) ? \lib\input::jsonEncode($receive['trx_invoice_info'],0) : '';

                $DB->update('transaction',$receive,'trx_id');

                $this->updateInfoReceive($receive, $oldReceive); //Cập nhật lại thông tin cho receive

                if($receive['trx_total'] == 0)
                {
                    //Delete receive
                    $sql_delete = "DELETE FROM ".root_table."transaction WHERE trx_id={$receive['trx_id']}";
                    $DB->query($sql_delete);
                }
            }

            if(!empty($credit_memo['trx_receive_info']))
            {
                krsort($credit_memo['trx_receive_info']);
            }

            $credit_memo['trx_receive_payment'] = $credit_memo['trx_total'];
        }
        else
        {
            //Lấy các invoice chưa thanh toán
            $sql_invoice = "SELECT trx_id, trx_receive_payment, trx_total, (trx_total-trx_receive_payment) unpaid_amount FROM ".root_table."transaction WHERE trx_subtype=1 AND trx_deleted=0 AND cus_type={$credit_memo['cus_type']} AND cus_id={$credit_memo['cus_id']} AND supplier_id={$credit_memo['supplier_id']} AND user_assign={$credit_memo['user_assign']} HAVING unpaid_amount>0 ORDER BY trx_id";
            $sql_invoice = $DB->query($sql_invoice);

            if($DB->num_rows($sql_invoice))
            {

                //Tạo trx_invoice_info cho receive info sắp tạo
                $trx_invoice_info = [];
                $trx_total = 0;

                while($invoice = $sql_invoice->fetch_assoc())
                {
                    if(!$credit_amount) break;

                    if($credit_amount > $invoice['unpaid_amount'])
                    {
                        $payment_amount = $invoice['unpaid_amount'];
                        $credit_amount -= $invoice['unpaid_amount'];
                    }
                    else
                    {
                        $payment_amount = $credit_amount;
                        $invoice['trx_receive_payment'] -= $credit_amount;
                        $credit_amount = 0;
                    }

                    $trx_total += $payment_amount;

                    $credit_memo['trx_receive_payment'] += $payment_amount;

                    $trx_invoice_info[$invoice['trx_id']] = $payment_amount;

                    if(!$trx_invoice_info[$invoice['trx_id']]) unset($trx_invoice_info[$invoice['trx_id']]);
                }

                //Tạo receive payment transaction
                $receive_data = [
                    'type' => 1,
                    'sub' => 2,
                    'trx_email' => $credit_memo['trx_email'],
                    'trx_payment_date' => date("d/m/Y"),
                    'cus_type' => $credit_memo['cus_type'],
                    'cus_id' => $credit_memo['cus_id'],
                    'supplier_id' => $credit_memo['supplier_id'],
                    'user_assign' => $credit_memo['user_assign'],
                    'trx_account' => $credit_memo['trx_account'],
                    'trx_reference_no' => $credit_memo['trx_reference_no'],
                    'trx_total' => $trx_total,
                    'trx_receive_payment' => $trx_total,
                    'tri_payment' => $trx_invoice_info
                ];

                $receive = $this->add($receive_data);

                $credit_memo['trx_receive_info'][$receive['trx_id']] = $credit_memo['trx_receive_info'][$receive['trx_id']]*1 + $trx_total;
            }
        }

        $new_trx_receive_info = $credit_memo['trx_receive_info'];

        //Update lại giá trị cho credit memo
        $credit_memo['trx_status'] = ($credit_memo['trx_receive_payment'] == $credit_memo['trx_total']) ? 3 : 0;

        $credit_memo['trx_receive_info'] = !empty($credit_memo['trx_receive_info']) && is_array($credit_memo['trx_receive_info']) ? \lib\input::jsonEncode($credit_memo['trx_receive_info'],0) : '';

        $sql_update_credit_memo = "UPDATE ".root_table."transaction SET trx_receive_payment={$credit_memo['trx_receive_payment']}, trx_status={$credit_memo['trx_status']}, trx_receive_info='{$credit_memo['trx_receive_info']}' WHERE trx_id={$credit_memo['trx_id']}";

        $DB->query($sql_update_credit_memo);

        //Cập nhật lại credit memo cho receive
        $new_trx_receive_info = array_keys($new_trx_receive_info);
        $new_trx_receive_info = !empty($new_trx_receive_info) ? $new_trx_receive_info : [];
        $old_trx_receive_info = array_keys($old_trx_receive_info);
        $old_trx_receive_info = !empty($old_trx_receive_info) ? $old_trx_receive_info : [];
        $receive_ids = array_merge($old_trx_receive_info,$new_trx_receive_info);
        $receive_ids = array_unique($receive_ids);

        foreach($receive_ids as $receive_id)
        {
            $this->updateTrxCreditMemoInfoForReceive($receive_id);
        }

        //Restore input
        $CMS->input = $backup_input;
    }

    /**
     * Cập nhật lại danh sách credit memo cho receive
     * @param int $receive_id
     * @return boolean
     */
    function updateTrxCreditMemoInfoForReceive($receive_id=0)
    {
        global $CMS, $DB;

        $sql = "SELECT trx_id, trx_receive_info FROM ".root_table."transaction WHERE trx_subtype=8 AND trx_deleted=0 AND trx_receive_info LIKE '%\"{$receive_id}\":%' ORDER BY trx_id";

        $sql = $DB->query($sql);

        $trx_credit_memo_info = [];

        if($DB->num_rows($sql))
        {
            while($credit = $sql->fetch_assoc())
            {
                $trx_receive_info = \lib\input::isJson($credit['trx_receive_info']) ? \lib\input::jsonDecode($credit['trx_receive_info']) : null;

                if($trx_receive_info[$receive_id])
                {
                    $trx_credit_memo_info[$credit['trx_id']] = $trx_receive_info[$receive_id];
                }
            }
        }
        $trx_credit_memo_info = !empty($trx_credit_memo_info) ? \lib\input::jsonEncode($trx_credit_memo_info,0) : '';

        $sql_update = "UPDATE ".root_table."transaction SET trx_credit_memo_info='{$trx_credit_memo_info}' WHERE trx_id={$receive_id}";

        return $DB->query($sql_update);
    }

    /**
    * ThamLV-Y2018M09D01: calculate shipping fee
    */
    function calculateShippingFee( $data = [], $original_total = 0 )
    {
        global $CMS, $DB;

        // Backup inputs
        $input_bk = $CMS->input;

        if( $data ) 
        {
            $CMS->input = array_merge($CMS->input, $data);
        }

        $fee_shipping = 0;
        if( isset($CMS->input['ord_fee_shipping']) )
        {
            $fee_shipping = $CMS->input['ord_fee_shipping']*1;
        }
        else if( isset($CMS->input['ship_country']) AND isset($CMS->input['ship_method']) )
        {
            // Inputs
            // ship_type_service: 0:Standard; 1: Express 
            // shipping location: 0: Domestics , 1: International 
            $ship_type_service = $CMS->input['ship_method'] == 0 ? 0 : 1;
            $ship_location = (isset($CMS->vars['default_shiping_location']) AND strtoupper($CMS->vars['default_shiping_location']) == strtoupper($CMS->input['ship_country'])) ? 0 : 1;

            // First check original total for shipping free
            $default_shiping_free = isset($CMS->vars['default_shiping_free']) ? $CMS->vars['default_shiping_free']*1 : 0;
            $original_total = $original_total*1;
            if( $default_shiping_free > 0 AND $default_shiping_free <= $original_total )
            {
                $fee_shipping = 0;
            }

            // Second, calculate shipping on each product 
            else
            {
                if( !empty($CMS->input['product_quantity']) AND is_array($CMS->input['product_quantity']) )
                {
                    // Convert inputs
                    $inputData = [];
                    foreach( $CMS->input['product_quantity'] as $k => $product_quantity ) 
                    {
                        $product_id         = isset($CMS->input['product_id'][$k]) ? $CMS->input['product_id'][$k]*1 : 0;
                        $product_name       = isset($CMS->input['product_name'][$k]) ? $CMS->input['product_name'][$k] : '';
                        $product_quantity   = $product_quantity*1;

                        if( $product_name AND $product_id > 0 AND $product_quantity > 0 )
                        {
                            if( isset($inputData[$product_id]) )
                            {
                                $inputData[$product_id]['product_quantity'] += $product_quantity;
                            }
                            else
                            {
                                $inputData[$product_id] = array(
                                    'product_id'        => $product_id, 
                                    'product_name'      => $product_name, 
                                    'product_quantity'  => $product_quantity, 
                                );
                            }
                        }
                    }
                    
                    // Calculate
                    foreach( $inputData as $product ) 
                    {
                        // Get shipping fee of product from config
                        $shipping_fee = $this->getShippingFee($product['product_id'], $ship_type_service, $ship_location );

                        // Fee of first product
                        if( isset($shipping_fee['ship_price']) )
                        {
                            $fee_shipping += $shipping_fee['ship_price'];
                        }

                        // Fee of 2nd product onwards
                        $product_quantity = $product['product_quantity'] - 1;
                        if( $product_quantity > 0 AND isset($shipping_fee['ship_price_extra']) )
                        {
                            $fee_shipping += $product_quantity*$shipping_fee['ship_price_extra'];
                        }
                    }
                }
            }
        }

        // Revert inputs
        $CMS->input = $input_bk; 

        return $fee_shipping;
    }

    /**
     * Get infomation
     * @param int $product_id, int $ship_type_service, int $ship_location
     * @return array/ bool
     */
    public function getShippingFee( $product_id = 0, $ship_type_service = 0, $ship_location = 0 )
    {
        global $CMS, $DB;

        $output = false;

        $sql = "
        SELECT * 
        FROM ".root_table."shipping_fee 
        WHERE ship_deleted = 0 AND product_id = '{$product_id}' AND ship_type_service = '{$ship_type_service}' AND ship_location = '{$ship_location}' 
        ORDER BY ship_id DESC 
        LIMIT 1 
        ";
        // print $sql; exit;

        $data = $DB->fetch_data($sql, 'shipping_fee');
        $output = isset($data[0]) ? $data[0] : false;

        return $output;
    }

    /**
     * Get infomation for search
     * @param int $id
     * @return array
     */
    static public function getInfoSearch( $id = 0, $field_name = '*', $sql_add = '' )
    {
        global $CMS, $DB;

        $output = false;

        if( $id )
        {
            $sql_add .= is_numeric($id) ? "AND trx_id='{$id}'" : "AND trx_code='{$id}'";

            $sql = "
            SELECT {$field_name} 
            FROM ".root_table."transaction 
            WHERE trx_deleted=0 {$sql_add} 
            ORDER BY trx_id DESC 
            LIMIT 1 
            ";
            // print $sql; exit;

            $sql = $DB->query($sql);
            $data = $DB->num_rows() > 0 ? $DB->fetch_array() : false;

            if ( $field_name !== '*' AND count(explode(",", $field_name)) == 1 ) 
            {
                if( isset($data[$field_name]) )
                {
                    $output = $data[$field_name];
                }
            }
            else
            {
                $output = $data;
            }
        }

        return $output;
    }
}
