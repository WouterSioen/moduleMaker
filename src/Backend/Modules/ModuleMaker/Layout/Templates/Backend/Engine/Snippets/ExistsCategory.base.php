
	/**
	 * Does the category exist?
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM {$underscored_name}_categories AS i
			 WHERE i.id = ? AND i.language = ?
			 LIMIT 1',
			array((int) $id, BL::getWorkingLanguage()));
	}
