<?php

class skin_returns {
	 

//===========================================================================
//  HTML HEADER
//===========================================================================

public function header()
{
	global $CMS, $DB, $member;
	
	$output = "";

 $ret_name = urldecode($CMS->input['quick_search']);
$output .= <<<EOF
 

<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['returns_title']}</h3>
		<figure class="pull-right right">
			<div class="search">
				<form method="post"  action="{$CMS->vars['root_domain']}/?site=returns&act=search" style="display:inline-block">

					<input type="submit" class="fa-input" value="&#xf002;">
					<input type="text" onkeyup="autocompleteSearch('#quick_search', '{$CMS->vars['root_domain']}/?site=returns&act=search&subact=searchkey', '', 'returns');" name="quick_search" id="quick_search" placeholder="{$CMS->lang['gsearch_quick']}" value="{$ret_name}">
				</form>
				 
			</div>
EOF;
			
		if($CMS->permit['returns_add'] == 1)
		{
			$output .=<<<EOF

			<a href="{$CMS->vars['root_domain']}/?site=returns&act=add" title="" class="add_bill">{$CMS->lang['returns_add']}</a>
EOF;

		}
			$output .=<<<EOF
		</figure>
	</figure>
	
<section class="add_table">
			
EOF;

    if($_SESSION['is_mobile'] == true)
    {
        $output .=<<<EOF
        <div class="data_table">
          <table id="example" class="display table table_cus" cellspacing="0" width="100%">
            <thead>
              <tr>
                  <th width="1%"></th>
  								<!--th  width="5%" data-sortable="false" >{$CMS->lang['ret_id']}</th-->
  								<th width="7%" data-sortable="true">{$CMS->lang['ret_code']}</th>
                  <th data-orderable="false" width="15%" >{$CMS->lang['ret_customer']}</th>
  								<th data-sortable="false" data-orderable="false" width="20%">{$CMS->lang['ret_product']}</th>
                  <th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_amount']}</th>
  				        <th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_store']}</th>
                  <!--th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_user_assign']}</th-->
                  <!--th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_user_check']}</th-->
                  <th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_status']}</th>
                  <th data-sortable="false" data-orderable="false"width="10%">{$CMS->lang['ret_time']}</th>
  					 
  								<th data-sortable="false" data-orderable="false" width="10%"></th>
              </tr>
            </thead>
          <tbody>
								
EOF;

    }
    else
    {
        $output .=<<<EOF
        <div class="table-responsive">
          <table class="table_cus" cellspacing="0" width="100%">
            <thead>
              <tr>
                <!--th  width="5%" data-sortable="false">{$CMS->lang['ret_id']}</th-->
                <th width="7%" data-sortable="true">{$CMS->lang['ret_code']}</th>
                <th data-orderable="false" width="15%">{$CMS->lang['ret_customer']}</th>
                <th data-sortable="false" data-orderable="false" width="20%">{$CMS->lang['ret_product']}</th>
                <th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_amount']}</th>
                <th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_store']}</th>
                <!--th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_user_assign']}</th-->
                <!--th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_user_check']}</th-->
                <th data-sortable="false" data-orderable="false" width="10%">{$CMS->lang['ret_status']}</th>
                <th data-sortable="false" data-orderable="false"width="10%">{$CMS->lang['ret_time']}</th>
           
                <th data-sortable="false" data-orderable="false" width="10%"></th>
            </tr>
            </thead>
          <tbody>
                
EOF;
    }
     

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";

  
    if($_SESSION['is_mobile'] == true)
    {
        $output .=<<<EOF
    <tr>
      <td></td>
      <!--td>#{$result['ret_id']}</td-->
      <td>{$result['ret_code']}</td>
      <td>{$result['cus_name_show']}</td>
      <td>{$result['ret_assets_show']}</td>
      <td>{$result['ret_amount_show']}</td>
      <td>{$result['store_name_show']}</td>
      <!--td>{$result['user_name_assign']}</td-->
      <!--td>{$result['user_name_check']}</td-->
      <td>{$result['ret_status_show']}</td>
      <td>{$result['ret_time_bk']}</td>
      <td><div class='pull-right'>{$result['action_box']}</div></td>
    </tr>
EOF;
    }
    else
    {
      if($_SESSION['highlight'] == $result['ret_id'])
      {
        $class_hl = "highlight_row";
      }else
      {
        $class_hl = "";
      }
      $output .=<<<EOF
    <tr class="{$class_hl}">
      <!--td>#{$result['ret_id']}</td-->
      <td>{$result['ret_code']}</td>
      <td>{$result['cus_name_show']}</td>
      <td>{$result['ret_assets_show']}</td>
      <td>{$result['ret_amount_show']}</td>
      <td>{$result['store_name_show']}</td>
      <!--td>{$result['user_name_assign']}</td-->
      <!--td>{$result['user_name_check']}</td-->
      <td>{$result['ret_status_show']}</td>
      <td>{$result['ret_time_bk']}</td>
      <td>{$result['action_box']}</td>
    </tr>

EOF;
    }
   
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function none()
{
	global $CMS, $DB, $member;
	
	$output = "";
  if($CMS->input['ret_name'] != "")
  {

    $output_search = <<<EOF
       Không có kết quả theo từ khóa: {$CMS->input['ret_name']}
EOF;
  }
  else
  {
    $output_search .= <<<EOF
        {$CMS->lang['returns_no_data']}
EOF;

  }

  if($_SESSION['is_mobile'] == false)
  {
    $output .= <<<EOF
       <tr><td colspan="9">{$output_search}</td></tr>
EOF;

  }

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
			 
					</tbody>
				</table>

		</div><!--.box-typical-body-->
		 
	 </section>
		<div class="block_bottom pagination pagination-sm">
			{$CMS->returns->show_page}
		</div>
		 
</section>
EOF;
  if($CMS->input['ret_name'] != "")
  {

    $output_search = <<<EOF
       Không có kết quả theo từ khóa: {$CMS->input['ret_name']}
EOF;
  }
  else
  {
    $output_search = <<<EOF
        {$CMS->lang['returns_no_data']}
EOF;

  }

    if($_SESSION['is_mobile'] == true)
    {
        $output .=<<<EOF
         <script>
            $(function() {
              $('#example').DataTable({
              language: {
                      emptyTable: '{$output_search}'
                  },
                 order: [],
              responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 1 },
                    { responsivePriority: 2, targets: 2 },
                    
                ],
                paging: false,
                  searching: false,
                  info: false
              });
            });
          </script>
EOF;

    }
    
  unset($_SESSION['highlight']);
	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF
