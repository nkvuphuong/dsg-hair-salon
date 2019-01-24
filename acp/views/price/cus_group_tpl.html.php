<? if($tpl->cus_group_tpl_data) { ?>
    <? foreach ($tpl->cus_group_tpl_data as $group) {
        ?>
        <span class="label label-primary"><?=$group['gc_name']?></span>
        <?
    } ?>
<? } ?>