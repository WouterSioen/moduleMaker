
	const QRY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT c.id, c.title, COUNT(i.id) AS num_items, c.sequence
		 FROM {$underscored_name}_categories AS c
		 LEFT OUTER JOIN {$underscored_name} AS i ON c.id = i.category_id AND i.language = c.language
		 WHERE c.language = ?
		 GROUP BY c.id
		 ORDER BY c.sequence ASC';
