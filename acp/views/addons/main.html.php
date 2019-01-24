<section class="add_table main_form">
    <figure class="heading">
      <h3><?=$CMS->lang['header_home'];?></h3>
    </figure>
  <figure class="box-typical box-typical-padding border">
    <div class="row">
      <header class="box-typical-header-sm"><?=$CMS->lang['title_list_addons'];?></header>
      <form name="config_general" action="?site=addons&act=edit_do" method="post" enctype="multipart/form-data">
        
        <div class="<?=$tpl->tableMobile['large_div'];?>">
          <table class="table_cus <?=$tpl->tableMobile['class_table'];?>" id="<?=$tpl->tableMobile['id_table'];?>" cellspacing="0" width="100%">
            <thead>
              <tr>
                <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : ""; ?>
                <th width="5%" data-sortable="true"><?=$CMS->lang['table_no'];?></th>
                <th data-orderable="false" width="15%"><?=$CMS->lang['table_module_name'];?></th>
                <th data-sortable="false" data-orderable="false" width="35%"><?=$CMS->lang['table_description'];?></th>
                <th data-sortable="false" data-orderable="false" width="10%"><?=$CMS->lang['table_license'];?></th>
                <th data-sortable="false" data-orderable="false" width="15%"><?=$CMS->lang['table_date_expried'];?></th>
                <th data-sortable="false" data-orderable="false" width="10%"><?=$CMS->lang['table_onoff'];?></th>
				<th data-sortable="false" data-orderable="false" width="10%"><?=$CMS->lang['table_setting'];?></th>
				<th data-sortable="false" data-orderable="false" width="10%"><?=$CMS->lang['table_status'];?></th>
              </tr>
            </thead>
            <tbody>
            <!--List config addons-->
            <? /* foreach ($tpl->listAddons as $data) { ?>
              <tr>
                <td>#<?=$data['conf_stt'];?></td>
                <td><?=$data['conf_title'];?></td>
                <td style="white-space: inherit;"><?=$data['conf_description'];?></td>
                <td><?=$data['conf_lisence'];?></td>
                <td>
                  <div class="checkbox-toggle">
                    <input type="checkbox" id="<?=$data['conf_key'];?>" name="<?=$data['conf_key'];?>">
                    <label for="<?=$data['conf_key'];?>"></label>
                  </div>
                </td>
				<td></td>
				<td></td>
              </tr>
            <? } */ ?>
            <!--End List config addons-->

            <tr>
                <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : ""; ?>
                <td>#1</td>
                <td style="white-space: inherit;"><?=$CMS->lang['label_management_goods'];?></td>
                <td style="white-space: inherit;"></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
                  <div class="checkbox-toggle">
                    <input type="checkbox" id="addon_goods_enable" class="auto_check" name="addon_goods_enable" value="<?=$CMS->vars['addon_goods_enable'];?>" onclick="updateStatus(this)">
                    <label for="addon_goods_enable"></label>
                  </div>
                </td>
				<td></td>
				<td></td>
              </tr>
              <tr>
                <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : ""; ?>
                <td>#2</td>
                <td style="white-space: inherit;"><?=$CMS->lang['label_management_website'];?></td>
                <td style="white-space: inherit;"></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
                  <div class="checkbox-toggle">
                    <input type="checkbox" id="addon_website_enable" class="auto_check" name="addon_website_enable" value="<?=$CMS->vars['addon_website_enable'];?>" onclick="updateStatus(this)">
                    <label for="addon_website_enable"></label>
                  </div>
                </td>
				<td></td>
				<td></td>
              </tr>
			  
			<tr>
				<?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#3</td>
				<td style="white-space: inherit;"><?=$CMS->lang['label_fresh_desk'];?></td>
                <td style="white-space: inherit;"><?=$CMS->lang['label_fresh_desk_description'];?></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
					<div class="checkbox-toggle">
						<input type="checkbox" id="addon_fresh_desk_enable" class="auto_check" name="addon_fresh_desk_enable" value="<?=$CMS->vars['addon_fresh_desk_enable'];?>" onclick="updateStatus(this)">
						<label for="addon_fresh_desk_enable"></label>
					</div>
                </td>
				<td>
					<? if ($CMS->vars['addon_fresh_desk_enable'] == 1) { ?>
					<ul class="list-inline">
						<li class="list-inline-item"><a href="<?=$CMS->vars['root_domain']?>/?site=addons&act=setting&module=freshdesk" title="<?=$CMS->lang['table_setting'];?>" style="color: #929faa;"><i class="fa fa-cog" aria-hidden="true"></i></a></li>
						<li class="list-inline-item"><a href="<?=$CMS->vars['root_domain']?>/?site=addons&act=logs&module=freshdesk" title="<?=$CMS->lang['table_logs'];?>" style="color: #929faa;"><i class="fa fa-history" aria-hidden="true"></i></a></li>
					</ul>
					<? } ?>
				</td>
				<td>
					<? if ($CMS->vars['addon_fresh_desk_enable'] == 1) {
						if(\lib\tpl::get('freshdesk_connect')) { ?>
					<button type="button" class="btn btn-rounded btn-inline btn-success"><?=$CMS->lang['success'];?></button>
					<? } else { ?>
					<button type="button" class="btn btn-rounded btn-inline btn-danger"><?=$CMS->lang['error'];?></button>
						<? }
					} ?>
				</td>
            </tr>
			<tr>
				<?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#4</td>
				<td style="white-space: inherit;"><?=$CMS->lang['label_graph_facebook'];?></td>
                <td style="white-space: inherit;"><?=$CMS->lang['label_graph_facebook_description'];?></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
					<div class="checkbox-toggle">
						<input type="checkbox" id="addon_graph_facebook_enable" class="auto_check" name="addon_graph_facebook_enable" value="<?=$CMS->vars['addon_graph_facebook_enable'];?>" onclick="updateStatus(this)">
						<label for="addon_graph_facebook_enable"></label>
					</div>
                </td>
				<td></td>
				<td></td>
            </tr>

			<tr>
				<?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#5</td>
				<td style="white-space: inherit;"><?=$CMS->lang['label_chatbot_facebook'];?></td>
                <td style="white-space: inherit;"><?=$CMS->lang['label_chatbot_facebook_description'];?></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
					<div class="checkbox-toggle">
						<input type="checkbox" id="addon_chatbot_facebook_enable" class="auto_check" name="addon_chatbot_facebook_enable" value="<?=\lib\input::vars('addon_chatbot_facebook_enable');?>" onclick="updateStatus(this)">
						<label for="addon_chatbot_facebook_enable"></label>
					</div>
                </td>
				<td>
					<? if (\lib\input::vars('addon_chatbot_facebook_enable') == 1) { ?>
					<ul class="list-inline">
						<li class="list-inline-item">
							<a href="<?=$CMS->vars['root_domain']?>/?site=addons&act=setting&module=chatbot_facebook" title="<?=$CMS->lang['table_setting'];?>" style="color: #929faa;">
								<i class="fa fa-cog" aria-hidden="true"></i>
							</a>
						</li>
						<li class="list-inline-item">
							<a href="<?=$CMS->vars['root_domain']?>/?site=addons&act=logs&module=chatbot_facebook" title="<?=$CMS->lang['table_logs'];?>" style="color: #929faa;">
								<i class="fa fa-history" aria-hidden="true"></i>
							</a>
						</li>
					</ul>
					<? } ?>
				</td>
				<td></td>
            </tr>
            <tr>
                <?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#6</td>
                <td style="white-space: inherit;"><?=$CMS->lang['label_enable_shipping'];?></td>
                <td style="white-space: inherit;"><?=$CMS->lang['label_enable_shipping_description'];?></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
                    <div class="checkbox-toggle">
                        <input type="checkbox" id="enable_shipping" class="auto_check" name="enable_shipping" value="<?=\lib\input::vars('enable_shipping');?>" onclick="updateStatus(this)">
                        <label for="enable_shipping"></label>
                    </div>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#7</td>
                <td style="white-space: inherit;"><?=$CMS->lang['selling_machine'];?></td>
                <td style="white-space: inherit;"></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
                    <div class="checkbox-toggle">
                        <input type="checkbox" id="pos_enabled" class="auto_check" name="pos_enabled" value="<?=$CMS->vars['pos_enabled'];?>" onclick="updateStatus(this)">
                        <label for="pos_enabled"></label>
                    </div>
                </td>
                <td>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="<?=$CMS->vars['root_domain']?>/?site=config_general&tab=tabs-config-sale" title="<?=$CMS->lang['table_setting'];?>" style="color: #929faa;"><i class="fa fa-cog" aria-hidden="true"></i></a></li>
                    </ul>
                </td>
                <td></td>
            </tr>
            <tr>
                <?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#8</td>
                <td style="white-space: inherit;"><?=$CMS->lang['booking_page'];?></td>
                <td style="white-space: inherit;"></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
                    <div class="checkbox-toggle">
                        <input type="checkbox" id="booking_page" class="auto_check" name="booking_page" value="<?=$CMS->vars['booking_page'];?>" onclick="updateStatus(this)">
                        <label for="booking_page"></label>
                    </div>
                </td>
                <td>
                </td>
                <td></td>
            </tr>

            <tr>
                <?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#8</td>
                <td style="white-space: inherit;"><?=$CMS->lang['menu_price'];?></td>
                <td style="white-space: inherit;"></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
                    <div class="checkbox-toggle">
                        <input type="checkbox" id="price_book_enabled" class="auto_check" name="price_book_enabled" value="<?=$CMS->vars['price_book_enabled'];?>" onclick="updateStatus(this)">
                        <label for="price_book_enabled"></label>
                    </div>
                </td>
                <td>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="<?=$CMS->vars['root_domain']?>/?site=price" title="<?=$CMS->lang['table_setting'];?>" style="color: #929faa;"><i class="fa fa-cog" aria-hidden="true"></i></a></li>
                    </ul>
                </td>
                <td></td>
            </tr>

            <tr>
                <?=($tpl->tableMobile['td_mobile'] == 1) ? '<th width="1%"></th>' : "";?>
                <td>#9</td>
                <td style="white-space: inherit;">Checkin</td>
                <td style="white-space: inherit;"></td>
                <td>Free</td>
                <td>Unlimited</td>
                <td>
                    <div class="checkbox-toggle">
                        <input type="checkbox" id="checkin_enabled" class="auto_check" name="checkin_enabled" value="<?=$CMS->vars['checkin_enabled'];?>" onclick="updateStatus(this)">
                        <label for="checkin_enabled"></label>
                    </div>
                </td>
                <td>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="<?=$CMS->vars['root_domain']?>/?site=config_general&tab=tabs-config-sale" title="<?=$CMS->lang['table_setting'];?>" style="color: #929faa;"><i class="fa fa-cog" aria-hidden="true"></i></a></li>
                    </ul>
                </td>
                <td></td>
            </tr>

            </tbody>
          </table>
        </div>


      </form>
    </div>
  </figure>
