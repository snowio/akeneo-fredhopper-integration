<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;


class FilterableAttributeMapperTest extends TestCase
{
    /** @var  FilterableAttributeMapper */
    private $filterableAttributeMapper;

    public function setUp()
    {
        $this->filterableAttributeMapper = FilterableAttributeMapper::of(
            StandardAttributeMapper::create(),
            function (AkeneoAttribute $attribute) {
                return $attribute->getCode() === 'size';
            });
    }

    public function testMap(
        AkeneoAttribute $akeneoAttribute,
        array $expectedFredhopperAttributes
    ) {
        $outputFredhopperAttributes = $this->filterableAttributeMapper
            ->map($akeneoAttribute);
        $getJson = function (FredhopperAttribute $fredhopperAttribute) {
            return $fredhopperAttribute->toJson();
        };

        self::assertEquals(array_map($getJson, $expectedFredhopperAttributes), array_map($getJson, $outputFredhopperAttributes));
    }

    public function testDataProvider()
    {
        return [
            'testFilterByAttributeId' => [
                AkeneoAttribute::fromJson([
                    'code' => 'size',
                    'type' => AkeneoAttributeType::SIMPLESELECT,
                    'localizable' => true,
                    'scopable' => true,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Size',
                        'fr_Fr' => 'Taille',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                function (AkeneoAttribute $attribute) {
                    return $attribute->getCode() === 'size';
                },
                [
                    FredhopperAttribute::of(
                        'size',
                        FredhopperAttributeType::LIST, [
                            'en_GB' => 'Size',
                            'fr_Fr' => 'Taille',
                        ]
                    ),
                ],
            ],
            'testFilterByAttributeIdReturnsEmptyArray' => [
                AkeneoAttribute::fromJson([
                    'code' => 'color',
                    'type' => AkeneoAttributeType::SIMPLESELECT,
                    'localizable' => true,
                    'scopable' => true,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Colour',
                        'fr_Fr' => 'Couleur',
                    ],
                    'group' => 'swatch',
                    '@timestamp' => 1508491122,
                ]),
                function (AkeneoAttribute $attribute) {
                    return $attribute->getCode() === 'size';
                },
                [],
            ],
        ];
    }
}
