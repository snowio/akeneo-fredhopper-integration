<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttribute;
use SnowIO\FredhopperDataModel\InternationalizedString;

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
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                    FredhopperAttribute::of(
                        'size_fr_fr',
                        FredhopperAttributeType::LIST,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
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
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                    FredhopperAttribute::of(
                        'size_fr_fr',
                        FredhopperAttributeType::LIST,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Taille', 'fr_FR')
                    ),
                ],
                function (string $type) {
                    return FredhopperAttributeType::LIST;
                },
            ]
        ];
    }

}
