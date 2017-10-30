<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper;

use PHPUnit\Framework\TestCase;

use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;


class SimpleAttributeValueMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(AkeneoAttributeValueSet $input, FredhopperAttributeValueSet $expectedOutput)
    {
        $actualOutput = (SimpleAttributeValueMapper::create())($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'testWithMulitpleDifferentAttributeValues' => [
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'large',
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                        'weight' => '30',
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('size', 'large'),
                    FredhopperAttributeValue::of('weight', '30'),
                ]),
            ]
        ];
    }

}
