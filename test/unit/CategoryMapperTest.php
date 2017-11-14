<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\CategoryData as AkeneoCategoryData;
use SnowIO\AkeneoDataModel\CategoryPath;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString;
use SnowIO\AkeneoFredhopper\CategoryMapper;
use SnowIO\FredhopperDataModel\CategoryData as FredhopperCategoryData;
use SnowIO\FredhopperDataModel\CategoryData;
use SnowIO\FredhopperDataModel\CategoryDataSet;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class CategoryMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        CategoryMapper $mapper,
        AkeneoCategoryData $input,
        CategoryDataSet $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function mapDataProvider()
    {
        return [
            'hasParent' => array(
                CategoryMapper::create(),
                AkeneoCategoryData::of(CategoryPath::of(array('clothes', 't_shirts')))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'fr_FR')),
                CategoryDataSet::of([FredhopperCategoryData::of(
                    'tshirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                )->withParent('clothes')]),
                null,
                null,
            ),
            'doesntHaveParent' => [
                CategoryMapper::create(),
                AkeneoCategoryData::of(CategoryPath::of(['t_shirts']))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'fr_FR')),
                CategoryDataSet::of([FredhopperCategoryData::of(
                    'tshirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                )]),
            ],
            'withCategoryIdMapper' => [
                CategoryMapper::create()->withCategoryIdMapper(function (string $categoryCode) {
                    return CategoryData::sanitizeId("foo_$categoryCode");
                }),
                AkeneoCategoryData::of(CategoryPath::of(['clothes', 't_shirts']))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'fr_FR')),
                CategoryDataSet::of([FredhopperCategoryData::of(
                    'footshirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                )->withParent('fooclothes')]),
            ],
            'withNameMapper' => [
                CategoryMapper::create()->withNameMapper(function (AkeneoInternationalizedString $labels) {
                    return FredhopperInternationalizedString::create()
                        ->withValue($labels->getValue('en_GB'), 'en_GB')
                        ->withValue($labels->getValue('en_FR'), 'fr_FR');
                }),
                AkeneoCategoryData::of(CategoryPath::of(['clothes', 't_shirts']))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'en_FR')),
                CategoryDataSet::of([FredhopperCategoryData::of(
                    'tshirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                )->withParent('clothes')]),
            ],
        ];
    }
}
