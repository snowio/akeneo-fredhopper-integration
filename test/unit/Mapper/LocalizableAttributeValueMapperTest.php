<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class LocalizableAttributeValueMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeValueSet $akeneoAttributeValues, FredhopperAttributeValueSet $expected)
    {
        $actual = LocalizableAttributeValueMapper::create()->map($akeneoAttributeValues);
        self::assertTrue($actual->equals($expected));
    }

    public function mapDataProvider()
    {
        return [
            'testWithVariousAttributes' => [
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'large',
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                        'weight' =>  '30'
                    ],
                    'localizations' => [
                        'en_GB' => [
                            'attribute_values' => [
                                'size' => 'Large'
                            ]
                        ]
                    ]
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('size_en_gb', 'Large'), //todo is this really the desired functionality
                ]),
            ],
        ];
    }
}
