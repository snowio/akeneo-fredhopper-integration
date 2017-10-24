<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\AkeneoDataModel\SingleChannelVariantGroupData;
use SnowIO\AkeneoFredhopper\VariantGroup;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProduct;

class VariantGroupProductMapperTest extends TestCase
{
    /** @var  VariantGroupToProductMapper */
    private $variantGroupToProductMapper;

    public function setUp()
    {
        $this->variantGroupToProductMapper = VariantGroupToProductMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        VariantGroup $akeneoVariantGroup,
        FredhopperProduct $expected,
        callable $categoryIdMapper = null,
        callable $productIdMapper = null,
        $attributeValueMapper = null
    ) {
        if ($productIdMapper !== null) {
            $this->variantGroupToProductMapper = $this->variantGroupToProductMapper
                ->withProductIdMapper($productIdMapper);
        }

        if ($categoryIdMapper !== null) {
            $this->variantGroupToProductMapper = $this->variantGroupToProductMapper
                ->withCategoryIdMapper($categoryIdMapper);
        }

        if ($attributeValueMapper !== null) {
            $this->variantGroupToProductMapper = $this->variantGroupToProductMapper
                ->withAttributeValueMapper($attributeValueMapper);
        }

        $actual = $this->variantGroupToProductMapper->map($akeneoVariantGroup);
        self::assertTrue($actual->equals($expected));
    }

    public function mapDataProvider()
    {
        return [
            'withMappers' => [
                VariantGroup::of(SingleChannelVariantGroupData::fromJson([
                    'code' => '1001425',
                    'axis' => "size_config",
                    'channel' => "demontweeks",
                    'attribute_values' => [
                        'color' => 'blue'
                    ],
                    '@timestamp' => 1508491122,
                ]))->withProductData(SingleChannelProductData::fromJson([
                    'sku' => 'abc123',
                    'channel' => 'main',
                    'categories' => [
                        ['mens', 't_shirts'],
                        ['mens', 'summer_wear'],
                    ],
                    'family' => "mens_t_shirts",
                    'attribute_values' => [
                        'size' => 'large',
                    ],
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ])),
                FredhopperProduct::of('1001425_modified')
                    ->withCategoryId('t_shirts_modified')
                    ->withCategoryId('summer_wear_modified')
                    ->withAttributeValue(FredhopperAttributeValue::of('color', 'blue')),
                function (string $categoryId) {
                    return $categoryId . '_modified';
                },
                function (string $code) {
                    return $code . '_modified';
                },
                SimpleAttributeValueMapper::create(),
            ],
            'withoutMappers' => [
                VariantGroup::of(SingleChannelVariantGroupData::fromJson([
                    'code' => '1001425',
                    'axis' => "size_config",
                    'channel' => "demontweeks",
                    'attribute_values' => [
                        'color' => 'blue'
                    ],
                    '@timestamp' => 1508491122,
                ]))->withProductData(SingleChannelProductData::fromJson([
                    'sku' => 'abc123',
                    'channel' => 'main',
                    'categories' => [
                        ['mens', 't_shirts'],
                        ['mens', 'summer_wear'],
                    ],
                    'family' => "mens_t_shirts",
                    'attribute_values' => [
                        'size' => 'large',
                    ],
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ])),
                FredhopperProduct::of('1001425')
                    ->withCategoryId('t_shirts')
                    ->withCategoryId('summer_wear')
                    ->withAttributeValue(FredhopperAttributeValue::of('color', 'blue')),
            ]
        ];
    }
}
