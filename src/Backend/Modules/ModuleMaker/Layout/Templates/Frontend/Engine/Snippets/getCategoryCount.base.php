
    /**
     * Get the number of items in a category
     *
     * @param int $categoryId
     * @return int
     */
    public static function getCategoryCount($categoryId)
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id)
             FROM {$underscored_name} AS i
             WHERE i.language = ? AND i.category_id = ?',
            array(FRONTEND_LANGUAGE, (int) $categoryId)
        );
    }
