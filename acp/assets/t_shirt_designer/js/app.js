$(document).ready(function(){
 
  var src_tshirt = $("#Tshirtsrc").attr("src");
  if(src_tshirt == "")
  {
    $(".designtext1").css("display","none");
  }
  
    $("#printable .text").draggable({ containment: "#printable"});
    
   
    $("#rotation").draggable({ 
             opacity: 0.01, 
              helper: 'clone',
            drag: function(event, ui){
             
                var rotateCSS = 'rotate(' + ui.position.left + 'deg)';

                $(this).parent().css({
                    '-moz-transform': rotateCSS,
                    '-webkit-transform': rotateCSS
                });
          }      
    });

   $(".text").find('p').resizable();

    // Update text on Tshirt -- applly event on keyup
    $('#designtext').keyup(function(){
        var text = $(this).val().replace(/\r\n|\r|\n/g,"<br />");
        $('.text p').html(text);

          $('#printable .text').find('p').resizable( "destroy" );
          $('#printable .text').find('p').resizable();

      //$('#printable .text').find('p').resizable();

    });

    // ON click on the new text button : clone Next text on T-shirt and add new textarea to edit text
    //var count = 2; // variable to count Texts
   
     var count =  $( "textarea[name='designtext[]']" ).size();
    $('.nexText').click(function(){
       var count =  $( "textarea[name='designtext[]']" ).size(); 
       count += 1;
       var src_img = $("#Tshirtsrc").attr("src");
       if(src_img == "")
        { 
            return false; 
        }
  
                //var count = 1;
        // clone text area and change class attribute , data-id, id and value
        $('#designtext').clone().prependTo("#texts").attr('class', 'form-control designtext span12 designtext' + count).attr('data-id', count).attr('id', ' ').val('text ' + count);
        // clone text on T-shirt  and make draggable
        $('.text').clone().prependTo(".designContainer").attr('class', ' t text' + count).attr('data-id', count).attr('style', 'z-index:9;' + count).css('top', Math.random()*100).find('p').text('text ' + count);
        $('#printable .text'+count).draggable({ containment: "#printable" })
         $('#printable .text'+count).find('p').resizable();
         $('#printable .text'+count).find('.icon-rotation').attr("id","rotation"+count);
         $('#printable .text'+count).find('.icon-rotation').draggable({ 
             opacity: 0.01, 
              helper: 'clone',
            drag: function(event, ui){
                
                var rotateCSS = 'rotate(' + ui.position.left + 'deg)';

                $(this).parent().css({
                    '-moz-transform': rotateCSS,
                    '-webkit-transform': rotateCSS
                });
          }      
       }); 
        count++; // increment variable when new text cloned
    });
 
     // update texts on keyup event - this works on cloned texts and textarea
     $( document ).on('keyup', '.designtext', function(){

        // get text from text area and replace breakline with br tag
        var text = $(this).val().replace(/\r\n|\r|\n/g,"<br />");
        //get the data-id from text
        var id = $(this).data('id');
        //update text on T-shirt
        $('.text' + id + " p").html(text);
       $('#printable .text'+ id).find('p').resizable( "destroy" );
          $('#printable .text'+ id).find('p').resizable();
    });

    // initial Current Text element to be edited
    var textElement = $('.designtext1 p');
     // events 

     // make texts draggable using jquery UI
    $(function() {
        //$( ".t" ).resizable();
        $( ".t" ).draggable({
            // on stop make current text the element to be edited
            stop: function() {
                 textElement = $(this).find('p');
              }
        });
        
    });
    
    // on click on the text make current text the element to be edited (font size, color, font familly )
    $(document).on('click', '.t p', function(){
        textElement =  $(this);
        //add some annimations 'bounce' using CSS3 and animate.css file
         $('.slider , .pick-a-color-markup, .dropup').addClass('animated bounce');

         setTimeout(function() {
                //remove annimation after 1s
                 $('.slider , .pick-a-color-markup, .dropup').removeClass('animated bounce');
            }, 1000);
    });

    // Actions to be apllayed on Texts
    $(document).on('click', '.action', function(){
        //get what action to use
        var action = $(this).data('action');
        // set the current element to be edited
        var currentEl = $(this);
        // find text element wich is 'P'
        textElement = $(this).parent().find('p');
        // test Action if Rmove
        if(action == 'remove'){
            // test if this is the original text (the text we clone) - if yes we can delete it , because we use it to add new texts
            if(textElement.parent().hasClass('no-delete')){
                //we can delete it , because we use it to add new texts
                alert("this is the orginal text you can't delete it");
                // stop event
                return false;
            }
            // if no - we users should confirm action (delete) 
            if(confirm('Please confirm?')){
                // action confirmed - now get the data-id of the parrent element (div class='text') ti use it to remove textaprea
                var inputId =  currentEl.parent().data('id');
                // remove input (textarea)
                $(".designtext" + inputId).remove();
                // remove text on T-shirt
                currentEl.parent().remove();
            }
           // if action is Edit
        }else{
            //add annmation on options available for this element (font size, font, color)
            $('.slider , .pick-a-color-markup, .dropup').addClass('animated bounce');
            // delete annimation after 2s
            setTimeout(function() {
                 $('.slider , .pick-a-color-markup, .dropup').removeClass('animated bounce');
            }, 2000);
        }
    });

 

    //font size using Slider based on jquery UI sliders
     $( "#slider" ).slider({
        range: "max", // set range Type
        min: 1, // set a minimum value
        max: 100, //a max value
        value: 11, // default value
        slide: function( event, ui ) { // event onslider
            $( ".size" ).text(ui.value + "px"); // update text on slider
            if(textElement != null){ // if text element is not null
            textElement.css( "font-size", ui.value); // apply value on text (font-size) using css function (jquery)
        }
            }
        });
        $( ".size" ).text($( "#slider" ).slider( "value" ) +   "px"); // get default value from slider and show it to the user

 

        $( "#slider_hshadow" ).slider({
        range: "max", // set range Type
        min: -10, // set a minimum value
        max: 10, //a max value
        step:1,
        value: 0, // default value
        slide: function( event, ui ) { // event onslider
            if(textElement != null){ // if text element is not null
                  $( ".size_hshadow" ).text(ui.value +   "px");  
                  text_shadow();
                }
            }
        });
        
        $( "#slider_vshadow" ).slider({
        range: "max", // set range Type
        min: -10, // set a minimum value
        max: 10, //a max value
        step:1,
        value: 0, // default value
        slide: function( event, ui ) {  
            if(textElement != null){ 
                    $( ".size_vshadow" ).text(ui.value +   "px");  
                      text_shadow();
                }
            }
        });
      
    $( "#slider_blurshadow" ).slider({
        range: "max", // set range Type
        min: -10, // set a minimum value
        max: 10, //a max value
        step:1,
        value: 0, // default value
        slide: function( event, ui ) {  
            if(textElement != null){  
                    $( ".size_blurshadow" ).text(ui.value +   "px");  
                    text_shadow();
        }
            }
        });
        $( ".size_blurshadow" ).text($( "#slider_blurshadow" ).slider( "value" ) +   "px"); // get default value from slider and show it to the user



        function text_shadow()
        {

            var size_hshadow = $(".size_hshadow").html();

            var size_vshadow = $(".size_vshadow").html();
            var size_blurshadow = $(".size_blurshadow").html();
            var shadow_color = $("input#shadow_color").val();

             if(textElement != null){
                textElement.css( "text-shadow", size_hshadow+" "+size_vshadow+" "+size_blurshadow +" #"+ shadow_color); 
            }

        }


        //Edit text's font - Get selected font on event Click
        //google.load('webfont','1');
        $('#font a').click(function(){
            // test if current text is not null ()
            if (textElement != null) {
                // get font name from clicked element (data-font='font name')
                var font = $(this).data('font');
                // apply a loading style
                $('.designContainer').prepend("<div class='loading'></div>");

                //google font loader API config
                WebFontConfig = {
                    // get selected font
                    google: { families: [ font ],
                    text: 'abcdedfghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890?’“!”(%)[#]{@}/&<-+÷×=>®©$€£¥¢:;,.*!' },
                        //loading: function() {}, // function when loading font
                        //active: function() {},// function when font is active
                        //inactive: function() {}, // function if font is inactive
                        //fontloading: function(familyName, fvd) {alert('fontloading')}, // font loading
                        fontactive: function(familyName, fvd) { // function if font is action and we get fontName in args
                            // we apply font on the slelected text using css function (Jquery)
                            textElement.css('font-family', familyName);
                            // remove loading annimation
                            $('.designContainer').find(".loading").remove();
                        },
                        fontinactive: function(familyName, fvd) { // if font is innactive 
                            // show alert ti the user
                            alert('this font no longer available, please choose another one from list');
                            // remove loading annimation
                            $('.designContainer').find(".loading").remove();
                        },
                    // timeout  if google load font take a longer time
                    timeout: 5000
                        
                }
                    // load google font API script
                    var wf = document.createElement('script');
                    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                    '://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js';
                    wf.type = 'text/javascript';
                    wf.async = 'true';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(wf, s);
            };
        }); 
        
        // function to get image preview on the t-shirt we don't need to upload it on the server using this function
        var countImg = 1;
    function readURL(input) {
        if (input.files && input.files[0]) { // if there is a file from input
            var reader = new FileReader(); // read file
            
            reader.onload = function (e) { // on load
                // add image to imagesContainer - e.target.result : image's source on local
                 $('#imagesContainer').prepend("<div class='images' style='z-index:9" + countImg + "'><i class='icon-remove text-error'></i><img src='" + e.target.result + "' alt='' ></div>");
                 // make images draggable and resizable using jquery UI functions
                 $('#imagesContainer').find('img').resizable();
                 $('#imagesContainer').find('.images').draggable();

                countImg ++;
                //$('#blah').css('background', 'transparent url('+e.target.result +') left top no-repeat');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    // load images 
    $("#imgInp").on('change',function(){
        readURL(this); // call our function readURL
    });

    // delete pictures
    $(document).on('click', '.images .icon-remove', function(){
        // user should confirm suppression
        if(confirm('Please confirm?')){
            // if confirmed get parent and delete image
            $(this).parent().remove();
        }
    })

    // Text Color picker
    $(".pick-a-color").pickAColor({
        showSpectrum            : true,
        showSavedColors         : true,
        saveColorsPerElement    : true,
        fadeMenuToggle          : true,
        showAdvanced            : true,
        showHexInput            : true,
        showBasicColors         : true
    });

    // event on color change ( get selected color)
    $("input#color").on("change", function () {
        // get value from input
        var color  = $(this).val();  
        // if textElement is not null
        if(textElement != null){
            //apply css on the text
            textElement.css('color','#' + color);
        }
        
    });

     // event on color change ( get selected color)
    $("input#shadow_color").on("change", function () {
        // get value from input
        var border_color  = $(this).val();    
        // if textElement is not null
        if(textElement != null){
            text_shadow();
        }
        
    });
     

    //change T-shirt
    $('.tshirts a').click(function(){
        //get clicked T-shirt src
        var src = $(this).find('img').attr('src');
        //apply it on the original image to be edited
        $('#Tshirtsrc').attr('src', src);

        return false;
    });

    // apply style on file's input

    // $('#imgInp').customFileInput({
    //     // put button 'browse' on right
    //     button_position : 'right'
    // });

    // Preview option (Modal)

    $('#myModal').on('shown', function () {
        //clone current design to Modal (show preview)
        $('.designContainer').clone().prependTo('.modal-body').find('i').remove();
        $('.modal-body').find('.ui-icon').css('display', 'none');
    });

    $('#myModal').on('hidden', function () {
        //initialize modal preview on hidden event
      $('.modal-body').html(' ');
    })


    // Printer call

    // Hook up the print link.
    $('a.print').on('click', function() {
        //Print span6(design container) with default options
        $.print(".span6");
    });

    // export as DESIGN
   
   $('.export').click(function(){
        //hide options
        $('#printable').find('i').css('display', 'none');
        $('#printable').find('.ui-icon').css('display', 'none');
        //get printable section
         var exportCanvas = document.getElementById('printable');
         //get convas container
         var canvasContainer = document.getElementById('convascontent');
            //export canvas to convascontainer
            html2canvas(exportCanvas, {
                //when finished fucntion
            onrendered: function(canvas) {
                // initialize canvas container (if we generate another canvas)
                $('#convascontent').html(' ');
                // append canvas to container
                canvasContainer.appendChild(canvas);
                //add id attribute to the canvas
                $('#convascontent').find('canvas').attr('id','mycanvas');
                // display options again
                $('#printable').find('i').css('display', 'block');
                $('#printable').find('.ui-icon').css('display', 'block');
                //document.getElementsByTagName("UL")

            }
        });
   // return false;
   });

   //export options
   $('.exportas').click(function(){
        // get type to export
        var to = $(this).data('type');
       // alert(to);
       // get our canvas
       var oCanvas = document.getElementById("mycanvas");  
    // support variable
    var bRes = false;
        if(to == 'png'){
            // export to png 
            bRes = Canvas2Image.saveAsPNG(oCanvas);
        }
        if(to == 'jpg'){
            // maybe in some old browsers it works only on Firefox
            bRes = Canvas2Image.saveAsJPEG(oCanvas);
        }if(to == 'bmp'){
            Res = Canvas2Image.saveAsBMP(oCanvas);
        }
        // if browser doesn't support mimetype alert user
        if (!bRes) {
            alert("Sorry, this browser is not capable of saving " + strType + " files!");
            return false;
        }
   });
    
 $('#helloss').click(function(){
        // get type to export
        var to = 'png';
       // alert(to);
       // get our canvas
       var oCanvas = document.getElementById("printable");  
    // support variable
    var bRes = false;
        if(to == 'png'){
            // export to png 
            bRes = Canvas2Image.saveAsPNG(oCanvas);
        }
        if(to == 'jpg'){
            // maybe in some old browsers it works only on Firefox
            bRes = Canvas2Image.saveAsJPEG(oCanvas);
        }if(to == 'bmp'){
            Res = Canvas2Image.saveAsBMP(oCanvas);
        }
        // if browser doesn't support mimetype alert user
        if (!bRes) {
            alert("Sorry, this browser is not capable of saving " + strType + " files!");
            return false;
        }
   });
    
/**
 *    Ken Fyrstenberg Nilsen
 *    Abidas Software
*/


/**
 * Demonstrates how to download a canvas an image with a single
 * direct click on a link.
 */
function doCanvas() {
    var canvas = document.getElementById('printable');
    ctx = canvas.getContext('2d');
    /* draw something */
    ctx.fillStyle = '#f90';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#fff';
    ctx.font = '60px sans-serif';
    ctx.fillText('Code Project', 10, canvas.height / 2 - 15);
    ctx.font = '26px sans-serif';
    ctx.fillText('Click link below to save this as image', 15, canvas.height / 2 + 35);
}

/**
 * This is the function that will take care of image extracting and
 * setting proper filename for the download.
 * IMPORTANT: Call it from within a onclick event.
*/
function downloadCanvas(link, canvasId, filename) {
    link.href = document.getElementById(canvasId).toDataURL();
    link.download = filename;
}

/** 
 * The event handler for the link's onclick event. We give THIS as a
 * parameter (=the link element), ID of the canvas and a filename.
*/

//*
var element = $("#printable"); // global variable
var getCanvas; // global variable
//*
$("#hello").on('click', function () {

    html2canvas(element, {
         onrendered: function (canvas) {
               // $("#mirror").append(canvas);
              var getCanvas=  canvas;
 
               var imgageData = getCanvas.toDataURL("image/png");
    // Now browser starts downloading it instead of just showing it
    var newData = imgageData.replace(/^data:image\/png/, "data:application/octet-stream");
    $("#hello").attr("download", "your_pic_name.png").attr("href", newData);
             }
    });


   
});
//*



//     var cnvs = document.getElementById('printable'),
//         ctx = cnvs.getContext('2d'),
//         mirror = document.getElementById('mirror');


//     cnvs.width = mirror.width = window.innerWidth;
//     cnvs.height = mirror.height = window.innerHeight;

//     mirror.addEventListener('contextmenu', function (e) { });
//     mirror.addEventListener('contextmenu', function (e) {
//     var dataURL = canvas.toDataURL('image/png');
//     mirror.src = dataURL;
// });

//     var button = document.getElementById('hello');
//         button.addEventListener('click', function (e) {
//                var oCanvas = document.getElementById("printable");  
//            // var dataURL = oCanvas.toDataURL('image/png');
//            // button.href = dataURL;
//            bRes = Canvas2Image.saveAsPNG(oCanvas);
//         });

});









