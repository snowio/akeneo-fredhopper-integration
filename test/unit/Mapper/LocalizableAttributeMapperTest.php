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
    public function testMap(LocalizableAttributeMapper $mapper, AkeneoAttribute $akeneoAttribute, array $expected)
    {
        $actual = $mapper->map($akeneoAttribute);
        self::assertEquals($this->getJson($expected), $this->getJson($actual));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCreationOfMapper()
    {
        LocalizableAttributeMapper::of([]);
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
                LocalizableAttributeMapper::of([
                    'en_GB',
                    'fr_FR'
                ]),
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
                        'size_en_gb',
                        FredhopperAttributeType::LIST,
                        [
                            'en_GB' => 'Size',
                            'fr_FR' => 'Taille', //todo question should we remove the locale label that is not needed?
                        ]),
                    FredhopperAttribute::of(
                        'size_fr_fr',
                        FredhopperAttributeType::LIST,
                        [
                            'en_GB' => 'Size',
                            'fr_FR' => 'Taille',
                        ]),
                ]
            ],
            'testAutomaticLocales' => [
                LocalizableAttributeMapper::create(),
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
                        'size_en_gb',
                        FredhopperAttributeType::LIST,
                        [
                            'en_GB' => 'Size',
                            'fr_FR' => 'Taille',
                        ]),
                    FredhopperAttribute::of(
                        'size_fr_fr',
                        FredhopperAttributeType::LIST,
                        [
                            'en_GB' => 'Size',
                            'fr_FR' => 'Taille',
                        ]),
                ]
            ]
        ];
    }

}