
	/**
	 * Delete a specific category
	 *
	 * @param int $id
	 */
	public static function deleteCategory($id)
	{
		$db = BackendModel::getContainer()->get('database');
		$item = self::getCategory($id);

		if(!empty($item))
		{
			$db->delete('meta', 'id = ?', array($item['meta_id']));
			$db->delete('{$underscored_name}_categories', 'id = ?', array((int) $id));
			$db->update('{$underscored_name}', array('category_id' => null), 'category_id = ?', array((int) $id));
		}
	}
