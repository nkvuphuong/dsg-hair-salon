<?php
class skin_transactions_form {
	public function form1($data = [])
    {
        global $CMS, $DB, $member;

        $out = <<<EOF
        <section class="add_form main_form">
                <figure class="box-typical box-typical box-typical-padding border">
                    {$this->store_options($data['store_id'])}
                    <div class="row">
                        <div class="col-xl-10 col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-3 col-lg-5 col-md-7 col-sm-7 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-7 col-sm-7 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_email']}</label>
                                        <input class="form-control " type="text" name="trx_email" id="trx_email"  data-validation-regex="/^$|^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['trx_cus_email_err']}" value="{$data['trx_email']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-4 col-lg-3 col-md-5 col-sm-5 col-xs-12">
                                    <label class="form-label hidden-sm-down">&nbsp;</label>
                                    <div class="checkbox">
                                        <input type="checkbox" id="trx_send_email" name="trx_send_email">
                                        <label for="trx_send_email"><em style="font-size: 12px">{$CMS->lang['trx_send_email']}</em></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_address']} <em>({$CMS->lang['remain']} <span class="address-limiter"></span> {$CMS->lang['character']})</em></label>
                                        <input class="form-control " type="text" name="trx_address" id="trx_address"  value="{$data['trx_address']}">
                                        <script>
                                            $("#trx_address").limiter(255, $(".address-limiter"));
                                        </script>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: -28px">
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_payment_date']}</label>
                                        <input class="form-control " type="text" name="trx_payment_date" id="trx_payment_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_payment_date_err']}" value="{$data['trx_payment_date']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <div style="clear: both; overflow: hidden;">
                                            <label class="form-label pull-left">{$CMS->lang['trx_terms']}</label>
                                            <div class="box_action pull-right">
                                                    {$data['btn_add_term']}
                                                    <span class="box_edit_term pull-right" style="margin-left: 10px;">{$data['btn_edit_term']}</span>
                                            </div>
                                        </div>
                                        <div class="box_container">
                                            <div class="input-group">
                                                <input class="form-control " type="hidden" name="trx_terms" id="trx_terms" value="{$data['trx_terms']}">
                                                <input class="form-control " type="text" name="trx_term_name" id="trx_term_name" value="{$data['trx_term_name']}">
                                                <div class="input-group-addon" style="cursor:pointer" onclick="autocompleteAll('#trx_term_name')">
                                                    <span class="fa fa-arrow-down"></span>
                                                </div>
                                                <script>
                                                $(document).ready(() =>  autocompleteSearch('#trx_term_name', site_root_domain + '/?site=transaction_terms&subact=dataAjax', '#trx_terms_ajax','term',0))
                                                </script>          
                                            </div>
                                            <div id="trx_terms_ajax" class="form-control" style="position: relative; display: none;"></div>
                                        </div> 
                                    </fieldset>    
                                </div>
                                <div class="hidden-md-up col-xs-12"></div>
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_due']}</label>
                                        <p class="form-control-static-input">
                                            <input name="trx_due_date" id="trx_due_date" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_due_date_err']}"  value="{$data['trx_due_date']}">
                                        </p>
                                    </fieldset>
                                </div>
                                <div class="hidden-sm-down col-sm-12"></div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_contract_code']}</label>
                                        <input class="form-control " type="text" name="trx_contract_code" id="trx_contract_code" value="{$data['trx_contract_code']}">
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12 col-xs-12 text-sm-left text-md-right">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price"><span class="total-show">0</span></p>
                            {$data['status_display']}
                        </div>
                    </div>
                </figure>
        </section>
    {$CMS->global->htmlTableProduct( $CMS->input['act'] == 'edit' ? $CMS->product->convert_trx_item_old($data['item']['product']) : $CMS->product->convert_input_old($CMS->input),1 )}
    {$CMS->global->htmlTableAsset( $CMS->input['act'] == 'edit' ? $CMS->assets->convert_trx_item_old($data['item']['asset']) :  $CMS->assets->convert_input_old($CMS->input) )}
    {$this->total($data)}
    {$this->footer($data)}


<script>
    var module_name = "module_transaction";
</script>


EOF;

        return $out;
    }

    public function form2($data = [])
    {
        global $CMS, $DB, $member;

        $data['trx_email'] = $data['trx_email'] ? $data['trx_email'] : $data['cus_info']['cus_email'];

        $out = <<<EOF
     
<script>
    var module_name = "module_transaction";
</script>


        <section class="add_form main_form">
                
                {$this->header($data)}
                
                <figure class="box-typical box-typical box-typical-padding border">
                    {$this->store_options($data['store_id'])}
                    <div class="row">
                        <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                                <div class="row hidden-lg-up"></div>
                                <div class="col-xl-3 col-lg-3 col-md-5 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_email']}</label>
                                        <input class="form-control " type="text" name="trx_email" id="trx_email" data-validation-regex="/^$|^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}" value="{$data['trx_email']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-4 col-lg-5 col-md-7 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="hidden-xs-down">&nbsp;</label>
                                        <div class="dropdown" style="position: relative !important;">
                                            <button type="button" class="btn dropdown-toggle btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  id="searchInvoiceDropDown">{$CMS->lang['trx_by_invoice']}</button>
                                        
                                            <div class="dropdown-menu" aria-labelledby="searchInvoiceDropDown" style="left: 0 !important;">
                                                <figure class="box-typical box-typical-padding border">
                                                    <div class="form-group">
                                                        <figure class="heading">{$CMS->lang['trx_invoice_id']}</figure>
                                                        <input type="text" class="form-control" id="search_invoice_id">
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-danger" data-toggle="dropdown">{$CMS->lang['comment_reset']}</button>
                                                        <button class="btn btn-primary" onclick="searchInvoice('{$CMS->input['type']}', $('[name=cus_type]:checked').val())">{$CMS->lang['search_form']}</button>
                                                    </div>
                                                </figure>                                       
                                            </div> 
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 hidden-md-down">
                                    &nbsp;
                                </div>
                                <div class="col-xl-4 col-lg-3 col-md-5 col-sm-12 col-xs-12">  
                                    <div class="checkbox">
                                        <input type="checkbox" id="trx_send_email" name="trx_send_email">
                                        <label for="trx_send_email"><em style="font-size: 12px">{$CMS->lang['trx_send_email']}</em></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-3 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_payment_date2']}</label>
                                        <input class="form-control " type="text" name="trx_payment_date" id="trx_payment_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_payment_date_err2']}"  value="{$data['trx_payment_date']}">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_method']}</label>
                                        <select class="form-control select2" name="trx_method" id="trx_method" data-validation="[NOTEMPTY]" data-validation-message="" >
                                            {$data['option_trx_method']}
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_reference_no']}</label>
                                        <input class="form-control hide_show_account" type="text" name="trx_reference_no" id="trx_reference_no" value="{$data['trx_reference_no']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_account']}</label>
                                        <select class="form-control hide_show_account select2" name="trx_account" id="trx_account" >
                                            {$data['option_trx_account']}
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['at_id']}</label>
                                        <select name="at_id" id="at_id" class="form-control select2">
                                            <option>--- {$CMS->lang['at_id']} ---</option>
                                            {$CMS->transactions->loadAccountTypeOption($data['at_id'])}
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-xs-12 text-xl-right text-lg-left text-md-left text-sm-left text-xs-left">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price" id="total_price"><span class="total_tax_show">0</span></p>
                            {$data['status_display']}
                        </div>
                    </div>
            </figure>
    </section>
        <section class="add_table">
        
EOF;

        $trx_invoice_info = @json_decode($data['trx_invoice_info'], true);

            if($_SESSION['is_mobile'] == true)
            {
                $out .=<<<EOF
				<div id="getUnbilledInvoices" class="table-responsive" style="overflow-y: visible !important;">
EOF;
            }
else
{
$out .=<<<EOF
                <div id="getUnbilledInvoices" class="table-responsive" style="overflow-x: visible !important;
                          overflow-y: visible !important;">
EOF;
}

$out .=<<<EOF
                    <thead>
EOF;

            if($_SESSION['is_mobile'] == true)
            {
                $out .=<<<EOF
				<table id="item_line" class="table_cus" width="100%">
EOF;
            }
            else
            {
                $out .=<<<EOF
                <table id="item_line" class="responsive-table table_cus">
EOF;
            }

            $out .=<<<EOF
                    <thead>
EOF;


            if(!$trx_invoice_info)
            {
                if($CMS->input['invoice'])
                {
                    $data_info = $CMS->transactions->getInfo($CMS->input['invoice']);

                    if($data_info)
                    {
                        if($CMS->input['sub'] == 2)
                        {
                            $subtype = 1;
                        }
                        else if($CMS->input['sub'] == 7)
                        {
                            $subtype = 5;
                        }
                        else
                        {
                            $subtype = 0;
                        }

                        if($data_info['cus_type'] == 1)
                        {
                            $trx_invoice_info = $CMS->transactions->getUnbilledByCustomer($data_info['cus_id'], 0, $subtype, $data_info['cus_type'], $data_info['trx_id']);
                        }
                        else if($data_info['cus_type'] == 2)
                        {
                            $trx_invoice_info = $CMS->transactions->getUnbilledByCustomer($data_info['supplier_id'], 0, $subtype, $data_info['cus_type'], $data_info['trx_id']);
                        }
                        else if($data_info['cus_type'] == 3)
                        {
                            $trx_invoice_info = $CMS->transactions->getUnbilledByCustomer($data_info['user_assign'], 0, $subtype, $data_info['cus_type'], $data_info['trx_id']);
                        }


                        $load_from_db = 1;
                    }
                }
            }

            $out .= <<<EOF
                        <tr>
                            <th scope="col" width="2%"></th>
                            <th scope="col">{$CMS->lang['table_inv_code']}</th>
                            <th scope="col">{$CMS->lang['trx_due_2']}</th>
                            <th scope="col">{$CMS->lang['trx_original_2']}</th>
                            <th scope="col">{$CMS->lang['trx_open_2']}</th>
                            <th scope="col">{$CMS->lang['trx_payment_2']}</th>
                        </tr>
                    </thead>
                    <tbody>
EOF;

            if(!$trx_invoice_info)
            {
                $out .= <<<EOF
            <script>
                $(document).ready(() => {
                    $("#getUnbilledInvoices").hide();
                })
            </script>
EOF;

            }

