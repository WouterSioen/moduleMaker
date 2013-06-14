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
 * @author Arend Pijls <arend.pijls@wijs.be>
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
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get record
		$this->record = Frontend{$camel_case_name}Model::get($this->URL->getParameter(1));

		// check if record is not empty
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// build Facebook Open Graph-data
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null || FrontendModel::getModuleSetting('core', 'facebook_app_id', null) !== null)
		{
			/**
			 * @TODO add specified image
			 * $this->header->addOpenGraphImage(FRONTEND_FILES_URL . '/{$underscored_name}/images/source/' . $this->record['image']);
			 */

			// add additional OpenGraph data
			$this->header->addOpenGraphData('title', $this->record['meta_title'], true);
			$this->header->addOpenGraphData('type', 'article', true);
			$this->header->addOpenGraphData('url', SITE_URL . FrontendNavigation::getURLForBlock('{$underscored_name}', 'detail') . '/' . $this->record['url'], true);
			$this->header->addOpenGraphData('site_name', FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE), true);
			$this->header->addOpenGraphData('description', $this->record['title'], true);
		}

		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['meta_title']);

		// set meta
		$this->header->setPageTitle($this->record['meta_title'], ($this->record['meta_title_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

		// advanced SEO-attributes
		if(isset($this->record['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index']));
		if(isset($this->record['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow']));

		// assign item
		$this->tpl->assign('item', $this->record);
	}
}
