 <option value="">Chọn Salon</option>
<?if( !empty($tpl->list_store ) ){?>
    <?foreach( $tpl->list_store as $key => $value ){  
    	if($tpl->typeshow == 0 )
    	{
    		$href = 'href="/salon/salon-'.$value['store_id'].'"';
    	}else { $href = "";}
    	?>
        <option <?=$tpl->selected_store == $value['store_id'] ? "selected" : ""?> slug="<?=$CMS->class->seo->cleanurl($value['store_name'])?>" value="<?=$value['store_id'];?>" <?=$href;?> ><?=$value['store_name'];?></option>
<?} }?>
