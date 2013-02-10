

	/**
	 * Retrieve the unique URL for an item
	 *
	 * @param string $url
	 * @param int[optional] $id	The id of the item to ignore.
	 * @return string
	 */
	public static function getURL($url, $id = null)
	{
		$url = SpoonFilter::urlise((string) $url);
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			// already exists
			if((bool) $db->getVar(
				'SELECT 1
				 FROM {$underscored_name} AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURL($url);
			}
		}
		// current item should be excluded
		else
		{
			// already exists
			if((bool) $db->getVar(
				'SELECT 1
				 FROM {$underscored_name} AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url, $id)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURL($url, $id);
			}
		}

		return $url;
	}