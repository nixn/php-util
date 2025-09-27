<?php
declare(strict_types=1);

namespace Test;

use nixn\php\Util;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UtilTest extends TestCase
{
	public static function provider_identity(): array
	{
		return [
			[null],
			[42],
			['abc'],
			[true],
		];
	}

	#[DataProvider('provider_identity')]
	public function test_identity(mixed $value): void
	{
		$this->assertSame($value, Util::identity($value));
	}

	public static function provider_map_nots(): array
	{
		return [
			[[null, fn() => true], true, 'value: null, fn() => true, no nots => true'],
			[[null, fn() => true, 'nots' => null], null, 'value: null, fn() => true, nots: null => null'],
			[[false, 'nots' => null], false, 'value: false, nots: null => false'],
			[[false, 'nots' => false], false, 'value: false, nots: false => false'],
			[[false, 'null_on_not' => true, 'nots' => false], null, 'value: false, null_on_not: true, nots: false => null'],
			[[false, 'null_on_not' => true, 'nots' => false], null, 'value: false, null_on_not: true, nots: false => null'],
			[[1, 'null_on_not' => true, 'func' => true, 'nots' => false], null, 'value: 1, null_on_not: true, func: true, nots: false => null'],
			[[1, fn() => 2, 'null_on_not' => true, 'func' => true, 'nots' => false], 2, 'value: 1, fn() => 2, null_on_not: true, func: true, nots: false => 2'],
		];
	}

	#[DataProvider('provider_map_nots')]
	public function test_map_nots(array $args, mixed $result, string $message = ''): void
	{
		$this->assertSame($result, Util::map_nots(...$args), $message);
	}

	public static function provider_map(): array
	{
		$ve = fn($v) => var_export($v, true);
		return [
			[[null, $ve, 'null' => true], null, 'null @ true'],
			[[null, $ve, 'null' => false], 'NULL', 'null @ false'],
			[[false, $ve, 'false' => true], false, 'false @ true'],
			[[false, $ve, 'false' => false], "false", 'false @ false'],
			[['', $ve, 'empty' => true], '', '"" @ true'],
			[['', $ve, 'empty' => false], "''", '"" @ false'],
			[[[], 'null_on_not' => true, 'empty' => true], null, '[] @ true'],
			[[[], 'null_on_not' => true, 'empty' => false], [], '[] @ false'],
			[[0, $ve, 'zero' => true], 0, '0 @ true'],
			[[0, $ve, 'zero' => false], '0', '0 @ false'],
			[[0.0, $ve, 'zero' => true], 0.0, '0.0 @ true'],
			[[0.0, $ve, 'zero' => false], '0.0', '0.0 @ false'],
		];
	}

	#[DataProvider('provider_map')]
	public function test_map(array $args, mixed $result, string $message = ''): void
	{
		$this->assertSame($result, Util::map(...$args), $message);
	}

	public static function provider_tree_path(): array
	{
		$a = ['value' => 'a', 'parent' => null];
		$b = ['value' => 'b', 'parent' => $a];
		$c = ['value' => 'c', 'parent' => $b];
		$get_parent = fn($node) => $node['parent'];
		$node_output = fn($node) => "{$node['value']},";
		return [
			[$c, $get_parent, null, $node_output, "a,b,c,"],
			[$c, $get_parent, fn($node) => $node['value'] !== 'a', $node_output, "b,c,"],
			[null, $get_parent, null, $node_output, ""],
		];
	}

	#[DataProvider('provider_tree_path')]
	public function test_tree_path(mixed $node, callable $get_parent, ?callable $while, callable $node_output, string $full_output): void
	{
		$this->expectOutputString($full_output);
		foreach (Util::tree_path($node, $get_parent, $while) as $node)
			echo $node_output($node);
	}

	public function test_new(): void
	{
		$this->assertInstanceOf(\DateTime::class, Util::new(\DateTime::class)());
	}
}
