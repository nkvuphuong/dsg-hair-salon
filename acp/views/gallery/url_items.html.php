<?if(count($tpl->data['url_image'])){?>
    <div class="url_items">
<?
    foreach($tpl->data['url_image'] as $key => $url_img){
?>
            <div class="url_item row">
                <div class="col-md-9">
                    <input autocomplete="off" value="<?=$url_img?>" class="form-control" type="text" name="url_image[]" value="<?=$data['url_image'];?>">
                </div>
                <div class="col-md-3">
                    <button <?=$key ? '' : 'disabled="disabled"';?> onclick="removeImgURL($(this));" class="btn btn-danger remove_img_url"><i class="fa fa-minus-circle" aria-hidden="true"></i></button>
                </div>
            </div>

        <?
    }?>
    </div>
<?
} else { ?>
    <div class="url_items">
        <div class="url_item row">
            <div class="col-md-9">
                <input autocomplete="off" class="form-control" type="text" name="url_image[]" value="<?=$data['url_image'];?>">
            </div>
            <div class="col-md-3">
                <button disabled="disabled" onclick="removeImgURL($(this));" class="btn btn-danger remove_img_url"><i class="fa fa-minus-circle" aria-hidden="true"></i></button>
            </div>
        </div>
    </div>
<? } ?>

<button class="btn" onclick="addImgURL();"><i class="fa fa-plus" aria-hidden="true"></i></button>