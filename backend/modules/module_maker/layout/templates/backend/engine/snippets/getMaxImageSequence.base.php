
	/**
	 * Get the maximum image sequence.
	 *
	 * @param int $id
	 * @return int
	 */
	public static function getMaximumImageSequence($id)
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT MAX(i.sequence)
			 FROM {$underscored_name}_images AS i
			 WHERE i.{$underscored_name}_id = ?',
			array((int) $id)
		);
	}
