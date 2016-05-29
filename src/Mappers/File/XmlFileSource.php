<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\File;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use RuntimeException;

class XmlFileSource extends FileSource
{
	/**
	 * @param string $file
	 */
	public function __construct($file)
	{
		parent::__construct($file);

		$document = new DOMDocument();
		
		if ($document->load($file) === false)
		{
			throw new RuntimeException("Failed parsing XML in \"$file\"");
		}

		try
		{
			$xpath = new DOMXPath($document);

			/** @var DOMElement $alias */
			foreach ($xpath->query('/mapping/use') as $alias)
			{
				$this->aliases[$alias->getAttribute('alias')] = $alias->getAttribute('fqn');
			}

			/** @var DOMElement $class */
			foreach ($xpath->query('/mapping/class') as $class)
			{
				$className = $class->getAttribute('name');

				/** @var DOMAttr $attribute */
				foreach ($class->attributes as $attribute)
				{
					$this->mapping[$className][$attribute->name] = $attribute->value;
				}

				/** @var DOMElement $converters */
				foreach ($xpath->query('converters[1]', $class) as $converters)
				{
					$this->mapping[$className]['converters']['serializer'] = $converters->getAttribute('serializer');
					$this->mapping[$className]['converters']['deserializer'] = $converters->getAttribute(
						'deserializer'
					);
				}

				/** @var DOMElement $property */
				foreach ($xpath->query('properties/*', $class) as $property)
				{
					/** @var DOMAttr $attribute */
					foreach ($property->attributes as $attribute)
					{
						$this->mapping[$className]['properties'][$property->tagName][$attribute->name] = $attribute->value;
					}

					/** @var DOMElement $converters */
					foreach ($xpath->query('converters[1]', $property) as $converters)
					{
						$this->mapping[$className]['properties'][$property->tagName]['converters']['serializer'] = $converters->getAttribute(
							'serializer'
						);
						$this->mapping[$className]['properties'][$property->tagName]['converters']['deserializer'] = $converters->getAttribute(
							'deserializer'
						);
					}
				}

				/** @var DOMElement $method */
				foreach ($xpath->query('methods/*', $class) as $method)
				{
					/** @var DOMAttr $attribute */
					foreach ($method->attributes as $attribute)
					{
						$this->mapping[$className]['methods'][$method->tagName][$attribute->name] = $attribute->value;
					}
				}
			}
		}
		catch (Exception $e)
		{
			throw new RuntimeException("Failed parsing XML in \"$file\"");
		}
	}
}
