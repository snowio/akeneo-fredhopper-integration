<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper;

use SnowIO\AkeneoDataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\FredhopperDataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;
use SnowIO\FredhopperDataModel\AttributeOptionSet;

class AttributeOptionMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeOption $attributeOption): AttributeOptionSet
    {
        $attributeId = ($this->attributeIdMapper)($attributeOption->getAttributeCode());
        $valueId = ($this->valueIdMapper)($attributeOption->getOptionCode());
        $labels = ($this->displayValueMapper)($attributeOption->getLabels());
        return AttributeOptionSet::of([
            FredhopperAttributeOption::of($attributeId, $valueId)->withDisplayValues($labels)
        ]);
    }

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
        return $result;
    }

    public function withValueIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->valueIdMapper = $fn;
        return $result;
    }

    public function withDisplayValueMapper(callable $fn): self
    {
        $result = clone $this;
        $result->displayValueMapper = $fn;
        return $result;
    }

    private $attributeIdMapper;
    private $valueIdMapper;
    private $displayValueMapper;

    private function __construct()
    {
        $this->attributeIdMapper = [AttributeData::class, 'sanitizeId'];
        $this->valueIdMapper = [FredhopperAttributeOption::class, 'sanitizeValueId'];
        $this->displayValueMapper = InternationalizedStringMapper::create();
    }
}
