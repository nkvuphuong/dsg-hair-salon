<div class="form-group row">
  <label class="form-control-label"><?=$CMS->lang['hair_category'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
    <select class="form-control auto_select" name="cat_cate" defaultvalue="<?=$data['cat_cate'];?>">
      <option value="0"><?=$CMS->lang['hair_category_1'];?></option>
      <option value="1"><?=$CMS->lang['hair_category_0'];?></option>
    </select>
  </div>
</div>