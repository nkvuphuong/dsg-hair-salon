<?for($i=1; $i>=0; $i--){?>
    <option <?= $tpl->status_selected[$i]; ?> value="<?=$i?>"><?= $CMS->lang['rating_status_'.$i]; ?></option>
<?}?>