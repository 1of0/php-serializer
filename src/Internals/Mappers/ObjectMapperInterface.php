<?php
namespace OneOfZero\Json\Internals\Mappers;

/**
 * Defines a mapper that maps the serialization metadata for a class.
 */
interface ObjectMapperInterface extends MapperInterface
{
	/**
	 * Should return a boolean value indicating whether or not members must be explicitly included.
	 *
	 * @return bool
	 */
	public function wantsExplicitInclusion();

	/**
	 * Should return a boolean value indicating whether or not the serialized representation of the class should bear
	 * library-specific metadata.
	 *
	 * @return bool
	 */
	public function wantsNoMetadata();

	/**
	 * Returns member mappers for all class properties and methods.
	 *
	 * @return MemberMapperInterface[]
	 */
	public function getMembers();

	/**
	 * Returns member mappers for all class properties.
	 *
	 * @return MemberMapperInterface[]
	 */
	public function getProperties();

	/**
	 * Returns a member mapper for the property with the provided name.
	 *
	 * @param string $name
	 *
	 * @return MemberMapperInterface|null
	 */
	public function getProperty($name);

	/**
	 * Returns member mappers for all class methods.
	 *
	 * @return MemberMapperInterface[]
	 */
	public function getMethods();

	/**
	 * Returns a member mapper for the method with the provided name.
	 *
	 * @param string $name
	 *
	 * @return MemberMapperInterface|null
	 */
	public function getMethod($name);
}