
	/**
	 * Update a certain category
	 *
	 * @param array $item
	 */
	public static function updateImage(array $item)
	{
		$item['edited_on'] = Model::getUTCDate();

		Model::get('database')->update(
			'{$underscored_name}_images', $item, 'id = ?', array($item['id'])
		);
	}
