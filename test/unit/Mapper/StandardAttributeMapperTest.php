<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class StandardAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        StandardAttributeMapper $mapper,
        AkeneoAttributeData $input,
        array $expectedOutput
    ) {
        $actualOutput = $mapper->map($input);
        self::assertEquals($this->getJson($expectedOutput), $this->getJson($actualOutput));
    }

    private function getJson(array $fredhopperAttributes)
    {
        return array_map(function (FredhopperAttributeData $attributeData) {
            return $attributeData->toJson();
        }, $fredhopperAttributes);
    }

    public function mapDataProvider()
    {
        return [
            'testLocalizableAttribute' => [
                StandardAttributeMapper::create(),
                AkeneoAttributeData::fromJson([
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
                    FredhopperAttributeData::of(
                        'size',
                        FredhopperAttributeType::ASSET,
                        FredhopperInternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                ],
            ],
            'testNonLocalizableAttribute' => [
                StandardAttributeMapper::create(),
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
                        FredhopperInternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                ],
            ],
            'testNonLocalizableAttributeWithNameMapper' => [
                StandardAttributeMapper::create()
                    ->withAttributeIdMapper(function (string $akeneoAttributeCode) {
                        return $akeneoAttributeCode . '_mapped';
                    })
                    ->withTypeMapper(function (string $akeneoAttributeType) {
                        return FredhopperAttributeType::ASSET;
                    })
                    ->withNameMapper(function (AkeneoInternationalizedString $labels) {
                        return FredhopperInternationalizedString::create()->withValue($labels->getValue('en_GB'), 'en_GB');
                    }),
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
                        'size_mapped',
                        FredhopperAttributeType::ASSET,
                        FredhopperInternationalizedString::create()->withValue('Size', 'en_GB')
                    ),
                ],
            ],
        ];
    }
}
