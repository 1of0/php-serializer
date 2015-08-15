<?php


namespace OneOfZero\Json;


use OneOfZero\Json\Annotations\InclusionStrategy;

class Configuration
{
	/**
	 * @var bool $prettyPrint
	 */
	public $prettyPrint = false;

	/**
	 * @var JsonConverterInterface[] $jsonConverters
	 */
	public $jsonConverters = [];

	/**
	 * @var int $defaultInclusionStrategy
	 */
	public $defaultInclusionStrategy = InclusionStrategy::IMPLICIT;

	/**
	 * @var bool $includeNullValues
	 */
	public $includeNullValues = false;

	/**
	 * @var int $maxDepth
	 */
	public $maxDepth = 32;



	/**
	 * @param JsonConverterInterface $converter
	 */
	public function addConverter(JsonConverterInterface $converter)
	{
		$this->jsonConverters[] = $converter;
	}
}