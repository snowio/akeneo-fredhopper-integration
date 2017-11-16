<?php
declare(strict_types = 1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoFredhopper\AttributeValueMapperWithFilter;
use SnowIO\AkeneoFredhopper\SimpleAttributeValueMapper;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\AkeneoFredhopper\AttributeWhitelist;
use SnowIO\AkeneoFredhopper\AttributeMapperWithFilter;
use SnowIO\AkeneoFredhopper\StandardAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class AttributeWhitelistTest extends TestCase
{
    public function testAttributeFiltration()
    {
        $attributeBlacklist = AttributeWhitelist::of(['size']);
        $attributeMapperWithFilter = AttributeMapperWithFilter::of(StandardAttributeMapper::create(),
            $attributeBlacklist->getAttributeFilter());

        $akeneoAttributeData = AkeneoAttributeData::fromJson([
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
        ]);

        /** @var AttributeDataSet $dataSet */
        $dataSet = $attributeMapperWithFilter($akeneoAttributeData);
        self::assertEquals(1, $dataSet->count());

        $akeneoAttributeData = AkeneoAttributeData::fromJson([
            'code' => 'color',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => false,
            'scopable' => true,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Color',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]);

        /** @var AttributeDataSet $actualOutput */
        $dataSet = $attributeMapperWithFilter($akeneoAttributeData);
        self::assertEquals(0, $dataSet->count());
    }

    public function testAttributeValueFiltration()
    {
        $attributeBlacklist = AttributeWhitelist::of(['size']);
        $attributeValueMapperWithFilter = AttributeValueMapperWithFilter::of(SimpleAttributeValueMapper::create(),
            $attributeBlacklist->getAttributeValueFilter());

        $akeneoAttributeValueData = AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'size' => 'large',
                'weight' =>  '30'
            ],
        ]);
        /** @var FredhopperAttributeValueSet $dataSet */
        $dataSet = $attributeValueMapperWithFilter($akeneoAttributeValueData);
        self::assertTrue(FredhopperAttributeValueSet::of([
            FredhopperAttributeValue::of('size', 'large')
        ])->equals($dataSet));
    }
}