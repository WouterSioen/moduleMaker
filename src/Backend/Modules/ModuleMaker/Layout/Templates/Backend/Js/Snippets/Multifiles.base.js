
        if ($('#uploadedImages').length == 1) jsBackend.{$underscored_name}.uploadHandler.init();
    },

    // ajax uploader
    uploadHandler:
    {
        lowestSequence: 0,

        init: function()
        {
            // create uploader
            uploader =
            {
                // create the file uploader
                element: $('#jsImageUploader').fineUploader(
                    {
                        request: {
                            endpoint: '/backend/ajax',
                            params: {
                                fork: {
                                    module: '{$camel_case_name}',
                                    action: 'Upload'
                                }
                            }
                        },

                        chunking: {
                            enabled: true,
                            partSize: 200000
                        },

                        validation:
                        {
                            allowedExtensions: ['jpeg', 'jpg', 'gif', 'png']
                        },

                        text:
                        {
                            uploadButton: jsBackend.locale.msg('AddNewImages')
                        },

                        template:
                            '<div class="qq-uploader span12">' +
                                '<a class="qq-upload-button button icon iconAdd" href="#"><span>{uploadButtonText}</span></a>'+
                                '<pre class="qq-upload-drop-area" style="display: none;"><span>{dragZoneText}</span></pre>' +
                                '<span class="qq-drop-processing" style="display: none;"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
                                '<ul class="qq-upload-list" style="display: none;"></ul>' +
                            '</div>'
                    }),

                uploadedFiles: {}
            };

            // add uploadedFiles if the uploadedImages field is filled
            if ($('#uploadedImages').val())
            {
                $images = $.parseJSON($('#uploadedImages').val());
                $.each($images, function(i, item){
                    uploader.uploadedFiles['_' + item.id] = {
                        id: '_' + item.id,
                        uploadURL: item.uploadURL,
                        uploadName: item.uploadName,
                        originalName: item.originalFileName,
                        warning: '',
                        sequence: item.sequence,
                        progress: 100
                    };
                });
                jsBackend.{$underscored_name}.uploadHandler.buildFileList();
            }

            // bind complete callback
            uploader.element.on('upload', function(event, id, fileName)
            {
                jsBackend.{$underscored_name}.uploadHandler.lowestSequence--;
                uploader.uploadedFiles[id] = {
                    id: id,
                    uploadURL: '',
                    uploadName: '',
                    originalName: fileName,
                    warning: '',
                    sequence: jsBackend.{$underscored_name}.uploadHandler.lowestSequence,
                    progress: 0
                };

                jsBackend.{$underscored_name}.uploadHandler.buildFileList();
            });

            // bind complete callback
            uploader.element.on('progress', function(event, id, fileName, uploadedBytes, totalBytes)
            {
                $('#upImage' + id).find('.progressBar').css('width', (uploadedBytes / totalBytes) * 100 + '%');
            });

            // bind complete callback
            uploader.element.on('complete', function(event, id, fileName, response)
            {
                if (response.success && typeof response.uploadURL !== 'undefined')
                {
                    // add image to uploaded files array
                    uploader.uploadedFiles[id]['uploadURL'] = response.uploadURL;
                    uploader.uploadedFiles[id]['uploadName'] = response.uploadName;
                    uploader.uploadedFiles[id]['originalName'] = response.originalFileName;
                    uploader.uploadedFiles[id]['warning'] = (response.warning !== undefined ? response.warning : '');
                    uploader.uploadedFiles[id]['progress'] = 100;

                    jsBackend.{$underscored_name}.uploadHandler.buildFileList();
                }
            });

            // make the items sortable
            $('#jsFileList').sortable({
                update: function(event, ui){ jsBackend.{$underscored_name}.uploadHandler.sortImages($(this)); },
                tolerance: 'pointer'
            });
        },

        // build the list of uploaded files
        buildFileList: function()
        {
            if ($('#jsFileList').length > 0)
            {
                var filledFields = {};
                var html = '';

                $('.jsNextAction .inputText').each(function()
                {
                    var element = $(this);
                    filledFields[element.attr('id')] = element.val();
                });

                // get the keys ordered by sequence
                var orderedKeys = jsBackend.{$underscored_name}.uploadHandler.getOrderedKeys();

                $.each(orderedKeys, function(index, item)
                {
                    item = uploader.uploadedFiles[item];

                    var formFields = '<div class="progressBar" style="width: 0%;"></div>';
                    if (item.progress == 100)
                    {
                        var find = '{id}';
                        var re = new RegExp(find, 'g');
                    }
                    html += '<li id="upImage' + item.id + '">';
                    html += '<div class="jsImage">';
                    html += (item.uploadURL != '' ? '<img src="' + item.uploadURL + '/100x100/' + item.uploadName + '">' : '');
                    html += '<div class="buttonHolder"><a href="#" class="jsDeleteImage button icon iconDelete iconOnly" data-list-id="' + item.id + '"><span>Remove</span></a></div>';
                    html += formFields + '</div>';
                    html += '</li>';
                });

                $('#uploadedImages').val(JSON.stringify(uploader.uploadedFiles));

                $('#jsFileList').html(html);

                $.each(filledFields, function(fieldId, fieldValue)
                {
                    $('#' + fieldId).val(fieldValue);
                });
                $('.jsDeleteImage').on('click', jsBackend.{$underscored_name}.uploadHandler.deleteImage );
            }
        },

        // delete an image from the canvas and uploaded files
        deleteImage: function(e)
        {
            e.preventDefault();
            var $this = $(this);

            // get the list id (this is actually the key in the uploaded files array)
            var listId = $this.data('list-id');

            // delete item from list
            var tempList = {};
            $.each(uploader.uploadedFiles, function(index, item)
            {
                if (typeof item !== 'undefined' && index != listId)
                {
                    tempList[index] = item;
                }
            });
            uploader.uploadedFiles = tempList;
            $('#uploadedImages').val(JSON.stringify(uploader.uploadedFiles));

            // fade out and remove the item on complete
            $this.closest('li').fadeOut(200, function()
            {
                // rebuild the list
                $('#upImage' + listId).remove();
            });
        },

        // orders the imageslist by sequence
        getOrderedKeys: function()
        {
            // First get all keys:
            var keys = [];
            for(var n in uploader.uploadedFiles) keys.push(n);

            // now sort the keys:
            keys.sort(function(a, b)
            {
                $objectA = uploader.uploadedFiles[a];
                $objectB = uploader.uploadedFiles[b];

                return parseInt($objectA.sequence) - parseInt($objectB.sequence);
            });

            return keys;
        },

        sortImages: function($items)
        {
            // fetch id's
            var ids = $items.sortable('toArray', {attribute: 'id'});

            // loop ids and change sequence
            $.each(ids, function(i, item){
                id = item.replace('upImage', '');
                uploader.uploadedFiles[id]['sequence'] = i;
            });

            jsBackend.{$underscored_name}.uploadHandler.lowestSequence = 0;
            $('#uploadedImages').val(JSON.stringify(uploader.uploadedFiles));
        }
