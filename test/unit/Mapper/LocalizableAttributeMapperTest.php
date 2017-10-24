<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;


class LocalizableAttributeMapperTest extends TestCase
{

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(array $locales, AkeneoAttribute $akeneoAttribute, array $expected, callable $typeMapper = null)
    {
        if (!empty($locales)) {
            $mapper = LocalizableAttributeMapper::of($locales);
        } else {
            $mapper = LocalizableAttributeMapper::create();
        }

        if ($typeMapper !== null) {
            $mapper = $mapper->withTypeMapper($typeMapper);
        }

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
                [
                    'en_GB',
                    'fr_FR'
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
                ],
                function (string $type) {
                    return FredhopperAttributeType::LIST;
                },
            ],
            'testAutomaticLocales' => [
                [],
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
                ],
                function (string $type) {
                    return FredhopperAttributeType::LIST;
                },
            ]
        ];
    }

}