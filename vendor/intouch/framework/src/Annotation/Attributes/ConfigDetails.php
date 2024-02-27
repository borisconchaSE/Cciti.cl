<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ConfigDetails {

    public function __construct(?string $name = null) {}

}