<?php
namespace nixn\php;

/**
 * @template T
 * @type With<T>
 * @since 1.1
 */
final class With
{
	/**
	 * @template W
	 * @param class-string<W> $class the class name to create
	 * @param mixed ...$args constructor arguments
	 * @return With<W> the created object, wrapped into `With`
	 * @since 1.1
	 */
	public static function new(string $class, mixed ...$args): With
	{
		return new self(new $class(...$args));
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
