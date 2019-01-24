<?if( !empty($tpl->liststyleHair) ){?>
 	<option value="">-- Chọn mẫu tóc -- </div>
    <?foreach( $tpl->liststyleHair as $data ){?>
    <option value="<?=$data['cat_id'];?>"><?=$data['cat_name'];?></div>
<?} }?>
