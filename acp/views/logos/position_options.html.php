<? foreach ($tpl->dataPositions as $pos) {?>
    <option value="<?=$pos['pos_id']?>" <?=isset($tpl->posSelected[$pos['pos_id']]) ?  $tpl->posSelected[$pos['pos_id']] : ''?>><?="{$pos['pos_name']} ({$pos['pos_width']}x{$pos['pos_height']})"?></option>
<?}?>