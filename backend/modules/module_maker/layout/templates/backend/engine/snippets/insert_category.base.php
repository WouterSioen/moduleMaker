
	/**
	 * Insert a category in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insertCategory(array $item)
	{
		$item['created_on'] = BackendModel::getUTCDate();

		return BackendModel::getContainer()->get('database')->insert('{$underscored_name}_categories', $item);
	}
