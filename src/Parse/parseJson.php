<?php

namespace Hug\Parse;


class parseJson
{
	 public static function parseJson(): array
    {
		$json = file_get_contents('/../tests/composer.json');
		$json_data = json_decode($json, true);
		$extensions = $json_data['require'];
		$keys = array_keys($extensions);
		$pattern = '/^ext-/i';
		$array = preg_grep($pattern, $keys);
        return $array;
    }
}
