<? foreach ($tpl->modes as $mode) {?>
    <option value="<?=$mode?>" <?=isset($tpl->modeSelected[$mode]) ?  $tpl->modeSelected[$mode] : ''?>><?="{$CMS->lang['pos_mode_'.$mode]}"?></option>
<?}?>