<?php

namespace SamanthaSeng\PolylineAlgorithm;

use Closure;

class Codec
{
	/**
	 * Polyline encodes an array of objects having latitude and longitude properties
	 *
	 * @param array $path
	 * @param integer $precision
	 * @return string
	 */
	public function encode(array $path, $precision = 5): string
	{
		$factor = pow(10, $precision);

		$transform = function ($latLng) use ($factor) {
			if (!is_array($latLng)) {
				$latLng = [$latLng->lat, $latLng->lng];
			}

			$latRoundedValue = $this->round($latLng[0] * $factor);
			$lngRoundedValue = $this->round($latLng[1] * $factor);

			return [$latRoundedValue, $lngRoundedValue];
		};

		return $this->polylineEncodeLine($path, $transform);
	}

	/**
	 * Encodes a generic polyline
	 * Optionally performing a transform on each point before encoding it
	 *
	 * @param array $path
	 * @param Closure $transform
	 * @return string
	 */
	private function polylineEncodeLine(array $path, Closure $transform): string
	{
		$value = [];
		$start = [0, 0];
		$end = null;

		$finale = [];

		foreach($path as $part) {
			$end = $transform($part);

			$latDifference = $this->round($end[0]) - $this->round($start[0]);
			$lngDifference = $this->round($end[1]) - $this->round($start[1]);

			$lat = $this->polylineEncodeSigned($latDifference, $value);
			$lng = $this->polylineEncodeSigned($lngDifference, $value);

			$finale = array_merge($finale, $lat, $lng);

			$start = $end;
		}

		return implode('', $finale);
	}

	/**
	 * Encodes the given value in our compact polyline format, appending the
	 * encoded value to the given array of strings
	 *
	 * @param integer $value
	 * @param array $array
	 * @return array
	 */
	private function polylineEncodeSigned(int $value, array $array): array
	{
		return $this->polylineEncodeUnsigned($value < 0 ? ~($value << 1) : $value << 1, $array);
	}

	private function polylineEncodeUnsigned(int $value, array $array): array
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
