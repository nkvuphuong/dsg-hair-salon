<?for($i=1; $i>=0; $i--){?>
    <option <?= \lib\input::arrayValue($tpl->status_selected, $i); ?> value="<?=$i?>"><?= $CMS->lang['rating_status_'.$i]; ?></option>
<?}?>