<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Nodes;

use RuntimeException;
use stdClass;

class AbstractObjectNode extends AbstractNode
{
	/**
	 * @var mixed $instance
	 */
	protected $instance;

	/**
	 * @var array $serializedInstance
	 */
	protected $serializedInstance;

	/**
	 * @param object $instance
	 *
	 * @return self
	 */
	public function withInstance($instance)
	{
		$new = clone $this;
		$new->instance = $instance;
		return $new;
	}

	/**
	 * @param mixed $serializedInstance
	 *
	 * @return self
	 */
	public function withSerializedInstance($serializedInstance)
	{
		if ($serializedInstance instanceof stdClass)
		{
			$serializedInstance = (array)$serializedInstance;
		}

		$new = clone $this;
		$new->serializedInstance = $serializedInstance;
		return $new;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withSerializedInstanceMember($name, $value)
	{
		if ($this->serializedInstance !== null && !is_array($this->serializedInstance))
		{
			throw new RuntimeException('Cannot set members when the serialized instance is not an array type');
		}

		$new = clone $this;
		$new->serializedInstance[$name] = $value;
		return $new;
	}

	/**
	 * @return mixed
	 */
	public function getInstance()
	{
		return $this->instance;
	}

	/**
	 * @return array
	 */
	public function getSerializedInstance()
	{
		return $this->serializedInstance;
	}
}
