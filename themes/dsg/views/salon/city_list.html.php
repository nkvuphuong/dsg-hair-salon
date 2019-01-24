<?if( !empty($tpl->optionCity_by_salon) ){?>
    <?foreach( $tpl->optionCity_by_salon as $data ){?>
    <option value="<?=$data['city_id'];?>"><?=$data['city_name'];?></div>
<?} }?>
