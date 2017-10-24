<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Category as AkeneoCategory;
use SnowIO\FredhopperDataModel\CategoryData as FredhopperCategoryData;
use SnowIO\FredhopperDataModel\InternationalizedString;

class CategoryMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->categoryIdMapper = function (string $categoryCode) { return $categoryCode; };
        $mapper->nameMapper = function (array $names) {
            $result = InternationalizedString::create();
            foreach ($names as $locale => $name) {
                $result = $result->withValue($name, $locale);
            }
            return $result;
        };
        return $mapper;
    }

    public function map(AkeneoCategory $akeneoCategory): FredhopperCategoryData
    {
        $categoryId = ($this->categoryIdMapper)($akeneoCategory->getCode());
        $names = ($this->nameMapper)($akeneoCategory->getLabels());
        $category = FredhopperCategoryData::of($categoryId, $names);
        if ($akeneoCategory->getParent() !== null) {
            $parentId = ($this->categoryIdMapper)($akeneoCategory->getParent());
            $category = $category->withParent($parentId);
        }
        return $category;
    }

    public function withCategoryIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->categoryIdMapper = $fn;
        return $result;
    }

    public function withNameMapper(callable $fn): self
    {
        $result = clone $this;
        $result->nameMapper = $fn;
        return $result;
    }

    /** @var callable */
    private $categoryIdMapper;

    /** @var callable */
    private $nameMapper;

    private function __construct()
    {

    }
}
