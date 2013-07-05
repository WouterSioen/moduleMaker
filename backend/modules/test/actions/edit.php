<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendTestEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();
		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the item data
	 */
	protected function loadData()
	{
		$this->id = $this->getParameter('id', 'int', null);
		if($this->id == null || !BackendTestModel::exists($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('index') . '&error=non-existing'
			);
		}

		$this->record = BackendTestModel::get($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		$this->frm->addText('title' ,$this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addImage('image');
		$this->frm->addText('image_caption', $this->record['image_caption']);

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
		$this->meta->setUrlCallBack('BackendTestModel', 'getUrl', array($this->record['id']));

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

		$this->tpl->assign('item', $this->record);
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
				$item['id'] = $this->id;
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

				BackendTestModel::update($item);
				$item['id'] = $this->id;

				BackendModel::triggerEvent(
					$this->getModule(), 'after_edit', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']
				);
			}
		}
	}
}
