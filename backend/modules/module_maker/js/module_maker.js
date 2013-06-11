/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the modulemaker module
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
jsBackend.modulemaker =
{
	// constructor
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();

		jsBackend.modulemaker.tagBoxes();
		jsBackend.modulemaker.toggleOptions();
		jsBackend.modulemaker.toggleMeta();
		jsBackend.modulemaker.toggleSearch();
		jsBackend.modulemaker.renderInit();
	},

	// initializes tagBox
	tagBoxes: function()
	{
		if($('input.tagBox').length > 0)
		{
			$('input.tagBox').tagBox(
			{
				emptyMessage: jsBackend.locale.msg('NoOptions'),
				errorMessage: jsBackend.locale.err('AddOptionsBeforeSubmitting'),
				addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
				removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('DeleteThisOption'))
			});

			$('input.tagBox').on('change', function() { 
				console.log($('input.tagBox').val());
			});
		}
	},

	// toggles the visibility of the field dropdown for meta
	toggleMeta: function()
	{
		$chkMeta = $('#meta');
		$ddmMetaFieldDiv = $('.showOnMeta');

		if($chkMeta.length > 0 && $ddmMetaFieldDiv.length > 0)
		{
			$chkMeta.on('change', function(){
				($chkMeta.attr('checked') === 'checked')
					? $ddmMetaFieldDiv.slideDown(200)
					: $ddmMetaFieldDiv.slideUp(200);
			});
		}
	},

	// toggles the options tagbox on the add field page
	toggleOptions: function()
	{
		$ddmType = $('#type');
		$options = $('#jsToggleOptions');
		$optionsLabel = $("label[for='addValue-tags']");

		if($ddmType.length > 0 && $options.length > 0)
		{
			$value = $ddmType.val();

			// initialize the options if necessary
			if($value == 'dropdown' || $value == 'multicheckbox' || $value == 'radiobutton' || $value == 'image')
			{
				$options.show();
			}

			$ddmType.on('change', function() {
				$value = $ddmType.val();

				// show or hide it on change
				if($value == 'dropdown' || $value == 'multicheckbox' || $value == 'radiobutton' || $value == 'image') $options.slideDown(200);
				else $options.slideUp(200);

				// change label to imagesizes when the type is image
				if($value == 'image') $optionsLabel.text(utils.string.ucfirst(jsBackend.locale.lbl('ImageSizes')) + '*');
				else $optionsLabel.text(utils.string.ucfirst(jsBackend.locale.lbl('Options')) + '*');

				// don't show default option for text area (mysql type TEXT can't use default value)
				if($value == 'editor') $('#defaultOption').hide();
				else $('#defaultOption').show();
			})
		}
	},

	// toggles the visibility of the fields multicheckbox for the search field
	toggleSearch: function()
	{
		$chkSearch = $('#search');
		$ddmSearchFieldsDiv = $('.showOnSearch');

		if($chkSearch.length > 0 && $ddmSearchFieldsDiv.length > 0)
		{
			$chkSearch.on('change', function(){
				($chkSearch.attr('checked') === 'checked')
					? $ddmSearchFieldsDiv.slideDown(200)
					: $ddmSearchFieldsDiv.slideUp(200);
			});
		}
	},

	// checks if a field changes
	renderInit: function()
	{
		$txtLabel = $('#label');
		$ddmType = $('#type');
		$chkRequired = $('#required');

		if($txtLabel.length > 0 && $ddmType.length > 0 && $chkRequired.length > 0)
		{
			$txtLabel.on('change', jsBackend.modulemaker.render);
			$ddmType.on('change', jsBackend.modulemaker.render);
			$('#addValue-tags').live('keyup', function(e) {
				if(e.which == '13') jsBackend.modulemaker.render;
			});
			$('.deleteButton-tags').live('click', jsBackend.modulemaker.render);
			$('#addValue-tags').live('change', jsBackend.modulemaker.render);
			$chkRequired.on('change', jsBackend.modulemaker.render);
		}
	},

	render: function()
	{
		// get values
		$label = $('#label').val();
		$type = $('#type').val();
		$required = ($chkRequired.attr('checked') === 'checked');
		$tags = $('#tags').val();

		// append input in tags field to the tags
		$tags = ($tags) ? $tags + ',' + $('#addValue-tags').val() : $('#addValue-tags').val();

		// do ajax call to render the field


		console.log($label);
		console.log($type);
		console.log($required);
		console.log($tags);
	}
}

$(jsBackend.modulemaker.init);
