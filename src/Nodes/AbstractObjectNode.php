<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Nodes;

use OneOfZero\Json\Mappers\ObjectMapperInterface;
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
	 * @var ObjectMapperInterface $mapper
	 */
	protected $mapper;
	
	/**
	 * @var array $metadata
	 */
	protected $metadata = [];
	
	/**
	 * @param MemberNode $node
	 *
	 * @return self
	 */
	public function withInstanceMember(MemberNode $node)
	{
		$new = clone $this;

		if ($node->getValue() !== null)
		{
			$node->getMapper()->setValue($new->getInstance(), $node->getValue());
		}

		return $new;
	}
	
	/**
	 * @param mixed $serializedInstance
	 *
	 * @return self
	 */
	public function withSerializedInstance($serializedInstance)
	{
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
		$new = clone $this;
		
		if ($this->serializedInstance === null || is_array($this->serializedInstance))
		{
			$new->serializedInstance[$name] = $value;
		}
		elseif ($this->serializedInstance instanceof stdClass)
		{
			$new->serializedInstance->{$name} = $value;
		}
		else
		{
			throw new RuntimeException('Cannot set members when the serialized instance is not an array stdClass or type');
		}
		
		return $new;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withMetadata($key, $value)
	{
		$new = clone $this;
		$new->metadata[$key] = $value;
		return $new;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function getSerializedMemberValue($name)
	{
		if (is_array($this->serializedInstance) && array_key_exists($name, $this->serializedInstance))
		{
			return $this->serializedInstance[$name];
		}
		elseif ($this->serializedInstance instanceof stdClass && property_exists($this->serializedInstance, $name))
		{
			return $this->serializedInstance->{$name};
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param bool $includeMetadata
	 *
	 * @return mixed
	 */
	public function getSerializedInstance($includeMetadata = true)
	{		
		if ($includeMetadata)
		{
			if (is_array($this->serializedInstance) || $this->serializedInstance instanceof stdClass)
			{
				return array_merge($this->metadata, (array)$this->serializedInstance);
			}
		}
		
		return $this->serializedInstance;
	}

	/**
	 * @return bool
	 */
	public function isRecursiveInstance()
	{
		if ($this->instance === null)
		{
			return false;
		}

		$parent = $this->parent;

		while ($parent !== null)
		{
			if ($parent instanceof AbstractObjectNode)
			{
				$parentInstance = $parent->getInstance();

				if ($parentInstance !== null && is_object($parentInstance) && $parentInstance === $this->instance)
				{
					return true;
				}
			}
			$parent = $parent->parent;
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function getDepth()
	{
		if ($this->instance === null)
		{
			return 0;
		}

		$count = 0;
		$parent = $this->parent;

		while ($parent !== null)
		{
			if ($parent instanceof AbstractObjectNode)
			{
				$count++;
			}
			$parent = $parent->parent;
		}

		return $count;
	}

	#region // Generic immutability helpers
	
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
	 * @param ObjectMapperInterface $mapper
	 *
	 * @return self
	 */
	public function withMapper(ObjectMapperInterface $mapper)
	{
		$new = clone $this;
		$new->mapper = $mapper;
		return $new;
	}
	
	#endregion

	#region // Generic getters and setters
	
	/**
	 * @return mixed
	 */
	public function getInstance()
	{
		return $this->instance;
	}

	/**
	 * @return ObjectMapperInterface
	 */
	public function getMapper()
	{
		return $this->mapper;
	}
	
	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}
	
	#endregion
}
