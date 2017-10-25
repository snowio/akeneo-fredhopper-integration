<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\CategoryData as AkeneoCategoryData;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString;
use SnowIO\FredhopperDataModel\CategoryData as FredhopperCategoryData;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class CategoryMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->categoryIdMapper = function (string $categoryCode) { return $categoryCode; };
        $mapper->nameMapper = function (AkeneoInternationalizedString $labels) {
            $result = FredhopperInternationalizedString::create();
            /** @var LocalizedString $label */
            foreach ($labels as $label) {
                $result = $result->withValue($label->getValue(), $label->getLocale());
            }
            return $result;
        };
        return $mapper;
    }

    public function map(AkeneoCategoryData $akeneoCategory): FredhopperCategoryData
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
