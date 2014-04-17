
    /**
     * Update a certain category
     *
     * @param array $item
     */
    public static function updateCategory(array $item)
    {
        $item['edited_on'] = Model::getUTCDate();

        Model::get('database')->update(
            '{$underscored_name}_categories', $item, 'id = ?', array($item['id'])
        );
    }
