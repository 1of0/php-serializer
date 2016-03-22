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
	 * @var string $__publicValue
	 */
	private $__publicValue;

	/**
	 * @var string $__protectedValue
	 */
	private $__protectedValue;

	/**
	 * @var string $__privateValue
	 */
	private $__privateValue;

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
		$this->__publicValue = $_publicValue;
		$this->__protectedValue = $_protectedValue;
		$this->__privateValue = $_privateValue;
	}

	/**
	 * @return string
	 */
	public function getPublicMethod()
	{
		return $this->__publicValue;
	}

	/**
	 * @return string
	 */
	protected function getProtectedMethod()
	{
		return $this->__protectedValue;
	}

	/**
	 * @return string
	 */
	private function getPrivateMethod()
	{
		return $this->__privateValue;
	}

	/**
	 * @param string $value
	 */
	public function setPublicMethod($value)
	{
		$this->__publicValue = $value;
	}

	/**
	 * @param string $value
	 */
	protected function setProtectedMethod($value)
	{
		$this->__protectedValue = $value;
	}

	/**
	 * @param string $value
	 */
	private function setPrivateMethod($value)
	{
		$this->__privateValue = $value;
	}

	/**
	 * @return string
	 */
	public function __getPublicProperty()
	{
		return $this->publicProperty;
	}

	/**
	 * @return string
	 */
	public function __getProtectedProperty()
	{
		return $this->protectedProperty;
	}

	/**
	 * @return string
	 */
	public function __getPrivateProperty()
	{
		return $this->privateProperty;
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function __getPublicMethod()
	{
		return $this->getPublicMethod();
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function __getProtectedMethod()
	{
		return $this->getProtectedMethod();
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function __getPrivateMethod()
	{
		return $this->getPrivateMethod();
	}

	/**
	 * @Ignore
	 * @param string $value
	 */
	public function __setPublicMethod($value)
	{
		$this->setPublicMethod($value);
	}

	/**
	 * @Ignore
	 * @param string $value
	 */
	public function __setProtectedMethod($value)
	{
		$this->setProtectedMethod($value);
	}

	/**
	 * @Ignore
	 * @param string $value
	 */
	public function __setPrivateMethod($value)
	{
		$this->setPrivateMethod($value);
	}
}
