<?php
namespace SnowIO\AkeneoFredhopper;

use SnowIO\AkeneoDataModel\Attribute;
use SnowIO\AkeneoDataModel\AttributeValue;

final class AttributeCodeFilter
{
    public static function of(callable $predicate): self
    {
        $whitelist = new self;
        $whitelist->predicate = $predicate;
        return $whitelist;
    }

    public function getAttributeFilter(): callable
    {
        return function (Attribute $akeneoAttribute): bool {
            $attributeCode = $akeneoAttribute->getCode();
            return ($this->predicate)($attributeCode);
        };
    }

    public function getAttributeValueFilter(): callable
    {
        return function (AttributeValue $akeneoAttributValue): bool {
            $attributeCode = $akeneoAttributValue->getAttributeCode();
            return ($this->predicate)($attributeCode);
        };
    }

    private function __construct()
    {

    }

    private $predicate;
}
