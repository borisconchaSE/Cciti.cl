<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class PreventCache {
    public function __construct() {}
}