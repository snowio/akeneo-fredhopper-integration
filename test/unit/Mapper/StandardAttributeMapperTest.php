<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttribute;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString;

class StandardAttributeMapperTest extends TestCase
{
    /** @var  StandardAttributeMapper */
    private $standardAttributeMapper;

    public function setUp()
    {
        $this->standardAttributeMapper = StandardAttributeMapper::create();
    }

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

        if (null !== $attributeIdMapper) {
            $this->standardAttributeMapper = $this->standardAttributeMapper
                ->withAttributeIdMapper($attributeIdMapper);
        }

        if (null !== $typeMapper) {
            $this->standardAttributeMapper = $this->standardAttributeMapper
                ->withTypeMapper($typeMapper);
        }

        if (null !== $nameMapper) {
            $this->standardAttributeMapper = $this->standardAttributeMapper
                ->withNameMapper($nameMapper);
        }

        $actual = $this->standardAttributeMapper->map($akeneoAttribute);
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
                        InternationalizedString::create()
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
                        InternationalizedString::create()
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
                        InternationalizedString::create()->withValue('Size', 'en_GB')
                    ),
                ],
                function (string $attributeIdMapper) {
                    return $attributeIdMapper . '_mapped';
                },
                function (string $typeMapper) {
                    return FredhopperAttributeType::ASSET;
                },
                function (array $name) {
                    return InternationalizedString::create()->withValue($name['en_GB'], 'en_GB');
                }
            ],
        ];
    }
}
