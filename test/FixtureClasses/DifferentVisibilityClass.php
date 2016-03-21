<?php

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\Ignore;

class DifferentVisibilityClass
{
	/**
	 * @var string $publicProperty
	 */
	public $publicProperty;

	/**
	 * @var string $protectedProperty
	 */
	protected $protectedProperty;

	/**
	 * @var string $privateProperty
	 */
	private $privateProperty;

	/**
	 * @Ignore
	 * @var string $_publicValue
	 */
	private $_publicValue;

	/**
	 * @Ignore
	 * @var string $_protectedValue
	 */
	private $_protectedValue;

	/**
	 * @Ignore
	 * @var string $_privateValue
	 */
	private $_privateValue;

	/**
	 * @param string $publicProperty
	 * @param string $protectedProperty
	 * @param string $privateProperty
	 * @param string $_publicValue
	 * @param string $_protectedValue
	 * @param string $_privateValue
	 */
	public function __construct(
		$publicProperty = null,
		$protectedProperty = null,
		$privateProperty = null,
		$_publicValue = null,
		$_protectedValue = null,
		$_privateValue = null
	) {
		$this->publicProperty = $publicProperty;
		$this->protectedProperty = $protectedProperty;
		$this->privateProperty = $privateProperty;
		$this->_publicValue = $_publicValue;
		$this->_protectedValue = $_protectedValue;
		$this->_privateValue = $_privateValue;
	}

	/**
	 * @return string
	 */
	public function getPublicMethod()
	{
		return $this->_publicValue;
	}

	/**
	 * @return string
	 */
	protected function getProtectedMethod()
	{
		return $this->_protectedValue;
	}

	/**
	 * @return string
	 */
	private function getPrivateMethod()
	{
		return $this->_privateValue;
	}

	/**
	 * @param string $value
	 */
	public function setPublicMethod($value)
	{
		$this->_publicValue = $value;
	}

	/**
	 * @param string $value
	 */
	protected function setProtectedMethod($value)
	{
		$this->_protectedValue = $value;
	}

	/**
	 * @param string $value
	 */
	private function setPrivateMethod($value)
	{
		$this->_privateValue = $value;
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function getPublicProperty()
	{
		return $this->publicProperty;
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function getProtectedProperty()
	{
		return $this->protectedProperty;
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function getPrivateProperty()
	{
		return $this->privateProperty;
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function _getPublicMethod()
	{
		return $this->getPublicMethod();
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function _getProtectedMethod()
	{
		return $this->getProtectedMethod();
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function _getPrivateMethod()
	{
		return $this->getPrivateMethod();
	}

	/**
	 * @Ignore
	 * @param string $value
	 */
	public function _setPublicMethod($value)
	{
		$this->setPublicMethod($value);
	}

	/**
	 * @Ignore
	 * @param string $value
	 */
	public function _setProtectedMethod($value)
	{
		$this->setProtectedMethod($value);
	}

	/**
	 * @Ignore
	 * @param string $value
	 */
	public function _setPrivateMethod($value)
	{
		$this->setPrivateMethod($value);
	}
}