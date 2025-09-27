<?php
declare(strict_types=1);

namespace Test;

/**
 * @method expectException(string $class)
 * @method expectExceptionMessageMatches(string $message)
 */
trait HandleTrait
{
	private function handle_exception(?array $exception): void
	{
		if ($exception)
		{
			$this->expectException($exception['class'] ?? $exception['c'] ?? $exception[0]);
			if ($message = $exception['message'] ?? $exception['m'] ?? $exception[1] ?? null)
				$this->expectExceptionMessageMatches($message);
		}
	}

	private function handle_suppress_warnings(bool $suppress_warnings, callable $callable, mixed ...$args): mixed
	{
		return $suppress_warnings ? @$callable(...$args) : $callable(...$args);
	}
}
