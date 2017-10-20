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

    public function testMap(
        AkeneoAttributeValueSet $akeneoAttributeValues,
        FredhopperAttributeValueSet $expectedFredhopperAttributeValues,
        array $mappers
    ) {
        foreach ($mappers as $mapper) {
            $this->compositeAttributeValueMapper = $this->compositeAttributeValueMapper
                ->with($mapper);
        }

        $outputFredhopperAttributeValues = $this->compositeAttributeValueMapper
            ->map($akeneoAttributeValues);

        self::assertTrue($expectedFredhopperAttributeValues->equals($outputFredhopperAttributeValues));
    }

    public function testDataProvider()
    {
        return [
            'with-simple-attribute-value-mappers' => [
                AkeneoAttributeValueSet::fromJson('main', [
                    'attribute_values' => [
                        'size' => 'large',
                        'price' => [
                            'gbp' => 30,
                            'eur' => 37.45,
                        ],
                        'weight' =>  30
                    ],
                ]),
                FredhopperAttributeValueSet::of([
                    FredhopperAttributeValue::of('size', 'large'),
                    FredhopperAttributeValue::of('price', [
                        'gbp' => 30.98,
                        'eur' => 37.45,
                    ]),
                    FredhopperAttributeValue::of('weight', 30)
                ]),
                $mappers = [
                    SimpleAttributeValueMapper::create(),
                ],
            ],
        ];
    }
}