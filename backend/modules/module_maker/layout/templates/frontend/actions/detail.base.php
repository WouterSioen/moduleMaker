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
class Frontend{$camel_case_name}Detail extends FrontendBaseBlock
{
	/**
	 * The record
	 *
	 * @var	array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$lastParameter = $this->getLastParameter();
		if (empty($lastParameter)) {
			$this->redirect(FrontendNavigation::getURL(404));
		}
		$this->record = Frontend{$camel_case_name}Model::get($lastParameter);

		if (empty($this->record)) {
			$this->redirect(FrontendNavigation::getURL(404));
		}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		/**
		 * @TODO add specified image
		 * $this->header->addOpenGraphImage(FRONTEND_FILES_URL . '/{$underscored_name}/images/source/' . $this->record['image']);
		 */

		// build Facebook  OpenGraph data
		$this->header->addOpenGraphData('title', $this->record['meta_title'], true);
		$this->header->addOpenGraphData('type', 'article', true);
		$this->header->addOpenGraphData(
			'url',
			SITE_URL . FrontendNavigation::getURLForBlock('{$underscored_name}', 'detail') . '/' . $this->record['url'],
			true
		);
		$this->header->addOpenGraphData(
			'site_name',
			FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE),
			true
		);
		$this->header->addOpenGraphData('description', $this->record['meta_title'], true);
{$twitterCard}
		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['meta_title']);

		// hide action title
		$this->tpl->assign('hideContentTitle', true);

		// show title linked with the meta title
		$this->tpl->assign('title', $this->record['{$pageTitle}']);

		// set meta
		$this->header->setPageTitle($this->record['meta_title'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

		// advanced SEO-attributes
		if(isset($this->record['meta_data']['seo_index']))
		{
			$this->header->addMetaData(
				array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index'])
			);
		}
		if(isset($this->record['meta_data']['seo_follow']))
		{
			$this->header->addMetaData(
				array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow'])
			);
		}

		// assign item
		$this->tpl->assign('item', $this->record);
	}

	/**
	 * @return mixed
	 */
	private function getLastParameter()
	{
		$numberOfParameters = count($this->URL->getParameters());
		return $this->URL->getParameter($numberOfParameters - 1);
	}

}
