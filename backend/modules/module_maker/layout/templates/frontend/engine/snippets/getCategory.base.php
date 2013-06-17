
	/**
	* Fetches a certain category
	*
	* @param string $URL
	* @return array
	*/
	public static function getCategory($URL)
	{
		$item = (array) FrontendModel::getContainer()->get('database')->getRecord(
				'SELECT i.*,
				m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
				m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
				m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
				FROM news_categories AS i
				INNER JOIN meta AS m ON i.meta_id = m.id
				WHERE m.url = ?',
				array((string) $URL)
		);

		// no results?
		if(empty($item)) return array();

		// create full url
		$item['full_url'] = FrontendNavigation::getURLForBlock('news', 'category') . '/' . $item['url'];

		return $item;
	}


