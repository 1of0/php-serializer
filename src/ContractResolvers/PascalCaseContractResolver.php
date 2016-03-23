<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\ContractResolvers;

use OneOfZero\Json\Mappers\ContractMemberMapper;
use OneOfZero\Json\Nodes\MemberNode;

class PascalCaseContractResolver extends AbstractContractResolver
{
	/**
	 * {@inheritdoc}
	 */
	public function createMemberContract(MemberNode $member)
	{
		$name = $this->pascalize($member->getMapper()->getName());
		
		return new ContractMemberMapper($name);
	}

	/**
	 * @param string $name
	 * 
	 * @return string
	 */
	private function pascalize($name)
	{
		$words = $this->splitIntoWords($name);
		
		$buffer = '';
		foreach ($words as $word)
		{
			$buffer .= ucfirst($word);
		}
		return $buffer;
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
