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
class BackendAwesomeModuleAdd extends BackendBaseActionAdd
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

		$this->frm->addText('title');
		$this->frm->addEditor('description');
		$this->frm->addText('price');
		$this->frm->addText('email');

		// build array with options for the visible dropdown
		$dropdownVisibleValues[] = array('label' => BL::lbl('Y'), 'value' => 'Y');
		$dropdownVisibleValues[] = array('label' => BL::lbl('N'), 'value' => 'N');
		$this->frm->addDropdown('visible', $dropdownVisibleValues ,'Y');
		$this->frm->addDate('publish_on_date');
		$this->frm->addTime('publish_on_time');
		$this->frm->addImage('image');

		// build array with options for the radiobutton radiobutton
		$radiobuttonRadiobuttonValues[] = array('label' => BL::lbl('1'), 'value' => '1');
		$radiobuttonRadiobuttonValues[] = array('label' => BL::lbl('2'), 'value' => '2');
		$radiobuttonRadiobuttonValues[] = array('label' => BL::lbl('3'), 'value' => '3');
		$this->frm->addRadioButton('radiobutton', $radiobuttonRadiobuttonValues);
		$this->frm->addCheckbox('accept_terms');

	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
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

			$fields('title')->isFilled(BL::err('FieldIsRequired'));
			$fields('description')->isFilled(BL::err('FieldIsRequired'));
			$fields('price')->isFilled(BL::err('FieldIsRequired'));
			$fields('price')->isInteger(BL::err('InvalidInteger'));
			$fields('visible')->isFilled(BL::err('FieldIsRequired'));
			$fields('publish_on_date')->isValid(BL::err('DateIsInvalid'));
			$fields('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
			if($this->frm->getField('image')->isFilled())
			{
				$this->frm->getField('image')->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
				$this->frm->getField('image')->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));
			{


			if($this->frm->isCorrect())
			{
				// build the item
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $fields['title']->getValue();
				$item['description'] = $fields['description']->getValue();
				$item['price'] = $fields['price']->getValue();
				$item['email'] = $fields['email']->getValue();
				$item['visible'] = $fields['visible']->getValue();
				$item['publish_on'] = $fields['publish_on']->getValue();

				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule()' . '/images';

				// create folders if needed
				if(!SpoonDirectory::exists($imagePath . '/64x64')) SpoonDirectory::create($imagePath . '/64x64');
				if(!SpoonDirectory::exists($imagePath . '/128x128')) SpoonDirectory::create($imagePath . '/128x128');
				if(!SpoonDirectory::exists($imagePath . '/250x250')) SpoonDirectory::create($imagePath . '/250x250');
				if(!SpoonDirectory::exists($imagePath . '/500x500')) SpoonDirectory::create($imagePath . '/500x500');
				if(!SpoonDirectory::exists($imagePath . '/source')) SpoonDirectory::create($imagePath . '/source');

				// image provided?
				if($this->frm->getField('image')->isFilled())
				{
					// build the image name
					$item['image'] = time() . '.' . $this->frm->getField('image')->getExtension();

					// upload the image & generate thumbnails
					$this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
				}

				$item['radiobutton'] = $fields['radiobutton']->getValue();
				$item['accept_terms'] = $fields['accept_terms']->getChecked() ? 'Y' : 'N';

				// insert it
				$item['id'] = BackendAwesomeModuleModel::insert($item);

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
