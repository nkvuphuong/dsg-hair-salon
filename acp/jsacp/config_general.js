function addCommissionRating(defaultVal = null){

    if(defaultVal == null) {
        defaultVal = {
          "label" : "",
          "value" : 100,
        };
    }

    let label = typeof defaultVal.label == "undefined" ? "" : defaultVal.label;
    let val = typeof defaultVal.value == "undefined" ? "" : defaultVal.value;

    let tpl = `
       <div class="row commission_rating_item">
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label class="form-label semibold" for="commission_rating_label">Label</label>
                    <input type="text" class="form-control" id="commission_rating_label" name="commission_rating_label[]" value="${label}">
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label class="form-label semibold" for="commission_rating_value">Value (%)</label>
                    <input type="number" class="form-control" id="commission_rating_value" name="commission_rating_value[]" value="${val}" min="0" max="100">
                </fieldset>
            </div>
            <div class="col-lg-3">
                <label class="form-label semibold" for="commission_rating_percent">&nbsp;</label>
                <div class="btn btn-danger" onclick="removeCommissionRating($(this));"><i class="fa fa-minus-circle"></i></div>
            </div>
        </div>
    `;

    $(tpl).insertBefore($("#commission_rating_footer"));
}

function removeCommissionRating(obj) {
    obj.parents(".commission_rating_item:first").remove();
}