<?php 
if(count($tpl->dataListKHtieubieu) > 0)
{
    foreach ($tpl->dataListKHtieubieu as $key => $data) {  
        $data['pathUpload'] = "customer/".$data['cus_image'];
?>
<div class="col-sm-6 col-md-3">
    <div class="customer-div" style="background-image: url('<?=\lib\input::getThumb( $data['pathUpload'], 555);?>')">
        <div class="bottom-text-wrapper">
            <div class="bottom-text-inner">
                <p class="bottom-text"><?=$data['cus_note'];?></p>
                <p class="bottom-text-bold"><a href="<?=$data['url'];?>" ><?=$data['cus_full_name'];?></a></p>
            </div>
        </div>
    </div>
</div>
<? } } ?>