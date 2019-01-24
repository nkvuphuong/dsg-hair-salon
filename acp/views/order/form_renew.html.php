 
<form id="formorenew_rder-signin_v1" name="formorenew_rder-signin_v1" action="<?=$tpl->form_action;?>" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$CMS->lang['order_title_renew'];?></h3>
            <a href="<?=$CMS->vars['root_domain'];?>/?site=order&act=show&id=<?=$tpl->data['ordi']['ord_id']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
 
                    <div class="row">
                        <div class="col-lg-7">
                            <fieldset class="form-group row">
                                <label class="col-xl-6 col-md-6 col-sm-6 col-xs-12 form-control-label2"><?=$CMS->lang['order_name'];?></label>
                                <div class="col-xl-6 col-sm-6 col-sm-6 col-xs-12 form-control-span2"><?=$tpl->data['ord']['ord_name'];?></div>
                            </fieldset>
                        </div><!-- col-lg-6-->
                         
                    </div><!-- class row -->
                    <div class="row">
                        <div class="col-lg-7">
                            <fieldset class="form-group row">
                                <label class="col-xl-6 col-md-6 col-sm-6 col-xs-12 form-control-label2"><?=$CMS->lang['product_header'];?></label>
                                <div class="col-xl-6 col-sm-6 col-sm-6 col-xs-12 form-control-span2"><?=$tpl->data['product']['product_name'];?></div>
                            </fieldset>
                        </div><!-- col-lg-6-->
                         
                    </div><!-- class row -->
   
                   <div class="row">
                 
                       <div class="col-lg-6">
                            <label class="form-label"><?=$CMS->lang['price'];?></label>
                            <div class="input-group">
                                    <input name="price" id="price" type="text" value="<?=$tpl->data['product']['product_price_o'];?>"  onkeypress="return check_enter_number(event,this);"  class="form-control" onfocusout="check_enter_number_2(this,0);" class="form-control">    
                             </div>
                        </div>     
             
                       <div class="col-lg-6">
                            <label class="form-label"><?=$CMS->lang['product_period'];?>: <?=$CMS->lang['cycle_typeunit_'.$tpl->data['ordi']['cycle_type']]?></label>
                            <input type="hidden" id="cycle_type" value="<?=$tpl->data['ordi']['cycle_type'];?>" />
                            <div class="input-group">
                                    <select class="form-control" name="cycle" id="cycle">
                                        <?php 
                                            for($i = 1; $i<=12; $i++)
                                            {

                                        ?>
                                            <option value="<?=$i;?>"><?=$i;?></option>
                                            <?php } ?>

                                    </select>  
                             </div>
                        </div>    
                  </div>  

                    <div class="row">
                 
                       <div class="col-lg-6">
                            <label class="form-label"><?=$CMS->lang['start_time'];?></label>
                            <div class="input-group">
                                        <input name="start_time" id="start_time" type="text" value="<?=$tpl->data['ordi']['ordi_expiry_date_bk'];?>" class="form-control datetimepicker-1">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                
                             </div>
                        </div>     
             
                       <div class="col-lg-6">
                            <label class="form-label"><?=$CMS->lang['end_time'];?> <font style="margin-left:5px" color="#FF0000">(*)</font></label>
                            <div class="input-group form-control-wrapper">
                                        <input name="end_time" id="end_time" type="text" value="" class="form-control datetimepicker-1" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['choice_date_end_renew'];?>"  >
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                
                             </div>
                        </div>    
                  </div>  


                </div><!-- col-md-6-->

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cus_show_info" style="display:<?=$tpl->data['customer']['cus_id'] > 0 ? "block" : "none";?>">
                        <h4 class="cus_show_info"><?=$CMS->lang['cus_info'];?></h4>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_realname'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_name_display"><?=$tpl->data['customer']['cus_full_name'];?></div>
                        </div>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_email'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_email"><?=$tpl->data['customer']['cus_email'];?></div>
                        </div>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_address'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_address"><?=$tpl->data['customer']['cus_address'];?></div>
                        </div>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_phone'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_phone"><?=$tpl->data['customer']['cus_phone'];?></div>
                        </div>
                    </div>
                    <h4><?=$CMS->lang['product_total_money'];?></h4>
                    <p class="total_price" id="total_renew_price">0</p>
                </div><!-- col-md-6-->
            </div>
        </figure>

    </section>

    
    <section class="add_cart_footer">
         <a href="<?=$CMS->vars['root_domain'];?>/?site=order&act=show&id=<?=$tpl->data['ordi']['ord_id']?>" class="pull-left cancel" title=""><?=$CMS->lang['back'];?></a>

         <button class="act_submit_save btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs" value="confirm_paid"><span class="ladda-label"><?=$CMS->lang['button_renew'];?></span><span class="ladda-spinner"></span><span class="ladda-spinner"></span></button>
    </section>                          


</form>
 
 <p style="font-style: italic;"><?=$CMS->lang['note_form_renew'];?></p>
<script src="<?=$CMS->vars['js_acp'];?>/order_renew.js"></script>
 <script>
    $(document).ready(function(){
        validate_form_custom("#formorenew_rder-signin_v1",".act_submit_save","renew_order");
    });
</script>