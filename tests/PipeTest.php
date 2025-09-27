<?php
declare(strict_types=1);

namespace Test;

use nixn\php\Pipe;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PipeTest extends TestCase
{
	use HandleTrait;

	public function test(): void
	{
		$pipe = new Pipe('2025-01-01t00:00:00');
		$this->assertSame('2025-01-01t00:00:00', $pipe->value);
		$pipe(strtoupper(...));
		$this->assertSame('2025-01-01T00:00:00', $pipe->value);
		$pipe->new(\DateTimeImmutable::class, Pipe::PLACEHOLDER, new \DateTimeZone('UTC'));
		$this->assertInstanceOf(\DateTimeImmutable::class, $pipe->value);
		$pipe->format('Y_m_d');
		$this->assertSame('2025_01_01', $pipe->value);
		$pipe(explode(...), '_', Pipe::PLACEHOLDER, 2);
		$this->assertSame(['2025', '01_01'], $pipe->value);
		$pipe->get(0);
		$this->assertSame('2025', $pipe->value);
		$pipe(intval(...));
		$this->assertSame(2025, $pipe->value);
		$pipe(fn($x) => $x + 1);
		$this->assertSame(2026, $pipe->value);
	}

	public static function provider_get(): array
	{
		return [
			[new Pipe(new \stdClass()), ['one'], null, 'empty object', 'suppress_warnings' => true],
			[(new Pipe(new \stdClass()))->set('one', 1), ['one'], 1, 'object->one'],
			[(new Pipe(new \stdClass()))->set('one', 1), ['one', true], null, 'object->one', 'exception' => [\Error::class, '/cannot use object .*as array/i']],
			[new Pipe([]), ['one', true], null, 'empty array', 'suppress_warnings' => true],
			[new Pipe(['one' => 1]), ['one'], null, 'assoc array', 'suppress_warnings' => true],
			[new Pipe(['one' => 1]), ['one', true], 1, 'assoc array'],
			[new Pipe([1 => 'one']), [1], 'one', 'indexed array'],
		];
	}

	#[DataProvider('provider_get')]
	public function test_get(Pipe $pipe, array $get, mixed $result, string $message = '', ?array $exception = null, bool $suppress_warnings = false): void
	{
		$this->handle_exception($exception);
		$this->handle_suppress_warnings($suppress_warnings, $pipe->get(...), ...$get);
		$this->assertSame($result, $pipe->value, $message);
	}

	public static function provider_set(): array
	{
		return [
			[['v' => 1], ['v', 2, true], ['v', true], 2, 'assoc array'],
			[[1], [0, 2], [0], 2, 'indexed array'],
			[(new Pipe(new \stdClass))->set('v', 1)->value, ['v', 2], ['v'], 2, 'object'],
		];
	}

	#[DataProvider('provider_set')]
	public function test_set(mixed $initial, array $set, array $get, mixed $result, string $message = ''): void
	{
		$this->assertSame($result, (new Pipe($initial))->set(...$set)->get(...$get)->value, $message);
	}

	public function test_toString(): void
	{
		$v = 'a string';
		$w = new Pipe($v);
		$this->assertSame("$v", "$w");
	}
}
