<?php
declare(strict_types=1);

namespace Test;

use nixn\php\Arr;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ArrTest extends TestCase
{
	use HandleTrait;

	public static function provider_pick(): array
	{
		$array = [0, 1, 2, 'a' => 'a', 'b' => 'b', 'c' => 'c'];
		return [
			[[$array], []],
			[[$array, 3, 'd'], []],
			[[$array, 1], [1 => 1]],
			[[$array, 1, 2, 3, 'c', 'd'], [1 => 1, 2 => 2, 'c' => 'c']],
		];
	}

	#[DataProvider('provider_pick')]
	public function test_pick(array $args, mixed $result): void
	{
		$this->assertSame($result, Arr::pick(...$args));
	}

	public static function provider_find_by(): array
	{
		$array = [0, 2, 4, 'a' => 'A', 'b' => 'B', 'c' => 'C'];
		return [
			[[$array, fn($v, $k) => is_int($v) && $v > 0], 2],
			[[$array, fn($v, $k) => is_int($v) && $v > 2, true], 2],
			[[$array, fn($v, $k) => $k === 'a'], 'A'],
			[[$array, fn($v, $k) => $v === 'B', true], 'b'],
			[[$array, fn($v, $k) => false], null],
		];
	}

	#[DataProvider('provider_find_by')]
	public function test_find_by(array $args, mixed $result): void
	{
		$this->assertSame($result, Arr::find_by(...$args));
	}

	public static function provider_reduce(): array
	{
		$add = fn(int $carry, int $next) => $carry + $next;
		return [
			[[[], $add], -1, 'exception' => [\RuntimeException::class, '/no elements and no initial value/']],
			[[[], $add, 1], 1],
			[[[], $add, 1, true], 1],
			[[[2], $add], 2],
			[[[2], $add, 1], 3],
			[[[2], $add, 1, true], 2],
			[[[2, 3], $add], 5],
			[[[2, 3], $add, 1], 6],
			[[[2, 3], $add, 1, true], 5],
		];
	}

	#[DataProvider('provider_reduce')]
	public function test_reduce(array $args, mixed $result, string $message = '', ?array $exception = null): void
	{
		$this->handle_exception($exception);
		$this->assertSame($result, Arr::reduce(...$args), $message);
	}

	public static function provider_kvjoin(): array
	{
		return [
			[[[]], ''],
			[[['a' => 'A']], 'a=A'],
			[[['a' => 'A'], ';'], 'a=A'],
			[[['a' => 'A'], ';', ':'], 'a:A'],
			[[['a' => 'A', 'b' => 'B']], 'a=A, b=B'],
			[[['a' => 'A', 'b' => 'B'], ';'], 'a=A;b=B'],
			[[['a' => 'A', 'b' => 'B'], ';', ':'], 'a:A;b:B'],
		];
	}

	#[DataProvider('provider_kvjoin')]
	public function test_kvjoin(array $args, string $result, string $message = ''): void
	{
		$this->assertSame($result, Arr::kvjoin(...$args), $message);
	}

	public static function provider_first(): array
	{
		$none = ['k' => null, 'v' => null];
		return [
			[[], $none],
			[[1], ['k' => 0, 'v' => 1]],
			[[1, 2], ['k' => 0, 'v' => 1]],
			[[1 => 1, 2], ['k' => 1, 'v' => 1]],
			[['a' => 1, 'b' => 2], ['k' => 'a', 'v' => 1]],
		];
	}

	#[DataProvider('provider_first')]
	public function test_first(array $data, array $result, string $message = ''): void
	{
		$this->assertSame($result, Arr::first($data), $message);
	}

	public static function provider_find(): array
	{
		$array = [0, 2, 4, 'a' => 'A', 'b' => 'B', 'c' => 'C'];
		$none = ['k' => null, 'v' => null];
		return [
			[[$array], $none],
			[[$array, 0], ['k' => 0, 'v' => 0]],
			[[$array, 1], ['k' => 1, 'v' => 2]],
			[[$array, 3], $none],
			[[$array, 0, 1], ['k' => 0, 'v' => 0]],
			[[$array, 1, 2], ['k' => 1, 'v' => 2]],
			[[$array, 'a', 'b', 'c'], ['k' => 'a', 'v' => 'A']],
			[[$array, 'x'], $none],
			[[$array, 'x', 3], $none],
			[[$array, 'a', 2, 'b'], ['k' => 2, 'v' => 4]],
			[[$array, 3, 'b'], ['k' => 'b', 'v' => 'B']],
			[[$array, 3, 'b', 2], ['k' => 2, 'v' => 4]],
		];
	}

	#[DataProvider('provider_find')]
	public function test_find(array $args, mixed $result, string $message = ''): void
	{
		$this->assertSame($result, Arr::find(...$args), $message);
	}
}
