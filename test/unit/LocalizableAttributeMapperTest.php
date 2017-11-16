<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\AkeneoFredhopper\LocalizableAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString;

class LocalizableAttributeMapperTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCreationOfMapper()
    {
        LocalizableAttributeMapper::of([]);
    }

    public function testMapWithLocales()
    {
        $mapper = LocalizableAttributeMapper::of(['en_GB', 'fr_FR'])
            ->withTypeMapper(function (string $type) {
                return FredhopperAttributeType::LIST;
            });

        $attributeData = AkeneoAttributeData::fromJson([
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
        ]);

        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_en_gb',
                FredhopperAttributeType::LIST,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
            FredhopperAttributeData::of(
                'size_fr_fr',
                FredhopperAttributeType::LIST,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);

        $actual = $mapper($attributeData);
        self::assertTrue($expected->equals($actual));
    }

    public function testAutomaticLocales()
    {
        $mapper = LocalizableAttributeMapper::create()->withTypeMapper(function (string $type) {
            return FredhopperAttributeType::LIST;
        });

        $attributeData = AkeneoAttributeData::fromJson([
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
        ]);

        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_en_gb',
                FredhopperAttributeType::LIST,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
            FredhopperAttributeData::of(
                'size_fr_fr',
                FredhopperAttributeType::LIST,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);

        $actual = $mapper($attributeData);
        self::assertTrue($expected->equals($actual));
    }

}
