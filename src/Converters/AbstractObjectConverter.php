<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;

use OneOfZero\Json\Nodes\AbstractObjectNode;
use OneOfZero\Json\Exceptions\ResumeSerializationException;

abstract class AbstractObjectConverter implements ObjectConverterInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(AbstractObjectNode $node)
	{
		throw new ResumeSerializationException();
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(AbstractObjectNode $node)
	{
		throw new ResumeSerializationException();
	}
}
