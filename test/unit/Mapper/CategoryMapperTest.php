<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Category as AkeneoCategory;
use SnowIO\FredhopperDataModel\CategoryData as FredhopperCategory;
use SnowIO\FredhopperDataModel\InternationalizedString;

class CategoryMapperTest extends TestCase
{
    /** @var CategoryMapper */
    private $categoryMapper;

    public function setUp()
    {
        $this->categoryMapper = CategoryMapper::create();
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AkeneoCategory $input,
        FredhopperCategory $expected,
        callable $categoryIdMapper = null,
        callable $nameMapper = null
    ) {

        if ($categoryIdMapper !== null) {
            $this->categoryMapper = $this->categoryMapper->withCategoryIdMapper($categoryIdMapper);
        }

        if ($nameMapper !== null) {
            $this->categoryMapper = $this->categoryMapper->withNameMapper($nameMapper);
        }

        $actual = $this->categoryMapper->map($input);
        self::assertTrue($actual->equals($expected));
    }

    public function mapDataProvider()
    {
        return [
            'hasParent' => [
                AkeneoCategory::fromJson([
                    'code' => 't_shirts',
                    'parent' => 'clothes',
                    'path' => ['clothes', 't_shirts'],
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'fr_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of(
                    't_shirts',
                    InternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-Shirt', 'fr_FR')
                )->withParent('clothes'),
                null,
                null,
            ],
            'doesntHaveParent' => [
                AkeneoCategory::fromJson([
                    'code' => 't_shirts',
                    'path' => ['clothes', 't_shirts'],
                    'parent' => null,
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'fr_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of(
                    't_shirts',
                    InternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-Shirt', 'fr_FR')
                ),
                null,
                null,
            ],
            'withCategoryIdMapper' => [
                AkeneoCategory::fromJson([
                    'code' => 't_shirts',
                    'path' => ['clothes', 't_shirts'],
                    'parent' => 'clothes',
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'fr_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of(
                    't_shirts',
                    InternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-Shirt', 'fr_FR')
                )->withParent('clothes'),
                $categoryIdMapper = function (string $categoryCode) {
                    return $categoryCode;
                },
                null
            ],
            'withNameMapper' => [
                AkeneoCategory::fromJson([
                    'code' => 't_shirts',
                    'path' => ['clothes', 't_shirts'],
                    'parent' => 'clothes',
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'en_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of(
                    't_shirts',
                    InternationalizedString::create()
                        ->withValue('T-Shirts', 'en_GB')
                        ->withValue('Tee-Shirt', 'fr_FR')
                )->withParent('clothes'),
                null,
                $nameMapper = function (array $names) {
                    return InternationalizedString::create()
                        ->withValue($names['en_GB'], 'en_GB')
                        ->withValue($names['en_FR'], 'fr_FR');
                },
            ],
        ];
    }
}