</section>
<script>
  $(document).ready(function(){
     $(".auto_check").each(function(){
        var valcheck = $(this).val();
        if(valcheck == 1)
        {
          $(this).attr("checked", "checked");
        }else
        {
          $(this).removeAttr("checked");
        }
     });
  // Check script
  <? if($tpl->tableMobile['script_mobile'] == 1) { ?> 
    $('#example').DataTable({
      language: {
              emptyTable: 'No data'
          },
         order: [],
      responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: 2 },
            { responsivePriority: 2, targets: -1 },
            
        ],
        paging: false,
          searching: false,
          info: false
      });

    <? } ?>
  });

  function updateStatus(onthis)
  {
    var conf_key = $(onthis).attr("name");
    var curr_val = $(onthis).val();
    var conf_val = 0;
    
    if(curr_val == 1)
    {
      $(onthis).prop("checked", false);
      $(onthis).val(0);
      conf_val = 0;
    }else
    {
      $(onthis).prop("checked", true);
      $(onthis).val(1);
      conf_val = 1;
    }

    // Update status
    $.ajax({
        type: "post",
        url: "?site=addons&act=edit_do",
        data: {conf_key:conf_key, conf_val:conf_val},
        success: function(html)
        {
          window.location.href = "";
        }
    });
  }
</script>  