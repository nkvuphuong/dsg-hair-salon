

			function submitlogos(obj, event)
			{
				var my_form_id        = $(obj).attr("id"); //ID of an element for response output
				var option_upload_method = $('#'+my_form_id + ' input[name=upload_option]:checked').val();
			 
			 	if(option_upload_method == 1)
			    {   
			      var p_design = [];
			      var style_push = "";
			      var style_p_push = "";
			      var angle = "";
			      $( "#printable .t" ).each(function( index ) {
			                  
			          var style_t = $(this).attr("style");
			          var style_p = $(this).find("p").attr("style"); 
			          style_push = style_t + "|||" + style_push;
			          style_p_push = style_p + "|||" + style_p_push;
			          $( this ).find("p").css("border","1px");
			          $(this).find(".icon-remove").css("visibility", "hidden");
			          $(this).find(".icon-edit").css("visibility", "hidden");
			          $(this).find(".icon-rotation").css("visibility", "hidden");
			          $(this).find(".ui-resizable-handle").css("visibility", "hidden");
			       
			          var style_transform_bk =  $(this).attr("style");
			          var res = style_transform_bk.split(";");

			          var arrayLength = res.length;
			          for (var i = 0; i < arrayLength; i++) {
			             var res_2 = res[i].split(":");
			             if(res_2[0] == " transform")
			             {
			                var angle = res_2[1]; 
			             }
			              //Do something
			          }

			           $(this).find("p").css("transform",angle);
			           $(this).css("transform","");
			         

			       });
			        var element = $("#printable"); // global variable
			       html2canvas(element, {
	                     onrendered: function (canvas) {
	                      var getCanvas=  canvas;
	         
	                        $("#style_push").val(style_push);
			      			$("#style_p_push").val(style_p_push); 
			      		 	$("#list_image").val( canvas.toDataURL() ); 
			      		 	return false;
	                      } 
	                });
			    }
 }

 


$("#form_logos input[name=upload_option]").on("change",function(){

	var option = $(this).attr("value");
	if(option == 0 )
	{
		$("#option_upload_image_0").show();
		$("#option_upload_image_1").hide();
	}
	else
	{
		$("#option_upload_image_1").show();
		$("#option_upload_image_0").hide();
	}

});
