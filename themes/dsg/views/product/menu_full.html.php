<?if( !empty($tpl->dataListProductGroup) ){?>
<div class="box-left box">
  <div class="title_style">
    <h3><i class="fa fa-bars" aria-hidden="true"></i>Danh mục sản phẩm</h3>
  </div>
  <nav class="bs-docs-sidebar side-bar-left border">
    <ul class="nav bs-docs-sidenav">
      <input type="hidden" name="input_group_id" value="<?=isset(\core\ezy::$input['group'])?\core\ezy::$input['group']:'';?>"/>
      <?foreach($tpl->dataListProductGroup as $group){?>
      <li class="border-bottom">
        <a href="<?=$group['url_none_html'];?>" data-group-id="<?=$group['id']?>" title="<?=$group['name'];?>"><span class=""><i class="fa fa-caret-right" aria-hidden="true"></i></span>&nbsp;<?=$group['name'];?></a>
        <?if( !empty($group['ListSubGroup']) ){?>
        <a href="javascript:void(0)" class="btn-hide-toggle-sub-menu sub-menu-control" data-group-expand="<?=$group['id']?>"><span class="caret"></span></a>
        <ul class="sub-menu hide-toggler-cont">
          <?foreach( $group['ListSubGroup'] as $subgroup ){?>
          <li class="border-bottom">
            <a href="<?=$subgroup['url'];?>" title="<?=$subgroup['name']?>" data-group-id="<?=$subgroup['id']?>" data-parent-group-id="<?=$group['id']?>" class="item-sub-menu"><?=$subgroup['name']?></a>
          </li>
          <?}?>
        </ul>
        <?}?>
      </li>
      <?}?>
    </ul>
  </nav>
</div>
<?}?> 