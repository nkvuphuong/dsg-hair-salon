<?php 
if(count($tpl->storefronts) > 0)
{
    foreach ($tpl->storefronts as $key => $data) {  
?>
        <option value="salon-<?=$data['store_id'];?>" href="/salon/salon-<?=$data['store_id'];?>"><?=$data['store_name'];?></option>
<? } } ?>