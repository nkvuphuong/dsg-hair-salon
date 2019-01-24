//
$(document).ready(function(){

	var option_upload_image = $("input[name=option_upload_image]:checked").val();
 	if(option_upload_image == 0)
	{
		$("#option_upload_image_0").show();$("#option_upload_image_1").hide();
	}
	else
	{
		$("#option_upload_image_1").show();$("#option_upload_image_0").hide();
	}
});

$("input[name=option_upload_image]").click(function(){

	var option_upload_image = $(this).val();
	if(option_upload_image == 0)
	{
		$("#option_upload_image_0").show();$("#option_upload_image_1").hide();
	}
	else
	{
		$("#option_upload_image_1").show();$("#option_upload_image_0").hide();
	}
});

$(".method-coupon").click(function(){

	var mtgc = $(this).attr("mtgc");
	if(mtgc == 1)
	{
		$("#library_giftcard").hide();
	}
	else
	{
		$("#library_giftcard").show();
	}
});

 

function embed_gc(src)
{
   var stbase64;
  toDataUrl_2(src, function(myBase64) {

  	$('#Tshirtsrc').attr('src',myBase64);
	  $(".designtext1").show();
	  $('#library_giftcard').hide();
	  $('#src_image_library').val(myBase64);
	 
  });
  
}
function toDataUrl_2(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
        var reader = new FileReader();
        reader.onloadend = function() {
            callback(reader.result);
        }
        reader.readAsDataURL(xhr.response);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
}
