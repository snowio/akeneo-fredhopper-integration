<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoFredhopper\LocalizableAttributeValueMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class LocalizableAttributeValueMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeValueSet $input, FredhopperAttributeValueSet $expectedOutput)
    {
        $actualOutput = (LocalizableAttributeValueMapper::create())($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
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
