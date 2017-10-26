<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;

class PriceAttributeValueMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeValueSet $input, FredhopperAttributeValueSet $expectedOutput)
    {
        $actualOutput = PriceAttributeValueMapper::create()->map($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'priceAttribute' => [
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('price_gbp', '30'),
                    FredhopperAttributeValue::of('price_eur', '37.45'),
                ]),
            ],
            'nonPriceAttribute' => [
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'large',
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    //todo should it return null for non price attributes
                ]),
            ]
        ];
    }
}
