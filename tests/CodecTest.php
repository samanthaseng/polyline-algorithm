<?php

namespace SamanthaSeng\PolylineAlgorithm\Tests;

use PHPUnit\Framework\TestCase;
use SamanthaSeng\PolylineAlgorithm\Codec;

class CodecTest extends TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function testCanEncodeEmptyArray(): void
	{
		$codec = new Codec();

		$this->assertEquals('', $codec->encode([]));
	}

	public function testCanEncodeWithCustomPrecision(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode([[38.5, -120.2]], 6);

		$this->assertEquals('string', gettype($encodedValue));
		$this->assertEquals('_izlhA~rlgdF', $encodedValue);
	}
}
