<script type="text/javascript" src="<?=$CMS->vars['js_url'];?>/acp_product.js?07092014"></script>
<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['p_title']?></h3>
        <figure class="pull-right right">
            <div class="search">
                <form method="post" id="frm_quickserch_product"  action="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";?>&act=search" style="display:inline-block">

                    <input type="submit" class="fa-input" value="&#xf002;">
                    <input type="text" name="p_quick_search" autocomplete="off" minlength="2" maxlength="64" id="p_quick_search" p_type="<?=$CMS->input['p_type']?>" style="position: :relative;" placeholder="<?=$CMS->lang['gsearch_quick']?>" value="<?=$p_name_convert?>">
                    <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
                </form>
                <a id="expand_formsearch" title=""><?=$CMS->lang['gsearch_advance']?><i class="fa fa-angle-double-right"></i></a>
            </div>
            <?=$CMS->global->importExportData('product', "&export_type={$export_type}", 1);?>
            <a href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=add"?>" title="" class="add_bill"><?=$CMS->lang['p_add_button']?></a>
            <?if($tpl->popupForm){?>
                <button type="button" class="btn btn-info popup-with-form" href="#quickActionForm" onclick="$('#quickActionForm').attr({'act':'add_do','product_id':'0'}); "><?=$CMS->lang['p_quick_add_button']?></button>
            <?}?>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>
    </figure>
    <section class="search_adv" >
        <form method="post" id="formsearch_adv" style="display:none"  action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=search" >
            <figure class="box-typical box-typical box-typical-padding border">
                <h5><?=$CMS->lang['gsearch_advance']?></h5>
                <ul class="input_li row match-height">
                    <li class="col-xl-3 col-md-3 col-sm-6">
                        <p class="form-control-static">
                            <input   name="p_id_search" id="p_id_search" type="number" class="form-control" placeholder="<?=$CMS->lang['p_id']?>" value="<?=$tpl->p_id_search;?>" >
                        </p>
                    </li>
                    <li class="col-xl-3 col-md-3 col-sm-6">
                        <p class="form-control-static">
                            <input  name="p_name_search" id="p_name_search" type="text" class="form-control" placeholder="<?=$CMS->lang['p_name']?>" value="<?=$tpl->p_name_search;?>" >
                        </p>
                    </li>
                    <li class="col-xl-3 col-md-3 col-sm-6">
                        <p class="form-control-static">
                            <select class="form-control select2" name="p_group_search" id="p_group_search" >
                                <?=$tpl->option_p_group_search;?>
                            </select>
                        </p>
                    </li>
                    <li class="col-xl-3 col-md-3 col-sm-6">
                        <p class="form-control-static">
                            <select class="form-control select2" name="p_supplier_search" id="p_supplier_search" >
                                <?=$tpl->option_p_supplier?>
                            </select>
                        </p>
                    </li>
                    <li class="col-xl-3 col-md-5 col-sm-9">
                        <div class="form-control-static">
                            <input class="pull-left" type="submit" value="<?=$CMS->lang['comment_filter']?>" style="margin-right: 15px;">
                            &nbsp;&nbsp;&nbsp;
                            <input type="button" class="downloadBtnFilter btn_white pull-left" value="<?=$CMS->lang['export_data'];?>" style="margin-right: 15px;">
                        </div>
                    </li>
                </ul>
            </figure>
        </form>
    </section>

    <?if( $tpl->p_type_search == 1 ){?>
    <!-- Service -->
    <!-- Tabs head -->
    <section class="box-heading box-status box-filternav" style="padding-bottom: 0;margin-bottom: 0;">
        <div class="box-heading-body">
            <ul class="nav-filter clearfix">
                <li class="<?=$tpl->tab_active['service'];?>">
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=service"><?=$tpl->totalProduct*1;?><span><?=$CMS->lang['list_service'];?></span></a>
                </li>
                <li class="<?=$tpl->tab_active['product_group'];?>">
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=product_group&pg_type=1"><?=$tpl->totalGroup*1;?><span><?=$CMS->lang['list_category_service'];?></span></a>
                </li>
                <?if($CMS->vars['enabled_commission']){?>
                <li class="<?=$tpl->tab_active['commission'];?>">
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=service&commission=1"><?=$tpl->totalCommission*1;?><span><?=$CMS->lang['gcommission'];?></span></a>
                </li>
                <?}?>
            </ul>
        </div>
    </section>

    <!-- Tabs bottom -->
    <section class="filter_custom_status" style="clear: both;padding: 10px 0; background: #fff; overflow: hidden; border: 1px solid #d8e2e7; border-top: none;">
        <form method="get" name="filterproductservice" id="filterproductservice" action="<?=$CMS->vars['root_domain']?>/?site=service">
            <input name="site" value="service" type="hidden">
            <input name="listgroup" value="<?=urldecode($CMS->input['listgroup']);?>" type="hidden">
            <div class="clearfix">
                <?
                if( is_array($tpl->listCountProductOfGroup) )
                {
                    foreach ($tpl->listCountProductOfGroup as $dataCount ) 
                    {
                ?>
                <div class="checkbox checkbox-inline v1">
                    <input id="check-<?=$dataCount['product_group_id'];?>" checkedgroup="" class="findgroup" value="<?=$dataCount['product_group_id'];?>" type="checkbox">
                    <label for="check-<?=$dataCount['product_group_id'];?>">
                        <span class="nav-link-in nav-link-in-custom">
                            <span class="title"><?=$dataCount['product_group_name'];?></span>
                            <span class="label label-pill label-primary <?=$dataCount['cnt'] ? '':'gray';?>"><?=$dataCount['cnt']*1;?></span>
                        </span>
                    </label>
                </div>
                <?}}?>
            </div>
        </form>
        
    </section>
    
    <?}else{?>
    <!-- product -->
    <!-- Tabs head -->
    <section class="box-heading box-status box-filternav" style="padding-bottom: 0;margin-bottom: 0;">
        <div class="box-heading-body">
            <ul class="nav-filter clearfix">
                <li class="<?=$tpl->tab_active['product'];?>">
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=product"><?=$tpl->totalProduct*1;?><span><?=$CMS->lang['list_product'];?></span></a>
                </li>
                <li class="<?=$tpl->tab_active['product_group'];?>">
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=product_group&pg_type=0"><?=$tpl->totalGroup*1;?><span><?=$CMS->lang['list_category_product'];?></span></a>
                </li>
                <?if($CMS->vars['enabled_commission']){?>
                <li class="<?=$tpl->tab_active['commission'];?>">
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=product&commission=1"><?=$tpl->totalCommission*1;?><span><?=$CMS->lang['gcommission'];?></span></a>
                </li>
                <?}?>
            </ul>
        </div>
    </section>

    <!-- Tabs bottom -->
    <section class="filter_custom_status" style="clear: both;padding: 10px 0; background: #fff; overflow: hidden; border: 1px solid #d8e2e7; border-top: none;">
        <form method="get" name="filterproductservice" id="filterproductservice" action="<?=$CMS->vars['root_domain']?>/?site=product">
            <input name="site" value="product" type="hidden">
            <input name="listgroup" value="<?=urldecode($CMS->input['listgroup']);?>" type="hidden">
            <div class="clearfix">
                <?
                if( is_array($tpl->listCountProductOfGroup) )
                {
                    foreach ($tpl->listCountProductOfGroup as $dataCount ) 
                    {
                ?>
                <div class="checkbox checkbox-inline v1">
                    <input id="check-<?=$dataCount['product_group_id'];?>" checkedgroup="" class="findgroup" value="<?=$dataCount['product_group_id'];?>" type="checkbox">
                    <label for="check-<?=$dataCount['product_group_id'];?>">
                        <span class="nav-link-in nav-link-in-custom">
                            <span class="title"><?=$dataCount['product_group_name'];?></span>
                            <span class="label label-pill label-primary <?=$dataCount['cnt'] ? '':'gray';?>"><?=$dataCount['cnt']*1;?></span>
                        </span>
                    </label>
                </div>
                <?}}?>
            </div>
        </form>
        
    </section>
    <?}?>

    <script>
        $(document).ready(function(){
            var listgroup = $('[name="listgroup"]').val();
            var activegroup = listgroup ? listgroup.split(",") : [];

            for( var x in activegroup ) 
            {
                if( activegroup[x] )
                {
                    $('[id="check-'+activegroup[x]+'"]').prop("checked", true).attr("checkedgroup", 1);
                }
            }

            $(".findgroup").click(function(){
                var idgroup = $(this).val();

                if( $(this).attr("checkedgroup") == 0)
                {
                    $(this).attr("checkedgroup", 1);
                    listgroup += ","+idgroup;
                }
                else
                {
                    $(this).attr("checkedgroup", 0);
                    
                    var Reg = new RegExp(","+idgroup, "g");
                    listgroup = listgroup.replace(Reg, "");
                }
                $("input[name='listgroup']").val(listgroup).attr('value', listgroup);

                setTimeout(function(){
                    $('form[name="filterproductservice"]').submit();
                }, 1000);
            });
        });
    </script>

    <!-- main -->
    <div id="reloadProductListing" style="margin-top: 15px !important;"><?=$tpl->main_form;?></div>
    <style type="text/css">.table-responsive { overflow-x: initial; }</style>
