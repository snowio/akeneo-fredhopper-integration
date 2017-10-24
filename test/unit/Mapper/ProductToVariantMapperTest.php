<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet;
use SnowIO\FredhopperDataModel\Variant as FredhopperVariant;

class ProductToVariantMapperTest extends TestCase
{
    /** @var ProductToVariantMapper */
    private $productToVariantMapper;

    public function setUp()
    {
        $this->productToVariantMapper = ProductToVariantMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        SingleChannelProductData $akeneoProduct,
        FredhopperVariant $expected,
        callable $skuToProductIdMapper = null,
        callable $variantGroupCodeToProductIdMapper = null,
        callable $variantIdMapper = null,
        SimpleAttributeValueMapper $attributeValueMapper = null
    ) {
        if ($skuToProductIdMapper !== null) {
            $this->productToVariantMapper = $this
                ->productToVariantMapper
                ->withSkuToProductIdMapper($skuToProductIdMapper);
        }

        if ($variantGroupCodeToProductIdMapper !== null) {
            $this->productToVariantMapper = $this
                ->productToVariantMapper
                ->withVariantGroupCodeToProductIdMapper($variantGroupCodeToProductIdMapper);
        }

        if ($variantIdMapper !== null) {
            $this->productToVariantMapper = $this
                ->productToVariantMapper
                ->withSkuToVariantIdMapper($variantIdMapper);
        }

        if ($attributeValueMapper !== null) {
            $this->productToVariantMapper = $this
                ->productToVariantMapper
                ->withAttributeValueMapper($attributeValueMapper);
        }

        $actual = $this->productToVariantMapper->map($akeneoProduct);
        self::assertEquals($expected, $actual);
    }

    public function mapDataProvider()
    {
        return [
            'testVariantsWithDefaultMappers' => [
                SingleChannelProductData::fromJson([
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
                FredhopperVariant::of('v_abc123', 'abc')
                    ->withAttributeValues(AttributeValueSet::of([
                        AttributeValue::of('size', 'Large'),
                    ]))
                ,
                null,
                null,
                null,
            ],
            'testStandaloneProductWithDefaultMappers' => [
                SingleChannelProductData::fromJson([
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
                FredhopperVariant::of('v_abc123', 'abc123')
                    ->withAttributeValues(AttributeValueSet::of([
                        AttributeValue::of('size', 'Large'),
                    ]))
                ,
                null,
                null,
                null,
            ],
            'testStandaloneProductWithCustomMappers' => [
                SingleChannelProductData::fromJson([
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
                FredhopperVariant::of('abc123_mapped', 'abc123_mapped')
                    ->withAttributeValues(AttributeValueSet::of([
                        AttributeValue::of('size', 'Large'),
                    ]))
                ,
                function (string $channel, string $variantId) {
                    return $variantId . '_mapped';
                },
                function (string $channel, string $sku) {
                    return $sku . '_mapped';
                },
                function (string $channel, string $sku) {
                    return $sku . '_mapped';
                },
                null
            ],

        ];
    }
}