<div class="container"> 
    <div class="row">
        <div class="col-sm-12 text-center">
            <h2 style="margin-bottom: 15px;">Mẫu tóc đẹp</h2>
        </div>
    </div>
    <div class="row section-title-wrapper"> 
            <div class="visible-md visible-lg col-md-1 col-lg-3">    </div>  
                <div class="col-md-10 col-lg-8">
                    <?=\core\ezy::render('gallery_filter', 'gallery');?>
                </div>
            <div class="visible-md visible-lg col-md-1 col-lg-2">   </div>  
    </div>
    <?=\core\ezy::render('gallery_data', 'gallery');?>  
    <div class="row section-title-wrapper" style="padding-top: 0;padding-bottom: 40px">
        <div class="col-md-12 text-center ">
            <a href="/dat-lich-hen" class="btn btn-md btn-primary">đặt lịch hẹn</a> 
        </div>
    </div>
</div>


<script type="text/javascript">
    

    // $("#style_hair").on("change",function(){
    //     var v_style_hair = $(this).val();
    //     if(v_style_hair != "")
    //     {
    //         window.location = "/gallery/list/"+v_style_hair;  
    //     }
    // });
</script>