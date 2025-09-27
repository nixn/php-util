<?php
declare(strict_types=1);

namespace Test;

use nixn\php\With;
use PHPUnit\Framework\TestCase;

final class WithTest extends TestCase
{
	public function test_new(): void
	{
		$this->assertInstanceOf(\DateTime::class, With::new(\DateTime::class)->object);
	}

	public function test(): void
	{
		$w = With::new(\DateTime::class, 'now');
		$this->assertInstanceOf(\DateTime::class, $w->object);
		$this->assertSame('2025-01-01', $w->setDate(2025, 1, 1)->object->format('Y-m-d'));
		$this->assertSame('2025-01-01 08:15:10', $w->setTime(8, 15, 10)->object->format('Y-m-d H:i:s'));
	}

	public function test_toString(): void
	{
		$obj = new \RuntimeException("just a test");
		$w = new With($obj);
		$this->assertSame("$obj", "$w");
	}
}
