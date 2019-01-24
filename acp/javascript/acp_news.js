function select2Tags(defaultValue = [], module="news")
{
    let tagSelector = $("#tagSelector").select2({
        tags: true,
        tokenSeparators: [',',';'],
        ajax: {
            url: "?site=tags&subact=load_autocomplete",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    module: module,
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data,
                    /*pagination: {
                     more: (params.page * 30) < data.total_count
                     }*/
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });

    try {
        defaultValue = $.parseJSON(defaultValue);
        if(defaultValue.length)
        {
            $.each(defaultValue, (k, v) => {
                if(!tagSelector.find('option:contains(' + v + ')').length)
                    tagSelector.append($('<option>').text(v));
            });
            tagSelector.val(defaultValue).trigger("change");
        }
    }
    catch (e) {
        return false;
    }
}

function formatRepo (repo) {
    if (repo.loading) return repo.text;
    var markup = `
        <div class='select2-result-repository clearfix'>
            <div class='select2-result-repository__title'>${repo.text}</div>
        </div>
    `;

    return markup;
}

function formatRepoSelection (repo) {
    return repo.text;
}
