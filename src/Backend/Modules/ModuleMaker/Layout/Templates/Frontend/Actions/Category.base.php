<?php

namespace Frontend\Modules\{$camel_case_name}\Actions;

use Frontend\Core\Engine\Base\Block;
use Frontend\Core\Engine\Model;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\{$camel_case_name}\Engine\Model as Frontend{$camel_case_name}Model;

/**
 * This is the category-action, it will display the overview of {$title} categories
 *
 * @author {$author_name} <{$author_email}>
 */
class Category extends Block
{
    /**
     * The items and category
     *
     * @var    array
     */
    private $items, $category;

    /**
     * The pagination array
     * It will hold all needed parameters, some of them need initialization.
     *
     * @var    array
     */
    protected $pagination = array(
        'limit' => 10,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null
    );

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
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        if ($this->URL->getParameter(0) === null) {
            $this->redirect(Navigation::getURL(404));
        }

        // get category
        $this->category = Frontend{$camel_case_name}Model::getCategory($this->URL->getParameter(0));
        if (empty($this->category)) {
            $this->redirect(Navigation::getURL(404));
        }

        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        // set URL and limit
        $this->pagination['url'] = Navigation::getURLForBlock('{$camel_case_name}', 'category') . '/' . $this->category['url'];

        $this->pagination['limit'] = $this->get('fork.settings')->get('{$camel_case_name}', 'overview_num_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = Frontend{$camel_case_name}Model::getCategoryCount($this->category['id']);
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // num pages is always equal to at least 1
        if ($this->pagination['num_pages'] == 0) {
            $this->pagination['num_pages'] = 1;
        }

        // redirect if the request page doesn't exist
        if ($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) {
            $this->redirect(Navigation::getURL(404));
        }

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] - 1) * $this->pagination['limit'];

        // get items
        $this->items = Frontend{$camel_case_name}Model::getAllByCategory(
            $this->category['id'], $this->pagination['limit'], $this->pagination['offset']
        );
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        // add into breadcrumb
        $this->breadcrumb->addElement($this->category['meta_title']);

        // hide action title
        $this->tpl->assign('hideContentTitle', true);

        // show the title
        $this->tpl->assign('title', $this->category['title']);

        // set meta
        $this->header->setPageTitle($this->category['meta_title'], ($this->category['meta_title_overwrite'] == 'Y'));
        $this->header->addMetaDescription($this->category['meta_description'], ($this->category['meta_description_overwrite'] == 'Y'));
        $this->header->addMetaKeywords($this->category['meta_keywords'], ($this->category['meta_keywords_overwrite'] == 'Y'));

        // advanced SEO-attributes
        if (isset($this->category['meta_data']['seo_index'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_index'])
            );
        }
        if (isset($this->category['meta_data']['seo_follow'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_follow'])
            );
        }

        // assign items
        $this->tpl->assign('items', $this->items);

        // parse the pagination
        $this->parsePagination();
    }
}
