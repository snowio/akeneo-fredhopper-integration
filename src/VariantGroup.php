<?php
namespace SnowIO\AkeneoFredhopper\Recipe;

use SnowIO\AkeneoDataModel\CategoryReferenceSet;
use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\AkeneoDataModel\SingleChannelVariantGroupData;

class VariantGroup
{
    public static function of(SingleChannelVariantGroupData $variantGroupData): self
    {
        $variantGroup = new self;
        $variantGroup->variantGroupData = $variantGroupData;
        return $variantGroup;
    }

    public function getCode(): string
    {
        return $this->variantGroupData->getCode();
    }

    public function getChannel(): string
    {
        return $this->variantGroupData->getChannel();
    }

    public function withProductData(SingleChannelProductData $productData): self
    {
        $result = clone $this;
        $result->productData = $productData;
        return $result;
    }

    public function getVariantGroupData(): SingleChannelVariantGroupData
    {
        return $this->variantGroupData;
    }

    public function getProductData(): SingleChannelProductData
    {
        return $this->productData;
    }

    public function getCategories(): CategoryReferenceSet
    {
        return $this->productData
            ->getProperties()
            ->getCategories();
    }

    /** @var  SingleChannelVariantGroupData */
    private $variantGroupData;

    /** @var SingleChannelProductData */
    private $productData;

    private function __construct()
    {

    }
}
