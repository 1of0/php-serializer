<?php

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\NameFilterInterface;

class PascalCaseNameFilter implements NameFilterInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName($name)
	{
		return implode('', array_map(function ($item) { return ucfirst($item); }, $this->splitIntoWords($name)));
	}

	/**
	 * @param string $string
	 *
	 * @return array
	 */
	private function splitIntoWords($string)
	{
		$words = [];
		$buffer = '';

		for ($i = 0; $i < strlen($string); $i++)
		{
			if (ctype_upper($string[$i]) && $i > 0)
			{
				$words[] = $buffer;
				$buffer = '';
			}

			$buffer .= strtolower($string[$i]);
		}

		if (strlen($buffer) > 0)
		{
			$words[] = $buffer;
		}

		return $words;
	}
}