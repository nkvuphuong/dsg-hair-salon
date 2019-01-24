<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['title_redeem'];?></h3>
        <figure class="pull-right right">
            <div class="search">
                <form method="post" id="formquicksearch_adv" action="<?=$CMS->vars['root_domain']?>/?site=redeem&subact=quick_search" style="display:inline-block">
                    <input type="submit" class="fa-input" value="&#xf002;">
                    <input name="keyword" id="quick_search" type="text" value="<?=urldecode($CMS->input['keyword'])?>" autocomplete="off" minlength="2" maxlength="64" placeholder="<?=$CMS->lang['giftcard_search_quick']?>" style="position: :relative;">
                </form>
            </div>
            <a href="<?=$CMS->vars['root_domain']?>/?site=redeem&act=add" title="" class="add_bill">Add card for customer</a>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>
    </figure>
    <form method="post" name="form_redeem" id="form_redeem" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="<?=$tpl->tableMobile['large_div'];?>">
                <div class="" style="border-top: none;">
                    <table id="<?=$tpl->tableMobile['id_table'];?>" class="table_cus <?=$tpl->tableMobile['class_table'];?>" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : ""; ?>
                            <th><div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_redeem');" id="checkall">
                                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                                   <label></label>
                                </div>
                            </th>
                            <th><?=$CMS->lang['giftcard_code']?></th>
                            <th><?=$CMS->lang['giftcard_name']?></th>
                            <th><?=$CMS->lang['giftcard_image']?></th>
                            <th><?=$CMS->lang['giftcard_amount']?></th>
                            <th><?=$CMS->lang['giftcard_amount_remain']?></th>
                            <!--th><?=$CMS->lang['title_time_create']?></th-->
                            <th><?=$CMS->lang['title_last_used']?></th>
                            <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody class="box_list_redeem">
                        <? if($tpl->data) { ?>
                            <?foreach($tpl->data as $result) {?>
                                <tr>
                                    <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : ""; ?>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt'];?>" id="id_<?=$result['record_cnt'];?>" value="<?=$result['gitem_id'];?>"/>
                                            <label for="id_<?=$result['record_cnt'];?>"></label>
                                         </div>
                                    </td>
                                    <td><?= $CMS->permit['redeem_read'] ? "<a href='{$CMS->vars['root_domain']}/?site=redeem&act=show&id={$result['gitem_id']}'>{$result['giftcard_code']}</a>" : "";?></td>
                                    <td style="text-align: center;"><?=$result['cus_name'];?><?=$result['cus_email'] ? "({$result['cus_email']})" : "";?></td>
                                    <td><img src="<?=$result['image'];?>" style="max-width: 200px; padding: 5px;"/><p style="clear: both; padding: 5px 0;"><?=$result['data_product']['product_name'];?></p></td>
                                    <td><?=$result['amount'];?></td>
                                    <td><?=$result['amount_remain'];?></td>
                                    <!--td><?=\lib\date::format($result['gitem_time'],"M d, Y g:i a");?></td-->
                                    <td><?= $result['gitem_time_update'] == $result['gitem_time'] ? "N/A" : \lib\date::format($result['gitem_time_update'],"M d, Y g:i a");?></td>
                                    <td style="text-align:center">
                                        <? if(($CMS->permit['redeem_edit'] || $CMS->permit['redeem_is_root']) and $result['gitem_amount_remain'] > 0){?>
                                            <button type="button" amount_remain="<?=$result['amount_remain'];?>" id="<?=$result['gitem_id'];?>" class="btn btn-primary add_redeem" name="redeem">Redeem</button>
                                        <?}?>
                                        <? if($CMS->permit['redeem_edit']){?>
                                            <button type="button" amount_remain="<?=$result['amount_remain'];?>" id="<?=$result['gitem_id'];?>" class="btn btn-primary edit_amount" name="edit_amount"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                        <?}?>
                                        <a href="<?=$CMS->vars['root_domain'];?>/?site=redeem&subact=sendmail&id=<?=$result['gitem_id'];?>" class="edit"><i class="fa fa-envelope-o"></i></a>
                                        <? if($CMS->permit['redeem_delete']) { ?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain'];?>/?site=redeem&act=delete&id=<?=$result['gitem_id'];?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
                                        <? } ?>
                                    </td>
                                </tr>
                            <?}?>
                        <?} else {?>
                            <?= $tpl->tableMobile['script_mobile'] == 1 ? "" : "<tr><td colspan=\"8\">{$CMS->lang['no_data']}</td></tr>";?>
                        <?}?>
                        </tbody>
                    </table>
                </div>
                <div class="fuction_table">
                    <div class="pull-left">
                        <p class="form-control-static ">
                            <select class="form-control" name="act" onchange="return check_submit_form('<?=$CMS->lang['gnotice_confirm_action'];?>', 'form_redeem', this);" defaultvalue="delete_all" emsg="<?=$CMS->lang['incomplete_action'];?>" ehide="1">
                                <option value=""><?=$CMS->lang['choose_action'];?></option>
                                <?=$tpl->control;?>
                            </select>
                        </p>
                    </div>
                    <nav class="pull-right">
                        <div class="block_bottom pagination pagination-sm"><?=$CMS->show_page;?></div>
                    </nav>
                </div>
            </div>
        </section>
        <input type="hidden" name="data_cnt" value="<?=\models\redeem::$record_cnt;?>">
    </form>
    <div id="box_redeem" class="popup_giftcard mfp-hide col-lg-4" style="background: #fff; display: table; margin: auto; float: none; padding: 20px;">
        <p style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;"><?=$CMS->lang['title_use_giftcard'];?></p>
        <form id="redeem_form" action="<?=$CMS->vars['root_domain'];?>/?site=redeem&act=edit_do" name="redeem_form" method="post" enctype="multipart/form-data">
          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
            <ul class="list_field_giftcard">
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="form-group row">
                      <label class="form-control-label"><?=$CMS->lang['title_choose_date'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control datetimepicker-3" type="text" name="redeem_date" value="" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['title_choose_date_incomplete']?>">
                      </div>
                    </div>
                </li>
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="form-group row">
                      <label class="form-control-label"><?=$CMS->lang['title_amount_remain'];?>: <span style="font-size: 16px; color: red;" class="box_amount_remain"></span></label>
                      <input type="hidden" name="gitem_id" value=""/> 
                    </div>
                </li>

                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <div class="form-group row">
                    <label class="form-control-label"><?=$CMS->lang['title_enter_amount'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="redeem_amount" onkeypress="return check_enter_number(event, this);" maxlength="4" value="" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['title_enter_amount_incomplete'];?>">
                    </div>
                  </div>
                </li>
                        
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                    <div class="form-group row">
                      <label class="form-control-label"></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <button class="btn" type="submit" name="add_redeem"><?=$CMS->lang['redeem_add_submit'];?></button>
                      </div>
                    </div>
                </li>
              </ul>
            </div>
        </form>
    </div>

    <div id="edit_amount" class="popup_giftcard mfp-hide col-lg-4" style="background: #fff; display: table; margin: auto; float: none; padding: 20px;">
        <p style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;"><?=$CMS->lang['title_edit_amount'];?></p>
        <form id="edit_money_form" action="<?=$CMS->vars['root_domain'];?>/?site=redeem&act=edit_do&subact=edit_amount" name="edit_money_form" method="post" enctype="multipart/form-data">
          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
            <p style="font-size: 14px; color: #f00; font-style: italic; margin:0;"><?=$CMS->lang['title_note_add_money'];?></p>
            <ul class="list_field_giftcard">
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="form-group row">
                      <label class="form-control-label"><?=$CMS->lang['title_amount_remain'];?>: <span style="font-size: 16px; color: red;" class="box_amount_remain"></span></label>
                      <input type="hidden" name="gitem_id" value=""/> 
                    </div>
                </li>

                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <div class="form-group row">
                    <label class="form-control-label"><?=$CMS->lang['title_enter_amount'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="money_amount" onkeypress="return check_enter_number(event, this);" maxlength="4" value="" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['title_enter_amount_incomplete'];?>">
                    </div>
                  </div>
                </li>
                        
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                    <div class="form-group row">
                      <label class="form-control-label"></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <button class="btn confirm_add" type="button" name="add_money"><?=$CMS->lang['money_add_submit'];?></button>
                      </div>
                    </div>
                </li>
              </ul>
            </div>
        </form>
    </div>
</section>
<script src="<?=$CMS->vars['root_domain'];?>/jsacp/app_footer.js"></script>
<script>
    $(document).ready(function(){
        $(".box_list_redeem").on("click",".add_redeem", function(){
            var gitem_id = $(this).attr("id");
            var amount_remain = $(this).attr("amount_remain");
            $("#box_redeem .box_amount_remain").html(amount_remain);
            $("#box_redeem input[name='gitem_id']").val(gitem_id);
            
            $.magnificPopup.open({
              type: 'inline',
              preloader: false,
              focus: '#name',
              items: {
                src: '#box_redeem'
              },

              // When elemened is focused, some mobile browsers in some cases zoom in
              // It looks not nice, so we disable it:
              callbacks: {
                beforeOpen: function() {
                  if($(window).width() < 700) {
                    this.st.focus = false;
                  } else {
                    this.st.focus = '#name';
                  }
                }
              }
            });
        });

        $(".box_list_redeem").on("click",".edit_amount", function(){
            var gitem_id = $(this).attr("id");
            var amount_remain = $(this).attr("amount_remain");
            $("#edit_amount .box_amount_remain").html(amount_remain);
            $("#edit_amount input[name='gitem_id']").val(gitem_id);
            
            $.magnificPopup.open({
              type: 'inline',
              preloader: false,
              focus: '#name',
              items: {
                src: '#edit_amount'
              },

              // When elemened is focused, some mobile browsers in some cases zoom in
              // It looks not nice, so we disable it:
              callbacks: {
                beforeOpen: function() {
                  if($(window).width() < 700) {
                    this.st.focus = false;
                  } else {
                    this.st.focus = '#name';
                  }
                }
              }
            });
        });
        // validate_form_custom("#edit_amount", "button[name='add_money']");
        // check add money
        $(".confirm_add").click(function(argument) {
            swal({
                  title: 'Are you sure?',
                  text: "Are you sure you want to renew this gift card?",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Ok'
                }).then(function () {
                    $("form[name='edit_money_form']").submit();
                });
        })

        validate_form_custom("#redeem_form", "button[name='add_redeem']");
        
        <? if($tpl->tableMobile['script_mobile'] == 1) { ?> 
        $('#example').DataTable({
          language: {
                  emptyTable: 'No data'
              },
             order: [],
          responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: 2 },
                { responsivePriority: 3, targets: 5 },
                { responsivePriority: 4, targets: -1 },
            ],
            paging: false,
              searching: false,
              info: false
          });

        <? } ?>

        // Search ajax
        $("#quick_search").keyup(function(){
            var key_search = $(this).val();
            // if(key_search.length >= 2)
            // {
                $.ajax({
                    type: "post",
                    url: site_root_domain+"/?site=redeem&subact=searchajax",
                    data: {key_search: key_search},
                    beforeSend: function(){

                    },
                    success: function(html)
                    {
                        // console.log(html);
                        var result = JSON.parse(html);
                        var output="";
                        var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        for(var x in result)
                        {
                            var email_show = result[x].cus_email ? `(`+result[x].cus_email+`)` : '';
                            var d = new Date(parseInt(result[x].gitem_time)* 1000);
                            var d2 = new Date(parseInt(result[x].gitem_time_update)* 1000);
                            // console.log(d.getMonth(), month[0], result[x].gitem_time);
                            var hours = d.getHours() >= 12 ? d.getHours() - 12 + ":"+d.getMinutes() + " pm" : d.getHours() + ":"+d.getMinutes() + " am";
                            var hours2 = d2.getHours() >= 12 ? d2.getHours() - 12 + ":"+d2.getMinutes() + " pm" : d2.getHours() + ":"+d2.getMinutes() + " am"; 
                            var time_show = month[d.getMonth()] +" "+d.getDate()+", "+ d.getFullYear()+" "+hours;
                            var last_used = month[d2.getMonth()] +" "+d2.getDate()+", "+ d2.getFullYear()+" "+hours2;

                            last_used = parseInt(result[x].gitem_time) == parseInt(result[x].gitem_time_update) ? "N/A" : last_used;

                            var button = "";
                            var button_del = "";
                            if(result[x].gitem_amount_remain > 0)//(permit.redeem_edit || permit.redeem_is_root) &&
                            {
                                button = `
                                    <button type="button" amount_remain="`+result[x].amount+`" id="`+result[x].gitem_id+`" class="btn btn-primary add_redeem" name="redeem">Redeem</button>
                                `;
                            }


                            if(pms['redeem_delete']) { 
                                button_del += `<a onclick="delete_confirm('`+site_root_domain+`/?site=redeem&act=delete&id=`+result[x].gitem_id+`');" class="edit"><i class="fa fa-trash-o"></i></a>`;
                            }

                            output += `
                                <tr>
                                    <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : ""; ?>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_`+result[x].record_cnt+`" id="id_`+result[x].record_cnt+`" value="`+result[x].gitem_id+`"/>
                                            <label for="id_`+result[x].record_cnt+`"></label>
                                         </div>
                                    </td>
                                    <td>`+result[x].giftcard_code+`</td>
                                    <td style="text-align: center;">`+result[x].cus_name + email_show + `</td>
                                    <td><img src="`+result[x].image+`" style="max-width: 200px; padding: 5px;"/><p style="clear: both; padding: 5px 0;">`+result[x].data_product.product_name+`</p></td>
                                    <td>`+result[x].amount+`</td>
                                    <td>`+result[x].amount_remain+`</td>
                                    <td>`+time_show+`</td>
                                    <td>`+last_used+`</td>
                                    <td style="text-align:center">
                                        `+button+`
                                        <a href="`+site_root_domain+`/?site=redeem&subact=sendmail&id=`+result[x].gitem_id+`" class="edit"><i class="fa fa-envelope-o"></i></a>
                                        `+button_del+`
                                    </td>
                                </tr>
                            `;
                        }

                        $(".box_list_redeem").html(output);
                        if(output)
                        {
                            $(".fuction_table .pull-right").hide();
                        }else
                        {
                            $(".fuction_table .pull-right").show();
                        }

                    }
                });
            // }// End if check length
        });
    });
</script>
