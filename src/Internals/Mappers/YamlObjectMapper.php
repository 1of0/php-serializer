<?php

namespace OneOfZero\Json\Internals\Mappers;

class YamlObjectMapper extends YamlAbstractMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;

	const METADATA_ATTR = 'metadata';
	const EXPLICIT_INCLUSION_ATTR = 'explicit';
	
	/**
	 * {@inheritdoc}
	 */
	public function wantsExplicitInclusion()
	{
		if ($this->hasAttribute(self::EXPLICIT_INCLUSION_ATTR))
		{
			return (bool)$this->readAttribute(self::EXPLICIT_INCLUSION_ATTR);
		}
		
		return $this->getBase()->wantsExplicitInclusion();
	}

	/**
	 * {@inheritdoc}
	 */
	public function wantsNoMetadata()
	{
		if ($this->hasAttribute(self::METADATA_ATTR))
		{
			return !((bool)$this->readAttribute(self::METADATA_ATTR));
		}
		
		return $this->getBase()->wantsNoMetadata();
	}
}