            if($load_from_db)
            {
                foreach($trx_invoice_info as $invoice_info)
                {
                    $invoice_info['trx_due_date'] = $CMS->class->date->date_format($invoice_info['trx_due_date']);

                    $invoice_total = $invoice_info['trx_total'] - $invoice_info['trx_receive_payment'];

                    $prefix_trx = $invoice_info['trx_type'] == 2 ? 'PAY' : 'INV';

//                    $remain_amount = $invoice_info['trx_total'] - $invoice_info['trx_receive_payment'] + $invoice_total;
                    $remain_amount = $invoice_total;

                    $out .= <<<EOF
                    <tr>
                        <td>
                            <input type='checkbox' id ='invoice_{$invoice_info['trx_id']}' name='item[{$invoice_info['trx_id']}]' onchange='changeCheckInvoiceParent(this, "{$invoice_info['trx_id']}")' class='parent' >
                        </td>
                        <td><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$invoice_info['trx_id']}">{$prefix_trx}{$invoice_info['trx_invoice_no']}</a></td>
                        <td>{$invoice_info['trx_due_date']}</td>
                        <td>{$invoice_info['trx_total']}</td>
                        <td>
                            {$remain_amount}
                        </td>
                        <td>
                            <div class="form-group">
                                <div style="position: relative">
                                    <input type='number' class='form-control' name='tri_payment[{$invoice_info['trx_id']}]' value='{$invoice_total}' onchange='changeTotalTax()' data-validation="[V<={$remain_amount}]" data-validation-message="{$CMS->lang['error_max_payment_amount']}" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
                                </div>
                            </div>
                        </td>
                    </tr>
EOF;

                }
            }
            else
            {

                foreach($trx_invoice_info as $invoice_id => $invoice_total)
                {
                    $invoice_info = $CMS->transactions->getInfo($invoice_id);
                    $invoice_info['trx_due_date'] = $CMS->class->date->date_format($invoice_info['trx_due_date']);

                    $prefix_trx = $invoice_info['trx_type'] == 2 ? 'PAY' : 'INV';

                    $remain_amount = $invoice_info['trx_total'] - $invoice_info['trx_receive_payment'] + $invoice_total;

                    $out .= <<<EOF
                    <tr>
                        <td>
                            <input type='checkbox' checked id ='invoice_{$invoice_info['trx_id']}' name='item[{$invoice_info['trx_id']}]' onchange='changeCheckInvoiceParent(this, "{$invoice_info['trx_id']}")' class='parent' >
                        </td>
                        <td><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$invoice_info['trx_id']}">{$prefix_trx}{$invoice_info['trx_invoice_no']}</a></td>
                        <td>{$invoice_info['trx_due_date']}</td>
                        <td>{$invoice_info['trx_total']}</td>
                        <td>
                            {$remain_amount}
                        </td>
                        <td>
                            <div class="form-group">
                                <div style="position: relative">
                                    <input type='number' class='form-control' name='tri_payment[{$invoice_info['trx_id']}]' value='{$invoice_total}' onchange='changeTotalTax()' data-validation="[V<={$remain_amount}]" data-validation-message="{$CMS->lang['error_max_payment_amount']}" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
                                </div>
                            </div>
                        </td>
                    </tr>
EOF;

                }
            }


                $out .= <<<EOF
                    </tbody>
                </table>
                </div>
            </section>
            <script>changeTotalTax();</script>
            {$this->note_form($data)}
            {$this->footer($data)}
EOF;

            if($data['cus_type'] == 1) //KH
            {
                $cus_type_id = $data['data_info']['cus_id'];
            }
            elseif($data['cus_type'] == 2) //NCC
            {
                $cus_type_id = $data['data_info']['supplier_id'];
            }
            elseif($data['cus_type'] == 3) //NV
            {
                $cus_type_id = $data['data_info']['user_assign'];
            }
            else
            {
                $cus_type_id = 0;
            }

            if (isset($CMS->input['invoice'])) {
                $tmp = isset($data['data_info']['trx_invoice_no']) && $data['data_info']['trx_invoice_no'] > 0 ? $data['data_info']['trx_invoice_no'] : $data['data_info']['trx_id'];
                $out .= <<<EOF
                <script>
                    getInvoice("{$cus_type_id}", {$tmp}, "{$CMS->input['type']}", "{$data['cus_type']}");
                </script>
EOF;
            }

        return $out;
    }

    public function form3($data = [])
    {
        global $CMS, $DB, $member;

        $out = <<<EOF
<script>
    var module_name = "module_transaction";
</script>


        <section class="add_form main_form">
                
                {$this->header($data)}
                
                <figure class="box-typical box-typical box-typical-padding border">
                    {$this->store_options($data['store_id'])}
                    <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-4 col-lg-5 col-md-4 col-sm-6 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_email']} <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_email" id="trx_email" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_cus_email_err']}" data-validation-regex="/^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['trx_cus_email_err']}" value="{$data['trx_email']}">
                                    </fieldset>
                                </div>
                                
                                <div class="col-xs-6 hidden-md-up  hidden-xs-down"> 
                                    <label>&nbsp;</label>
                                </div>
                                
                                <div class="col-xl-4 col-lg-3 col-md-4 col-sm-4 col-xs-12">  
                                    <label class="hidden-sm-down">&nbsp;</label>
                                    <div class="checkbox">
                                        <input type="checkbox" id="trx_send_email" name="trx_send_email">
                                        <label for="trx_send_email"><em style="font-size: 12px">{$CMS->lang['trx_send_email']}</em></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-5 col-sm-9 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_address']} <em>({$CMS->lang['remain']} <span class="address-limiter"></span> {$CMS->lang['character']})</em> <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_address" id="trx_address" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_address_err']}" value="{$data['trx_address']}">
                                        <script>
                                            $("#trx_address").limiter(255, $(".address-limiter"));
                                        </script>
                                    </fieldset>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_payment_date2']}</label>
                                        <input class="form-control " type="text" name="trx_payment_date" id="trx_payment_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_payment_date_err2']}"  value="{$data['trx_payment_date']}">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_method']}</label>
                                        <select class="form-control select2" name="trx_method" id="trx_method" data-validation="[NOTEMPTY]" data-validation-message="" >
                                            {$data['option_trx_method']}
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-2 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_reference_no']}</label>
                                        <input class="form-control hide_show_account" type="text" name="trx_reference_no" id="trx_reference_no" value="{$data['trx_reference_no']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_account']}</label>
                                        <select class="form-control hide_show_account select2" name="trx_account" id="trx_account" >
                                            {$data['option_trx_account']}
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['at_id']}</label>
                                        <select name="at_id" id="at_id" class="form-control select2">
                                            <option>--- {$CMS->lang['at_id']} ---</option>
                                            {$CMS->transactions->loadAccountTypeOption($data['at_id'])}
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_contract_code']}</label>
                                        <input class="form-control " type="text" name="trx_contract_code" id="trx_contract_code" value="{$data['trx_contract_code']}">
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-xs-12 text-xl-right  text-lg-right  text-md-left text-sm-left text-xs-left">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price" id="total_price"><span class="total-show">0</span></p>
                            {$data['status_display']}
                        </div>
                    </div>
                </figure>
        </section>
        {$CMS->global->htmlTableProduct( $CMS->input['act'] == 'edit' ? $CMS->product->convert_trx_item_old($data['item']['product']) : $CMS->product->convert_input_old($CMS->input),1 )}
    {$CMS->global->htmlTableAsset( $CMS->input['act'] == 'edit' ? $CMS->assets->convert_trx_item_old($data['item']['asset']) :  $CMS->assets->convert_input_old($CMS->input) )}
        {$this->total($data)}
        {$this->footer($data)}
EOF;

        return $out;
    }

    public function form4($data = [])
    {
        global $CMS, $DB, $member;

        $out = <<<EOF
 <script>
    var module_name = "module_transaction";
</script>


        <section class="add_form main_form">
                
                {$this->header($data)}
                
                <figure class="box-typical box-typical box-typical-padding border">
                    {$this->store_options($data['store_id'])}
                    <div class="row">
                        <div class="col-xl-10 col-lg-10 col-md-9 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-3 col-lg-5 col-md-7 col-sm-7 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-7 col-sm-7 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_email']} <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_email" id="trx_email" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_cus_email_err']}" data-validation-regex="/^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['trx_cus_email_err']}" value="{$data['trx_email']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-4 col-lg-3 col-md-5 col-sm-5 col-xs-12">
                                    <label class="form-label hidden-sm-down">&nbsp;</label>
                                    <div class="checkbox">
                                        <input type="checkbox" id="trx_send_email" name="trx_send_email">
                                        <label for="trx_send_email"><em style="font-size: 12px">{$CMS->lang['trx_send_email']}</em></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_address']} <em>({$CMS->lang['remain']} <span class="address-limiter"></span> {$CMS->lang['character']})</em> <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_address" id="trx_address" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_address_err']}" value="{$data['trx_address']}">
                                        <script>
                                            $("#trx_address").limiter(255, $(".address-limiter"));
                                        </script>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_estimate_date']}</label>
                                        <input class="form-control " type="text" name="trx_estimate_date" id="trx_estimate_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_estimate_date_err']}"  value="{$data['trx_estimate_date']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_expiration_date']}</label>
                                        <p class="form-control-static-input">
                                            <input name="trx_expiration_date" id="trx_expiration_date" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_expiration_date_err']}"  value="{$data['trx_expiration_date']}">
                                        </p>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_contract_code']}</label>
                                        <input class="form-control " type="text" name="trx_contract_code" id="trx_contract_code" value="{$data['trx_contract_code']}">
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 col-xs-12 text-sm-left text-md-right">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price"><span class="total-show">0</span></p>
                            <fieldset class="form-group">
                                <div class="dropdown">
                                    
                                    <span  class="label label-default dropdown-toggle"  id="estimate_status_text" data-toggle="dropdown" ria-haspopup="true" aria-expanded="false" style=" cursor: pointer">Đang chờ</span>
                                
                                    <div class="dropdown-menu" aria-labelledby="searchInvoiceDropDown" style="left: 0 !important; position: relative !important; top: 0px !important">
                                        <figure class="box-typical box-typical-padding border" style="padding: 10px !important;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <fieldset class="form-group">
                                                        <label class="form-label">{$CMS->lang['trx_status']}</label>
                                                        <select class="form-control select2" name="trx_status" id="trx_estimate_status" onchange="selectEstimateStatus();">
                                                            {$data['option_trx_estimate_status']}
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-12 est_status_display">
                                                    <fieldset class="form-group">
                                                        <label class="form-label">{$CMS->lang['trx_accepted_by']}</label>
                                                        <input type="text" class="form-control" name="trx_accepted_by" id="trx_accepted_by" value="{$data['trx_accepted_by']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_accepted_by_err']}">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-12 est_status_display">
                                                    <fieldset class="form-group">
                                                        <label class="form-label">{$CMS->lang['trx_accepted_date']}</label>
                                                        <input type="text" class="form-control" name="trx_accepted_date" id="trx_accepted_date" value="{$data['trx_accepted_date']}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_accepted_date_err']}">
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </figure>                                       
                                    </div> 
                                </div>
                            </fieldset>
                            <script>
                                $('.dropdown-menu').find('figure').click(function (e) {
                                    e.stopPropagation();
                                });
                                $("#trx_estimate_status").trigger("change");
                            </script>
                        </div>
                    </div>
                </figure>
        </section>
        
    {$CMS->global->htmlTableProduct( $CMS->input['act'] == 'edit' ? $CMS->product->convert_trx_item_old($data['item']['product']) : $CMS->product->convert_input_old($CMS->input),1 )}
    {$CMS->global->htmlTableAsset( $CMS->input['act'] == 'edit' ? $CMS->assets->convert_trx_item_old($data['item']['asset']) :  $CMS->assets->convert_input_old($CMS->input) )}
    {$this->total($data)}
    {$this->footer($data)}
EOF;
        return $out;
    }

    /**
     * Memo credit form
     * @param array $data
     * @return string
     */
    public function form8($data = [])
    {
        global $CMS, $DB, $member;

        $out = <<<EOF
 <script>
    var module_name = "module_transaction";
</script>


        <section class="add_form main_form">
                
                {$this->header($data)}
                
                <figure class="box-typical box-typical box-typical-padding border">
                    <div class="row">
                        {$this->store_options($data['store_id'])}
                        <div class="col-xl-10 col-lg-10 col-md-9 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-3 col-lg-5 col-md-7 col-sm-7 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-7 col-sm-7 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_email']} <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_email" id="trx_email" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_cus_email_err']}" data-validation-regex="/^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['trx_cus_email_err']}" value="{$data['trx_email']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-4 col-lg-3 col-md-5 col-sm-5 col-xs-12">
                                    <label class="form-label hidden-sm-down">&nbsp;</label>
                                    <div class="checkbox">
                                        <input type="checkbox" id="trx_send_email" name="trx_send_email">
                                        <label for="trx_send_email"><em style="font-size: 12px">{$CMS->lang['trx_send_email']}</em></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_address']} <em>({$CMS->lang['remain']} <span class="address-limiter"></span> {$CMS->lang['character']})</em> <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_address" id="trx_address" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_address_err']}" value="{$data['trx_address']}">
                                        <script>
                                            $("#trx_address").limiter(255, $(".address-limiter"));
                                        </script>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_credit_memo_date']}</label>
                                        <input class="form-control " type="text" name="trx_credit_memo_date" id="trx_credit_memo_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_credit_memo_date_err']}"  value="{$data['trx_credit_memo_date']}">
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 col-xs-12 text-sm-left text-md-right">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price"><span class="total-show">0</span></p>
                        </div>
                    </div>
                </figure>
        </section>
        
    {$CMS->global->htmlTableProduct( $CMS->input['act'] == 'edit' ? $CMS->product->convert_trx_item_old($data['item']['product']) : $CMS->product->convert_input_old($CMS->input),1 )}
    {$CMS->global->htmlTableAsset( $CMS->input['act'] == 'edit' ? $CMS->assets->convert_trx_item_old($data['item']['asset']) :  $CMS->assets->convert_input_old($CMS->input) )}
    {$this->total($data)}
    {$this->footer($data)}
