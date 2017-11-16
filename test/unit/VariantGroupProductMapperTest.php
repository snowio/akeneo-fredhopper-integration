<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoDataModel\VariantGroupData;
use SnowIO\AkeneoFredhopper\VariantGroupToProductMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;
use SnowIO\FredhopperDataModel\ProductDataSet;

class VariantGroupProductMapperTest extends TestCase
{
    public function testMappers()
    {
        $mapper = VariantGroupToProductMapper::create()
            ->withProductIdMapper(function (string $variantGroupCode, string $channel) {
                return "{$channel}_{$variantGroupCode}";
            })
            ->withAttributeValueMapper(function(AkeneoAttributeValueSet $akeneoAttributeValues) {
                return FredhopperAttributeValueSet::create()->with(FredhopperAttributeValue::of('foo', 'bar'));
            });
        $actual = $mapper(VariantGroupData::fromJson([
            'code' => '1001425',
            'axis' => "size_config",
            'channel' => "demontweeks",
            'attribute_values' => [
                'color' => 'blue'
            ],
        ]));
        $expected = ProductDataSet::of([FredhopperProductData::of('demontweeks_1001425')
            ->withAttributeValue(FredhopperAttributeValue::of('foo', 'bar'))]);
        self::assertTrue($expected->equals($actual));
    }

    public function testWithoutMappers()
    {
        $mapper = VariantGroupToProductMapper::create();
        $actual = $mapper(VariantGroupData::fromJson([
            'code' => '1001425',
            'axis' => "size_config",
            'channel' => "demontweeks",
            'attribute_values' => [
                'color' => 'blue'
            ],
        ]));
        $expected = ProductDataSet::of([FredhopperProductData::of('1001425')
            ->withAttributeValue(FredhopperAttributeValue::of('color', 'blue'))]);
        self::assertTrue($expected->equals($actual));
    }
}
