<?php


namespace Intouch\Framework\Annotation;

class ClassAttribute {
    public function __construct(public string $className, public $attribute) {}
}