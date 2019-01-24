<?for($i=1; $i>=0; $i--){?>
    <option <?= \lib\input::arrayValue($tpl->data, 'price_status') == $i ? 'selected' : '' ?> <?= \lib\input::arrayValue($tpl->status_selected, $i); ?> value="<?=$i?>"><?= \lib\input::lang('price_status_'.$i); ?></option>
<?}?>