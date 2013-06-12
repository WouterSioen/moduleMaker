<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of {$title} posts
 *
 * @author {$author_name} <{$author_email}>
 */
class Frotend{$camel_case_name}Index extends FrontendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->parse();
		$this->display();
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// parse the dataGrid if there are results
		$this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
	}
}
