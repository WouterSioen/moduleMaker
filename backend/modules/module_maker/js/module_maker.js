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
	}
}

$(jsBackend.modulemaker.init);
