<?php
/**
 * Copyright (c) 2017 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Fluent;


class MappedGetter extends AbstractMappedMember
{
	public function __toArray()
	{
		$structure = parent::__toArray();
		$structure['getter'] = true;
		return $structure;
	}
}