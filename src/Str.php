<?php
namespace nixn\php;

/** @since 1.0 */
final class Str
{
	/** @codeCoverageIgnore */
	private function __construct() {}

	/**
	 * Trims a string prefix when it matches. Example: `trim_prefix("Hello all", "Hello ") === "all"`.
	 * @param string $string the original string
	 * @param string $prefix the prefix to remove
	 * @return string the resulting string
	 * @since 1.0
	 */
	public static function trim_prefix(string $string, string $prefix): string
	{
		return $prefix === '' ? $string : preg_replace('#^' . preg_quote($prefix, '#') . '#', '', $string); // @phpstan-ignore return.type
	}

	/**
	 * Trims a string suffix when it matches. Example: `trim_suffix("Hello all", " all") === "Hello"`.
	 * @param string $string the original string
	 * @param string $suffix the suffix to remove
	 * @return string the resulting string
	 * @since 1.0
	 */
	public static function trim_suffix(string $string, string $suffix): string
	{
		return $suffix === '' ? $string : preg_replace('#' . preg_quote($suffix, '#') . '$#', '', $string); // @phpstan-ignore return.type
	}
}
