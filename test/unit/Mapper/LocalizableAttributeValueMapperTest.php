<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class LocalizedAttributeValueMapperTest extends TestCase
{
    /** @var  LocalizedAttributeValueMapper */
    private $localizableAttributeValueMapper;

    public function setUp()
    {
        $this->localizableAttributeValueMapper = LocalizedAttributeValueMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeValueSet $akeneoAttributeValues, FredhopperAttributeValueSet $expected)
    {
        $actual = $this->localizableAttributeValueMapper->map($akeneoAttributeValues);
        self::assertEquals($this->toJson($expected), $this->toJson($actual));
    }

    public function toJson(FredhopperAttributeValueSet $attributeValueSet)
    {
        return array_map(function (FredhopperAttributeValue $attributeValue) {
            return $attributeValue->toJson();
        } ,iterator_to_array($attributeValueSet));
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