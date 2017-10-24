<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;

class PriceAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(array $currencies, AkeneoAttribute $akeneoAttribute, array $expected)
    {
        $priceAttributeMapper = PriceAttributeMapper::of($currencies);
        $actual = $priceAttributeMapper->map($akeneoAttribute);
        self::assertEquals($this->getJson($expected), $this->getJson($actual));
    }

    public function getJson(array $expectedFredhopperAttributes)
    {
        return array_map(function (FredhopperAttribute $attribute) {
            return $attribute->toJson();
        }, $expectedFredhopperAttributes);
    }

    public function mapDataProvider()
    {
        return [
            'test-with-price-type' => [
                [
                    'gbp',
                    'eur',
                ],
                AkeneoAttribute::fromJson([
                    'code' => 'price',
                    'type' => AkeneoAttributeType::PRICE_COLLECTION,
                    'localizable' => false,
                    'scopable' => false,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Price',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                [
                    FredhopperAttribute::of(
                        'price_gbp',
                        FredhopperAttributeType::FLOAT, [
                            'en_GB' => 'Price',
                        ]
                    ),
                ],
            ],
            'test-with-non-price-type' => [
                [
                    'gbp',
                    'eur',
                ],
                AkeneoAttribute::fromJson([
                    'code' => 'size',
                    'type' => AkeneoAttributeType::SIMPLESELECT,
                    'localizable' => true,
                    'scopable' => true,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Size',
                        'fr_FR' => 'Taille',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                [
                    FredhopperAttribute::of(
                        'size',
                        FredhopperAttributeType::LIST, [
                            'en_GB' => 'Size',
                            'fr_FR' => 'Taille',
                        ]
                    ),
                ],
            ],
            'without-currencies' => [
                [],
                AkeneoAttribute::fromJson([
                    'code' => 'price',
                    'type' => AkeneoAttributeType::PRICE_COLLECTION,
                    'localizable' => false,
                    'scopable' => false,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Price',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                [
                    //todo should we return an empty array really
                ],
            ]
        ];
    }
}