<section class="p-contact-us">
    <!-- tpl main -->
  <div class="container"> 
    <div class="row">
        <div class="col-sm-12 text-center">
            <h2 style="margin-bottom: 15px;">Hệ thống Salon Dũng Sài Gòn</h2>
        </div>  
    </div>
    <div class="row section-title-wrapper">
        <div class="visible-md visible-lg col-md-4 col-lg-3">	</div> 
        <div class="col-md-10 col-lg-8">
						
						<div class="row">
							<div class="col-sm-4 col-md-4">
								<div class="form-group">
									<select class="form-control input-with-right-icon" id="city_id">
										<?if( !empty($tpl->optionCity_by_salon) ){?>
										    <?foreach( $tpl->optionCity_by_salon as $data ){
                                                    if($data['city_id'] == "4167")
                                                    {
                                                        $selected = "selected";
                                                    }else{ $selected = ""; }
                                                ?>
										    <option value="<?=$data['city_id'];?>" <?=$selected;?> ><?=$data['city_name'];?></option>
										<?}  } ?>

									</select>
									<span class="fa fa-caret-down right-icon-input"></span>			
								</div>									
							</div>
							 
							<div class="col-sm-4 col-md-4">
								<div class="form-group">
								  
                      <select class="form-control input-with-right-icon" name="salon_select" value="" id="liststore">
                          <option>Hệ thống Salon</option>
                            
                      </select>
                      <span class="fa fa-caret-down right-icon-input"></span>
             
								</div>
							</div>		
						</div>		

			</div>
    </div>

    <div id="liststore_html">
      <?=\core\ezy::render("list_store","salon");?>
    </div>
 
    
</div>
</section>
<script type="text/javascript">
    
    $(document).ready(function(){
        var city_id = $("#city_id").val();
        get_storebycity(city_id);

    });
    $("#city_id").on('change',function(){
        var city_id  = $("#city_id").val();
        get_storebycity(city_id);
        get_liststorebycity(city_id);
    });
    function get_storebycity(id)
    {
        $("#liststore").html("");
        $.ajax({
          type: "post",
          url: "/salon/optionstore/typeshow-1/id-"+id,
          data: {},
          success: function(response)
          {
              $("#liststore").html(response);
          }
        });
    }
    function get_liststorebycity(id)
    {
        $("#liststore").html("");
        $.ajax({
          type: "post",
          url: "/salon/getstore/id-"+id,
          data: {},
          success: function(response)
          {
              $("#liststore_html").html(response);
          }
        });
    }
    $("#liststore").on("change",function(){

      var store_id = $(this).val();
      $('html, body').animate({scrollTop:$('#salon-'+store_id).position().top}, 'slow');
 
    });

      
</script>



 