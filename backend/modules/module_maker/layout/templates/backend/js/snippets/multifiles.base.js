
		if($('#uploadedImages').length == 1) jsBackend.{$underscored_name}.uploadHandler.init();
	},

	// ajax uploader
	uploadHandler:
	{
		init: function()
		{
			// create uploader
			uploader =
			{
				// create the file uploader
				element: $('#jsImageUploader').fineUploader(
					{
						request: {
							endpoint: '/backend/ajax.php',
							params: {
								fork: {
									module: '{$underscored_name}',
									action: 'upload'
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

			// bind complete callback
			uploader.element.on('upload', function(event, id, fileName)
			{
				uploader.uploadedFiles[id] = {
					id: id,
					uploadURL: '',
					uploadName: '',
					originalName: fileName,
					warning: '',
					progress: 0
				};

				jsBackend.{$underscored_name}.uploadHandler.buildFileList();
			});

			// bind complete callback
			uploader.element.on('progress', function(event, id, fileName, uploadedBytes, totalBytes)
			{
				$('#upImage' + id).find('.progressBar').css('width', (uploadedBytes / totalBytes) + '%').text(uploadedBytes + '/' + totalBytes);
			});

			// bind complete callback
			uploader.element.on('complete', function(event, id, fileName, response)
			{
				if(response.success && typeof response.uploadURL !== 'undefined')
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
		},

		// build the list of uploaded files
		buildFileList: function()
		{
			if($('#jsFileList').length > 0)
			{
				var filledFields = {};
				var html = '';

				$('.jsNextAction .inputText').each(function()
				{
					var $element = $(this);
					filledFields[$element.attr('id')] = $element.val();
				});
				$.each(uploader.uploadedFiles, function(index, item)
				{
					var formFields = 'Uploading...<div class="progressBar" style="width: 0%;"></div>';
					if(item.progress == 100)
					{
						var find = '{id}';
						var re = new RegExp(find, 'g');
					}
					html += '<li id="upImage' + item.id + '">';
					html += '<div class="jsImage">';
					html += (item.uploadURL != '' ? '<img src="' + item.uploadURL + '/100x100/' + item.uploadName + '">' : '');
					html += '<div class="buttonHolder"><a href="#" class="jsDeleteImage button icon iconDelete" data-list-id="' + item.id + '"><span>Remove</span></a></div>';
					html += '</div>';

					if(item.warning != '') html += '<div class="jsNextAction warning">' + item.warning + '</div>';
					else html += '<div class="jsNextAction">' + formFields + '</div>';

					html += '</li>';
				});

				$('#uploadedImages').val(JSON.stringify(uploader.uploadedFiles));

				$('#jsFileList').html(html);

				$.each(filledFields, function(fieldId, fieldValue)
				{
					$('#' + fieldId).val(fieldValue);
				});
				$('.jsDeleteImage').on('click', jsBackend.test.uploadHandler.deleteImage );
			}
		},

		// delete an image from the canvas and uploaded files
		deleteImage: function(event)
		{
			event.preventDefault();

			var $this = $(this);

			// get the list id (this is actually the key in the uploaded files array)
			var listId = parseInt($this.data('list-id'));

			// delete item from list
			var tempList = {};
			$.each(uploader.uploadedFiles, function(index, item)
			{
				if(typeof item !== 'undefined' && index != listId)
				{
					tempList[index] = item;
				}
			});
			uploader.uploadedFiles = tempList;

			// fade out and remove the item on complete
			$this.parent().fadeOut(300, function()
			{
				// rebuild the list
				$('#upImage' + listId).remove();
			});
		}
