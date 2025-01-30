<?php
namespace nixn\php;

/** @since 1.0 */
final class Arr
{
	private function __construct()
	{}

	/**
	 * Returns a new array with only the key => value mappings left, whose keys are in $keys.
	 * @param array $array the source array
	 * @param string|int ...$keys the keys for the values to retain
	 * @return array the resulting array
	 * @since 1.0
	 */
	public static function pick(array $array, string|int ...$keys): array
	{
		return array_filter($array, fn(string|int $k) => in_array($k, $keys, true), ARRAY_FILTER_USE_KEY);
	}

	/**
	 * Searches for a value in the array, based on the predicate, returning it (or its key) or null, when not found.
	 * @param array $array the source array
	 * @param callable(mixed, string|int): bool $predicate called with every value (until found)
	 * @param bool $return_key whether to return the key instead of the value
	 * @return mixed the value or the key for a found item, null otherwise
	 * @since 1.0
	 */
	public static function find_by(array $array, callable $predicate, bool $return_key = false): mixed
	{
		foreach ($array as $k => &$v)
			if ($predicate($v, $k))
				return $return_key ? $k : $v;
		return null;
	}

	/**
	 * Like array_reduce(), but the callback passes the key of the element, too.
	 * @param callable(mixed $carry, mixed $value, string|int $key): mixed $callback
	 * @since 1.0
	 */
	public static function reduce(array $array, callable $callback, mixed $initial = null): mixed
	{
		$first = true;
		foreach ($array as $k => $v) {
			if ($first && $initial === null) {
				$first = false;
				$initial = $v;
				continue;
			}
			$initial = $callback($initial, $v, $k);
		}
		return $initial;
	}

	/**
	 * Like implode()/join() in legacy syntax, but outputs the keys too and takes an additional parameter `$kv_sep`.
	 * @param string $sep the separator of elements
	 * @param string $kv_sep the separator for key and value
	 * @since 1.0
	 */
	public static function kvjoin(array $data, string $sep = ', ', string $kv_sep = '='): string
	{
		$first = true;
		$str = '';
		foreach ($data as $k => $v) {
			if ($first) $first = false;
			else $str .= $sep;
			$str .= $k . $kv_sep . $v;
		}
		return $str;
	}

	/**
	 * Returns the first mapping (element) of an array as key and value in an array, or null/null if the array is empty.
	 * Array destructuring is a very convenient way to handle the result: `['k' => $key, 'v' => $value] = array_first($array)`
	 * @param array $array the source array
	 * @return array ['k' => <key>/null, 'v' => <value>/null]
	 * @since 1.0
	 */
	public static function first(array $array): array
	{
		foreach ($array as $k => $v)
			return compact('k', 'v');
		return ['k' => null, 'v' => null];
	}

	/**
	 * Searches for a mapping in the array whose key is one of $keys, returns the key and the value or null/null (if not found) as an array.
	 * Array destructuring is a very convenient way to handle the result: `['k' => $key, 'v' => $value] = array_find($array, 'key1', 'key2')`
	 * @param array $array the source array
	 * @param string|int ...$ks the keys
	 * @return array ['k' => <key>/null, 'v' => <value>/null]
	 * @since 1.0
	 */
	public static function find(array $array, string|int ...$ks): array
	{
		foreach ($array as $k => $v)
			if (in_array($k, $ks, true))
				return compact('k', 'v');
		return ['k' => null, 'v' => null];
	}
}