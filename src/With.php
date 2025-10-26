<?php
namespace nixn\php;

/**
 * An object wrapper for chainable method calls.
 *
 * @template T
 * @type With<T>
 * @since 1.1
 */
final class With
{
	/**
	 * @template W of object
	 * @param class-string<W> $class the class name to create
	 * @param mixed ...$args constructor arguments
	 * @return With<W> the created object, wrapped into `With`
	 * @since 1.1
	 */
	public static function new(string $class, mixed ...$args): With
	{
		return new self(new $class(...$args)); // @phpstan-ignore return.type
	}

	/**
	 * @template W of object
	 * @param W $object the object to wrap
	 */
	public function __construct(
		public object $object,
	)
	{}

	/**
	 * @return $this
	 * @phpstan-ignore missingType.iterableValue
	 */
	public function __call(string $method, array $args): self
	{
		$this->object->{$method}(...$args);
		return $this;
	}

	public function __toString(): string
	{
		return "$this->object"; // @phpstan-ignore encapsedStringPart.nonString, return.type
	}
}
