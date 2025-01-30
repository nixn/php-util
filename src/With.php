<?php
namespace nixn\php;

/** @since 1.1 */
final class With
{
	/**
	 * @template T
	 * @param class-string<T> $class
	 * @param mixed ...$args
	 * @return T
	 * @since 1.1
	 */
	public static function new(string $class, mixed ...$args): object
	{
		return new $class(...$args);
	}

	public function __construct(
		public object $object,
	)
	{}

	public function __call(string $method, array $args)
	{
		$this->object->{$method}(...$args);
		return $this;
	}

	public function __toString(): string
	{
		return "$this->object";
	}
}
