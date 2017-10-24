<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class CompositeAttributeValueMapperTest extends TestCase
{

    /** @var CompositeAttributeValueMapper */
    private $compositeAttributeValueMapper;

    public function setUp()
    {
        $this->compositeAttributeValueMapper = CompositeAttributeValueMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoAttributeValueSet $akeneoAttributeValues,
        FredhopperAttributeValueSet $expected,
        array $mappers
    ) {
        foreach ($mappers as $mapper) {
            $this->compositeAttributeValueMapper = $this->compositeAttributeValueMapper
                ->with($mapper);
        }

        $actual = $this->compositeAttributeValueMapper
            ->map($akeneoAttributeValues);

        self::assertTrue($expected->equals($actual));
    }

    public function mapDataProvider()
    {
        return [
            'with-simple-attribute-value-mappers' => [
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'Large',
                        'price' => [
                            'gbp' => '30',
                            'eur' => '37.45',
                        ],
                        'weight' => '30',
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('size', 'Large'),
                    FredhopperAttributeValue::of('weight', '30'),
                ]),
                [
                    SimpleAttributeValueMapper::create(),
                ],
            ],
        ];
    }
}