EOF;
        return $out;
    }

    public function form5($data = [])
    {
        global $CMS, $DB, $member;

        $out = <<<EOF
  
  <script>
    var module_name = "module_transaction";
</script>

        <section class="add_form main_form">

                {$this->header($data)}
                
                <figure class="box-typical box-typical box-typical-padding border">
                    {$this->store_options($data['store_id'])}
                    <div class="row">
                        <div class="col-xl-10 col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-4 col-lg-5 col-md-7 col-sm-7 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-9 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_address']} <em>({$CMS->lang['remain']} <span class="address-limiter"></span> {$CMS->lang['character']})</em> <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_address" id="trx_address" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_address_err']}" value="{$data['trx_address']}">
                                        <script>
                                            $("#trx_address").limiter(255, $(".address-limiter"));
                                        </script>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: -28px">
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_payment_date']}</label>
                                        <input class="form-control " type="text" name="trx_payment_date" id="trx_payment_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_payment_date_err']}" value="{$data['trx_payment_date']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <div style="clear: both; overflow: hidden;">
                                            <label class="form-label pull-left">{$CMS->lang['trx_terms']}</label>
                                            <div class="box_action pull-right">
                                                    {$data['btn_add_term']}
                                                    <span class="box_edit_term pull-right" style="margin-left: 10px;">{$data['btn_edit_term']}</span>
                                            </div>
                                        </div>
                                        <div class="box_container">
                                            <div class="input-group">
                                                <input class="form-control " type="hidden" name="trx_terms" id="trx_terms" value="{$data['trx_terms']}">
                                                <input class="form-control " type="text" name="trx_term_name" id="trx_term_name" value="{$data['trx_term_name']}">
                                                <div class="input-group-addon" style="cursor:pointer" onclick="autocompleteAll('#trx_term_name')">
                                                    <span class="fa fa-arrow-down"></span>
                                                </div>
                                                <script>
                                                $(document).ready(() =>  autocompleteSearch('#trx_term_name', site_root_domain + '/?site=transaction_terms&subact=dataAjax', '#trx_terms_ajax','term',0))
                                                </script>          
                                            </div>
                                            <div id="trx_terms_ajax" class="form-control" style="position: relative; display: none;"></div>
                                        </div> 
                                    </fieldset>    
                                </div>
                                <div class="hidden-md-up col-xs-12"></div>
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_due']}</label>
                                        <p class="form-control-static-input">
                                            <input name="trx_due_date" id="trx_due_date" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_due_date_err']}"  value="{$data['trx_due_date']}">
                                        </p>
                                    </fieldset>
                                </div>
                                <div class="hidden-sm-down col-sm-12"></div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_contract_code']}</label>
                                        <input class="form-control " type="text" name="trx_contract_code" id="trx_contract_code" value="{$data['trx_contract_code']}">
                                    </fieldset>
                                </div>
                                <div class="hidden-md-up col-sm-12"></div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['at_id']}</label>
                                        <select name="at_id" id="at_id" class="form-control select2">
                                            <option>--- {$CMS->lang['at_id']} ---</option>
                                            {$CMS->transactions->loadAccountTypeOption($data['at_id'])}
                                        </select>
                                    </fieldset>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-12 col-xs-12 text-sm-left text-md-right" style="margin-top: 10px">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price"><span class="total-show">0</span> </p>
                            {$data['status_display']}
                        </div>
                    </div>
                </figure>
        </section>
        {$CMS->global->htmlTableProduct( $CMS->input['act'] == 'edit' ? $CMS->product->convert_trx_item_old($data['item']['product']) : $CMS->product->convert_input_old($CMS->input),1 )}
    {$CMS->global->htmlTableAsset( $CMS->input['act'] == 'edit' ? $CMS->assets->convert_trx_item_old($data['item']['asset']) :  $CMS->assets->convert_input_old($CMS->input) )}
        {$this->note_form($data)}
        {$this->footer($data)}
EOF;

        return $out;
    }

    public function form6($data = [])
    {
        global $CMS, $DB, $member;

        $out = <<<EOF
 <script>
    var module_name = "module_transaction";
</script>

        <section class="add_form main_form">
                
                {$this->header($data)}
                
                <figure class="box-typical box-typical box-typical-padding border">
                    {$this->store_options($data['store_id'])}
                    <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-4 col-lg-5 col-md-4 col-sm-6 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_account']}</label>
                                        <select class="form-control hide_show_account select2" name="trx_account" id="trx_account" onchange="chooseAccount($(this), $('.account_balance'))">
                                            {$data['option_trx_account']}
                                        </select>
                                        <script>
                                            $(document).ready(() => {
                                                $("[name=trx_account]").trigger("change");
                                            })
                                        </script>
                                    </fieldset>
                                </div>
                                
                                <div class="col-xs-6 hidden-md-up  hidden-xs-down"> 
                                    <label>&nbsp;</label>
                                </div>
                                
                                <div class="col-xl-4 col-lg-3 col-md-4 col-sm-4 col-xs-12">  
                                    <fieldset class="form-group">
                                        <label class="hidden-sm-down">&nbsp;</label>
                                        <div><strong>{$CMS->lang['balance']}: </strong><small  class="account_balance">0<sub>đ</sub></small></div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_payment_date2']}</label>
                                        <input class="form-control " type="text" name="trx_payment_date" id="trx_payment_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_payment_date_err2']}"  value="{$data['trx_payment_date']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_method']}</label>
                                        <select class="form-control select2" name="trx_method" id="trx_method" data-validation="[NOTEMPTY]" data-validation-message="" >
                                            {$data['option_trx_method']}
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_reference_no']}</label>
                                        <input class="form-control hide_show_account" type="text" name="trx_reference_no" id="trx_reference_no" value="{$data['trx_reference_no']}">
                                    </fieldset>
                                </div>
                                
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['at_id']}</label>
                                        <select name="at_id" id="at_id" class="form-control select2">
                                            <option>--- {$CMS->lang['at_id']} ---</option>
                                            {$CMS->transactions->loadAccountTypeOption($data['at_id'])}
                                        </select>
                                    </fieldset>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-xs-12 text-xl-right  text-lg-right  text-md-left text-sm-left text-xs-left">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price" id="total_price"><span class="total-show">0</span></p>
                            {$data['status_display']}
                        </div> 
                    </div>
                </figure>
        </section>
        {$CMS->global->htmlTableProduct( $CMS->input['act'] == 'edit' ? $CMS->product->convert_trx_item_old($data['item']['product']) : $CMS->product->convert_input_old($CMS->input),1 )}
    {$CMS->global->htmlTableAsset( $CMS->input['act'] == 'edit' ? $CMS->assets->convert_trx_item_old($data['item']['asset']) :  $CMS->assets->convert_input_old($CMS->input) )}
        {$this->note_form($data)}
        {$this->footer($data)}
