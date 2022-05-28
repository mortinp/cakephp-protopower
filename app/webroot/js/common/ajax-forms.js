/*function ajaxifyForm(form, alias, fnAfterSuccess) {
    var doAjax = form.attr('onsubmit') != '' && form.attr('onsubmit') != null && form.attr('onsubmit') != undefined;// TODO: This is a hack
    if(doAjax == true) {
        var messageDiv = $('#' + alias + '-ajax-message');
        form.submit(function() {
            $.ajax({
                type: "POST",
                data: $(this).serialize(),
                url: $(this).attr('action'),
                success: function(response) {
                    var upperAlias = alias[0].toUpperCase() + alias.substring(1);
                    
                    messageDiv.empty().append($("<div class='alert alert-success'><b>" + upperAlias + "</b> data was saved successfully.</div>"));

                    var inputs = form.find('input');
                    var obj = {};
                    $.each(inputs, function(k, v){
                        elem = $(v);
                        if(elem.attr('id') == null) return;
                        entryName = elem.attr('id').replace(upperAlias, '').toLowerCase();
                        obj[entryName] = elem.val();
                    });
                    
                    if(fnAfterSuccess) fnAfterSuccess(obj);
                    setTimeout(function(){
                        messageDiv.empty();
                    }, 5000);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    messageDiv.append("<div class='alert alert-error'><b>" + alias[0].toUpperCase() + alias.substring(1) + "</b> data could not be saved.</div>");
                }
            });
        });
    }
}*/



function _ajaxifyForm(form, obj, alias, onSuccess) {
    if(obj != null) setupFormForEdit(form, obj, alias);
    
    var doAjax = form.attr('onsubmit') != '' && form.attr('onsubmit') != null && form.attr('onsubmit') != undefined;// TODO: This is a hack
    if(doAjax == true) {
        var messageDiv = $('#' + alias + '-ajax-message');
        form.submit(function() {
            var data = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "POST",
                data: $(this).serialize(),
                url: $(this).attr('action'),
                success: function(response) {
                    response = JSON.parse(response);
                    
                    var upperAlias = alias[0].toUpperCase() + alias.substring(1);
                    
                    messageDiv.empty().append($("<div class='alert alert-success'><b>" + upperAlias + "</b> data was saved successfully.</div>"));
                    setTimeout(function(){
                        messageDiv.empty();
                    }, 5000);

                    if(onSuccess) {
                        if(response != null && typeof response === 'object' && response.object != null) 
                            onSuccess(response.object);
                        else {
                            var inputs = form.find('input, textarea');
                            var obj = {};
                            $.each(inputs, function(k, v){
                                elem = $(v);
                                if(elem.attr('id') == null) return;
                                entryName = elem.attr('id').replace(upperAlias, '').toLowerCase();
                                obj[entryName] = elem.val();
                            });
                            onSuccess(obj);
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    messageDiv.append("<div class='alert alert-danger'><b>" + alias[0].toUpperCase() + alias.substring(1) + "</b> data could not be saved.</div>");
                }
            });
        });
    }
}


function setupFormForEdit(form, obj, alias) {
    if(obj.id == null) return; // TODO: throw exception???
    
    var upperAlias = capitalizarAlias(alias);
    for(k in obj) {
        var upperFieldName = capitalizarAlias(k);
        var input = form.find('#' + upperAlias + upperFieldName);
        input.val(obj[k]);
    }
    form.attr('action', form.attr('action').replace('/add', '/edit/' + obj.id));
}

function capitalizarAlias(alias) {
    return splitWith(alias, "");
}
    
function stringifyAlias(alias) {
    return splitWith(alias, " ");
}

function splitWith(alias, separator) {
    result = "";

    parts = alias.split("_");
    sep = "";
    for (p in parts) {
        result += sep + parts[p].substring(0, 1).toUpperCase() + parts[p].substring(1, parts[p].length);
        sep = separator;
    }

    return result;
}