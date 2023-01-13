# Polyline algorithm encoding PHP package

## Description
Polyline encoding allows coordinates compression into single string values.

More information should be found in [Google documentation](https://developers.google.com/maps/documentation/utilities/polylinealgorithm).

These algorithmes are based on [JS algorithmes](https://github.com/googlemaps/js-polyline-codec).

## Use example

```php
use PolylineAlgorithm\Codec;

$codec = new Codec;

$path = [
	[38.5, -120.2],
	[40.7, -120.95],
	[43.252, -126.453],
];

$encodedPath = $codec->encode($path);

echo $encodedPath;
// "_p~iF~ps|U_ulLnnqC_mqNvxq`@"
```