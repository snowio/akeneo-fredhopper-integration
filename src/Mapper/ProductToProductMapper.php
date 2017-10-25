<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\ProductData as AkeneoProductData;
use SnowIO\FredhopperDataModel\CategoryIdSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;

class ProductToProductMapper
{
    public static function create(): self
    {
        $standaloneProductMapper = new self;
        $standaloneProductMapper->categoryIdMapper = function ($categoryCode) { return $categoryCode; };
        $standaloneProductMapper->productIdMapper = function (string $channel, string $sku) {
            return $sku;
        };
        $standaloneProductMapper->attributeValueMapper = SimpleAttributeValueMapper::create();
        return $standaloneProductMapper;
    }

    public function map(AkeneoProductData $akeneoProductData): FredhopperProductData
    {
        $channel = $akeneoProductData->getChannel();
        $sku = $akeneoProductData->getSku();
        $productId = ($this->productIdMapper)($channel, $sku);
        $categoryCodes = $akeneoProductData->getProperties()->getCategories()
            ->getCategoryCodes();
        $categoryIds = \array_map(function (string $categoryCode) {
            return ($this->categoryIdMapper)($categoryCode);
        }, $categoryCodes);
        $akeneoAttributeValues = $akeneoProductData->getAttributeValues();
        $fredhopperAttributeValues = $this->attributeValueMapper->map($akeneoAttributeValues);
        return FredhopperProductData::of($productId)
            ->withCategoryIds(CategoryIdSet::of($categoryIds))
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
