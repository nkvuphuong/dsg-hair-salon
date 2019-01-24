<link rel="stylesheet" type="text/css" href='/acp/assets/css/orders.css'>
<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site'];?>"
      action="/acp/?site=attribute&act=<?=$tpl->act;?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : ""; ?>" onsubmit="return check_form(this.id);">
    <section class="add_form main_form manage-container">
        <figure class="heading">
            <h3><?=$tpl->header_title;?></h3>
            <a href="/acp/?site=attribute" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <div class="box-typical box-typical-padding no-radius-top">
            <div class="row">
                <div class="col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label"><?=$CMS->lang['group_name'];?> <span style="color:red">(*)</span></label>
                      <div class="form-control-wrapper">
                      <input class="form-control" type="text" name="group_name" id="group_name" value="<?=$tpl->data['group_name'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['attr_name_group_error'];?>">
                  </div>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label"><?=$CMS->lang['group_description'];?></label>
                      <textarea class="form-control" name="group_description" id="group_description" rows="7"><?=$tpl->data['group_description'];?></textarea>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="form-label"><?=$CMS->lang['group_status'];?></label>
                    <div class="radio w25">
                        <input type="radio" name="group_status" id="status_1" value="1">
                        <label for="status_1"><?=$CMS->lang['group_status_1'];?></label>
                    </div>   
                    <div class="radio w25">
                        <input type="radio" name="group_status" id="status_0" value="0">
                        <label for="status_0"><?=$CMS->lang['group_status_0'];?></label>
                    </div>                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h5 class="section-title no-pt">
                        <?=$CMS->lang['add_attribute_title'];?>
                        <a class="pull-right" style="font-size: 14px;" onclick="openPopup('#open_list_attribute', listAttribute());"><i class="fa fa-th-list"></i> Attribute list</a>
                    </h5>
                    
                </div>
            </div>
            
            <div class="box_addtribute row">
                <div class="box_header col-md-12">
                    <div class="line">
                        <div class="box_css col-md-2">
                            <label class="lbl_row"><?=$CMS->lang['attr_name'];?></label>
                        </div>
                        <div class="box_css col-md-2">
                            <label class="lbl_row"><?=$CMS->lang['attr_value'];?></label>
                            
                        </div>
                        <div class="box_css col-md-2">
                            <label class="lbl_row"><?=$CMS->lang['attr_key'];?></label>
                        </div>
                        <div class="box_css col-md-2">
                            <label class="lbl_row"><?=$CMS->lang['attr_unit'];?></label>
                        </div>
                        <div class="box_css col-md-1">
                            <label class="lbl_row"><?=$CMS->lang['attr_order'];?></label>
                        </div>
                        
                        <div class="box_css col-md-2">
                            <label class="lbl_row"><?=$CMS->lang['attr_description'];?></label>
                        </div>
                        <div class="box_css col-md-1">
                            <label class="lbl_row">&nbsp;</label>
                        </div>
                    </div>
                </div>
                <div class="box_list_attribute col-md-12">
                    <? if(count($tpl->attribute_list) > 0) { 
                            foreach ($tpl->attribute_list as $attribute) {?>
                        <div class="box_line" rowid="line_<?=$attribute['attr_id'];?>">
                           <div class="mini_line" style="display: none;">
                                <div class=" col-md-12">
                                    <p class="line_name"><?=$attribute['attr_name'];?></p>
                                    <span class="expand">+</span>
                                </div>
                            </div>
                            <div class="line">
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Name</label>
                                    <span class="name"><?=$attribute['attr_name'];?></span>
                                    <input type="hidden" value="<?=$attribute['attr_id'];?>" name="attr_id[]">
                                </div>
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Value</label>
                                    <p class="content value"><a class="view_list_options" datajson='<?=json_encode($attribute['attribute_options'], JSON_UNESCAPED_UNICODE);?>' id="<?=$attribute['attr_id'];?>" onclick="openPopup('#box_list_options', viewListOptions(this));"><?=$attribute['options_count'];?></a></p>
                                </div>
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Key</label>
                                    <span class="key"><?=$attribute['attr_key'];?></span>
                                </div>
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Unit</label>
                                    <span class="unit"><?=$attribute['attr_unit'];?></span>
                                </div>
                                <div class="box_css col-md-1">
                                    <label class="lbl_row" style="display: none;">Order</label>
                                    <span class="order"><?=$attribute['attr_order'];?></span>
                                </div>
                                
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Description</label>
                                    <p class="content description"><?=$attribute['attr_description'];?></p>
                                </div>
                                <div class="box_css col-md-1">
                                    <label class="lbl_row" style="display: none;">&nbsp;</label>
                                    <a class="more_option" id="<?=$attribute['attr_id'];?>">More...</a>
                                    <a class="remove_line" id="<?=$attribute['attr_id'];?>" data-toggle="tooltip" title="Remove this attribute line"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    <? }} ?>
                </div>
                <div class="box_control">
                    <div class="col-md-12">
                        <a class="add_att attribute-add" href="#box_attribute">+ <?=$CMS->lang['title_add_attribute'];?></a>
                        <a class="add_att quick-add" onclick="openPopup('#search_attribute');" style="margin-left: 20px;">+ <?=$CMS->lang['btn_quick_add_attribute'];?></a>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <section class="add_cart_footer">
        <?=$CMS->global->footer_back(['list' => "{$CMS->vars['root_domain']}/?site=attribute"]) ?>
        <?=$CMS->input['act'] == 'add' || $CMS->input['act'] == 'add_do' ? $CMS->global->footer_save("{$CMS->input['site']}") : $CMS->global->footer_edit();?>
    </section>

