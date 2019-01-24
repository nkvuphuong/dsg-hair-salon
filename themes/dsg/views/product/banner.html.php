<div class="title-main bottom clearfix">
  <div class="container">
    <div class="pull-left"><h1>Sản phẩm</h1></div>
    <div class="pull-right">
      <ol class="breadcrumb">
        <li><a href="/">Trang Chủ</a></li>
        <?if( \core\ezy::$act == "group" ){?>
        <li class="active"><?=$tpl->title;?></li>
        <?}else if( \core\ezy::$act == "detail" ){?>
        <li><a href="<?=$tpl->data['group_url'];?>" title="<?=$tpl->data['group_name'];?>"><?=$tpl->data['group_name'];?></a></li>
        <li class="active"><?=$tpl->data['name'];?></li>
        <?}else{?>
        <li class="active">Sản phẩm</li>
        <?}?>
      </ol>
    </div>
  </div>
</div>