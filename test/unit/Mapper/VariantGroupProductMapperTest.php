<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\VariantGroupData;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProduct;

class VariantGroupProductMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        VariantGroupData $akeneoVariantGroup,
        FredhopperProduct $expected,
        callable $productIdMapper = null,
        $attributeValueMapper = null
    ) {
        $variantGroupToProductMapper = VariantGroupToProductMapper::create();

        if ($productIdMapper !== null) {
            $variantGroupToProductMapper = $variantGroupToProductMapper->withProductIdMapper($productIdMapper);
        }
        if ($attributeValueMapper !== null) {
            $variantGroupToProductMapper = $variantGroupToProductMapper->withAttributeValueMapper($attributeValueMapper);
        }

        $actual = $variantGroupToProductMapper->map($akeneoVariantGroup);
        self::assertTrue($actual->equals($expected));
    }

    public function mapDataProvider()
    {
        return [
            'withMappers' => [
                VariantGroupData::fromJson([
                    'code' => '1001425',
                    'axis' => "size_config",
                    'channel' => "demontweeks",
                    'attribute_values' => [
                        'color' => 'blue'
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperProduct::of('1001425_modified')->withAttributeValue(FredhopperAttributeValue::of('color', 'blue')),
                function (string $code) {
                    return $code . '_modified';
                },
                SimpleAttributeValueMapper::create(),
            ],
            'withoutMappers' => [
                VariantGroupData::fromJson([
                    'code' => '1001425',
                    'axis' => "size_config",
                    'channel' => "demontweeks",
                    'attribute_values' => [
                        'color' => 'blue'
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperProduct::of('1001425')->withAttributeValue(FredhopperAttributeValue::of('color', 'blue')),
            ]
        ];
    }
}
