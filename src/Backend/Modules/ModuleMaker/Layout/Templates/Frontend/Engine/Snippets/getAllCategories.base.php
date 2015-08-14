    /**
     * Get all categories used
     *
     * @return array
     */
    public static function getAllCategories()
    {
        $return = (array) FrontendModel::get('database')->getRecords(
            'SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total, m.data AS meta_data
             FROM {$underscored_name}_categories AS c
             INNER JOIN {$underscored_name} AS i ON c.id = i.category_id AND c.language = i.language
             INNER JOIN meta AS m ON c.meta_id = m.id
             GROUP BY c.id
             ORDER BY c.sequence ASC',
            array(),
            'id'
        );

        // loop items and unserialize
        foreach ($return as &$row) {
            if (isset($row['meta_data'])) {
                $row['meta_data'] = unserialize($row['meta_data']);
            }
        }

        return $return;
    }
