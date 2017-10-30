<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\ProductData as AkeneoProductData;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\VariantData as FredhopperVariantData;

class ProductToVariantMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        ProductToVariantMapper $mapper,
        AkeneoProductData $input,
        FredhopperVariantData $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'testVariantWithDefaultMappers' => [
                ProductToVariantMapper::create(),
                AkeneoProductData::fromJson([
                    'sku' => 'abc123',
                    'channel' => 'main',
                    'categories' => [
                        ['mens', 't_shirts'],
                        ['mens', 'trousers'],
                    ],
                    'family' => "mens_t_shirts",
                    'attribute_values' => [
                        'size' => 'Large',
                    ],
                    'group' => "abc",
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]),
                FredhopperVariantData::of('v_abc123', 'abc')->withAttributeValue(AttributeValue::of('size', 'Large')),
            ],
            'testVariantWithCustomMappers' => [
                ProductToVariantMapper::create()
                    ->withVariantGroupCodeToProductIdMapper(function (string $vgCode, string $channel) {
                        return "{$channel}_{$vgCode}";
                    }),
                AkeneoProductData::fromJson([
                    'sku' => 'abc123',
                    'channel' => 'main',
                    'categories' => [
                        ['mens', 't_shirts'],
                        ['mens', 'trousers'],
                    ],
                    'family' => "mens_t_shirts",
                    'attribute_values' => [
                        'size' => 'Large',
                    ],
                    'group' => "abc",
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]),
                FredhopperVariantData::of('v_abc123', 'main_abc')->withAttributeValue(AttributeValue::of('size', 'Large')),
            ],
            'testStandaloneProductWithDefaultMappers' => [
                ProductToVariantMapper::create(),
                AkeneoProductData::fromJson([
                    'sku' => 'abc123',
                    'channel' => 'main',
                    'categories' => [
                        ['mens', 't_shirts'],
                        ['mens', 'trousers'],
                    ],
                    'family' => "mens_t_shirts",
                    'attribute_values' => [
                        'size' => 'Large',
                    ],
                    'group' => null,
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]),
                FredhopperVariantData::of('v_abc123', 'abc123')->withAttributeValue(AttributeValue::of('size', 'Large')),
            ],
            'testStandaloneProductWithCustomMappers' => [
                ProductToVariantMapper::create()
                    ->withSkuToProductIdMapper(function (string $sku, string $channel) {
                        return "{$channel}_{$sku}";
                    })
                    ->withSkuToVariantIdMapper(function (string $sku, string $channel) {
                        return "{$channel}_v_{$sku}";
                    }),
                AkeneoProductData::fromJson([
                    'sku' => 'abc123',
                    'channel' => 'main',
                    'categories' => [
                        ['mens', 't_shirts'],
                        ['mens', 'trousers'],
                    ],
                    'family' => "mens_t_shirts",
                    'attribute_values' => [
                        'size' => 'Large',
                    ],
                    'group' => null,
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]),
                FredhopperVariantData::of('main_v_abc123', 'main_abc123')->withAttributeValue(AttributeValue::of('size', 'Large')),
            ],
        ];
    }
}
