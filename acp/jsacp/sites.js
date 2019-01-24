$('#code_storename').on('keyup', function() {
        var val = $(this).val();  
        $(this).val(storeName(val));
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


$("#code_storename, #site_domainname").on("focus",function(){

  //var id_input = $(this).attr("id");
  //$(".site_domainname").not("#"+id_input).val("");
 
});  

// $(document).ready(function(){
//   var theme = $("#site_theme").val();
//
//   $("#preview_theme").attr("src","/themes/"+theme+"/preview.jpg");
// });

$("#site_theme").change(function(){
   var theme = $(this).val();
   var theme_link =$('#site_theme option:selected').attr('theme_link');
 
   $("#preview_theme").attr("src","/themes/"+theme+"/preview.jpg");
   $("#preview_theme_link").html("");
   if(theme_link != "")
   {
     $("#preview_theme_link").html(theme_link);
   }
   
   // $("#preview_theme").attr("src",$(this).find("option:checked").attr("avatar"));
});

$("#site_theme").trigger("change");

$("#generate_secure_password").click(function(){

  var random_password = generate_secure_password();
  $("#hosting_password").val(random_password);
});

$("#generate_acp_password").click(function(){

  var random_password = generate_secure_password();
  $("#acp_password").val(random_password);
});

$("#generate_secure_username").click(function(){

  var random_username = generate_secure_username();
  $("#new_hosting_username").val(random_username);
});




function generate_secure_username()
{
  var specials = 'abcdefghijklmnopqrstuvwxyz';
  var lowercase = 'abcdefghijklmnopqrstuvwxyz';
  var numbers = '0123456789';
  var all = specials + lowercase   + numbers;
  var username = ( lowercase.pick(4) + specials.pick(1) + numbers.pick(2) + all.pick(7, 10)).shuffle();
  if (!isNaN(username.charAt(0)))
  {
    // Is a number
    username = "f"+username;
  }

  return username;
}



function generate_secure_password()
{
  var specials = '@';
  var lowercase = 'abcdefghijklmnopqrstuvwxyz';
  var uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  var numbers = '0123456789';
  var all = specials + lowercase + uppercase + numbers;
  var password = ( lowercase.pick(1) + specials.pick(1) + uppercase.pick(3) + all.pick(10, 12)).shuffle();
  return password;
}

String.prototype.pick = function(min, max) {
    var n, chars = '';

    if (typeof max === 'undefined') {
        n = min;
    } else {
        n = min + Math.floor(Math.random() * (max - min));
    }

    for (var i = 0; i < n; i++) {
        chars += this.charAt(Math.floor(Math.random() * this.length));
    }

    return chars;
};
String.prototype.shuffle = function() {
    var array = this.split('');
    var tmp, current, top = array.length;

    if (top) while (--top) {
        current = Math.floor(Math.random() * (top + 1));
        tmp = array[current];
        array[current] = array[top];
        array[top] = tmp;
    }

    return array.join('');
};

function autocomplete_quick_search()
{

    $("#p_quick_search").keyup(function(){

        if($(this).val().length < 2)
        {
            return false;
        }
        var key = $(this).val();
        $.ajax({
            type: "post",
            url: site_root_domain+"/?site=sites&subact=autocomplete",
            data:{ keyword : key },
            beforeSend: function(){
                $("#p_quick_search").css("background","#FFF url(/acp/images/fb-loading.gif) no-repeat 165px");
            },
            success: function(data){
                var obj = JSON.parse(data);
                $("#suggesstion-box").html("");
                if(obj.status == "success")
                {
                    output_li = "<ul class='list_goods'>"+obj.data_option+"</ul>"
                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(output_li);
                    $("#p_quick_search").css("background","#FFF");

                }
                else
                {
                    $("#suggesstion-box").hide();
                    $("#suggesstion-box").html("");
                    $("#p_quick_search").css("background","#FFF");
                }

            }
        });
    });

}

function loadThemesByIndustry(selectedTheme="0", themeObj=$("select#site_theme"), industryObj=$("select#industry")){
    let industryValue = industryObj.val();
    themeObj.prop("disabled",true).html("");

    $.ajax({
        url: site_root_domain + "/?site=sites&subact=load_theme_by_industry&theme_industry="+industryValue+"&site_theme="+selectedTheme,
        success: function(res){
            themeObj.prop("disabled", false).html(res);
        }
    })
}



$('#sv_id').on('change', function() {
    var hostname = $('option:selected', this).attr('hostname');
    $("#original_domain").html("." + hostname);
       
});

