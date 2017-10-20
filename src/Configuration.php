<?php
namespace SnowIO\AkeneoFredhopper;

use SnowIO\AkeneoDataModel\Attribute;
use SnowIO\AkeneoDataModel\AttributeValue;
use SnowIO\AkeneoFredhopper\Mapper\FilterableAttributeValueMapper;
use SnowIO\AkeneoFredhopper\Mapper\LocalizedAttributeValueMapper;
use SnowIO\AkeneoFredhopper\Mapper\PriceAttributeValueMapper;
use SnowIO\AkeneoFredhopper\Mapper\ProductToVariantMapper;
use SnowIO\AkeneoFredhopper\Mapper\SimpleAttributeValueMapper;
use SnowIO\AkeneoFredhopper\Mapper\StandardAttributeMapper;
use SnowIO\AkeneoFredhopper\Mapper\AttributeOptionMapper;
use SnowIO\AkeneoFredhopper\Mapper\AttributeValueMapper;
use SnowIO\AkeneoFredhopper\Mapper\CategoryMapperTest;
use SnowIO\AkeneoFredhopper\Mapper\CompositeAttributeValueMapper;
use SnowIO\AkeneoFredhopper\Mapper\LocalizableAttributeMapper;
use SnowIO\AkeneoFredhopper\Mapper\PriceAttributeMapper;
use SnowIO\AkeneoFredhopper\Mapper\ProductToProductMapper;
use SnowIO\AkeneoFredhopper\Mapper\VariantGroupToProductMapper;

class Configuration
{
    const LOCALIZABLE_ASSET = 'asset';
    const LOCALIZABLE_MULTI_ATTRIBUTE = 'multi_attribute';

    public static function create(): self
    {
        $simpleRecipe = new self;

        $simpleRecipe->skuToProductIdMapper = function (string $channel, string $sku) {
            return "{$channel}_v_{$sku}";
        };

        $simpleRecipe->skuToVariantIdMapper = function (string $channel, string $sku) {
            return "{$channel}_{$sku}";
        };

        $simpleRecipe->variantGroupCodeToProductIdMapper = function (string $channel, string $code) {
            return "{$channel}_{$code}";
        };

        $simpleRecipe->productIdMapper = function (string $channel, string $sku) {
            return "{$channel}_{$sku}";
        };

        $simpleRecipe->categoryIdMapper = function (string $code) {
            return $code;
        };

        $simpleRecipe->localizedStringSetMapper = function (array $localesStringSet) {
            return $localesStringSet;
        };

        $simpleRecipe->localisedAttributeTypeIdentifier = function (Attribute $attribute) {
            return self::LOCALIZABLE_ASSET;
        };

        return $simpleRecipe;
    }

    public function withLocales(array $locales): self
    {
        $result = clone $this;
        $result->locales = $locales;
        return $result;
    }

    public function withCurrencies(array $currencies): self
    {
        $result = clone $this;
        $result->currencies = $currencies;
        return $result;
    }

    public function withLocalizedStringSetMapper(callable $fn): self
    {
        $result = clone $this;
        $result->localizedStringSetMapper = $fn;
        return $result;
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

    public function withSkuToProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->skuToProductIdMapper = $fn;
        return $result;
    }

    public function withSkuToVariantIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->skuToVariantIdMapper = $fn;
        return $result;
    }

    public function withVariantGroupCodeToProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->variantGroupCodeToProductIdMapper = $fn;
        return $result;
    }

    public function withLocalizableAttributeTypeIdentifier(callable $fn): self
    {
        $result = clone $this;
        $result->localisedAttributeTypeIdentifier = $fn;
        return $result;
    }

    public function getProductToProductMapper(): ProductToProductMapper
    {
        return ProductToProductMapper::create()
            ->withCategoryIdMapper($this->categoryIdMapper)
            ->withAttributeValueMapper($this->attributeValueMapper)
            ->withProductIdMapper($this->productIdMapper);
    }

    public function getCategoryMapper(): CategoryMapperTest
    {
        return CategoryMapperTest::create()
            ->withCategoryIdMapper($this->categoryIdMapper)
            ->withNameMapper($this->localizedStringSetMapper);
    }

    public function getAttributeValueMapper(): AttributeValueMapper
    {
        return CompositeAttributeValueMapper::create()
            ->with(
                FilterableAttributeValueMapper::of(
                    SimpleAttributeValueMapper::create(),
                    function (AttributeValue $attributeValue) {
                        if ($attributeValue->getScope()->getLocale() === null) {
                            return true;
                        }
                        $attributeCode = $attributeValue->getAttributeCode();
                        $type = ($this->localisedAttributeTypeIdentifier)($attributeCode);
                        return ($type !== self::LOCALIZABLE_MULTI_ATTRIBUTE);
                    }
                )
            )
            ->with(
                FilterableAttributeValueMapper::of(
                    LocalizedAttributeValueMapper::create(),
                    function (AttributeValue $attributeValue) {
                        if ($attributeValue->getScope()->getLocale() === null) {
                            return false;
                        }
                        $attributeCode = $attributeValue->getAttributeCode();
                        $type = ($this->localisedAttributeTypeIdentifier)($attributeCode);
                        return ($type === self::LOCALIZABLE_MULTI_ATTRIBUTE);
                    }
                )
            )
            ->with(PriceAttributeValueMapper::create());
    }

    public function getProductToVariantMapper(): ProductToVariantMapper
    {
        return ProductToVariantMapper::create()
            ->withSkuToProductIdMapper($this->skuToProductIdMapper)
            ->withSkuToVariantIdMapper($this->skuToVariantIdMapper)
            ->withVariantGroupCodeToProductIdMapper($this->variantGroupCodeToProductIdMapper);
    }

    public function getVariantGroupToProductMapper(): VariantGroupToProductMapper
    {
        return VariantGroupToProductMapper::create()
            ->withProductIdMapper($this->variantGroupCodeToProductIdMapper)
            ->withCategoryIdMapper($this->categoryIdMapper);
    }

    public function getAttributeMapper(): StandardAttributeMapper
    {
        return StandardAttributeMapper::create();
    }

    public function getAttributeOptionMapper(): AttributeOptionMapper
    {
        return AttributeOptionMapper::create();
    }

    public function getLocalizableAttributeMapper(): LocalizableAttributeMapper
    {
        return LocalizableAttributeMapper::of($this->locales);
    }

    public function getPriceAttributeMapper(): PriceAttributeMapper
    {
        return PriceAttributeMapper::of($this->currencies);
    }

    private $productIdMapper;
    private $categoryIdMapper;
    private $attributeValueMapper;
    private $localizedStringSetMapper;
    private $localisedAttributeTypeIdentifier;
    private $variantGroupCodeToProductIdMapper;
    private $skuToProductIdMapper;
    private $skuToVariantIdMapper;
    private $currencies = [];
    private $locales = [];

    private function __construct()
    {

    }
}