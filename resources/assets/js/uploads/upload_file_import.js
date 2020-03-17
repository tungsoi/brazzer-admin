this.CMS = this.CMS || {};
this.CMS.upload_import = function(self, upload_field){
    var is_validate = true;
    if(is_validate){
        var url = $('#upload_import-' + upload_field).attr('action')+'?';
        if(upload_field == 'agency_topup'){
            url += '&import_bank=' + $('select[name=import_bank]').val();
        }
        //$(self).attr("disabled", "disabled");
        var fileInput = $('#modal-import-file-' + upload_field + ' input#inp_upload')
        fileInput.click();
        var progress_upload = $('#modal-import-file-' + upload_field + '.progress-upload-import');
        var alert_upload = $('#modal-import-file-' + upload_field + ' .alert');
        $('#upload_import-' + upload_field).fileupload({
            url: url,
            dropZone: $('#drop'),
            maxChunkSize: 256 * 1024,
            maxRetries: 100,
            retryTimeout: 1000,
            multipart: false,
            //sequentialUploads: true,
            //dataType: 'json',
            fileInput: fileInput,
            //formData: formData,
            acceptFileTypes: /(\.|\/)(csv|txt|xls|xlsx)$/i,
            beforeSend:
                function(xhr, data){
                    var name = $('.progress-upload-import').find('#progress').data("file-name");
                    xhr.setRequestHeader('Content-Disposition', 'attachment; filename="' + encodeURI(name) + '"');
                }
            ,
            add: function(e, data){
                var filename = fileInput[0].files.length ? fileInput[0].files[0].name : "";
                //var ext = file.type.replace("image/", ".").replace("video/", ".");
                var ext = filename.substr(filename.lastIndexOf('.') + 1, filename.length);
                var regExpr = /[^a-zA-Z0-9.]/g;
                $('#filename_import').text(filename);
                filename = filename.replace('.' + ext, '');
                filename = filename + "-" + new Date().getTime() + '.' + ext;
                filename = filename.replace(regExpr, "-").replace(/-{2,}/g, '-');
                $('#modal-import-file-' + upload_field + ' .alert').hide().html('');
                $('.progress-upload-import').html('<div id="progress" class="progress" data-file-name="' + filename + '">\n' +
                    '                                <div class="progress-bar progress-bar-success" style="width: 0%;"></div>\n' +
                    '                            </div>');
                //data.formData = {import_bank: '1'};
                var jqXHR = data.submit();
            }
            ,
            progress: function(e, data){
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.progress-upload-import').find('#progress .progress-bar').removeClass('progress-bar-danger').addClass('progress-bar-success');
                $('.progress-upload-import').find('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
                if(progress == 100){
                    $('#modal-import-file-' + upload_field + ' .alert').removeClass('alert-danger').addClass('alert-success').show().html('<p>Đang import dữ liệu. Vui lòng đợi trong giây lát.</p>');
                }
            }
            ,
            done: function(e, data){
                //console.log(data);
                if(data.result != ''){
                    var res = JSON.parse(JSON.stringify(data.result));
                    if(res.success){
                        $('.progress-upload-import').find('#progress .progress-bar').addClass('progress-bar-success').removeClass('progress-bar-danger');
                        $('#modal-import-file-' + upload_field + ' .alert').addClass('alert-success').removeClass('alert-danger').show().html('<p>' + res.message + '</p>');
                        $(self).removeAttr("disabled");
                        /*if(typeof res.data != undefined && res.data != ''){
                            $('#modal-import-file-' + upload_field).modal('hide');
                            $('#modal-import-data-' + upload_field).modal('show');
                            $('#modal-import-data-' + upload_field + ' .modal-body').html(res.data);
                        }*/
                    }else{
                        $('.progress-upload-import').find('#progress .progress-bar').addClass('progress-bar-danger').removeClass('progress-bar-success');
                        $('#modal-import-file-' + upload_field + ' .alert').addClass('alert-danger').removeClass('alert-success').show().html('<p>' + res.message + '</p>');
                        $(self).removeAttr("disabled");
                    }
                }else{
                    $('.progress-upload-import').find('#progress .progress-bar').addClass('progress-bar-danger').removeClass('progress-bar-success');
                    $('#modal-import-file-' + upload_field + ' .alert').addClass('alert-danger').removeClass('alert-success').show().html('<p>Quá trình upload có lỗi. Vui lòng thử lại sau.</p>');
                    $(self).removeAttr("disabled");
                }
            }
            ,
            fail: function(e, data){
                $('.progress-upload-import').find('#progress .progress-bar').addClass('progress-bar-danger').removeClass('progress-bar-success');
                $('#modal-import-file-' + upload_field + ' .alert').addClass('alert-danger').removeClass('alert-success').show().html('<p>Lỗi kết nối đến server upload. Vui lòng thử lại sau.</p>');
                $(self).removeAttr("disabled");
            }
        });
    }
    return false;
};
this.CMS.upload_import_insert = function(self, upload_field){
    $(self).attr("disabled", "disabled");
    return false;
};