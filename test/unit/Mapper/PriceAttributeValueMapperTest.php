<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;

class PriceAttributeValueMapperTest extends TestCase
{
    /** @var  PriceAttributeValueMapper */
    private $priceAttributeValueMapper;

    public function setUp()
    {
        $this->priceAttributeValueMapper = PriceAttributeValueMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeValueSet $akeneoAttributeValues, FredhopperAttributeValueSet $expected)
    {
        $actual = $this->priceAttributeValueMapper->map($akeneoAttributeValues);
        self::assertEquals($this->getJson($expected), $this->getJson($actual));
    }

    private function getJson(FredhopperAttributeValueSet $attributeValueSet)
    {
        return array_map(function (FredhopperAttributeValue $attributeValue) {
            return $attributeValue->toJson();
        }, iterator_to_array($attributeValueSet));
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
