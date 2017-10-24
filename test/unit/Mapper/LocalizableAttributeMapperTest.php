<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;


class LocalizableAttributeMapperTest extends TestCase
{

    private $localizableAttributeMapper;

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttribute $akeneoAttribute, array $locales, array $expected)
    {
        $this->localizableAttributeMapper = LocalizableAttributeMapper::of($locales);
        $actual = $this->localizableAttributeMapper->map($akeneoAttribute);
        self::assertEquals($this->getJson($expected), $this->getJson($actual));
    }

    public function getJson(array $fredhopperAttributes)
    {
        return array_map(function (FredhopperAttribute $attribute) {
            return $attribute->toJson();
        }, $fredhopperAttributes);
    }

    public function mapDataProvider()
    {
        return [
            'testWithLocales' => [
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
                    'en_GB',
                    'fr_FR'
                ],
                [
                    FredhopperAttribute::of(
                        'size_en_GB',
                        FredhopperAttributeType::TEXT,
                        [
                            'en_GB' => 'Size',
                            'de_DE' => 'Größe',
                        ]),
                    FredhopperAttribute::of(
                        'size_fr_FR',
                        FredhopperAttributeType::TEXT,
                        [
                            'en_GB' => 'Size',
                            'de_DE' => 'Größe',
                        ]),
                ]
            ],
            'testNoLocales' => [
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
                [],
                [
                    //todo discussion are we really suppose to get nothing
                ]
            ]
            //todo more testcases regarding locales
        ];
    }

}