<form enctype="multipart/form-data" class="" name="form_product_search" id="form_product_search" action="/product/search/" method="post">
<div class="box-left mb-15">
  <ul class="nav_title clearfix">
    <li class="active"><a>Tìm kiếm nhanh</a><a class="btn-toggle-cont-box-left" href="javascript:void(0)"><i class="demo-icon icon-minus-3"></i></a></li>
  </ul>
  <div class="content-box-left hidden-mobile mb-0">
    <div class="trademark-filter mb-0 pb-0">
      <div class="search-brand">
        <button type="submit" class="btn btn-default btn-search-style1 color-main"><i class="fa fa-search" aria-hidden="true"></i></button>
        <div class="form-group">
          <input type="text" name="keyword" class="form-control" value="<?=isset($tpl->keyword) ? $tpl->keyword : "";?>" placeholder="Tìm kiếm nhanh...">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="box-left">
  <ul class="nav_title clearfix">
    <li class="active"><a href="/">Chọn giá</a><a class="btn-toggle-cont-box-left" href="javascript:void(0)"><i class="demo-icon icon-minus-3"></i></a></li>
  </ul>
  <div class="content-box-left hidden-mobile">
    <div class="layout-slider" style="">
      <span class="bar-price" style="display: inline-block; padding: 0px;"><input type="slider" name="slider_price_from_to" value="<?=!empty($tpl->price_from) ? $tpl->price_from : "0";?>;<?=!empty($tpl->price_to) ? $tpl->price_to : "0";?>" /></span>
    </div>
    <input type="hidden" name="price_from" value="<?=!empty($tpl->price_from) ? $tpl->price_from : "0";?>" />
    <input type="hidden" name="price_to" value="<?=!empty($tpl->price_to) ? $tpl->price_to : "0";?>" />
    <button type="submit" class="btn btn-default btn-search-style2 color-main"><i class="fa fa-search" aria-hidden="true"></i></button>
  </div>
</div>
</form>

<script type="text/javascript">
    var link = "<?=isset($tpl->modLink)?$tpl->modLink:'';?>";
    if (window.history.replaceState && link) 
    {
      //prevents browser from storing history with each change:
      window.history.replaceState("", "Filter", link);
    }

</script>