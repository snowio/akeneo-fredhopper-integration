<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoDataModel\Category as AkeneoCategory;
use SnowIO\FredhopperDataModel\Category as FredhopperCategory;

class CategoryMapperTest extends TestCase
{
    /** @var CategoryMapper */
    private $categoryMapper;

    public function setUp()
    {
        $this->categoryMapper = CategoryMapper::create();
    }

    public function testMap(
        AkeneoCategory $input,
        FredhopperCategory $expected,
        ?callable $categoryIdMapper,
        ?callable $nameMapper
    ) {

        if ($categoryIdMapper !== null) {
            $this->categoryMapper = $this->categoryMapper->withCategoryIdMapper($categoryIdMapper);
        }

        if ($nameMapper !== null) {
            $this->categoryMapper = $this->categoryMapper->withNameMapper($nameMapper);
        }

        $fredhopperOutputCategory = $this->categoryMapper->map($input);
        self::assertEquals($expected->toJson(), $fredhopperOutputCategory->toJson());
    }

    public function testMapDataProvider()
    {
        return [
            'hasParent' => [
                AkeneoCategory::fromJson([
                    'code' => 't-shirts',
                    'parent' => 'clothes',
                    'path' => ['clothes', 't-shirts'],
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'fr_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of('t-shirts', [
                    'en_GB' => 'T-Shirts',
                    'fr_FR' => 'Tee-Shirt',
                ])->withParent('clothes')
                ->withTimestamp(1508491122),
                null,
                null,
            ],
            'doesntHaveParent' => [
                AkeneoCategory::fromJson([
                    'code' => 't-shirts',
                    'path' => ['clothes', 't-shirts'],
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'fr_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of('t-shirts', [
                    'en_GB' => 'T-Shirts',
                    'fr_FR' => 'Tee-Shirt',
                ])->withTimestamp(1508491122),
                null,
                null,
            ],
            'withCategoryIdMapper' => [
                AkeneoCategory::fromJson([
                    'code' => 't-shirts',
                    'path' => ['clothes', 't-shirts'],
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'en_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of('t-shirts', [
                    'en_GB' => 'T-Shirts',
                    'fr_FR' => 'Tee-Shirt',
                ])->withTimestamp(1508491122),
                $categoryIdMapper = function (string $categoryCode) {
                    return $categoryCode;
                },
                null
            ],
            'withNameMapper' => [
                AkeneoCategory::fromJson([
                    'code' => 't-shirts',
                    'path' => ['clothes', 't-shirts'],
                    'labels' => [
                        'en_GB' => 'T-Shirts',
                        'en_FR' => 'Tee-Shirt',
                    ],
                    '@timestamp' => 1508491122,
                ]),
                FredhopperCategory::of('t-shirts', [
                    'en_GB' => 'T-Shirts',
                    'fr_FR' => 'Tee-Shirt',
                ])->withTimestamp(1508491122),
                null,
                $nameMapper = function (array $names) {
                    return [
                        'en_GB' => $names['en_GB'],
                        'fr_FR' => $names['en_FR'],
                    ];
                },
            ],
        ];
    }
}