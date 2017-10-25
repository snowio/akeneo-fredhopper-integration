<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttribute;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class StandardAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoAttribute $akeneoAttribute,
        array $expected,
        callable $attributeIdMapper = null,
        callable $typeMapper = null,
        callable $nameMapper = null
    ) {
        $attributeMapper = StandardAttributeMapper::create();

        if (null !== $attributeIdMapper) {
            $attributeMapper = $attributeMapper->withAttributeIdMapper($attributeIdMapper);
        }
        if (null !== $typeMapper) {
            $attributeMapper = $attributeMapper->withTypeMapper($typeMapper);
        }
        if (null !== $nameMapper) {
            $attributeMapper = $attributeMapper->withNameMapper($nameMapper);
        }

        $actual = $attributeMapper->map($akeneoAttribute);
        self::assertEquals($this->getJson($expected), $this->getJson($actual));
    }

    private function getJson(array $fredhopperAttributes)
    {
        return array_map(function (FredhopperAttribute $akeneoAttribute) {
            return $akeneoAttribute->toJson();
        }, $fredhopperAttributes);
    }

    public function mapDataProvider()
    {
        return [
            'testLocalizableAttribute' => [
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
                        FredhopperAttributeType::ASSET,
                        FredhopperInternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                ],
                null,
                null,
                null,
            ],
            'testNonLocalizableAttribute' => [
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
                        FredhopperInternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                ],
                null,
                null,
                null,
            ],
            'testNonLocalizableAttributeWithNameMapper' => [
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
                        'size_mapped',
                        FredhopperAttributeType::ASSET,
                        FredhopperInternationalizedString::create()->withValue('Size', 'en_GB')
                    ),
                ],
                function (string $attributeIdMapper) {
                    return $attributeIdMapper . '_mapped';
                },
                function (string $typeMapper) {
                    return FredhopperAttributeType::ASSET;
                },
                function (AkeneoInternationalizedString $labels) {
                    return FredhopperInternationalizedString::create()->withValue($labels->getValue('en_GB'), 'en_GB');
                }
            ],
        ];
    }
}
