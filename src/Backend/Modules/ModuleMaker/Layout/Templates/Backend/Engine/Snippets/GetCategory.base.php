
    /**
     * Get all the categories
     *
     * @param bool $includeCount
     * @return array
     */
    public static function getCategories($includeCount = false)
    {
        $db = BackendModel::get('database');

        if ($includeCount) {
            return (array) $db->getPairs(
                'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
                 FROM {$underscored_name}_categories AS i
                 LEFT OUTER JOIN {$underscored_name} AS p ON i.id = p.category_id AND i.language = p.language
                 WHERE i.language = ?
                 GROUP BY i.id',
                array(Language::getWorkingLanguage())
            );
        }

        return (array) $db->getPairs(
            'SELECT i.id, i.title
             FROM {$underscored_name}_categories AS i
             WHERE i.language = ?',
            array(Language::getWorkingLanguage())
        );
    }

    /**
     * Fetch a category
     *
     * @param int $id
     * @return array
     */
    public static function getCategory($id)
    {
        return (array) BackendModel::get('database')->getRecord(
            'SELECT i.*
             FROM {$underscored_name}_categories AS i
             WHERE i.id = ? AND i.language = ?',
            array((int) $id, Language::getWorkingLanguage())
        );
    }

    /**
     * Get the maximum sequence for a category
     *
     * @return int
     */
    public static function getMaximumCategorySequence()
    {
        return (int) BackendModel::get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM {$underscored_name}_categories AS i
             WHERE i.language = ?',
            array(Language::getWorkingLanguage())
        );
    }
