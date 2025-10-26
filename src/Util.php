<?php
namespace nixn\php;

/**
 * Miscellaneous utilities.
 *
 * @since 1.0
 */
final class Util
{
	/** @codeCoverageIgnore */
	private function __construct() {}

	/**
	 * Just returns the input value. Useful in functional programming style.
	 * @param mixed $v the input value
	 * @return mixed the input value
	 * @since 1.0
	 */
	public static function identity(mixed $v): mixed
	{
		return $v;
	}

	private static function nots(bool $null, bool $false, bool $empty, bool $zero): array // @phpstan-ignore missingType.iterableValue
	{
		$nots = [];
		if ($null) $nots[] = null;
		if ($false) $nots[] = false;
		if ($empty) { $nots[] = ''; $nots[] = []; }
		if ($zero) { $nots[] = 0; $nots[] = 0.0; }
		return $nots;
	}

	/**
	 * Maps a value through a function, iff both the value and the function are 'set'. For the function this means it is not null,
	 * for the value it means, it is not one of the $nots (compared strictly).
	 * @template T type of the input value
	 * @template U type of the mapped value
	 * @param ?T $v the input value to be mapped
	 * @param ?callable(?T): U $fn the mapping function. `null` means {@link identity()} (return $v as-is)
	 * @param bool $null_on_not whether to return null iff the input value matches one of the $nots
	 * @param bool $func whether to test $fn on null as a 'not' value, too.
	 *        if true, $fn === null means return $v unmapped (only useful with $null_on_not === true, which returns null then);
	 *        if false, $fn === null means $v is returned unchanged (like {@link identity()}).
	 * @param mixed ...$nots the values which count as a 'not' value (mostly useful: null, false, '', 0). see {@link map()}
	 * @return U|T|null the possibly mapped value
	 * @since 1.0
	 */
	public static function map_nots(mixed $v, ?callable $fn = null, bool $null_on_not = false, bool $func = false, mixed ... $nots): mixed
	{
		if (in_array($v, $nots, true) || ($func && $fn === null))
			return $null_on_not ? null : $v;
		return $fn === null ? $v : $fn($v);
	}

	/**
	 * Checks $v on (selectable) common falsy values and returns $fn($v) or $v (when $fn === null) when not falsy. Returns $v or null otherwise.
	 * By default only null is selected as a falsy value.
	 * @template T type of the input value
	 * @template U type of the mapped value
	 * @param ?T $v
	 * @param ?callable(?T): ?U $fn the mapping function. `null` means {@link identity()} (return $v as-is)
	 * @param bool $null_on_not return null on falsy (as selected by $func, $null, $false, $empty and $zero)
	 * @param bool $func whether $fn === null means falsy too
	 * @param bool $null whether null is a falsy value
	 * @param bool $false whether false is a falsy value
	 * @param bool $empty whether an empty string is a falsy value
	 * @param bool $zero whether 0 (zero) is a falsy value
	 * @return U|T|null the possibly mapped value
	 * @since 1.0
	 */
	public static function map(mixed $v, ?callable $fn = null, bool $null_on_not = false, bool $func = false, bool $null = true, bool $false = false, bool $empty = false, bool $zero = false): mixed
	{
		return self::map_nots($v, $fn, $null_on_not, $func, ...self::nots(null: $null, false: $false, empty: $empty, zero: $zero));
	}

	/**
	 * Like {@link when()}, but the the falsy values are given as arguments (`$nots`).
	 * @since 1.0
	 */
	public static function when_nots(mixed $test, mixed $v, ?callable $fn = null, mixed ... $nots): mixed
	{
		if (!self::map_nots($test, fn() => true, true, false, ...$nots))
			return null;
		if ($v instanceof \Closure)
			$v = $v($test);
		return $fn !== null ? $fn($v) : $v;
	}

	/**
	 * When `$test` is a falsy value (as selected by `$null`, `$false`, `$empty` and `$zero`), returns `null`.
	 * Otherwise returns `$v`, which can be a function, which is called then (the usual use case for this).
	 * This function is like the ternary operator: `$test ? $fn($v) : null`, but it handles `$fn` and `$v` carefully
	 * and relieves the caller to use a temporary variable for a dynamic test value and more tests
	 * (f.e. `when($obj->get(), identity(...), empty: true)` returns the get() result only if it is not null, false or the empty string)
	 * @param mixed $test the value to test on
	 * @param mixed $v the value to return. can be a callable, which is called then with $test as the only argument.
	 * @param callable|null $fn an optional function (only Closure, not just callable) to map the value through (after an optional `$v = $v()` call)
	 * @param bool $null whether null is a falsy value
	 * @param bool $false whether false is a falsy value
	 * @param bool $empty whether an empty string is a falsy value
	 * @param bool $zero whether 0 (zero) is a falsy value
	 * @return mixed the value, possibly mapped, or null if `$test` was falsy
	 * @since 1.0
	 */
	public static function when(mixed $test, mixed $v, ?callable $fn = null, bool $null = true, bool $false = true, bool $empty = false, bool $zero = false): mixed
	{
		return self::when_nots($test, $v, $fn, ...self::nots($null, $false, $empty, $zero));
	}

	/**
	 * For any node in a tree structure, get all parents (possibly up to a specific one) and return them from top to bottom
	 * as an iterable (Generator): `foreach (tree_path($node) as $parent_node) {...}`
	 * @template T the node type
	 * @param ?T $element the starting node
	 * @param callable(T): ?T $get_parent the function to get the parent node of a child node
	 * @param ?callable(T): bool $while an optional check when to stop (`$while` must return false to not rise further in the tree)
	 * @return \Generator<T> the iterable, directly usable in `foreach(...)`
	 * @since 1.0
	 */
	public static function tree_path(mixed $element, callable $get_parent, ?callable $while = null): \Generator
	{
		if ($element !== null && ($while === null || $while($element)))
		{
			yield from self::tree_path($get_parent($element), $get_parent, $while);
			yield $element;
		}
	}

	/**
	 * Returns a function (Closure) for a constructor call.
	 * PHP does not support `new MyClass(...)`, so this is a workaround: Util::new(MyClass::class)`
	 * @template T the class type
	 * @param class-string<T> $class the class name
	 * @return \Closure(mixed ...$args): T the function (Closure) to create the class
	 * @since 1.0
	 */
	public static function new(string $class): \Closure
	{
		return static fn(...$args) => new $class(...$args);
	}
}
