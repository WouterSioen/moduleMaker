
	/**
	 * Get the maximum {$title} sequence.
	 *
	 * @return int
	 */
	public static function getMaximumSequence()
	{
		return (int) Model::get('database')->getVar(
			'SELECT MAX(i.sequence)
			 FROM {$underscored_name} AS i'
		);
	}
