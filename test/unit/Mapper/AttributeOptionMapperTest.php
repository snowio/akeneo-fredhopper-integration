<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\AkeneoDataModel\AttributeOptionIdentifier;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;

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
        self::assertEquals($expected->toJson(), $actual->toJson());
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
                    ->withDisplayValue('Groß', 'de_DE')
                    ->withDisplayValue('Grand', 'fr_FR') //todo test locales that will change for fredhopper's case understand fr_FR
                    ->withDisplayValue('Large', 'en_GB'),
                null,
                function (array $displayValues) {
                    return [
                        'en_GB' => $displayValues['en_GB'],
                        'de_DE' => $displayValues['de_DE'],
                        'fr_FR' => $displayValues['eu_FR'],
                    ];
                },
            ],
            'noLabelsWithAttributeIdMapper' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel('Groß', 'de_DE')
                    ->withLabel('Grand', 'eu_FR')
                    ->withLabel('Large', 'en_GB'),
                FredhopperAttributeOption::of('size_modified', 'large')
                    ->withDisplayValue('Groß', 'de_DE')
                    ->withDisplayValue('Grand', 'fr_FR') //todo test locales that will change for fredhopper's case understand fr_FR
                    ->withDisplayValue('Large', 'en_GB'),
                function (string $attributeCode) {
                    return $attributeCode . '_modified';
                },
                function (array $displayValues) {
                    return [
                        'en_GB' => $displayValues['en_GB'],
                        'de_DE' => $displayValues['de_DE'],
                        'fr_FR' => $displayValues['eu_FR'],
                    ];
                },
            ],
            'labelsWithDisplayValueMapper' => [
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
                    ->withLabel('Groß', 'de_DE')
                    ->withLabel('Grand', 'eu_FR')
                    ->withLabel('Large', 'en_GB'),
                FredhopperAttributeOption::of('size', 'large')
                    ->withDisplayValue('Groß', 'de_DE')
                    ->withDisplayValue('Large', 'en_GB'),
                null,
                function (array $displayValues) {
                    return [
                        'en_GB' => $displayValues['en_GB'],
                        'de_DE' => $displayValues['de_DE'],
                    ];
                },
            ],

        ];
    }
}