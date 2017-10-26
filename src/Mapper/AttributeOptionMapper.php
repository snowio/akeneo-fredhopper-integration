<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class AttributeOptionMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function map(AkeneoAttributeOption $attributeOption): FredhopperAttributeOption
    {
        $attributeId = ($this->attributeIdMapper)($attributeOption->getAttributeCode());
        $valueId = $attributeOption->getOptionCode();
        $labels = ($this->displayValueMapper)($attributeOption->getLabels());
        return FredhopperAttributeOption::of($attributeId, $valueId)->withDisplayValues($labels);
    }

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
        return $result;
    }

    public function withDisplayValueMapper(callable $fn): self
    {
        $result = clone $this;
        $result->displayValueMapper = $fn;
        return $result;
    }

    private $attributeIdMapper;
    private $displayValueMapper;

    private function __construct()
    {
        $this->attributeIdMapper = function (string $code) { return $code; };
        $this->displayValueMapper = function (AkeneoInternationalizedString $labels) {
            $result = FredhopperInternationalizedString::create();
            /** @var LocalizedString $label */
            foreach ($labels as $label) {
                $result = $result->withValue($label->getValue(), $label->getLocale());
            }
            return $result;
        };
    }
}
