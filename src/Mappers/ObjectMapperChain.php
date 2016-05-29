<?php

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Mappers\Null\NullObjectMapper;
use ReflectionMethod;
use ReflectionProperty;

class ObjectMapperChain extends AbstractMapperChain
{
	/**
	 * @return MemberMapperChain[]
	 */
	public function mapMembers()
	{
		return array_merge(
			$this->parseMembers($this->target->getProperties()),	
			$this->parseMembers($this->target->getMethods())	
		);
	}

	/**
	 * @param ReflectionProperty[]|ReflectionMethod[] $members
	 * 
	 * @return MemberMapperChain[]
	 */
	private function parseMembers(array $members)
	{
		$parsed = [];
		
		foreach ($members as $member)
		{
			// Skip magic properties/methods
			if (strpos($member->name, '__') === 0)
			{
				continue;
			}

			$parsed[] = new MemberMapperChain($member, $this->factoryChain, $this);
		}
		
		return $parsed;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getMapper($index)
	{
		if ($this->chain[$index] === null)
		{
			$factory = $this->factoryChain->getFactory($index);
			$this->chain[$index] = $factory->mapObject($this->target, $this);
		}
		
		return $this->chain[$index];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getNullMapper()
	{
		return new NullObjectMapper(null, $this->target, $this);
	}
}
