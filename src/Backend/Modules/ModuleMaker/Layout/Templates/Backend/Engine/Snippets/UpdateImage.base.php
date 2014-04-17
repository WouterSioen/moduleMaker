
    /**
     * Update a certain category
     *
     * @param array $item
     */
    public static function updateImage(array $item)
    {
        $item['edited_on'] = BackendModel::getUTCDate();

        BackendModel::get('database')->update(
            '{$underscored_name}_images', $item, 'id = ?', array($item['id'])
        );
    }
