<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoDataModel\VariantGroupData;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;

class VariantGroupProductMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        VariantGroupToProductMapper $mapper,
        VariantGroupData $input,
        FredhopperProductData $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'withMappers' => [
                VariantGroupToProductMapper::create()
                    ->withProductIdMapper(function (string $variantGroupCode, string $channel) {
                        return "{$channel}_{$variantGroupCode}";
                    })
                    ->withAttributeValueMapper(function(AkeneoAttributeValueSet $akeneoAttributeValues) {
                        return FredhopperAttributeValueSet::create()->with(FredhopperAttributeValue::of('foo', 'bar'));
                    }),
                VariantGroupData::fromJson([
                    'code' => '1001425',
                    'axis' => "size_config",
                    'channel' => "demontweeks",
                    'attribute_values' => [
                        'color' => 'blue'
                    ],
                ]),
                FredhopperProductData::of('demontweeks_1001425')
                    ->withAttributeValue(FredhopperAttributeValue::of('foo', 'bar')),
            ],
            'withoutMappers' => [
                VariantGroupToProductMapper::create(),
                VariantGroupData::fromJson([
                    'code' => '1001425',
                    'axis' => "size_config",
                    'channel' => "demontweeks",
                    'attribute_values' => [
                        'color' => 'blue'
                    ],
                ]),
                FredhopperProductData::of('1001425')
                    ->withAttributeValue(FredhopperAttributeValue::of('color', 'blue')),
            ]
        ];
    }
}
