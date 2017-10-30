<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class CompositeAttributeValueMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        CompositeAttributeValueMapper $mapper,
        AkeneoAttributeValueSet $input,
        FredhopperAttributeValueSet $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'with-simple-attribute-value-mappers' => [
                CompositeAttributeValueMapper::create()
                    ->with(SimpleAttributeValueMapper::create()),
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'Large',
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                        'weight' => '30',
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('size', 'Large'),
                    FredhopperAttributeValue::of('weight', '30'),
                ]),
            ],
        ];
    }
}
