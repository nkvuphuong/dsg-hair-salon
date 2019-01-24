<form id="formorenew_rder-signin_v1" name="formorenew_rder-signin_v1" action="<?=$CMS->vars['root_domain'];?>/?site=order&act=rating_do&id=<?=$tpl->order['ord_id'];?>" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$CMS->lang['rate'];?></h3>
            <a href="<?=$CMS->vars['root_domain'];?>/?site=order&act=show&id=<?=$tpl->data['ord_id']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical box-typical-padding border">
            <ul>
                <?
                foreach($tpl->rating as $key => $rate){
                    $rate = \models\rating::convertValue($rate);
                    $checked = $rate['data_bk']['rating_value'] == $tpl->order['ord_commission_rating'] ? "checked" : "";
                ?>
                    <li>
                        <label>
                            <input <?=$checked?> type="radio" name="commission_rating" value="<?=$rate['rating_value']?>">
                            <?=$rate['rating_label']?>
                        </label>
                    </li>
                <?}?>
            </ul>
        </figure>
    </section>


    <section class="add_cart_footer">
        <a href="<?=$CMS->vars['root_domain'];?>/?site=order&act=show&id=<?=$tpl->data['ordi']['ord_id']?>" class="pull-left cancel" title=""><?=$CMS->lang['back'];?></a>

        <button class="act_submit_save btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs" value="confirm_paid"><span class="ladda-label"><?=$CMS->lang['rate'];?></span><span class="ladda-spinner"></span><span class="ladda-spinner"></span></button>
    </section>
</form>
<script>
    if($(":radio[name='commission_rating']:checked").length == 0) {
        $(":radio[name='commission_rating']:first").prop("checked", true);
    }
</script>