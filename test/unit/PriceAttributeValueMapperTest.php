<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoFredhopper\PriceAttributeValueMapper;
use SnowIO\FredhopperDataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;

class PriceAttributeValueMapperTest extends TestCase
{
    public function testMap()
    {
        $actual = (PriceAttributeValueMapper::create())(AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'price' => [
                    'gbp' => '30',
                    'eur' => '37.45',
                ],
            ],
        ]));

        $expected = FredhopperAttributeValueSet::of([
            FredhopperAttributeValue::of('price_gbp', '30'),
            FredhopperAttributeValue::of('price_eur', '37.45'),
        ]);

        self::assertTrue($expected->equals($actual));
    }

    public function testMapWithAttributeIdMapper()
    {
        $actual = (PriceAttributeValueMapper::create()->withAttributeIdMapper(function (string $akeneoAttributeCode, string $akeneoCurrency) {
            return AttributeData::sanitizeId("{$akeneoAttributeCode}_{$akeneoCurrency}_test");
        }))(AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'price' => [
                    'gbp' => '30',
                    'eur' => '37.45',
                ],
            ],
        ]));

        $expected = FredhopperAttributeValueSet::of([
            FredhopperAttributeValue::of('price_gbp_test', '30'),
            FredhopperAttributeValue::of('price_eur_test', '37.45'),
        ]);

        self::assertTrue($expected->equals($actual));
    }



    public function testMapWithNonPriceAttribute()
    {
        $actual = (PriceAttributeValueMapper::create())(AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'size' => 'large',
            ],
        ]));

        $expected = FredhopperAttributeValueSet::of([]);

        self::assertTrue($expected->equals($actual));
    }

}
