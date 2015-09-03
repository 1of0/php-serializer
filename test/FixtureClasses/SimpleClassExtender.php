<?php


namespace OneOfZero\Json\Test\FixtureClasses;


class SimpleClassExtender extends SimpleClass
{
	public $baz;

	public function __construct($foo = null, $bar = null, $baz = null)
	{
		parent::__construct($foo, $bar);
		$this->baz = $baz;
	}
}