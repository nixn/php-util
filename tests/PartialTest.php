<?php
declare(strict_types=1);

namespace Test;

use nixn\php\Partial;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PartialTest extends TestCase
{
	use HandleTrait;

	public static function stringify_args(mixed ...$args): string
	{
		return join(',', $args);
	}

	public static function power(int $a, int $b): int
	{
		return pow($a, $b);
	}

	public static function provider_partial(): array
	{
		return [
			[[self::stringify_args(...)], [], ''],
			[[self::stringify_args(...), 1, 2], [], '1,2'],
			[[self::stringify_args(...), 1, Partial::PLACEHOLDER], [2], '1,2'],
			[[self::stringify_args(...), 1, 2], [3, 4], '1,2,3,4'],
			[[self::stringify_args(...), 1, Partial::PLACEHOLDER, 3], [2, 4], '1,2,3,4'],
			[[self::stringify_args(...), 1, Partial::PLACEHOLDER, 3, Partial::PLACEHOLDER, 5], [2, 4, 6], '1,2,3,4,5,6'],
			[[self::power(...), 'b' => 10], [2], 1024, 'exception' => [\Error::class, '/positional argument/']],
			[[self::power(...), 'b' => 10], ['a' => 2], 1024],
		];
	}

	#[DataProvider('provider_partial')]
	public function test_partial(array $args1, array $args2, mixed $result, ?array $exception = null): void
	{
		$this->handle_exception($exception);
		$this->assertSame($result, Partial::partial(...$args1)(...$args2));
	}
}
