<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendTestAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the actions
	 */
	public function execute()
	{
		parent::execute();

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		$this->frm = new BackendForm('add');

		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addImage('image');
		$this->frm->addText('image_caption');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);

		// parse additional variables
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validation
			$fields = $this->frm->getFields();

			$fields['title']->isFilled(BL::err('FieldIsRequired'));

			// validate meta
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build the item
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $fields['title']->getValue();

				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image';

				// create folders if needed
				if(!SpoonDirectory::exists($imagePath . '/100x100')) SpoonDirectory::create($imagePath . '/100x100');
				if(!SpoonDirectory::exists($imagePath . '/200x200')) SpoonDirectory::create($imagePath . '/200x200');
				if(!SpoonDirectory::exists($imagePath . '/source')) SpoonDirectory::create($imagePath . '/source');

				// image provided?
				if($fields['image']->isFilled())
				{
					// build the image name
					$item['image'] = $this->meta->getUrl() . '.' . $fields['image']->getExtension();

					// upload the image & generate thumbnails
					$fields['image']->generateThumbnails($imagePath, $item['image']);
				}

				$item['image_caption'] = $fields['image_caption']->getValue();

				$item['meta_id'] = $this->meta->save();

				// insert it
				$item['id'] = BackendTestModel::insert($item);

				BackendModel::triggerEvent(
					$this->getModule(), 'after_add', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
				);
			}
		}
	}
}
