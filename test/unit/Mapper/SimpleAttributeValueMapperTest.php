<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;

use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;


class SimpleAttributeValueMapperTest extends TestCase
{
    /** @var  SimpleAttributeValueMapper */
    private $simpleAttributeValueMapper;

    public function setUp()
    {
        $this->simpleAttributeValueMapper = SimpleAttributeValueMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeValueSet $akeneoAttributeValues, FredhopperAttributeValueSet $expected)
    {
        $actual = $this->simpleAttributeValueMapper->map($akeneoAttributeValues);
        self::assertEquals($this->getJson($actual), $this->getJson($expected));
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
            'testWithMulitpleDifferentAttributeValues' => [
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'large',
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                        'weight' => '30',
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('size', 'large'),
                    FredhopperAttributeValue::of('weight', '30'),
                ]),
            ]
        ];
    }

}