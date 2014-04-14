<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the {$title}-categories
 *
 * @author {$author_name} <{$author_email}>
 */
class Frontend{$camel_case_name}WidgetCategories extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		// get categories
		$categories = Frontend{$camel_case_name}Model::getAllCategories();

		// any categories?
		if(!empty($categories))
		{
			// build link
			$link = FrontendNavigation::getURLForBlock('{$underscored_name}', 'category');

			// loop and reset url
			foreach($categories as &$row) $row['url'] = $link . '/' . $row['url'];
		}

		// assign comments
		$this->tpl->assign('widget{$camel_case_name}Categories', $categories);
	}
}
