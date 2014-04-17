
	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function deleteImage($id)
	{
		Model::get('database')->delete('{$underscored_name}_images', 'id = ?', (int) $id);
	}
