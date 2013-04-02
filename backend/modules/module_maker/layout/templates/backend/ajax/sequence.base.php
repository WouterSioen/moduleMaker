<?php

/**
 * Alters the sequence of {$title} articles
 *
 * @author {$author_name} <{$author_email}>
 */
class Backend{$camel_case_name}AjaxSequence extends BackendBaseAJAXAction
{
	public function execute()
	{
		parent::execute();

		// get parameters
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// list id
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			// update sequence
			if(Backend{$camel_case_name}Model::exists($id)) Backend{$camel_case_name}Model::update($id, array('sequence' => $i + 1));
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}
