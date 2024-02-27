<?php


namespace Intouch\Framework\Annotation;

class MethodAttribute {
    public function __construct(
        public string $methodName, 
        public array $methodParameters,
        public $attribute) {}
}