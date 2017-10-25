<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper;

use SnowIO\AkeneoDataModel\AttributeData;
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
        return function (AttributeData $akeneoAttributeData): bool {
            $attributeCode = $akeneoAttributeData->getCode();
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
