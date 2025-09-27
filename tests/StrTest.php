<?php
declare(strict_types=1);

namespace Test;

use nixn\php\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
	public static function provider_trim_prefix(): array
	{
		return [
			['', '', ''],
			['a', '', 'a'],
			['', 'a', ''],
			['a', 'a', ''],
			['abc', 'a', 'bc'],
			['aaa', 'a', 'aa'],
			['Aaa', 'a', 'Aaa'],
			['AAA', 'A', 'AA'],
			['aAA', 'A', 'aAA'],
			['abcdef', 'abc', 'def'],
			['abcdef', 'def', 'abcdef'],
		];
	}

	#[DataProvider('provider_trim_prefix')]
	public function test_trim_prefix(string $string, string $prefix, string $result): void
	{
		$this->assertSame($result, Str::trim_prefix($string, $prefix));
	}

	public static function provider_trim_suffix(): array
	{
		return [
			['', '', ''],
			['a', '', 'a'],
			['', 'a', ''],
			['a', 'a', ''],
			['abc', 'c', 'ab'],
			['aaa', 'a', 'aa'],
			['aaA', 'a', 'aaA'],
			['AAA', 'A', 'AA'],
			['AAa', 'A', 'AAa'],
			['abcdef', 'def', 'abc'],
			['abcdef', 'abc', 'abcdef'],
		];
	}

	#[DataProvider('provider_trim_suffix')]
	public function test_trim_suffix(string $string, string $suffix, string $result): void
	{
		$this->assertSame($result, Str::trim_suffix($string, $suffix));
	}
}
