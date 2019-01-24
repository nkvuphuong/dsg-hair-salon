 <?if( !empty($tpl->optionCity_by_salon) ){?>
    <?foreach( $tpl->optionCity_by_salon as $data ){
            if($data['city_id'] == "4167")
            {
                $selected = "selected";
            }else{ $selected = ""; }
        ?>
    <option value="<?=$data['city_id'];?>" <?=$selected;?> ><?=$data['city_name'];?></option>
<?}  } ?>
