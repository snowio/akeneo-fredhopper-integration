<?php
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
        AkeneoAttributeOption $input,
        FredhopperAttributeOption $expected,
        callable $attributeIdMapper = null,
        callable $displayValueMapper = null
    ) {
        $attributeOptionMapper = AttributeOptionMapper::create();

        if ($attributeIdMapper !== null) {
            $attributeOptionMapper = $attributeOptionMapper->withAttributeIdMapper($attributeIdMapper);
        }

        if ($displayValueMapper !== null) {
            $attributeOptionMapper = $attributeOptionMapper->withDisplayValueMapper($displayValueMapper);
        }

        $actual = $attributeOptionMapper->map($input);
        self::assertTrue($expected->equals($actual));
    }

    public function mapDataProvider()
    {
        return [
            'noLabels' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large')),
                FredhopperAttributeOption::of('size', 'large'),
                null,
                null,
            ],
            'labels' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel(AkeneoLocalizedString::of('Groß', 'de_DE'))
                    ->withLabel(AkeneoLocalizedString::of('Grand', 'eu_FR'))
                    ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB')),
                FredhopperAttributeOption::of('size', 'large')
                    ->withDisplayValue(FredhopperLocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Grand', 'fr_FR'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB')),
                null,
                function (AkeneoInternationalizedString $optionLabels) {
                    return FredhopperInternationalizedString::create()
                        ->withValue($optionLabels->getValue('en_GB'), 'en_GB')
                        ->withValue($optionLabels->getValue('de_DE'), 'de_DE')
                        ->withValue($optionLabels->getValue('eu_FR'), 'fr_FR');
                },
            ],
            'noLabelsWithAttributeIdMapper' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel(AkeneoLocalizedString::of('Groß', 'de_DE'))
                    ->withLabel(AkeneoLocalizedString::of('Grand', 'eu_FR'))
                    ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB')),
                FredhopperAttributeOption::of('size_modified', 'large')
                    ->withDisplayValue(FredhopperLocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Grand', 'fr_FR'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB')),
                function (string $attributeCode) {
                    return $attributeCode . '_modified';
                },
                function (AkeneoInternationalizedString $optionLabels) {
                    return FredhopperInternationalizedString::create()
                        ->withValue($optionLabels->getValue('en_GB'), 'en_GB')
                        ->withValue($optionLabels->getValue('de_DE'), 'de_DE')
                        ->withValue($optionLabels->getValue('eu_FR'), 'fr_FR');
                },
            ],
            'labelsWithDisplayValueMapper' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel(AkeneoLocalizedString::of('Groß', 'de_DE'))
                    ->withLabel(AkeneoLocalizedString::of('Grand', 'eu_FR'))
                    ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB')),
                FredhopperAttributeOption::of('size', 'large')
                    ->withDisplayValue(FredhopperLocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB')),
                null,
                function (AkeneoInternationalizedString $optionLabels) {
                    return FredhopperInternationalizedString::create()
                        ->withValue($optionLabels->getValue('en_GB'), 'en_GB')
                        ->withValue($optionLabels->getValue('de_DE'), 'de_DE');
                },
            ],

        ];
    }
}
