<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\CategoryData as AkeneoCategory;
use SnowIO\AkeneoDataModel\CategoryPath;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString;
use SnowIO\FredhopperDataModel\CategoryData as FredhopperCategory;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class CategoryMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoCategory $input,
        FredhopperCategory $expected,
        callable $categoryIdMapper = null,
        callable $nameMapper = null
    ) {
        $categoryMapper = CategoryMapper::create();

        if ($categoryIdMapper !== null) {
            $categoryMapper = $categoryMapper->withCategoryIdMapper($categoryIdMapper);
        }
        if ($nameMapper !== null) {
            $categoryMapper = $categoryMapper->withNameMapper($nameMapper);
        }

        $actual = $categoryMapper->map($input);
        self::assertTrue($actual->equals($expected));
    }

    public function mapDataProvider()
    {
        return [
            'hasParent' => [
                AkeneoCategory::of(CategoryPath::of(['clothes', 't_shirts']))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'fr_FR')),
                FredhopperCategory::of(
                    't_shirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                )->withParent('clothes'),
                null,
                null,
            ],
            'doesntHaveParent' => [
                AkeneoCategory::of(CategoryPath::of(['t_shirts']))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'fr_FR')),
                FredhopperCategory::of(
                    't_shirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                ),
                null,
                null,
            ],
            'withCategoryIdMapper' => [
                AkeneoCategory::of(CategoryPath::of(['clothes', 't_shirts']))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'fr_FR')),
                FredhopperCategory::of(
                    'foo_t_shirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                )->withParent('foo_clothes'),
                $categoryIdMapper = function (string $categoryCode) {
                    return "foo_$categoryCode";
                },
                null
            ],
            'withNameMapper' => [
                AkeneoCategory::of(CategoryPath::of(['clothes', 't_shirts']))
                    ->withLabel(LocalizedString::of('T-Shirts', 'en_GB'))
                    ->withLabel(LocalizedString::of('Tee-shirt', 'en_FR')),
                FredhopperCategory::of(
                    't_shirts',
                    FredhopperInternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-shirt', 'fr_FR')
                )->withParent('clothes'),
                null,
                $nameMapper = function (AkeneoInternationalizedString $labels) {
                    return FredhopperInternationalizedString::create()
                        ->withValue($labels->getValue('en_GB'), 'en_GB')
                        ->withValue($labels->getValue('en_FR'), 'fr_FR');
                },
            ],
        ];
    }
}
