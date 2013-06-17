
	/**
	* Get the number of items in a category
	*
	* @param int $categoryId
	* @return int
	*/
	public static function getAllCategoryCount($categoryId)
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(i.id) AS count
			FROM news AS i
			WHERE i.category_id',
			array((int) $categoryId)
		);
	}
