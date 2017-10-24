<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\AkeneoDataModel\SingleChannelVariantGroupData;
use SnowIO\AkeneoFredhopper\VariantGroup;
use SnowIO\FredhopperDataModel\AttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\Product as FredhopperProduct;

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
        SimpleAttributeValueMapper $attributeValueMapper = null
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
        self::assertEquals($expected->toJson(), $actual->toJson());
    }

    public function mapDataProvider()
    {
        return [
            [
                VariantGroup::of(SingleChannelVariantGroupData::fromJson([
                    'code' => '1001425',
                    'axis' => "size_config",
                    'channel' => "demontweeks",
                    'attribute_values' => [
                        'color' => 'blue'
                    ],
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
                ])),
                FredhopperProduct::of('1001425', [
                    't_shirts', 'summer_wear'
                ])->withAttributeValues(AttributeValueSet::of([
                    FredhopperAttributeValue::of('color', 'blue'),
                ])),
                null,
                null,
                null,
            ]
        ];
    }
}