</section>
<?=$CMS->global->print_barcode_popup();?>
<?=$CMS->global->preview_barcode_popup();?>

<!-- Modal -->
<div id="quickActionForm" act="add_do" module="<?=$CMS->input['site']?>" class="white-popup-block mfp-hide" style="max-width: 80%; margin: 20px auto; ">
    <?=$tpl->popupForm;?>
</div>
<script>
    $("#quickActionForm #langTab").attr("style","");
    $('.popup-with-form').magnificPopup({
        type: 'inline',
        midClick: true,
        callbacks: {
            open: function() {
                $("#quickActionForm .btn-save-close").text(cms_lang.btn_add_and_close);
                $("#quickActionForm .btn-save-continue").text(cms_lang.btn_add_and_continue);
                clearProductQuickAction();
            }
        }
    });

    let form = $("#quickActionForm form#form-signin_v1");
    let buttons = `
            <button type="submit" class="btn btn-primary btn-save-continue" onclick="$('#quickActionForm').attr('subact','continue');"></button>
            <button type="submit" class="btn btn-primary btn-save-close" onclick="$('#quickActionForm').attr('subact','close');"></button>
            <button type="button" class="btn btn-secondary" onclick="$.magnificPopup.close();">${cms_lang.close}</button>
        `;
    $("#quickActionForm").find(".heading, .add_cart_footer").remove();
    $(buttons).appendTo(form.find(".main_form:first .order-tab-content"));

    $(document).ready(function(){
        validate_form_custom(form,"[type='submit']","product_quick_action");
    })
</script>

<script src="<?=$CMS->vars['js_acp']?>/product.js?22022014"></script>
<script language="javascript">arrange_setup("<?=$CMS->product->arrange_data?>");</script>
