<section class="p-product">
   <section class="ps-service">
      <div class="container">
          <div class="row section-title-wrapper" style="padding-top: 0">
              <div class="col-md-12 text-center">
                  <h1>Mỹ phẩm</h1>
              </div>
          </div>
         <div class="col-sm-3"><!-- Menu -->
             <?=\core\ezy::render('menu_full', 'product');?>    
             <?=\core\ezy::render('top_product', 'product');?>    
         </div>
          <div class="col-sm-9"> 
              <div class="top-list-product-subpage">
                <div class="row">
                  

                    <div class="col-sm-12">
                      <div class="top-list-product-subpage-left">
                         <h2 class="title-sub-category"><?=$tpl->title;?><span class="number"> (<?=count($tpl->dataListProduct);?> Sản Phẩm) </span></h2>
                      </div>
                      <div class="top-list-product-subpage-right">
                        <div class="clearfix">
                          <div class="view-style Foatright">
                            <a href="javascript:void(0)" class="btn-view btn-view-gird active"><i class="fa fa-th-large"></i></a>
                            <a href="javascript:void(0)" class="btn-view btn-view-list "><i class="fa fa-bars"></i></a>
                          </div>
       
                        </div>
                      </div>
                    </div>
                </div>
              </div>
    
              
              <div class="box-detail-list-product">
                <?=\core\ezy::render('product_data', 'product');?>    
              </div>   
          </div>       
      </div>
      
  </section>
 
</section>



 <script>

$('body').on('click', '.btn-view-list', function (e) {
        
        $(".list-item").addClass('view-list');
         $(".list-item-blogs").addClass('view-list');
        
        
    });
    $('body').on('click', '.btn-view-gird', function (e) {
        
        $(".list-item").removeClass('view-list');
        $(".list-item-blogs").removeClass('view-list');
        
        
    });
    $('body').on('click', '.view-style .btn-view', function (e) {
        
        $(".view-style .btn-view").removeClass('active');
         $(this).addClass('active');
        
        
    });
 </script>