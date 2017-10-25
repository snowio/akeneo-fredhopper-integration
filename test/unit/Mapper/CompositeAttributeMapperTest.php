<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttribute;
use SnowIO\FredhopperDataModel\InternationalizedString;

class CompositeAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttribute $akeneoAttribute, array $expected, array $mappers = [])
    {
        $compositeAttributeMapper = CompositeAttributeMapper::create();
        foreach ($mappers as $mapper) {
            $compositeAttributeMapper = $compositeAttributeMapper->with($mapper);
        }
        $actual = $compositeAttributeMapper->map($akeneoAttribute);
        $renderJson = function (FredhopperAttribute $attribute) {
            return $attribute->toJson();
        };
        self::assertEquals(\array_map($renderJson, $expected),\array_map($renderJson, $actual));
    }

    public function mapDataProvider()
    {
        return  [
            'test-multiple-mappers-on-attribute' => [
                AkeneoAttribute::fromJson([
                    'code' => 'size',
                    'type' => AkeneoAttributeType::IDENTIFIER,
                    'localizable' => false,
                    'scopable' => false,
                    'sort_order' => 3,
                    'labels' => [
                        'en_GB' => 'Size',
                        'de_DE' => 'Größe',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                [
                    FredhopperAttribute::of(
                        'size_mapper_modified_1',
                        FredhopperAttributeType::TEXT,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Größe', 'de_DE')
                    ),
                    FredhopperAttribute::of(
                        'size_mapper_modified_2',
                        FredhopperAttributeType::TEXT,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Größe', 'de_DE')
                    ),
                    FredhopperAttribute::of(
                        'size_mapper_modified_3',
                        FredhopperAttributeType::TEXT,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Größe', 'de_DE')
                    ),
                ],
                [
                    StandardAttributeMapper::create()
                        ->withAttributeIdMapper(function (string $attributeId) {
                            return $attributeId . '_mapper_modified_1';
                        }),
                    StandardAttributeMapper::create()
                        ->withAttributeIdMapper(function (string $attributeId) {
                            return $attributeId . '_mapper_modified_2';
                        }),
                    StandardAttributeMapper::create()
                        ->withAttributeIdMapper(function (string $attributeId) {
                            return $attributeId . '_mapper_modified_3';
                        })
                ]
            ]
        ];
    }

}
