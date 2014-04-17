
	/**
	 * Insert a category in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insertCategory(array $item)
	{
		$item['created_on'] = Model::getUTCDate();
		$item['edited_on'] = Model::getUTCDate();

		return Model::get('database')->insert('{$underscored_name}_categories', $item);
	}