</form>


<!-- POPUP ADD EDIT ATTRIBUTE -->
<div id="box_attribute" class="box_attribute white-popup-block mfp-hide col-md-6" style="float: none;">
    <div class="row">
        <form id="popup_attribute" name="popup_attribute" method="post" action="/acp/?site=attribute&subact=add_attr">
        <div class="row"><div class="col-md-12"><h3 class="title_form"><?=$CMS->lang['title_add_attribute'];?></h3></div></div>
        <div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label"><?=$CMS->lang['attr_name'];?> <span style="color:red">(*)</span></label>
                <div class="col-md-9">
                    <input class="form-control" type="text" maxlength="70" name="attr_name" value="" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['attr_name_err'];?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label"><?=$CMS->lang['attr_key'];?> <span style="color:red">(*)</span></label>
                <div class="col-md-9">
                    <input class="form-control" onkeypress="return nospace(event.which);" type="text" maxlength="70" name="attr_key" value="" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['attr_key_err'];?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label"><?=$CMS->lang['title_options_list'];?></label>
                <div class="col-md-9">
                    <div class="box_line_option">
                        <div class="line_option row">
                            <div class="col-md-1 col-xs-1">
                                <span class="number">1</span>
                            </div>
                            <div class="col-md-6 col-xs-5">
                                <input class="form-control line_input" name="options_name[]" type="text" placeholder="Options name " />
                                <input type="hidden" class="" value="" name="options_id[]">
                            </div>
                            <div class="col-md-3 col-xs-5">
                                <input class="form-control line_input" name="options_value[]" type="text" placeholder="Options value" />
                            </div>
                            <div class="col-md-2 col-xs-1">
                                <div class="list_control">
                                    <a class="option_add" data-toggle="tooltip" title="Add new line" onclick="addNewLine(this);"><i class="fa fa-plus-circle"></i></a>
                                    <a class="option_del" data-toggle="tooltip" title="Delete this option" id="" onclick="delLine(this);"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--textarea class="col-md-9 form-control" name="attr_value" rows="6"></textarea-->
                </div>
            </div>
        </div>

        <!--div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label"><?=$CMS->lang['attr_type'];?></label>
                <div class="col-md-9">
                    <select class="form-control" name="attr_type" defaultvalue="">
                        <option value=""><?=$CMS->lang['plz_choose'];?></option>
                        <option value="1" selected="selected">Select</option>
                        <option value="2">Input</option>
                    </select>
                </div>
            </div>
        </div-->
        <div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label"><?=$CMS->lang['attr_unit'];?></label>
                <div class="col-md-9">
                    <input class="form-control" type="text" maxlength="70" name="attr_unit" value="">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label"><?=$CMS->lang['attr_order'];?></label>
                <div class="col-md-9">
                    <input class="form-control" type="number" min="0" name="attr_order" value="">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label"><?=$CMS->lang['attr_description'];?></label>
                <div class="col-md-9">
                    <input class="form-control" type="text" name="attr_description" value="">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group" style="margin-top: 10px;">
                <label class="col-md-3 form-label">&nbsp;</label>
                <div class="checkbox checkbox-inline">
                    <input type="checkbox" id="check-required" name="attr_required" value="1">
                    <label for="check-required"><?=$CMS->lang['attr_required'];?></label>
                </div>  
                <div class="checkbox checkbox-inline">
                    <input type="checkbox" id="check-search" name="attr_search" value="1">
                    <label for="check-search"><?=$CMS->lang['attr_search'];?></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="p_error" style="display: none;"></p>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label class="col-md-3 form-label">&nbsp;</label>
                <div class="col-md-9">
                    <div class="box_control">
                        <a class="btn btn-primary pull-left add_attr" type="add"><?=$CMS->lang['btn_save_and_close'];?></a>
                        <a class="btn btn-primary pull-left add_attr" type="add_more" style="margin-left: 10px;"><?=$CMS->lang['btn_save_and_add'];?></a>
                        <a class="btn btn-default pull-left btn_cancel" style="margin-left: 10px;"><?=$CMS->lang['btn_close'];?></a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- POPUP LISTING OPTIONS ATTRIBUTE -->
