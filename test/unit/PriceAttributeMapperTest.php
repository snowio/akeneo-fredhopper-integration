<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\AkeneoFredhopper\PriceAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttribute;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString;

class PriceAttributeMapperTest extends TestCase
{

    public function testWithPriceType()
    {
        $mapper = PriceAttributeMapper::of([
            'gbp',
            'eur',
        ]);
        $actual = $mapper(AkeneoAttribute::fromJson([
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
        ]));

        $expected = AttributeDataSet::of([
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
        ]);

        self::assertTrue($expected->equals($actual));
    }

    public function testWithoutCurrencies()
    {
        $mapper = PriceAttributeMapper::of([]);
        $actual = $mapper(AkeneoAttribute::fromJson([
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
        ]));
        $expected = AttributeDataSet::create();
        self::assertTrue($expected->equals($actual));
    }

    public function testWithNonPriceType()
    {
        $mapper = PriceAttributeMapper::of([
            'gbp',
            'eur',
        ]);

        $actual = $mapper(AkeneoAttribute::fromJson([
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
        ]));

        $expected = AttributeDataSet::create();

        self::assertTrue($expected->equals($actual));

    }
}