EOF;

        return $out;
    }

    public function form7($data = [])
    {
        global $CMS, $DB, $member;

        $out = <<<EOF
<script>
    var module_name = "module_transaction";
</script>

        <section class="add_form main_form">
                
                {$this->header($data)}
                
                <figure class="box-typical box-typical box-typical-padding border">
                    {$this->store_options($data['store_id'])}
                    <div class="row">
                        <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            {$this->choose_cus_type($data)}
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                         {$this->customer_form($data)}
                                    </fieldset>
                                </div>
                                <div class="row hidden-lg-up"></div>
                                <div class="col-xl-3 col-lg-3 col-md-5 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_email']} <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="trx_email" id="trx_email" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_cus_email_err']}" data-validation-regex="/^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['trx_cus_email_err']}" value="{$data['trx_email']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-4 col-lg-5 col-md-7 col-sm-8 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="hidden-xs-down">&nbsp;</label>
                                        <div class="dropdown" style="position: relative !important;">
                                            <button type="button" class="btn dropdown-toggle btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  id="searchInvoiceDropDown">{$CMS->lang['trx_by_invoice']}</button>
                                        
                                            <div class="dropdown-menu" aria-labelledby="searchInvoiceDropDown" style="left: 0 !important;">
                                                <figure class="box-typical box-typical-padding border">
                                                    <div class="form-group">
                                                        <figure class="heading">{$CMS->lang['trx_invoice_id']}</figure>
                                                        <input type="text" class="form-control" id="search_invoice_id">
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-danger" data-toggle="dropdown">{$CMS->lang['comment_reset']}</button>
                                                        <button class="btn btn-primary" onclick="searchInvoice('{$CMS->input['type']}', $('[name=cus_type]:checked').val())">{$CMS->lang['search_form']}</button>
                                                    </div>
                                                </figure>                                       
                                            </div> 
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 hidden-md-down">
                                    &nbsp;
                                </div>
                                <div class="col-xl-4 col-lg-3 col-md-5 col-sm-12 col-xs-12">  
                                    <div class="checkbox">
                                        <input type="checkbox" id="trx_send_email" name="trx_send_email">
                                        <label for="trx_send_email"><em style="font-size: 12px">{$CMS->lang['trx_send_email']}</em></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_payment_date2']}</label>
                                        <input class="form-control " type="text" name="trx_payment_date" id="trx_payment_date" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_payment_date_err2']}"  value="{$data['trx_payment_date']}">
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_account']}</label>
                                        <select class="form-control hide_show_account select2" name="trx_account" id="trx_account" >
                                            {$data['option_trx_account']}
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <fieldset class="form-group">
                                        <label class="form-label">{$CMS->lang['trx_reference_no']}</label>
                                        <input class="form-control hide_show_account" type="text" name="trx_reference_no" id="trx_reference_no" value="{$data['trx_reference_no']}">
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-xs-12 text-xl-right text-lg-left text-md-left text-sm-left text-xs-left">
                            <h4>{$CMS->lang['trx_sum']}</h4>
                            <p class="total_price" id="total_price"><span class="total_tax_show">0</span></p>
                            {$data['status_display']}
                        </div>
                    </div>
            </figure>
    </section>
        <section class="add_table">
        
EOF;

        $trx_invoice_info = @json_decode($data['trx_invoice_info'], true);

        if($_SESSION['is_mobile'] == true)
        {
            $out .=<<<EOF
				<div id="getUnbilledInvoices" class="table-responsive" style="overflow-y: visible !important;">
EOF;
        }
        else
        {
            $out .=<<<EOF
                <div id="getUnbilledInvoices" class="table-responsive" style="overflow-x: visible !important;
                          overflow-y: visible !important;">
EOF;
        }

        $out .=<<<EOF
                    <thead>
EOF;

        if($_SESSION['is_mobile'] == true)
        {
            $out .=<<<EOF
				<table id="item_line" class="table_cus" width="100%">
EOF;
        }
        else
        {
            $out .=<<<EOF
                <table id="item_line" class="responsive-table table_cus">
EOF;
        }

        $out .=<<<EOF
                    <thead>
EOF;


        if(!$trx_invoice_info)
        {
            if($CMS->input['invoice'])
            {
                $data_info = $CMS->transactions->getInfo($CMS->input['invoice']);

                if($data_info)
                {
                    if($CMS->input['sub'] == 2)
                    {
                        $subtype = 1;
                    }
                    else if($CMS->input['sub'] == 7)
                    {
                        $subtype = 5;
                    }
                    else
                    {
                        $subtype = 0;
                    }

                    if($data_info['cus_type'] == 1)
                    {
                        $trx_invoice_info = $CMS->transactions->getUnbilledByCustomer($data_info['cus_id'], 0, $subtype, $data_info['cus_type']);
                    }
                    else if($data_info['cus_type'] == 2)
                    {
                        $trx_invoice_info = $CMS->transactions->getUnbilledByCustomer($data_info['supplier_id'], 0, $subtype, $data_info['cus_type']);
                    }
                    else if($data_info['cus_type'] == 3)
                    {
                        $trx_invoice_info = $CMS->transactions->getUnbilledByCustomer($data_info['user_assign'], 0, $subtype, $data_info['cus_type']);
                    }


                    $load_from_db = 1;
                }
            }
        }

        $out .= <<<EOF
                        <tr>
                            <th scope="col" width="2%"></th>
                            <th scope="col">{$CMS->lang['table_inv_code']}</th>
                            <th scope="col">{$CMS->lang['trx_due_2']}</th>
                            <th scope="col">{$CMS->lang['trx_original_2']}</th>
                            <th scope="col">{$CMS->lang['trx_open_2']}</th>
                            <th scope="col">{$CMS->lang['trx_payment_2']}</th>
                        </tr>
                    </thead>
                    <tbody>
EOF;

        if(!$trx_invoice_info)
        {
            $out .= <<<EOF
            <script>
                $(document).ready(() => {
                    $("#getUnbilledInvoices").hide();
                })
            </script>
EOF;

        }

        if($load_from_db)
        {
            foreach($trx_invoice_info as $invoice_info)
            {
                $invoice_info['trx_expiration_date'] = $CMS->class->date->date_format($invoice_info['trx_expiration_date']);

                $invoice_total = $invoice_info['trx_total'] - $invoice_info['trx_receive_payment'];

                $prefix_trx = $invoice_info['trx_type'] == 2 ? 'PAY' : 'INV';

//                $remain_amount = $invoice_info['trx_total'] - $invoice_info['trx_receive_payment'] + $invoice_total;
                $remain_amount = $invoice_total;

                $out .= <<<EOF
                    <tr>
                        <td>
                            <input type='checkbox' id ='invoice_{$invoice_info['trx_id']}' name='item[{$invoice_info['trx_id']}]' onchange='changeCheckInvoiceParent(this, "{$invoice_info['trx_id']}")' class='parent' >
                        </td>
                        <td><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$invoice_info['trx_id']}">{$prefix_trx}{$invoice_info['trx_invoice_no']}</a></td>
                        <td>{$invoice_info['trx_expiration_date']}</td>
                        <td>{$invoice_info['trx_total']}</td>
                        <td>
                            {$remain_amount}
                        </td>
                        <td>
                            <div class="form-group">
                                <div style="position: relative">
                                    <input type='number' class='form-control' name='tri_payment[{$invoice_info['trx_id']}]' value='{$invoice_total}' onchange='changeTotalTax()' data-validation="[V<={$remain_amount}]" data-validation-message="{$CMS->lang['error_max_payment_amount']}" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
                                </div>
                            </div>
                        </td>
                    </tr>
EOF;

            }
        }
        else
        {
            foreach($trx_invoice_info as $invoice_id => $invoice_total)
            {
                $invoice_info = $CMS->transactions->getInfo($invoice_id);
                $invoice_info['trx_expiration_date'] = $CMS->class->date->date_format($invoice_info['trx_expiration_date']);

                $prefix_trx = $invoice_info['trx_type'] == 2 ? 'PAY' : 'INV';

//                $remain_amount = $invoice_info['trx_total'] - $invoice_info['trx_receive_payment'] + $invoice_total;
                $remain_amount = $invoice_info['trx_total'] - $invoice_info['trx_receive_payment'];

                $out .= <<<EOF
                    <tr>
                        <td>
                            <input type='checkbox' checked id ='invoice_{$invoice_info['trx_id']}' name='item[{$invoice_info['trx_id']}]' onchange='changeCheckInvoiceParent(this, "{$invoice_info['trx_id']}")' class='parent' >
                        </td>
                        <td><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$invoice_info['trx_id']}">{$prefix_trx}{$invoice_info['trx_invoice_no']}</a></td>
                        <td>{$invoice_info['trx_expiration_date']}</td>
                        <td>{$invoice_info['trx_total']}</td>
                        <td>
                            {$remain_amount}
                        </td>
                        <td>
                            <div class="form-group">
                                <div style="position: relative">
                                    <input type='number' class='form-control' name='tri_payment[{$invoice_info['trx_id']}]' value='{$invoice_total}' onchange='changeTotalTax()' data-validation="[V<={$remain_amount}]" data-validation-message="{$CMS->lang['error_max_payment_amount']}" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
                                </div>
                            </div>
                        </td>
                    </tr>
EOF;

            }
        }


        $out .= <<<EOF
                    </tbody>
                </table>
                </div>
            </section>
            <script>changeTotalTax();</script>
            {$this->note_form($data)}
            {$this->footer($data)}
