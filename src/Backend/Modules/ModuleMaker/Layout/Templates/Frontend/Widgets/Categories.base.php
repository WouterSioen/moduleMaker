<?php

namespace Frontend\Modules\{$camel_case_name}\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\{$camel_case_name}\Engine\Model as Frontend{$camel_case_name}Model;

/**
 * This is a widget with the {$title}-categories
 *
 * @author {$author_name} <{$author_email}>
 */
class Categories extends Widget
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
        if (!empty($categories)) {
            // build link
            $link = Navigation::getURLForBlock('{$camel_case_name}', 'category');

            // loop and reset url
            foreach ($categories as &$row) {
                $row['url'] = $link . '/' . $row['url'];
            }
        }

        // assign comments
        $this->tpl->assign('widget{$camel_case_name}Categories', $categories);
    }
}
