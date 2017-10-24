<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet;
use SnowIO\FredhopperDataModel\CategoryIdSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProduct;

class ProductToProductMapperTest extends TestCase
{
    /** @var ProductToProductMapper */
    private $productToProductMapper;

    public function setUp()
    {
        $this->productToProductMapper = ProductToProductMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        SingleChannelProductData $akeneoProductData,
        FredhopperProduct $expected,
        callable $categoryIdMapper = null,
        callable $productIdMapper = null,
        $attributeValueMapper = null
    ) {
        if (null !== $categoryIdMapper) {
            $this->productToProductMapper = $this->productToProductMapper
                ->withCategoryIdMapper($categoryIdMapper);
        }

        if (null !== $productIdMapper) {
            $this->productToProductMapper = $this->productToProductMapper
                ->withProductIdMapper($productIdMapper);
        }

        if (null !== $attributeValueMapper) {
            $this->productToProductMapper = $this->productToProductMapper
                ->withAttributeValueMapper($attributeValueMapper);
        }

        $actual = $this->productToProductMapper->map($akeneoProductData);
        self::assertTrue($actual->equals($expected));
    }

    public function mapDataProvider()
    {
        return [
            'testWithDefaultMappers' => [
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
