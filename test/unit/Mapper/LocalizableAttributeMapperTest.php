<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString;

class LocalizableAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        LocalizableAttributeMapper $mapper,
        AkeneoAttributeData $input,
        AttributeDataSet $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCreationOfMapper()
    {
        LocalizableAttributeMapper::of([]);
    }

    public function mapDataProvider()
    {
        return [
            'testWithLocales' => [
                LocalizableAttributeMapper::of(['en_GB', 'fr_FR'])
                    ->withTypeMapper(function (string $type) {
                        return FredhopperAttributeType::LIST;
                    }),
                AkeneoAttributeData::fromJson([
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
                AttributeDataSet::of([
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
                ]),
            ],
            'testAutomaticLocales' => [
                LocalizableAttributeMapper::create()->withTypeMapper(function (string $type) {
                    return FredhopperAttributeType::LIST;
                }),
                AkeneoAttributeData::fromJson([
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
                AttributeDataSet::of([
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
                ]),
            ],
        ];
    }

}
