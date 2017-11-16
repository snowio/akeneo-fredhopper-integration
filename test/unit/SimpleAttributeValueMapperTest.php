<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;

use SnowIO\AkeneoFredhopper\SimpleAttributeValueMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;


class SimpleAttributeValueMapperTest extends TestCase
{

    public function testWithMultipleDifferentAttributeValues()
    {
        $actual = (SimpleAttributeValueMapper::create())(AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'size' => 'large',
                'price' => [
                    'gbp' => '30',
                    'eur' => '37.45',
                ],
                'weight' => '30',
            ],
        ]));

        $expected = FredhopperAttributeValueSet::of([
            FredhopperAttributeValue::of('size', 'large'),
            FredhopperAttributeValue::of('weight', '30'),
        ]);

        self::assertTrue($expected->equals($actual));
    }
}
