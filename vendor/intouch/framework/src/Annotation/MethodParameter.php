<?php


namespace Intouch\Framework\Annotation;

class MethodParameter {

    public function __construct(
        public string $name,
        public string $type,
        public bool   $allowsNull,
        public $defaultValue,
        public int $position,
        public bool $isOptional,
    ) {}
}