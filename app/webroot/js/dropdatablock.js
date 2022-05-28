function DatablockManager() {
    if(window.app != undefined && window.app != null) {
        this.currentPowerSource = window.app.current_power_source; // Received from CakePHP
		
        this.init();
    }
}

DatablockManager.prototype = {
    init: function() {
        _this = this;
	
        Dropzone.autoDiscover = false;
        this.datablocks = new DLL.DoublyLinkedList();
        this.nextId = 0;
	
        // TODO: Detect all dropzones
        var existingDatablocks = /*$('.dropzone')*/this.currentPowerSource.datablocks;
		
        // Create dropzones
        $.each(existingDatablocks, function(key, value) {
            var code = value.code;
            var dz = new DropDatablock(value.id, code, $.proxy(_this._firstFileAdded, _this), $.proxy(_this._lastFileRemoved, _this));
            _this.datablocks.append(dz);
            dz.loadFiles();
        });
		
        if(existingDatablocks.length != 0)
            this.nextId = Number(existingDatablocks[existingDatablocks.length - 1].code.slice(2)) + 1;// Assumes existingDatablocks are ordered by id
		
        this.datablocks.append(new DropDatablock(null, 'db' + this.nextId, $.proxy(this._firstFileAdded, this), $.proxy(this._lastFileRemoved, this)));
    },

    _firstFileAdded: function(datablock) {
        // Append new node
        this.nextId++;
        this.datablocks.append(new DropDatablock(null, 'db' + this.nextId, $.proxy(this._firstFileAdded, this), $.proxy(this._lastFileRemoved, this)));
    //alert('_firstFileAdded: id=>' + datablock.id + ', index=>' + datablock.index);
    },
	
    _lastFileRemoved: function(datablock) {
        // TODO: Remove datablock from server
		
        // Remove datablock from DOM
        $('#' + datablock.code).parent().remove();
		
        // Remove from model
        this.datablocks.remove(datablock.index);
    }
}


function DropDatablock(id, code, firstFileAdded, lastFileRemoved) {
    var _this = this;
	
    this._places = {
        container: '#datablocks-innerframe'
    };
	
    this.id = id;
    this.code = code;
    this.hasFiles = false;
    this.firstFileAdded = firstFileAdded;
    this.lastFileRemoved = lastFileRemoved;
    this.filesCount = 0;
	
    // Create dropzone
    $(this._places.container).append(this._getMarkup());
    Dropzone.options[code] = this._bind();
    this.dropzone = new Dropzone('#' + code, {
        url: '/cakephp-protopower/datablocks/upload_file', 
        /*addRemoveLinks: true,*/ 
        maxFiles: 7,
        maxFilesize:2
    });
}

DropDatablock.prototype = {
    equals: function(code) {
        if(this.code == code) return true;
        return false;
    },

    _getMarkup: function() {
        var idInput = (this.id != null)? "<input type='hidden' name='datablock_id' value='" + this.id + "' />":"";
        return "<div class='datablock'>" +
                    "<form class='dropzone' id='" + this.code + "'>" +
                        "<input type='hidden' name='project_id' value='" + window.app.project.id + "' />" +
                        "<input type='hidden' name='power_source_id' value='" + window.app.current_power_source.id +"' />" +
                        "<input type='hidden' name='datablock_code' value='" + this.code + "' />" +
                        idInput +
                    "</form>" + 
                "</div>";
    },
	
    setIndex: function(index) {
        this.index = index;
        var dzform = $('#' + this.code);
        dzform.find("input[name='datablock_index']").remove();
        dzform.append("<input type='hidden' name='datablock_index' value='" + index + "' />");
    },

    _bind: function() {
        var _this = this;
        return {
            init: function() {				
                this.on("success", function(file, response) {
                    response = JSON.parse(response);
                    if(response.file_id != null && response.file_id != undefined) {
                        file.id = response.file_id;
                        file.tag = response.file_tag;
                        file.fileType = response.file_type;
                        file.info = response.file_info;
                        if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {// File finished uploading, and there aren't any left in the queue.
                            if(_this.filesCount == 0) _this.firstFileAdded(_this);
                            this.hasFiles = true;
                        }
						
                        _this.filesCount++;
						
                        _this._initSuccessFile(file);						
                    } //else error
                });
				
                this.on("error", function(file, responseText) {
                    var seconds = 10;
                    $(file.previewTemplate).append("<div style='text-align:center'>This file will be removed <br/> within " + seconds + " seconds.</div>");
                    //file.previewTemplate.appendChild(document.createTextNode('This file will be removed <br/> within ' + seconds + ' seconds.'));
                    setTimeout(function(){
                        _this.dropzone.removeFile(file);
                    }, seconds * 1000);
                });
				
                this.on("removedfile", function(file) {
                    /*$.ajax({
                        type: "POST",
                        data: $('#' + _this.id).serialize(),
                        url: '/cakephp-protopower/datablocks/remove_file/' + file.id,
                        success: function(response) {
                                //alert(response);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                                alert(jqXHR.responseText);
                                console.log(jqXHR.responseText);
                        }
                    });*/
				
                    if(/*this.getAcceptedFiles().length < 1*/_this.filesCount == 1) _this.lastFileRemoved(_this);
                    _this.filesCount--;
                });
            }
        };
    },
	
    loadFiles: function() {// Load files already on the server
        var _this = this;
		
        var files = window.app.current_power_source.datablocks[this.index].files;
        $.each(files, function(key,value){
            var mockFile = { 
                id: value.id, 
                name: value.name, 
                type: 'file',
                size: value.byte_size,
                status: Dropzone.SUCCESS,
                fileType: value.type,
                tag: value.label,
                info: value.label
            };	

            _this.dropzone.addMockFile(mockFile);
            //_this.dropzone._finished([_this.dropzone.files[key]]);
			
            //_this.dropzone.emit('addedfile', mockFile);
            //_this.dropzone.options.thumbnail.call(_this, mockFile, "uploads/"+value.name);
			
            _this._initSuccessFile(_this.dropzone.files[key]);
			
            _this.dropzone.options.maxFiles--;
            _this.filesCount++;
        });
    },
	
    _initSuccessFile: function(dzFile) {
        var _this = this;
		
        // Add remove button
        var removeButton = Dropzone.createElement("<div style='clear:both;text-decoration: none;'><button class='btn btn-primary btn-delete-file' style='width:100%'><i class='glyphicon glyphicon-trash'></i> Delete</button></div>");
        removeButton.addEventListener("click", function(e) {
            // Make sure the button click doesn't submit the form:
            e.preventDefault();
            e.stopPropagation();
            
            if (confirm('Are you sure you want to delete this file?')) {
                $.ajax({
                    type: "POST",
                    data: $('#' + _this.code).serialize(),
                    url: '/cakephp-protopower/datablocks/remove_file/' + dzFile.id,
                    success: function(response) {
                        _this.dropzone.removeFile(dzFile);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        console.log(jqXHR.responseText);
                    }
                });
            }            
			
        });
        dzFile.previewElement.appendChild(removeButton);	
	
        dzFile.previewTemplate.appendChild(document.createTextNode(dzFile.info));
        $(dzFile.previewElement)
            .wrap("<a href='/cakephp-protopower/analisis/analyse/" +  window.app.project.id + "/" + window.app.current_power_source.id + "/" + _this.code + "/" + dzFile.tag + "'></a>")
            .attr('title', 'Click this file to analyse it'); // Tooltip
        //dzFile.previewElement.addEventListener("click", function() { alert("File " + dzFile.id + " was clicked!!!")});
    }
}