<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\ContractResolvers;

use OneOfZero\Json\Exceptions\NotSupportedException;
use OneOfZero\Json\Mappers\ContractMemberMapper;
use OneOfZero\Json\Nodes\MemberNode;
use Stringy\Stringy as S;
use Stringy\Stringy;

class PropertyNameContractResolver extends AbstractContractResolver
{
	const PASCAL_CASE = 'PASCAL_CASE';
	const CAMEL_CASE = 'CAMEL_CASE';
	const SNAKE_CASE = 'SNAKE_CASE';
	const HYPHEN_CASE = 'HYPHEN_CASE';

	/**
	 * @var string $serializedStyle
	 */
	private $serializedStyle;

	/**
	 * @var string $deserializedStyle
	 */
	private $deserializedStyle;

	/**
	 * @param string $deserializedStyle
	 * @param string $serializedStyle
	 *
	 * @throws NotSupportedException
	 */
	public function __construct($deserializedStyle = self::CAMEL_CASE, $serializedStyle = self::PASCAL_CASE)
	{
		if (!class_exists(S::class))
		{
			throw new NotSupportedException('PascalCaseContractResolver requires the package "danielstjules/stringy"');
		}

		$this->deserializedStyle = $deserializedStyle;
		$this->serializedStyle = $serializedStyle;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createSerializingMemberContract(MemberNode $member)
	{
		$name = $member->getMapper()->getDeserializedName();
		return new ContractMemberMapper(
			$this->applyStyle(S::create($name), $this->deserializedStyle)->__toString(),
			$this->applyStyle(S::create($name), $this->serializedStyle)->__toString()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function createDeserializingMemberContract(MemberNode $member)
	{
		$name = $member->getMapper()->getSerializedName();
		return new ContractMemberMapper(
			$this->applyStyle(S::create($name), $this->deserializedStyle)->__toString(),
			$this->applyStyle(S::create($name), $this->serializedStyle)->__toString()
		);
	}

	/**
	 * @param Stringy $name
	 * @param string $style
	 *
	 * @return Stringy
	 *
	 * @throws NotSupportedException
	 */
	private function applyStyle(Stringy $name, $style)
	{
		switch ($style)
		{
			case self::PASCAL_CASE: return $name->upperCamelize();
			case self::CAMEL_CASE: return $name->camelize();
			case self::SNAKE_CASE: return $name->underscored();
			case self::HYPHEN_CASE: return $name->dasherize();
		}

		throw new NotSupportedException("Style \"$style\" is not supported");
	}
}
