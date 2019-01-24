$(document).ready(function(){
    //Check constraint when remove industry from theme
    $("#theme_industry.select2").on("select2:unselecting", function(e){
        let _this = $(this);
        let data = e.params.args.data;
        let industry_id = data.id;
        let theme_id = $("#module_id").val();
        let _return = false;

        blockLoading($('#industry-form-group'));

        $.ajax({
            url: site_root_domain + "?site=themes&subact=check_remove_industry",
            dataType: 'json',
            async: false,
            data: {
                industry_id: industry_id,
                theme_id: theme_id,
            },
            success: function(res){
                unblockLoading($('#industry-form-group'));
                if(res.status == 'ok') {
                    _return = true;
                }
                else {
                    pNotifyACP(res.msg);
                    _return = false;
                }
            },
            error: function(res){
                unblockLoading($('#industry-form-group'));
                pNotifyACP("Error!");
                _return = false;
            }
        });

        return _return;
    })
})