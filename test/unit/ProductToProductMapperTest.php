<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\ProductData as AkeneoProductData;
use SnowIO\AkeneoFredhopper\ProductToProductMapper;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\CategoryData;
use SnowIO\FredhopperDataModel\CategoryIdSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;
use SnowIO\FredhopperDataModel\ProductDataSet;

class ProductToProductMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        ProductToProductMapper $mapper,
        AkeneoProductData $input,
        ProductDataSet $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'testWithDefaultMappers' => [
                ProductToProductMapper::create(),
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
                ProductDataSet::of([FredhopperProductData::of('abc123')
                    ->withCategoryIds(CategoryIdSet::of(['tshirts', 'trousers']))
                    ->withAttributeValue(AttributeValue::of('size', 'Large'))]),
            ],
            'testWithCustomMappers' => [
                ProductToProductMapper::create()
                    ->withCategoryIdMapper(function (string $categoryId) {
                        return CategoryData::sanitizeId($categoryId . '_mapped');
                    })
                    ->withProductIdMapper(function (string $sku) {
                        return $sku . '_mapped';
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
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]),
                ProductDataSet::of([FredhopperProductData::of('abc123_mapped')
                    ->withCategoryIds(CategoryIdSet::of(['tshirtsmapped', 'trousersmapped']))
                    ->withAttributeValue(AttributeValue::of('size', 'Large'))]),
            ],
        ];
    }
}
