<?php
namespace nixn\php;

enum Partial
{
	case PLACEHOLDER;

	/**
	 * Returns a new function, which will call the callable with the provided args and can use placeholders for runtime args.
	 *
	 * Examples:
	 * <ul>
	 *     <li>`partial($fn, 1, 2)(3, 4)` = `$fn(1, 2, 3, 4)`</li>
	 *     <li>`partial($fn, 1, PLACEHOLDER, 3)(2, 4)` = `$fn(1, 2, 3, 4)`</li>
	 *     <li>`partial($fn, 1, PLACEHOLDER, 3, PLACEHOLDER, 5)(2, 4, 6)` = `$fn(1, 2, 3, 4, 5, 6)`</li>
	 * </ul>
	 * @param callable $callable
	 * @param mixed ...$args
	 * @return callable
	 */
	public static function partial(callable $callable, mixed ...$args): callable
	{
		return function(mixed ... $placeholder_args) use ($callable, $args): mixed {
			$final_args = [];
			foreach ($args as $key => $arg)
			{
				if ($arg === self::PLACEHOLDER)
					$final_args[] = array_shift($placeholder_args);
				elseif (is_string($key))
					$final_args[$key] = $arg;
				else
					$final_args[] = $arg;
			}
			foreach ($placeholder_args as $arg)
				$final_args[] = $arg;
			return $callable(...$final_args);
		};
	}
}
