<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ReturnCacheTableData {
    public function __construct() {}
}