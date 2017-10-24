<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoFredhopper\VariantGroup;
use SnowIO\FredhopperDataModel\Product as FredhopperProduct;

class VariantGroupToProductMapper
{
    public static function create(): self
    {
        $productMapper = new self;
        $productMapper->categoryIdMapper = function ($categoryCode) { return $categoryCode; };
        $productMapper->productIdMapper = function (string $code) {
            return $code;
        };
        $productMapper->attributeValueMapper = SimpleAttributeValueMapper::create();
        return $productMapper;
    }

    public function map(VariantGroup $variantGroup): FredhopperProduct
    {
        $productId = ($this->productIdMapper)($variantGroup->getCode());
        $categoryCodes = $variantGroup->getCategories()->getCategoryCodes();
        $categoryIds = \array_map(function (string $categoryCode) {
            return ($this->categoryIdMapper)($categoryCode);
        }, $categoryCodes);
        $akeneoAttributeValues = $variantGroup->getVariantGroupData()->getAttributeValues();
        $fredhopperAttributeValues = $this->attributeValueMapper->map($akeneoAttributeValues);
        return FredhopperProduct::of($productId, $categoryIds)
            ->withAttributeValues($fredhopperAttributeValues);
    }

    public function withCategoryIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->categoryIdMapper = $fn;
        return $result;
    }

    public function withProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->productIdMapper = $fn;
        return $result;
    }

    public function withAttributeValueMapper($attributeValueMapper): self
    {
        $result = clone $this;
        $result->attributeValueMapper = $attributeValueMapper;
        return $result;
    }

    /** @var callable */
    private $categoryIdMapper;

    /** @var callable */
    private $productIdMapper;

    /** @var SimpleAttributeValueMapper */
    private $attributeValueMapper;

    private function __construct()
    {

    }
}
