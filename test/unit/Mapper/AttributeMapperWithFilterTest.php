<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString;

class AttributeMapperWithFilterTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeData $akeneoAttribute, array $expected)
    {
        $mapper = AttributeMapperWithFilter::of(
            StandardAttributeMapper::create(),
            function (AkeneoAttributeData $akeneoAttributeData) {
                return $akeneoAttributeData->getCode() === 'size';
            }
        );
        $actual = $mapper->map($akeneoAttribute);
        $getJson = function (FredhopperAttributeData $fredhopperAttribute) {
            return $fredhopperAttribute->toJson();
        };
        self::assertEquals(array_map($getJson, $expected), array_map($getJson, $actual));
    }

    public function mapDataProvider()
    {
        return [
            'testFilterByAttributeId' => [
                AkeneoAttributeData::fromJson([
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
                    FredhopperAttributeData::of(
                        'size',
                        FredhopperAttributeType::LIST,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                ],
            ],
            'testFilterByAttributeIdReturnsEmptyArray' => [
                AkeneoAttributeData::fromJson([
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
