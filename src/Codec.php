<?php

namespace SamanthaSeng\PolylineAlgorithm;

use Closure;
use Exception;

class Codec
{
	/**
	 * Polyline encodes an array of objects having latitude and longitude properties
	 *
	 * @param array $path
	 * @param integer $precision
	 * @return string
	 * @throws Exception
	 */
	public function encode(array $path, int $precision = 5): string
	{
		$isPathCorrect = $this->checkPath($path);

		if (!$isPathCorrect) throw new Exception('Incorrect path value');

		$factor = pow(10, $precision);

		$transform = function ($latLng) use ($factor) {
			if (!is_array($latLng)) {
				$latLng = [$latLng->lat, $latLng->lng];
			}

			$latRoundedValue = $this->round($latLng[0] * $factor);
			$lngRoundedValue = $this->round($latLng[1] * $factor);

			return [$latRoundedValue, $lngRoundedValue];
		};

		return $this->encodeLine($path, $transform);
	}

	/**
	 * Check path value structure
	 *
	 * @param array $path
	 * @return bool
	 */
	private function checkPath(array $path): bool
	{
		if (gettype($path) !== 'array') return false;

		foreach($path as $part) {
			$partType = gettype($part);
	
			if (!in_array($partType, ['array', 'object'])) return false;
			if ($partType === 'object') {
				if (!isset($part->lat)) return false;
				if (!isset($part->lng)) return false;
			}
		}

		return true;
	}

	/**
	 * Encodes a generic polyline
	 * Optionally performing a transform on each point before encoding it
	 *
	 * @param array $path
	 * @param Closure $transform
	 * @return string
	 */
	private function encodeLine(array $path, Closure $transform): string
	{
		$value = [];
		$start = [0, 0];
		$end = null;

		$finale = [];

		foreach($path as $part) {
			$end = $transform($part);

			$latDifference = $this->round($end[0]) - $this->round($start[0]);
			$lngDifference = $this->round($end[1]) - $this->round($start[1]);

			$lat = $this->encodeSignedValue($latDifference, $value);
			$lng = $this->encodeSignedValue($lngDifference, $value);

			$finale = array_merge($finale, $lat, $lng);

			$start = $end;
		}

		return implode('', $finale);
	}

	/**
	 * Encodes the given value in compact polyline format, appending the
	 * encoded value to the given array of strings
	 *
	 * @param integer $value
	 * @param array $array
	 * @return array
	 */
	private function encodeSignedValue(int $value, array $array): array
	{
		return $this->encodeUnsignedValue($value < 0 ? ~($value << 1) : $value << 1, $array);
	}

	private function encodeUnsignedValue(int $value, array $array): array
	{
		while ($value >= 0x20) {
			$array[] = (chr((0x20 | ($value & 0x1f)) + 63));
			$value >>= 5;
		}

		$array[] = (chr($value + 63));
		return $array;
	}

	private function round(int $value): float
	{
		return floor(abs($value) + 0.5) * ($value >= 0 ? 1 : -1);
	}
}
