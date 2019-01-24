<? if($tpl->group_customers) {
    foreach ($tpl->group_customers as $group_customer) { ?>
        <option <?= \lib\input::arrayValue($tpl->data, 'price_cus_groups') && in_array($group_customer['gc_id'] ,\lib\input::arrayValue($tpl->data, 'price_cus_groups')) ? 'selected' : '' ?> value="<?=$group_customer['gc_id']?>"><?=$group_customer['gc_name']?></option>
    <? }
}?>