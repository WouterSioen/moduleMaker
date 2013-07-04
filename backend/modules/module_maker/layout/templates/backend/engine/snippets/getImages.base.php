
	/**
	 * Get the maximum images for an item sequence.
	 *
	 * @param int $id
	 * @return int
	 */
	public static function getImages($id)
	{
		$images = (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.id, i.name
			 FROM {$underscored_name}_images AS i
			 WHERE i.{$underscored_name}_id = ?',
			array((int) $id),
			'id'
		);

		$url = FRONTEND_FILES_URL . '/{$underscored_name}';

		foreach($images as &$image)
		{
			$image['uploadURL'] = $url;
			$image['uploadName'] = $image['name'];
			$image['warning'] = '';
			$image['progress'] = 100;
			unset($image['name']);
		}

		return $images;
	}
