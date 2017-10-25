<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\ProductData as AkeneoProductData;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\CategoryIdSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProduct;

class ProductToProductMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoProductData $akeneoProductData,
        FredhopperProduct $expected,
        callable $categoryIdMapper = null,
        callable $productIdMapper = null,
        $attributeValueMapper = null
    ) {
        $productToProductMapper = ProductToProductMapper::create();

        if (null !== $categoryIdMapper) {
            $productToProductMapper = $productToProductMapper->withCategoryIdMapper($categoryIdMapper);
        }
        if (null !== $productIdMapper) {
            $productToProductMapper = $productToProductMapper->withProductIdMapper($productIdMapper);
        }
        if (null !== $attributeValueMapper) {
            $productToProductMapper = $productToProductMapper->withAttributeValueMapper($attributeValueMapper);
        }

        $actual = $productToProductMapper->map($akeneoProductData);
        self::assertTrue($actual->equals($expected));
    }

    public function mapDataProvider()
    {
        return [
            'testWithDefaultMappers' => [
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
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]),
                FredhopperProduct::of('abc123')
                    ->withCategoryIds(CategoryIdSet::of(['t_shirts', 'trousers']))
                    ->withAttributeValue(AttributeValue::of('size', 'Large')),
                null,
                null,
                null,
            ],
            'testWithCustomMappers' => [
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
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]),
                FredhopperProduct::of('abc123_mapped')
                    ->withCategoryIds(CategoryIdSet::of(['t_shirts_mapped', 'trousers_mapped']))
                    ->withAttributeValue(AttributeValue::of('size', 'Large'))
                ,
                function (string $categoryId) {
                    return $categoryId . '_mapped';
                },
                function (string $channel, string $sku) {
                    return $sku . '_mapped';
                },
                SimpleAttributeValueMapper::create(),
            ],
        ];
    }
}
