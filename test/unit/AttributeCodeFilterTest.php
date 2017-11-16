<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoFredhopper\AttributeCodeFilter;
use SnowIO\AkeneoFredhopper\AttributeMapperWithFilter;
use SnowIO\AkeneoFredhopper\AttributeValueMapperWithFilter;
use SnowIO\AkeneoFredhopper\SimpleAttributeValueMapper;
use SnowIO\AkeneoFredhopper\StandardAttributeMapper;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeDataSet as FredhopperAttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;

class AttributeCodeFilterTest extends TestCase
{

    public function testAttributeFiltration()
    {
        $attributeCodeFilter = AttributeCodeFilter::of(function (string $code) {
            return strpos($code, 'vg_') === FALSE;
        });

        $attributeMapperWithFilter = AttributeMapperWithFilter::of(StandardAttributeMapper::create(),
            $attributeCodeFilter->getAttributeFilter());

        $akeneoAttributeData = AkeneoAttributeData::fromJson([
            'code' => 'vg_size',
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

        /** @var FredhopperAttributeDataSet $dataSet */
        $dataSet = $attributeMapperWithFilter($akeneoAttributeData);
        self::assertEquals(0, $dataSet->count());

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

        /** @var FredhopperAttributeDataSet $dataSet */
        $dataSet = $attributeMapperWithFilter($akeneoAttributeData);
        self::assertEquals(1, $dataSet->count());
    }

    public function testAttributeValueFiltration()
    {
        $attributeCodeFilter = AttributeCodeFilter::of(function (string $code) {
            return strpos($code, 'vg_') === FALSE;
        });

        $attributeValueMapperWithFilter = AttributeValueMapperWithFilter::of(SimpleAttributeValueMapper::create(),
            $attributeCodeFilter->getAttributeValueFilter());

        $akeneoAttributeValueData = AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'vg_size' => 'large',
                'size' => 'large',
                'weight' =>  '30'
            ],
        ]);
        /** @var FredhopperAttributeValueSet $dataSet */
        $dataSet = $attributeValueMapperWithFilter($akeneoAttributeValueData);
        self::assertTrue(FredhopperAttributeValueSet::of([
            FredhopperAttributeValue::of('weight', '30'),
            FredhopperAttributeValue::of('size', 'large'),
        ])->equals($dataSet));

    }

}