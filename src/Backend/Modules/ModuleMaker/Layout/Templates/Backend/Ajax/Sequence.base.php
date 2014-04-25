<?php

namespace Backend\Modules\{$camel_case_name}\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\{$camel_case_name}\Engine\Model as Backend{$camel_case_name}Model;

/**
 * Alters the sequence of {$title} articles
 *
 * @author {$author_name} <{$author_email}>
 */
class Sequence extends AjaxAction
{
    public function execute()
    {
        parent::execute();

        // get parameters
        $newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

        // list id
        $ids = (array) explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            $item['id'] = $id;
            $item['sequence'] = $i + 1;

            // update sequence
            if (Backend{$camel_case_name}Model::exists($id)) {
                Backend{$camel_case_name}Model::update($item);
            }
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}
