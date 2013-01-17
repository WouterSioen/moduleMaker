/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the {$title} module
 *
 * @author {$author_name} <{$author_email}>
 */
jsBackend.{$underscored_name} =
{
	// constructor
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	}
}

$(jsBackend.{$underscored_name}.init);
