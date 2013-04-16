
	/**
	 * Insert a category in the database
	 *
	 * @param array $item
	 * @param array[optional] $meta The metadata for the category to insert.
	 * @return int
	 */
	public static function insertCategory(array $item)
	{
		return BackendModel::getContainer()->get('database')->insert('{$underscored_name}_categories', $item);
	}
