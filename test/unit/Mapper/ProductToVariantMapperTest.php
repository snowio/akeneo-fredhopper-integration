<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\ProductData as AkeneoProductData;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\VariantData as FredhopperVariant;

class ProductToVariantMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoProductData $akeneoProduct,
        FredhopperVariant $expected,
        callable $skuToProductIdMapper = null,
        callable $variantGroupCodeToProductIdMapper = null,
        callable $variantIdMapper = null,
        $attributeValueMapper = null
    ) {
        $productToVariantMapper = ProductToVariantMapper::create();;

        if ($skuToProductIdMapper !== null) {
            $productToVariantMapper = $productToVariantMapper->withSkuToProductIdMapper($skuToProductIdMapper);
        }
        if ($variantGroupCodeToProductIdMapper !== null) {
            $productToVariantMapper = $productToVariantMapper->withVariantGroupCodeToProductIdMapper($variantGroupCodeToProductIdMapper);
        }
        if ($variantIdMapper !== null) {
            $productToVariantMapper = $productToVariantMapper->withSkuToVariantIdMapper($variantIdMapper);
        }
        if ($attributeValueMapper !== null) {
            $productToVariantMapper = $productToVariantMapper->withAttributeValueMapper($attributeValueMapper);
        }

        $actual = $productToVariantMapper->map($akeneoProduct);
        self::assertEquals($expected, $actual);
    }

    public function mapDataProvider()
    {
        return [
            'testVariantsWithDefaultMappers' => [
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
                FredhopperVariant::of('v_abc123', 'abc')->withAttributeValue(AttributeValue::of('size', 'Large')),
                null,
                null,
                null,
            ],
            'testStandaloneProductWithDefaultMappers' => [
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
                FredhopperVariant::of('v_abc123', 'abc123')->withAttributeValue(AttributeValue::of('size', 'Large')),
                null,
                null,
                null,
            ],
            'testStandaloneProductWithCustomMappers' => [
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
                FredhopperVariant::of('abc123_v_mapped', 'abc123_mapped')->withAttributeValue(AttributeValue::of('size', 'Large')),
                function (string $channel, string $variantId) {
                    return $variantId . '_mapped';
                },
                function (string $channel, string $sku) {
                    return $sku . '_mapped';
                },
                function (string $channel, string $sku) {
                    return $sku . '_v_mapped';
                },
                SimpleAttributeValueMapper::create(),
            ],

        ];
    }
}
