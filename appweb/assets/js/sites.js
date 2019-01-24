$(document).ready(function(){
   var option = $("#site_theme").find(':selected');
   var theme =  option.attr('theme') ;

  $("#preview_theme").attr("src","/themes/"+theme+"/preview.jpg");

  if(packet_id == 0)
  {
    $('input[name=cycle]').each(function(){
      $('input[name=cycle]:first').attr('checked', true);

    });
  }
	
	cal_total();
});

$("#site_theme").change(function(){
   var option = $(this).find(':selected');
   var theme =  option.attr('theme') ;
 
   $("#preview_theme").attr("src","/themes/"+theme+"/preview.jpg");
});


function cal_total()
{
	var elem =  $('input[name=cycle]:checked');
	var val_cycle = elem.val();
	var val_price = elem.attr("price");
	var total = 0;
	total = val_cycle * val_price;
	var val_packet_id = elem.attr("packet_id");
	$('input[name=packet_id]').val(val_packet_id);
	$("#out_total").html(formatNumberInput(total));
	$("#out_month").html(val_cycle);
}

$('input[name=cycle]').on("change",function(){
	$(this).attr('checked', true);
	cal_total();
});


 function formatNumberInput(number, dec_number) 
  {
    //Check input num is NaN
    if(isNaN(number)) {
      number = 0;
    }

    //dec_number: Phần thập phân
    dec_number = !isNaN(dec_number) || typeof(dec_number) !== "undefined" ? parseInt(dec_number) : 0;
    if(currency_type == "$")
    {

     return "$ "+number.toFixed(2).replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        });
    }
    else
    {
       return number.toFixed(dec_number).replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        })  + " đ "; 
    }
  }

$('input[name=code_storename]').on('keyup', function() {
        var val = $(this).val();  
        
         if ($("input[name=private_domain]").prop("checked")) {
             $(this).val(storeName_2(val));
            $("#reg_domain").html(storeName_2(val));
         }else
         {
              $(this).val(storeName(val));
              $("#reg_domain").html(storeName(val)+"."+storename_domain);
         }
      
});

function  storeName(v) {
      v = v.toLowerCase();
      v = v.replace(/a|Ă¡|Ă |áº£|Ă£|áº¡|Äƒ|áº¯|áº±|áº³|áºµ|áº·|Ă¢|áº¥|áº§|áº©|áº«|áº­/g, "a");
      v = v.replace(/o|Ă³|Ă²|á»|Ăµ|á»|Ă´|á»‘|á»“|á»•|á»—|á»™|Æ¡|á»›|á»|á»Ÿ|á»¡|á»£/g, "o");
      v = v.replace(/e|Ă©|Ă¨|áº»|áº½|áº¹|Ăª|áº¿|á»|á»ƒ|á»…|á»‡/g, "e");
      v = v.replace(/i|Ă­|Ă¬|á»‰|Ä©|á»‹/g, "i");
      v = v.replace(/u|Ăº|Ă¹|á»§|Å©|á»¥|Æ°|á»©|á»«|á»­|á»¯|á»±/g, "u");
      v = v.replace(/Ä‘|Ä/g, "d");
      v = v.replace(/y|Ă½|á»³|á»·|á»¹|á»µ/g, "y");
      v = v.replace(/ /g, "");
      v = v.replace(/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\_|\+|\=|\[|\{|\]|\}|\\|\||\;|\:|\'|\"|\,|\<|\.|\>|\/|\?/g, "");
      v = v.replace(/[^\x00-\x7F]/g, "");
      return v;
  }

function  storeName_2(v) {
      v = v.toLowerCase();
      v = v.replace(/a|Ă¡|Ă |áº£|Ă£|áº¡|Äƒ|áº¯|áº±|áº³|áºµ|áº·|Ă¢|áº¥|áº§|áº©|áº«|áº­/g, "a");
      v = v.replace(/o|Ă³|Ă²|á»|Ăµ|á»|Ă´|á»‘|á»“|á»•|á»—|á»™|Æ¡|á»›|á»|á»Ÿ|á»¡|á»£/g, "o");
      v = v.replace(/e|Ă©|Ă¨|áº»|áº½|áº¹|Ăª|áº¿|á»|á»ƒ|á»…|á»‡/g, "e");
      v = v.replace(/i|Ă­|Ă¬|á»‰|Ä©|á»‹/g, "i");
      v = v.replace(/u|Ăº|Ă¹|á»§|Å©|á»¥|Æ°|á»©|á»«|á»­|á»¯|á»±/g, "u");
      v = v.replace(/Ä‘|Ä/g, "d");
      v = v.replace(/y|Ă½|á»³|á»·|á»¹|á»µ/g, "y");
      v = v.replace(/ /g, "");
      v = v.replace(/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\\|\||\;|\:|\'|\"|\,|\<|\>|\/|\?/g, "");
      v = v.replace(/[^\x00-\x7F]/g, "");
      return v;
  }
$(".checkbox-item").on("change",function(){

   $("input[name=code_storename]").val("");
   if ($("input[name=private_domain]").prop("checked")) {
   		$(".fa-suffix").hide();
   }else
   {
   	 $(".fa-suffix").show();
   }
 

});




