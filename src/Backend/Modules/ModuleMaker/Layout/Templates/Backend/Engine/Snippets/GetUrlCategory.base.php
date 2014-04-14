
	/**
	 * Retrieve the unique URL for a category
	 *
	 * @param string $url
	 * @param int[optional] $id The id of the category to ignore.
	 * @return string
	 */
	public static function getURLForCategory($url, $id = null)
	{
		$url = SpoonFilter::urlise((string) $url);
		$db = BackendModel::getContainer()->get('database');

		// new category
		if($id === null)
		{
			if((bool) $db->getVar(
				'SELECT 1
				 FROM {$underscored_name}_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURLForCategory($url);
			}
		}
		// current category should be excluded
		else
		{
			if((bool) $db->getVar(
				'SELECT 1
				 FROM {$underscored_name}_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url, $id)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURLForCategory($url, $id);
			}
		}

		return $url;
	}