EOF;

        if($data['cus_type'] == 1) //KH
        {
            $cus_type_id = $data['data_info']['cus_id'];
        }
        elseif($data['cus_type'] == 2) //NCC
        {
            $cus_type_id = $data['data_info']['supplier_id'];
        }
        elseif($data['cus_type'] == 3) //NV
        {
            $cus_type_id = $data['data_info']['user_assign'];
        }
        else
        {
            $cus_type_id = 0;
        }

        if (isset($CMS->input['invoice'])) {
            $tmp = isset($data['data_info']['trx_invoice_no']) && $data['data_info']['trx_invoice_no'] > 0 ? $data['data_info']['trx_invoice_no'] : $data['data_info']['trx_id'];
            $out .= <<<EOF
                <script>
                    getInvoice("{$cus_type_id}", {$tmp}, "{$CMS->input['type']}", "{$data['cus_type']}");
                </script>
EOF;
        }

        return $out;
    }

    function header($data)
    {
        global $CMS;

        $out = <<<EOF
        <figure class="heading">
            <h3>{$data['add_text']}</h3>
          <a href="{$data['back_link']}" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <div class="alert alert-grey-darker">{$CMS->lang['trx_subtype_desc_'.$CMS->input['sub']]}</div>
EOF;
        return $out;
    }

    function note_form($data = [])
    {
        global $CMS;
        $output = <<<EOF
        <section class="add_form main_form">
            <figure class="box-typical box-typical box-typical-padding border">
                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <fieldset class="form-group">
                                    <label class="form-label">{$CMS->lang['trx_msg']} <em>({$CMS->lang['remain']} <span class="msg-limiter"></span> {$CMS->lang['character']})</em></label>
                                    <textarea class="form-control" rows="5" id="trx_msg" name="trx_msg">{$data['trx_msg']}</textarea>
                                </fieldset>
                                <script>
                                    $("#trx_msg").limiter(255, $(".msg-limiter"));
                                </script>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <fieldset class="form-group">
                                    <label class="form-label">{$CMS->lang['trx_note']} <em>({$CMS->lang['remain']} <span class="note-limiter"></span> {$CMS->lang['character']})</em></label>
                                    <textarea class="form-control" rows="5" id="trx_note" name="trx_note">{$data['trx_note']}</textarea>
                                </fieldset>
                                <script>
                                    $("#trx_note").limiter(255, $(".note-limiter"));
                                </script>
                            </div>
                        </div>
                    </div>
EOF;
        if($CMS->input['sub'] == 5 || $CMS->input['sub'] == 6)
        {
            $output .= <<<EOF
                    <div class="col-xl-8 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="row total_price">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">{$CMS->lang['table_total']}:</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><span class="total-show">0</span></div>
                      </div>
                    </div>
EOF;
        }

        if($CMS->input['sub'] == 2 || $CMS->input['sub'] == 7)
        {
            $output .= <<<EOF
                    <div class="col-xl-8 col-lg-6 col-md-6 col-sm-12 col-xs-12">
EOF;

            if($CMS->input['sub'] == 2)
            {
                $output .= <<<EOF
                <div class="row">
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">{$CMS->lang['trx_amount_received']}:</div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><input name="trx_receive_payment" type='text' class='form-control amount_received_show' value='{$data['trx_receive_payment']}' onchange="changeAmountReceive($(this).val())"></div>
                  </div>
EOF;

            }

            $output .= <<<EOF
                      <div class="row" style="margin-top: 10px">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">{$CMS->lang['trx_sum']}:</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><input id="total_tax" type='text' class='form-control total_tax_show' value='0' disabled></div>
                      </div>
EOF;

            $output .= <<<EOF
                    </div>
EOF;
        }


        $output.= <<<EOF
                </div>
                {$this->attachFilesForm($data)}
            </figure>
        </section>
EOF;
        return $output;
    }

    function choose_cus_type($data)
    {
        global $CMS;
        
        $data = array_merge($data, $CMS->input);

        $cus_type = $data['cus_type'] ? $data['cus_type'] : 1;

        $checked[$cus_type] = "checked";

        $output = <<<EOF
        <div class="row">
            <div class="col-md-12">
                <fieldset class="form-group">
                    <div class="radio w25">
                      <input class="form-control" id="choose_cus_type_1" type="radio" name="cus_type" value="1" {$checked[1]} onchange="chooseCusType($(this), '1')"><label for="choose_cus_type_1">{$CMS->lang['cus_type_1']}</label>
                    </div>
                    <div class="radio w25">
                      <input class="form-control" id="choose_cus_type_2" type="radio" name="cus_type" value="2" {$checked[2]} onchange="chooseCusType($(this), '1')"><label for="choose_cus_type_2">{$CMS->lang['cus_type_2']}</label>
                    </div>
                    <div class="radio w25">
                      <input class="form-control" id="choose_cus_type_3" type="radio" name="cus_type" value="3" {$checked[3]} onchange="chooseCusType($(this), '1')"><label for="choose_cus_type_3">{$CMS->lang['cus_type_3']}</label>
                    </div>
                </fieldset>
            </div>
        </div>

    <script>
        $(document).ready(() => {
            chooseCusType($("#choose_cus_type_{$cus_type}"));
        })
    </script>
EOF;

        return $output;
    }

    function  customer_form($data=[])
    {
        global $CMS, $member;

        $data = array_merge($data, $CMS->input);

        if($CMS->permit['supplier_add'] == 1)
        {
            $btn_add_ncc =<<<EOF
                <a class="btn_gen add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;

        }else
        {
            $btn_add_ncc = "";
        }

        if($CMS->permit['supplier_edit'] == 1 && ($CMS->input['supplier_id'] || $data['supplier']['supplier_id']))
        {
            $CMS->input['supplier_id'] = $CMS->input['supplier_id'] ? $CMS->input['supplier_id'] : $data['supplier']['supplier_id'];
            $btn_edit_ncc =<<<EOF
                <a id="{$CMS->input['supplier_id']}" class="btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
EOF;

        }else
        {
            $btn_edit_ncc = "";
        }

        $option_supplier = $CMS->supplier->get_list_supplier(0, $data['supplier_id']);

        $output = <<<EOF
<div id="custype_1">
    <div style="clear: both; overflow: hidden;">
        <label class="form-label pull-left">{$CMS->lang['trx_cus']}</label>
        <div class="box_action pull-right">
            {$data['btn_add_cus']}
            <span class="box_edit_cus pull-right" style="margin-left: 10px;">{$data['btn_edit_cus']}</span>
        <script>
            $(document).ready(function(){
                add_new_customer('#transactionForm');
                add_do_new_customer('#box_customer', '#transactionForm');
                edit_do_customer('#box_customer', '#transactionForm');
            });
         </script>
        </div>
     </div>
    
    <div class="box_container">
        <div class="input-group">
            <input class="form-control " type="text" name="trx_cus" id="trx_cus" sub="{$CMS->input['sub']}"  value="{$data['trx_cus']}" autocomplete="false">
            <div class="input-group-addon"  style="cursor:pointer" onclick="autocompleteAll('#trx_cus')">
                <span class="fa fa-arrow-down"></span>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(() =>  autocompleteSearch('#trx_cus', site_root_domain + '/?site=customer&subact=quicksearch', '#trx_cus_ajax','trx',0))
    </script>
    <input name="cus_id" id="cus_id" style="display: none" value="{$data['cus_id']}">
    <div id="trx_cus_ajax" class="form-control" style="position: relative;"></div>
</div>


<div id="custype_2">
    <ul class="list_field_product">
        <li>
            <fieldset class="form-group">
                <label class="form-label pull-left" for="sup_id">{$CMS->lang['title_product_supplier']}</label>
                <div class="box_action pull-right">
                    {$btn_add_ncc}
                    <span class="box_edit_supplier pull-right" style="margin-left: 10px;">{$btn_edit_ncc}</span>
                </div>
                <div class="typeahead-field"> 
                    <span class="typeahead-query">
                        <select id="supplier_id" name="supplier_id" class="form-control select_supplier select2" for="change"  sub="{$CMS->input['sub']}">
                            {$option_supplier}
                        </select>
                    </span>
                </div>
            </fieldset>
        </li>
    </ul>
</div>

<div id="custype_3">
        <label class="form-label pull-left" for="user_id_assign">Nhân viên</label>
        <div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_assign', 'user_assign', 'trx_email', 'trx_address')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}' val_email='{$member['user_email']}'  val_address='{$member['user_address']}' sub="{$CMS->input['sub']}">Gán cho tôi</a></div>
        <div class="input-group"  style="clear: both;">
            <input name="user_name_assign" value="{$data['user_name_assign']}" class="form-control search_user" autocomplete="off" sub="{$CMS->input['sub']}">
            <div class="input-group-addon">
              <span style="cursor:pointer" onclick="autocompleteAll('.search_user')" class="fa fa-arrow-down"></span>
          </div>
        </div>
        <input name='user_assign' type='hidden' value="{$data['user_assign']}" class="user_assign" id="user_assign"/>
        <script>
            $(document).ready(() => {
                autocompleteSearch('.search_user', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_assign','trx2',0);
            });
        </script>
</div>
    
EOF;

        return $output;
    }

    function footer($data = [])
    {
        global $CMS;

        $title_button1 = $CMS->input['act'] == 'edit' ? $CMS->lang['update_and_saveoption'] : $CMS->lang['save_and_saveoption'];

        $title_button2 = $CMS->lang['save_option'];

        $title_button3 = $CMS->input['act'] == 'edit' ? $CMS->lang['trx_edit_'.$CMS->input['sub']] : $CMS->lang['trx_add_'.$CMS->input['sub']];

        $out = <<<EOF
                <section class="add_cart_footer">
                    <input type="hidden" value="0" name="add_product_option"  />
                    <button type="submit" id="trigger_submit" style="display:none"></button>
                    <a href="{$data['back_link']}" class="pull-left cancel">{$CMS->lang['back_to_list']}</a>
EOF;

        if($CMS->input['id'])
        {
            $out .= <<<EOF
                    <a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$CMS->input['id']}&type={$CMS->input['type']}&sub={$CMS->input['sub']}" class="pull-left cancel">{$CMS->lang['back_to_detail']}</a>
EOF;
        }


        $out .= <<<EOF
                    <button id="form_submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart hidden-xs-down" data-style="expand-right" data-size="xs"><span class="ladda-label">{$title_button3}</span><span class="ladda-spinner"></span></button>
                    <button id="form_submit_option" class="btn btn-inline btn-primary ladda-button pull-right add_cart hidden-xs-down" data-style="expand-right" data-size="xs"><span class="ladda-label">{$title_button1}</span><span class="ladda-spinner"></span></button>
                        <div class="btn-group dropup pull-right hidden-sm-up">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-save"></i>{$title_button2}
                            </button>
                            <div class="dropdown-menu">
                                <ul>
                                    <li><a id="form_submit_mobile"  title=""><i class="fa fa-plus"></i>{$title_button1}</a></li>
                                    <li><a id="form_submit_option_mobile" title=""><i class="fa fa-save"></i>{$title_button3}</a></li>
                                </ul>
                            </div>
                        </div>
                    </section>
                    <input type="hidden" name="transactionFormStateChange" id="transactionFormStateChange" value="0">
<script>

     //Check state change of input in form
     $("#transactionForm").find("input, textarea, select").change(function(){
         $("#transactionFormStateChange").val("1");
     });   

     $("#form_submit, #form_submit_mobile , #form_submit_option, #form_submit_option_mobile").click(function(){
//        $("#transactionForm input[name='add_product_option']").attr('value','1');
        confirmWhenEditTrx('{$CMS->input['id']}' , $("#transactionFormStateChange").val(),function(){
           $("#trigger_submit").trigger("click"); 
        });
     });
</script>
EOF;
        return $out;
    }

    function attachFilesForm($data = [])
    {
        global $CMS;

        $icons = $CMS->transactions->file_icons;

        $acceptedAttachFiles = @json_encode($CMS->transactions->accepted_files);
        $attachFileIcons = @json_encode($CMS->transactions->file_icons);

        $output = <<<EOF
        <script>
            var attachFrmTrx = 1;
            var acceptedAttachFiles = '{$acceptedAttachFiles}';
            var maxSizeAttachFiles = '{$CMS->transactions->max_size}';
            var attachFileIcons = '{$attachFileIcons}';
        </script>
                    <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['attach_files']} : {$CMS->lang['max_size']} {$CMS->transactions->max_size}MB</label>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-xs-12">
                          <div class="box-typical-upload box-typical-upload-in">
                                <div class="drop-zone fileinput-button" style="height: inherit">
                                    <i class="font-icon font-icon-cloud-upload-2"></i>
                                    <div class="drop-zone-caption">Drag file to upload</div>
                                    <input type="file" multiple name="upload_file[]" id="attach_files" class="multiple_upload">
                                </div><!--.drop-zone-->
                            <p class="box_error" style="display: none;"></p>
                            <ul class="uploading-list list_upload">
EOF;

        $files = $CMS->attach->get_array("transaction", $data['trx_id']);

        foreach ($files as $file)
        {
            $file['attach_ext'] = strtolower($file['attach_ext']);
            $icon = isset($icons[$file['attach_ext']]) ? $icons[$file['attach_ext']] : $icons['default'];

            $output .= <<<EOF
            <li class="uploading-list-item" id="attachFiles_{$file['attach_id']}">
                <div class="uploading-list-item-wrapper">
                    <div class="uploading-list-item-name">
                        <i class="{$icon}"></i>
                        {$file['attach_name']}
                    </div>
                    <div class="uploading-list-item-size">{$CMS->class->input->formatSizeUnits($file['attach_size'])}</div>
                    <button type="button" class="uploading-list-item-close" onclick="deleteAttach('{$file['attach_id']}')">
                        <i class="font-icon-close-2"></i>
                    </button>
                </div>
                <progress class="progress" value="100" max="100">
                    <div class="progress">
                        <span class="progress-bar" style="width: 100%;">100%</span>
                    </div>
                </progress>
                <div class="uploading-list-item-progress">100% done</div>
                <div class="uploading-list-item-speed"></div>
            </li>
EOF;

        }

        $output .= <<<EOF
                            </ul>
                        </div>
                     </div>
EOF;
        return $output;
    }

    function total($data = [])
    {
        global $CMS;

        $trx_discount_type = isset($CMS->input['trx_discount_type']) ? $CMS->input['trx_discount_type'] : $data['trx_discount_type'];
        $trx_discount_value = isset($CMS->input['trx_discount_value']) ? $CMS->input['trx_discount_value'] : $data['trx_discount_value'];

        $selected[$trx_discount_type] = "selected";

        $max_length = $trx_discount_type == 0 ? 3 : 9;

        $output = <<<EOF
        <section class="add_form main_form">
            <figure class="box-typical box-typical box-typical-padding border">
                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <fieldset class="form-group">
                                    <label class="form-label">{$CMS->lang['trx_msg']} <em>({$CMS->lang['remain']} <span class="msg-limiter"></span> {$CMS->lang['character']})</em></label>
                                    <textarea class="form-control" rows="5" id="trx_msg" name="trx_msg">{$data['trx_msg']}</textarea>
                                </fieldset>
                                <script>
                                    $("#trx_msg").limiter(255, $(".msg-limiter"));
                                </script>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <fieldset class="form-group">
                                    <label class="form-label">{$CMS->lang['trx_note']} <em>({$CMS->lang['remain']} <span class="note-limiter"></span> {$CMS->lang['character']})</em></label>
                                    <textarea class="form-control" rows="5" id="trx_note" name="trx_note">{$data['trx_note']}</textarea>
                                </fieldset>
                                <script>
                                    $("#trx_note").limiter(255, $(".note-limiter"));
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="row">
                             <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">{$CMS->lang['gsubtotal']}:</div>
                             <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><span class="subtotal-show"></span></div>
                       </div>
                       <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">{$CMS->lang['gdiscount']}:</div>
                        <!--<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3 "><span class="discount-show">0</span></div>-->
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="input-group">
                                <input name="total_discount_value" id="total-discount-show" onchange="calculate_money()" type="number" class="form-control total-discount-show" value="{$data['trx_total_discount']}">
                                <div class="input-group-btn" id="ord_discount_dropdown">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%<span class="caret"></span></button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" value="0">%</a>
                                        <a class="dropdown-item" value="1">{$CMS->vars['currency_type']}</a>
                                    </div>
                                    <input onchange="calculate_money();" type="hidden" name="total_discount_type" value="1" id="total_discount_type">
                                    <script>
                                        dropdownInput($("#ord_discount_dropdown"), $("#total_discount_type"));
                                    </script>
                                </div><!-- /btn-group -->
                            </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">{$CMS->lang['gtax']}:</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><span class="tax-show">0</span></div>
                      </div>
                      <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">{$CMS->lang['gtotal']}:</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><span class="total-show">0</span></div>
                      </div>
                    </div>
                </div>
                {$this->attachFilesForm($data)}
            </figure>
        </section>
        <script>
            $(document).ready(() => {     
                
                
                $("#transactionForm").on("change", "[name='trx_discount_type']", (e) => {
                    
                    $("[name='trx_discount_value']").val("").trigger("change");
                    
                    if($(e.currentTarget).val() == "1")
                    {
                        $("[name='trx_discount_value']").prop("maxlength", 9);
                    }
                    else 
                    {
                        $("[name='trx_discount_value']").prop("maxlength", "3");
                    }
                });
                
                $("#transactionForm").on("change keyup keydown", "[name='product_quantity[]'],[name='ass_quantity[]'], [name='product_price[]'], [name='ass_price[]'], [name='product_tax[]'], [name='ass_tax[]'], [name='product_cycle[]'], [name='trx_discount_value'], [name='trx_discount_type']", calculate_total_transaction);
                
                calculate_total_transaction();
            })
           
        </script>
EOF;

        return $output;
    }

    function show_1($data=[])
    {
        global $CMS;
 
    

        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>
EOF;

        if($data['ord_link'] != "")
        {
            $output .= <<<EOF
        <fieldset class="row">
            <div class="form-control-label2">
                <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['title_order']}</div>
                <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['ord_link']}</div>
            </div>
        </fieldset>
