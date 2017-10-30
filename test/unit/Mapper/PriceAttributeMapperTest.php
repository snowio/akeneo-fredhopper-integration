<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttribute;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString;

class PriceAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        PriceAttributeMapper $mapper,
        AkeneoAttribute $input,
        AttributeDataSet $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'test-with-price-type' => [
                PriceAttributeMapper::of([
                    'gbp',
                    'eur',
                ]),
                AkeneoAttribute::fromJson([
                    'code' => 'price',
                    'type' => AkeneoAttributeType::PRICE_COLLECTION,
                    'localizable' => false,
                    'scopable' => false,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Price',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                AttributeDataSet::of([
                    FredhopperAttribute::of(
                        'price_gbp',
                        FredhopperAttributeType::FLOAT,
                        InternationalizedString::create()->withValue('Price', 'en_GB')
                    ),
                    FredhopperAttribute::of(
                        'price_eur',
                        FredhopperAttributeType::FLOAT,
                        InternationalizedString::create()->withValue('Price', 'en_GB')
                    ),
                ]),
            ],
            'without-currencies' => [
                PriceAttributeMapper::of([]),
                AkeneoAttribute::fromJson([
                    'code' => 'price',
                    'type' => AkeneoAttributeType::PRICE_COLLECTION,
                    'localizable' => false,
                    'scopable' => false,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Price',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                AttributeDataSet::create(),
            ],
            'test-with-non-price-type' => [
                PriceAttributeMapper::of([
                    'gbp',
                    'eur',
                ]),
                AkeneoAttribute::fromJson([
                    'code' => 'size',
                    'type' => AkeneoAttributeType::SIMPLESELECT,
                    'localizable' => true,
                    'scopable' => true,
                    'sort_order' => 34,
                    'labels' => [
                        'en_GB' => 'Size',
                        'fr_FR' => 'Taille',
                    ],
                    'group' => 'general',
                    '@timestamp' => 1508491122,
                ]),
                AttributeDataSet::create(),
            ],
        ];
    }
}
