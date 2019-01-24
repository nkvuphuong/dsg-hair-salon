$(document).ready(function(){

   var val = $("#code").val();
  $(".storetxt").html(storeName(val));
});


$('#code').on('keyup', function() {
        var val = $(this).val();
        $(".storetxt").html(storeName(val));
        $(this).val(storeName(val));
});

$('#name').on('keyup', function() {
        var val = $(this).val();
      
        $(this).val(storeName(val));
});


$("#reg-step-1").validate({
          rules: {
            // simple rule, converted to {required:true}
            // compound rule
            fullname: { required: true },
            phone: { required: true },
            storename: { required: true },
            code: { required: true },
            name: { required: true },
            pass: { required: true,     minlength: 6 },
            passagain: { required: true , minlength: 6,  equalTo: "#pass"},

          },
           messages: {
            fullname: {
              required: "Vui lòng nhập họ tên!",
             
            },
             phone: {
              required: "Vui lòng nhập số điện thoại!",
             
            },
             storename: {
              required: "Vui lòng nhập tên cửa hàng!",
             
            },
             code: {
              required: "Vui lòng nhập địa chỉ gian hàng!",
             
            },
             name: {
              required: "Vui lòng nhập tên đăng nhập!",
             
            },
             pass: {
              required: "Vui lòng nhập mật khẩu!",
              minlength: "Mật khẩu tối thiểu 6 ký tự"

             
            },
            passagain: {
                 required: "Vui lòng nhập lại mật khẩu!",
                 minlength: "Mật khẩu tối thiểu 6 ký tự",
                    equalTo : "Mật khẩu không trùng khớp"
     
            }

          }
  });

 

$("#reg-step-2").validate({
          rules: {
            // simple rule, converted to {required:true}
            // compound rule
            location: { required: true },
            email: { email: true },
            

          },
           messages: {
            location: {
              required: "Vui lòng chọn tỉnh thành!",
             
            },
             email: {
              email: "Email không đúng định dạng!",
             
            }

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
        return v;
    }


 
 $(".popup_trial").click(function(){

  var package_id = $(this).attr("package-id");
  ///register/buy-ezybook/step-1/id-1
  
  $("#contai-url-buy").attr("href","/register/buy-ezybook/step-1/id-"+package_id);
  $("#contai-url-trial").attr("href","/register/trial-ezybook/step-1/id-"+package_id);

   $.magnificPopup.open({
        type: 'inline',
        items: {
          src: '#box_popup_trial'
        },
      });

 });   


 $("#choice_cycle").on("change",function(){

    var cycle = $(this).val();
    var price = $("#package_price").val();
    var total = cycle * price;
    $("#total").html(formatNumberInput(total)+" đ");
 });


 function formatNumberInput(number, dec_number) 
  {
    //dec_number: Phần thập phân
    dec_number = !isNaN(dec_number) || typeof(dec_number) !== "undefined" ? parseInt(dec_number) : 0;
    return number.toFixed(dec_number).replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        });
  }