EOF;

        }

            $output .= <<<EOF
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_no']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_invoice_no_c']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_due']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_due_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_contract_code']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_contract_code']}</div>
			        </div>
				</fieldset>
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
                {$this->show_person($data)}		
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_email']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['cus_email']}
                        </div>
			        </div>
				</fieldset>	
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_address']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['trx_billing_address']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
					    <div class="form-control-label2 row">
					        <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['table_subtotal']}</div>
                            <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_amount_c']}</div>
					    </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['discount']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_discount_value_c']}</div>
			            </div>
                       
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['tax']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_tax_c']}</div>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_sum']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_total_c']}</div>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_amount_received']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_receive_payment_c']}</div>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_remain']}</div>
			                <div class="form-control-span2 total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_remain_c']}</div>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>
{$this->attachFilesShow($data)}
{$this->show_receive_info($data['trx_receive_info'])}
{$this->show_items($data)}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_2($data=[])
    {
        global $CMS;
 
        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
			    <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date2']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_method']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_method_0'.$data['trx_payment_method']]}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_reference_no']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_reference_no']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_account']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$data['account_info']['accounts_id']}">{$data['account_info']['accounts_name']}</a></div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['at_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['accounts_type_info']['accounts_type_name']}</div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
                {$this->show_person($data)}				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_email']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['cus_email']}
                        </div>
			        </div>
				</fieldset>	
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_address']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['trx_billing_address']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
                    <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
                            <div class="form-control-label2 row">
                                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_amount_received']}</div>
                                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_receive_payment_c']}</div>
                            </div>
                    </div>
                    <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
                            <div class="form-control-label2 row">
                                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_sum']}</div>
                                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_total_c']}</div>
                            </div>
                    </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['trx_remain']}</div>
                          <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['trx_remain_c']}</p>
                          <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>
{$this->attachFilesShow($data)}
{$this->show_invoices_info($data['trx_invoice_info'])}
{$this->show_credit_memo_info($data['trx_credit_memo_info'])}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_3($data=[])
    {
        global $CMS;

        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date2']}</div>
			            <div class="form-control-span2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_contract_code']}</div>
			            <div class="form-control-span2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_contract_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_method']}</div>
			            <div class="form-control-span2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_method_0'.$data['trx_payment_method']]}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_reference_no']}</div>
			            <div class="form-control-span2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_reference_no']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_account']}</div>
			            <div class="form-control-span2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$data['account_info']['accounts_id']}">{$data['account_info']['accounts_name']}</a></div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['at_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['accounts_type_info']['accounts_type_name']}</div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
EOF;
            if($data['request_id'])
            {
                $data_rq = $CMS->store_request->get_info($data['request_id']);
                $output .=<<<EOF
                <fieldset class="row">
                    <div class="form-control-label2">
                        <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['request_id']}</div>
                        <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
                            <a href="{$CMS->vars['root_domain']}/?site=store_request&act=show&stage=request_eis&id={$data['request_id']}">{$data_rq['request_code']}</a>
                        </div>
                    </div>
                </fieldset> 

EOF;

            }

            if($data['ret_id'])
            {
                $data_rt = $CMS->returns->get_info($data['ret_id']);
                $output .=<<<EOF
                <fieldset class="row">
                    <div class="form-control-label2">
                        <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['ret_id']}</div>
                        <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
                            <a href="{$CMS->vars['root_domain']}/?site=returns&act=show&id={$data['ret_id']}">{$data_rt['ret_code']}</a>
                        </div>
                    </div>
                </fieldset> 

EOF;

            }

$output .=<<<EOF


                {$this->show_person($data)}				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_email']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
			                {$data['cus_email']}
                        </div>
			        </div>
				</fieldset>	
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_address']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
			                {$data['trx_billing_address']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
					    <div class="form-control-label2 row">
					        <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['table_amount']}</div>
                            <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_amount_c']}</div>
					    </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['discount']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_discount_value_c']}</div>
			            </div>
                       
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['tax']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_tax_c']}</div>
			            </div>
			            
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['trx_sum']}</div>
                          <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['trx_total_c']}</p>
                          <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>

{$this->attachFilesShow($data)}
{$this->show_items($data)}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_4($data=[])
    {
        global $CMS;

        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_no']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">#{$data['trx_invoice_no']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_expiration_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_expiration_date_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_estimate_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_estimate_date_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_contract_code']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_contract_code']}</div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
                {$this->show_person($data)}				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_email']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['cus_email']}
                        </div>
			        </div>
				</fieldset>	
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_address']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['trx_billing_address']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_accepted_by']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
			                {$data['trx_accepted_by']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_accepted_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
			                {$data['trx_accepted_date_c']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
					    <div class="form-control-label2 row">
					        <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['table_subtotal']}</div>
                            <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_amount_c']}</div>
					    </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['discount']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_discount_value_c']}</div>
			            </div>
                       
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['tax']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_tax_c']}</div>
			            </div>
			            
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['trx_sum']}</div>
                          <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['trx_total_c']}</p>
                          <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>
{$this->attachFilesShow($data)}
{$this->show_items($data)}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_5($data=[])
    {
        global $CMS;

        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_no']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_invoice_no_c']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_due']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_due_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_contract_code']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_contract_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['at_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['accounts_type_info']['accounts_type_name']}</div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
                {$this->show_person($data)}				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_email']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['cus_email']}
                        </div>
			        </div>
				</fieldset>	
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_address']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['trx_billing_address']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
				    <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_sum']}</div>
                          <p class="form-control-span2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6" style="color: #344154">{$data['trx_total_c']}</p>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_amount_received']}</div>
                          <p class="form-control-span2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6" style="color: #344154">{$data['trx_receive_payment_c']}</p>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['trx_remain']}</div>
                          <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['trx_remain_c']}</p>
                          <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>
{$this->attachFilesShow($data)}
{$this->show_receive_info($data['trx_receive_info'])}
{$this->show_items($data)}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_6($data=[])
    {
        global $CMS;

        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
			    <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date2']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_reference_no']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_reference_no']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_account']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$data['account_info']['accounts_id']}">{$data['account_info']['accounts_name']}</a></div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['at_id']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['accounts_type_info']['accounts_type_name']}</div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
EOF;
            if($data['request_id'])
            {
                $data_rq = $CMS->store_request->get_info($data['request_id']);
                $output .=<<<EOF
                <fieldset class="row">
                    <div class="form-control-label2">
                        <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['request_id']}</div>
                        <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
                            <a href="{$CMS->vars['root_domain']}/?site=store_request&act=show&stage=request_eis&id={$data['request_id']}">{$data_rq['request_code']}</a>
                        </div>
                    </div>
                </fieldset> 

EOF;

            }
            
            if($data['ret_id'])
            {
                $data_rt = $CMS->returns->get_info($data['ret_id']);
                $output .=<<<EOF
                <fieldset class="row">
                    <div class="form-control-label2">
                        <div class="title_label2 col-xl-5 col-lg-5 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['ret_id']}</div>
                        <div class="form-control-span2 col-xl-7 col-lg-7 col-md-6 col-sm-6 col-xs-6">
                            <a href="{$CMS->vars['root_domain']}/?site=returns&act=show&id={$data['ret_id']}">{$data_rt['ret_code']}</a>
                        </div>
                    </div>
                </fieldset> 

EOF;

            }

$output .=<<<EOF

                {$this->show_person($data)}				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['trx_sum']}</div>
                          <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['trx_total_c']}</p>
                          <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>
{$this->attachFilesShow($data)}
{$this->show_items($data)}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_7($data=[])
    {
        global $CMS;

        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
			    <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>

				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date2']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_method']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_method_0'.$data['trx_payment_method']]}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-6  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_reference_no']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$data['trx_reference_no']}</div>
			        </div>
				</fieldset>
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['trx_account']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$data['account_info']['accounts_id']}">{$data['account_info']['accounts_name']}</a></div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2  col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
                {$this->show_person($data)}				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_email']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['cus_email']}
                        </div>
			        </div>
				</fieldset>	
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_address']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['trx_billing_address']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
				    <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_sum']}</div>
                          <p class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6" style="color: #344154">{$data['trx_total_c']}</p>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_receive_amount']}</div>
                          <p class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6" style="color: #344154">{$data['trx_receive_payment_c']}</p>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['trx_remain']}</div>
                          <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['trx_remain_c']}</p>
                          <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>
{$this->attachFilesShow($data)}
{$this->show_invoices_info($data['trx_invoice_info'])}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_8($data=[])
    {
        global $CMS;

        $output = <<<EOF
<section class="add_form main_form">
	
	{$this->show_header($data)}
    
	<div class="box-typical box-typical box-typical-padding border">
		<div class="row">
			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">ID</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_code']}</div>
			        </div>
				</fieldset>

                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['store_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['store_id_c']}</div>
			        </div>
				</fieldset>
EOF;

        if($data['ord_link'] != "")
        {
            $output .= <<<EOF
        <fieldset class="row">
            <div class="form-control-label2">
                <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['title_order']}</div>
                <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['ord_link']}</div>
            </div>
        </fieldset>
EOF;

        }

        $output .= <<<EOF
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_payment_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_payment_date_c']}</div>
			        </div>
				</fieldset>
				
				
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_credit_memo_date']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_credit_memo_date_c']}</div>
			        </div>
				</fieldset>
				
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['user_id']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6"><a href="{$CMS->vars['root_domain']}/acp/?site=user&amp;act=show&amp;id={$data['user_info']['user_id']}">{$data['user_info']['user_name']}</a> <a data-toggle="tooltip" data-placement="bottom" title="" href="{$CMS->vars['root_domain']}/acp/?site=transactions&amp;user_id={$data['user_info']['user_id']}"><i class="fa fa-search q-search" aria-hidden="true"></i></a></div>
			        </div>
				</fieldset>
			</div>
			<div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
                {$this->show_person($data)}		
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_email']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['cus_email']}
                        </div>
			        </div>
				</fieldset>	
                <fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_address']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
			                {$data['trx_billing_address']}
                        </div>
			        </div>
				</fieldset>	
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_msg2']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_msg']}</div>
			        </div>
				</fieldset>
				<fieldset class="row">
					<div class="form-control-label2">
			            <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['trx_note']}</div>
			            <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">{$data['trx_note']}</div>
			        </div>
				</fieldset>
			</div>
			
			<div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
					    <div class="form-control-label2 row">
					        <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['table_subtotal']}</div>
                            <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_amount_c']}</div>
					    </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['discount']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_discount_value_c']}</div>
			            </div>
                       
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['tax']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_tax_c']}</div>
			            </div>
			            
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_sum']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_total_c']}</div>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
			                <div class="title_label2 col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$CMS->lang['trx_amount_received']}</div>
			                <div class="form-control-span2 col-xl-7 col-lg-12 col-md-12 col-sm-12 col-xs-6">{$data['trx_receive_payment_c']}</div>
			            </div>
			        </div>
			        <div class="col-xl-12 col-md-3 col-sm-3 col-xs-12">
			            <div class="form-control-label2 row">
                         <div class="title_label2 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$CMS->lang['amount_to_refund']}</div>
                          <p class="total_price col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color: #344154">{$data['amount_to_refund_c']}</p>
                          <p class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">{$data['trx_status_c']}</p>
			            </div>
			        </div>
			    </div>
			</div>
	 
		</div>	
	</div>
