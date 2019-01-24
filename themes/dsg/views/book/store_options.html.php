<?if($tpl->storeCnt>1){?>
    <div class="col-md-6">
        <div class="form-group">
            <label itemprop="name" class="form-control-label">Storefront</label>
            <div class="form-control-wrapper">
                <select name="choose_store" class="form-control">
                    <?foreach ($tpl->dataStories as $storefront){?>
                   <option value="<?=$storefront['store_id']?>"><?=$storefront['store_name']?></option>
                    <?}?>
                </select>
            </div>
        </div>
    </div>
<?}  else {?>
    <input type="hidden" name="choose_store" value="<?=$tpl->dataStories[0]['store_id']?>">
<?}?>