<div id="box_list_options" class="box_list_options white-popup-block mfp-hide col-md-6" style="float: none;margin: 0 auto; background: #fff; clear: both; overflow: hidden; padding: 20px 30px;">
    <div class="row">
        <div class="row"><div class="col-md-12"><h3 class="headtitle"><?=$CMS->lang['title_options_list'];?></h3></div></div>
        <div class="bs-example" data-example-id="bordered-table" style="margin-bottom: 10px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Option Name</th>
                        <th>Option value</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="listOption"></tbody>
            </table>
        </div>
        <div class="form-group">
            <label class="col-md-3 form-label">&nbsp;</label>
            <div class="col-md-9">
                <a class="btn btn-default pull-right" onclick="$.magnificPopup.close();"><?=$CMS->lang['btn_close'];?></a>
            </div>
        </div>
    </div>   
</div>


<!-- POPUP QUICK ADD ATTRIBUTE -->
<div id="search_attribute" class="search_attribute white-popup-block mfp-hide col-md-6" style="float: none;margin: 0 auto; background: #fff; clear: both; overflow: hidden; padding: 20px 30px;">
    <div class="row"><h3 style="margin-top: 20px; text-align: center;"><?=$CMS->lang['title_search_attr'];?></h3></div>
    <div class="row">
        <div class="form-group" style="position: relative;">
            <label class="form-label"><?=$CMS->lang['title_search_attr_hint'];?></label>
            <input class="form-control" type="text" onkeyup="autoAttrSearch('[name=search_auto]', '/acp/?site=attribute&subact=search_attr');" name="search_auto" value="">
            <p class="show_error" style="display: none"></p>
            <div class="ui-widget" style="margin-top:1em; overflow-y: scroll;">
                <label class="form-label">List of selected attributes</label>
                <div id="listattr" style="height: 200px;" class="form-control"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <label class="col-md-3 form-label">&nbsp;</label>
            <div class="col-md-9">
                <a class="btn btn-default pull-right" onclick="closePopup();"><?=$CMS->lang['btn_close'];?></a>
            </div>
        </div>
    </div>

</div>

<!--Attribute list-->
<div id="open_list_attribute" class="open_list_attribute white-popup-block mfp-hide col-md-6" style="float: none;margin: 0 auto; background: #fff; clear: both; overflow: hidden; padding: 20px 30px;">
    <div class="row">
        <div class="row"><div class="col-md-12"><h3><?=$CMS->lang['title_attribute_list'];?></h3></div></div>
        <div class="bs-example" data-example-id="bordered-table" style="margin-bottom: 10px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Attribute Name</th>
                        <th>Attribute key</th>
                        <th>Attribute unit</th>
                        <th>Quantity of value</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="listAttribute"></tbody>
            </table>
        </div>
        <div class="form-group">
            <label class="col-md-3 form-label">&nbsp;</label>
            <div class="col-md-9">
                <a class="btn btn-default pull-right" onclick="$.magnificPopup.close();"><?=$CMS->lang['btn_close'];?></a>
            </div>
        </div>
    </div>   
</div>


<script language="javascript">
    $(document).ready(function () {
        validate_form_custom("#form_attribute", "a.act_submit_save, a.act_submit_save, a.act_submit_save, [type='submit']");
        $("input[name='group_status'][value='<?=$tpl->data['group_status'];?>']").prop("checked", true);
    });
</script>