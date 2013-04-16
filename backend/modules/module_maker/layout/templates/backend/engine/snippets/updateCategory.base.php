
	/**
	 * Update a certain category
	 *
	 * @param array $item
	 */
	public static function updateCategory(array $item)
	{
		BackendModel::getContainer()->get('database')->update(
			'{$underscored_name}_categories', $item, 'id = ?', array($item['id'])
		);
	}
