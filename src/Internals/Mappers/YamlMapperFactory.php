<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;
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
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		$objectMapping = array_key_exists($reflector->name, $this->mapping) ? $this->mapping[$reflector->name] : [];

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
		$parentMapping = $memberParent->getMapping();
		$memberMapping = array_key_exists($reflector->name, $parentMapping) ? $parentMapping[$reflector->name] : [];
		
		$mapper = new YamlMemberMapper($memberMapping);

		$mapper->setTarget($reflector);
		$mapper->setMemberParent($memberParent);
		$mapper->setBase($this->getParent()->mapMember($reflector, $memberParent->getBase()));

		return $mapper;
	}
}