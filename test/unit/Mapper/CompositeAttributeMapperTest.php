<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;

class CompositeAttributeMapperTest extends TestCase
{
    /** @var CompositeAttributeMapper */
    private $compositeAttributeMapper;

    public function setUp()
    {
        $this->compositeAttributeMapper = CompositeAttributeMapper::create();
    }

    public function testMap(AkeneoAttribute $akeneoAttribute, array $expectedFredhopperAttributes, array $mappers = [])
    {
        foreach ($mappers as $mapper) {
            $this->compositeAttributeMapper = $this->compositeAttributeMapper->with($mapper);
        }
        $outputFredhopperAttributes = $this->compositeAttributeMapper->map($akeneoAttribute);
        $renderJson = function (FredhopperAttribute $attribute) {
            return $attribute->toJson();
        };
        self::assertEquals(\array_map($renderJson, $expectedFredhopperAttributes),\array_map($renderJson, $outputFredhopperAttributes));
    }

    public function testMapDataProvider()
    {
        return  [
            'test-mulitple-mappers-on-attribute' => [
                AkeneoAttribute::fromJson([
                    'code' => 'size',
                    'type' => AkeneoAttributeType::IDENTIFIER,
                    'localizable' => true,
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
                    FredhopperAttribute::of('size_mapper_modified_1', FredhopperAttributeType::TEXT, [
                        'en_GB' => 'Size',
                        'de_DE' => 'Größe',
                    ]),
                    FredhopperAttribute::of('size_mapper_modified_2', FredhopperAttributeType::TEXT, [
                        'en_GB' => 'Size',
                        'de_DE' => 'Größe',
                    ]),
                    FredhopperAttribute::of('size_mapper_modified_3', FredhopperAttributeType::TEXT, [
                        'en_GB' => 'Size',
                        'de_DE' => 'Größe',
                    ]),
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