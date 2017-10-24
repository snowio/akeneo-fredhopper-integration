<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValue as AkeneoAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class AttributeValueMapperWithFilterTest extends TestCase
{
    /** @var AttributeValueMapperWithFilter  */
    private $filterableAttributeValueMapper;

    public function setUp()
    {
        $this->filterableAttributeValueMapper = AttributeValueMapperWithFilter::of(
            SimpleAttributeValueMapper::create(),
            function (AkeneoAttributeValue $akeneoAttributeValue) {
                return $akeneoAttributeValue->getAttributeCode() === 'size';
            }
        );
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoAttributeValueSet $akeneoAttributeValues,
        FredhopperAttributeValueSet $expected
    ) {
        $actual = $this->filterableAttributeValueMapper->map($akeneoAttributeValues);
        self::assertTrue($expected->equals($actual));
    }

    public function mapDataProvider()
    {
        return [
            'filterSize' => [
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
