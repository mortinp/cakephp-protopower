/*var projectTemplate;
$(document).ready(function() {
    
    // Setup handlebars
    Handlebars.registerHelper('stringify', function() {
        return new Handlebars.SafeString(
        JSON.stringify(this)
    );
    });
    projectTemplate = Handlebars.compile($("#project-template").html());
    
    // Fill projects table
    $.each(window.app.projects, function(k, v) {
        $('#table-projects tbody').append(projectTemplate(v));
        _setupActions(v.id);        
    });
    
    // Create filpping form for every .create-new-project element
    var front = document.getElementById('flipthis')
    , back_content = window.app.html_project_form
    , back;
    $('.create-new-project').click(function() {
        back = flippant.flip(front, back_content, 'modal', 'flippant-modal-light');
        $('#btn-cancel-project').click(function() {
            back = back.close();
        });
        _ajaxifyForm($("#ProjectIndexForm"), null, "project", function(pro) {
            back = back.close();
            $('#table-projects tbody').append(projectTemplate(pro));
            
            _setupActions(pro.id);
            
        });
    });
});

function _setupActions(id) {
    var projectRow = $('#project-row-' + id);
    
    _setupEdit(projectRow);
    _setupDelete(projectRow);
}

function _setupEdit(projectRow) {
    projectRow.find('.edit-project').click(function() {
        var obj = $(this).data('obj');
        
        var front = document.getElementById('flipthis')
        , back_content = window.app.html_project_form
        , back;
        back = flippant.flip(front, back_content, 'modal', 'flippant-modal-light');
        $('#btn-cancel-project').click(function() {
            back = back.close();
        });
        
        _ajaxifyForm($("#ProjectIndexForm"), obj, "project", function(pro) {
            back = back.close();
            
            // Replace row
            $("#project-row-" + pro.id).replaceWith(projectTemplate(pro))
            
            _setupActions(pro.id);            
        });        
    });
}

function _setupDelete(projectRow) {
    projectRow.find('.delete-project').click(function() {
        if (confirm('Are you sure you want to delete this project?')) {
            var obj = $(this).data('obj');
        
            $.ajax({
                type: "POST",
                url: '/cakephp-protopower/projects/remove/' + obj.id,
                block:'actions-' + obj.id,
                success: function(response) {
                    projectRow.remove();
                },
                error: function(jqXHR, textStatus, errorThrown) {

                }
            });
        }
    });
}*/



$(document).ready(function() {
    
    $('.delete-button').click(function() {
        var _this = $(this);
        bootbox.dialog({
            title: "Confirm Project Deletion: <span class='text-danger'>Beware!!!</span>",
            
            message: "<div class='alert alert-warning'><big>This action is not reversible</big></div>"
                + "<p>Deleting this project will <span class='text-danger'>also delete everything inside it</span>:</p>"
                + "<ul style='list-style-type:none'>"
                    + "<li><i class='glyphicon glyphicon-folder-open' style='margin-left:-20px'></i> <big><b>" + _this.data('project-powersource-count') + "</b></big> power sources</li>"
                    + "<li><i class='glyphicon glyphicon-stats' style='margin-left:-20px'></i> <big><b>" + _this.data('project-analisi-count') + "</b></big> analises</li>"
                + "</ul>",
            
            buttons: {
                confirm: {
                    label: "Delete",
                    className: "btn-danger",
                    callback: function() {
                        window.location = '/cakephp-protopower/projects/remove/' + _this.data('project-id');
                    }
                },
                
                cancel: {
                    label: "Cancel",
                    className: "btn",
                    callback: function() {
                        
                    }
                }
            }
        });
        
        
        
        /*bootbox.confirm("Are you sure you want to delete this project?", function(result) {
            if(result){
                window.location = '/cakephp-protopower/projects/remove/' + _this.data('project-id');
            }
        }); */
    });
    
});