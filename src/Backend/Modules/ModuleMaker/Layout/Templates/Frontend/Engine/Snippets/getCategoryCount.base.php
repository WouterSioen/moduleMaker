
    /**
     * Get the number of items in a category
     *
     * @param int $categoryId
     * @return int
     */
    public static function getCategoryCount($categoryId)
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM {$underscored_name} AS i
             WHERE i.category_id = ?',
            array((int) $categoryId)
        );
    }
