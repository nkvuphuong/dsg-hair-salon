<? if($tpl->stores) {
    foreach ($tpl->stores as $store) { ?>
        <option <?= \lib\input::arrayValue($tpl->data, 'price_stores') && in_array($store['store_id'] ,\lib\input::arrayValue($tpl->data, 'price_stores')) ? 'selected' : '' ?> value="<?=$store['store_id']?>"><?=$store['city_name']?> - <?=$store['store_name']?></option>
    <? }
}?>