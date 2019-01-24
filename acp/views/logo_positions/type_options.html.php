<? foreach ($tpl->types as $type) {?>
    <option value="<?=$type?>" <?=isset($tpl->typeSelected[$type]) ?  $tpl->typeSelected[$type] : ''?>><?="{$CMS->lang['pos_type_'.$type]}"?></option>
<?}?>