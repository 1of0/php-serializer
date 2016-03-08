<?php

namespace OneOfZero\Json\Internals\Contexts;

class AnonymousObjectContext extends AbstractObjectContext
{
	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withInstanceMember($name, $value)
	{
		$new = clone $this;
		$new->instance->{$name} = $value;
		return $new;
	}
}