<?php


namespace Intouch\Framework\Annotation;

class PropertyAttribute {
    public function __construct(public string $propertyName, public $attribute) {}
}