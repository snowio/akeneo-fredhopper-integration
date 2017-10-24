<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Category as AkeneoCategory;
use SnowIO\FredhopperDataModel\Category as FredhopperCategory;

class CategoryMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->categoryIdMapper = function (string $categoryCode) { return $categoryCode; };
        $mapper->nameMapper = function (array $names) { return $names; };
        return $mapper;
    }

    public function map(AkeneoCategory $akeneoCategory): FredhopperCategory
    {
        $categoryId = ($this->categoryIdMapper)($akeneoCategory->getCode());
        $names = ($this->nameMapper)($akeneoCategory->getLabels());
        $category = FredhopperCategory::of($categoryId, $names);
        if ($akeneoCategory->getParent() !== null) {
            $parentId = ($this->categoryIdMapper)($akeneoCategory->getParent());
            $category = $category->withParent($parentId);
        }

        return $category->withTimestamp($akeneoCategory->getTimestamp());
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
