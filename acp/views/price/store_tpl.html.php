<? if($tpl->store_tpl_data) { ?>
    <? foreach ($tpl->store_tpl_data as $store) {
        ?>
        <span class="label label-primary"><?=$store['store_name']?></span>
        <?
    } ?>
<? } ?>