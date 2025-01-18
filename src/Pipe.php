<?php
namespace nixn\php;

/**
 * A Pipe wraps an intial value and pipes it through any function which modifies it. The resulting value can be accessed at the end.
 *
 * Example:
 * ```
 * echo (new Pipe('NOW')) // wrap initial value
 * (strtolower(...)) // map through strtolower() => 'now'
 * ->new(DateTimeImmutable::class, Pipe::PLACEHOLDER, DateTimeZone::UTC) // create class
 * ->format("Y_m_d") // => '2025_01_01' (that was 'now' not long ago...)
 * (str_replace(...), '_', '-') // => '2025-01-01'
 * (explode(...), '-', Pipe::PLACEHOLDER, 2) // => ['2025', '01-01']
 * ->get(0) // => '2025'
 * (intval(...)) // => 2025 (still wrapped)
 * ->value; // unwrap => 2025
 * // prints: 2025
 * ```
 */
final class Pipe
{
	const PLACEHOLDER = Partial::PLACEHOLDER;

	public function __construct(
		public mixed $value,
	)
	{}

	public function __call(string $method, array $args): self
	{
		$this->value = $this->value->{$method}(...$args);
		return $this;
	}

	public function __invoke(callable $callable, mixed ... $args): self
	{
		$add = true;
		foreach ($args as $k => $v)
			if ($v === self::PLACEHOLDER)
			{
				$args[$k] = $this->value;
				$add = false;
				break;
			}
		if ($add)
			$args[] = $this->value;
		$this->value = $callable(...$args);
		return $this;
	}

	/**
	 * calls `new $class(...)` with the current value placed last or to the first PLACEHOLDER argument (only once!)
	 * @param class-string $class
	 */
	public function new(string $class, mixed ... $args): self
	{
		return $this(fn(mixed ... $final_args) => new $class(...$final_args), ...$args);
	}

	/**
	 * Returns the class field or array element with key `$key`.
	 * @param string|int $key the key. when it is an integer, array access is used automatically.
	 * @param bool $array_access whether to use array access
	 */
	public function get(string|int $key, bool $array_access = false): self
	{
		$this->value = $array_access || is_int($key) ? $this->value[$key] : $this->value->{$key};
		return $this;
	}

	public function __toString(): string
	{
		// TODO: Implement __toString() method.
	}
}
