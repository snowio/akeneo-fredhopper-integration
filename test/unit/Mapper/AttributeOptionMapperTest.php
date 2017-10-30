<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\AkeneoDataModel\AttributeOptionIdentifier;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString as AkeneoLocalizedString;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;
use SnowIO\FredhopperDataModel\LocalizedString as FredhopperLocalizedString;

class AttributeOptionMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AttributeOptionMapper $mapper,
        AkeneoAttributeOption $input,
        FredhopperAttributeOption $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'noLabels' => [
                AttributeOptionMapper::create(),
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large')),
                FredhopperAttributeOption::of('size', 'large'),
            ],
            'labels' => [
                AttributeOptionMapper::create()
                    ->withDisplayValueMapper(function (AkeneoInternationalizedString $optionLabels) {
                        return FredhopperInternationalizedString::create()
                            ->withValue($optionLabels->getValue('en_GB'), 'en_GB')
                            ->withValue($optionLabels->getValue('de_DE'), 'de_DE')
                            ->withValue($optionLabels->getValue('eu_FR'), 'fr_FR');
                    }),
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel(AkeneoLocalizedString::of('Groß', 'de_DE'))
                    ->withLabel(AkeneoLocalizedString::of('Grand', 'eu_FR'))
                    ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB')),
                FredhopperAttributeOption::of('size', 'large')
                    ->withDisplayValue(FredhopperLocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Grand', 'fr_FR'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB')),
            ],
            'noLabelsWithIdMappers' => [
                AttributeOptionMapper::create()
                    ->withAttributeIdMapper(function (string $akeneoAttributeCode) {
                        return $akeneoAttributeCode . '_modified';
                    })
                    ->withValueIdMapper(function (string $akeneoOptionCode) {
                        return $akeneoOptionCode . '_modified';
                    }),
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB')),
                FredhopperAttributeOption::of('size_modified', 'large_modified')
                    ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB')),
            ],
            'labelsWithDisplayValueMapper' => [
                AttributeOptionMapper::create()
                    ->withDisplayValueMapper(function (AkeneoInternationalizedString $optionLabels) {
                        return FredhopperInternationalizedString::create()
                            ->withValue($optionLabels->getValue('en_GB'), 'en_GB')
                            ->withValue($optionLabels->getValue('de_DE'), 'de_DE');
                    }),
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel(AkeneoLocalizedString::of('Groß', 'de_DE'))
                    ->withLabel(AkeneoLocalizedString::of('Grand', 'eu_FR'))
                    ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB')),
                FredhopperAttributeOption::of('size', 'large')
                    ->withDisplayValue(FredhopperLocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB')),
            ],
            'idSanitization' => [
                AttributeOptionMapper::create(),
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('Size!', 'LARGE!')),
                FredhopperAttributeOption::of('size', 'large'),
            ],
        ];
    }
}
