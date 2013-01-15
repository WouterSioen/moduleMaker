/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the awesome module module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
jsBackend.awesome_module =
{
	// constructor
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	}
}

$(jsBackend.awesome_module.init);
