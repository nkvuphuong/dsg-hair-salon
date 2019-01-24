<!-- Slider sub page -->
<div class="banner_subpage slider_sub_page">
  <div id="owl_view_1" class="owl-carousel noNav">
    <!-- slider sub page -->
    <?if( ! empty($tpl->banner['slider_sub_page']) ){foreach($tpl->banner['slider_sub_page'] as $data ){?>
    <div class="item">
      <a href="<?= ( $data['logo_link'] ) ? $data['logo_link'] : '/'; ?>" title="<?=$data['logo_name'];?>"><img src="<?=$data['logo_src'];?>" alt="<?=$data['logo_src_alt'];?>"></a>
    </div>
    <?}}?>
  </div>
</div><!-- End slider sub page -->