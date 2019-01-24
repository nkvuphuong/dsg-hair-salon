function crawlingSEO(src, type="url")
{
    $('[crawling]').prop("disabled",true);

    $.ajax({
        type: "post",
        url: '?subact=crawling_seo',
        data: {'src': src, 'type': type},
        dataType: 'json',
        success: function(res){
            $('[crawling]').prop("disabled",false);
            if(res.status == 'ok')
            {
                $('[crawling]').val('');
                $.each(res.data,function(key, items){
                    $.each(items, function(key2, value){
                        $('[crawling="' + key + "." + key2 + '"]').val(value);
                        $('[crawling="' + key + "." + key2 + '"]').trigger("click");
                    })
                })
            }
            else
            {
                alert(res.msg);
            }
        }
    })
}