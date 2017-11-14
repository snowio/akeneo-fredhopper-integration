<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValue as AkeneoAttributeValue;
use SnowIO\AkeneoFredhopper\AttributeValueMapperWithFilter;
use SnowIO\AkeneoFredhopper\SimpleAttributeValueMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class AttributeValueMapperWithFilterTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AttributeValueMapperWithFilter $mapper,
        AkeneoAttributeValueSet $akeneoAttributeValues,
        FredhopperAttributeValueSet $expectedOutput
    ) {
        $actualOutput = $mapper($akeneoAttributeValues);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'filterSize' => [
                AttributeValueMapperWithFilter::of(
                    SimpleAttributeValueMapper::create(),
                    function (AkeneoAttributeValue $akeneoAttributeValue) {
                        return $akeneoAttributeValue->getAttributeCode() === 'size';
                    }
                ),
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'large',
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                        'weight' =>  '30'
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('size', 'large'),
                ]),
            ],
            'filterSizeWillReturnEmptyAttributeValueSet' => [
                AttributeValueMapperWithFilter::of(
                    SimpleAttributeValueMapper::create(),
                    function (AkeneoAttributeValue $akeneoAttributeValue) {
                        return $akeneoAttributeValue->getAttributeCode() === 'size';
                    }
                ),
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                    ],
                ]),
                FredhopperAttributeValueSet::of([]),
            ],
        ];
    }
}
