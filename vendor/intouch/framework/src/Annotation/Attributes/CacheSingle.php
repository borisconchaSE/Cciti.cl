<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CacheSingle {
    public function __construct() {}
}