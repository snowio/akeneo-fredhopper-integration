<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttribute;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString;


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

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoAttribute $akeneoAttribute,
        array $expected
    ) {
        $actual = $this->filterableAttributeMapper
            ->map($akeneoAttribute);
        $getJson = function (FredhopperAttribute $fredhopperAttribute) {
            return $fredhopperAttribute->toJson();
        };

        self::assertEquals(array_map($getJson, $expected), array_map($getJson, $actual));
    }

    public function mapDataProvider()
    {
        return [
            'testFilterByAttributeId' => [
                AkeneoAttribute::fromJson([
                    'code' => 'size',
                    'type' => AkeneoAttributeType::SIMPLESELECT,
                    'localizable' => false,
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
                        FredhopperAttributeType::LIST,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
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
                        'fr_FR' => 'Couleur',
                    ],
                    'group' => 'swatch',
                    '@timestamp' => 1508491122,
                ]),
                [],
            ],
        ];
    }
}
