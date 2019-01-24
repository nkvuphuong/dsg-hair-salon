$(document).ready(function(){
	var p_price = $("#formorenew_rder-signin_v1 #price").val();

	var p_cycle = $("#formorenew_rder-signin_v1 #cycle").val();
	var total = p_price * p_cycle;
    var total_show = formatNumberInput(total);  
    $("#formorenew_rder-signin_v1 #total_renew_price").html(total_show);


  
 	var curdate =  $("#formorenew_rder-signin_v1 #start_time").val();
 
  
  	var cycle_type =  $("#formorenew_rder-signin_v1 #cycle_type").val();
  	if(cycle_type == 2)
  	{
  		p_cycle = p_cycle * 12; 
  	}
 	var date = new Date(convert_format_datejs(curdate))
 	var a = date.setMonth(date.getMonth()+parseInt(p_cycle));
	var t = new Date(a).toISOString().slice(0, 10);;
 
 	$("#formorenew_rder-signin_v1 #end_time").val(convert_dateformatjs(t));


});

 $("#formorenew_rder-signin_v1 #price").on("keyup", function(){
 	caltotal_renew_order();
 });
 $("#formorenew_rder-signin_v1 #cycle").on("change", function(){
 	caltotal_renew_order();
 	var p_cycle = $("#formorenew_rder-signin_v1 #cycle").val();
 	var curdate =  $("#formorenew_rder-signin_v1 #start_time").val();
 	var cycle = $(this).val();
  
  	var cycle_type =  $("#formorenew_rder-signin_v1 #cycle_type").val();
  	if(cycle_type == 2)
  	{
  		cycle = cycle * 12; 
  	}
 	var date = new Date(convert_format_datejs(curdate))
 	var a = date.setMonth(date.getMonth()+parseInt(cycle));
	var t = new Date(a).toISOString().slice(0, 10);;
 
 	$("#formorenew_rder-signin_v1 #end_time").val(convert_dateformatjs(t));
 });

 function caltotal_renew_order()
 {
 	var p_price = $("#formorenew_rder-signin_v1 #price").val();
	var p_cycle = $("#formorenew_rder-signin_v1 #cycle").val();
	var total = p_price * p_cycle;
    var total_show = formatNumberInput(total);  
    $("#formorenew_rder-signin_v1 #total_renew_price").html(total_show);
 }

 function convert_dateformatjs(date_input)
 {
 	//Format input :2017-10-05 YYYY-MM-DD
 	var res = date_input.split("-");
 	if(dateFormat == "MM/DD/YYYY")
 	{
 		return res[1]+"/"+res[2]+"/"+res[0];
 	}
 	if(dateFormat == "YYYY/MM/DD")
 	{
 		return res[0]+"/"+res[1]+"/"+res[2];
 	}
 	if(dateFormat == "DD/MM/YYYY")
 	{
 		return res[2]+"/"+res[1]+"/"+res[0];
 	}
 }

 function convert_format_datejs(date_input)
 {
 	// Original format : '2009-07-16 00:00:00'
 	var res = date_input.split("/");
 	if(dateFormat == "MM/DD/YYYY")
 	{
 		return res[2]+"."+res[0]+"."+res[1];
 	}
 	if(dateFormat == "YYYY/MM/DD")
 	{
 		return res[0]+"."+res[1]+"."+res[2];
 	}
 	if(dateFormat == "DD/MM/YYYY")
 	{
 		return res[2]+"."+res[1]+"."+res[0];
 	}
 }