</section>
{$this->attachFilesShow($data)}
{$this->show_receive_info($data['trx_receive_info'])}
{$this->show_items($data)}
{$this->show_footer($data)}
EOF;
        return $output;
    }

    function show_person($data=[])
    {
        global $CMS;

        if($data['cus_type'] == 1)
        {
            $output = <<<EOF
	    <fieldset class="row">
            <div class="form-control-label2">
                <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['cus_type_1']}</div>
                <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
                    <a href="{$CMS->vars['root_domain']}/?site=customer&act=show&id={$data['cus_info']['cus_id']}">{$data['cus_info']['cus_full_name']}</a>
                </div>
            </div>
        </fieldset>	
EOF;
        }
	    else if($data['cus_type'] == 2)
        {
            $output = <<<EOF
	    <fieldset class="row">
            <div class="form-control-label2">
                <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['cus_type_2']}</div>
                <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
                    <a href="{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$data['supplier_info']['supplier_id']}">{$data['supplier_info']['supplier_name']}</a>
                </div>
            </div>
        </fieldset>	
EOF;
        }
        else if($data['cus_type'] == 3)
        {
            $output = <<<EOF
	    <fieldset class="row">
            <div class="form-control-label2">
                <div class="title_label2 col-xl-5 col-lg-5 col-md-5 col-sm-6 col-xs-6">{$CMS->lang['cus_type_3']}</div>
                <div class="form-control-span2 col-xl-7 col-lg-7 col-md-7 col-sm-6 col-xs-6">
                    <a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['assign_info']['user_id']}">{$data['assign_info']['user_display_name']}</a>
                </div>
            </div>
        </fieldset>	
EOF;
        }

	    return $output;
    }


    function show_header($data=[])
    {
        global  $CMS;

        $data['trx_invoice_no_c'] =  $data['trx_invoice_no_c'] != '-' ? $data['trx_invoice_no_c'] : '';

        $output = <<<EOF
        <figure class="heading">
            <h3>{$data['info_text']} {$data['trx_invoice_no_c']}</h3>
              <a href="{$data['back_link']}" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <div class="alert alert-grey-darker">{$CMS->lang['trx_subtype_desc_'.$data['trx_subtype']]}</div>
EOF;
        return $output;
    }

    function show_footer($data=[])
    {
        global $CMS;

        $output = <<<EOF
<script type="text/javascript" src="/acp/jsacp/custom_transaction.js?20180312"></script>
<section class="add_cart_footer">
	<a href="{$data['back_link']}" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['back_to_list']}</span></a>
EOF;

        if(($CMS->permit['transactions_edit'] || $CMS->permit['transactions_root']) && $data['trx_status'] <=2)
        {
            $output .= <<<EOF
						<a href="{$CMS->vars['root_domain']}/?site=transactions&act=edit&id={$data['trx_id']}&type={$data['trx_type']}&sub={$data['trx_subtype']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['act_edit']}</a>		
EOF;
        }
        
        $output .= $CMS->transactions->action_button_html($data);

        $output .= <<<EOF
	<div class="btn-group dropup pull-right hidden-xl-up">
	  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<i class="fa fa-save"></i>{$CMS->lang['gaction']}
	  </button>
	  <div class="dropdown-menu">
	  	<ul>
EOF;

        if($CMS->permit['transactions_edit'] && $data['trx_status'] <=2)
        {
            $output  .= <<<EOF
				<li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=transactions&act=edit&id={$data['trx_id']}&type={$data['trx_type']}&sub={$data['trx_subtype']}"><i class="fa fa-pencil-square-o"></i>{$CMS->lang['act_edit']}</a></li>		
EOF;
        }


        $output .= <<<EOF
           {$CMS->transactions->action_button_html($data, 'dropdown')}
		</ul>
	  </div>
	</div>
		
</section>

{$this->preview_popup()}
{$this->getExportFiles()}
{$CMS->global->sendEmailPopup()}
EOF;

        return $output;
    }

    function show_items($data=[])
    {
        global $CMS, $DB;


        $output = "";

        if($data['item']['product'])
        {
            $output .= <<<EOF
<section class="add_table">

    <h4 class="heading" onclick="$('#show_items').toggle();"><i class="fa fa-caret-down"></i><span>{$CMS->lang['table_product_service']}</span></h4>

    <div id="show_items" class="table-responsive" style="overflow-x: initial;">
        <div id="table_show_wrapper"  >
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table_cus" width="100%">
                        <thead>
                        <tr role="row">
                            <th data-sortable="false" width="5%">#
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_product_service']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_description']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_price']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_quantity']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['tax']}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
EOF;

            $product_total = 0;

            foreach($data['item']['product'] as $k => $product)
            {
                $product_total += $product['tri_total'];

                $cycle_type = $product['tri_cycle_type'] == 0 ? $CMS->lang['cycle_type_0'] : "{$product['tri_cycle']} {$CMS->lang['cycle_type_'.$product['tri_cycle_type']]}";

                $index = $k+1;

                $output .= <<<EOF
                        <tr class="odd">
                            <td>#{$index}</td>
                            <td><a href="{$CMS->vars['root_domain']}/?site=product&act=show&id={$product['product_id']}" target="_blank">{$product['tri_name']}</a></td>
                            <td><span style="max-width: 300px;word-wrap: break-word;">{$product['tri_description']}</span></td>
                            <td>{$CMS->class->input->currency($product['tri_price'])} x {$cycle_type}</td>
                            <td>{$product['tri_quantity']}</td>
                            <td>{$CMS->class->input->number($product['tri_tax'])}</td>
                        </tr>
EOF;
            }


            $output .= <<<EOF
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
EOF;
        }

        if($CMS->vars['addon_goods_enable'] == 0)
        {
            return $output;
        }

        if($data['item']['asset'])
        {
            //asset
            $output .= <<<EOF
<section class="add_table">

    <h4 class="heading"><i class="fa fa-caret-down"></i><span>{$CMS->lang['table_asset']}</span></h4>

    <div class="table-responsive" style="overflow-x: initial;">
        <div id="table_show_wrapper"  >
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table_cus" width="100%">
                        <thead>
                        <tr role="row">
                            <th data-sortable="false" width="5%">#
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_asset']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['ass_code']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_price']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_quantity']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_discount_value']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_tax']}
                            </th>
                            <th data-sortable="false">{$CMS->lang['table_amount']}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
EOF;

            $asset_total = 0;

            foreach($data['item']['asset'] as $k => $asset)
            {
                $asset_total += $asset['tri_total'];

                $index = $k+1;

                $output .= <<<EOF
                        <tr class="odd">
                            <td>#{$index}</td>
                            <td><a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$asset['ass_key']}" target="_blank">{$asset['tri_name']}</a></td>
                            <td>{$asset['tri_description']}</td>
                            <td>{$CMS->class->input->currency($asset['tri_price'])}</td>
                            <td>{$asset['tri_quantity']}</td>
                            <td>{$CMS->class->input->currency($asset['tri_total_discount'])}</td>
                            <td>{$CMS->class->input->number($asset['tri_tax'])}</td>
                            <td>{$CMS->class->input->currency($asset['tri_total'])}</td>
                        </tr>
EOF;
            }


            $output .= <<<EOF
                        <tr>
                            <td width="70%" colspan="6"></td>
                            <td width="15%">{$CMS->lang['table_total']}</td>
                            <td width="15%">{$CMS->class->input->currency($asset_total)}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
EOF;
        }

        return $output;
    }

    function show_invoices_info($data=[])
    {
        global $CMS, $DB;

        $data = $CMS->transactions->convertReceiveInvoiceInfo($data);

        if(!$data) return false;

        $subtype = $CMS->input['sub'] == 7 ? 5 : 1;

        $output = <<<EOF
<section class="add_table">
    <h4 class="heading" onclick="$('#show_invoices_info').toggle();"><i class="fa fa-caret-down"></i><span>{$CMS->lang['related_transactions']}</span></h4>
    <div id="show_invoices_info" class="table-responsive" style="overflow-x: initial;">
        <div>
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table_cus" width="100%">
                        <thead>
                        <tr>
                            <th scope="col" width="10%">{$CMS->lang['trx_subtype_0'.$subtype]}</th>
                            <th scope="col" width="20%">{$CMS->lang['created_at']}</th>
                            <th scope="col" width="20%">{$CMS->lang['updated_at']}</th>
                            <th scope="col" width="25%">{$CMS->lang['created_by']}</th>
                            <th scope="col" width="5%">{$CMS->lang['trx_status']}</th>
                            <th scope="col" width="10%" style="text-align: right">{$CMS->lang['trx_original_2']}</th>
                            <th scope="col"width="10%" style="text-align: right">{$CMS->lang['trx_payment_2']}</th>
                        </tr>
                        </thead>
                        <tbody>
EOF;

        $total = 0;
        $sum_total = 0;

        foreach($data as $invoice_info)
        {
            $invoice_info = $CMS->transactions->convertvalue($invoice_info);
            $total += $invoice_info['receive_amount'];
            $sum_total += $invoice_info['trx_total'];

            $output .= <<<EOF
                    <tr>
                        <td><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$invoice_info['trx_id']}">{$invoice_info['trx_invoice_no_c']}</a></td>
                        <td>{$invoice_info['trx_time_c']}</td>
                        <td>{$invoice_info['trx_time_update_c']}</td>
                        <td>{$invoice_info['cus_id_c']}</td>
                        <td>{$invoice_info['trx_status_c']}</td>
                        <td class="text-right" style="text-align: right">{$invoice_info['trx_total_tax_c']}</td>
                        <td class="text-right" style="text-align: right">{$CMS->class->input->currency($invoice_info['data_bk']['receive_amount'])}</td>
                    </tr>
EOF;

        }


        $output .= <<<EOF
                        <tr>
                            <td colspan="5" style="text-align: right">{$CMS->lang['table_total']}</td>
                            <td class="text-right" style="text-align: right">{$CMS->class->input->currency($sum_total)}</td>
                            <td class="text-right" style="text-align: right">{$CMS->class->input->currency($total)}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
EOF;

        return $output;
    }

    function show_receive_info($data=[])
    {
        global $CMS, $DB;
        
        $data = $CMS->transactions->convertReceiveInvoiceInfo($data);

        if(!$data) return false;

        $subtype = $CMS->input['sub'] == 1 ? 2 : 7;

        $output = <<<EOF
<section class="add_table">
    <h4 class="heading" onclick="$('#show_receive_info').toggle();"><i class="fa fa-caret-down"></i><span>{$CMS->lang['related_transactions']}</span></h4>
    <div id="show_receive_info" class="table-responsive" style="overflow-x: initial;">
        <div>
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table_cus" width="100%">
                        <thead>
                        <tr>
                            <th scope="col" width="10%">{$CMS->lang['trx_subtype_0'.$subtype]}</th>
                            <th scope="col" width="20%">{$CMS->lang['created_at']}</th>
                            <th scope="col" width="20%">{$CMS->lang['updated_at']}</th>
                            <th scope="col" width="25%">{$CMS->lang['created_by']}</th>
                            <th scope="col" width="5%">{$CMS->lang['trx_status']}</th>
                            <th scope="col" width="10%" style="text-align: right">{$CMS->lang['trx_original_2']}</th>
                            <th scope="col"width="10%" style="text-align: right">{$CMS->lang['trx_receive_amount']}</th>
                        </tr>
                        </thead>
                        <tbody>
EOF;

        $total = 0;
        $sum_total = 0;

        foreach($data as $invoice_info)
        {
            $invoice_info = $CMS->transactions->convertvalue($invoice_info);

            $total += $invoice_info['receive_amount'];
            $sum_total += $invoice_info['trx_total'];

            $output .= <<<EOF
                    <tr>
                        <td><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$invoice_info['trx_id']}">{$invoice_info['trx_code']}</a></td>
                        <td>{$invoice_info['trx_time_c']}</td>
                        <td>{$invoice_info['trx_time_update_c']}</td>
                        <td>{$invoice_info['cus_id_c']}</td>
                        <td>{$invoice_info['trx_status_c']}</td>
                        <td class="text-right" style="text-align: right">{$invoice_info['trx_total_tax_c']}</td>
                        <td class="text-right" style="text-align: right">{$CMS->class->input->currency($invoice_info['data_bk']['receive_amount'])}</td>
                    </tr>
EOF;

        }


        $output .= <<<EOF
                        <tr>
                            <td colspan="5" style="text-align: right">{$CMS->lang['table_total']}</td>
                            <td class="text-right" style="text-align: right">{$CMS->class->input->currency($sum_total)}</td>
                            <td class="text-right" style="text-align: right">{$CMS->class->input->currency($total)}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
