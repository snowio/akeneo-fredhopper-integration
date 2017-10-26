<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString;

class CompositeAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(CompositeAttributeMapper $mapper, AkeneoAttributeData $input, array $expectedOutput)
    {
        $actualOutput = $mapper->map($input);
        $renderJson = function (FredhopperAttributeData $attribute) {
            return $attribute->toJson();
        };
        self::assertEquals(\array_map($renderJson, $expectedOutput), \array_map($renderJson, $actualOutput));
    }

    public function mapDataProvider()
    {
        return  [
            'test-multiple-mappers-on-attribute' => [
                CompositeAttributeMapper::create()
                    ->with(
                        StandardAttributeMapper::create()->withAttributeIdMapper(function (string $attributeId) {
                            return $attributeId . '_mapper_modified_1';
                        })
                    )
                    ->with(
                        StandardAttributeMapper::create()->withAttributeIdMapper(function (string $attributeId) {
                            return $attributeId . '_mapper_modified_2';
                        })
                    )
                    ->with(
                        StandardAttributeMapper::create()->withAttributeIdMapper(function (string $attributeId) {
                            return $attributeId . '_mapper_modified_3';
                        })
                    ),
                AkeneoAttributeData::fromJson([
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
                    FredhopperAttributeData::of(
                        'size_mapper_modified_1',
                        FredhopperAttributeType::TEXT,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Größe', 'de_DE')
                    ),
                    FredhopperAttributeData::of(
                        'size_mapper_modified_2',
                        FredhopperAttributeType::TEXT,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Größe', 'de_DE')
                    ),
                    FredhopperAttributeData::of(
                        'size_mapper_modified_3',
                        FredhopperAttributeType::TEXT,
                        InternationalizedString::create()
                            ->withValue('Size', 'en_GB')
                            ->withValue('Größe', 'de_DE')
                    ),
                ],
            ]
        ];
    }

}
