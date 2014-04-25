
    /**
     * Delete a certain item
     *
     * @param int $id
     */
    public static function deleteImage($id)
    {
        BackendModel::get('database')->delete('{$underscored_name}_images', 'id = ?', (int) $id);
    }