EOF;

        return $output;
    }

    function show_credit_memo_info($data=[])
    {
        global $CMS, $DB;

        $data = $CMS->transactions->convertReceiveInvoiceInfo($data);

        if(!$data) return false;

        $subtype = 8;

        $output = <<<EOF
<section class="add_table">
    <h4 class="heading" onclick="$('#show_credit_memo_info').toggle();"><i class="fa fa-caret-down"></i><span>{$CMS->lang['trx_info_8']}</span></h4>
    <div id="show_credit_memo_info" class="table-responsive" style="overflow-x: initial;">
        <div>
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table_cus" width="100%">
                        <thead>
                        <tr>
                            <th scope="col" width="10%">{$CMS->lang['trx_subtype_0'.$subtype]}</th>
                            <th scope="col" width="20%">{$CMS->lang['created_at']}</th>
                            <th scope="col" width="20%">{$CMS->lang['trx_credit_memo_date']}</th>
                            <th scope="col" width="25%">{$CMS->lang['created_by']}</th>
                            <th scope="col" width="5%">{$CMS->lang['trx_status']}</th>
                            <th scope="col" width="10%" style="text-align: right">{$CMS->lang['trx_original_2']}</th>
                            <th scope="col"width="10%" style="text-align: right">{$CMS->lang['trx_receive_amount']}</th>
                        </tr>
                        </thead>
                        <tbody>
EOF;

        $total = 0;
        $sum_total = 0;

        foreach($data as $invoice_info)
        {
            $invoice_info = $CMS->transactions->convertvalue($invoice_info);

            $total += $invoice_info['receive_amount'];
            $sum_total += $invoice_info['trx_total'];

            $output .= <<<EOF
                    <tr>
                        <td><a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$invoice_info['trx_id']}">{$invoice_info['trx_code']}</a></td>
                        <td>{$invoice_info['trx_time_c']}</td>
                        <td>{$invoice_info['trx_credit_memo_date_c']}</td>
                        <td>{$invoice_info['cus_id_c']}</td>
                        <td>{$invoice_info['trx_status_c']}</td>
                        <td class="text-right" style="text-align: right">{$invoice_info['trx_total_tax_c']}</td>
                        <td class="text-right" style="text-align: right">{$CMS->class->input->currency($invoice_info['data_bk']['receive_amount'])}</td>
                    </tr>
EOF;

        }


        $output .= <<<EOF
                        <tr>
                            <td colspan="5" style="text-align: right">{$CMS->lang['table_total']}</td>
                            <td class="text-right" style="text-align: right">{$CMS->class->input->currency($sum_total)}</td>
                            <td class="text-right" style="text-align: right">{$CMS->class->input->currency($total)}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
EOF;

        return $output;
    }

    function attachFilesShow($data = [])
    {
        global $CMS;

        $icons = $CMS->transactions->file_icons;

        $files = $CMS->attach->get_array("transaction", $data['trx_id']);


        if(!$files) return "";

        $output = <<<EOF
                <div class="box-typical box-typical-padding border" style="padding: 0px 15px !important;">
                    <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['attach_files']}</label>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <div class="box-typical-upload box-typical-upload-in">
                            <ul class="uploading-list list_upload">
EOF;



        foreach ($files as $file)
        {
            $file['attach_ext'] = strtolower($file['attach_ext']);
            $icon = isset($icons[$file['attach_ext']]) ? $icons[$file['attach_ext']] : $icons['default'];

            $output .= <<<EOF
            <li>
                <a href="{$file['attach_location']}" target="_blank">
                    <div class="uploading-list-item-wrapper">
                        <div class="uploading-list-item-name">
                            <i class="{$icon}"></i>
                            {$file['attach_name']}
                        </div>
                        <div class="uploading-list-item-size">{$CMS->class->input->formatSizeUnits($file['attach_size'])}</div>
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </div>
                </a>
            </li>
EOF;

        }

        $output .= <<<EOF
                            </ul>
                        </div>
                     </div>
                </div>
            </div>
EOF;
        return $output;
    }

    function preview_popup()
    {
        global $CMS;

        $output  = <<<EOF
        <!-- POPUP preview -->
<div id="box_preview_invoice" class="mfp-hide" style="clear: both; overflow: hidden;height:50%"  >
    <p class="title_add title_change_cus" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['preview_invoice']}</p>
  	<div class="pdfContent" data-dojo-attach-point="_pdfContent" style="width:100%;" height="100%">
  		<iframe src="" width="100%" height="400px" frameborder="0"></iframe>
    </div>

    <ul class="list_field_supplier">
        <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
            <fieldset class="form-group">
                <div class="typeahead-field"> 
                    <span class="typeahead-query change_action_cus" style="float:right"><a target="_blank" id="print_trx" href="" width="100%" height="400px" frameborder="0" class="btn btn_add_cus" type="button" style="float:right" >{$CMS->lang['print_invoice']}</a></span>
                </div>
                <div class="typeahead-field"> 
                    <span class="typeahead-query change_action_cus" style="float:right"><a target="_blank" id="print_trx" href="" width="100%" height="400px" frameborder="0" class="btn btn_add_cus" type="button" style="float:right" >{$CMS->lang['print_invoice']}</a></span>
                </div>
            </fieldset>
        </li>
    </ul>
</div>


<!-- POPUP preview model-->
<div id="box_preview_model" class="modal fade">
  <div class="modal-dialog modal-lg" role="document">
    <form method="post" id="sendEmailInv">
        <div class="modal-content">
          <div class="modal-header">
            <h5 style="float:left" class="modal-title">Preview</h5>
            <button style="float:right" type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="tabs-section-nav">
					<div class="tbl">
						<ul class="nav" role="tablist">
							<li class="nav-item">
								<a class="nav-link active send_email" group="tab_box_review" href="#tab_box_review" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										Preview & print
									</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link send_email" group="tab_send_email" href="#tab_send_email" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										Send email
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div><!--.tabs-section-nav-->
            <div class="tab-content box clearfix">
                <div id="tab_box_review" class="tab-pane fade active in">
                    <div class="pdfContent" data-dojo-attach-point="_pdfContent" style="width:100%;" height="100%">
                        <iframe src="" width="100%" height="400px" frameborder="0"></iframe>
                    </div>
                </div>
                <div id="tab_send_email" class="tab-pane fade">
                    <div class="rows" style="margin-top: 15px">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label">Email from (*):</label>
                                <div class="typeahead-field">
                                    <span class="typeahead-query">
                                        <input data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_cus_email_err']}" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}" type="text" class="form-control" name="email_from">
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label">Email to (*):</label>
                                <div class="typeahead-field">
                                    <span class="typeahead-query">
                                        <input data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_cus_email_err']}" type="text" class="form-control" name="email_to">
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label">CC:</label>
                                <div class="typeahead-field">
                                    <span class="typeahead-query">
                                        <input type="text" class="form-control" name="email_cc">
                                    </span>
                                </div>
                            </fieldset class="form-label">
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label">BCC:</label>
                                <div class="typeahead-field">
                                    <span class="typeahead-query">
                                        <input type="text" class="form-control" name="email_bcc">
                                    </span>
                                </div>
                            </fieldset class="form-label">
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label">Title (*):</label>
                                <div class="typeahead-field">
                                    <span class="typeahead-query">
                                        <input data-validation="[NOTEMPTY]" type="text" class="form-control" name="email_title">
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label class="form-label">Content (*):</label>
                                <div class="typeahead-field">
                                    <span class="typeahead-query">
                                        <textarea id="send_file_email_content" name="email_content" class="editor_texarea"></textarea>
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <input name="backTo" value="" type="hidden">
            <input id="trx_id" name="trx_id" value="" type="hidden">
            <div id="sendEmailInvBtn" style="cursor: pointer; display:none;" class="btn btn-primary tab_send_email group_tab"> Send email <img style="display:none; height: 60%" id="sendEmailLoading" src="/acp/images/fb-loading.gif"></div>
            <button type="button" class="btn btn-primary tab_box_review group_tab" onclick="printTrxPopup()">Print</button>
            <button type="button" class="btn btn-warning" onclick="backToPreviewTrxPopup()">Back</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
    </form>
    <script>
     $(document).ready(function(){
        validate_form_custom2("#sendEmailInv", function(){return sendEmailInv()},"#sendEmailInvBtn");
        
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if($(e.target).hasClass("send_email"))
            {
                $(".group_tab").hide();
                let group = $(e.target).attr("group");
                $(".group_tab."+group).show();   
            }
        })
    });
    </script>
  </div>
</div>

<!-- POPUP compose -->
<div id="box_compose_invoice" class="modal fade">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 style="float:left" class="modal-title">{$CMS->lang['compose']}</h5>
        <button style="float:right" type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <section class="tabs-section" id="previewTabs">
				<div class="tabs-section-nav">
					<div class="tbl">
						<ul class="nav" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" href="#tabs-2-tab-1" role="tab" data-toggle="tab" onclick="changeComposeTrxMode('default')">
									<span class="nav-link-in">
										{$CMS->lang['default_invoice']}
									</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#tabs-2-tab-2" role="tab" data-toggle="tab" onclick="changeComposeTrxMode('commercial')">
									<span class="nav-link-in">
										{$CMS->lang['commercial_invoice']}
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div><!--.tabs-section-nav-->
			</section>
        <textarea class="editor_texarea" id="invoice_content"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="previewTrxPopup()">{$CMS->lang['create_pdf']} & {$CMS->lang['preview']}<img style="display:none" id="previewTrxPopupLoading" src="/acp/images/fb-loading.gif"></button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$CMS->lang['close']}</button>
      </div>
    </div>
  </div>
</div>
EOF;

        return $output;

    }

    public function getExportFiles()
    {
        $output = <<<EOF
<!-- Modal -->
<div class="modal fade" id="getExportFiles" tabindex="-1" role="dialog" aria-labelledby="getExportFilesTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="getExportFiles" style="float:left">Attachments</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float:right">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <table id="getExportFilesTable" class="table table-striped table-bordered dt-responsive nowrap rows" cellspacing="0" width="100%">
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
EOF;
        return $output;
    }

    function store_options($default = 0)
    {
        global $CMS;

        if(! lib\security::checkPermission($CMS->input['site'], 'all_branches')) {
            return '';
        }

        \core\ezy::load_model("store", "acp");
        $stores = \models\store::getStores();
        $default *= 1;

        $output = <<<EOF
                    <div class="row">
                        <div class="col-xl-3 col-lg-5 col-md-7 col-sm-7 col-xs-12">
                            <fieldset class="form-group">
                                <label class="form-label">{$CMS->lang['store_id']}</label>
                                <select name="store_id" class="select2">
                                    <option value="0">--------</option>
EOF;

        if ($stores) {
            foreach ($stores as $store) {
                $selected = $store['store_id'] == $default ? "selected" : "";
                $output .= "<option {$selected} value='{$store['store_id']}'>{$store['store_name']}</option>";
            }
        }

        $output .= <<<EOF
                                </select>
                            </fieldset>
                        </div>
                    </div>
EOF;

        return $output;
    }
}
?>