<div class="block_action">
	
</div>
EOF;

    return $output;
}

//===========================================================================
//  HTML EDIT
//===========================================================================

public function edit( $data )
{
	global $CMS, $DB, $member;
        
    // print "<pre>";
    // print_r($data);exit;
    $_SESSION['list_product'] = json_decode($data['ret_assets'],1);
    list($row_store, $option_store, $store_id_first) = $CMS->store->get_list_store($data['store_id']);

    $html_product = $CMS->global->htmlTableAsset($_SESSION['list_product']);
    // print "<pre>";
    // print_r($_SESSION['list_product']);exit;
    $btn_edit_cus = "";
    if($data['cus_id'])
    {
        $btn_edit_cus = <<<EOF
          <a id='{$data['cus_id']}' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;

    }
    $output=<<<EOF
<form id="form_returns" name="form_returns" class="form_returns" action="{$CMS->vars['root_domain']}/?site=returns&act=edit_do&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">
  
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['returns_title_edit']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=returns{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
<figure class="box-typical box-typical box-typical-padding border">
  <div class="row">
    <div class="col-md-6">
      <div class="row">                                        
        <div class="col-md-6">
          <fieldset class="form-group" id="box_choose_customer">
                <label class="form-label pull-left">{$CMS->lang['ret_customer']} <span style="color:red">(*)</span></label>
                <div class="box_action pull-right">
                    <a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    <span class="box_edit_cus pull-right" style="margin-left: 10px;">{$btn_edit_cus}</span>
                      
                </div>
                
                <div class="box_container form-control-wrapper" style="clear: both;">
                    <div class="input-group">
                        <input class="form-control cus_name_check" type="text" name="cus_name" id="cus_name" data-validation="[NOTEMPTY]" sub="1" data-validation-message="{$CMS->lang['ret_not_empty_customer']}" value="{$data['cus_name']}" data-prompt-position="bottomRight" autocomplete="off">
                        <div class="input-group-addon">
                          <span style="cursor:pointer" onclick="autocompleteAll('#cus_name')" class="fa fa-arrow-down"></span>
                        </div>
                    </div>
              </div>
              <script>
              $(document).ready(() =>  autocompleteSearch('#cus_name', site_root_domain + '/?site=customer&subact=quicksearch', '#cus_id','customer',0))
              </script>
              <input name="cus_id" id="cus_id" type="hidden" value="{$data['cus_id']}">
            </fieldset>
          <fieldset class="form-group">
            <label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
// 
        if($row_store >= 3)
        {
          $output .=<<<EOF
            <div class="form-control-wrapper">
              <div class="box_choose_store">
                <select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control select2" autocomplete="off" data-validation-message="{$CMS->lang['plz_choose_store']}" data-validation="[NOTEMPTY]">           
                            {$option_store}
                          </select>
              </div>
            </div>

EOF;
        }                 
        else
        {
          $output .=<<<EOF
           {$option_store}
EOF;
        }
        
        $output .=<<<EOF
            </fieldset>
            <fieldset class="form-group">
              <label class="form-label pull-left" for="ret_fee">{$CMS->lang['ret_fee']}</label>
              
              <input name="ret_fee" value="{$data['ret_fee']}" class="form-control" onkeypress="return check_enter_number(event, this)" maxlength="15" autocomplete="off">
            </fieldset>
        </div>
        <div class="col-md-6">
          <fieldset class="form-group">
            <label class="form-label pull-left" for="user_id_assign">{$CMS->lang['user_id_assign']}</label>
            <div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_assign', 'user_assign')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
            <div class="input-group"  style="clear: both;">
              <input name="user_name_assign" value="{$data['user_name_assign']}" class="form-control search_user" autocomplete="off">
              <div class="input-group-addon">
                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user')" class="fa fa-arrow-down"></span>
              </div>
            </div>
            <input name='user_assign' type='hidden' value="{$data['user_assign']}" class="user_assign" />
          </fieldset>

          <fieldset class="form-group">
            <label class="form-label pull-left" for="user_id_check">{$CMS->lang['user_id_check']}</label>
            <div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_check', 'user_check')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
            <div class="input-group"  style="clear: both;">
              <input name="user_name_check" value="{$data['user_name_check']}" class="form-control search_user_check" autocomplete="off">
              <div class="input-group-addon">
                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user_check')" class="fa fa-arrow-down"></span>
              </div>
            </div>
            <input name='user_check' type='hidden' value="{$data['user_check']}" class="user_check" />
          </fieldset>
          <script>
            $(document).ready(() =>  autocompleteSearch('.search_user', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_assign','',0));
            $(document).ready(() =>  autocompleteSearch('.search_user_check', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_check','',0));
            </script>
          
        </div>
    </div>
    </div>
    <div class="col-md-6">
      
      <fieldset class="form-group">
        <label class="form-label" for="ret_note">{$CMS->lang['ret_note']}</label>
        <textarea name="ret_note" id="ret_note" rows="6" cols="50" class="form-control">{$data['ret_note']}</textarea>  
      </fieldset>
      <fieldset class="form-group">
        <h4>{$CMS->lang['ret_total_amount']}</h4>
        <p class="total_price" id="total_refund">0 đ</p>
      </fieldset>

    </div>
</section>

    {$html_product}
    <section class="add_cart_footer">
      <a href="{$CMS->vars['root_domain']}/?site=returns" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
      <button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_edit_ret" data-style="expand-right" data-size="xs" type="button" ><span class="ladda-label">{$CMS->lang['returns_title_edit']}</span><span class="ladda-spinner"></span></button>
    </section>
  </form>
  
  {$CMS->global->fullFormHtml()}
  </div>
</section>
<script src="{$CMS->vars['js_acp']}/custom_transaction.js?20180312"></script>
<script>
  $(document).ready(function(){
    edit_do_customer('#box_customer','#box_choose_customer');
    add_new_customer('#box_choose_customer');
    add_do_new_customer('#box_choose_customer','#box_choose_customer');
    validate_form_custom("#form_returns",".btn_edit_ret", "returns");
  });
</script>

EOF;
    return $output;
}

//===========================================================================
//  HTML ADD
//===========================================================================

public function add( $data )
{
	   global $CMS, $DB, $member;
        
    // print "<pre>";
    // print_r($data);exit;
    
    list($row_store, $option_store, $store_id_first) = $CMS->store->get_list_store($data['store_id'], 0, 0);

    $html_product = $CMS->global->htmlTableAsset($_SESSION['list_product']);
    // print "<pre>";
    // print_r($_SESSION['list_product']);exit;
    $btn_edit_cus = "";
    if($data['cus_id'])
    {
        $btn_edit_cus = <<<EOF
          <a id='{$data['cus_id']}' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;

    }
    $output=<<<EOF
<form id="form_returns" name="form_returns" class="form_returns" action="{$CMS->vars['root_domain']}/?site=returns&act=add_do" method="POST" enctype="multipart/form-data">
  
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['returns_title_add']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=returns{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
<figure class="box-typical box-typical box-typical-padding border">
  <div class="row">
    <div class="col-md-6">
      <div class="row">                                        
        <div class="col-md-6">
          <fieldset class="form-group" id="box_choose_customer">
                <label class="form-label pull-left">{$CMS->lang['ret_customer']} <span style="color:red">(*)</span></label>
                <div class="box_action pull-right">
                    <a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    <span class="box_edit_cus pull-right" style="margin-left: 10px;">{$btn_edit_cus}</span>
                      
                </div>
                
                <div class="box_container form-control-wrapper" style="clear: both;">
                    <div class="input-group">
                        <input class="form-control cus_name_check" type="text" name="cus_name" id="cus_name" data-validation="[NOTEMPTY]" sub="1" data-validation-message="{$CMS->lang['ret_not_empty_customer']}" value="{$data['cus_name']}" data-prompt-position="bottomRight" autocomplete="off">
                        <div class="input-group-addon">
                          <span style="cursor:pointer" onclick="autocompleteAll('#cus_name')" class="fa fa-arrow-down"></span>
                        </div>
                    </div>
              </div>
              <script>
              $(document).ready(() =>  autocompleteSearch('#cus_name', site_root_domain + '/?site=customer&subact=quicksearch', '#cus_id','customer',0))
              </script>
              <input name="cus_id" id="cus_id" type="hidden" value="{$data['cus_id']}">
            </fieldset>
          <fieldset class="form-group">
            <label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
// 
        if($row_store >= 3)
        {
          $output .=<<<EOF
            <div class="form-control-wrapper">
              <div class="box_choose_store">
                <select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control select2" autocomplete="off" data-validation-message="{$CMS->lang['plz_choose_store']}" data-validation="[NOTEMPTY]">           
                            {$option_store}
                          </select>
              </div>
            </div>

EOF;
        }                 
        else
        {
          $output .=<<<EOF
           {$option_store}
EOF;
        }
        
        $output .=<<<EOF
            </fieldset>
            <fieldset class="form-group">
              <label class="form-label pull-left" for="ret_fee">{$CMS->lang['ret_fee']}</label>
              
              <input name="ret_fee" value="{$data['ret_fee']}" class="form-control" onkeypress="return check_enter_number(event, this)" maxlength="15" autocomplete="off">
            </fieldset>
        </div>
        <div class="col-md-6">
          <fieldset class="form-group">
            <label class="form-label pull-left" for="user_id_assign">{$CMS->lang['user_id_assign']}</label>
            <div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_assign', 'user_assign')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
            <div class="input-group"  style="clear: both;">
              <input name="user_name_assign" value="{$data['user_name_assign']}" class="form-control search_user" autocomplete="off">
              <div class="input-group-addon">
                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user')" class="fa fa-arrow-down"></span>
              </div>
            </div>
            <input name='user_assign' type='hidden' value="{$data['user_assign']}" class="user_assign" />
          </fieldset>

          <fieldset class="form-group">
            <label class="form-label pull-left" for="user_id_check">{$CMS->lang['user_id_check']}</label>
            <div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_check', 'user_check')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
            <div class="input-group"  style="clear: both;">
              <input name="user_name_check" value="{$data['user_name_check']}" class="form-control search_user_check" autocomplete="off">
              <div class="input-group-addon">
                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user_check')" class="fa fa-arrow-down"></span>
              </div>
            </div>
            <input name='user_check' type='hidden' value="{$data['user_check']}" class="user_check" />
          </fieldset>
          <script>
            $(document).ready(() =>  autocompleteSearch('.search_user', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_assign','',0));
            $(document).ready(() =>  autocompleteSearch('.search_user_check', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_check','',0));
            </script>
          
        </div>
    </div>
    </div>
    <div class="col-md-6">
      
      <fieldset class="form-group">
        <label class="form-label" for="ret_note">{$CMS->lang['ret_note']}</label>
        <textarea name="ret_note" id="ret_note" rows="6" cols="50" class="form-control">{$data['ret_note']}</textarea>  
      </fieldset>
      <fieldset class="form-group">
        <h4>{$CMS->lang['ret_total_amount']}</h4>
        <p class="total_price" id="total_refund">0 đ</p>
      </fieldset>

    </div>
</section>

    {$html_product}
    <section class="add_cart_footer">
      <a href="{$CMS->vars['root_domain']}/?site=returns" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
EOF;

    if($_SESSION['is_mobile'] == true)
    {
      
        $output_btn = <<<EOF
        <div class="btn-group dropup pull-right hidden-xl-up">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-save"></i>Lưu
          </button>
          <div class="dropdown-menu">
            <ul>
            <li class="hidden-xl-up">
              <a class="btn_add_ret" page_type="1" ><i class="fa fa-save"></i>{$CMS->lang['returns_title_add_and_approve']}</a>
            </li>
            <li class="hidden-xl-up">
              <a class="btn_add_ret" page_type="0" ><i class="fa fa-save"></i>{$CMS->lang['returns_title_add']}</a>
            </li>

          </ul>
          </div>
        </div>
        
EOF;


    }else
    {
      $output_btn =<<<EOF

        <button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_ret" data-style="expand-right" data-size="xs" type="button" page_type="1"><span class="ladda-label">{$CMS->lang['returns_title_add_and_approve']}</span><span class="ladda-spinner"></span></button>

        <button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_add_ret" data-style="expand-right" data-size="xs" type="button" page_type="0"><span class="ladda-label">{$CMS->lang['returns_title_add']}</span><span class="ladda-spinner"></span></button>
EOF;

    }

    $output .=<<<EOF
          {$output_btn}
        <input type="hidden" name="type_submit" value="0" />
      
    </section>
  </form>
  
  {$CMS->global->fullFormHtml()}
  </div>
</section>
<script src="{$CMS->vars['js_acp']}/custom_transaction.js?20180312"></script>
<script>
  $(document).ready(function(){
    $(".btn_add_ret").click(function(){
      var checktype = $(this).attr("page_type");
      $("input[name='type_submit']").val(checktype);
    });
    
    edit_do_customer('#box_customer','#box_choose_customer');
    add_new_customer('#box_choose_customer');
    add_do_new_customer('#box_choose_customer','#box_choose_customer');
    validate_form_custom("#form_returns",".btn_add_ret", "returns");
  });
</script>

EOF;
    return $output;
}

//===========================================================================
//  HTML SHOW
//===========================================================================

public function show( $data )
{
	global $CMS, $DB, $member;
 
	$output = "";
  if($_SESSION['is_mobile'] and !$_SESSION['is_tablet']) 
  {
    $label_125 = 'title-label-120';
    $label_100 = 'title-label-120';
  }else
  {
    $label_125 = 'title-label-125';
    $label_100 = 'title-label-100';
  }

  if($_SESSION['is_mobile']) 
  {
    $position = "pull-left";
  }else
  {
    $position = "pull-right";
  }
$output .= <<<EOF
 <section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['returns_info']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=returns&page={$CMS->input['page']}" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			<div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
 				<fieldset class="row">
					<div class="form-control-label2" >
            <div class="title_label {$label_125} pull-left">{$CMS->lang['ret_code']}</div>
            <div class="form-control-span2">{$data['ret_code']}</div>
          </div>
				</fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_125} pull-left">{$CMS->lang['ret_customer']}</div>
            <div class="form-control-span2">{$data['cus_name_show']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_125} pull-left">{$CMS->lang['ret_time']}</div>
            <div class="form-control-span2">{$data['ret_time_bk']}</div>
          </div>
        </fieldset>
				
        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_125} pull-left">{$CMS->lang['ret_user_assign']}</div>
            <div class="form-control-span2">{$data['user_name_assign']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_125} pull-left">{$CMS->lang['ret_user_check']}</div>
            <div class="form-control-span2">{$data['user_name_check']}</div>
          </div>
        </fieldset>
 			</div>


      <div class="col-xl-5 col-md-6 col-sm-6 col-xs-12">
        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_100} pull-left">{$CMS->lang['ret_store']}</div>
            <div class="form-control-span2">{$data['store_name_show']}</div>
          </div>
        </fieldset>

EOF;
        if($data['request_id'])
        {
            $info_request = $CMS->store_request->get_info($data['request_id']);
            $stage = $info_request['request_stage'] == 2 ? "request_ei" : "request_eis";
            $type_bill = $info_request['request_type'] == 1 ? "import" : "export";
            $request_code = "<a href='{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}&act=show&id={$data['request_id']}'>{$info_request['request_code']}</a>";
$output .=<<<EOF
        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_100} pull-left">{$CMS->lang['ret_request_code']}</div>
            <div class="form-control-span2">{$request_code}</div>
          </div>
        </fieldset>

EOF;
        }

        $trans_link = "";
        if($data['trx_id_fee'])
        {
          $trx_info = $CMS->transactions->getInfo($data['trx_id_fee']);
$trans_link =<<<EOF

              (<a href="{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$data['trx_id_fee']}">{$trx_info['trx_code']}</a>)
EOF;

        }

        if($data['trx_id'])
        {
$output .=<<<EOF
        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_100} pull-left">{$CMS->lang['ret_trx_id']}</div>
            <div class="form-control-span2">{$data['trx_id_show']}</div>
          </div>
        </fieldset>
EOF;
        }

$output .=<<<EOF

        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_100} pull-left">{$CMS->lang['ret_fee']}</div>
            <div class="form-control-span2">{$data['ret_fee_show']} {$trans_link}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
            <div class="title_label {$label_100} pull-left">{$CMS->lang['ret_note']}</div>
            <div class="form-control-span2" style=" display: inline; padding: 0;">{$data['ret_note']}</div>
          </div>
        </fieldset>

      </div>

      <div class="col-xl-3 col-md-12 col-sm-12 col-xs-12">
        <div class="{$position}">
          <fieldset class="form-group">
            <h4>{$CMS->lang['ret_total_amount']}</h4>
            <p class="total_price" style="margin: 0">{$data['ret_amount_show']}</p>
          </fieldset>
          <fieldset class="form-group">
            {$data['ret_status_show']}
          </fieldset>
        </div>
      </div>

 		</div>
 	 </figure>
 </section>

<section class="add_table">
   <h4 class="heading"><i class="fa fa-caret-down"></i><span>{$CMS->lang['request_property']}</span></h4>
       <div class="data-table">
          <table id="example" class="display table table_cus" width="100%">
            <thead>
              <tr>
                  <th width="1%"></th>
                  <th data-sortable="false" width="5%">#ID</th>
                  <th data-sortable="false" width="25%">{$CMS->lang['table_name']}</th>
                  <th data-sortable="false" width="20%">{$CMS->lang['table_code']}</th>
                  <th data-sortable="false" width="10%">{$CMS->lang['table_quantity']}</th>
                  <th data-sortable="false" width="10%">{$CMS->lang['table_price']}</th>
                  <th data-sortable="false" width="10%" style="text-align: right">{$CMS->lang['table_tax']}</th>
                  <th data-sortable="false" width="10%" style="text-align: right">{$CMS->lang['table_amount']}</th>
           
              </tr>
            </thead>  
            <tbody>
EOF;

        $data_product = json_decode($data['ret_assets'], true);
// print "<pre>";
// print_r($data_product);exit;
        if(is_array($data_product))
        {
          $count = count($data_product);
          $i=1;
          $total = 0;
          foreach ($data_product as $key => $value) 
          {
            $subtotal = $value['ass_price'] *  $value['ass_quantity'];
            $fee_tax = $value['ass_tax'];

            $amount = $subtotal + round(($subtotal * $fee_tax)/100);
            $total += $amount;
            $product_amount = $CMS->class->input->currency($amount);

            $name_product = $value['ass_name'];
            if($value['product_id'])
            {
              $name_product = "<a style='display: inline-block;' href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$value['product_id']}'>{$name_product}</a>";
            }

            if($CMS->permit['assets_read'] and $value['product_id'])
            {
              $name_product .= " <a data-toggle='tooltip' data-placement='bottom' title='{$CMS->lang['tooltip_search_assets']}' style='display: inline-block;' href='{$CMS->vars['root_domain']}/?site=assets&product_id={$value['product_id']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
            }

            $description = $value['ass_code'];
            $quantity = $value['ass_quantity'];
            $price = $value['ass_price'];
            $price = $CMS->class->input->currency($price);
            $fee_tax_show = $fee_tax ? $fee_tax."%" : "";
$output .=<<<EOF

              <tr>
                <td></td>
                <td>#{$i}</td>
                <td>{$name_product}</td>
                <td>{$description}</td>
                <td>{$quantity}</td>
                <td>{$price}</td>
                <td style="text-align: right">{$fee_tax_show}</td>
                <td style="text-align: right">{$product_amount}</td>
              </tr>
EOF;
            $i++;
          }
        }

        $total_show = $CMS->class->input->currency($total);
        if($_SESSION['is_mobile'] == true)
        {
$total_html =<<<EOF
            <table id="example" class="display table" width="100%" style="background: #fff; border: 1px solid #e3e3e3;">
              <tr>
                <td></td>
                <td></td>
                <td style="text-align: right; font-size: 14px; padding: 10px 15px;">{$CMS->lang['table_total']}</td>
                <td style="text-align: right; font-size: 14px; padding: 10px 15px;">{$total_show}</td>
              </tr>
            </table>

EOF;
        }else
        {
$total_html = "";
$output .=<<<EOF
          
              <tr>
                <td colspan="6"></td>
                <td style="text-align: right" width="15%">{$CMS->lang['table_total']}</td>
                <td style="text-align: right" width="15%">{$total_show}</td>
              </tr>
                 
EOF;
        }
$output .=<<<EOF

            
              </tbody>
        </table>
          {$total_html}
      </div>

EOF;
    if($_SESSION['is_mobile'] == true)
    {
        $output .=<<<EOF
         <script>
            $(function() {
              $('#example').DataTable({
              language: {
                      emptyTable: '{$CMS->lang['returns_no_data']}'
                  },
                 order: [],
              responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: -2 },
                    { responsivePriority: 2, targets: -1 },
                    
                ],
                paging: false,
                  searching: false,
                  info: false
              });
            });
          </script>
EOF;

    }  
    $output .=<<<EOF
 </section>
 
<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=returns" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
EOF;
    $btn_delete = "";
    $btn_delete_mobile = "";
    $btn_edit = "";
    $btn_edit_mobile = "";
    $btn_approve = "";
    $btn_approve_mobile = "";
						if($CMS->permit['returns_delete'] == 1 and ($data['ret_status'] == 0 or $data['ret_status'] == 3))
						{

							$btn_delete .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=returns&act=delete&id={$data['ret_id']}');"  class="pull-right add_cart_2 hidden-md-down">{$CMS->lang['delete']}</a>

EOF;
              $btn_delete_mobile .=<<<EOF
                <li>
                    <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=returns&act=delete&id={$data['ret_id']}');"><i class="fa fa-trash"></i>{$CMS->lang['delete']}</a>
                </li>
EOF;


						}

						if($CMS->permit['returns_edit'] == 1 and $data['ret_status'] == 0)
						{
							$btn_edit .=<<<EOF
							<a href="{$CMS->vars['root_domain']}/?site=returns&act=edit&id={$data['ret_id']}"   class="pull-right add_cart_2 hidden-md-down">{$CMS->lang['edit']}</a>
EOF;

              $btn_edit_mobile .=<<<EOF
                <li>
                  <a href="{$CMS->vars['root_domain']}/?site=returns&act=edit&id={$data['ret_id']}"><i class="fa fa-pencil"></i>{$CMS->lang['edit']}</a>
                </li>
EOF;


						}

            if($CMS->permit['returns_approve'] == 1 and $data['ret_status'] == 0)
            {

              $btn_approve .=<<<EOF
              <a href="{$CMS->vars['root_domain']}/?site=returns&act=approve&id={$data['ret_id']}"  class="pull-right add_cart_2 hidden-md-down">{$CMS->lang['title_approve_returns']}</a>

EOF;

              $btn_approve_mobile .= <<<EOF
                <li>
                  <a href="{$CMS->vars['root_domain']}/?site=returns&act=approve&id={$data['ret_id']}"><i class="fa fa-check-circle-o"></i>{$CMS->lang['title_approve_returns']}</a>
                </li>
EOF;


            }            

						 
        if($btn_delete or $btn_edit or $btn_approve)
        {						
					$output .=<<<EOF
          {$btn_delete} 
          {$btn_edit}   
          {$btn_approve}
          <div class="btn-group dropup pull-right hidden-lg-up">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-save"></i>{$CMS->lang['title_action']}
            </button>
            <div class="dropdown-menu">
              <ul>
                {$btn_approve_mobile}
                {$btn_edit_mobile} 
                {$btn_delete_mobile}  
              </ul>
            </div>
          </div>
EOF;
        }
$output .=<<<EOF

		</section>

		{$this->formAddreturns()}
EOF;
 
	
	return $output;	
}

//===========================================================================
//  HTML SEARCH
//===========================================================================

public function search($data)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<form method="post" id="returns" name="returns" action="{$CMS->vars['root_domain']}/?site=returns&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=returns{$CMS->class->search->url_return}">&laquo; {$CMS->lang['returns_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['returns_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="returns_name" value="{$data['returns_name']}"></td>
  </tr>
   <tr>
    <td class="left25"><b>{$CMS->lang['position_id']}</b>:</td>
    <td>
    		  <select class="input_text select2" name="position_id" id="position_id" defaultvalue="{$CMS->input['position_id']}" onchange="this.form.submit()">
                {$CMS->returns->get_position_list()}
            </select>
    </td>
  </tr>
 <tr>
    <td class="left25"><b>{$CMS->lang['returns_status']}</b>:</td>
    <td>
    		    <select class="input_text select2" name="returns_status" id="returns_status" defaultvalue="{$CMS->input['returns_status']}" onchange="this.form.submit()">
                     <option value="">{$CMS->lang['returns_status']}</option>
                    <option value="1">{$CMS->lang['yes']}</option>
                     <option value="0">{$CMS->lang['no']}</option>
                </select>
    
    </td>
  </tr>

  <tr>
    <td class="left25"><b>{$CMS->lang['returns_website']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="returns_website" value="{$data['returns_website']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['returns_owner']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="returns_owner" value="{$data['returns_owner']}"></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("returns",1,1);</script>
EOF;
	
	return $output;	
}

public function formAddreturns()
	{
		global $CMS;
		$pg_type = 0;
		 
		$output =<<<EOF
		<div id="box_add_returns" class="popup_add_returns mfp-hide">
			<p class="title_add_returns  " style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['returns_new']}</p>
			<p style="color:red" class="shimanu_error_msg"></p>
			<form id="add_returns_form" name="add_returns_form">
			<input type="hidden" name="ret_id" />
				<ul class="list_field_returns">		
					  
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['returns_text_name']} <span style="color:red">(*)</span></label>
										<input class="form-control" type="text" name="ret_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_name_err']}" value="{$ret_name}" maxlength="50">
									
							</fieldset>
						</li>
						<li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">	
							<fieldset class="form-group">
								<label class="form-label" >{$CMS->lang['returns_text_description']}</label>
										<textarea class="form-control" rows="5"  name="ret_description" id="ret_description" maxlength="250"></textarea>
									
							</fieldset>
						</li>
						  
							<li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
								<fieldset class="form-group">
									<div class="typeahead-field"> 
										<span class="typeahead-query change_action_returns"><input class="btn btn_add_returns" type="button" value="Thêm lô hàng"></span>
									</div>
								</fieldset>
							</li> 

				</ul>
			 
			</form>
		</div>	

		 

EOF;
		return $output;
	}

public function approve( $data )
{
    global $CMS, $DB, $member;
        
    // print "<pre>";
    // print_r($data);exit;
    $_SESSION['list_product'] = json_decode($data['ret_assets'],1);
    list($row_store, $option_store, $store_id_first) = $CMS->store->get_list_store($data['store_id']);

    $html_product = $CMS->global->htmlTableAsset($_SESSION['list_product']);
    // print "<pre>";
    // print_r($_SESSION['list_product']);exit;
    $btn_edit_cus = "";
    if($data['cus_id'])
    {
        $btn_edit_cus = <<<EOF
          <a id='{$data['cus_id']}' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;

    }
    $output=<<<EOF
<form id="form_returns" name="form_returns" class="form_returns" action="{$CMS->vars['root_domain']}/?site=returns&act=approve_do&id={$CMS->input['id']}" method="POST" enctype="multipart/form-data">
  
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['returns_title_approve']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=returns{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
<figure class="box-typical box-typical box-typical-padding border">
  <div class="row">
    <div class="col-md-6">
      <div class="row">                                        
        <div class="col-md-6">
          <fieldset class="form-group" id="box_choose_customer">
                <label class="form-label pull-left">{$CMS->lang['ret_customer']} <span style="color:red">(*)</span></label>
                <div class="box_action pull-right">
                    <a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    <span class="box_edit_cus pull-right" style="margin-left: 10px;">{$btn_edit_cus}</span>
                      
                </div>
                
                <div class="box_container form-control-wrapper" style="clear: both;">
                    <div class="input-group">
                        <input class="form-control cus_name_check" type="text" name="cus_name" id="cus_name" data-validation="[NOTEMPTY]" sub="1" data-validation-message="{$CMS->lang['ret_not_empty_customer']}" value="{$data['cus_name']}" data-prompt-position="bottomRight" autocomplete="off">
                        <div class="input-group-addon">
                          <span style="cursor:pointer" onclick="autocompleteAll('#cus_name')" class="fa fa-arrow-down"></span>
                        </div>
                    </div>
              </div>
              <script>
              $(document).ready(() =>  autocompleteSearch('#cus_name', site_root_domain + '/?site=customer&subact=quicksearch', '#cus_id','customer',0))
              </script>
              <input name="cus_id" id="cus_id" type="hidden" value="{$data['cus_id']}">
            </fieldset>
          <fieldset class="form-group">
            <label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
EOF;
// 
        if($row_store >= 3)
        {
          $output .=<<<EOF
            <div class="form-control-wrapper">
              <div class="box_choose_store">
                <select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control select2" autocomplete="off" data-validation-message="{$CMS->lang['plz_choose_store']}" data-validation="[NOTEMPTY]">           
                            {$option_store}
                          </select>
              </div>
            </div>

EOF;
        }                 
        else
        {
          $output .=<<<EOF
           {$option_store}
EOF;
        }
        
        $output .=<<<EOF
            </fieldset>
            <fieldset class="form-group">
              <label class="form-label pull-left" for="ret_fee">{$CMS->lang['ret_fee']}</label>
              <input name="ret_fee" value="{$data['ret_fee']}" class="form-control" onkeypress="return check_enter_number(event, this)" maxlength="15" autocomplete="off">
            </fieldset>
        </div>
        <div class="col-md-6">
          <fieldset class="form-group">
            <label class="form-label pull-left" for="user_id_assign">{$CMS->lang['user_id_assign']}</label>
            <div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_assign', 'user_assign')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
            <div class="input-group"  style="clear: both;">
              <input name="user_name_assign" value="{$data['user_name_assign']}" class="form-control search_user" autocomplete="off">
              <div class="input-group-addon">
                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user')" class="fa fa-arrow-down"></span>
              </div>
            </div>
            <input name='user_assign' type='hidden' value="{$data['user_assign']}" class="user_assign" />
          </fieldset>

          <fieldset class="form-group">
            <label class="form-label pull-left" for="user_id_check">{$CMS->lang['user_id_check']}</label>
            <div class="pull-right"><a class='form-label' onclick="cus_assign_me(this,'user_name_check', 'user_check')" val_name='{$member['user_display_name']}' val_id='{$member['user_id']}'>{$CMS->lang['title_assign_me']}</a></div>
            <div class="input-group"  style="clear: both;">
              <input name="user_name_check" value="{$data['user_name_check']}" class="form-control search_user_check" autocomplete="off">
              <div class="input-group-addon">
                  <span style="cursor:pointer" onclick="autocompleteAll('.search_user_check')" class="fa fa-arrow-down"></span>
              </div>
            </div>
            <input name='user_check' type='hidden' value="{$data['user_check']}" class="user_check" />
          </fieldset>
          <script>
            $(document).ready(() =>  autocompleteSearch('.search_user', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_assign','',0));
            $(document).ready(() =>  autocompleteSearch('.search_user_check', site_root_domain + '/?site=user&act=search&subact=search_user', '.user_check','',0));
          </script>

          
        </div>
    </div>
    </div>
    <div class="col-md-6">
      
      <fieldset class="form-group">
        <label class="form-label" for="ret_note">{$CMS->lang['ret_note']}</label>
        <textarea name="ret_note" id="ret_note" rows="6" cols="50" class="form-control">{$data['ret_note']}</textarea>  
      </fieldset>
      <fieldset class="form-group">
        <h4>{$CMS->lang['ret_total_amount']}</h4>
        <p class="total_price" id="total_refund">0 đ</p>
      </fieldset>

    </div>
</section>

    {$html_product}
    <section class="add_cart_footer">
      <a href="{$CMS->vars['root_domain']}/?site=returns" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
      <button class="btn btn-inline btn-primary ladda-button pull-right add_cart btn_edit_ret" data-style="expand-right" data-size="xs" type="button" ><span class="ladda-label">{$CMS->lang['returns_title_approve']}</span><span class="ladda-spinner"></span></button>
    </section>
  </form>
  
  {$CMS->global->fullFormHtml()}
  </div>
</section>
<script src="{$CMS->vars['js_acp']}/custom_transaction.js?20180312"></script>
<script>
  $(document).ready(function(){
    edit_do_customer('#box_customer','#box_choose_customer');
    add_new_customer('#box_choose_customer');
    add_do_new_customer('#box_choose_customer','#box_choose_customer');
    validate_form_custom("#form_returns",".btn_edit_ret", "returns");
  });
</script>

EOF;
    return $output;
}


}

?>