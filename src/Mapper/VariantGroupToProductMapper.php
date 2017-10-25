<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\VariantGroupData;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;

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

    public function map(VariantGroupData $variantGroup): FredhopperProductData
    {
        $productId = ($this->productIdMapper)($variantGroup->getCode());
        $akeneoAttributeValues = $variantGroup->getAttributeValues();
        $fredhopperAttributeValues = $this->attributeValueMapper->map($akeneoAttributeValues);
        return FredhopperProductData::of($productId)->withAttributeValues($fredhopperAttributeValues);
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
