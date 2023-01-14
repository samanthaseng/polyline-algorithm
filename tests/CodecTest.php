<?php

namespace SamanthaSeng\PolylineAlgorithm\Tests;

use Exception;

use PHPUnit\Framework\TestCase;
use SamanthaSeng\PolylineAlgorithm\Codec;

class CodecTest extends TestCase
{
	const PATH_CASES = [
		'POSITIVE' => [
			[38.5, 120.2],
			[40.7, 120.95],
			[43.252, 126.453],
		],
		'NEGATIVE' => [
			[-38.5, -120.2],
			[-40.7, -120.95],
			[-43.252, -126.453],
		],
		'MIXED' => [
			[38.5, -120.2],
			[40.7, -120.95],
			[43.252, -126.453],
		],
		'ROUNDED' => [
			[39, -120],
			[41, -121],
			[43, -126],
		],
		'EMPTY' => [],
		'INCOMPLETE' => [
			[38.5, 120.2],
			[120.95],
			[43.252, 126.453],
		],
	];

	public function testCanEncodePositiveCoordinateArray(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode(self::PATH_CASES['POSITIVE']);

		$this->assertEquals('string', gettype($encodedValue));
		$this->assertEquals('_p~iF_qs|U_ulLonqC_mqNwxq`@', $encodedValue);
	}

	public function testCanEncodeNegativeCoordinateArray(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode(self::PATH_CASES['NEGATIVE']);

		$this->assertEquals('string', gettype($encodedValue));
		$this->assertEquals('~o~iF~ps|U~tlLnnqC~lqNvxq`@', $encodedValue);
	}

	public function testCanEncodeMixedCoordinateArray(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode(self::PATH_CASES['MIXED']);

		$this->assertEquals('string', gettype($encodedValue));
		$this->assertEquals('_p~iF~ps|U_ulLnnqC_mqNvxq`@', $encodedValue);
	}

	public function testCanEncodeRoundedCoordinateArray(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode(self::PATH_CASES['ROUNDED']);

		$this->assertEquals('string', gettype($encodedValue));
		$this->assertEquals('_e`mF~nl{U_seK~hbE_seK~po]', $encodedValue);
	}

	public function testCanEncodeObjectCoordinateArray(): void
	{
		$objectArray = array_map(function ($array) {
			return (object) [
				'lat' => $array[0],
				'lng' => $array[1],
			];
		}, self::PATH_CASES['MIXED']);

		$codec = new Codec();
		$encodedValue = $codec->encode($objectArray);

		$this->assertEquals('_p~iF~ps|U_ulLnnqC_mqNvxq`@', $encodedValue);
	}

	public function testCanEncodeEmptyArray(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode(self::PATH_CASES['EMPTY']);

		$this->assertEquals('', $encodedValue);
	}

	public function testCanEncodeWithCustomPrecision(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode(self::PATH_CASES['MIXED'], 6);

		$this->assertEquals('string', gettype($encodedValue));
		$this->assertEquals('_izlhA~rlgdF_{geC~ywl@_kwzCn`{nI', $encodedValue);
	}

	public function testCanEncodeWithZeroPrecision(): void
	{
		$codec = new Codec();
		$encodedValue = $codec->encode(self::PATH_CASES['MIXED'], 0);

		$this->assertEquals('string', gettype($encodedValue));
		$this->assertEquals('kAnFC?EJ', $encodedValue);
	}

	public function testCannotEncodeIncompleteCoordinateArray():void
	{
		$this->expectException(Exception::class);

		$codec = new Codec();
		$codec->encode(self::PATH_CASES['INCOMPLETE']);
	}

	public function testCannotEncodeIncompleteObjectCoordinateArray():void
	{
		$this->expectException(Exception::class);

		$objectArray = array_map(function ($array) {
			return (object) [
				'lat' => $array[0]
			];
		}, self::PATH_CASES['MIXED']);

		$codec = new Codec();
		$codec->encode($objectArray);
	}
}
