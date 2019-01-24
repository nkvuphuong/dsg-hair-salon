function commissionMergeInputs(){
    let data = {};

    let inputs = $("[commission_name]");

    $.each(inputs, function(k, item){
        let _name = $(item).attr("commission_name");
        let _id = $(item).attr("commission_id");
        let _val = $(item).val();
        if(typeof data[_name] == "undefined") {
            data[_name] = {};
        }
        data[_name][_id] = _val;
    })

    let myJsonString = JSON.stringify(data);
    $(":hidden#commission_data").val(myJsonString);
}