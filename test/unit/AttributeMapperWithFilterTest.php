<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\AkeneoFredhopper\AttributeMapperWithFilter;
use SnowIO\AkeneoFredhopper\StandardAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString;

class AttributeMapperWithFilterTest extends TestCase
{

    public function testMapFilterByAttributeId()
    {
        $mapper = AttributeMapperWithFilter::of(
            StandardAttributeMapper::create(),
            function (AkeneoAttributeData $akeneoAttributeData) {
                return $akeneoAttributeData->getCode() === 'size';
            }
        );
        $actual = $mapper(AkeneoAttributeData::fromJson([
            'code' => 'size',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => false,
            'scopable' => true,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Size',
                'fr_FR' => 'Taille',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]));

        $expected = AttributeDataSet::create()
            ->with(FredhopperAttributeData::of(
                'size',
                FredhopperAttributeType::LIST,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ));

        self::assertTrue($expected->equals($actual));
    }

    public function testMapFilterByAttributeIdReturnsEmptyArray()
    {
        $mapper = AttributeMapperWithFilter::of(
            StandardAttributeMapper::create(),
            function (AkeneoAttributeData $akeneoAttributeData) {
                return $akeneoAttributeData->getCode() !== 'size';
            }
        );
        $actual = $mapper(AkeneoAttributeData::fromJson([
            'code' => 'size',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => false,
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
