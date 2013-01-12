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
	},

	// toggles the options tagbox on the add field page
	toggleOptions: function()
	{
		$ddmType = $('#type');
		$options = $('#jsToggleOptions');

		if($ddmType.length > 0 && $options.length > 0)
		{
			$value = $ddmType.val();

			if($value == 'dropdown' || $value == 'multicheckbox' || $value == 'radiobutton' || $value == 'image')
			{
				$options.show();
			}

			$ddmType.on('change', function() {
				$value = $ddmType.val();

				if($value == 'dropdown' || $value == 'multicheckbox' || $value == 'radiobutton' || $value == 'image')
				{
					$options.slideDown(200);
				}
				else
				{
					$options.slideUp(200);
				}
			})
		}
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
	}
}

$(jsBackend.modulemaker.init);
