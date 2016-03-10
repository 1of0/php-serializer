<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Symfony\Component\Yaml\Parser;

class YamlMapperFactory implements MapperFactoryInterface
{
	use BaseFactoryTrait;

	/**
	 * @var array $mapping
	 */
	private $mapping;

	/**
	 * @var array $aliases
	 */
	private $aliases;

	/**
	 * @param string $mappingFile
	 */
	public function __construct($mappingFile)
	{
		if (!class_exists(Parser::class))
		{
			throw new RuntimeException('The package symfony/yaml is required to be able to use the yaml mapper');
		}

		if (!file_exists($mappingFile))
		{
			throw new RuntimeException("File \"$mappingFile\" does not exist");
		}

		$parser = new Parser();
		
		$this->mapping = $parser->parse(file_get_contents($mappingFile));
		$this->aliases = array_key_exists('@use', $this->mapping) ? $this->mapping['@use'] : [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		$objectMapping = $this->getObjectMapping($reflector->name);

		$mapper = new YamlObjectMapper($objectMapping);

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setBase($this->getParent()->mapObject($reflector));

		return $mapper;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param YamlObjectMapper $memberParent
	 */
	public function mapMember($reflector, ObjectMapperInterface $memberParent)
	{
		$objectMapping = $memberParent->getMapping();
		$memberMapping = $this->getMemberMapping($reflector, $objectMapping);
		
		$mapper = new YamlMemberMapper($memberMapping);

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setMemberParent($memberParent);
		$mapper->setBase($this->getParent()->mapMember($reflector, $memberParent->getBase()));

		return $mapper;
	}

	/**
	 * @param string $alias
	 * 
	 * @return string
	 */
	public function resolveAlias($alias)
	{
		return array_key_exists($alias, $this->aliases) ? $this->aliases[$alias] : $alias;
	}

	/**
	 * @param string $class
	 * 
	 * @return string
	 */
	public function findAlias($class)
	{
		$alias = array_search($class, $this->aliases);
		return ($alias === false) ? $class : $alias;
	}

	/**
	 * @param string $class
	 * 
	 * @return array
	 */
	private function getObjectMapping($class)
	{
		$alias = $this->findAlias($class);
		return array_key_exists($alias, $this->mapping) ? $this->mapping[$alias] : [];
	}

	/**
	 * @param ReflectionProperty|ReflectionMethod $reflector
	 * @param array $objectMapping
	 * 
	 * @return array
	 */
	private function getMemberMapping($reflector, array $objectMapping)
	{
		if ($reflector instanceof ReflectionProperty
			&& array_key_exists('properties', $objectMapping)
			&& array_key_exists($reflector->name, $objectMapping['properties']))
		{
			return $objectMapping['properties'][$reflector->name];
		}

		if ($reflector instanceof ReflectionMethod
			&& array_key_exists('methods', $objectMapping)
			&& array_key_exists($reflector->name, $objectMapping['methods']))
		{
			return $objectMapping['methods'][$reflector->name];
		}

		return [];
	}
}
