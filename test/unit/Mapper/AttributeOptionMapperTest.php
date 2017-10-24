<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\AkeneoDataModel\AttributeOptionIdentifier;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;
use SnowIO\FredhopperDataModel\InternationalizedString;
use SnowIO\FredhopperDataModel\LocalizedString;

class AttributeOptionMapperTest extends TestCase
{

    /** @var AttributeOptionMapper */
    private $attributeOptionMapper;

    public function setUp()
    {
        $this->attributeOptionMapper = AttributeOptionMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoAttributeOption $input,
        FredhopperAttributeOption $expected,
        callable $attributeIdMapper = null,
        callable $displayValueMapper = null
    ) {
        if ($attributeIdMapper !== null) {
            $this->attributeOptionMapper = $this
                ->attributeOptionMapper
                ->withAttributeIdMapper($attributeIdMapper);
        }

        if ($displayValueMapper !== null) {
            $this->attributeOptionMapper = $this
                ->attributeOptionMapper
                ->withDisplayValueMapper($displayValueMapper);
        }

        $actual = $this
            ->attributeOptionMapper
            ->map($input);
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
                    ->withLabel('Groß', 'de_DE')
                    ->withLabel('Grand', 'eu_FR')
                    ->withLabel('Large', 'en_GB'),
                FredhopperAttributeOption::of('size', 'large')
                    ->withDisplayValue(LocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(LocalizedString::of('Grand', 'fr_FR'))
                    ->withDisplayValue(LocalizedString::of('Large', 'en_GB')),
                null,
                function (array $optionLabels) {
                    return InternationalizedString::create()
                        ->withValue($optionLabels['en_GB'], 'en_GB')
                        ->withValue($optionLabels['de_DE'], 'de_DE')
                        ->withValue($optionLabels['eu_FR'], 'fr_FR');
                },
            ],
            'noLabelsWithAttributeIdMapper' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel('Groß', 'de_DE')
                    ->withLabel('Grand', 'eu_FR')
                    ->withLabel('Large', 'en_GB'),
                FredhopperAttributeOption::of('size_modified', 'large')
                    ->withDisplayValue(LocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(LocalizedString::of('Grand', 'fr_FR'))
                    ->withDisplayValue(LocalizedString::of('Large', 'en_GB')),
                function (string $attributeCode) {
                    return $attributeCode . '_modified';
                },
                function (array $optionLabels) {
                    return InternationalizedString::create()
                        ->withValue($optionLabels['en_GB'], 'en_GB')
                        ->withValue($optionLabels['de_DE'], 'de_DE')
                        ->withValue($optionLabels['eu_FR'], 'fr_FR');
                },
            ],
            'labelsWithDisplayValueMapper' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel('Groß', 'de_DE')
                    ->withLabel('Grand', 'eu_FR')
                    ->withLabel('Large', 'en_GB'),
                FredhopperAttributeOption::of('size', 'large')
                    ->withDisplayValue(LocalizedString::of('Groß', 'de_DE'))
                    ->withDisplayValue(LocalizedString::of('Large', 'en_GB')),
                null,
                function (array $optionLabels) {
                    return InternationalizedString::create()
                        ->withValue($optionLabels['en_GB'], 'en_GB')
                        ->withValue($optionLabels['de_DE'], 'de_DE');
                },
            ],

        ];
    }
}
